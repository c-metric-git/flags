<?php
class Magestore_Sociallogin_GologinController extends Mage_Core_Controller_Front_Action{
	
	public function loginAction() {		
		if(!Mage::helper('magenotification')->checkLicenseKeyFrontController($this)){return;}
		if (!$this->getAuthorizedToken()) {
			$token = $this->getAuthorization();
		}
		else {
			$token = $this->getAuthorizedToken();
		}
		
        return $token;
    }
	
	public function userAction() {
		$gologin = Mage::getModel('sociallogin/gologin');
		$oauth2 = new Google_Oauth2Service($gologin);
		$code = $this->getRequest()->getParam('code');
		if(!$code){
                        $message=$this->__('Login failed as you have not granted access.');
			Mage::getSingleton('core/session')->addError($message);			
			die("<script type=\"text/javascript\">try{window.opener.location.reload(true);}catch(e){window.opener.location.href=\"".Mage::app()->getStore()->getBaseUrl()."\"} window.close();</script>");
		}
		$accessToken = $gologin->authenticate($code);						
		$client = $oauth2->userinfo->get();
		
		$user = array();		
		$email = $client['email'];		
		$name = $client['name'];
		$arrName = explode(' ', $name, 2);
		$user['firstname'] = $arrName[0];
		$user['lastname'] = $arrName[1];			
		$user['email'] = $email;
		
		//get website_id and sote_id of each stores
		$store_id = Mage::app()->getStore()->getStoreId();//add
		$website_id = Mage::app()->getStore()->getWebsiteId();//add
		
		$customer = Mage::helper('sociallogin')->getCustomerByEmail($user['email'],$website_id );//add edition
		if(!$customer || !$customer->getId()){
			//Login multisite
				$customer = Mage::helper('sociallogin')->createCustomerMultiWebsite($user, $website_id, $store_id );                               
                                if(Mage::getStoreConfig(('sociallogin/general/send_newemail'),Mage::app()->getStore()->getId())) $customer->sendNewAccountEmail('registered','',Mage::app()->getStore()->getId());                                
				if (Mage::getStoreConfig('sociallogin/gologin/is_send_password_to_customer')){
					$customer->sendPasswordReminderEmail();
				}
		}
			// fix confirmation
			if ($customer->getConfirmation())
			{
				try {
					$customer->setConfirmation(null);
					$customer->save();
				}catch (Exception $e) {
				}
	  		}
		Mage::getSingleton('customer/session')->setCustomerAsLoggedIn($customer);
		die("<script type=\"text/javascript\">if(navigator.userAgent.match('CriOS')){window.location.href=\"".$this->_loginPostRedirect()."\";}else{try{window.opener.location.href=\"".$this->_loginPostRedirect()."\";}catch(e){window.opener.location.reload(true);} window.close();}</script>");
    }
	
	// if exit access token
	public function getAuthorizedToken() {
        $token = false;
        if (!is_null(Mage::getSingleton('core/session')->getAccessToken())) {
            $token = unserialize(Mage::getSingleton('core/session')->getAccessToken());
        }
        return $token;
    }
     
	// if not exit access token
     public function getAuthorization() {      
       	$scope = array(
					'https://www.googleapis.com/auth/userinfo.profile',
					'https://www.googleapis.com/auth/userinfo.email'
				 );		
		$gologin = Mage::getModel('sociallogin/gologin');        			
		$gologin->setScopes($scope); 		
		$gologin->authenticate();					
		$authUrl = $gologin->createAuthUrl();
		header('Localtion: '.$authUrl);
		die(0);
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