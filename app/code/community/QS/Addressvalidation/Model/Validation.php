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
class QS_Addressvalidation_Model_Validation extends Mage_Core_Model_Abstract
{
    const ADDRESSVALIDATION_ERROR_CITY = 'city';
    const ADDRESSVALIDATION_ERROR_REGION = 'region';
    const ADDRESSVALIDATION_ERROR_POSTCODE = 'postcode';
    const ADDRESSVALIDATION_ERROR_COUNTRY = 'country';
    const ADDRESSVALIDATION_ERROR_STREET = 'street';
    /**
     * 
     * 
     *
     * @param string $string
     * @return string
     */    
    protected function _capitalize($string) {
        return ucwords(strtolower($string));    
    } 
    
    protected function _isAddressEmpty ($address) {
        if($address->getStreet(1) || $address->getStreet(2) || $address->getCity() || $address->getRegionId() || $address->getPostcode()) {
            return false;
        }
        return true;
    }

    /**
     * @return QS_Addressvalidation_Helper_Data
     */
	protected function _getHelper()
	{
		return Mage::helper('addressvalidation');
	}

    /**
     * Chooses needed carrier to Validate address
     * and loads it model
     *
     * @param $carrierCode
     * @param int $storeId
     * @return boolean|QS_Addressvalidation_Model_CARRIERCODE
     */
	protected function _getValidationModelByCode($carrierCode, $storeId = null)
    {
        if (!$carrierCode) {
            return false;
        }
        $className = 'addressvalidation/'.$carrierCode;
        $obj = Mage::getModel($className);
        if ($storeId) {
            $obj->setStore($storeId);
        }
        return $obj;
    }

    /**
     * Validate address
     * Connect to carrier validation
     *
     * @param $address
     * @return Varien_Object
     */
    public function validate($address) {
		$result = new Varien_Object; 
		$errors = array();
		$notices = array();
		$normalized = false;
		$addressResult = $address->getData();
		$msg = false;
		if ($this->_getHelper()->canValidate($address) && !$this->_isAddressEmpty($address))
        {
			// We need load validation model of carrier
            if ($model = $this->_getValidationModelByCode($this->_getHelper()->getValidationCarrier()))
            {
                // Check address and get normalized
				$addressResponse = $model->getAddressNormalized($address);
				//no response from service

                if($addressResponse->hasError()) {
                    $errors[] = $addressResponse->getError();
                }
				elseif($addressResponse->getNoResponse() && !$this->_getHelper()->getNoResponseAllow()) {
					$errors[] = $this->_getHelper()->getNoResponseMessage();
				}
				
				//multiple address
				elseif($addressResponse->getMultiAddress()) {
					if($this->_getHelper()->getMultiAddressAllow()) {
						$msg = $this->_getHelper()->getNoResponseMessage();
						if( $this->_getHelper()->getNormalizeAddressAllow() ){
							$addressResult = $this->_prepareNormalizedAddress($addressResponse);
							$normalized = true;
						}
					}else{
						$errors[] = $this->_getHelper()->getMultiAddressMessage() 
										? $this->_getHelper()->getMultiAddressMessage() 
										: $addressResponse->getReturnText();						
					}
				}
				
				//no address or other error
				elseif( ($error = $addressResponse->getError()) && (!$this->_getHelper()->getNoAddressAllow()) ){
					$errors[] = $this->_getHelper()->getNoAddressMessage() ? $this->_getHelper()->getNoAddressMessage() : $error;
				} 
				
				//good address
				elseif($addressResponse->getSuccess()) {
                    if( $this->_getHelper()->getNormalizeAddressAllow() ){
						$addressResult = $this->_prepareNormalizedAddress($addressResponse);
						$normalized = true;
					}
				}
				
				//something other - bad
				else {
//					$errors[] = 'Error happend. Please contact administrator. ' . $addressResponse->getError();
				}
				
            }
        }
        if ( ! $this->_getHelper()->getAutocorrectAllow() && $normalized) {
            $compareNotices = $this->_compareNormalizedAddress($address, $addressResult);
            if (! empty($compareNotices)) {
                $notices = $compareNotices;
//                $notices[] = $compareNotices;
            }
        }
        $result->setData(
            array(
                'notices'   => $notices,
			    'errors'    => $errors,
			    'address'	=> $addressResult,
				'normalized'=> $normalized,
				'msg'       => $msg,
            )
		);
		
		mage::log('adress validate result', null, 'addressValidation.log');
		mage::log($addressResponse, null, 'addressValidation.log');		
        	mage::log($result->getData(), null, 'addressValidation.log');
		
		return $result;
    }

