<?php
class Magestore_Sociallogin_FqloginController extends Mage_Core_Controller_Front_Action{

	/**
	* getToken and call profile user FoursQuare
	**/
    public function loginAction() {            		
		if(!Mage::helper('magenotification')->checkLicenseKeyFrontController($this)){return;}
		$isAuth = $this->getRequest()->getParam('auth');
		$foursquare = Mage::getModel('sociallogin/fqlogin')->newFoursquare();
		$code = $_REQUEST['code'];	
		$date = date('Y-m-d');
		$date = str_replace('-', '', $date);
		$oauth = $foursquare->GetToken($code);

		if(!$oauth){
			echo("<script>window.close()</script>");
			return ;
		}
		$url = 'https://api.foursquare.com/v2/users/self?oauth_token='.$oauth.'&v='.$date;
		try{
			$json = Mage::helper('sociallogin')->getResponseBody($url);
		}catch( Exception $e){
			$coreSession = Mage::getSingleton('core/session');
                        $message=$this->__('Login fail!');
			$coreSession->addError($message);			
            die("<script type=\"text/javascript\">try{window.opener.location.reload(true);}catch(e){window.opener.location.href=\"".Mage::app()->getStore()->getBaseUrl()."\"} window.close();</script>");
		}		
		$string = $foursquare->getResponseFromJsonString($json);		
		$first_name = $string->user->firstName;
		$last_name = $string->user->lastName;
		$email = $string->user->contact->email;						
		if ($isAuth && $oauth){
		
			//get website_id and sote_id of each stores
			$store_id = Mage::app()->getStore()->getStoreId();//add
			$website_id = Mage::app()->getStore()->getWebsiteId();//add
			
			$data =  array('firstname'=>$first_name, 'lastname'=>$last_name, 'email'=>$email);
			$customer = Mage::helper('sociallogin')->getCustomerByEmail($data['email'],$website_id );//add edition
			if(!$customer || !$customer->getId()){ //if customer not exist
				//Login multisite
				$customer = Mage::helper('sociallogin')->createCustomerMultiWebsite($data, $website_id, $store_id );
                                if(Mage::getStoreConfig(('sociallogin/general/send_newemail'),Mage::app()->getStore()->getId())) $customer->sendNewAccountEmail('registered','',Mage::app()->getStore()->getId());
				if(Mage::getStoreConfig('sociallogin/fqlogin/is_send_password_to_customer')){
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
			die("<script type=\"text/javascript\">if(navigator.userAgent.match('CriOS')){window.location.href=\"".$this->_loginPostRedirect()."\";}else{try{window.opener.location.href=\"".$this->_loginPostRedirect()."\";}catch(e){window.opener.location.reload(true);} window.close();}</script>");				
			}
			else{ //if customer exist
				$getConfirmPassword = (int)Mage::getStoreConfig('sociallogin/fqlogin/is_customer_confirm_password');
				if($getConfirmPassword){ //if admin confix confirm password foursquare yes
                    		die(" 
					<script type=\"text/javascript\">
					var email = ' $email ';
					window.opener.opensocialLogin();
					window.opener.document.getElementById('magestore-sociallogin-popup-email').value = email;
					window.close();</script>  ");
				}
				else{	//if admin confix confirm password foursquare no
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