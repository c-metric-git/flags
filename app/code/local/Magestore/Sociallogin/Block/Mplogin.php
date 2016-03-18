<?php
class Magestore_Sociallogin_Block_Mplogin extends Mage_Core_Block_Template
{	
	public function getUrlAuthorCode(){
		return Mage::getModel('sociallogin/mplogin')->getUrlAuthorCode();
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