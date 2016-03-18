<?php
class Magestore_Sociallogin_Model_Mplogin extends Mage_Core_Model_Abstract {      
	
	//static public $token;	
	
    public function newMp($token = null){
        
		try{
			require_once Mage::getBaseDir('base').DS.'lib'.DS.'Author'.DS.'OAuth.php';
            require_once Mage::getBaseDir('base').DS.'lib'.DS.'Author'.DS.'OAuth1Client.php';
		}catch(Exception $e){}
        try{
			if ($token){
				$mp = new OAuth1Client(
                    Mage::helper('sociallogin')->getMpConsumerKey(), 					
                    Mage::helper('sociallogin')->getMpConsumerSecret(),                    
					$token['oauth_token'],
					$token['oauth_token_secret']
                );    
			}else{
				$mp = new OAuth1Client(
                    Mage::helper('sociallogin')->getMpConsumerKey(), 					
                    Mage::helper('sociallogin')->getMpConsumerSecret()                  					
                ); 
			} 
			$mp->api_base_url          = "http://api.myspace.com/v1/";
			$mp->authorize_url         = "http://api.myspace.com/authorize";			
			$mp->request_token_url     = "http://api.myspace.com/request_token";
			$mp->access_token_url      = "http://api.myspace.com/access_token";
            return $mp;
        }catch(Exception $e){}
    }
    
	
    public function getUrlAuthorCode(){
        $mp = $this->newMp();		
        $token = $mp->requestToken(Mage::helper('sociallogin')->getAuthUrlMp());			
		Mage::getSingleton('core/session')->setRequestToken($token);
		return  $mp->authorizeUrl($token);		
    }	
}

  
