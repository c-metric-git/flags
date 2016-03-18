<?php
class Magestore_Sociallogin_Block_Linkedlogin extends Mage_Core_Block_Template
{
	public function getLoginUrl(){
		return $this->getUrl('sociallogin/linkedlogin/login');
	}
	
	/*public function getLoginUrl(){
        return Mage::getModel('sociallogin/linkedlogin')->getLinkedLoginUrl();
    }*/
	
	protected function _beforeToHtml()
	{
		if(!Mage::helper('magenotification')->checkLicenseKey('Sociallogin')){
			$this->setTemplate(null);
		}
		return parent::_beforeToHtml();
	}
}