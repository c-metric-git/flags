<?php
class Magestore_Sociallogin_CalloginController extends Mage_Core_Controller_Front_Action{
	
	/**
	* getToken and call profile user Clavid
	**/
    public function loginAction($name_blog) {
		if(!Mage::helper('magenotification')->checkLicenseKeyFrontController($this)){return;}
		$cal = Mage::getModel('sociallogin/callogin')->newCal();       
		$userId = $cal->mode;
		if(!$userId){
            $cal_session = Mage::getModel('sociallogin/callogin')->setcalIdlogin($aol, $name_blog);
            $url = $cal_session->authUrl();
			echo "<script type='text/javascript'>top.location.href = '$url';</script>";
			exit;
		}
        else{                        
            if (!$cal->validate()){                
              $message=$this->__('Login failed as you have not granted access.');
               Mage::getSingleton('core/session')->addError($message);			
               die("<script type=\"text/javascript\">try{window.opener.location.reload(true);}catch(e){window.opener.location.href=\"".Mage::app()->getStore()->getBaseUrl()."\"} window.close();</script>");
            }
            else{                
                $user_info = $cal->getAttributes();                 
                if(count($user_info)){
                    $frist_name = $user_info['namePerson/first'];
                    $last_name = $user_info['namePerson/last'];
                    $email = $user_info['contact/email'];
                    if(!$frist_name){
                        if($user_info['namePerson/friendly']){
                        $frist_name = $user_info['namePerson/friendly'] ;   
                        }
                        else{
                            $email = explode("@", $email);
                            $frist_name = $email['0'];
                        }                   
                    }

                    if(!$last_name){
                        $last_name = '_cal';
                    }
					
					//get website_id and sote_id of each stores
					$store_id = Mage::app()->getStore()->getStoreId();//add
					$website_id = Mage::app()->getStore()->getWebsiteId();//add
					
                    $data = array('firstname'=>$frist_name, 'lastname'=>$last_name, 'email'=>$user_info['contact/email']);
                    $customer = Mage::helper('sociallogin')->getCustomerByEmail($data['email'],$website_id );//add edition
                    if(!$customer || !$customer->getId()){
						//Login multisite
						$customer = Mage::helper('sociallogin')->createCustomerMultiWebsite($data, $website_id, $store_id );
                                                if(Mage::getStoreConfig(('sociallogin/general/send_newemail'),Mage::app()->getStore()->getId())) $customer->sendNewAccountEmail('registered','',Mage::app()->getStore()->getId());
						if (Mage::getStoreConfig('sociallogin/callogin/is_send_password_to_customer')){
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
                   Mage::getSingleton('core/session')->addError($message);                   			
                   die("<script type=\"text/javascript\">try{window.opener.location.reload(true);}catch(e){window.opener.location.href=\"".Mage::app()->getStore()->getBaseUrl()."\"} window.close();</script>");
                }
            }           
        }
    }
    
    public function setBlockAction()
    {             
        	$this->loadLayout();
		$this->renderLayout();
    }
    
    public function setClaivdNameAction(){
        $data = $this->getRequest()->getPost();
        if($data){
            $name = $data['name'];
            $url = Mage::getModel('sociallogin/callogin')->getCalLoginUrl($name);
            $this->_redirectUrl($url);
        }
        else{
            $message=$this->__('Please enter Blog name!');
            Mage::getSingleton('core/session')->addError($message);
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