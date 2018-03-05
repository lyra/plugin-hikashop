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

// Load VADS constant language
$lang =& JFactory::getLanguage();
$lang->load('plg_hikashoppayment_vads',  JPATH_ADMINISTRATOR);

// Load VADS API
require_once 'vads_api.php';

class plgHikashoppaymentVads extends JPlugin
{
	// Called by Hikashop to test if VADS payment method is available
    function onPaymentDisplay(&$order, &$methods, &$usable_methods){
    	if(!empty($methods)){
    		foreach($methods as $method){
				if($method->payment_type!='vads' || (version_compare(JText::_('HIKA_LANG_VERSION'),'1.4.7','>=') && !$method->enabled)){
					continue;
				}
				
				// if available in payment zone
				if(!empty($method->payment_zone_namekey)){
					$zoneClass = hikashop::get('class.zone');
	    			$zones = $zoneClass->getOrderZones($order);
					if(!in_array($method->payment_zone_namekey,$zones)){
						return true;
					}
				}
				
				// if supported currency
				$currencyClass = hikashop::get('class.currency');
				$null = null;
				if(!empty($order->total)){
					$currency_id = intval(@$order->total->prices[0]->price_currency_id);
					$currency = $currencyClass->getCurrencies($currency_id, $null);
					
					if(!empty($currency)) {
						$vads_currency = VadsApi::findCurrencyByAlphaCode(@$currency[$currency_id]->currency_code);
						if($vads_currency == null) {
							return true;
						}
					}
				}
				
				// if amount min / max
				$amount = @$order->total->prices[0]->price_value_with_tax;
				if(!empty($method->payment_params->vads_amount_min) && $amount < $method->payment_params->vads_amount_min) {
					return true;
				}
    			if(!empty($method->payment_params->vads_amount_max) && $amount > $method->payment_params->vads_amount_max) {
					return true;
				}
				
				$usable_methods[$method->ordering]=$method;
    		}
    	}
    	return true;
    }
    
    
    
    // Called by Hikashop : entry point to onPaymentDisplay
    function onPaymentSave(&$cart, &$rates, &$payment_id){
    	$usable = array();
    	$this->onPaymentDisplay($cart, $rates, $usable); 
    	$payment_id = (int) $payment_id;
    	foreach($usable as $usable_method){
    		if($usable_method->payment_id == $payment_id) {
    			return $usable_method;
    		}
    	}
    	return false;
    }
    
