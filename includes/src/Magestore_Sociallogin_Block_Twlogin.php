<?php
class Magestore_Sociallogin_Block_Twlogin extends Mage_Core_Block_Template
{
	public function getLoginUrl(){
		return $this->getUrl('sociallogin/twlogin/login');
	}
	
	public function setBackUrl(){
		$currentUrl = Mage::helper('core/url')->getCurrentUrl();
		Mage::getSingleton('core/session')->setBackUrl($currentUrl);
        //Zend_debug::dump($currentUrl);
		return $currentUrl;
	}
	
	protected function _beforeToHtml()
	{
		if(!Mage::helper('magenotification')->checkLicenseKey('Sociallogin')){
			$this->setTemplate(null);
		}
		return parent::_beforeToHtml();
	}		
}