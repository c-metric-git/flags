<?php
class Magestore_Sociallogin_Block_Gologin extends Mage_Core_Block_Template
{
	public function getLoginUrl(){
		return $this->getUrl('sociallogin/gologin/login');
	}
	
	protected function _beforeToHtml()
	{
		if(!Mage::helper('magenotification')->checkLicenseKey('Sociallogin')){
			$this->setTemplate(null);
		}
		return parent::_beforeToHtml();
	}		
}