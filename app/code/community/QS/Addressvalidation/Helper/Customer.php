<?php
/**
 * Created by JetBrains PhpStorm.
 * User: inknex
 * Date: 2/22/13
 * Time: 11:46 AM
 * To change this template use File | Settings | File Templates.
 */


class QS_Addressvalidation_Helper_Customer extends Mage_Customer_Helper_Address
{
    /**
     * Array of Customer Address Attributes
     *
     * @var array
	 
     */
	 
	const XML_PATH_VAT_VALIDATION_ENABLED          = 'customer/create_account/auto_group_assign';
  
    const XML_PATH_VAT_FRONTEND_VISIBILITY = 'customer/create_account/vat_frontend_visibility';
	
    protected $_attributes;

	 /**
     * Check if VAT ID address attribute has to be shown on frontend (on Customer Address management forms)
     *
     * @return boolean
     */
    public function isVatAttributeVisible()
    { 
	try{
         $bool = (bool)Mage::getStoreConfig(self::XML_PATH_VAT_FRONTEND_VISIBILITY);
         }catch (Exception $e){
	        $bool = false;
                Mage::logException($e);
            }
			return $bool;
	}
	
	
    /**
     * Return array of Customer Address Attributes
     *
     * @return array
     */

	
    public function getAttributes()
    {
        if (is_null($this->_attributes)) {
            $this->_attributes = array();
            /* @var $config Mage_Eav_Model_Config */
            $config = Mage::getSingleton('eav/config');
            foreach ($config->getEntityAttributeCodes('customer_address') as $attributeCode) {
                $this->_attributes[$attributeCode] = $config->getAttribute('customer_address', $attributeCode);
            }
        }
        return $this->_attributes;
    }

    /**
     * Get string with frontend validation classes for attribute
     *
     * @param string $attributeCode
     * @return string
     */
    public function getAttributeValidationClass($attributeCode)
    {
        /** @var $attribute Mage_Customer_Model_Attribute */
        $attribute = isset($this->_attributes[$attributeCode]) ? $this->_attributes[$attributeCode]
            : Mage::getSingleton('eav/config')->getAttribute('customer_address', $attributeCode);
        $class = $attribute ? $attribute->getFrontend()->getClass() : '';

        if (in_array($attributeCode, array('firstname', 'middlename', 'lastname', 'prefix', 'suffix', 'taxvat'))) {
            if ($class && !$attribute->getIsVisible()) {
                $class = ''; // address attribute is not visible thus its validation rules are not applied
            }

            /** @var $customerAttribute Mage_Customer_Model_Attribute */
            $customerAttribute = Mage::getSingleton('eav/config')->getAttribute('customer', $attributeCode);
            $class .= $customerAttribute && $customerAttribute->getIsVisible()
                ? $customerAttribute->getFrontend()->getClass() : '';
            $class = implode(' ', array_unique(array_filter(explode(' ', $class))));
        }

        return $class;
    }

}