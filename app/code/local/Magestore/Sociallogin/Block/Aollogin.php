<?php
class Magestore_Sociallogin_Block_Aollogin extends Mage_Core_Block_Template
{
    public function getLoginUrl(){
		return $this->getUrl('sociallogin/allogin/login');
	}	
	
    public function getAlLoginUrl(){
        return $this->getUrl('sociallogin/allogin/setScreenName');
    }
	
	public function getEnterName(){
		return 'ENTER SCREEN NAME';
	}
	
	public function getName(){
		return 'Name';
	}
	
	public function getCheckName(){
		return $this->getUrl('sociallogin/allogin/setBlock');
	}
	
	protected function _beforeToHtml()
	{
		if(!Mage::helper('magenotification')->checkLicenseKey('Sociallogin')){
			$this->setTemplate(null);
		}
		return parent::_beforeToHtml();
	}
}