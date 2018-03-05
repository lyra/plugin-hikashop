<?php
#####################################################################################################
#
#					Module pour la plateforme de paiement PayZen
#						Version : 1.0d (révision 28826)
#									########################
#					Développé pour hikashop
#						Version : 1.4.7
#						Compatibilité plateforme : V2
#									########################
#					Développé par Lyra Network
#						http://www.lyra-network.com/
#						08/09/2011
#						Contact : support@payzen.eu
#
#####################################################################################################

defined('_JEXEC') or die('Restricted access');
?>

<tr>
	<td class="key" style="background-color: #dddddd;">
		<label>
			<?php echo JText::_( 'VADS_DEVELOPED_BY' ); ?>
		</label>
	</td>
	<td style="background-color: #dddddd;">
		<label>
			<a href="http://www.lyra-network.com/" target="_blank">Lyra network</a>
		</label>
	</td>
</tr>

<tr>
	<td class="key">
		<label>
			<?php echo JText::_( 'VADS_CONTACT_EMAIL' ); ?>
		</label>
	</td>
	<td>
		<label>
			<a href="mailto:support@payzen.eu">support@payzen.eu</a>
		</label>
	</td>
</tr>

<tr>
	<td class="key" style="background-color: #dddddd;">
		<label>
			<?php echo JText::_( 'VADS_CONTRIB_VERSION' ); ?>
		</label>
	</td>
	<td style="background-color: #dddddd;">
		<label>
			1.0d
		</label>
	</td>
</tr>

<tr>
	<td class="key">
		<label>
			<?php echo JText::_( 'VADS_GATEWAY_VERSION' ); ?>
		</label>
	</td>
	<td>
		<label>
			V2
		</label>
	</td>
</tr>

<tr>
	<td class="key" style="background-color: #dddddd;">
		<label>
			<?php echo JText::_( 'VADS_CMS_VERSION' ); ?>
		</label>
	</td>
	<td style="background-color: #dddddd;">
		<label>
			hikashop 1.4.7
		</label>
	</td>
</tr>

<tr>
	<td class="key">
		<label for="data[payment][payment_params][vads_gateway_url]">
			<?php echo JText::_( 'VADS_GATEWAY_URL' ); ?>
		</label>
	</td>
	<td>
		<input type="text" name="data[payment][payment_params][vads_gateway_url]" value="<?php echo @$this->element->payment_params->vads_gateway_url; ?>" />
	</td>
</tr>
<tr>
	<td class="key" style="background-color: #dddddd;">
		<label for="data[payment][payment_params][vads_site_id]">
			<?php echo JText::_( 'VADS_SITE_ID' ); ?>
		</label>
	</td>
	<td style="background-color: #dddddd;">
		<input type="text" name="data[payment][payment_params][vads_site_id]" value="<?php echo @$this->element->payment_params->vads_site_id; ?>" />
	</td>
</tr>
<tr>
	<td class="key">
		<label for="data[payment][payment_params][vads_test_key]">
			<?php echo JText::_( 'VADS_TEST_KEY' ); ?>
		</label>
	</td>
	<td>
		<input type="text" name="data[payment][payment_params][vads_test_key]" value="<?php echo @$this->element->payment_params->vads_test_key; ?>" />
	</td>
</tr>

<tr>
	<td class="key" style="background-color: #dddddd;">
		<label for="data[payment][payment_params][vads_prod_key]">
			<?php echo JText::_( 'VADS_PROD_KEY' ); ?>
		</label>
	</td>
	<td style="background-color: #dddddd;">
		<input type="text" name="data[payment][payment_params][vads_prod_key]" value="<?php echo @$this->element->payment_params->vads_prod_key; ?>" />
	</td>
</tr>


<tr>
	<td class="key">
		<label for="data[payment][payment_params][vads_ctx_mode]">
			<?php echo JText::_( 'VADS_CTX_MODE' ); ?>
		</label>
	</td>
	<td>
		<select name="data[payment][payment_params][vads_ctx_mode]" class="inputbox" size="1">
			<option <?php if (@$this->element->payment_params->vads_ctx_mode == 'TEST') echo 'selected="selected"'; ?> value="TEST">TEST</option>
			<option <?php if (@$this->element->payment_params->vads_ctx_mode == 'PRODUCTION') echo 'selected="selected"'; ?> value="PRODUCTION">PRODUCTION</option>
		</select> 
	</td>
</tr>

