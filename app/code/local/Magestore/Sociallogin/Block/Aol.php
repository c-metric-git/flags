<?php
class Magestore_Sociallogin_Block_Aol extends Mage_Core_Block_Template
{
    public function getLoginUrl(){
		return $this->getUrl('sociallogin/allogin/login');
	}	
	
    public function getAlLoginUrl(){
        return Mage::getModel('sociallogin/allogin')->getAlLoginUrl();
    }
}