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
			die("<script type=\"text/javascript\">if(navigator.userAgent.match('CriOS')){window.location.href=\"".$this->_loginPostRedirect()."\";}else{try{window.opener.location.href=\"".$this->_loginPostRedirect()."\";}catch(e){window.opener.location.reload(true);} window.close();}</script>");
			
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
                                if(Mage::getStoreConfig(('sociallogin/general/send_newemail'),Mage::app()->getStore()->getId())) $customer->sendNewAccountEmail('registered','',Mage::app()->getStore()->getId());
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
        $selecturl= Mage::getStoreConfig(('sociallogin/general/select_url'),Mage::app()->getStore()->getId());
	if($selecturl==0) return Mage::getUrl('customer/account');	
	if($selecturl==2) return Mage::getUrl();
	if($selecturl==3) return Mage::getSingleton('core/session')->getSocialCurrentpage();
	if($selecturl==4) return Mage::getStoreConfig(('sociallogin/general/custom_page'),Mage::app()->getStore()->getId());
        if($selecturl==1 && Mage::helper('checkout/cart')->getItemsCount()!=0) return Mage::getUrl('checkout/cart');
        else return Mage::getUrl();
    }
}