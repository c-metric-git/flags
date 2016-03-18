<?php
class Magestore_Sociallogin_Block_Livelogin extends Mage_Core_Block_Template
{
	public function getLoginUrl(){
		return $this->getUrl('sociallogin/fqlogin/login');
	}
	
	public function getFqUser(){
		return Mage::getModel('sociallogin/fqlogin')->getFqUser();
	}
	
	public function getUrlAuthorCode(){
		return Mage::getModel('sociallogin/livelogin')->getUrlAuthorCode();
	}
	
	public function getDirectLoginUrl(){
		return Mage::helper('sociallogin')->getDirectLoginUrl();
	}
	
	protected function _beforeToHtml()
	{
		if(!Mage::helper('magenotification')->checkLicenseKey('Sociallogin')){
			$this->setTemplate(null);
		}
		return parent::_beforeToHtml();
	}		
		
}