    protected function _prepareNormalizedAddress($normalizedAddress)
	{
			$address = array();
            if($normalizedAddress->getAddress1()){
                $address['street'] = array($this->_capitalize($normalizedAddress->getAddress1()), $this->_capitalize($normalizedAddress->getAddress2()));
            } else {
                $address['street'] = array($this->_capitalize($normalizedAddress->getAddress2()));
            }
            $address['city'] = $this->_capitalize($normalizedAddress->getCity());

			$country = Mage::getModel('directory/country')->load($normalizedAddress->getCountry());
			$region = Mage::getModel('directory/region')->loadByCode($normalizedAddress->getRegion(), $country->getIso2Code());
			$address['region'] = $this->_capitalize(($region && $region->getName())?$region->getName():$normalizedAddress->getRegion());
            $address['region_id'] = $region->getId(); // CA
			$address['country'] = $this->_capitalize(($country && $country->getIso2Code())?$country->getIso2Code():$normalizedAddress->getCountry());
			$address['country_id'] = $country->getId();
			
            if($normalizedAddress->getZip4() && $this->_getHelper()->getZip4Allow()) {
                $address['postcode'] = $normalizedAddress->getZip5() . '-' . $normalizedAddress->getZip4();
            } else {
                $address['postcode'] = $normalizedAddress->getZip5();
            }
			return $address;
    }

    /**
     * @param Varien_Object $address
     * @param array $normalizedResult
     * @return array
     */
    protected function _compareNormalizedAddress($address, $normalizedResult)
    {
        $errors = array();

        if ($address['address_id'] != '') return implode("\n", $errors);

        $street = $address->getStreet();
        if (!is_array($street)) {
            $street = array($street);
        }
        for ($i = 0; $i < count($street); $i++) {
            if ((isset($street[$i]) && isset($normalizedResult['street'][$i])) && (strtolower(trim($street[$i])) != strtolower($normalizedResult['street'][$i]))) {
                $errors[self::ADDRESSVALIDATION_ERROR_STREET] .= $this->_getHelper()->__('Street Address') .' ('. $this->_capitalize($normalizedResult['street'][$i]) .')';
            }
        }
        if (strtolower(trim($address->getCity())) != strtolower($normalizedResult['city'])) {
            $errors[self::ADDRESSVALIDATION_ERROR_CITY] = $this->_getHelper()->__('City') .' ('. $this->_capitalize($normalizedResult['city']) .')';
        }
        if ($address->getRegionId() && !empty($normalizedResult['region_id']) && (strtolower($address->getRegionId()) != (strtolower($normalizedResult['region_id'])))) {
            $errors[self::ADDRESSVALIDATION_ERROR_REGION] = $this->_getHelper()->__('State/Province') .' ('. $this->_capitalize($normalizedResult['region']) .')';
        } elseif ($address->getRegion() && !empty($normalizedResult['region']) && (strtolower($address->getRegion()) != (strtolower($normalizedResult['region'])))) {
            $errors[self::ADDRESSVALIDATION_ERROR_REGION] = $this->_getHelper()->__('State/Province') .' ('. $this->_capitalize($normalizedResult['region']) .')';
        }
        if ($address->getCountryId() != $normalizedResult['country_id']) {
            $errors[self::ADDRESSVALIDATION_ERROR_COUNTRY] = $this->_getHelper()->__('Country') .' ('. $this->_capitalize($normalizedResult['country']) .')';
        }
        if ($address->getPostcode() != $normalizedResult['postcode']) {
            $errors[self::ADDRESSVALIDATION_ERROR_POSTCODE] = $this->_getHelper()->__('Zip/Postal Code') .' ('. $this->_capitalize($normalizedResult['postcode']) .')';
        }
        
//        return implode("\n", $errors);
//        mage::log($errors,null,'addressValidation.log');
        return $errors;
    }
}
