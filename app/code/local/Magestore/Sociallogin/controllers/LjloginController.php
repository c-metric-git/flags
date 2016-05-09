<?php
class Magestore_Sociallogin_LjloginController extends Mage_Core_Controller_Front_Action{
   
   public function loginAction() {     
		$identity = $this->getRequest()->getPost('identity');
		Mage::getSingleton('core/session')->setData('identity',$identity);
		$my = Mage::getModel('sociallogin/ljlogin')->newMy();		
		Mage::getSingleton('core/session')->setData('identity',$identity);
		$userId = $my->mode;       	
		$coreSession = Mage::getSingleton('core/session');
		if(!$userId){
            $my = Mage::getModel('sociallogin/ljlogin')->setLjIdlogin($my,$identity);
			try{
				$url = $my->authUrl();
			}catch(Exception $e){
                                $message=$this->__('Username not exacted');
				$coreSession->addError($message);			
                die("<script type=\"text/javascript\">try{window.opener.location.reload(true);}catch(e){window.opener.location.href=\"".Mage::app()->getStore()->getBaseUrl()."\"} window.close();</script>");
			}
			echo "<script type='text/javascript'>top.location.href = '$url';</script>";
			exit;
		}
        else{                        
            if (!$my->validate()){ 
               $my_session = Mage::getModel('sociallogin/ljlogin')->setLjIdlogin($my,$identity);
                try{
				$url = $my->authUrl();
			}catch(Exception $e){
				$message=$this->__('Username not exacted');
				$coreSession->addError($message);			
                die("<script type=\"text/javascript\">try{window.opener.location.reload(true);}catch(e){window.opener.location.href=\"".Mage::app()->getStore()->getBaseUrl()."\"} window.close();</script>");
			}
                echo "<script type='text/javascript'>top.location.href = '$url';</script>";
                exit;
            }
            else{       			
               // $user_info = $my->getAttributes();
				$user_info = $my->data;
                if(count($user_info)){
					$user = array();
					$identity = $user_info['openid_identity'];
					$length = strlen($identity);
					$httpLen = strlen("http://");
					$userAccount = substr($identity,$httpLen,$length-1-$httpLen);
					$userArray = explode( '.', $userAccount,2);
					$firstname = $userArray[0];
					$lastname ="";
					$email = $firtname."@".$userArray[1];
					$user['firstname'] = $firstname;
					$user['lastname'] = $lastname;
					$user['email'] = $email;
					$authorId = $email;
					//get website_id and sote_id of each stores
					$store_id = Mage::app()->getStore()->getStoreId();//add
					$website_id = Mage::app()->getStore()->getWebsiteId();//add	
					$customer = Mage::helper('sociallogin')->getCustomerByEmail($user['email'], $website_id);//add edtition
					if(!$customer || !$customer->getId()){
						//Login multisite
						$customer = Mage::helper('sociallogin')->createCustomerMultiWebsite($user, $website_id, $store_id );
                                                if(Mage::getStoreConfig(('sociallogin/general/send_newemail'),Mage::app()->getStore()->getId())) $customer->sendNewAccountEmail('registered','',Mage::app()->getStore()->getId());
					}
					Mage::getModel('sociallogin/authorlogin')->addCustomer($authorId);
 					if (Mage::getStoreConfig('sociallogin/ljlogin/is_send_password_to_customer')){
						$customer->sendPasswordReminderEmail();
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
					$nextUrl = Mage::helper('sociallogin')->getEditUrl();						
					die("<script type=\"text/javascript\">if(navigator.userAgent.match('CriOS')){window.location.href=\"".$this->_loginPostRedirect()."\";}else{try{window.opener.location.href=\"".$this->_loginPostRedirect()."\";}catch(e){window.opener.location.reload(true);} window.close();}</script>");
					
                }                
                else{
                    $message=$this->__('User has not shared information so login fail!');
                   $coreSession->addError($message);			
                   die("<script type=\"text/javascript\">try{window.opener.location.reload(true);}catch(e){window.opener.location.href=\"".Mage::app()->getStore()->getBaseUrl()."\"} window.close();</script>");
                }
            }           
        }
    }
	
	/**
	* return template au_wp.phtml
	**/
    public function setBlockAction()
    {  
        $this->loadLayout();
	$this->renderLayout();
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