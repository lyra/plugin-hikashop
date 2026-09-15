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
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text as JText;

require_once rtrim(JPATH_ADMINISTRATOR, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_payzen' . DIRECTORY_SEPARATOR . 'classes/sdk-autoload.php';

// Load plugins translations.
$lang = Factory::getApplication()->getLanguage();
$lang->load('plg_hikashoppayment_payzenmulti', dirname(__FILE__));

// Load plugin features class.
if (! class_exists('com_payzenInstallerScript')) {
    require_once rtrim(JPATH_ADMINISTRATOR, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_payzen' . DIRECTORY_SEPARATOR . 'script.install.php';
}

use \Lyranetwork\Payzen\Sdk\Form\Api as PayzenApi;
use \Lyranetwork\Payzen\Sdk\Form\Request as PayzenRequest;
use \Lyranetwork\Payzen\Sdk\Form\Response as PayzenResponse;

class plgHikashoppaymentPayzenmulti extends hikashopPaymentPlugin
{
    var $name = 'payzenmulti';
    var $accepted_currencies = array();
    var $doc_form = 'payzenmulti';
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
     * Persist the customer's chosen instalment option in session before order save.
     *
     * @param object $cart       The current cart object
     * @param array  $rates      Available payment rates
     * @param int    $payment_id The selected payment method ID
     *
     * @return mixed Parent return value
     */
    function onPaymentSave(&$cart, &$rates, &$payment_id)
    {
        $app = Factory::getApplication();
        $session = $app->getSession();
        $session->set('payzen_multi_option', $app->input->getVar('payzen_multi_option'));

        return parent::onPaymentSave($cart, $rates, $payment_id);
    }

    /**
     * Called by HikaShop before redirecting to the payment gateway.
     *
     * Builds the payment parameters array (including instalment config) from the order
     * data and plugin settings, then triggers the redirect form page.
     *
     * @param object $order     The current order object
     * @param array  $methods   Available payment methods
     * @param int    $method_id The selected payment method ID
     *
     * @return mixed False on configuration error, otherwise the result of showPage()
     */
    function onAfterOrderConfirm(&$order, &$methods, $method_id)
    {
        parent::onAfterOrderConfirm($order, $methods, $method_id);
        $app = Factory::getApplication();

        // Process shop language.
        $lang = $app->getLanguage();
        $langCode = strtoupper(substr($lang->get('tag'), 0, 2));
        $payzenmultiLanguage = PayzenApi::isSupportedLanguage($langCode) ? $langCode : $this->payment_params->payzenmulti_language;

        // Process currency.
        $payzenmultiCurrency = PayzenApi::findCurrencyByAlphaCode($this->currency->currency_code);
        if ($payzenmultiCurrency === null) {
            $this->log('Unsupported currency: ' . $this->currency->currency_code, 'ERROR');

            return false;
        }

        // Amount.
        $price = $order->cart->full_total->prices[0];
        $amount = round(
            $price->price_value_with_tax,
            hikashop_get('class.currency')->getRounding($price->price_currency_id)
        );

        // 3DS activation according to amount.
        $threedsMpi = null;
        if (! empty($this->payment_params->payzenmulti_threeds_amount_min) &&
             $amount < $this->payment_params->payzenmulti_threeds_amount_min) {
            $threedsMpi = '2';
        }

        // Load config to retrieve hikashop version.
        $config = hikashop_config();

        $billingAddress = $order->cart->billing_address ?? null;
        $shippingAddress = $order->cart->shipping_address ?? null;

        $this->vars = array(
            'amount' => $payzenmultiCurrency->convertAmountToInteger($amount),
            'contrib' => 'HikaShop_2.x-6.x_2.2.1/' . JVERSION . '_' . $config->get('version') . '/' . PayzenApi::shortPhpVersion(),
            'currency' => $payzenmultiCurrency->getNum(),
            'language' => $payzenmultiLanguage,
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
            'ship_to_country' => isset($shippingAddress->address_country) !== null ? ($shippingAddress->address_country->zone_code_2 ?? "") : "",
            'ship_to_phone_num' => $shippingAddress->address_telephone ?? "",
            'ship_to_zip' => $shippingAddress->address_post_code ?? "",

            'url_return' => HIKASHOP_LIVE .
                'index.php?option=com_hikashop&ctrl=checkout&task=notify&notif_payment=payzenmulti&tmpl=component&Itemid=' .
                 $app->input->getInt('Itemid'),
            'payment_method_id' => $method_id
        );

        foreach ($this->param_keys as $param) {
            $paramName = 'payzenmulti_' . $param;
            $this->vars[$param] = $this->payment_params->$paramName;
        }

        // Prepare payment in installments data.
        $multiOptions = is_array($this->payment_params->payzen_multi_options ?? null) ? $this->payment_params->payzen_multi_options : array();
        if (empty($multiOptions)) {
            $this->log('No payment in installments option configured.', 'ERROR');

            return false;
        }

        $session = $app->getSession();
        $selectedKey = $session->get('payzen_multi_option');
        $selectedOption = isset($multiOptions[$selectedKey]) ? $multiOptions[$selectedKey] : reset($multiOptions); // The selected payment option.
        if (! is_array($selectedOption)) {
            $this->log('Unable to resolve a valid payment in installments option.', 'ERROR');

            return false;
        }

        $configFirst = $selectedOption['first'] ?? null;
        $count = $selectedOption['count'] ?? null;
        $period = $selectedOption['period'] ?? null;
        if (! is_numeric($count) || ! is_numeric($period)) {
            $this->log('Invalid payment in installments option: count/period must be numeric.', 'ERROR');

            return false;
        }

        $first = (is_numeric($configFirst) && (float) $configFirst > 0)
            ? $payzenmultiCurrency->convertAmountToInteger(((float) $configFirst / 100) * $amount)
            : null;
        $this->multivars = array(
            'count' => (int) $count,
            'period' => (int) $period,
            'first' => $first
        );

        if (isset($selectedOption['contract']) && $selectedOption['contract']) {
            $this->multivars['contract'] = 'CB=' . $selectedOption['contract'];
        }

        return $this->showPage('end');
    }

    /**
     * Handle the payment notification from the gateway (IPN server call or return URL).
     *
     * Validates the gateway signature, retrieves the matching order, and updates
     * its status according to the payment result. Delegates to the single-payment
     * plugin when the notification is not for a multi/instalment payment.
     * Terminates with a gateway-specific acknowledgement string on server-to-server calls.
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
            if ((! ($payCfg = $input->getVar('vads_payment_config')) || stripos($payCfg, 'MULTI') === false) &&
                (! ($contrib = $input->getVar('vads_contrib')) || stripos($contrib, 'multi') === false)) {

                $data = hikashop_import('hikashoppayment', 'payzen');
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
        $elements = $pluginsClass->getMethods('payment', 'payzenmulti');
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

        $payzenmultiResponse = new PayzenResponse(
            $requestData,
            $element->payment_params->payzenmulti_ctx_mode,
            $element->payment_params->payzenmulti_key_test,
            $element->payment_params->payzenmulti_key_prod,
            $element->payment_params->payzenmulti_sign_algo
         );

        $fromServer = ($payzenmultiResponse->get('hash') !== null);

        if (! $payzenmultiResponse->isAuthentified()) {
            $this->log("Received invalid response from return/IPN URL with data: " . print_r($requestData, true));
            $this->log('Signature algorithm selected in module settings must be the same as one selected in gateway Back Office.');

            if ($fromServer) {
                $this->log('SERVER URL PROCESS END');
                die($payzenmultiResponse->getOutputForGateway('auth_fail'));
            } else {
                $this->log('RETURN URL PROCESS END');
                $app->enqueueMessage(JText::_('PAYZENMULTI_ERROR_MSG'), 'error');
                $app->redirect(hikashop_completeLink('order' . $urlItemId, false, true));
                die();
            }
        }

        // Retrieve order info from database.
        $orderClass = hikashop::get('class.order');
        $orderId = hikashop::decode($payzenmultiResponse->get('order_id'));
        $order = $orderClass->get($orderId);

        if (empty($order)) {
            // Order not found.
            $this->log('Error: Order (' . $orderId . ') not found or key does not match received invoice ID.');

            if ($fromServer) {
                $this->log('SERVER URL PROCESS END');
                die($payzenmultiResponse->getOutputForGateway('order_not_found'));
            } else {
                $this->log('RETURN URL PROCESS END');
                $app->enqueueMessage(JText::_('PAYZENMULTI_ERROR_MSG'), 'error');
                $app->redirect(hikashop_completeLink('order' . $urlItemId, false, true));
                die();
            }
        }

        if ($element->payment_params->payzenmulti_ctx_mode === 'TEST' && $this->plugin_features['prodfaq']) {
            $app->enqueueMessage(JText::_('PAYZENMULTI_SHOP_TO_PROD_INFO'));
        }

        // Redirect to those URLs.
        $successUrl = hikashop_completeLink('checkout&task=after_end&order_id=' . $order->order_id . $urlItemId, false, true);
        $errorUrl = hikashop_completeLink('order&task=cancel_order&order_id=' . $order->order_id . $urlItemId, false, true);

        // If unpaid order : reset order status.
        $hikashopConfig = hikashop_config();
        $unpaidStatusesRaw = $hikashopConfig->get('order_unpaid_statuses');
        $unpaidStatuses = $unpaidStatusesRaw ? explode(',', $unpaidStatusesRaw) : array();
        if ($hikashopConfig->get('allow_payment_button') && in_array($order->order_status, $unpaidStatuses)) {
            $order->order_status = $hikashopConfig->get('order_created_status');
        }

        // Process according to order status and payment result.
        if ($order->order_status === $hikashopConfig->get('order_created_status')) {
            // Order not processed yet.
            if ($payzenmultiResponse->isAcceptedPayment()) {
                $this->log('Payment successfull, let\'s save order #' . $orderId);

                if (method_exists($this, 'modifyOrder')) {
                    $history = $this->_createOrderHistory($payzenmultiResponse, $element, 1);
                    $this->modifyOrder($order->order_id, $element->payment_params->payzenmulti_verified_status,
                        $history);
                } else {
                    $this->_confirmOrder(
                        $order,
                        $element->payment_params->payzenmulti_verified_status,
                        $element,
                        $payzenmultiResponse,
                        1
                    );
                }

                if ($fromServer) {
                    $this->log('Payment completed successfully by server URL call.');
                    $this->log('SERVER URL PROCESS END');
                    die($payzenmultiResponse->getOutputForGateway('payment_ok'));
                } else {
                    $this->log('Warning ! IPN URL call has not worked. Payment completed by return URL call.');
                    if ($element->payment_params->payzenmulti_ctx_mode === 'TEST') {
                        // Test mode warning : check URL not correctly called.
                        $app->enqueueMessage(JText::_('PAYZENMULTI_CHECK_URL_WARN') . '<br />' . JText::_('PAYZENMULTI_CHECK_URL_WARN_DETAILS'), 'error');
                    }

                    $this->log('RETURN URL PROCESS END');
                    $app->redirect($successUrl);
                    die();
                }
            } else {
                if (method_exists($this, 'modifyOrder')) {
                    $history = $this->_createOrderHistory($payzenmultiResponse, $element);
                    $this->modifyOrder($order->order_id, $element->payment_params->payzenmulti_invalid_status, $history);
                } else {
                    $this->_confirmOrder(
                        $order,
                        $element->payment_params->payzenmulti_invalid_status,
                        $element,
                        $payzenmultiResponse
                    );
                }

                $this->log('Payment failed or cancelled. ' . $payzenmultiResponse->getLogMessage());
                if ($fromServer) {
                    $this->log('SERVER URL PROCESS END');
                    die($payzenmultiResponse->getOutputForGateway('payment_ko'));
                } else {
                    $this->log('RETURN URL PROCESS END');
                    $app->enqueueMessage(JText::_('PAYZENMULTI_FAILURE_MSG'), 'error');
                    $app->redirect($errorUrl);
                    die();
                }
            }
        } else {
            // Order already processed.
            $this->log('Order #' . $orderId . ' is already processed. Just show payment result.');
            if ($payzenmultiResponse->isAcceptedPayment() &&
                 ($order->order_status === $element->payment_params->payzenmulti_verified_status)) {
                $this->log('Payment successfull reconfirmed.');
                if ($fromServer) {
                    $this->log('SERVER URL PROCESS END');
                    die($payzenmultiResponse->getOutputForGateway('payment_ok_already_done'));
                } else {
                    $this->log('RETURN URL PROCESS END');
                    $app->redirect($successUrl);
                    die();
                }
            } elseif (! $payzenmultiResponse->isAcceptedPayment() &&
                 ($order->order_status === $element->payment_params->payzenmulti_invalid_status)) {
                $this->log('Payment failed reconfirmed.');
                if ($fromServer) {
                    $this->log('SERVER URL PROCESS END');
                    die($payzenmultiResponse->getOutputForGateway('payment_ko_already_done'));
                } else {
                    $this->log('RETURN URL PROCESS END');
                    $app->enqueueMessage(JText::_('PAYZENMULTI_FAILURE_MSG'), 'error');
                    $app->redirect($errorUrl);
                    die();
                }
            } else {
                $this->log(
                    'Error ! Invalid payment result received for already saved order. Payment result : ' .
                    $payzenmultiResponse->get('result') . ', Order status : ' . $order->order_status
                );

                if ($fromServer) {
                    $this->log('SERVER URL PROCESS END');
                    die($payzenmultiResponse->getOutputForGateway('payment_ko_on_order_ok'));
                } else {
                    $this->log('RETURN URL PROCESS END');
                    $app->enqueueMessage(JText::_('PAYZENMULTI_ERROR_MSG'), 'error');
                    $app->redirect(hikashop_completeLink('order' . $urlItemId, false, true));
                    die();
                }
            }
        }
    }

    /**
     * Update an order status and persist an associated history entry.
     *
     * @param object          $orderData           The original order object
     * @param string          $newStatus           The new order status to apply
     * @param object          $payment             The payment method configuration object
     * @param PayzenResponse  $payzenmultiResponse The gateway response object
     * @param int             $notify              Whether to notify the customer: 1 yes, 0 no (default)
     *
     * @return void
     */
    function _confirmOrder($orderData, $newStatus, $payment, $payzenmultiResponse, $notify = 0)
    {
        // Prepare order and history order.
        $order = new stdClass();
        $order->order_id = $orderData->order_id;
        $order->order_status = $newStatus;

        $order->old_status = new stdClass();
        $order->old_status->order_status = $orderData->order_status;

        $order->history = new stdClass();
        $history = $this->_createOrderHistory($payzenmultiResponse, $payment, $notify);
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
     * @param PayzenResponse $payzenmultiResponse The gateway response object
     * @param object         $payment             The payment method configuration object
     * @param int            $notify              Whether to notify the customer: 1 yes, 0 no (default)
     *
     * @return stdClass The populated history entry object
     */
    function _createOrderHistory($payzenmultiResponse, $payment, $notify = 0)
    {
        $currencyObj = PayzenApi::findCurrencyByNumCode($payzenmultiResponse->get('currency'));
        $currencyCode = ($currencyObj !== null) ? $currencyObj->getAlpha3() : '';
        $history = new stdClass();
        $history->amount = $payzenmultiResponse->getFloatAmount() . ' ' . $currencyCode;
        $history->reason = JText::_('AUTOMATIC_PAYMENT_NOTIFICATION');
        $history->payment_id = $payment->payment_id;
        $history->payment_method = $payment->payment_type;
        $history->type = 'payment';
        $history->notified = $notify;

        $info = JText::_('PAYZENMULTI_RESULT') . $payzenmultiResponse->getMessage();

        $info .= ' | ' . JText::_('PAYZENMULTI_TRANS_ID') . $payzenmultiResponse->get('trans_id');

        if ($payzenmultiResponse->get('card_brand')) {
            $info .= ' | ' . JText::_('PAYZENMULTI_CC_TYPE') . $payzenmultiResponse->get('card_brand');

            // Add card brand user choice.
            if ($payzenmultiResponse->get('brand_management')) {
                $brandInfo = json_decode($payzenmultiResponse->get('brand_management'));
                $msgBrandChoice = '';

                if (isset($brandInfo->userChoice) && $brandInfo->userChoice) {
                    $msgBrandChoice .= JText::_('PAYZENMULTI_CARD_BRAND_BUYER_CHOICE');
                } else {
                    $msgBrandChoice .= JText::_('PAYZENMULTI_CARD_BRAND_DEFAULT_CHOICE');
                }

                $info .= ' (' . $msgBrandChoice . ')';
            }
        }

        if ($payzenmultiResponse->get('card_number')) {
            $info .= ' | ' . JText::_('PAYZENMULTI_CC_NUMBER') . $payzenmultiResponse->get('card_number');
        }

        if ($payzenmultiResponse->get('expiry_month') && $payzenmultiResponse->get('expiry_year')) {
            $info .= ' | ' . JText::_('PAYZENMULTI_CC_EXPIRY') .
                 str_pad($payzenmultiResponse->get('expiry_month'), 2, '0', STR_PAD_LEFT) . ' / ' .
                 $payzenmultiResponse->get('expiry_year');
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
        $this->title = JText::_('PAYZENMULTI_CONFIG_PAGE_TITLE');
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
        $element->payment_name = JText::_('PAYZENMULTI_DEFAULT_TITLE');
        $element->payment_description = JText::_('PAYZENMULTI_DEFAULT_DESCRIPTION');
        $element->payment_images = 'payzenmulti_cards';

        // Default values.
        $element->payment_params->payzenmulti_site_id = '12345678';
        $element->payment_params->payzenmulti_key_test = '1111111111111111';
        $element->payment_params->payzenmulti_key_prod = '2222222222222222';
        $element->payment_params->payzenmulti_ctx_mode = 'TEST';
        $element->payment_params->payzenmulti_sign_algo = 'SHA-256';
        $element->payment_params->payzenmulti_platform_url = $this->platform_url;
        $element->payment_params->payzenmulti_language = 'fr';
        $element->payment_params->payzenmulti_available_languages = '';
        $element->payment_params->payzenmulti_capture_delay = '';
        $element->payment_params->payzenmulti_validation_mode = '';
        $element->payment_params->payzenmulti_payment_cards = '';
        $element->payment_params->payzenmulti_threeds_amount_min = '';
        $element->payment_params->payzenmulti_redirect_enabled = 0;
        $element->payment_params->payzenmulti_redirect_success_timeout = '5';
        $element->payment_params->payzenmulti_redirect_success_message = JText::_('PAYZENMULTI_REDIRECT_SUCCESS_MESSAGE_DFEAULT');
        $element->payment_params->payzenmulti_redirect_error_timeout = '5';
        $element->payment_params->payzenmulti_redirect_error_message = JText::_('PAYZENMULTI_REDIRECT_ERROR_MESSAGE_DFEAULT');
        $element->payment_params->payzenmulti_return_mode = 'GET';
        $element->payment_params->payzenmulti_verified_status = 'confirmed';
        $element->payment_params->payzenmulti_invalid_status = 'cancelled';
    }

    /**
     * Called before the plugin configuration is saved.
     *
     * Normalises multi-value fields, validates instalment options and all payment
     * parameters against the gateway API. Enqueues error messages and returns false
     * when validation fails.
     *
     * @param object $element The payment method element being saved
     *
     * @return boolean True on success, false if validation errors occurred
     */
    function onPaymentConfigurationSave(&$element)
    {
        $langs = $element->payment_params->payzenmulti_available_languages ?? null;
        if (! is_array($langs)) {
            $langs = array();
        }

        $element->payment_params->payzenmulti_available_languages = implode(';', $langs);

        $cards = $element->payment_params->payzenmulti_payment_cards ?? null;
        if (! is_array($cards)) {
            $cards = array();
        }

        $element->payment_params->payzenmulti_payment_cards = implode(';', $cards);

        $platformUrl = $element->payment_params->payzenmulti_platform_url ?? null;
        if ($platformUrl == null) {
            $element->payment_params->payzenmulti_platform_url = $this->platform_url;
        }

        // Configuration fields validation.
        $errors = array();
        $multiOptions = $element->payment_params->payzen_multi_options ?? null;
        if (! is_array($multiOptions)) {
            $multiOptions = array();
        }

        $line = 1;
        foreach ($multiOptions as $option) {
            $count = $option['count'] ?? null;
            $period = $option['period'] ?? null;
            $first = $option['first'] ?? null;

            if (! is_numeric($count) || $count < 0) {
                $errors[] = sprintf(JText::_('PAYZENMULTI_ERROR_SAVE_MULTI_OPTION'), JText::_('PAYZENMULTI_COUNT'),
                    $line);
            }

            if (! is_numeric($period) || $period < 0) {
                $errors[] = sprintf(JText::_('PAYZENMULTI_ERROR_SAVE_MULTI_OPTION'), JText::_('PAYZENMULTI_PERIOD'),
                    $line);
            }

            if ($first !== '' && $first !== null && (! is_numeric($first) || $first < 0 || $first > 100)) {
                $errors[] = sprintf(JText::_('PAYZENMULTI_ERROR_SAVE_MULTI_OPTION'), JText::_('PAYZENMULTI_FIRST'),
                    $line);
            }

            $line ++;
        }

        // Instanciate PayzenRequest to validate parameters.
        $request = new PayzenRequest();

        foreach ($this->param_keys as $param) {
            $paramName = 'payzenmulti_' . $param;
            $value = $element->payment_params->$paramName ?? null;

            if (! $request->set($param, $value)) {
                $errors[] = sprintf(JText::_('PAYZENMULTI_ERROR_SAVE'), JText::_($paramName));
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
     * Filter instalment options by order amount and inject the choice widget into the payment method HTML.
     *
     * Options whose min/max amount constraints do not match the current order total are removed.
     * Payment methods with no remaining valid options are hidden from the checkout.
     *
     * @param object $order         The current order object
     * @param array  $methods       All payment methods indexed by key
     * @param array  $usable_methods Methods already deemed usable
     *
     * @return mixed Parent return value
     */
    function onPaymentDisplay(&$order, &$methods, &$usable_methods)
    {
        if (isset($methods)) {
            $orderTotal = $order->full_total->prices[0]->price_value_with_tax;
            foreach ($methods as $key => $method) {
                if ($method->payment_type === $this->name) {
                    $multiOptions = property_exists($method->payment_params, "payzen_multi_options") ? $this->_getAvailbleMultiOptions($method->payment_params->payzen_multi_options,
                        $orderTotal) : array();

                    if (! count($multiOptions)) {
                        unset($methods[$key]);
                    } else {
                        $method->custom_html = $this->_getCustomHtml($multiOptions);
                    }
                }
            }
        }

        return parent::onPaymentDisplay($order, $methods, $usable_methods);
    }

    /**
     * Filter instalment options according to the current order total.
     *
     * Returns all options unchanged when no order total is provided.
     *
     * @param array      $options    Raw instalment options array from plugin configuration
     * @param float|null $orderTotal Current order total used for min/max filtering
     *
     * @return array Filtered array of available instalment options
     */
    function _getAvailbleMultiOptions($options, $orderTotal = null)
    {
        if (! is_array($options) || ! count($options)) {
            return array();
        }

        if (! $orderTotal) {
            return $options;
        }

        $availableAptions = array();
        foreach ($options as $key => $option) {
            $amountMin = $option['amount_min'] ?? null;
            $amountMax = $option['amount_max'] ?? null;
            if (($amountMax && $orderTotal > $amountMax) ||
                ($amountMin && $orderTotal < $amountMin)) {
                continue;
            }

            $availableAptions[$key] = $option;
        }

        return $availableAptions;
    }

    /**
     * Build the HTML radio-list widget for selecting an instalment option at checkout.
     *
     * Restores the previously selected option from session when available.
     *
     * @param array $multiOptions Available instalment options (already filtered by amount)
     *
     * @return string HTML string for the instalment option selector
     */
    function _getCustomHtml($multiOptions)
    {
        $title = (count($multiOptions) === 1) ? JText::_('PAYZENMULTI_ONE_OPTION_SELECT_TITLE') :
            JText::_('PAYZENMULTI_SEVERAL_OPTIONS_SELECT_TITLE');

        $result = array();
        $selected = false;
        $first = true;
        foreach ($multiOptions as $key => $option) {
            $result[] = HTMLHelper::_('select.option', $key, $option['label'] ?? '');

            if ($first) {
                $selected = $key;
                $first = false;
            }
        }

        $session = Factory::getApplication()->getSession();
        if (($key = $session->get('payzen_multi_option')) && isset($multiOptions[$key])) {
            $selected = $key;
        }

        $onclick = '';
        $config = hikashop_config();
        if ($config->get('auto_submit_methods', 1)) {
            $onclick = ' onclick="this.form.action=this.form.action+\'#hikashop_payment_methods\'; this.form.submit(); return false;"';
        }

        $html = '<div style="margin-left:10%; ">';
        $html .= '<span style="font-weight: bold;">' . $title . '</span>';
        $html .= HTMLHelper::_('select.radiolist', $result, 'payzen_multi_option', 'class="inputbox" size="1" ' . $onclick,
            'value', 'text', $selected);
        $html .= '</div>';

        $doc = Factory::getApplication()->getDocument();
        $doc->addScript(JUri::root(true) . '/plugins/hikashoppayment/payzenmulti/assets/js/payzenmulti.js');

        return $html;
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

        if (! (File::exists($destFolder . DIRECTORY_SEPARATOR . 'payzenmulti_cards.png'))) {
            File::copy($sourceFolder . DIRECTORY_SEPARATOR . 'payzenmulti_cards.png', $destFolder . DIRECTORY_SEPARATOR . 'payzenmulti_cards.png');
        }

        if (! (File::exists($destFolder . DIRECTORY_SEPARATOR . 'payzenmulti.png'))) {
            File::copy($sourceFolder . DIRECTORY_SEPARATOR . 'payzenmulti.png', $destFolder . DIRECTORY_SEPARATOR . 'payzenmulti.png');
        }
    }

    /**
     * Return the subset of gateway-supported card types that are eligible for instalment payments.
     *
     * @return array Associative array of card type code => label
     */
    public static function getAvailableMultiCards()
    {
        $multi_cards = array(
            'AMEX',
            'CB',
            'DINERS',
            'DISCOVER',
            'E-CARTEBLEUE',
            'JCB',
            'MASTERCARD',
            'PRV_BDP',
            'PRV_BDT',
            'PRV_OPT',
            'PRV_SOC',
            'VISA',
            'VISA_ELECTRON',
            'VPAY'
        );

        $allCards = PayzenApi::getSupportedCardTypes();
        $availableCards = array();

        foreach ($allCards as $key => $value) {
            if (in_array($key, $multi_cards)) {
                $availableCards[$key] = $value;
            }
        }

        return $availableCards;
    }

    /**
     * Write a message to the multi-payment plugin log file.
     *
     * @param string $msg   The message to log
     * @param string $level The log level (default 'INFO')
     *
     * @return void
     */
    function log($msg, $level = 'INFO')
    {
        com_payzenInstallerScript::log($msg, 'payzenmulti.log', $level);
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
