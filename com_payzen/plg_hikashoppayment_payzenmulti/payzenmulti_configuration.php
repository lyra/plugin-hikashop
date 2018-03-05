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
            <legend><?php echo JText::_('PAYZENMULTI_MODULE_INFORMATION'); ?></legend>

            <table>
                <tr>
                    <td class="key">
                        <label><?php echo JText::_('PAYZENMULTI_DEVELOPED_BY'); ?></label>
                    </td>
                    <td>
                        <a href="http://www.lyra-network.com/" target="_blank">Lyra Network</a>
                    </td>
                </tr>

                <tr>
                    <td class="key">
                        <label><?php echo JText::_('PAYZENMULTI_CONTACT_EMAIL'); ?></label>
                    </td>
                    <td>
                        <a href="mailto:support@payzen.eu">support@payzen.eu</a>
                    </td>
                </tr>

                <tr>
                    <td class="key">
                        <label><?php echo JText::_('PAYZENMULTI_CONTRIB_VERSION'); ?></label>
                    </td>
                    <td>
                        <label>2.0.0</label>
                    </td>
                </tr>

                <tr>
                    <td class="key">
                        <label><?php echo JText::_('PAYZENMULTI_PLATFORM_VERSION'); ?></label>
                    </td>
                    <td>
                        <label>V2</label>
                    </td>
                </tr>

                <tr>
                    <td class="key">
                        <label><?php echo JText::_('PAYZENMULTI_DOCUMENTAION'); ?></label>
                    </td>
                    <td>
                        <?php
                        $docUrl = HIKASHOP_LIVE . 'administrator' . DS . 'components' . DS . 'com_payzen' . DS . 'installation_doc' . DS .
                            'Integration_PayZen_HikaShop_2.x-3.x_v2.0.0.pdf';
                        ?>
                        <a style="color: red;" target="_blank" href="<?php echo $docUrl; ?>"><?php echo JText::_('PAYZENMULTI_DOCUMENTAION_TEXT'); ?></a>
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
            <legend><?php echo JText::_('PAYZENMULTI_PLATFORM_ACCESS'); ?></legend>

            <table>
                <tr>
                    <td class="key" style="vertical-align: top; white-space: normal !important;">
                        <label for="payzenmulti_site_id"><?php echo JText::_('PAYZENMULTI_SITE_ID'); ?></label>
                    </td>
                    <td>
                        <input type="text" name="data[payment][payment_params][payzenmulti_site_id]" value="<?php echo @$params->payzenmulti_site_id; ?>" id="payzenmulti_site_id" style="width: 120px;" /><br />
                        <span style="font-size: 10px; font-style: italic;"><?php echo JText::_('PAYZENMULTI_SITE_ID_DESC'); ?></span>
                    </td>
                </tr>

                <tr>
                    <td class="key" style="vertical-align: top; white-space: normal !important;">
                        <label for="payzenmulti_key_test"><?php echo JText::_('PAYZENMULTI_KEY_TEST'); ?></label>
                    </td>
                    <td>
                        <input type="text" name="data[payment][payment_params][payzenmulti_key_test]" value="<?php echo @$params->payzenmulti_key_test; ?>" id="payzenmulti_key_test" style="width: 120px;" /><br />
                        <span style="font-size: 10px; font-style: italic;"><?php echo JText::_('PAYZENMULTI_KEY_TEST_DESC'); ?></span>
                    </td>
                </tr>

                <tr>
                    <td class="key" style="vertical-align: top; white-space: normal !important;">
                        <label for="payzenmulti_key_prod"><?php echo JText::_('PAYZENMULTI_KEY_PROD'); ?></label>
                    </td>
                    <td>
                        <input type="text" name="data[payment][payment_params][payzenmulti_key_prod]" value="<?php echo @$params->payzenmulti_key_prod; ?>" id="payzenmulti_key_prod" style="width: 120px;" /><br />
                        <span style="font-size: 10px; font-style: italic;"><?php echo JText::_('PAYZENMULTI_KEY_PROD_DESC'); ?></span>
                    </td>
                </tr>

                <tr>
                    <td class="key" style="vertical-align: top; white-space: normal !important;">
                        <label for="payzenmulti_ctx_mode"><?php echo JText::_('PAYZENMULTI_CTX_MODE'); ?></label>
                    </td>
                    <td>
                        <select name="data[payment][payment_params][payzenmulti_ctx_mode]" class="inputbox" id="payzenmulti_ctx_mode" style="width: 122px;" >
                            <option <?php if (@$params->payzenmulti_ctx_mode == 'TEST') echo 'selected="selected"'; ?> value="TEST"><?php echo JText::_('PAYZENMULTI_CTX_MODE_TEST'); ?></option>
                            <option <?php if (@$params->payzenmulti_ctx_mode == 'PRODUCTION') echo 'selected="selected"'; ?> value="PRODUCTION"><?php echo JText::_('PAYZENMULTI_CTX_MODE_PROD'); ?></option>
                        </select><br />
                        <span style="font-size: 10px; font-style: italic;"><?php echo JText::_('PAYZENMULTI_CTX_MODE_DESC'); ?></span>
                    </td>
                </tr>

                <tr>
                    <td class="key" style="vertical-align: top; white-space: normal !important;">
                        <label for="payzenmulti_platform_url"><?php echo JText::_('PAYZENMULTI_PLATFORM_URL'); ?></label>
                    </td>
                    <td>
                        <input type="text" name="data[payment][payment_params][payzenmulti_platform_url]" value="<?php echo @$params->payzenmulti_platform_url; ?>" id="payzenmulti_platform_url" style="width: 300px;" /><br />
                        <span style="font-size: 10px; font-style: italic;"><?php echo JText::_('PAYZENMULTI_PLATFORM_URL_DESC'); ?></span>
                    </td>
                </tr>

                <tr>
                    <td class="key" style="vertical-align: top; white-space: normal !important;">
                        <label><?php echo JText::_('PAYZENMULTI_IPN_URL'); ?></label>
                    </td>
                    <td>
                        <label><b><?php echo HIKASHOP_LIVE . 'index.php?option=com_hikashop&amp;ctrl=checkout&amp;task=notify&amp;notif_payment=payzenmulti&amp;tmpl=component'; ?></b></label><br />
                        <span style="font-size: 10px; font-style: italic;"><?php echo JText::_('PAYZENMULTI_IPN_URL_DESC'); ?></span>
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
            <legend><?php echo JText::_('PAYZENMULTI_PAYMENT_PAGE'); ?></legend>

            <table>
                <tr>
                    <td class="key" style="vertical-align: top; white-space: normal !important;">
                        <label for="payzenmulti_language"><?php echo JText::_('PAYZENMULTI_LANGUAGE'); ?></label>
                    </td>
                    <td>
                        <select name="data[payment][payment_params][payzenmulti_language]" class="inputbox" id="payzenmulti_language" style="width: 122px;" >
                            <?php
                            foreach (PayzenApi::getSupportedLanguages() as $code => $label) {
                                $selected = (@$params->payzenmulti_language == $code) ? ' selected="selected"' : '';
                                echo '<option' . $selected . ' value="'. $code . '">' . JText::_('PAYZENMULTI_LANGUAGE_' . strtoupper($label)) . '</option>';
                            }
                            ?>
                        </select><br />
                        <span style="font-size: 10px; font-style: italic;"><?php echo JText::_('PAYZENMULTI_LANGUAGE_DESC'); ?></span>
                    </td>
                </tr>

                <tr>
                    <td class="key" style="vertical-align: top; white-space: normal !important;">
                        <label for="payzenmulti_available_languages"><?php echo JText::_('PAYZENMULTI_AVAILABLE_LANGUAGES'); ?></label>
                    </td>
                    <td>
                        <select name="data[payment][payment_params][payzenmulti_available_languages][]" class="inputbox" multiple="multiple" size="8" id="payzenmulti_available_languages" style="width: 122px;" >
                            <?php
                            $langs = @$params->payzenmulti_available_languages ? explode(';', $params->payzenmulti_available_languages) : array();
                            foreach (PayzenApi::getSupportedLanguages() as $code => $label) {
                                $selected = in_array($code, $langs) ? ' selected="selected"' : '';
                                echo '<option' . $selected . ' value="'. $code . '">' . JText::_('PAYZENMULTI_LANGUAGE_' . strtoupper($label)) . '</option>';
                            }
                            ?>
                        </select><br />
                        <span style="font-size: 10px; font-style: italic;"><?php echo JText::_('PAYZENMULTI_AVAILABLE_LANGUAGES_DESC'); ?></span>
                    </td>
                </tr>

                <tr>
                    <td class="key" style="vertical-align: top; white-space: normal !important;">
                        <label for="payzenmulti_capture_delay"><?php echo JText::_('PAYZENMULTI_CAPTURE_DELAY'); ?></label>
                    </td>
                    <td>
                        <input type="text" name="data[payment][payment_params][payzenmulti_capture_delay]" value="<?php echo @$params->payzenmulti_capture_delay; ?>" id="payzenmulti_capture_delay" style="width: 120px;" /><br />
                        <span style="font-size: 10px; font-style: italic;"><?php echo JText::_('PAYZENMULTI_CAPTURE_DELAY_DESC'); ?></span>
                    </td>
                </tr>

                <tr>
                    <td class="key" style="vertical-align: top; white-space: normal !important;">
                        <label for="payzenmulti_validation_mode"><?php echo JText::_('PAYZENMULTI_VALIDATION_MODE'); ?></label>
                    </td>
                    <td>
                        <select name="data[payment][payment_params][payzenmulti_validation_mode]" class="inputbox" id="payzenmulti_validation_mode" style="width: 122px;" >
                            <option <?php if (@$params->payzenmulti_validation_mode == '')  echo 'selected="selected"'; ?>value=''><?php echo JText::_('PAYZENMULTI_MODE_DEFAULT'); ?></option>
                            <option <?php if (@$params->payzenmulti_validation_mode == '0') echo 'selected="selected"'; ?>value='0'><?php echo JText::_('PAYZENMULTI_MODE_AUTOMATIC'); ?></option>
                            <option <?php if (@$params->payzenmulti_validation_mode == '1') echo 'selected="selected"'; ?>value='1'><?php echo JText::_('PAYZENMULTI_MODE_MANUAL'); ?></option>
                        </select><br />
                        <span style="font-size: 10px; font-style: italic;"><?php echo JText::_('PAYZENMULTI_VALIDATION_MODE_DESC'); ?></span>
                    </td>
                </tr>

                <tr>
                    <td class="key" style="vertical-align: top; white-space: normal !important;">
                        <label for="payzenmulti_payment_cards"><?php echo JText::_('PAYZENMULTI_PAYMENT_CARDS'); ?></label>
                    </td>
                    <td>
                        <select name="data[payment][payment_params][payzenmulti_payment_cards][]" class="inputbox" multiple="multiple" size="8" id="payzenmulti_payment_cards" style="width: 122px;" >
                            <?php
                            $cards = @$params->payzenmulti_payment_cards ? explode(';', $params->payzenmulti_payment_cards) : array();
                            foreach (plgHikashoppaymentPayzenmulti::getAvailableMultiCards() as $code => $label) {
                                $selected = in_array($code, $cards) ? ' selected="selected"' : '';
                                echo '<option' . $selected . ' value="'. $code . '">' . $label . '</option>';
                            }
                            ?>
                        </select><br />
                        <span style="font-size: 10px; font-style: italic;"><?php echo JText::_('PAYZENMULTI_PAYMENT_CARDS_DESC'); ?></span>
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
            <legend><?php echo JText::_('PAYZENMULTI_SELECTIVE_3DS'); ?></legend>

            <table>
                <tr>
                    <td class="key" style="vertical-align: top; white-space: normal !important;">
                        <label for="payzenmulti_threeds_amount_min"><?php echo JText::_('PAYZENMULTI_THREEDS_AMOUNT_MIN'); ?></label>
                    </td>
                    <td>
                        <input type="text" name="data[payment][payment_params][payzenmulti_threeds_amount_min]" value="<?php echo @$params->payzenmulti_threeds_amount_min; ?>"  style="width: 120px;" /><br />
                        <span style="font-size: 10px; font-style: italic;"><?php echo JText::_('PAYZENMULTI_THREEDS_AMOUNT_MIN_DESC'); ?></span>
                    </td>
                </tr>
            </table>
        </fieldset>
    </td>
