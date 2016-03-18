<?php
class Magestore_Sociallogin_Block_Selogin extends Mage_Core_Block_Template
{
	protected function _beforeToHtml()
	{
		if(!Mage::helper('magenotification')->checkLicenseKey('Sociallogin')){
			$this->setTemplate(null);
		}
		return parent::_beforeToHtml();
	}	
	public function getSeLoginUrl(){
		return $this->getUrl('sociallogin/selogin/login');
	}
}