    // Called by Hikashop before redirect to VADS payment platform
    // Construct array of parameters here
    function onAfterOrderConfirm(&$order, &$methods, $method_id) {
     	$method =& $methods[$method_id];
    
    	$lang = &JFactory::getLanguage();
		$locale = strtoupper(substr($lang->get('tag'), 0, 2));
		
		$vads_language = in_array($locale, VadsApi::getSupportedLanguages()) ? $locale : 
			($method->payment_params->vads_language ? $method->payment_params->vads_language : 'fr');
    	   	
    	$currencyClass = hikashop::get('class.currency');
		$currencies = null;
		$currencies = $currencyClass->getCurrencies($order->order_currency_id, $currencies);
		
		$vads_currency = null;
		$currency = VadsApi::findCurrencyByAlphaCode($currencies[$order->order_currency_id]->currency_code);
		if($currency == null) {
			$vads_currency = '978';
		} else {
			$vads_currency = $currency->num;
		}
		
		// Default return URL to the shop
		$url_return = HIKASHOP_LIVE.'index.php?option=com_hikashop&ctrl=checkout&task=notify&notif_payment=vads&tmpl=component';
		
		$app =& JFactory::getApplication();
	    $user = hikashop::loadUser(true);
	    $cart = hikashop::get('class.cart');
	    
	    // Load billing address to order->cart->billing_address
		$address = $app->getUserState(HIKASHOP_COMPONENT.'.billing_address');
		$cart->loadAddress($order->cart, $address, 'object', 'billing');
		
		// Load shipping address to order->cart->shipping_address
		$address = $app->getUserState(HIKASHOP_COMPONENT.'.shipping_address');
		$cart->loadAddress($order->cart, $address, 'object', 'shipping');
		
		$post = array(
			'vads_amount' => round($order->cart->full_total->prices[0]->price_value_with_tax, $currency->decimals) * 100,
			'vads_contrib' => 'hikashop1.4.7_1.0d',
			'vads_currency' => $vads_currency,
			'vads_language' => $vads_language,
			'vads_order_id' => $order->order_id,
		
			'vads_cust_id' => $user->user_id,
			'vads_cust_address' => @$order->cart->billing_address->address_street,
			'vads_cust_zip' => @$order->cart->billing_address->address_post_code,
			'vads_cust_country' => @$order->cart->billing_address->address_country->zone_code_2,
			'vads_cust_email' => $user->user_email,
			'vads_cust_phone' => @$order->cart->billing_address->address_telephone,
			'vads_cust_city' => @$order->cart->billing_address->address_city,
			'vads_cust_name' => @$order->cart->billing_address->address_firstname . ' ' . @$order->cart->billing_address->address_lastname,
	
		    'vads_ship_to_name' => @$order->cart->shipping_address->address_firstname . ' ' . @$order->cart->shipping_address->address_lastname,
		    'vads_ship_to_street' => @$order->cart->shipping_address->address_street,
		    'vads_ship_to_city' => @$order->cart->shipping_address->address_city,
		    'vads_ship_to_state' =>@$order->cart->shipping_address->address_state->zone_name_english,
		    'vads_ship_to_country' => @$order->cart->shipping_address->address_country->zone_code_2,
			'vads_ship_to_phone_num' => @$order->cart->shipping_address->address_telephone,
		    'vads_ship_to_zip' => @$order->cart->shipping_address->address_post_code,
		
			'vads_platform_url' => $method->payment_params->vads_gateway_url,
			'vads_site_id' => $method->payment_params->vads_site_id,
			'vads_key_test' => $method->payment_params->vads_test_key,
			'vads_key_prod' => $method->payment_params->vads_prod_key,
			'vads_ctx_mode' => $method->payment_params->vads_ctx_mode,
			'vads_capture_delay' => $method->payment_params->vads_capture_delay,
			'vads_payment_cards'  => $method->payment_params->vads_payment_cards,
			'vads_validation_mode' => $method->payment_params->vads_validation_mode,
			
			'vads_return_mode' => $method->payment_params->vads_return_mode,
			'vads_redirect_enabled' => $method->payment_params->vads_redirect_enabled,
		 	'vads_redirect_success_timeout' => $method->payment_params->vads_redirect_success_timeout,
			'vads_redirect_success_message' => $method->payment_params->vads_redirect_success_message,
			'vads_redirect_error_timeout' => $method->payment_params->vads_redirect_error_timeout,
			'vads_redirect_error_message' => $method->payment_params->vads_redirect_error_message,
			
			'vads_url_return' => $url_return
		);

		JHTML::_('behavior.mootools');
		$app =& JFactory::getApplication();
		$name = $method->payment_type.'_end.php';
    	$path = JPATH_THEMES.DS.$app->getTemplate().DS.'hikashoppayment'.DS.$name;
    	if(!file_exists($path)){
    		if(version_compare(JVERSION,'1.6','<')){
    			$path = JPATH_PLUGINS .DS.'hikashoppayment'.DS.$name;
    		}else{
    			$path = JPATH_PLUGINS .DS.'hikashoppayment'.DS.$method->payment_type.DS.$name;
    		}
    		if(!file_exists($path)){
    			return true;
    		}
    	}
    	require($path);
    	return true;
    }
    
