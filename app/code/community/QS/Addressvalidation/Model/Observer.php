<?php
/**
 * Address Validation
 * USPS Address Validation
 *
 * @category   QS
 * @package    QS_Addressvalidation
 * @author     Quart-soft Magento Team <magento@quart-soft.com> 
 * @copyright  Copyright (c) 2011 Quart-Soft Ltd http://quart-soft.com
 */
class QS_Addressvalidation_Model_Observer {

    /**
     * Retrieve customer session object
     *
     * @return Mage_Customer_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('customer/session');
    }

    protected function _getCheckoutSession()
    {
        return Mage::getSingleton('checkout/session');
    }
	
    protected function _getAdminSession()
    {
        return Mage::getSingleton('adminhtml/session');
    }

	protected function _getAdminQuote()
    {
        return Mage::getSingleton('adminhtml/session_quote');
    }	
	
	protected function _getHelper()
	{
		return Mage::helper('addressvalidation');
	}

    protected function _isEnabled() 
	{
		return $this->_getHelper()->isEnabled();
	}
	
	/**
	 *  Check for availability of carrier, country
	 */
	public function canValidate($address) 
	{
		return $this->_canValidate($address);	
	}
	 
	protected function _canValidate($address) 
	{
		if (!$this->_getHelper()->canValidate($address))
		{
			return false;

		}
		if(Mage::app()->getStore()->isAdmin() && !$this->_getHelper()->getAdminProcessAllow()) {
            return false;
		}
		
		return true;	
	}
    
    /**
     * Prepare notices for Session Message Box
     * 
     *
     * @param array $notices
     * @return string
     */    
    protected function _prepareNoticesForMessageBox($notices) {
        
        $_html = "";
        $_html .= $this->_getHelper()->__('Correct address: ');
        
        foreach ($notices as $_notice) {
            if (is_Array($_notice)) {
                $_html .= implode("<br />", $_notice);    
            } else {
                $_html .= "<br />".$_notice;
            }
        }
             
        return $_html;     
    }    
	
	/*
	 * Validate address for OnePageCheckout
	 */
	protected function _validateCheckoutAddress($observer, $type)
	{
//		$this->_getCheckoutSession()->addData(array($type.'_address_validated' => false));
		$postData = new Varien_Object;
		$postData->setData($observer->getControllerAction()->getRequest()->getPost($type));
		$customerAddress = null;
		if($customerAddressId = $observer->getControllerAction()->getRequest()->getPost($type.'_address_id', false)) {
            $customerAddress = Mage::getModel('customer/address')->load($customerAddressId);
            if ($customerAddress->getId()) {
                if ($customerAddress->getCustomerId() != $this->_getCheckoutSession()->getQuote()->getCustomerId()) {
                    echo Mage::helper('core')->jsonEncode( array('error' => 1,
																'message' => $this->_helper->__('Customer Address is not valid.')
						));
					exit;
                }
                $postData->addData($customerAddress->getData());
            }
		}
		
		if($this->_canValidate($postData) && !Mage::app()->getStore()->isAdmin()){
			$validationResult = Mage::getModel('addressvalidation/validation')->validate($postData);
			if($validationResult->getErrors()){
                echo Mage::helper('core')->jsonEncode(array(
                    'error' => true,
                    'message' => implode("\n",$validationResult->getErrors())
                ));
                exit;
			} elseif ($validationResult->getNotices()) {
                echo Mage::helper('core')->jsonEncode(array(
                    'error' => true,
                    'message' => $this->_prepareNoticesForMessageBox($validationResult->getNotices())
                   // 'message' => $this->_getHelper()->__('Your correct address: ') . implode("\n",$validationResult->getNotices())
                ));
                exit;
            } else {
				if($validationResult->getMsg()) {
//					$this->_getSession()->addNotice($validationResult->getMsg());
				}
//				$this->_getCheckoutSession()->addData(array($type.'_address_validated' => true));
				if($validationResult->getNormalized()){
					$postData->addData($validationResult->getAddress());
					if($customerAddress) {
						$customerAddress->addData($validationResult->getAddress())->save();
					}
					$observer->getControllerAction()->getRequest()->setPost($type, $postData->getData());
				}
			}

		}
	}
	
	protected function _getMainAddressInfoMerged($addressData) 
	{
		$address = Mage::getModel('customer/address')->setData($addressData->getData())->getFormated();
		return $address;
	}
	