<tr>
	<td class="key" style="background-color: #dddddd;">
		<label for="data[payment][payment_params][vads_language]">
			<?php echo JText::_( 'VADS_LANGUAGE' ); ?>
		</label>
	</td>
	<td style="background-color: #dddddd;">
		<select  name="data[payment][payment_params][vads_language]" class="inputbox" size="1">
			<option  <?php if (@$this->element->payment_params->vads_language == 'fr') echo 'selected="selected"'; ?>  value="fr">fr</option>
			<option  <?php if (@$this->element->payment_params->vads_language == 'de') echo 'selected="selected"'; ?>value="de">de</option>
			<option  <?php if (@$this->element->payment_params->vads_language == 'en') echo 'selected="selected"'; ?>value="en">en</option>
			<option  <?php if (@$this->element->payment_params->vads_language == 'es') echo 'selected="selected"'; ?>value="es">es</option>
			<option  <?php if (@$this->element->payment_params->vads_language == 'zh') echo 'selected="selected"'; ?>value="zh">zh</option>
			<option  <?php if (@$this->element->payment_params->vads_language == 'it') echo 'selected="selected"'; ?>value="it">it</option>
			<option  <?php if (@$this->element->payment_params->vads_language == 'ja') echo 'selected="selected"'; ?>value="ja">ja</option>
			<option  <?php if (@$this->element->payment_params->vads_language == 'pt') echo 'selected="selected"'; ?>value="pt">pt</option>
		</select>
	</td>
</tr>

<tr>
	<td class="key">
		<label for="data[payment][payment_params][vads_payment_cards]">
			<?php echo JText::_( 'VADS_PAYMENT_CARDS' ); ?>
		</label>
	</td>
	<td>
		<input type="text" name="data[payment][payment_params][vads_payment_cards]" value="<?php echo @$this->element->payment_params->vads_payment_cards; ?>" />
	</td>
</tr>

<tr>
	<td class="key" style="background-color: #dddddd;">
		<label for="data[payment][payment_params][vads_capture_delay]">
			<?php echo JText::_( 'VADS_CAPTURE_DELAY' ); ?>
		</label>
	</td>
	<td style="background-color: #dddddd;">
		<input type="text" name="data[payment][payment_params][vads_capture_delay]" value="<?php echo @$this->element->payment_params->vads_capture_delay; ?>" />
	</td>
</tr>

<tr>
	<td class="key">
		<label for="data[payment][payment_params][vads_validation_mode]">
			<?php echo JText::_( 'VADS_VALIDATION_MODE' ); ?>
		</label>
	</td>
	<td>
		<select  name="data[payment][payment_params][vads_validation_mode]" class="inputbox" size="1">
			<option <?php if (@$this->element->payment_params->vads_validation_mode == '') echo 'selected="selected"'; ?>  value=''>Par d&eacute;faut</option>
			<option <?php if (@$this->element->payment_params->vads_validation_mode == '0') echo 'selected="selected"'; ?>value='0'>Automatique</option>
			<option <?php if (@$this->element->payment_params->vads_validation_mode == '1') echo 'selected="selected"'; ?>value='1'>Manuel</option>
		</select>
	</td>
</tr>

<tr>
	<td class="key" style="background-color: #dddddd;">
		<label for="data[payment][payment_params][vads_amount_min]">
			<?php echo JText::_( 'VADS_AMOUNT_MIN' ); ?>
		</label>
	</td>
	<td style="background-color: #dddddd;">
		<input type="text" name="data[payment][payment_params][vads_amount_min]" value="<?php echo @$this->element->payment_params->vads_amount_min; ?>" />
	</td>
</tr>

<tr>
	<td class="key">
		<label for="data[payment][payment_params][vads_amount_max]">
			<?php echo JText::_( 'VADS_AMOUNT_MAX' ); ?>
		</label>
	</td>
	<td>
		<input type="text" name="data[payment][payment_params][vads_amount_max]" value="<?php echo @$this->element->payment_params->vads_amount_max; ?>" />
	</td>
</tr>

<tr>
	<td class="key" style="background-color: #dddddd;">
		<label for="data[payment][payment_params][vads_redirect_enabled]">
			<?php echo JText::_( 'VADS_REDIRECT_ENABLED' ); ?>
		</label>
	</td>
	<td style="background-color: #dddddd;">
		<?php echo JHTML::_('select.booleanlist', "data[payment][payment_params][vads_redirect_enabled]" , '',@$this->element->payment_params->vads_redirect_enabled	); ?>
	</td>
</tr>


<tr>
	<td class="key">
		<label for="data[payment][payment_params][vads_redirect_success_timeout]">
			<?php echo JText::_( 'VADS_REDIRECT_SUCCESS_TIMEOUT' ); ?>
		</label>
	</td>
	<td>
		<input type="text" name="data[payment][payment_params][vads_redirect_success_timeout]" value="<?php echo @$this->element->payment_params->vads_redirect_success_timeout; ?>" />
	</td>
