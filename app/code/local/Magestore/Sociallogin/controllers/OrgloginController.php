<?php
class Magestore_Sociallogin_OrgloginController extends Mage_Core_Controller_Front_Action{

	/**
	* getToken and call profile user Orange
	**/
    public function loginAction() {     
		if(!Mage::helper('magenotification')->checkLicenseKeyFrontController($this)){return;}
		$org = Mage::getModel('sociallogin/orglogin')->newOrg();            
		$coreSession = Mage::getSingleton('core/session');                      
                $user_info = $org->data;                 
                if(count($user_info)){
                    $frist_name = $user_info['openid_sreg_nickname'];
                    $last_name = $user_info['openid_sreg_nickname'];
                    $email = $user_info['openid_sreg_email'];                    
					
					//get website_id and sote_id of each stores
					$store_id = Mage::app()->getStore()->getStoreId();//add
					$website_id = Mage::app()->getStore()->getWebsiteId();//add
					
                    $data = array('firstname'=>$frist_name, 'lastname'=>$last_name, 'email'=>$email);
					
                    $customer = Mage::helper('sociallogin')->getCustomerByEmail($data['email'],$website_id);
                    if(!$customer || !$customer->getId()){
						//Login multisite
						$customer = Mage::helper('sociallogin')->createCustomerMultiWebsite($data, $website_id, $store_id );
						if (Mage::getStoreConfig('sociallogin/orglogin/is_send_password_to_customer')){
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
                else{
                   $message=$this->__('Login failed as you have not granted access.');
                   $coreSession->addError($message);			
                   die("<script type=\"text/javascript\">try{window.opener.location.reload(true);}catch(e){window.opener.location.href=\"".Mage::app()->getStore()->getBaseUrl()."\"} window.close();</script>");
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