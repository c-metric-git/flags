<?php
class Magestore_Sociallogin_FbloginController extends Mage_Core_Controller_Front_Action{

    public function loginAction() {        
		if(!Mage::helper('magenotification')->checkLicenseKeyFrontController($this)){return;}
		$isAuth = $this->getRequest()->getParam('auth');
		$facebook = Mage::getModel('sociallogin/fblogin')->newFacebook();
		$userId = $facebook->getUser();
		
		if($isAuth && !$userId && $this->getRequest()->getParam('error_reason') == 'user_denied'){
			echo("<script>window.close()</script>");
		}elseif ($isAuth && !$userId){
			$loginUrl = $facebook->getLoginUrl(array('scope' => 'email'));
			echo "<script type='text/javascript'>top.location.href = '$loginUrl';</script>";
			exit;
		}
 		$user = Mage::getModel('sociallogin/fblogin')->getFbUser();
 
		if ($isAuth && $user){
			$store_id = Mage::app()->getStore()->getStoreId();//add
			$website_id = Mage::app()->getStore()->getWebsiteId();//add
			$data =  array('firstname'=>$user['first_name'], 'lastname'=>$user['last_name'], 'email'=>$user['email']);
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
				die("<script type=\"text/javascript\">if(navigator.userAgent.match('CriOS')){window.location.href=\"".$this->_loginPostRedirect()."\";}else{try{window.opener.location.href=\"".$this->_loginPostRedirect()."\";}catch(e){window.opener.location.reload(true);} window.close();}</script>");   
			}else{
                                $message=$this->__('You provided a email invalid!');
				Mage::getSingleton('core/session')->addError($message);			
				die("<script type=\"text/javascript\">try{window.opener.location.reload(true);}catch(e){window.opener.location.href=\"".Mage::app()->getStore()->getBaseUrl()."\"} window.close();</script>");
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