<?php
require_once Mage::getBaseDir('base').DS.'lib'.DS.'Vk'.DS.'VK.php';
require_once Mage::getBaseDir('base').DS.'lib'.DS.'Vk'.DS.'VKException.php';
class Magestore_Sociallogin_Model_Vklogin extends Mage_Core_Model_Abstract {
	
	public function getVk()
	{
		$appId = Mage::helper('sociallogin')->getVkAppId();
		$secretId = Mage::helper('sociallogin')->getVkSecureKey();		
	    $vk = new VK($appId, $secretId);
		return $vk;
	}

	public function _construct()
    {            
		parent::_construct();
        $this->_init('sociallogin/vklogin');
    }	
}
 
