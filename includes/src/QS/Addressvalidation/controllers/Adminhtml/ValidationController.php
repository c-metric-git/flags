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
 * @package     Mage_BurganPayments
 * @copyright   Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class QS_Addressvalidation_Adminhtml_ValidationController extends Mage_Adminhtml_Controller_action
{

    protected function _getAdminSession()
    {
        return Mage::getSingleton('adminhtml/session');
    }
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
	
	protected function _getHelper()
	{
		return Mage::helper('addressvalidation');
	} 
	
	protected function _getMainAddressInfoMerged($addressData) 
	{
		$address = Mage::getModel('customer/address')->setData($addressData->getData())->getFormated();
		return $address;
	}
	
	public function validateadminAction()
    {  
		//$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($post));
		 
	   if(!$this->_getHelper()->getAdminProcessAllow()) {
			return ;
		}
		
		//echo '{"error":1,"message":"<ul class=\"messages\"><li class=\"error-msg\"><ul><li>HIHI<\/li><\/ul><\/li><\/ul>"}';
		
		$response = new Varien_Object();
		
        $response->setError(0);		
		
		$post = $this->getRequest()->getParams();
		
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
//                        mage::log($validationResult->getNotices(),null,'addressValidation.log');
                        $errors[] = $this->_prepareNoticesForMessageBox($validationResult->getNotices());
                        $correctAddressesArray[$id] = $validationResult->getAddress();
                        $correctAddressesArray[$id]['_second_request'] = 1; 
                        $noticesArrat[$id] = $validationResult->getNotices();
                        //$errors[] = $this->_getHelper()->__('Correct address: ') . implode(" ", $validationResult->getNotices());
                    } else {
						if($validationResult->getMsg()) {
						
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
			$response->setError(1);
			
	
            $response->setAdminCustomerAddressesArrayData($correctAddressesArray);
            $response->setNotices($noticesArrat); 

			 $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
			exit;
		}
	}



    public function massValidateAction()
    {
        $customers = Mage::getModel('customer/customer')
            ->getCollection()
            ->addAttributeToFilter('entity_id', array('in' => explode(',', $this->getRequest()->getParam('customer'))));

        foreach ($customers as $_customer) {
            $addressSuccess = 0;
            $addressError = 0;
            foreach ($_customer->getAddresses() as $_address) {
               // $addressError = 0;
               // $addressSuccess = 0;
                $addressData = new Varien_Object($_address->getData());
                $validationResult = Mage::getModel('addressvalidation/validation')->validate($addressData);
                if (!$validationResult->getErrors()) {
                    $addressData = $validationResult->getAddress();
                    $_address->setStreet(implode("\n", $addressData['street']))
                        ->setCity($addressData['city'])
                        ->setRegion($addressData['region'])
                        ->setRegionId($addressData['region_id'])
                        ->setCountry($addressData['country'])
                        ->setCountryId($addressData['country_id'])
                        ->setPostcode($addressData['postcode'])
                        ->save();
                    $addressSuccess++;
                    $_address->setValidationFlag(2)->save();
                } else {
                    $addressError++;
                    $_address->setValidationFlag(1)->save();
                }
            }
            if ($addressError) {
                $_customer->setValidationFlag(1)->save();
            } else {
                $_customer->setValidationFlag(2)->save();
            }

        }

        if ($addressSuccess) {
            Mage::getSingleton('adminhtml/session')->addSuccess('Total of ' . $addressSuccess . ' address(s) were updated.');
        }
        $this->_redirect('adminhtml/customer/index');
    }

    /**
     * Export customer grid to XML format
     */
    public function exportXmlAction()
    {
        $fileName   = 'address.xml';
        $content    = $this->getLayout()->createBlock('addressvalidation/export_customer')
            ->getExcelFile();

        $this->_prepareDownloadResponse($fileName, $content);
    }

    public function validateAction()
    {
        $pos = $this->getRequest()->getParams();

        $postData = new Varien_Object;
        $postData->setData($pos);


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
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($validationResult->getData()));
    }
}
