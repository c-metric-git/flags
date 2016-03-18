<?php
require_once Mage::getBaseDir('base').DS.'lib'.DS.'Oauth2'.DS.'service'.DS.'Google_ServiceResource.php';
require_once Mage::getBaseDir('base').DS.'lib'.DS.'Oauth2'.DS.'service'.DS.'Google_Service.php';
require_once Mage::getBaseDir('base').DS.'lib'.DS.'Oauth2'.DS.'service'.DS.'Google_Model.php';
require_once Mage::getBaseDir('base').DS.'lib'.DS.'Oauth2'.DS.'contrib'.DS.'Google_Oauth2Service.php';
require_once Mage::getBaseDir('base').DS.'lib'.DS.'Oauth2'.DS.'Google_Client.php';
class Magestore_Sociallogin_Model_Gologin extends Google_Client {

	protected $_options = null;
	public function __construct(){
		$this->_config = new Google_Client;					
		$this->_config->setClientId(Mage::helper('sociallogin')->getGoConsumerKey());
		$this->_config->setClientSecret(Mage::helper('sociallogin')->getGoConsumerSecret());
		$this->_config->setRedirectUri(Mage::app()->getStore()->getUrl('sociallogin/gologin/user',array('_secure'=>true)));		
	}		 
}
  