    // Notify payment after callback from vads platform
    function onPaymentNotification(&$statuses){
	    $pluginsClass = hikashop::get('class.plugins');
	    
	    $elements = $pluginsClass->getMethods('payment', 'vads');
	    if(empty($elements)) return false;
		
		$element = reset($elements);
		
		$vads = new VadsApi();
		$vad_api_resp = $vads->getResponse(
									$_REQUEST, 
									$element->payment_params->vads_ctx_mode,
									$element->payment_params->vads_test_key,
									$element->payment_params->vads_prod_key
								   );
				
		$from_server = isset($_REQUEST['vads_hash']);
			
	    if( !$vad_api_resp->isAuthentified()) {
			if($from_server) {
			   exit($vad_api_resp->getOutputForGateway('auth_fail'));
			} else {
				exit ("Authentication failed.");
			}
		}
		
    	// Retrieve order info from database
		$orderClass = hikashop::get('class.order');
		$dbOrder = $orderClass->get((int)$vad_api_resp->get('order_id'));
		
		// Order not found
		if(empty($dbOrder)) {
			if($from_server){
				die($vad_api_resp->getOutputForGateway('order_not_found'));
			}
			else {
				echo "Could not load any order for your notification ".$vad_api_resp->get('order_id');
				return true;
			}
		}

     	$vads_message = $vad_api_resp->message;
		if(!empty($vad_api_resp->extraMessage)) {
			$vads_message .= '. '.$vad_api_resp->extraMessage;
		}
		if(!empty($vad_api_resp->authMessage)) {
			$vads_message .= '. '.$vad_api_resp->authMessage;
		}	
		if(!empty($vad_api_resp->warrantyMessage)) {
		   	$vads_message .= '. '.$vad_api_resp->warrantyMessage;
		}
		
		// Redirect to the specified URLs in the admin page or to the default URLs
		if(empty($element->payment_params->vads_url_return)) {
			$url_return = HIKASHOP_LIVE.'index.php?option=com_hikashop&ctrl=checkout&task=step&step=0&order_id='.$dbOrder->order_id;
		} else {
			$url_return = $element->payment_params->vads_url_return;
			$sep = strpos($url_return, '?') ? '&' : '?';
			$url_return .= $sep.'order_id='.$dbOrder->order_id;  
		}
		
    	if(empty($element->payment_params->vads_url_success)) {
			$url_success =  HIKASHOP_LIVE.'index.php?option=com_hikashop&ctrl=checkout&task=after_end&order_id='.$dbOrder->order_id;
		} else {
			$url_success = $element->payment_params->vads_url_success;
			$sep = strpos($url_success, '?') ? '&' : '?'; 
			$url_success .= $sep.'order_id='.$dbOrder->order_id;  
		}
		
		$url_check = HIKASHOP_LIVE.'index.php?option=com_hikashop&amp;ctrl=checkout&amp;task=notify&amp;notif_payment=vads&amp;tmpl=component';

		// Order not processed yet
		if($dbOrder->order_status == 'created') {
			if($vad_api_resp->isAcceptedPayment()) {
				$this->_confirmOrder(
							$dbOrder->order_id, 
							$_REQUEST, 
							$element->payment_id, 
							$element->payment_type, 
							$dbOrder->order_status, 
							$element->payment_params->verified_status, 
							$vad_api_resp->message,
							1
				);
				
				if($from_server) {
					die($vad_api_resp->getOutputForGateway('payment_ok'));
				}
				else {
					if($element->payment_params->vads_ctx_mode == 'TEST') {
						// Mode TEST warning : Check URL not correctly called
						echo "Avertissement mode TEST : la commande a bien été enregistrée, mais la validation automatique n'a pas fonctionné.";
						echo "<br/>Vérifiez que vous avez correctement configuré l'url serveur (".$url_check.") dans l'outil de gestion de caisse PayZen." ;
					}
					header("Location:".$url_success);
					die();
				}
			}
			else {
				$this->_confirmOrder(
							$dbOrder->order_id, 
							$_REQUEST, 
							$element->payment_id, 
							$element->payment_type, 
							$dbOrder->order_status, 
							$element->payment_params->invalid_status,
							$vad_api_resp->message
				);
				
				if($from_server){
					die($vad_api_resp->getOutputForGateway('payment_ko'));
				}
				else {
					if ($element->payment_params->vads_ctx_mode == 'TEST'){
						// Mode TEST warning : Check URL not correctly called
						echo "Avertissement mode TEST : la commande a bien été enregistrée, mais la validation automatique n'a pas fonctionné.";
						echo "<br/>Vérifiez que vous avez correctement configuré l'url serveur (".$url_check.") dans l'outil de gestion de caisse PayZen." ;
					}
					header("Location:".$url_return);
					die();
				}
			}
		}
		else {
			// Order already processed
			if($vad_api_resp->isAcceptedPayment()) {
				if($from_server){
					die ($vad_api_resp->getOutputForGateway('payment_ok_already_done'));
				}
				else {	
					header("Location:".$url_success);
					die();
				}
			}
			else {
				if($from_server){
					die ($vad_api_resp->getOutputForGateway('payment_ko_on_order_ok'));
				}
				else {
		    		header("Location:".$url_return);
					die();
				}
			}	
		}
		
    	return false;
    }
    
