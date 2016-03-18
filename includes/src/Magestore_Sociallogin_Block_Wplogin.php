<?php
class Magestore_Sociallogin_Block_Wplogin extends Mage_Core_Block_Template
{
    public function getLoginUrl(){
		return $this->getUrl('sociallogin/wplogin/login');
	}	
	
    public function getAlLoginUrl(){
        return $this->getUrl('sociallogin/wplogin/setBlogName');
    }
    
    public function getCheckName(){        
        return $this->getUrl('sociallogin/wplogin/setBlock');
    }
	
	public function getEnterName(){
		return 'ENTER YOUR BLOG NAME';
	}
	
	public function getName(){
		return 'Name';
	}
}