<?php
class Magestore_Sociallogin_Block_Perlogin extends Mage_Core_Block_Template
{
	protected function _beforeToHtml()
	{
		if(!Mage::helper('magenotification')->checkLicenseKey('Sociallogin')){
			$this->setTemplate(null);
		}
		return parent::_beforeToHtml();
	}
	public function getPerLoginUrl(){
		return $this->getUrl('sociallogin/perlogin/login/');
	}
}