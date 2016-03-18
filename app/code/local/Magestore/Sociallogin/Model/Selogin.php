<?php
class Magestore_Sociallogin_Model_Selogin extends Mage_Core_Model_Abstract {
    
	public function newSe(){	
		try{
			require_once Mage::getBaseDir('base').DS.'lib'.DS.'OpenId'.DS.'openid.php';           
		}catch(Exception $e){}					
        
        $openid = new LightOpenID(Mage::app()->getStore()->getUrl());    
        return $openid;
	}		
	
	public function getSeLoginUrl($name){
		$aol_id = $this->newSe();
        $aol = $this->setSeIdlogin($aol_id, $name);
        try{
            $loginUrl = $aol->authUrl();
            return $loginUrl;
        }catch(Exception $e){
            return null;
        }        		
		
	}
    
    public function setSeIdlogin($openid, $name){
        
        $openid->identity = 'https://openid.stackexchange.com';
        $openid->required = array(
        'namePerson/first',
        'namePerson/last',
        'namePerson/friendly',
        'contact/email',
        );
        
        $openid->returnUrl = Mage::app()->getStore()->getUrl('sociallogin/selogin/login');
		return $openid;
    }
       
}
  
