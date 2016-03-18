<?php
class Magestore_Sociallogin_Block_Amazon extends Mage_Core_Block_Template
{
	protected function _beforeToHtml()
	{
		if(!Mage::helper('magenotification')->checkLicenseKey('Sociallogin')){
			$this->setTemplate(null);
		}
		return parent::_beforeToHtml();
	}		
}