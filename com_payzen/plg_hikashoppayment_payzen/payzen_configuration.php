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

$params = $this->element->payment_params;

// @formatter:off
?>

<!------------------------------- module information ------------------------------------------>
<tr>
    <td colspan="2">
        <fieldset>
            <legend><?php echo JText::_('PAYZEN_MODULE_INFORMATION'); ?></legend>

            <table>
                <tr>
                    <td class="key">
                        <label><?php echo JText::_('PAYZEN_DEVELOPED_BY'); ?></label>
                    </td>
                    <td>
                        <a href="http://www.lyra-network.com/" target="_blank">Lyra Network</a>
                    </td>
                </tr>

                <tr>
                    <td class="key">
                        <label><?php echo JText::_('PAYZEN_CONTACT_EMAIL'); ?></label>
                    </td>
                    <td>
                        <a href="mailto:support@payzen.eu">support@payzen.eu</a>
                    </td>
                </tr>

                <tr>
                    <td class="key">
                        <label><?php echo JText::_('PAYZEN_CONTRIB_VERSION'); ?></label>
                    </td>
                    <td>
                        <label>2.0.0</label>
                    </td>
                </tr>

                <tr>
                    <td class="key">
                        <label><?php echo JText::_('PAYZEN_PLATFORM_VERSION'); ?></label>
                    </td>
                    <td>
                        <label>V2</label>
                    </td>
                </tr>

                <tr>
                    <td class="key">
                        <label><?php echo JText::_('PAYZEN_DOCUMENTAION'); ?></label>
                    </td>
                    <td>
                        <?php
                        $docUrl = HIKASHOP_LIVE . 'administrator' . DS . 'components' . DS. 'com_payzen' . DS . 'installation_doc' . DS .
                            'Integration_PayZen_HikaShop_2.x-3.x_v2.0.0.pdf';
                        ?>
                        <a style="color: red;" target="_blank" href="<?php echo $docUrl?>"><?php echo JText::_ ('PAYZEN_DOCUMENTAION_TEXT'); ?></a>
                    </td>
                </tr>
            </table>
        </fieldset>
    </td>
</tr>

<!------------------------------- platform access ------------------------------------------>
<tr>
    <td colspan="2">
        <fieldset>
            <legend><?php echo JText::_('PAYZEN_PLATFORM_ACCESS'); ?></legend>

            <table>
                <tr>
                    <td class="key" style="vertical-align: top; white-space: normal !important;">
                        <label for="payzen_site_id"><?php echo JText::_('PAYZEN_SITE_ID'); ?></label>
                    </td>
                    <td>
                        <input type="text" name="data[payment][payment_params][payzen_site_id]" value="<?php echo @$params->payzen_site_id; ?>" id="payzen_site_id" style="width: 120px;" /><br />
                        <span style="font-size: 10px; font-style: italic;"><?php echo JText::_('PAYZEN_SITE_ID_DESC'); ?></span>
                    </td>
                </tr>

                <tr>
                    <td class="key" style="vertical-align: top; white-space: normal !important;">
                        <label for="payzen_key_test"><?php echo JText::_('PAYZEN_KEY_TEST'); ?></label>
                    </td>
                    <td>
                        <input type="text" name="data[payment][payment_params][payzen_key_test]" value="<?php echo @$params->payzen_key_test; ?>" id="payzen_key_test" style="width: 120px;" /><br />
                        <span style="font-size: 10px; font-style: italic;"><?php echo JText::_('PAYZEN_KEY_TEST_DESC'); ?></span>
                    </td>
                </tr>

                <tr>
                    <td class="key" style="vertical-align: top; white-space: normal !important;">
                        <label for="payzen_key_prod"><?php echo JText::_('PAYZEN_KEY_PROD'); ?></label>
                    </td>
                    <td>
                        <input type="text" name="data[payment][payment_params][payzen_key_prod]" value="<?php echo @$params->payzen_key_prod; ?>" id="payzen_key_prod" style="width: 120px;" /><br />
                        <span style="font-size: 10px; font-style: italic;"><?php echo JText::_('PAYZEN_KEY_PROD_DESC'); ?></span>
                    </td>
                </tr>

                <tr>
                    <td class="key" style="vertical-align: top; white-space: normal !important;">
                        <label for="payzen_ctx_mode"><?php echo JText::_('PAYZEN_CTX_MODE'); ?></label>
                    </td>
                    <td>
                        <select name="data[payment][payment_params][payzen_ctx_mode]" class="inputbox" id="payzen_ctx_mode" style="width: 122px;" >
                            <option <?php if (@$params->payzen_ctx_mode == 'TEST') echo 'selected="selected"'; ?> value="TEST"><?php echo JText::_('PAYZEN_CTX_MODE_TEST'); ?></option>
                            <option <?php if (@$params->payzen_ctx_mode == 'PRODUCTION') echo 'selected="selected"'; ?> value="PRODUCTION"><?php echo JText::_('PAYZEN_CTX_MODE_PROD'); ?></option>
                        </select><br />
                        <span style="font-size: 10px; font-style: italic;"><?php echo JText::_('PAYZEN_CTX_MODE_DESC'); ?></span>
                    </td>
                </tr>

                <tr>
                    <td class="key" style="vertical-align: top; white-space: normal !important;">
                        <label for="payzen_platform_url"><?php echo JText::_('PAYZEN_PLATFORM_URL'); ?></label>
                    </td>
                    <td>
                        <input type="text" name="data[payment][payment_params][payzen_platform_url]" value="<?php echo @$params->payzen_platform_url; ?>" id="payzen_platform_url" style="width: 300px;" /><br />
                        <span style="font-size: 10px; font-style: italic;"><?php echo JText::_('PAYZEN_PLATFORM_URL_DESC'); ?></span>
                    </td>
                </tr>

                <tr>
                    <td class="key" style="vertical-align: top; white-space: normal !important;">
                        <label><?php echo JText::_('PAYZEN_IPN_URL'); ?></label>
                    </td>
                    <td>
                        <label><?php echo '<b>' . HIKASHOP_LIVE . 'index.php?option=com_hikashop&amp;ctrl=checkout&amp;task=notify&amp;notif_payment=payzen&amp;tmpl=component</b>'; ?></label><br />
                        <span style="font-size: 10px; font-style: italic;"><?php echo JText::_('PAYZEN_IPN_URL_DESC'); ?></span>
                    </td>
                </tr>
            </table>
        </fieldset>
    </td>
