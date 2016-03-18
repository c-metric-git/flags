<?php
class Magestore_Sociallogin_Model_Fqlogin extends Mage_Core_Model_Abstract {
	public function newFoursquare(){
	
		try{
			require_once Mage::getBaseDir('base').DS.'lib'.DS.'Foursquare'.DS.'FoursquareAPI.class.php';
		}catch(Exception $e){}
		
		$foursquare = new FoursquareApi(
			Mage::helper('sociallogin')->getFqAppkey(),
			Mage::helper('sociallogin')->getFqAppSecret(),
            urlencode(Mage::helper('sociallogin')->getAuthUrlFq())
		);
		return $foursquare;
	}		
	
	public function getFqLoginUrl(){
		$foursquare = $this->newFoursquare();
		$loginUrl = $foursquare->AuthenticationLink();
		return $loginUrl;
	}
}
  