	/**
	 * When customer saves his address from frontend
	 */
	public function catchCustomerAddressSave_(Varien_Event_Observer $observer)
	{




     //   $_customer = Mage::getModel('customer/customer')->load($observer->getCustomerAddress()->getCustomerId());
     //            $_customer->setValidationFlag(1)->save();
     //   if(is_object($_customer)){
      //      mage::log('catchCustomerAddressSave');


       /* $addressSuccess = 0;
        $addressError = 0;

        foreach ($_customer->getAddresses() as $_address) {
            $addressData = new Varien_Object($_address->getData());
            $validationResult = Mage::getModel('addressvalidation/validation')->validate($addressData);
            if (!$validationResult->getErrors()) {
                $addressSuccess++;
                //$_address->setValidationFlag(1);
            } else {
                $addressError++;
               // $_address->setValidationFlag(0);
            }
        }
        if ($addressError) {
            $_customer->setValidationFlag(1);
        } else {
            $_customer->setValidationFlag(1);
        }  */
        }

	
	/**
	 * When customer creates login info with his address from frontend
	 */
	public function catchCustomerCreatePost(Varien_Event_Observer $observer)
	{
		$postData = new Varien_Object;
		$postData->setData($observer->getControllerAction()->getRequest()->getPost());
		if($postData->getCreateAddress() && $this->_canValidate($postData) && !Mage::app()->getStore()->isAdmin()){
			$validationResult = Mage::getModel('addressvalidation/validation')->validate($postData);
			if($validationResult->getErrors()){
				$this->_getSession()->setCustomerFormData($postData->getData());
				$this->_getSession()->addError(implode("\n",$validationResult->getErrors()));
				$request = Mage::app()->getRequest();
				$url = Mage::getModel('core/url')->getUrl('*/*/create');
				Mage::app()->getResponse()->setRedirect($url);
				Mage::app()->getResponse()->sendResponse();
				exit;
			} else {
				if($validationResult->getMsg()) {
					$this->_getSession()->addNotice($validationResult->getMsg());
				}
				if($validationResult->getNormalized()){
					$postData->addData($validationResult->getAddress());
					$observer->getControllerAction()->getRequest()->setPost($postData->getData());
				}
			}
		}
	}
	
	/*
	 *  Onepage checkout
	 */
    public function catchSaveBilling(Varien_Event_Observer $observer)
	{
		$this->_validateCheckoutAddress($observer, 'billing');
	}	
	
    public function catchSaveShipping(Varien_Event_Observer $observer)
	{
		$this->_validateCheckoutAddress($observer, 'shipping');
	}	
	
	/*
	 *  Onestep checkout
	 *  (it is not implemented yet. in developing)
	 */
/*
	public function catchOnestepCheckoutPaymentMethod(Varien_Event_Observer $observer)
	{
//		$this->_validateCheckoutAddress($observer, 'billing');
//		$this->_validateCheckoutAddress($observer, 'shipping');
		$response = array(
            'success' => false,
            'error'=> true,
            'message' => '111111111111111111111111111111111',
			'shipping_method' => '<script type="text/javascript">alert("nononon");</script>'
        );
		echo Zend_Json::encode($response); exit;
	}
*/

	/************************************
	 *   ADMIN SECTION
	 ************************************/
	 