    // private : create and save order
    function _confirmOrder($order_id, $data, $payment_id, $payment_type, $old_status, $status, $msg_result, $history_notified = 0) {
    	// Prepare order and history order
		$order = null;
		$order->order_id = $order_id;
		$order->order_status = $status;
		$order->old_status->order_status = $old_status;
			
		$order->history->history_reason = JText::sprintf('AUTOMATIC_PAYMENT_NOTIFICATION');
		$order->history->history_amount = ($data['vads_amount'] / 100) . '  ' . VadsApi::findCurrencyByNumCode($data['vads_currency'])->alpha3;
		$order->history->history_payment_id = $payment_id;
		$order->history->history_payment_method = $payment_type;
		$order->history->history_type = 'payment';
		$order->history->history_notified = $history_notified;
    	
		$info = JText::_( 'VADS_RESULT' ) . $msg_result . ' ('. $data['vads_result'] . ')' . ', ';
		$info .= JText::_( 'VADS_TRANS_ID' ) . $data['vads_trans_id'] . ', ';
		$info .= JText::_( 'VADS_CC_NUMBER' ) . $data['vads_card_number'] . ', ';
		$info .= JText::_( 'VADS_CC_EXPIRY' ) . str_pad($data['vads_expiry_month'], 2, '0', STR_PAD_LEFT) . ' / ' . $data['vads_expiry_year'] . ', ';
		$info .= JText::_( 'VADS_CC_TYPE' ) . $data['vads_card_brand'];
		
		$order->history->history_data = $info;
		
		// Save order and history order
		$orderClass = hikashop::get('class.order');
		$orderClass->save($order);
    }
    
    // Called before load VADS module configuration page
    function onPaymentConfiguration(&$element){
    	$this->vads = JRequest::getCmd('name', 'vads');  
		if(empty($element)){
			$element = null;
    		$element->payment_name = 'PayZen';
    		$element->payment_description = JText::_( 'VADS_DEFAULT_DESCRIPTION' );
    		$element->payment_images = 'PayZen';
    		$element->payment_type = $this->vads;
    		
    		$element->payment_params = null;
    		
    		$element->payment_params->vads_gateway_url = 'https://secure.payzen.eu/vads-payment/';
     		$element->payment_params->vads_site_id = '12345678';
			$element->payment_params->vads_test_key = '1111111111111111';    			
    		$element->payment_params->vads_prod_key = '2222222222222222';
    		$element->payment_params->vads_ctx_mode = 'TEST';
    		$element->payment_params->vads_language = 'fr';
    		$element->payment_params->vads_capture_delay = '';
    		$element->payment_params->vads_validation_mode = '';
    		$element->payment_params->vads_payment_cards = '';
    		$element->payment_params->vads_amount_min = '';
    		$element->payment_params->vads_amount_max = '';
			$element->payment_params->vads_redirect_enabled = 1;
			$element->payment_params->vads_redirect_success_timeout = '5';
			$element->payment_params->vads_redirect_success_message = JText::_( 'VADS_DEFAULT_SUCCESS_MSG' );
			$element->payment_params->vads_redirect_error_timeout = '5';
			$element->payment_params->vads_redirect_error_message = JText::_( 'VADS_DEFAULT_ERROR_MSG' );
			$element->payment_params->vads_return_mode = 'GET';
			$element->payment_params->vads_url_success = HIKASHOP_LIVE.'index.php?option=com_hikashop&ctrl=checkout&task=after_end';
			$element->payment_params->vads_url_return = HIKASHOP_LIVE.'index.php?option=com_hikashop&ctrl=checkout&task=step&step=0';	
			$element->payment_params->invalid_status = 'cancelled';
    		$element->payment_params->verified_status = 'confirmed';
			
    		$element = array($element);
    	}
    	
	    $bar = & JToolBar::getInstance('toolbar');
		JToolBarHelper::save();
		JToolBarHelper::apply();
		JToolBarHelper::cancel();
		JToolBarHelper::divider();
		$bar->appendButton('Pophelp', 'payment-vads-form');
		hikashop::setTitle('PayZen - Configuration', 'plugin', 'plugins&plugin_type=payment&task=edit&name='.$this->vads);
		$app =& JFactory::getApplication();
		$app->setUserState(HIKASHOP_COMPONENT.'.payment_plugin_type', $this->vads);
		$this->address = hikashop::get('type.address');
		$this->category = hikashop::get('type.categorysub');
	
		$this->category->type = 'status';
    }
    
    // Called before save VADS module configuration 
    function onPaymentConfigurationSave(&$element){
		return true;
    }
}

?>