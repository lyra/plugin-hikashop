<?php
/**
 * Copyright © Lyra Network.
 * This file is part of PayZen plugin for HikaShop. See COPYING.md for license details.
 *
 * @author    Lyra Network (https://www.lyra.com/)
 * @copyright Lyra Network
 * @license   http://www.gnu.org/licenses/gpl.html GNU General Public License (GPL v3)
 */

defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;

if (! class_exists('\\Joomla\\Filesystem\\File', false) && class_exists('\\Joomla\\CMS\\Filesystem\\File')) {
    class_alias('\\Joomla\\CMS\\Filesystem\\File', '\\Joomla\\Filesystem\\File');
}

if (! class_exists('\\Joomla\\Filesystem\\Folder', false) && class_exists('\\Joomla\\CMS\\Filesystem\\Folder')) {
    class_alias('\\Joomla\\CMS\\Filesystem\\Folder', '\\Joomla\\Filesystem\\Folder');
}

if (! class_exists('\\Joomla\\Filesystem\\Path', false) && class_exists('\\Joomla\\CMS\\Filesystem\\Path')) {
    class_alias('\\Joomla\\CMS\\Filesystem\\Path', '\\Joomla\\Filesystem\\Path');
}

use Joomla\Filesystem\File;
use Joomla\Filesystem\Folder;
use Joomla\CMS\Language\Text as JText;

// Load plugin translations.
$lang = Factory::getApplication()->getLanguage();
$lang->load('plg_hikashoppayment_payzen', dirname(__FILE__));