	/*
	 *  Check address before customer address form submit
	 */
	public function catchAdminAdressValidate($observer) 
	{
		if(!$this->_getHelper()->getAdminProcessAllow()) {
			return ;
		}
		
		//echo '{"error":1,"message":"<ul class=\"messages\"><li class=\"error-msg\"><ul><li>HIHI<\/li><\/ul><\/li><\/ul>"}';
		
		$response = new Varien_Object();
        $response->setError(0);		
		$post = $observer->getControllerAction()->getRequest()->getPost();
		$postAddresses = $post['address'];
		$normalizedAddressesArray = array();
		$errors = array();
		foreach($postAddresses as $id => $address){
			if($id != '_template_') {
				$addressData = new Varien_Object($address);
				if(!$addressData->getData('_deleted')){
					$validationResult = Mage::getModel('addressvalidation/validation')->validate($addressData);
					                    
					if($validationResult->getErrors()){
						$errors[] = $this->_getHelper()->__('Error in the address "') ." ". $this->_getMainAddressInfoMerged($addressData) . '".<br/>' . implode(' ', $validationResult->getErrors());
					} elseif ($validationResult->getNotices()) {
                        //mage::log($validationResult->getNotices(),null,'addressValidation.log');
                        $errors[] = $this->_prepareNoticesForMessageBox($validationResult->getNotices());
                        $correctAddressesArray[$id] = $validationResult->getAddress();
                        $correctAddressesArray[$id]['_second_request'] = 1; 
                        $noticesArrat[$id] = $validationResult->getNotices();
                        //$errors[] = $this->_getHelper()->__('Correct address: ') . implode(" ", $validationResult->getNotices());
                    } else {
						if($validationResult->getMsg()) {
							$this->_getAdminSession()->addNotice($validationResult->getMsg());
						}
						if($validationResult->getNormalized()){
							$normalizedAddressesArray[$id] = $validationResult->getAddress();
						}
					}
				}
			}
		}
		$this->_getAdminSession()->setAddressValidationNormalizedData($normalizedAddressesArray);
		if (count($errors))
		{
            // Init layout Messages
			$response->setError(1);
			$this->_getAdminSession()->addError(implode("<br/>", $errors));					
			$observer->getControllerAction()->getLayout()->getMessagesBlock()->addMessages($this->_getAdminSession()->getMessages(true));
			$observer->getControllerAction()->getLayout()->getMessagesBlock()->setEscapeMessageFlag(
					$this->_getAdminSession()->getEscapeMessages(true)
				);
			$response->setMessage($observer->getControllerAction()->getLayout()->getMessagesBlock()->getGroupedHtml());
            $response->setAdminCustomerAddressesArrayData($correctAddressesArray);
            $response->setNotices($noticesArrat); 
			echo Mage::helper('core')->jsonEncode($response);
			exit;
		}
	}
	
	/*
	 *  Check address on customer save
	 */
	
	public function catchAdminCustomerSave($observer) 
	{
        if(!$this->_getHelper()->getAdminProcessAllow()) {
			return ;
		}
		
		//echo '{"error":1,"message":"<ul class=\"messages\"><li class=\"error-msg\"><ul><li>HIHI<\/li><\/ul><\/li><\/ul>"}';		
		$normalizedAddressesArray = $this->_getAdminSession()->getAddressValidationNormalizedData();

		$response = new Varien_Object();
        $response->setError(0);		
		$post = $observer->getControllerAction()->getRequest()->getPost();
		$postAddresses = $post['address'];
		if (! (bool) count($normalizedAddressesArray))
		{
			$normalizedAddressesArray = array();
			$errors = array();
			foreach($postAddresses as $id => $address){
				if($id != '_template_') {
					$addressData = new Varien_Object($address);
					if(!$addressData->getData('_deleted')){
						$validationResult = Mage::getModel('addressvalidation/validation')->validate($addressData);
						if($validationResult->getErrors()){
							$errors[] = 'Error in the address "' . $this->_getMainAddressInfoMerged($addressData) . '".<br/>' . implode(' ', $validationResult->getErrors());
                        } elseif ($validationResult->getNotices()) {
                            $errors[] = $this->_prepareNoticesForMessageBox($validationResult->getNotices());
                            //$errors[] = $this->_getHelper()->__('Correct address: ') . implode(" ", $validationResult->getNotices());
						} else {
							if($validationResult->getMsg()) {
								$this->_getAdminSession()->addNotice($validationResult->getMsg());
							}
							if($validationResult->getNormalized()){
								$normalizedAddressesArray[$id] = $validationResult->getAddress();
							}
						}
					}
				}
			}
			if (count($errors))
			{
				// Init layout Messages
				$response->setError(1);
				$this->_getAdminSession()->addError(implode("<br/>", $errors));					
				$observer->getControllerAction()->getLayout()->getMessagesBlock()->addMessages($this->_getAdminSession()->getMessages(true));
				$observer->getControllerAction()->getLayout()->getMessagesBlock()->setEscapeMessageFlag(
						$this->_getAdminSession()->getEscapeMessages(true)
					);
				$response->setMessage($observer->getControllerAction()->getLayout()->getMessagesBlock()->getGroupedHtml());
				echo Mage::helper('core')->jsonEncode($response);
				exit;
			}
		}
		foreach($normalizedAddressesArray as $id => $address)
		{
			$addressData = new Varien_Object($postAddresses[$id]);
			$addressData->addData($address);
			$postAddresses[$id] = $addressData->getData();
			$post['address'] = $postAddresses;
			$observer->getControllerAction()->getRequest()->setPost($post);
		}
	}

