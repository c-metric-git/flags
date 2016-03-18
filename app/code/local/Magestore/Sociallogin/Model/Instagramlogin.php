<?php
class Magestore_Sociallogin_Model_Instagramlogin extends Mage_Core_Model_Abstract {
	public function newInstagram(){
		try{
			require_once Mage::getBaseDir('base').DS.'lib'.DS.'instagram'.DS.'instagram.class.php';
		}catch(Exception $e){}
		
		$instagram = new Instagram(array(
			'apiKey'      => trim(Mage::getStoreConfig('sociallogin/instalogin/consumer_key')),
			'apiSecret'   => trim(Mage::getStoreConfig('sociallogin/instalogin/consumer_secret')),
			'apiCallback' => Mage::app()->getStore()->getUrl('sociallogin/instagramlogin/login/',array('_secure'=>true)), // must point to success.php
		));
		return $instagram;  
	}
	public function getInstagramLoginUrl(){
		return $this->newInstagram()->getLoginUrl();
	}
}
  