</tr>

<!------------------------------- payment page ------------------------------------------>
<tr>
    <td colspan="2">
        <fieldset>
            <legend><?php echo JText::_('PAYZEN_PAYMENT_PAGE'); ?></legend>

            <table>
                <tr>
                    <td class="key" style="vertical-align: top; white-space: normal !important;">
                        <label for="payzen_language"><?php echo JText::_('PAYZEN_LANGUAGE'); ?></label>
                    </td>
                    <td>
                        <select name="data[payment][payment_params][payzen_language]" class="inputbox" id="payzen_language" style="width: 122px;" >
                            <?php
                            foreach (PayzenApi::getSupportedLanguages() as $code => $label) {
                                $selected = (@$params->payzen_language == $code) ? ' selected="selected"' : '';
                                echo '<option' . $selected . ' value="'. $code . '">' . JText::_('PAYZEN_LANGUAGE_' . strtoupper($label)) . '</option>';
                            }
                            ?>
                        </select><br />
                        <span style="font-size: 10px; font-style: italic;"><?php echo JText::_('PAYZEN_LANGUAGE_DESC'); ?></span>
                    </td>
                </tr>

                <tr>
                    <td class="key" style="vertical-align: top; white-space: normal !important;">
                        <label for="payzen_available_languages"><?php echo JText::_('PAYZEN_AVAILABLE_LANGUAGES'); ?></label>
                    </td>
                    <td>
                        <select name="data[payment][payment_params][payzen_available_languages][]" class="inputbox" multiple="multiple" size="8" id="payzen_available_languages" style="width: 122px;" >
                            <?php
                            $langs = @$params->payzen_available_languages ? explode(';', $params->payzen_available_languages) : array();
                            foreach (PayzenApi::getSupportedLanguages() as $code => $label) {
                                $selected = in_array($code, $langs) ? ' selected="selected"' : '';
                                echo '<option' . $selected . ' value="'. $code . '">' . JText::_('PAYZEN_LANGUAGE_' . strtoupper($label)) . '</option>';
                            }
                            ?>
                        </select><br />
                        <span style="font-size: 10px; font-style: italic;"><?php echo JText::_('PAYZEN_AVAILABLE_LANGUAGES_DESC'); ?></span>
                    </td>
                </tr>

                <tr>
                    <td class="key" style="vertical-align: top; white-space: normal !important;">
                        <label for="payzen_capture_delay"><?php echo JText::_('PAYZEN_CAPTURE_DELAY'); ?></label>
                    </td>
                    <td>
                        <input type="text" name="data[payment][payment_params][payzen_capture_delay]" value="<?php echo @$params->payzen_capture_delay; ?>" id="payzen_capture_delay" style="width: 120px;" /><br />
                        <span style="font-size: 10px; font-style: italic;"><?php echo JText::_('PAYZEN_CAPTURE_DELAY_DESC'); ?></span>
                    </td>
                </tr>

                <tr>
                    <td class="key" style="vertical-align: top; white-space: normal !important;">
                        <label for="payzen_validation_mode"><?php echo JText::_('PAYZEN_VALIDATION_MODE'); ?></label>
                    </td>
                    <td>
                        <select name="data[payment][payment_params][payzen_validation_mode]" class="inputbox" id="payzen_validation_mode" style="width: 122px;" >
                            <option <?php if (@$params->payzen_validation_mode == '')  echo 'selected="selected"'; ?>value=''><?php echo JText::_('PAYZEN_MODE_DEFAULT'); ?></option>
                            <option <?php if (@$params->payzen_validation_mode == '0') echo 'selected="selected"'; ?>value='0'><?php echo JText::_('PAYZEN_MODE_AUTOMATIC'); ?></option>
                            <option <?php if (@$params->payzen_validation_mode == '1') echo 'selected="selected"'; ?>value='1'><?php echo JText::_('PAYZEN_MODE_MANUAL'); ?></option>
                        </select><br />
                        <span style="font-size: 10px; font-style: italic;"><?php echo JText::_('PAYZEN_VALIDATION_MODE_DESC'); ?></span>
                    </td>
                </tr>

                <tr>
                    <td class="key" style="vertical-align: top; white-space: normal !important;">
                        <label for="payzen_payment_cards"><?php echo JText::_('PAYZEN_PAYMENT_CARDS'); ?></label>
                    </td>
                    <td>
                        <select name="data[payment][payment_params][payzen_payment_cards][]" class="inputbox" multiple="multiple" size="8" id="payzen_payment_cards" style="width: 122px;" >
                            <?php
                            $cards = @$params->payzen_payment_cards ? explode(';', $params->payzen_payment_cards) : array();
                            foreach (PayzenApi::getSupportedCardTypes() as $code => $label) {
                                $selected = in_array($code, $cards) ? ' selected="selected"' : '';
                                echo '<option' . $selected . ' value="'. $code . '">' . $label . '</option>';
                            }
                            ?>
                        </select><br />
                        <span style="font-size: 10px; font-style: italic;"><?php echo JText::_('PAYZEN_PAYMENT_CARDS_DESC'); ?></span>
                    </td>
                </tr>
            </table>
        </fieldset>
    </td>