// Load gateway API.
require_once rtrim(JPATH_ADMINISTRATOR, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_payzen' . DIRECTORY_SEPARATOR . 'classes/sdk-autoload.php';

// Load plugin features class.
if (! class_exists('com_payzenInstallerScript')) {
    require_once rtrim(JPATH_ADMINISTRATOR, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_payzen' . DIRECTORY_SEPARATOR . 'script.install.php';
}

use \Lyranetwork\Payzen\Sdk\Form\Api as PayzenApi;
use \Lyranetwork\Payzen\Sdk\Form\Request as PayzenRequest;
use \Lyranetwork\Payzen\Sdk\Form\Response as PayzenResponse;

class plgHikashoppaymentPayzen extends hikashopPaymentPlugin
{
    var $name = 'payzen';
    var $accepted_currencies = array();
    var $doc_form = 'payzen';
    var $multiple = true;
    var $platform_url = 'https://secure.payzen.eu/vads-payment/';

    /**
     * Plugin constructor.
     *
     * Initialises the list of accepted currencies, plugin features and parameter keys,
     * then delegates to the parent constructor.
     *
     * @param object $subject The dispatcher object
     * @param array  $config  Plugin configuration array
     */
    function __construct(&$subject, $config)
    {
        foreach (PayzenApi::getSupportedCurrencies() as $currency) {
            // Currency alpha3 code.
            $this->accepted_currencies[] = $currency->getAlpha3();
        }

        // Plugin features.
        $this->plugin_features = com_payzenInstallerScript::$plugin_features;

        // Configuration param keys.
        $this->param_keys = com_payzenInstallerScript::$param_keys;

        parent::__construct($subject, $config);
    }

    /**
     * Called by HikaShop before redirecting to the payment gateway.
     *
     * Builds the payment parameters array from the order data and plugin settings,
     * then triggers the redirect form page.
     *
     * @param object $order     The current order object
     * @param array  $methods   Available payment methods
     * @param int    $method_id The selected payment method ID
     *
     * @return void
     */
    function onAfterOrderConfirm(&$order, &$methods, $method_id)
    {
        parent::onAfterOrderConfirm($order, $methods, $method_id);
        $app = Factory::getApplication();

        // Process shop language.
        $lang = $app->getLanguage();
        $langCode = strtoupper(substr($lang->get('tag'), 0, 2));
        $payzenLanguage = PayzenApi::isSupportedLanguage($langCode) ? $langCode : $this->payment_params->payzen_language;

        // Process currency.
        $payzenCurrency = PayzenApi::findCurrencyByAlphaCode($this->currency->currency_code);
        if ($payzenCurrency === null) {
            $this->log('Unsupported currency: ' . $this->currency->currency_code, 'ERROR');

            return false;
        }

        // Amount.
        $price = $order->cart->full_total->prices[0];
        $amount = round($price->price_value_with_tax, hikashop_get('class.currency')->getRounding($price->price_currency_id));

        // 3DS activation according to amount.
        $threedsMpi = null;
        if (! empty($this->payment_params->payzen_threeds_amount_min) &&
            ($amount < $this->payment_params->payzen_threeds_amount_min)) {
            $threedsMpi = '2';
        }

        // Load config to retrieve HikaShop version.
        $config = hikashop_config();

        $billingAddress = $order->cart->billing_address ?? null;
        $shippingAddress = $order->cart->shipping_address ?? null;

        $this->vars = array(
            'amount' => $payzenCurrency->convertAmountToInteger($amount),
            'contrib' => 'HikaShop_2.x-6.x_2.2.1/' . JVERSION . '_' . $config->get('version') . '/' . PayzenApi::shortPhpVersion(),
            'currency' => $payzenCurrency->getNum(),
            'language' => $payzenLanguage,
            'order_id' => $order->order_number,
            'threeds_mpi' => $threedsMpi,

            'cust_id' => $order->customer->id ?? "",
            'cust_email' => $this->user->user_email ?? "",

            'cust_title' => $billingAddress->address_title ?? "",
            'cust_first_name' => $billingAddress->address_firstname ?? "",
            'cust_last_name' => $billingAddress->address_lastname ?? "",
            'cust_address' => ($billingAddress->address_street ?? "") . ' ' . ($billingAddress->address_street2 ?? ""),
            'cust_zip' => $billingAddress->address_post_code ?? "",
            'cust_city' => $billingAddress->address_city ?? "",
            'cust_state' => isset($billingAddress->address_state) ? ($billingAddress->address_state->zone_name ?? "") : "",
            'cust_country' => isset($billingAddress->address_country) ? ($billingAddress->address_country->zone_code_2 ?? "") : "",
            'cust_phone' => $billingAddress->address_telephone ?? "",

            'ship_to_first_name' => $shippingAddress->address_firstname ?? "",
            'ship_to_last_name' => $shippingAddress->address_lastname ?? "",
            'ship_to_street' => $shippingAddress->address_street ?? "",
            'ship_to_street2' => $shippingAddress->address_street2 ?? "",
            'ship_to_city' => $shippingAddress->address_city ?? "",
            'ship_to_state' => isset($shippingAddress->address_state) ? ($shippingAddress->address_state->zone_name ?? "") : "",
            'ship_to_country' => isset($shippingAddress->address_country) ? ($shippingAddress->address_country->zone_code_2 ?? "") : "",
            'ship_to_phone_num' => $shippingAddress->address_telephone ?? "",
            'ship_to_zip' => $shippingAddress->address_post_code ?? "",

            'url_return' => HIKASHOP_LIVE .
                'index.php?option=com_hikashop&ctrl=checkout&task=notify&notif_payment=payzen&tmpl=component&Itemid=' .
                $app->input->getInt('Itemid'),
            'payment_method_id' => $method_id
        );

        foreach ($this->param_keys as $param) {
            $paramName = 'payzen_' . $param;
            $this->vars[$param] = $this->payment_params->$paramName;
        }

        return $this->showPage('end');
    }

    /**
     * Handle the payment notification from the gateway (IPN server call or return URL).
     *
     * Validates the gateway signature, retrieves the matching order, and updates
     * its status according to the payment result. Terminates with a gateway-specific
     * acknowledgement string when called from the server-to-server URL.
     *
     * @param array $statuses Available order statuses
     *
     * @return boolean False on error, otherwise terminates via die()
     */
    function onPaymentNotification(&$statuses)
    {
        $app = Factory::getApplication();
        $input = $app->input;

        // Check if this is a server call.
        if ($input->getVar('vads_hash') !== null) {
            if ((($payCfg = $input->getVar('vads_payment_config')) && stripos($payCfg, 'MULTI') !== false) ||
                (($contrib = $input->getVar('vads_contrib')) && stripos($contrib, 'multi') !== false)) {

                $data = hikashop_import('hikashoppayment', 'payzenmulti');
                if (! empty($data)) {
                    return $data->onPaymentNotification($statuses);
                }
            }
        }

        // Load user.
        $custId = $input->getInt('vads_cust_id');
        $user = $custId
            ? Factory::getContainer()->get(\Joomla\CMS\User\UserFactoryInterface::class)->loadUserById($custId)
            : $app->getIdentity();

        // Set user to current session.
        $session = $app->getSession();
        $session->set('user', $user);
        $app->setUserState('user', $user);

        // Load payment method parameters.
        $pluginsClass = hikashop::get('class.plugins');
        $elements = $pluginsClass->getMethods('payment', 'payzen');
        if (empty($elements)) {
            return false;
        }

        $itemId = $input->getInt('Itemid');
        $urlItemId = $itemId ? '&Itemid=' . $itemId : '';

        // Keep full gateway payload while using Joomla Input accessors.
        $requestData = $input->post->getArray();
        if (empty($requestData)) {
            $requestData = $input->get->getArray();
        }

        $paymentMethodId = $requestData['vads_ext_info_payment_method_id'] ?? '';
        $element = $this->getElement($elements, $paymentMethodId);

        $payzenResponse = new PayzenResponse(
            $requestData,
            $element->payment_params->payzen_ctx_mode,
            $element->payment_params->payzen_key_test,
            $element->payment_params->payzen_key_prod,
            $element->payment_params->payzen_sign_algo
        );

        $fromServer = ($payzenResponse->get('hash') !== null);

        if (! $payzenResponse->isAuthentified()) {
            $this->log("Received invalid response from return/IPN URL with data: " . print_r($requestData, true));
            $this->log('Signature algorithm selected in module settings must be the same as one selected in gateway Back Office.');

            if ($fromServer) {
                $this->log('SERVER URL PROCESS END');
                die($payzenResponse->getOutputForGateway('auth_fail'));
            } else {
                $this->log('RETURN URL PROCESS END');
                $app->enqueueMessage(JText::_('PAYZEN_ERROR_MSG'), 'error');
                $app->redirect(hikashop_completeLink('order' . $urlItemId, false, true));
                die();
            }
        }

        // Retrieve order info from database.
        $orderClass = hikashop::get('class.order');
        $orderId = hikashop::decode($payzenResponse->get('order_id'));
        $order = $orderClass->get($orderId);

        if (empty($order)) {
            // Order not found.
            $this->log('Error: Order (' . $orderId . ') not found or key does not match received invoice ID.');

            if ($fromServer) {
                $this->log('SERVER URL PROCESS END');
                die($payzenResponse->getOutputForGateway('order_not_found'));
            } else {
                $this->log('RETURN URL PROCESS END');
                $app->enqueueMessage(JText::_('PAYZEN_ERROR_MSG'), 'error');
                $app->redirect(hikashop_completeLink('order' . $urlItemId, false, true));
                die();
            }
        }

        if ($element->payment_params->payzen_ctx_mode === 'TEST' && $this->plugin_features['prodfaq']) {
            $app->enqueueMessage(JText::_('PAYZEN_SHOP_TO_PROD_INFO'));
        }

        // Redirect to those URLs.
        $successUrl = hikashop_completeLink('checkout&task=after_end&order_id=' . $order->order_id . $urlItemId, false, true);
        $errorUrl = hikashop_completeLink('order&task=cancel_order&order_id=' . $order->order_id . $urlItemId, false, true);

        // If unpaid order reset order status.
        $hikashopConfig = hikashop_config();
        $unpaidStatusesRaw = $hikashopConfig->get('order_unpaid_statuses');
        $unpaidStatuses = $unpaidStatusesRaw ? explode(',', $unpaidStatusesRaw) : array();
        if ($hikashopConfig->get('allow_payment_button') && in_array($order->order_status, $unpaidStatuses)) {
            $order->order_status = $hikashopConfig->get('order_created_status');
        }

        // Process according to order status and payment result.
        if ($order->order_status === $hikashopConfig->get('order_created_status')) {
            // Order not processed yet.
            if ($payzenResponse->isAcceptedPayment()) {
                $this->log("Payment successfull, let's save order #$orderId");

                if (method_exists($this, 'modifyOrder')) {
                    $history = $this->_createOrderHistory($payzenResponse, $element, 1);
                    $this->modifyOrder($order->order_id, $element->payment_params->payzen_verified_status, $history);
                } else {
                    $this->_confirmOrder($order, $element->payment_params->payzen_verified_status, $element, $payzenResponse, 1);
                }

                if ($fromServer) {
                    $this->log('Payment completed successfully by server URL call.');
                    $this->log('SERVER URL PROCESS END');
                    die($payzenResponse->getOutputForGateway('payment_ok'));
                } else {
                    $this->log('Warning! IPN URL call has not worked. Payment completed by return URL call.');
                    if ($element->payment_params->payzen_ctx_mode === 'TEST') {
                        // Test mode warning : check URL not correctly called.
                        $app->enqueueMessage(JText::_('PAYZEN_CHECK_URL_WARN') . '<br />' . JText::_('PAYZEN_CHECK_URL_WARN_DETAILS'), 'error');
                    }

                    $this->log('RETURN URL PROCESS END');
                    $app->redirect($successUrl);
                    die();
                }
            } else {
                if (method_exists($this, 'modifyOrder')) {
                    $history = $this->_createOrderHistory($payzenResponse, $element);
                    $this->modifyOrder($order->order_id, $element->payment_params->payzen_invalid_status, $history);
                } else {
                    $this->_confirmOrder($order, $element->payment_params->payzen_invalid_status, $element, $payzenResponse);
                }

                $this->log('Payment failed or cancelled. ' . $payzenResponse->getLogMessage());
                if ($fromServer) {
                    $this->log('SERVER URL PROCESS END');
                    die($payzenResponse->getOutputForGateway('payment_ko'));
                } else {
                    $this->log('RETURN URL PROCESS END');
                    $app->enqueueMessage(JText::_('PAYZEN_FAILURE_MSG'), 'error');
                    $app->redirect($errorUrl);
                    die();
                }
            }
        } else {
            // Order already processed.
            $this->log("Order #$orderId is already processed. Just show payment result.");
            if ($payzenResponse->isAcceptedPayment()
                && ($order->order_status === $element->payment_params->payzen_verified_status)) {
                $this->log('Payment successfull reconfirmed.');
                if ($fromServer) {
                    $this->log('SERVER URL PROCESS END');
                    die($payzenResponse->getOutputForGateway('payment_ok_already_done'));
                } else {
                    $this->log('RETURN URL PROCESS END');
                    $app->redirect($successUrl);
                    die();
                }
            } elseif (! $payzenResponse->isAcceptedPayment() &&
                ($order->order_status === $element->payment_params->payzen_invalid_status)) {
                $this->log('Payment failed reconfirmed.');
                if ($fromServer) {
                    $this->log('SERVER URL PROCESS END');
                    die($payzenResponse->getOutputForGateway('payment_ko_already_done'));
                } else {
                    $this->log('RETURN URL PROCESS END');
                    $app->enqueueMessage(JText::_('PAYZEN_FAILURE_MSG'), 'error');
                    $app->redirect($errorUrl);
                    die();
                }
            } else {
                $this->log('Error ! Invalid payment result received for already saved order. Payment result : ' .
                    $payzenResponse->get('result') . ', Order status : ' . $order->order_status);
                if ($fromServer) {
                    $this->log('SERVER URL PROCESS END');
                    die($payzenResponse->getOutputForGateway('payment_ko_on_order_ok'));
                } else {
                    $this->log('RETURN URL PROCESS END');
                    $app->enqueueMessage(JText::_('PAYZEN_ERROR_MSG'), 'error');
                    $app->redirect(hikashop_completeLink('order' . $urlItemId, false, true));
                    die();
                }
            }
        }
    }

    /**
     * Update an order status and persist an associated history entry.
     *
     * @param object         $orderData      The original order object
     * @param string         $newStatus      The new order status to apply
     * @param object         $payment        The payment method configuration object
     * @param PayzenResponse $payzenResponse The gateway response object
     * @param int            $notify         Whether to notify the customer: 1 yes, 0 no (default)
     *
     * @return void
     */
    function _confirmOrder($orderData, $newStatus, $payment, $payzenResponse, $notify = 0)
    {
        // Prepare order and history order.
        $order = new stdClass();
        $order->order_id = $orderData->order_id;
        $order->order_status = $newStatus;

        $order->old_status = new stdClass();
        $order->old_status->order_status = $orderData->order_status;

        $order->history = new stdClass();
        $history = $this->_createOrderHistory($payzenResponse, $payment, $notify);
        foreach ($history as $key => $value) {
            $key = 'history_' . $key;
            $order->history->$key = $value;
        }

        // Save order and history order.
        $orderClass = hikashop::get('class.order');
        $orderClass->save($order);
    }

    /**
     * Build an order history entry from the gateway response.
     *
     * @param PayzenResponse $payzenResponse The gateway response object
     * @param object         $payment        The payment method configuration object
     * @param int            $notify         Whether to notify the customer: 1 yes, 0 no (default)
     *
     * @return stdClass The populated history entry object
     */
    function _createOrderHistory($payzenResponse, $payment, $notify = 0)
    {
        $currencyObj = PayzenApi::findCurrencyByNumCode($payzenResponse->get('currency'));
        $currencyCode = ($currencyObj !== null) ? $currencyObj->getAlpha3() : '';
        $history = new stdClass();
        $history->amount = $payzenResponse->getFloatAmount() . ' ' . $currencyCode;
        $history->reason = JText::_('AUTOMATIC_PAYMENT_NOTIFICATION');
        $history->payment_id = $payment->payment_id;
        $history->payment_method = $payment->payment_type;
        $history->type = 'payment';
        $history->notified = $notify;

        $info = JText::_('PAYZEN_RESULT') . $payzenResponse->getMessage();

        $info .= ' | ' . JText::_('PAYZEN_TRANS_ID') . $payzenResponse->get('trans_id');

        if ($payzenResponse->get('card_brand')) {
            $info .= ' | ' . JText::_('PAYZEN_CC_TYPE') . $payzenResponse->get('card_brand');

            // Add card brand user choice.
            if ($payzenResponse->get('brand_management')) {
                $brandInfo = json_decode($payzenResponse->get('brand_management'));
                $msgBrandChoice = '';

                if (isset($brandInfo->userChoice) && $brandInfo->userChoice) {
                    $msgBrandChoice .= JText::_('PAYZEN_CARD_BRAND_BUYER_CHOICE');
                } else {
                    $msgBrandChoice .= JText::_('PAYZEN_CARD_BRAND_DEFAULT_CHOICE');
                }

                $info .= ' (' . $msgBrandChoice . ')';
            }
        }

        if ($payzenResponse->get('card_number')) {
            $info .= ' | ' . JText::_('PAYZEN_CC_NUMBER') . $payzenResponse->get('card_number');
        }

        if ($payzenResponse->get('expiry_month') && $payzenResponse->get('expiry_year')) {
            $info .= ' | ' . JText::_('PAYZEN_CC_EXPIRY') . str_pad($payzenResponse->get('expiry_month'), 2, '0', STR_PAD_LEFT) .
                ' / ' . $payzenResponse->get('expiry_year');
        }

        $history->data = $info;

        return $history;
    }

    /**
     * Called before the plugin configuration page is loaded.
     *
     * Sets the page title and copies payment images to the HikaShop images directory.
     *
     * @param object $element The payment method element
     *
     * @return void
     */
    function onPaymentConfiguration(&$element)
    {
        $this->title = JText::_('PAYZEN_CONFIG_PAGE_TITLE');
        $this->_copyImages();

        parent::onPaymentConfiguration($element);
    }

    /**
     * Populate a payment method element with the plugin's default parameter values.
     *
     * Called by {@see onPaymentConfiguration()} when creating a new payment method instance.
     *
     * @param object $element The payment method element to populate
     *
     * @return void
     */
    function getPaymentDefaultValues(&$element)
    {
        $element->payment_name = JText::_('PAYZEN_DEFAULT_TITLE');
        $element->payment_description = JText::_('PAYZEN_DEFAULT_DESCRIPTION');
        $element->payment_images = 'payzen_cards';

        // Default values.
        $element->payment_params->payzen_site_id = '12345678';
        $element->payment_params->payzen_key_test = '1111111111111111';
        $element->payment_params->payzen_key_prod = '2222222222222222';
        $element->payment_params->payzen_ctx_mode = 'TEST';
        $element->payment_params->payzen_sign_algo = 'SHA-256';
        $element->payment_params->payzen_platform_url = $this->platform_url;
        $element->payment_params->payzen_language = 'fr';
        $element->payment_params->payzen_available_languages = '';
        $element->payment_params->payzen_capture_delay = '';
        $element->payment_params->payzen_validation_mode = '';
        $element->payment_params->payzen_payment_cards = '';
        $element->payment_params->payzen_threeds_amount_min = '';
        $element->payment_params->payzen_redirect_enabled = 0;
        $element->payment_params->payzen_redirect_success_timeout = '5';
        $element->payment_params->payzen_redirect_success_message = JText::_('PAYZEN_REDIRECT_SUCCESS_MESSAGE_DFEAULT');
        $element->payment_params->payzen_redirect_error_timeout = '5';
        $element->payment_params->payzen_redirect_error_message = JText::_('PAYZEN_REDIRECT_ERROR_MESSAGE_DFEAULT');
        $element->payment_params->payzen_return_mode = 'GET';
        $element->payment_params->payzen_verified_status = 'confirmed';
        $element->payment_params->payzen_invalid_status = 'cancelled';
    }

    /**
     * Called before the plugin configuration is saved.
     *
     * Normalises multi-value fields and validates all payment parameters
     * against the gateway API. Enqueues error messages and returns false
     * when validation fails.
     *
     * @param object $element The payment method element being saved
     *
     * @return boolean True on success, false if validation errors occurred
     */
    function onPaymentConfigurationSave(&$element)
    {
        $langs = $element->payment_params->payzen_available_languages ?? null;
        if (! is_array($langs)) {
            $langs = array();
        }

        $element->payment_params->payzen_available_languages = implode(';', $langs);

        $cards = $element->payment_params->payzen_payment_cards ?? null;
        if (! is_array($cards)) {
            $cards = array();
        }

        $element->payment_params->payzen_payment_cards = implode(';', $cards);

        $platformUrl = $element->payment_params->payzen_platform_url ?? null;
        if ($platformUrl == null) {
            $element->payment_params->payzen_platform_url = $this->platform_url;
        }

        // Configuration fields validation.
        $errors = array();

        // Instanciate PayzenRequest to validate parameters.
        $request = new PayzenRequest();

        foreach ($this->param_keys as $param) {
            $paramName = 'payzen_' . $param;
            $value = $element->payment_params->$paramName ?? null;

            if (! $request->set($param, $value)) {
                $errors[] = sprintf(JText::_('PAYZEN_ERROR_SAVE'), JText::_($paramName));
            }
        }

        if (! empty($errors)) {
            $app = Factory::getApplication();
            foreach ($errors as $error) {
                $app->enqueueMessage($error, 'error');
            }

            return false;
        }

        return true;
    }

    /**
     * Copy plugin payment images to the HikaShop images/payment directory.
     *
     * Creates the destination directory if it does not exist and skips files
     * that are already present.
     *
     * @return void
     */
    function _copyImages()
    {
        $destFolder = HIKASHOP_IMAGES . 'payment';
        $sourceFolder = realpath(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'images';

        if (! (Folder::exists($destFolder))) {
            Folder::create($destFolder);
        }

        if (! (File::exists($destFolder . DIRECTORY_SEPARATOR . 'payzen_cards.png'))) {
            File::copy($sourceFolder . DIRECTORY_SEPARATOR . 'payzen_cards.png', $destFolder . DIRECTORY_SEPARATOR . 'payzen_cards.png');
        }

        if (! (File::exists($destFolder . DIRECTORY_SEPARATOR . 'payzen.png'))) {
            File::copy($sourceFolder . DIRECTORY_SEPARATOR . 'payzen.png', $destFolder . DIRECTORY_SEPARATOR . 'payzen.png');
        }
    }

    /**
     * Write a message to the plugin log file.
     *
     * @param string $msg   The message to log
     * @param string $level The log level (default 'INFO')
     *
     * @return void
     */
    function log($msg, $level = 'INFO')
    {
        com_payzenInstallerScript::log($msg, 'payzen.log', $level);
    }

    /**
     * Retrieve a payment method element by its ID from the list of elements.
     *
     * Returns the first element when no payment ID is provided.
     *
     * @param array  $elements  List of payment method elements
     * @param string $paymentId The payment method ID to look for
     *
     * @return object|false The matching element, or false if none found
     */
    function getElement($elements, $paymentId)
    {
        if (! $paymentId) {
            return reset($elements);
        }

        foreach ($elements as $elem) {
            if ($elem->payment_id == $paymentId) {
                return $elem;
            }
        }

        return reset($elements);
    }
}