	/*
	 *  Check address before save order
	 */
	public function catchAdminOrderSave($observer) 
	{
        if(!$this->_getHelper()->getAdminProcessAllow()) {
			return ;
		}
		
		//echo '{"error":1,"message":"<ul class=\"messages\"><li class=\"error-msg\"><ul><li>HIHI<\/li><\/ul><\/li><\/ul>"}';
		$normalizedAddressesArray = $this->_getAdminSession()->getAddressValidationNormalizedData();

		$response = new Varien_Object();
        $response->setError(0);		
		$post = $observer->getControllerAction()->getRequest()->getPost();
		$postOrder = $post['order'];
		$postAddresses = array();
		if(isset($post['order']['billing_address'])) {
			$postAddresses['billing_address'] = $post['order']['billing_address'];
		}
		if(isset($post['order']['shipping_address'])) {
			$postAddresses['shipping_address'] = $post['order']['shipping_address'];
		}
		$normalizedAddressesArray = array();
		//Normalizing addresses 
		foreach($postAddresses as $id => $address)
		{
			$addressData = new Varien_Object($address);
			$validationResult = Mage::getModel('addressvalidation/validation')->validate($addressData);
			if($validationResult->getErrors()){
				$errors[] = 'Error in the address "' . $this->_getMainAddressInfoMerged($addressData) . '".<br/>' . implode(' ', $validationResult->getErrors());
            } elseif ($validationResult->getNotices()) {
                $errors[] = $this->_prepareNoticesForMessageBox($validationResult->getNotices());
                //$errors[] = $this->_getHelper()->__('Correct address: ') . implode(" ", $validationResult->getNotices());
			} else {
				if($validationResult->getMsg()) {
					$this->_getAdminSession()->addNotice($validationResult->getMsg());
				}
				if($validationResult->getNormalized()){
					$normalizedAddressesArray[$id] = $validationResult->getAddress();
				}
			}			
		}
		//error output
		if (count($errors))
		{
			// Init layout Messages
			$response->setError(1);
			$this->_getAdminQuote()->addError(implode('<br/>', $errors));

			$request = Mage::app()->getRequest();
			$request->setModuleName($request->getModuleName())
						->setControllerName($request->getControllerName())
						->setActionName('index')
						->setDispatched(false);
		}
		//substitute by normalized adress
		foreach($normalizedAddressesArray as $id => $address)
		{
			$addressData = new Varien_Object($postAddresses[$id]);
			$addressData->addData($address);
			$postAddresses[$id] = $addressData->getData();
			$post['order'][$id] = $postAddresses[$id];
			$observer->getControllerAction()->getRequest()->setPost($post);
		}
	}

    public function addToGrid($observer)
    {
        /* @var $block Mage_Adminhtml_Block_Customer_Grid */
        $block = $observer->getBlock();
        if (!isset($block)) return;

        if ($block->getType() == 'adminhtml/customer_grid') {

            $block->addColumnAfter('validation_flag', array(
                'header'    =>  Mage::helper('customer')->__('Address Status'),
                'width'     =>  '90',
                'index'     =>  'validation_flag',
                'filter_index' => 'validation_flag',
                'type'      =>  'options',
                'options'   =>  array(1=>"Not Validated",
                                      2=>"Validated"),
                'values'    =>  array(1=>"Not Validated",
                                      2=>"Validated")
            ), 'billing_region');


            $block->addExportType('addressvalidation/adminhtml_validation/exportXml',
                                  Mage::helper('customer')->__('Not Valid Address Excel XML'));

            $block->getMassactionBlock()->addItem('addressvalidation', array(
                'label'    => Mage::helper('customer')->__('Bulk Address Validation'),
                'url'      => $block->getUrl('addressvalidation/adminhtml_validation/massvalidate')
            ));

        }
    }

    public function addFieldToGrid($observer)
    {

        $collection = $observer->getCollection();
        if ( get_class($collection) == get_class(Mage::getResourceModel('customer/customer_collection')) )
        {
            $collection->addAttributeToSelect('validation_flag');
            $block = Mage::getBlockSingleton('adminhtml/customer_grid');
            $filter  = $block->getParam($block->getVarNameFilter(), null);
            $data = Mage::helper('adminhtml')->prepareFilterString($filter);

            if (isset($data['validation_flag'])) {
                $collection->addAttributeToFilter('validation_flag', $data['validation_flag']);
            }
        }
    }

}