<?php
class Magestore_Sociallogin_AmazonloginController extends Mage_Core_Controller_Front_Action{

public function loginAction() {            
    $amazon = Mage::getModel('sociallogin/amazon');
        $token = $this->getRequest()->getParam('token', false);
        if(!$token) {
            $message=$this->__('You provided a email invalid!');
            Mage::getSingleton('core/session')->addError($message);           	
            die("<script type=\"text/javascript\">try{window.location.reload(true);}catch(e){window.location.href=\"".Mage::app()->getStore()->getBaseUrl()."\"}</script>");
            return;
        }
        // get profile
        $profile = $amazon->getUserProfileFromAccessToken($token);
	if($profile && $profile->user_id) {
            $store_id = Mage::app()->getStore()->getStoreId();//add
            $website_id = Mage::app()->getStore()->getWebsiteId();//add
            $data =  array();
            if(false===strpos($profile->name, ' ')) {
                $len = round(strlen($profile->name) / 2);
                $data['firstname'] = substr($profile->name, 0, $len);
                $data['lastname'] = substr($profile->name, $len);
            } else {
                $list = explode(' ', $profile->name);
                $data['lastname'] = array_pop($list);
                $data['firstname'] = implode(' ', $list);
            }
            $data['email'] = $profile->email;
            if($data['email']){
				$customer = Mage::helper('sociallogin')->getCustomerByEmail($data['email'],$website_id );//add edition
				if(!$customer || !$customer->getId()){
					//Login multisite
					$customer = Mage::helper('sociallogin')->createCustomerMultiWebsite($data, $website_id, $store_id );
                                        if(Mage::getStoreConfig(('sociallogin/general/send_newemail'),Mage::app()->getStore()->getId())) $customer->sendNewAccountEmail('registered','',Mage::app()->getStore()->getId());
					if(Mage::getStoreConfig('sociallogin/fblogin/is_send_password_to_customer')){
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
				die("<script type=\"text/javascript\">try{window.location.href=\"".$this->_loginPostRedirect()."\";}catch(e){window.location.reload(true);}</script>");   
			}else{
				$message=$this->__('You provided a email invalid!');
				Mage::getSingleton('core/session')->addError($message);			
				die("<script type=\"text/javascript\">try{window.location.reload(true);}catch(e){window.location.href=\"".Mage::app()->getStore()->getBaseUrl()."\"}</script>");
			}
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