<?php
class Magestore_Sociallogin_PopupController extends Mage_Core_Controller_Front_Action{
	
	
	protected function _getSession()
    {
        return Mage::getSingleton('customer/session');
    }
	
    public function loginAction() { 	
		//$sessionId = session_id();
        $username = $this->getRequest()->getPost('socialogin_email', false);
        $password = $this->getRequest()->getPost('socialogin_password',  false);
        $session = Mage::getSingleton('customer/session');

        $result = array('success' => false);

        if ($username && $password) {
            try {
                $session->login($username, $password);
                $wishlist_productId=$this->getRequest()->getParam('wishlist_productId');
                if(isset($wishlist_productId) && $wishlist_productId > 0 )
                {
                    $customerId =$session->getCustomer()->getId();
                    $wishlist = Mage::getModel('wishlist/wishlist')->loadByCustomer($customerId, true);
                    $product = Mage::getModel('catalog/product')->load($wishlist_productId);
                    // $buyRequest = new Varien_Object(array()); // any possible options that are configurable and you want to save with the product
                    //$result = $wishlist->addNewItem($product, $buyRequest);
                    $result = $wishlist->addNewItem($product);
                    $wishlist->save();
                    $result['redirectToWishlist']=true;
                    $result['redirectToWishlisturl']=Mage::getUrl('wishlist');
                    $session->addSuccess(Mage::helper('wishlist')->__("%s has been moved to wishlist", $product->getName()));
                 }   
            } catch (Exception $e) {
                $result['error'] = $e->getMessage();
            }
            if (! isset($result['error'])) {
                $result['success'] = true;
            }
        } else {
            $result['error'] = $this->__(
            'Please enter a username and password.');
        }

        //session_id($sessionId);
        $this->getResponse()->setBody(Zend_Json::encode($result));
    }		
	 public function sendPassAction() { 	
		//$sessionId = session_id();
        $email = $this->getRequest()->getPost('socialogin_email_forgot', false);        
        $customer = Mage::getModel('customer/customer')
            ->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
            ->loadByEmail($email);

            if ($customer->getId()) {
                try {
                    $newPassword = $customer->generatePassword();
                    $customer->changePassword($newPassword, false);
                    $customer->sendPasswordReminderEmail();
                    Mage::getSingleton('core/session')->addNotice($this->__('If there is an account associated with ').$email.$this->__(' you will receive an email with a link to reset your password.'));
                    $result = array('success'=>true, 'message'=>"If there is an account associated with ".$email."you will receive an email with a link to reset your password.");
                }
                catch (Exception $e){
                    $result = array('success'=>false, 'error'=>"Request Time out! Please try again.");
                }
            }
            else {
                $result = array('success'=>false, 'error'=>'Your email address '.$email.' does not exist!');
            }
        
        $this->getResponse()->setBody(Zend_Json::encode($result));
    }		
	
	public function createAccAction() { 		
		$session = Mage::getSingleton('customer/session');
		if ($session->isLoggedIn()) {
           $result = array('success'=>false, 'Can Not Login!');		   
        }
		else{
			$firstName =  $this->getRequest()->getPost('firstname', false);  
			$lastName =  $this->getRequest()->getPost('lastname', false);  
			$pass =  $this->getRequest()->getPost('pass', false);  
			$passConfirm =  $this->getRequest()->getPost('passConfirm', false);  
			$email = $this->getRequest()->getPost('email', false);        
			$customer = Mage::getModel('customer/customer')
						->setFirstname($firstName)
						->setLastname($lastName)
						->setEmail($email)
						->setPassword($pass)
						->setConfirmation($passConfirm);
									
			try{
				$customer->save();
				Mage::dispatchEvent('customer_register_success',
                        array('customer' => $customer)
                    );
				 if ($customer->isConfirmationRequired()) {
					 /** @var $app Mage_Core_Model_App */
					 $app =  Mage::app();
					 /** @var $store  Mage_Core_Model_Store*/
					 $store = $app->getStore();
					 $customer->sendNewAccountEmail(
						 'confirmation',
						 $session->getBeforeAuthUrl(),
						 $store->getId()
					 );
					 $customerHelper = Mage::helper('customer');            
					 $result = array('success'=>false, 'error'=>'Account confirmation is required. Please, check your email for the confirmation link.');
					 }	
				 else {
					$result = array('success'=>true);				 
					$session->setCustomerAsLoggedIn($customer);
					}
				//$url = $this->_welcomeCustomer($customer);
               // $this->_redirectSuccess($url);
			}catch(Exception $e){
				 $result = array('success'=>false, 'error'=>$e->getMessage());
			}          
		}		        
        $this->getResponse()->setBody(Zend_Json::encode($result));
    }
	
	// copy to AccountController
	protected function _welcomeCustomer(Mage_Customer_Model_Customer $customer, $isJustConfirmed = false)
    {
        $this->_getSession()->addSuccess(
            $this->__('Thank you for registering with %s.', Mage::app()->getStore()->getFrontendName())
        );
        if ($this->_isVatValidationEnabled()) {
            // Show corresponding VAT message to customer
            $configAddressType = Mage::helper('customer/address')->getTaxCalculationAddressType();
            $userPrompt = '';
            switch ($configAddressType) {
                case Mage_Customer_Model_Address_Abstract::TYPE_SHIPPING:
                    $userPrompt = $this->__('If you are a registered VAT customer, please click <a href="%s">here</a> to enter you shipping address for proper VAT calculation', Mage::app()->getStore()->getUrl('customer/address/edit'));
                    break;
                default:
                    $userPrompt = $this->__('If you are a registered VAT customer, please click <a href="%s">here</a> to enter you billing address for proper VAT calculation', Mage::app()->getStore()->getUrl('customer/address/edit'));
            }
            $this->_getSession()->addSuccess($userPrompt);
        }

        $customer->sendNewAccountEmail(
            $isJustConfirmed ? 'confirmed' : 'registered',
            '',
            Mage::app()->getStore()->getId()
        );

        $successUrl = Mage::app()->getStore()->getUrl('customer/account/login', array('_secure'=>true));
        if ($this->_getSession()->getBeforeAuthUrl()) {
            $successUrl = $this->_getSession()->getBeforeAuthUrl(true);
        }
        return $successUrl;
    }
	
	protected function _isVatValidationEnabled($store = null)
    {
        return Mage::helper('customer/address')->isVatValidationEnabled($store);
    }
}