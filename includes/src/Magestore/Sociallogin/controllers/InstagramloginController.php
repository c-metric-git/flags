<?php
class Magestore_Sociallogin_InstagramloginController extends Mage_Core_Controller_Front_Action{
    public function loginAction() {            
		if(!Mage::helper('magenotification')->checkLicenseKeyFrontController($this)){return;}
		$code = $_GET['code'];
		$instagram = Mage::getModel('sociallogin/instagramlogin')->newInstagram();
		if(!$code){
			$loginUrl = $instagram->getLoginUrl();
			echo "<script type='text/javascript'>top.location.href = '$loginUrl';</script>";
			exit;
		}
 		$data = $instagram->getOAuthToken($code);
		if($code && !$data->user->username){
			$loginUrl = $instagram->getLoginUrl();
			echo "<script type='text/javascript'>top.location.href = '$loginUrl';</script>";
			exit;
		}
		$token=$data->user;
		$instaframId = $token->id;			
		$customerId = $this->getCustomerId($instaframId);
		
		if($customerId){
			$customer = Mage::getModel('customer/customer')->load($customerId);
			if ($customer->getConfirmation())
			{
				try {
					$customer->setConfirmation(null);
					$customer->save();
				}catch (Exception $e) {
				}
	  		}
			Mage::getSingleton('customer/session')->setCustomerAsLoggedIn($customer);
			die("<script type=\"text/javascript\">try{window.opener.location.href=\"".$this->_loginPostRedirect()."\";}catch(e){window.opener.location.reload(true);} window.close();</script>");
			
		}else{	// redirect to login page
			$name = (string)$token->username;		
			$email = $name . '@instagram.com';
			$user['firstname'] = $name;
			$user['lastname'] = $name;			
			$user['email'] = $email;
			//get website_id and sote_id of each stores
			$store_id = Mage::app()->getStore()->getStoreId();
			$website_id = Mage::app()->getStore()->getWebsiteId();
			$customer = Mage::helper('sociallogin')->getCustomerByEmail($user['email'], $website_id);//add edtition	
			if(!$customer || !$customer->getId()){
				//Login multisite
				$customer = Mage::helper('sociallogin')->createCustomerMultiWebsite($user, $website_id, $store_id );
			}	
			if ($customer->getConfirmation())
			{
				try {
					$customer->setConfirmation(null);
					$customer->save();
				}catch (Exception $e) {
				}
	  		}	
			Mage::getSingleton('customer/session')->setCustomerAsLoggedIn($customer);							
			$this->setAuthorCustomer($instaframId, $customer->getId());	
			Mage::getSingleton('core/session')->setCustomerIdSocialLogin($instaframId);						
			if (Mage::getStoreConfig('sociallogin/mplogin/is_send_password_to_customer')){
				$customer->sendPasswordReminderEmail();
			}			
			$nextUrl = Mage::helper('sociallogin')->getEditUrl();	
			Mage::getSingleton('core/session')->addNotice('Please enter your contact detail.');			
			die("<script>window.close();window.opener.location = '$nextUrl';</script>");
		}
	}
	public function getCustomerId($instaframId){
		$user = Mage::getModel('sociallogin/customer')->getCollection()
						->addFieldToFilter('instagram_id', $instaframId)
						->getFirstItem();
		if($user)
			return $user->getCustomerId();
		else
			return NULL;
	}
	public function setAuthorCustomer($inId, $customerId){
		$mod = Mage::getModel('sociallogin/customer');
		$mod->setData('instagram_id', $inId);		
		$mod->setData('customer_id', $customerId);		
		$mod->save();		
		return ;
	}
	protected function _loginPostRedirect()
    {
        $session = Mage::getSingleton('customer/session');

        if (!$session->getBeforeAuthUrl() || $session->getBeforeAuthUrl() == Mage::getBaseUrl()) {
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