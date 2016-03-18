<?php
class Magestore_Sociallogin_Block_Fqlogin extends Mage_Core_Block_Template
{
	public function getLoginUrl(){
		return $this->getUrl('sociallogin/fqlogin/login');
	}
	
	public function getFqUser(){
		return Mage::getModel('sociallogin/fqlogin')->getFqUser();
	}
	
	public function getFqLoginUrl(){
		return Mage::getModel('sociallogin/fqlogin')->getFqLoginUrl();
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