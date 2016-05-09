<?php

class Magestore_Sociallogin_PerloginController extends Mage_Core_Controller_Front_Action{
	
	
    public function loginAction() {
		// url de xac nhan
		$url = 'https://verifier.login.persona.org/verify';
		$assert=$this->getRequest()->getParam('assertion');// lay ma xac nhan	
		//Url+port
		//$audience = ($_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://') . $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'];
		$params = 'assertion=' . urlencode($assert) . '&audience=' .
				   urlencode(Mage::app()->getStore()->getUrl());
		//gui xac nhan
		$ch = curl_init();
		$options = array(
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => TRUE,
			CURLOPT_POST => 2,
			CURLOPT_POSTFIELDS => $params
		);
		curl_setopt_array($ch, $options);
		$result = curl_exec($ch);
		curl_close($ch);
		$status=Mage::helper('sociallogin')->getPerResultStatus($result);
		if($status=='okay'){
		
			//get website_id and sote_id of each stores
			$store_id = Mage::app()->getStore()->getStoreId();
			$website_id = Mage::app()->getStore()->getWebsiteId();
			
			$email= Mage::helper('sociallogin')->getPerEmail($result);
			$name=explode("@", $email);
			$data =  array('firstname'=>$name[0], 'lastname'=>$name[0], 'email'=>$email);
			$customer = Mage::helper('sociallogin')->getCustomerByEmail($email, $website_id);
			if(!$customer || !$customer->getId()){
				//Login multisite
				$customer = Mage::helper('sociallogin')->createCustomerMultiWebsite($data, $website_id, $store_id );
				if(Mage::getStoreConfig('sociallogin/perlogin/is_send_password_to_customer')){
					$customer->sendPasswordReminderEmail();
				}
				if ($customer->getConfirmation())
				{
					try {
						$customer->setConfirmation(null);
						$customer->save();
					}catch (Exception $e) {
						Mage::getSingleton('core/session')->addError(Mage::helper('sociallogin')->__('Error'));
					}
				}
			}
			
			Mage::getSingleton('customer/session')->setCustomerAsLoggedIn($customer);
			die("<script type=\"text/javascript\">if(navigator.userAgent.match('CriOS')){window.location.href=\"".$this->_loginPostRedirect()."\";}else{try{window.opener.location.href=\"".$this->_loginPostRedirect()."\";}catch(e){window.opener.location.reload(true);} window.close();}</script>");
			
		}
		else{
			Mage::getSingleton('core/session')->addError($this->__('Login failed as you have not granted access.'));
			$this->_redirect();
		}
    }
	protected function _loginPostRedirect()
    {
        $selecturl= Mage::getStoreConfig(('sociallogin/general/select_url'),Mage::app()->getStore()->getId());
	if($selecturl==0) return Mage::getUrl('customer/account');
	if($selecturl==2) return Mage::getUrl();
	if($selecturl==3) return Mage::getSingleton('core/session')->getSocialCurrentpage();
	if($selecturl==4) return Mage::getStoreConfig(('sociallogin/general/custom_page'),Mage::app()->getStore()->getId());
	if($selecturl==1 && Mage::helper('checkout/cart')->getItemsCount()!=0) return Mage::getUrl('checkout/cart');else return Mage::getUrl();
    }
}
