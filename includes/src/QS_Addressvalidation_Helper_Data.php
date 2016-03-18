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
class QS_Addressvalidation_Helper_Data extends Mage_Core_Helper_Abstract
{    
    const XML_PATH_VALIDATION_ENABLED 							= 'addressvalidation/default/active';
    const XML_PATH_VALIDATION_CARRIER 							= 'addressvalidation/default/carrier';
    const XML_PATH_VALIDATION_AUTOCORRECT 						= 'addressvalidation/default/autocorrect';
    const XML_PATH_VALIDATION_ZIP4 						        = 'addressvalidation/default/zip4';
    const XML_PATH_VALIDATION_ADMIN 							= 'addressvalidation/default/enabled_in_admin';
    //const XML_PATH_VALIDATION_NORMALIZE 						= 'addressvalidation/default/normalize_addresses';
	const XML_PATH_VALIDATION_NO_RESPONSE 						= 'addressvalidation/default/no_response_action';
	const XML_PATH_VALIDATION_NO_RESPONSE_MESSAGE				= 'addressvalidation/default/no_response_message';
	const XML_PATH_VALIDATION_NO_ADDRESS 						= 'addressvalidation/default/no_address_action';
	const XML_PATH_VALIDATION_NO_ADDRESS_MESSAGE				= 'addressvalidation/default/no_address_message';
	const XML_PATH_VALIDATION_MULTI_ADDRESS						= 'addressvalidation/default/multi_address_action';
    const XML_PATH_VALIDATION_MULTI_ADDRESS_MESSAGE				= 'addressvalidation/default/multi_address_message';
    const XML_PATH_VALIDATION_COUNTRY				            = 'addressvalidation/default/address_validation_country';

    public function isEnabled()
    {
		return (bool)Mage::getStoreConfig(self::XML_PATH_VALIDATION_ENABLED);
    }
	
	public function canUseCountry($address)
    {
		return in_array($address->getCountryId(), explode(',',Mage::getStoreConfig(self::XML_PATH_VALIDATION_COUNTRY)));
    }
    
    public function canValidate(Varien_Object $address)
    {
        return ((bool)Mage::getStoreConfig(self::XML_PATH_VALIDATION_ENABLED) && $this->canUseCountry($address));
    }
    
	public function getValidationCarrier()
    {
        return Mage::getStoreConfig(self::XML_PATH_VALIDATION_CARRIER);
    }

    public function getNormalizeAddressAllow()
    {
        return true; //(bool)Mage::getStoreConfig(self::XML_PATH_VALIDATION_NORMALIZE);
    }

    public function getAdminProcessAllow()
    {
        return (bool)Mage::getStoreConfig(self::XML_PATH_VALIDATION_ADMIN);
    }
	
     public function getAutocorrectAllow()
    {
        return (bool)Mage::getStoreConfig(self::XML_PATH_VALIDATION_AUTOCORRECT);
    }

    public function getZip4Allow()
    {
        return (bool)Mage::getStoreConfig(self::XML_PATH_VALIDATION_ZIP4);
    }

    public function getMultiAddressAllow()
    {
        return (bool)Mage::getStoreConfig(self::XML_PATH_VALIDATION_MULTI_ADDRESS);
    }
	
    public function getMultiAddressMessage()
    {
        return (string)Mage::getStoreConfig(self::XML_PATH_VALIDATION_MULTI_ADDRESS_MESSAGE);
    }	
	
	public function getNoAddressAllow()
	{
	    return (bool)Mage::getStoreConfig(self::XML_PATH_VALIDATION_NO_ADDRESS);
	}
	
	public function getNoAddressMessage()
	{
	    return (string)Mage::getStoreConfig(self::XML_PATH_VALIDATION_NO_ADDRESS_MESSAGE);
	}
	
    public function getNoResponseAllow()
	{
	    return (bool)Mage::getStoreConfig(self::XML_PATH_VALIDATION_NO_RESPONSE);
	}
	
	public function getNoResponseMessage()
	{
	    return (string)Mage::getStoreConfig(self::XML_PATH_VALIDATION_NO_RESPONSE_MESSAGE);
	}

}
