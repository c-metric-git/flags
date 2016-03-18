<?php
class Magestore_Sociallogin_Block_Openlogin extends Mage_Core_Block_Template
{
	public function getLoginUrl(){
		return $this->getUrl('sociallogin/openlogin/login');
		//return Mage::getModel('sociallogin/mylogin')->getMyLoginUrl();
	}
    
    public function getSetBlock(){
        return $this->getUrl('sociallogin/openlogin/setBlock');        
    }
	
	public function setBackUrl(){
		$currentUrl = Mage::helper('core/url')->getCurrentUrl();
		Mage::getSingleton('core/session')->setBackUrl($currentUrl);
		return $currentUrl;
	}
	
	protected function _beforeToHtml()
	{
		if(!Mage::helper('magenotification')->checkLicenseKey('Sociallogin')){
			$this->setTemplate(null);
		}
		return parent::_beforeToHtml();
	}		
}