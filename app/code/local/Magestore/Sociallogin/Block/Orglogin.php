<?php
class Magestore_Sociallogin_Block_Orglogin extends Mage_Core_Block_Template
{
    public function getLoginUrl(){
		return $this->getUrl('sociallogin/orglogin/login');
	}	
	
    public function getAlLoginUrl(){
        return Mage::getModel('sociallogin/orglogin')->getOrgLoginUrl();
    }
}