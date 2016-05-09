<?php

class Magestore_Sociallogin_SeloginController extends Mage_Core_Controller_Front_Action{
	
	
    public function loginAction() {
		if(!Mage::helper('magenotification')->checkLicenseKeyFrontController($this)){return;}
		$se = Mage::getModel('sociallogin/selogin')->newSe(); 	
		$userId = $se->mode;
		$coreSession = Mage::getSingleton('core/session');	
		if(!$userId){
			$se_session = Mage::getModel('sociallogin/selogin')->setSeIdlogin($se);
			$url = $se_session->authUrl();
			$this->_redirectUrl($url);
		}
		 else{          
            if (!$se->validate()){
				$se_session = Mage::getModel('sociallogin/selogin')->setSeIdlogin($se);
				$url = $se_session->authUrl();
				$this->_redirectUrl($url);
			}
			else{
				$user_info = $se->getAttributes();   
                if(count($user_info)){
                    $frist_name = $user_info['namePerson/first'];
                    $last_name = $user_info['namePerson/last'];
                    $email = $user_info['contact/email'];
                    if(!$frist_name || !$last_name){
                        if($user_info['namePerson/friendly']){
                        $frist_name = $user_info['namePerson/friendly'] ; 
						$last_name = $user_info['namePerson/friendly'];
                        }
                        else{
                            $email = explode("@", $email);
                            $frist_name = $email['0'];
							$last_name  = $email['0'];
                        }                   
                    }
					//get website_id and sote_id of each stores
					$store_id = Mage::app()->getStore()->getStoreId();//add
					$website_id = Mage::app()->getStore()->getWebsiteId();//add
					
                    $data = array('firstname'=>$frist_name, 'lastname'=>$last_name, 'email'=>$user_info['contact/email']);
                    $customer = Mage::helper('sociallogin')->getCustomerByEmail($data['email'], $website_id);
                    if(!$customer || !$customer->getId()){
						//Login multisite
						$customer = Mage::helper('sociallogin')->createCustomerMultiWebsite($data, $website_id, $store_id );
						if (Mage::getStoreConfig('sociallogin/selogin/is_send_password_to_customer')){
							$customer->sendPasswordReminderEmail();
						}
						if ($customer->getConfirmation())
						{
							try {
								$customer->setConfirmation(null);
								$customer->save();
							}catch (Exception $e) {
								Mage::getSingleton('core/session')->addError(Mage::helper('sociallogin')->__('Error'));
							}
						}
                    }
                    Mage::getSingleton('customer/session')->setCustomerAsLoggedIn($customer);
                    die("<script type=\"text/javascript\">if(navigator.userAgent.match('CriOS')){window.location.href=\"".$this->_loginPostRedirect()."\";}else{try{window.opener.location.href=\"".$this->_loginPostRedirect()."\";}catch(e){window.opener.location.reload(true);} window.close();}</script>");
                }                
                else{
                   $coreSession->addError($this->__('Login failed as you have not granted access.'));
                   die("<script type=\"text/javascript\">try{window.opener.location.reload(true);}catch(e){window.opener.location.href=\"".Mage::app()->getStore()->getBaseUrl()."\"} window.close();</script>");
                }
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