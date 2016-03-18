<?php
class Magestore_Sociallogin_AmazonloginController extends Mage_Core_Controller_Front_Action{

public function loginAction() {            
    $amazon = Mage::getModel('sociallogin/amazon');
        $token = $this->getRequest()->getParam('token', false);
        if(!$token) {
            Mage::getSingleton('core/session')->addError('You provided a email invalid!');			
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
				Mage::getSingleton('core/session')->addError('You provided a email invalid!');			
				die("<script type=\"text/javascript\">try{window.location.reload(true);}catch(e){window.location.href=\"".Mage::app()->getStore()->getBaseUrl()."\"}</script>");
			}
		}
	}
	protected function _loginPostRedirect()
    {
        $session = Mage::getSingleton('customer/session');

        if (!$session->getBeforeAuthUrl() || $session->getBeforeAuthUrl() == Mage::app()->getStore()->getBaseUrl()) {
            // Set default URL to redirect customer to
            $session->setBeforeAuthUrl(Mage::helper('customer')->getDashboardUrl());
            
        } else if ($session->getBeforeAuthUrl() == Mage::helper('customer')->getLogoutUrl()) {
            $session->setBeforeAuthUrl(Mage::helper('customer')->getDashboardUrl());
        } else {
            if (!$session->getAfterAuthUrl()) {
                $session->setAfterAuthUrl($session->getBeforeAuthUrl());
            }
            if ($session->isLoggedIn()) {
                $session->setBeforeAuthUrl($session->getAfterAuthUrl(true));
            }
        }
        return $session->getBeforeAuthUrl(true);
    }

	
}