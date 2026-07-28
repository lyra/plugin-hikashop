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
use Joomla\CMS\Language\Text as JText;

require_once rtrim(JPATH_ADMINISTRATOR, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_payzen' . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR . 'sdk-autoload.php';
require_once rtrim(JPATH_ADMINISTRATOR, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_payzen' . DIRECTORY_SEPARATOR . 'script.install.php';

$payzenRequest = new \Lyranetwork\Payzen\Sdk\Form\Request();
$payzenRequest->addExtInfo('payment_method_id', $this->vars['payment_method_id']);
$payzenRequest->setFromArray($this->vars);

$payzenRequest->setMultiPayment(
    null /* Let API use already set amount. */,
    $this->multivars['first'],
    $this->multivars['count'],
    $this->multivars['period']
);

if (isset($this->multivars['contract']) && $this->multivars['contract']) {
    $payzenRequest->set('contracts', $this->multivars['contract']);
}

// Log data that will be sent to payment gateway.
$msg =('Data to be sent to payment gateway : ' . print_r($payzenRequest->getRequestFieldsArray(true /* To hide sensitive data. */), true));
com_payzenInstallerScript::log($msg, 'payzenmulti.log');
?>

<div class="hikashop_payzenmulti_end" id="hikashop_payzenmulti_end">
    <span id="hikashop_payzenmulti_end_message" class="hikashop_payzenmulti_end_message">
        <?php echo JText::_('PAYZENMULTI_PLEASE_WAIT_BEFORE_REDIRECTION') . '<br/>' . JText::_('PAYZENMULTI_CLICK_ON_BUTTON_IF_NOT_REDIRECTED'); ?>
    </span>
    <span id="hikashop_payzenmulti_end_spinner" class="hikashop_payzenmulti_end_spinner">
        <img src="<?php echo HIKASHOP_IMAGES . 'spinner.gif'; ?>" />
    </span>
    <br/>
    <form id="hikashop_payzenmulti_form" name="hikashop_payzenmulti_form" action="<?php echo $payzenRequest->get('platform_url'); ?>" method="post">
        <div id="hikashop_payzenmulti_end_image" class="hikashop_payzenmulti_end_image">
            <input id="hikashop_payzenmulti_button" type="submit" value="<?php echo JText::_('PAYZENMULTI_SEND_BTN_VALUE'); ?>" name="" alt="<?php echo JText::_('PAYZENMULTI_SEND_BTN_ALT'); ?>" />
        </div>
        <?php
        echo $payzenRequest->getRequestHtmlFields();

        $doc = Factory::getApplication()->getDocument();
        $doc->addScriptDeclaration("window.hikashop.ready( function() { document.getElementById('hikashop_payzenmulti_form').submit(); });");
        hikaInput::get()->set('noform', 1);
        ?>
    </form>
</div>
