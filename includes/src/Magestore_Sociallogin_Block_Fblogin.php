<?php
class Magestore_Sociallogin_Block_Fblogin extends Mage_Core_Block_Template
{
	public function getLoginUrl(){
		return $this->getUrl('sociallogin/fblogin/login');
	}
	
	public function getFbUser(){
		return Mage::getModel('sociallogin/fblogin')->getFbUser();
	}
	
	public function getFbLoginUrl(){
		return Mage::getModel('sociallogin/fblogin')->getFbLoginUrl();
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