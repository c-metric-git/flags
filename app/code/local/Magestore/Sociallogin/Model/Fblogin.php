<?php
class Magestore_Sociallogin_Model_Fblogin extends Mage_Core_Model_Abstract {
	public function newFacebook(){
		error_reporting(E_ALL^ E_WARNING);
		error_reporting(E_ALL^ E_NOTICE);
		try{
			require_once Mage::getBaseDir('base').DS.'lib'.DS.'Facebook'.DS.'facebook.php';
		}catch(Exception $e){}
		
		$facebook = new Facebook(array(
			'appId'  => Mage::helper('sociallogin')->getFbAppId(),
			'secret' => Mage::helper('sociallogin')->getFbAppSecret(),
			'cookie' => true,
		));
		return $facebook;
	}
	
	public function getFbUser(){
		$facebook = $this->newFacebook();
    	$userId = $facebook->getUser();
		$fbme = NULL;

		if ($userId) {
			try {
				$fbme = $facebook->api('/me?fields=email,first_name,last_name');
			} catch (FacebookApiException $e) {}
		}
		
		return $fbme;	
	}
	
	public function getFbLoginUrl(){
		$facebook = $this->newFacebook();
		$loginUrl = $facebook->getLoginUrl(
			array(
				'display'   => 'popup',
				'redirect_uri' => Mage::helper('sociallogin')->getAuthUrl(),
				'scope' => 'email',
			)
  		);
		return $loginUrl;
	}
}
  
