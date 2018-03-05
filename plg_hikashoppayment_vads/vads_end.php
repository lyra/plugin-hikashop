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


$vads = new VadsApi();
$vads->setFromArray($post);
?>

<div class="hikashop_vads_end" id="hikashop_vads_end">
	<span id="hikashop_vads_end_message" class="hikashop_vads_end_message">
		<?php echo JText::_('VADS_PLEASE_WAIT_BEFORE_REDIRECTION').'<br/>'. JText::_('VADS_CLICK_ON_BUTTON_IF_NOT_REDIRECTED');?>
	</span>
	<span id="hikashop_vads_end_spinner" class="hikashop_vads_end_spinner">
		<img src="<?php echo HIKASHOP_IMAGES.'spinner.gif';?>" />
	</span>
	<br/>
	<form id="hikashop_vads_form" name="hikashop_vads_form" action="<?php echo $vads->get('platform_url');?>" method="post">
		<div id="hikashop_vads_end_image" class="hikashop_vads_end_image">
			<input id="hikashop_vads_button" type="submit" value="<?php echo JText::_('VADS_SEND_BTN_VALUE');?>" name="" alt="<?php echo JText::_('VADS_SEND_BTN_ALT');?>" />
		</div>
		<?php
			echo $vads->getRequestFieldsHtml();
			
			$doc =& JFactory::getDocument();
			$doc->addScriptDeclaration("window.addEvent('domready', function() {document.getElementById('hikashop_vads_form').submit();});");
			JRequest::setVar('noform',1);
		?>
	</form>
</div>