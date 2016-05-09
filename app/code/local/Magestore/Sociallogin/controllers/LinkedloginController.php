<?php
class Magestore_Sociallogin_LinkedloginController extends Mage_Core_Controller_Front_Action{
	
	public function loginAction() {
		if(!Mage::helper('magenotification')->checkLicenseKeyFrontController($this)){return;}
		if (!$this->getAuthorizedToken()) {
			try{
				$token = $this->getAuthorization();
			}catch(Exception $e){
                                $message=$this->__('Htpp not request.Please input api key on config again');
				Mage::getSingleton('core/session')->addError($message);			
				die("<script type=\"text/javascript\">try{window.opener.location.reload(true);}catch(e){window.opener.location.href=\"".Mage::app()->getStore()->getBaseUrl()."\"} window.close();</script>");
			}
		}
		else {
			$token = $this->getAuthorizedToken();
		}
        return $token;
    }
	
	public function userAction() {
		$linkedlogin = Mage::getModel('sociallogin/linkedlogin');
		
		$oauth_data = array(
                'oauth_token' => $this->getRequest()->getParam('oauth_token'),
                'oauth_verifier' => $this->getRequest()->getParam('oauth_verifier')
         );
		 
		
		$requestToken = Mage::getSingleton('core/session')->getRequestToken(array('scope' =>'r_emailaddress'));
		try{
			$accessToken = $linkedlogin ->getAccessToken($oauth_data, unserialize($requestToken));
		}catch(Exception $e){
                        $message=$this->__('User has not shared information so login fail!');
			Mage::getSingleton('core/session')->addError($message);			
			die("<script type=\"text/javascript\">try{window.opener.location.reload(true);}catch(e){window.opener.location.href=\"".Mage::app()->getStore()->getBaseUrl()."\"} window.close();</script>");
		}
		$oauthOptions = $linkedlogin->getOptions();
		$options = $oauthOptions;
		$client = $accessToken->getHttpClient($options);
		$client -> setUri('http://api.linkedin.com/v1/people/~');
		$client->setMethod(Zend_Http_Client::GET);
		$response = $client -> request ();
		$content =  $response -> getBody ();
		$xml = simplexml_load_string ($content);
		$user = array();
		$firstname = (string) $xml->{'first-name'};
		$lastname = (string) $xml->{'last-name'};
		$client2 = $accessToken->getHttpClient($options);
		$client2 -> setUri('http://api.linkedin.com/v1/people/~/email-address');
		$client2->setMethod(Zend_Http_Client::GET);
		$response2 = $client2 -> request ();
		$content2 =  $response2 -> getBody ();
		$xml2 = simplexml_load_string ($content2);
		$email = (string) $xml2->{0};
		if(!$email){
			$email = $firstname.$lastname."@linkedin.com";
		}
		
		$user['firstname'] = $firstname;
		$user['lastname'] = $lastname;
		$user['email'] = $email;
		
		//get website_id and sote_id of each stores
		$store_id = Mage::app()->getStore()->getStoreId();
		$website_id = Mage::app()->getStore()->getWebsiteId();
		
		$customer = Mage::helper('sociallogin')->getCustomerByEmail($email,$website_id);//add edition
		if(!$customer || !$customer->getId()){
			//Login multisite
				$customer = Mage::helper('sociallogin')->createCustomerMultiWebsite($user, $website_id, $store_id );
                                if(Mage::getStoreConfig(('sociallogin/general/send_newemail'),Mage::app()->getStore()->getId())) $customer->sendNewAccountEmail('registered','',Mage::app()->getStore()->getId());
				if (Mage::getStoreConfig('sociallogin/linklogin/is_send_password_to_customer')){
					$customer->sendPasswordReminderEmail();
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
		else{
			$getConfirmPassword = (int)Mage::getStoreConfig('sociallogin/linklogin/is_customer_confirm_password');
			if($getConfirmPassword){
				die(" 
				<script type=\"text/javascript\">
				var email = ' $email ';
				window.opener.opensocialLogin();
				window.opener.document.getElementById('magestore-sociallogin-popup-email').value = email;
				window.close();</script>  ");
			}
			else{
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
		}
			
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
	 	$scope = 'r_emailaddress';
        $olinked = Mage::getModel('sociallogin/linkedlogin');
        $olinked ->setCallbackUrl(Mage::app()->getStore()->getUrl('sociallogin/linkedlogin/user'));        
        if (!is_null($this->getRequest()->getParam('oauth_token')) && !is_null($this->getRequest()->getParam('oauth_verifier'))) {
            $oauth_data = array(
                'oauth_token' => $this->_getRequest()->getParam('oauth_token'),
                'oauth_verifier' => $this->_getRequest()->getParam('oauth_verifier')
            );
            $token =  $olinked ->getAccessToken($oauth_data, unserialize(Mage::getSingleton('core/session')->getRequestToken()));
            Mage::getSingleton('core/session')->setAccessToken(serialize($token));
             $olinked ->redirect();
        } else {
            $token = $olinked ->getRequestToken(array('scope' => $scope));
            Mage::getSingleton('core/session')->setRequestToken(serialize($token));
            $olinked ->redirect();
        }
        return $token;
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