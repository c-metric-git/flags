<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Customer
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Customer address controller
 *
 * @category   Mage
 * @package    Mage_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
require_once 'Mage/Customer/controllers/AddressController.php';
class QS_Addressvalidation_AddressController extends Mage_Customer_AddressController
{
	/*---------------------------*/
	public function validateAction()
    {  
	    $pos = $this->getRequest()->getParams();
	    
	    $postData = new Varien_Object;
		$postData->setData($pos);
	  
       //mage::log('controller post data');
	   //mage::log($pos);
	   
	   if(Mage::getModel('addressvalidation/observer')->canValidate($postData) && !Mage::app()->getStore()->isAdmin()){}
	   
	   $validationResult = Mage::getModel('addressvalidation/validation')->validate($postData);
	   
	   if($validationResult->getErrors() || $validationResult->getNotices()){
	   
	        	if ($validationResult->getNormalized()) {
                   //$this->_getSession()->setAddressFormData($validationResult->getAddress());
                } else {
				   //$this->_getSession()->setAddressFormData($postData->getData());
                }
				
                if ($validationResult->getErrors()) {
                   
                }
				
                if ($validationResult->getNotices()) {

                }
	   }
	   else
	   {
	   
	   }
	   mage::log($validationResult->getData());
       $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($validationResult->getData()));
    }
	/*---------------------------*/
	

    public function formPostAction()
    {
        mage::log('my controller');
        if (!$this->_validateFormKey()) {
            return $this->_redirect('*/*/');
        }
        // Save data
        if ($this->getRequest()->isPost()) {
            $customer = $this->_getSession()->getCustomer();
            /* @var $address Mage_Customer_Model_Address */
            $address  = Mage::getModel('customer/address');
            $addressId = $this->getRequest()->getParam('id');
            if ($addressId) {
                $existsAddress = $customer->getAddressById($addressId);
                if ($existsAddress->getId() && $existsAddress->getCustomerId() == $customer->getId()) {
                    $address->setId($existsAddress->getId());
                }
            }

            $errors = array();

            /* @var $addressForm Mage_Customer_Model_Form */
            $addressForm = Mage::getModel('customer/form');
            $addressForm->setFormCode('customer_address_edit')
                ->setEntity($address);
            $addressData    = $addressForm->extractData($this->getRequest());
            $addressErrors  = $addressForm->validateData($addressData);
            if ($addressErrors !== true) {
                $errors = $addressErrors;
            }

            try {
                $addressForm->compactData($addressData);
                $address->setCustomerId($customer->getId())
                    ->setIsDefaultBilling($this->getRequest()->getParam('default_billing', false))
                    ->setIsDefaultShipping($this->getRequest()->getParam('default_shipping', false));

                $addressErrors = $address->validate();
                if ($addressErrors !== true) {
                    $errors = array_merge($errors, $addressErrors);
                }

                if (count($errors) === 0) {
                    $address->save();
                    $this->_getSession()->addSuccess($this->__('The address has been saved.'));

                    $addressSuccess = 0;
                    $addressError = 0;

                    foreach ($customer->getAddresses() as $_address) {
                        $addressData = new Varien_Object($_address->getData());
                        $validationResult = Mage::getModel('addressvalidation/validation')->validate($addressData);

                        if ((!$validationResult->getErrors())&&(!$validationResult->getNotices())) {
                            $addressSuccess++;
                            $_address->setValidationFlag(2)->save();
                        } else {
                            $addressError++;
                             $_address->setValidationFlag(1)->save();
                        }
                    }
                    if ($addressError) {
                        $customer->setValidationFlag(1)->save();
                    } else {
                        $customer->setValidationFlag(2)->save();
                    }
                    $this->_redirectSuccess(Mage::getUrl('*/*/index', array('_secure'=>true)));
                    return;
                } else {
                    $this->_getSession()->setAddressFormData($this->getRequest()->getPost());
                    foreach ($errors as $errorMessage) {
                        $this->_getSession()->addError($errorMessage);
                    }
                }
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->setAddressFormData($this->getRequest()->getPost())
                    ->addException($e, $e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->setAddressFormData($this->getRequest()->getPost())
                    ->addException($e, $this->__('Cannot save address.'));
            }
        }
        return $this->_redirectError(Mage::getUrl('*/*/edit', array('id' => $address->getId())));
    }


}
