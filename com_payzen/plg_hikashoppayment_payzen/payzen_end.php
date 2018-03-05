<?php
/**
 * PayZen V2-Payment Module version 2.0.0 for HikaShop 2.x-3.x. Support contact : support@payzen.eu.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author    Lyra Network (http://www.lyra-network.com/)
 * @copyright 2014-2017 Lyra Network and contributors
 * @license   http://www.gnu.org/licenses/gpl.html  GNU General Public License (GPL v3)
 * @category  payment
 * @package   payzen
 */
defined('_JEXEC') or die('Restricted access');

require_once (rtrim(JPATH_ADMINISTRATOR, DS) . DS . 'components' . DS . 'com_payzen' . DS . 'classes' . DS .
     'payzen_request.php');
$payzen = new PayzenRequest();
$payzen->setFromArray($this->vars);

// @formatter:off
?>

<div class="hikashop_payzen_end" id="hikashop_payzen_end">
    <span id="hikashop_payzen_end_message" class="hikashop_payzen_end_message">
        <?php echo JText::_('PAYZEN_PLEASE_WAIT_BEFORE_REDIRECTION').'<br/>'. JText::_('PAYZEN_CLICK_ON_BUTTON_IF_NOT_REDIRECTED'); ?>
    </span>
    <span id="hikashop_payzen_end_spinner" class="hikashop_payzen_end_spinner">
        <img src="<?php echo HIKASHOP_IMAGES . 'spinner.gif'; ?>" />
    </span>
    <br/>
    <form id="hikashop_payzen_form" name="hikashop_payzen_form" action="<?php echo $payzen->get('platform_url'); ?>" method="post">
        <div id="hikashop_payzen_end_image" class="hikashop_payzen_end_image">
            <input id="hikashop_payzen_button" type="submit" value="<?php echo JText::_('PAYZEN_SEND_BTN_VALUE'); ?>" name="" alt="<?php echo JText::_('PAYZEN_SEND_BTN_ALT'); ?>" />
        </div>
        <?php
        // @formatter:on
        echo $payzen->getRequestHtmlFields();

        $doc = JFactory::getDocument();
        $doc->addScriptDeclaration(
            "window.addEvent('domready', function() { document.getElementById('hikashop_payzen_form').submit(); });");
        JRequest::setVar('noform', 1);
        ?>
    </form>
</div>
