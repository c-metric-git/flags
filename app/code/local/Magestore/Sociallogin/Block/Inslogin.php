<?php
class Magestore_Sociallogin_Block_Inslogin extends Mage_Core_Block_Template
{
	public function getInstagramLoginUrl(){
		return Mage::getModel('sociallogin/instagramlogin')->getInstagramLoginUrl();
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