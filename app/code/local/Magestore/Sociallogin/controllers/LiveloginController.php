<?php
class Magestore_Sociallogin_LiveloginController extends Mage_Core_Controller_Front_Action{

    public function loginAction(){  
		$isAuth = $this->getRequest()->getParam('auth');
        $code = $this->getRequest()->getParam('code');
        $live = Mage::getModel('sociallogin/livelogin')->newLive();        
		try{
			$json = $live->authenticate($code);
			$user = $live->get("me", $live->param);	
		}catch(Exception $e){
                        $message=$this->__('Login failed as you have not granted access.');
			Mage::getSingleton('core/session')->addError($message);			
			die("<script type=\"text/javascript\">try{window.opener.location.reload(true);}catch(e){window.opener.location.href=\"".Mage::app()->getStore()->getBaseUrl()."\"} window.close();</script>");
		}		
        $first_name = $user->first_name;
		$last_name = $user->last_name;
		$email = $user->emails->account;	
		//get website_id and sote_id of each stores
		$store_id = Mage::app()->getStore()->getStoreId();//add
		$website_id = Mage::app()->getStore()->getWebsiteId();//add		
		
		if ($isAuth){
			$data =  array('firstname'=>$first_name, 'lastname'=>$last_name, 'email'=>$email);		
			$customer = Mage::helper('sociallogin')->getCustomerByEmail($data['email'], $website_id);//add edtition
			if(!$customer || !$customer->getId()){
				//Login multisite
				$customer = Mage::helper('sociallogin')->createCustomerMultiWebsite($data, $website_id, $store_id );
				if(Mage::getStoreConfig(('sociallogin/general/send_newemail'),Mage::app()->getStore()->getId())) $customer->sendNewAccountEmail('registered','',Mage::app()->getStore()->getId());
                                if (Mage::getStoreConfig('sociallogin/livelogin/is_send_password_to_customer')){
					$customer->sendPasswordReminderEmail();
				}
			}
				// fix confirmation
			if ($customer->getConfirmation())
			{
				try {
					$customer->setConfirmation(null);
					$customer->save();
				}catch (Exception $e) {
				}
	  		}
			Mage::getSingleton('customer/session')->setCustomerAsLoggedIn($customer);
			die("<script type=\"text/javascript\">if(navigator.userAgent.match('CriOS')){window.location.href=\"".$this->_loginPostRedirect()."\";}else{try{window.opener.location.href=\"".$this->_loginPostRedirect()."\";}catch(e){window.opener.location.reload(true);} window.close();}</script>");
    	}
	}
	protected function _loginPostRedirect()
    {
        $selecturl= Mage::getStoreConfig(('sociallogin/general/select_url'),Mage::app()->getStore()->getId());
	if($selecturl==0) return Mage::getUrl('customer/account');	
	if($selecturl==2) return Mage::getUrl();
	if($selecturl==3) return Mage::getSingleton('core/session')->getSocialCurrentpage();
	if($selecturl==4) return Mage::getStoreConfig(('sociallogin/general/custom_page'),Mage::app()->getStore()->getId());
        if($selecturl==1 && Mage::helper('checkout/cart')->getItemsCount()!=0) return Mage::getUrl('checkout/cart');else return Mage::getUrl();
    }        
}