</tr>

<tr>
	<td class="key" style="background-color: #dddddd;">
		<label for="data[payment][payment_params][vads_redirect_success_message]">
			<?php echo JText::_( 'VADS_REDIRECT_SUCCESS_MESSAGE' ); ?>
		</label>                 
	</td>
	<td style="background-color: #dddddd;">
		<input type="text" name="data[payment][payment_params][vads_redirect_success_message]" value="<?php echo @$this->element->payment_params->vads_redirect_success_message; ?>" />
	</td>
</tr>


<tr>
	<td class="key">
		<label for="data[payment][payment_params][vads_redirect_error_timeout]">
			<?php echo JText::_( 'VADS_REDIRECT_ERROR_TIMEOUT' ); ?>
		</label>
	</td>
	<td>
		<input type="text" name="data[payment][payment_params][vads_redirect_error_timeout]" value="<?php echo @$this->element->payment_params->vads_redirect_error_timeout; ?>" />
	</td>
</tr>

<tr>
	<td class="key" style="background-color: #dddddd;">
		<label for="data[payment][payment_params][vads_redirect_error_message]">
			<?php echo JText::_( 'VADS_REDIRECT_ERROR_MESSAGE' ); ?>
		</label>                
	</td>
	<td style="background-color: #dddddd;">
		<input type="text" name="data[payment][payment_params][vads_redirect_error_message]" value="<?php echo @$this->element->payment_params->vads_redirect_error_message; ?>" />
	</td>
</tr>


<tr>
	<td class="key">
		<label for="data[payment][payment_params][vads_return_mode]">
			<?php echo JText::_( 'VADS_RETURN_MODE' ); ?>
		</label>                
	</td>
	<td>
		<select  name="data[payment][payment_params][vads_return_mode]" class="inputbox" size="1">
			<option <?php if (@$this->element->payment_params->vads_return_mode == 'GET') echo 'selected="selected"'; ?>  value='GET'>GET</option>
			<option <?php if (@$this->element->payment_params->vads_return_mode == 'POST') echo 'selected="selected"'; ?>value='POST'>POST</option>
			<option <?php if (@$this->element->payment_params->vads_return_mode == 'NONE') echo 'selected="selected"'; ?>value='NONE'>NONE</option>
		</select>
	</td>
</tr>

<tr>
	<td class="key" style="background-color: #dddddd;">
		<label for="data[payment][payment_params][vads_url_return]">
			<?php echo JText::_( 'VADS_URL_ERROR' ); ?>
		</label>                
	</td>
	<td style="background-color: #dddddd;">
		<input type="text" name="data[payment][payment_params][vads_url_return]" value="<?php echo @$this->element->payment_params->vads_url_return; ?>" />
	</td>
</tr>

<tr>
	<td class="key">
		<label for="data[payment][payment_params][vads_url_success]">
			<?php echo JText::_( 'VADS_URL_SUCCESS' ); ?>
		</label>                
	</td>
	<td>
		<input type="text" name="data[payment][payment_params][vads_url_success]" value="<?php echo @$this->element->payment_params->vads_url_success; ?>" />
	</td>
</tr>



<tr>
	<td class="key" style="background-color: #dddddd;>
		<label for="data[payment][payment_params][invalid_status]">
			<?php echo JText::_( 'VADS_INVALID_STATUS' ); ?>
		</label>
	</td>
	<td style="background-color: #dddddd;">
		<?php echo $this->data['category']->display("data[payment][payment_params][invalid_status]",@$this->element->payment_params->invalid_status); ?>
	</td>
</tr>

<tr>
	<td class="key" >
		<label for="data[payment][payment_params][verified_status]">
			<?php echo JText::_( 'VADS_VERIFIED_STATUS' ); ?>
		</label>
	</td>
	
	<td>
		<?php echo $this->data['category']->display("data[payment][payment_params][verified_status]",@$this->element->payment_params->verified_status); ?>
	</td>
</tr>

<tr>
	<td colspan="2" style="background-color: #dddddd;">
		<label>
			<?php 
			echo JText::_( 'VADS_URL_CHECK' ); 
			$url_check = HIKASHOP_LIVE.'index.php?option=com_hikashop&amp;ctrl=checkout&amp;task=notify&amp;notif_payment=vads&amp;tmpl=component';
			echo '<b>'.$url_check.'</b>'; 
			?>
		</label>
	</td>
</tr>