</tr>

<!------------------------------- selective 3-DS ------------------------------------------>
<tr>
    <td colspan="2">
        <fieldset>
            <legend><?php echo JText::_('PAYZEN_SELECTIVE_3DS'); ?></legend>

            <table>
                <tr>
                    <td class="key" style="vertical-align: top; white-space: normal !important;">
                        <label for="payzen_threeds_amount_min"><?php echo JText::_('PAYZEN_THREEDS_AMOUNT_MIN'); ?></label>
                    </td>
                    <td>
                        <input type="text" name="data[payment][payment_params][payzen_threeds_amount_min]" value="<?php echo @$params->payzen_threeds_amount_min; ?>" id="payzen_threeds_amount_min" style="width: 120px;" /><br />
                        <span style="font-size: 10px; font-style: italic;"><?php echo JText::_('PAYZEN_THREEDS_AMOUNT_MIN_DESC'); ?></span>
                    </td>
                </tr>
            </table>
        </fieldset>
    </td>
</tr>

<!------------------------------- return to shop ------------------------------------------>
<tr>
    <td colspan="2">
        <fieldset>
            <legend><?php echo JText::_('PAYZEN_RETURN_TO_SHOP'); ?></legend>

            <table>
                <tr>
                    <td class="key" style="vertical-align: top; white-space: normal !important;">
                        <label for="payzen_redirect_enabled"><?php echo JText::_('PAYZEN_REDIRECT_ENABLED'); ?></label>
                    </td>
                    <td>
                        <?php echo JHTML::_('select.booleanlist', 'data[payment][payment_params][payzen_redirect_enabled]' , '', @$params->payzen_redirect_enabled); ?><br />
                        <span style="font-size: 10px; font-style: italic;"><?php echo JText::_('PAYZEN_REDIRECT_ENABLED_DESC'); ?></span>
                    </td>
                </tr>

                <tr>
                    <td class="key" style="vertical-align: top; white-space: normal !important;">
                        <label for="payzen_redirect_success_timeout"><?php echo JText::_('PAYZEN_REDIRECT_SUCCESS_TIMEOUT'); ?></label>
                    </td>
                    <td>
                        <input type="text" name="data[payment][payment_params][payzen_redirect_success_timeout]" value="<?php echo @$params->payzen_redirect_success_timeout; ?>" id="payzen_redirect_success_timeout" style="width: 120px;" /><br />
                        <span style="font-size: 10px; font-style: italic;"><?php echo JText::_('PAYZEN_REDIRECT_SUCCESS_TIMEOUT_DESC'); ?></span>
                    </td>
                </tr>

                <tr>
                    <td class="key" style="vertical-align: top; white-space: normal !important;">
                        <label for="payzen_redirect_success_message"><?php echo JText::_('PAYZEN_REDIRECT_SUCCESS_MESSAGE'); ?></label>
                    </td>
                    <td>
                        <input type="text" name="data[payment][payment_params][payzen_redirect_success_message]" value="<?php echo @$params->payzen_redirect_success_message; ?>" id="payzen_redirect_success_message" style="width: 300px;" /><br />
                        <span style="font-size: 10px; font-style: italic;"><?php echo JText::_('PAYZEN_REDIRECT_SUCCESS_MESSAGE_DESC'); ?></span>
                    </td>
                </tr>

                <tr>
                    <td class="key" style="vertical-align: top; white-space: normal !important;">
                        <label for="payzen_redirect_error_timeout"><?php echo JText::_('PAYZEN_REDIRECT_ERROR_TIMEOUT'); ?></label>
                    </td>
                    <td>
                        <input type="text" name="data[payment][payment_params][payzen_redirect_error_timeout]" value="<?php echo @$params->payzen_redirect_error_timeout; ?>" id="payzen_redirect_error_timeout" style="width: 120px;" /><br />
                        <span style="font-size: 10px; font-style: italic;"><?php echo JText::_('PAYZEN_REDIRECT_ERROR_TIMEOUT_DESC'); ?></span>
                    </td>
                </tr>

                <tr>
                    <td class="key" style="vertical-align: top; white-space: normal !important;">
                        <label for="payzen_redirect_error_message"><?php echo JText::_('PAYZEN_REDIRECT_ERROR_MESSAGE'); ?></label>
                    </td>
                    <td>
                        <input type="text" name="data[payment][payment_params][payzen_redirect_error_message]" value="<?php echo @$params->payzen_redirect_error_message; ?>" id="payzen_redirect_error_message" style="width: 300px;" /><br />
                        <span style="font-size: 10px; font-style: italic;"><?php echo JText::_('PAYZEN_REDIRECT_ERROR_MESSAGE_DESC'); ?></span>
                    </td>
                </tr>

                <tr>
                    <td class="key" style="vertical-align: top; white-space: normal !important;">
                        <label for="payzen_return_mode"><?php echo JText::_('PAYZEN_RETURN_MODE'); ?></label>
                    </td>
                    <td>
                        <select  name="data[payment][payment_params][payzen_return_mode]" class="inputbox" id="payzen_return_mode" style="width: 122px;" >
                            <option<?php if (@$params->payzen_return_mode == 'GET') echo ' selected="selected"'; ?> value='GET'>GET</option>
                            <option<?php if (@$params->payzen_return_mode == 'POST') echo ' selected="selected"'; ?> value='POST'>POST</option>
                        </select><br />
                        <span style="font-size: 10px; font-style: italic;"><?php echo JText::_('PAYZEN_RETURN_MODE_DESC'); ?></span>
                    </td>
                </tr>

                <tr>
                    <td class="key" style="vertical-align: top; white-space: normal !important;">
                        <label for="datapaymentpayment_paramspayzen_verified_status"><?php echo JText::_('PAYZEN_VERIFIED_STATUS'); ?></label>
                    </td>

                    <td>
                        <?php echo $this->data['order_statuses']->display('data[payment][payment_params][payzen_verified_status]', @$params->payzen_verified_status, 'style="width: 122px;"'); ?><br />
                        <span style="font-size: 10px; font-style: italic;"><?php echo JText::_('PAYZEN_VERIFIED_STATUS_DESC'); ?></span>
                    </td>
                </tr>

                <tr>
                    <td class="key" style="vertical-align: top; white-space: normal !important;">
                        <label for="datapaymentpayment_paramspayzen_invalid_status"><?php echo JText::_('PAYZEN_INVALID_STATUS'); ?></label>
                    </td>
                    <td>
                        <?php echo $this->data['order_statuses']->display('data[payment][payment_params][payzen_invalid_status]', @$params->payzen_invalid_status, 'style="width: 122px;"'); ?><br />
                        <span style="font-size: 10px; font-style: italic;"><?php echo JText::_('PAYZEN_INVALID_STATUS_DESC'); ?></span>
                    </td>
                </tr>
            </table>
        </fieldset>
    </td>
</tr>