</tr>

<!------------------------- multi payment options ----------------------------------------->
<tr>
    <td colspan="2">
        <fieldset>
            <legend><?php echo JText::_('PAYZENMULTI_MULTI_OPTIONS'); ?></legend>
            <?php
            // @formatter:on
            $multi_options = @$params->payzen_multi_options;

            $js = 'function payzenAddOption(first, deleteText, contract) {
                       if (first) {
                           jQuery("#payzen_multi_options_btn").css("display", "none");
                           jQuery("#payzen_multi_options_table").css("display", "");
                       }

                       var timestamp = new Date().getTime();
                       var optionLine = "<tr id=\"payzen_multi_option_" + timestamp + "\">" +
                                        "    <td><input name=\"data[payment][payment_params][payzen_multi_options][" + timestamp + "][label]\" style=\"width: 150px;\" type=\"text\" /></td>" +
                                        "    <td><input name=\"data[payment][payment_params][payzen_multi_options][" + timestamp + "][amount_min]\" style=\"width: 80px;\" type=\"text\" /></td>" +
                                        "    <td><input name=\"data[payment][payment_params][payzen_multi_options][" + timestamp + "][amount_max]\" style=\"width: 80px;\" type=\"text\" /></td>" ;
                       if (contract) {
                          optionLine += "<td><input name=\"data[payment][payment_params][payzen_multi_options][" + timestamp + "][contract]\" style=\"width: 70px;\" type=\"text\" /></td>";
                       }
		               optionLine += "    <td><input name=\"data[payment][payment_params][payzen_multi_options][" + timestamp + "][count]\" style=\"width: 70px;\" type=\"text\" /></td>" +
                                     "    <td><input name=\"data[payment][payment_params][payzen_multi_options][" + timestamp + "][period]\" style=\"width: 70px;\" type=\"text\" /></td>" +
                                     "    <td><input name=\"data[payment][payment_params][payzen_multi_options][" + timestamp + "][first]\" style=\"width: 70px;\" type=\"text\" /></td>" +
                                     "    <td><button type=\"button\" onclick= \"payzenDeleteOption(" + timestamp + ");\">" + deleteText + " </td>" +
                                     "</tr>";

                       jQuery(optionLine).insertBefore("#payzen_multi_option_add");
                   };

                   function payzenDeleteOption(key) {
                       jQuery("#payzen_multi_option_" + key).remove();

                       if (jQuery("#payzen_multi_options_table tbody tr").length == 1) {
                           jQuery("#payzen_multi_options_btn").css("display", "");
                           jQuery("#payzen_multi_options_table").css("display", "none");
                       }
                   };';

            $doc = JFactory::getDocument();
            $doc->addScriptDeclaration($js);

            $cb_avail = key_exists('CB', plgHikashoppaymentPayzenmulti::getAvailableMultiCards());
            $str_cb_avail = $cb_avail ? 'true' : 'false';

            //@formatter:off
            ?>

            <button id="payzen_multi_options_btn" type="button" style="<?php if (!empty($multi_options)) echo 'display: none;'; else echo ''; ?>"
                    onclick= "payzenAddOption(true, '<?php echo JText::_('PAYZENMULTI_MULTI_OPTIONS_DELETE'); ?>', <?php echo $str_cb_avail; ?>);" >
                    <?php echo JText::_('PAYZENMULTI_MULTI_OPTIONS_ADD'); ?>
            </button>
            <br />

            <table id="payzen_multi_options_table" style="<?php if (empty($multi_options)) echo 'display: none;'; else echo ''; ?>" class="payzen-table" >
                <thead>
                    <tr>
                        <th><?php echo JText::_('PAYZENMULTI_LABEL'); ?></th>
                        <th><?php echo JText::_('PAYZENMULTI_MIN_AMOUNT'); ?></th>
                        <th><?php echo JText::_('PAYZENMULTI_MAX_AMOUNT'); ?></th>
                        <?php if ($cb_avail) echo '<th>'.JText::_('PAYZENMULTI_MAX_CONTRACT').'</th>'; ?>
                        <th><?php echo JText::_('PAYZENMULTI_COUNT'); ?></th>
                        <th><?php echo JText::_('PAYZENMULTI_PERIOD'); ?></th>
                        <th><?php echo JText::_('PAYZENMULTI_FIRST'); ?></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>

    <?php
    if (! empty($multi_options)) {
        foreach ($multi_options as $key => $option) {
            echo '<tr id="payzen_multi_option_' . $key . '">
                      <td>' . payzen_create_text('data[payment][payment_params][payzen_multi_options][' . $key . '][label]', $option['label'], 'style="width: 150px;"') . '</td>
                      <td>' . payzen_create_text('data[payment][payment_params][payzen_multi_options][' . $key . '][amount_min]', $option['amount_min'], 'style="width: 80px;"') . '</td>
                      <td>' . payzen_create_text('data[payment][payment_params][payzen_multi_options][' . $key . '][amount_max]', $option['amount_max'], 'style="width: 80px;"') . '</td>';

            if ($cb_avail) {
                echo '<td>' . payzen_create_text('data[payment][payment_params][payzen_multi_options][' . $key . '][contract]', $option['contract'], 'style="width: 70px;"') . '</td>';
            }

            echo '    <td>' . payzen_create_text('data[payment][payment_params][payzen_multi_options][' . $key . '][count]', $option['count'], 'style="width: 70px;"') . '</td>
                      <td>' . payzen_create_text('data[payment][payment_params][payzen_multi_options][' . $key . '][period]', $option['period'], 'style="width: 70px;"') . '</td>
                      <td>' . payzen_create_text( 'data[payment][payment_params][payzen_multi_options][' . $key . '][first]', $option['first'], 'style="width: 70px;"') . '</td>
                      <td><button type="button" onclick="payzenDeleteOption(' . $key . ');">' . JText::_('PAYZENMULTI_MULTI_OPTIONS_DELETE') . '</button></td>
                  </tr>';
        }
    }

    function payzen_create_text($name, $value, $extra_attributes = '')
    {
        $output = '<input type="text" name="' . $name . '" value="' . $value . '" ' . $extra_attributes . '>';
        return $output;
    }

    ?>

                    <tr id="payzen_multi_option_add">
                        <td colspan="<?php $colspan = $cb_avail ? '7' : '6'; echo $colspan; ?>"></td>
                        <td>
                            <button type="button" onclick="payzenAddOption(false, '<?php echo JText::_('PAYZENMULTI_MULTI_OPTIONS_DELETE'); ?>', <?php echo $str_cb_avail; ?>);" >
                                <?php echo JText::_('PAYZENMULTI_MULTI_OPTIONS_ADD'); ?>
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
            <span style="font-size: 10px; font-style: italic;"><?php $numdesc = $cb_avail ? '1' : '2';echo JText::_('PAYZENMULTI_MULTI_OPTIONS_DESC'.$numdesc); ?></span>
        </fieldset>
    </td>
</tr>

<!------------------------------- return to shop ------------------------------------------>
<tr>
    <td colspan="2">
        <fieldset>
            <legend><?php echo JText::_('PAYZENMULTI_RETURN_TO_SHOP'); ?></legend>

            <table>
                <tr>
                    <td class="key" style="vertical-align: top; white-space: normal !important;">
                        <label for="payzenmulti_redirect_enabled"><?php echo JText::_('PAYZENMULTI_REDIRECT_ENABLED'); ?></label>
                    </td>
                    <td>
                        <?php echo JHTML::_('select.booleanlist', 'data[payment][payment_params][payzenmulti_redirect_enabled]' , '', @$params->payzenmulti_redirect_enabled); ?><br />
                        <span style="font-size: 10px; font-style: italic;"><?php echo JText::_('PAYZENMULTI_REDIRECT_ENABLED_DESC'); ?></span>
                    </td>
                </tr>

                <tr>
                    <td class="key" style="vertical-align: top; white-space: normal !important;">
                        <label for="payzenmulti_redirect_success_timeout"><?php echo JText::_('PAYZENMULTI_REDIRECT_SUCCESS_TIMEOUT'); ?></label>
                    </td>
                    <td>
                        <input type="text" name="data[payment][payment_params][payzenmulti_redirect_success_timeout]" value="<?php echo @$params->payzenmulti_redirect_success_timeout; ?>" id="payzenmulti_redirect_success_timeout" style="width: 120px;" /><br />
                        <span style="font-size: 10px; font-style: italic;"><?php echo JText::_('PAYZENMULTI_REDIRECT_SUCCESS_TIMEOUT_DESC'); ?></span>
                    </td>
                </tr>

                <tr>
                    <td class="key" style="vertical-align: top; white-space: normal !important;">
                        <label for="payzenmulti_redirect_success_message"><?php echo JText::_('PAYZENMULTI_REDIRECT_SUCCESS_MESSAGE'); ?></label>
                    </td>
                    <td>
                        <input type="text" name="data[payment][payment_params][payzenmulti_redirect_success_message]" value="<?php echo @$params->payzenmulti_redirect_success_message; ?>" id="payzenmulti_redirect_success_message" style="width: 300px;" /><br />
                        <span style="font-size: 10px; font-style: italic;"><?php echo JText::_('PAYZENMULTI_REDIRECT_SUCCESS_MESSAGE_DESC'); ?></span>
                    </td>
                </tr>

                <tr>
                    <td class="key" style="vertical-align: top; white-space: normal !important;">
                        <label for="payzenmulti_redirect_error_timeout"><?php echo JText::_('PAYZENMULTI_REDIRECT_ERROR_TIMEOUT'); ?></label>
                    </td>
                    <td>
                        <input type="text" name="data[payment][payment_params][payzenmulti_redirect_error_timeout]" value="<?php echo @$params->payzenmulti_redirect_error_timeout; ?>" id="payzenmulti_redirect_error_timeout" style="width: 120px;" /><br />
                        <span style="font-size: 10px; font-style: italic;"><?php echo JText::_('PAYZENMULTI_REDIRECT_ERROR_TIMEOUT_DESC'); ?></span>
                    </td>
                </tr>

                <tr>
                    <td class="key" style="vertical-align: top; white-space: normal !important;">
                        <label for="payzenmulti_redirect_error_message"><?php echo JText::_('PAYZENMULTI_REDIRECT_ERROR_MESSAGE'); ?></label>
                    </td>
                    <td>
                        <input type="text" name="data[payment][payment_params][payzenmulti_redirect_error_message]" value="<?php echo @$params->payzenmulti_redirect_error_message; ?>" id="payzenmulti_redirect_error_message" style="width: 300px;" /><br />
                        <span style="font-size: 10px; font-style: italic;"><?php echo JText::_('PAYZENMULTI_REDIRECT_ERROR_MESSAGE_DESC'); ?></span>
                    </td>
                </tr>

                <tr>
                    <td class="key" style="vertical-align: top; white-space: normal !important;">
                        <label for="payzenmulti_return_mode"><?php echo JText::_('PAYZENMULTI_RETURN_MODE'); ?></label>
                    </td>
                    <td>
                        <select  name="data[payment][payment_params][payzenmulti_return_mode]" class="inputbox" id="payzenmulti_return_mode" style="width: 122px;" >
                            <option<?php if (@$params->payzenmulti_return_mode == 'GET') echo ' selected="selected"'; ?> value='GET'>GET</option>
                            <option<?php if (@$params->payzenmulti_return_mode == 'POST') echo ' selected="selected"'; ?> value='POST'>POST</option>
                        </select><br />
                        <span style="font-size: 10px; font-style: italic;"><?php echo JText::_('PAYZENMULTI_RETURN_MODE_DESC'); ?></span>
                    </td>
                </tr>

                <tr>
                    <td class="key" style="vertical-align: top; white-space: normal !important;">
                        <label for="datapaymentpayment_paramspayzenmulti_verified_status"><?php echo JText::_('PAYZENMULTI_VERIFIED_STATUS'); ?></label>
                    </td>

                    <td>
                        <?php echo $this->data['order_statuses']->display('data[payment][payment_params][payzenmulti_verified_status]', @$params->payzenmulti_verified_status, 'style="width: 122px;"'); ?><br />
                        <span style="font-size: 10px; font-style: italic;"><?php echo JText::_('PAYZENMULTI_VERIFIED_STATUS_DESC'); ?></span>
                    </td>
                </tr>

                <tr>
                    <td class="key" style="vertical-align: top; white-space: normal !important;">
                        <label for="datapaymentpayment_paramspayzenmulti_invalid_status"><?php echo JText::_('PAYZENMULTI_INVALID_STATUS'); ?></label>
                    </td>
                    <td>
                        <?php echo $this->data['order_statuses']->display('data[payment][payment_params][payzenmulti_invalid_status]', @$params->payzenmulti_invalid_status, 'style="width: 122px;"'); ?><br />
                        <span style="font-size: 10px; font-style: italic;"><?php echo JText::_('PAYZENMULTI_INVALID_STATUS_DESC'); ?></span>
                    </td>
                </tr>
            </table>
        </fieldset>
    </td>
</tr>
