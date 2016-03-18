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
class QS_Addressvalidation_Model_Usps
    extends QS_Addressvalidation_Model_Abstract
    implements QS_Addressvalidation_Model_Interface {

    const XML_PATH_USPS_VALIDATION_ENABLED = 'carriers/usps/active_address_validation';

    protected $_code = 'usps';

    protected $_request = null;

    protected $_result = null;

    protected $_defaultGatewayUrl = 'http://production.shippingapis.com/ShippingAPI.dll';

    protected $_testGatewayUrl = 'http://testing.shippingapis.com/ShippingAPITest.dll';

	public function isValidationAvailable() {
        return (bool) Mage::getStoreConfig(self::XML_PATH_USPS_VALIDATION_ENABLED);
    }

    /**
	 *  Main function 
	 * @param Varien_Object $address
     * @return Varien_Object
     */
	public function getAddressNormalized($address) {
        $this->_setAddressRequest($address);
        $responseXML = $this->_getXmlNormalizing();
		$result = $this->_parseXmlNormalizingResponse($responseXML);
		
		
		$result->setCountry($address->getCountryId());
		if ($result->getUrbanization())
			$result->setRegion( $result->getRegion() . ' ' . $result->getUrbanization() );
        return $result;
    }

    /**
     *  Format Data for request to USPS field names
     *
     * @param Varien_Object $address
     * @return QS_Addressvalidation_Model_Usps
     */
    protected function _setAddressRequest($address) {
        $r = new Varien_Object();

        $userId = $this->getConfigData('userid');
        $r->setUserId($userId);
		$r->setApi('Verify');

		$data = array();
        $data['FirmName'] = substr($address->getCompany(), 0, 38);
        if ($address->getStreet(1) && $address->getStreet(2)) {
            $data['Address1'] = $address->getStreet(1);
            $data['Address2'] = $address->getStreet(2);
        } else {
			//USPS Address2 required, Address1 optional (for appartments, suits)
            $data['Address1'] = '';
			if(is_array($address->getStreet())) {
				$data['Address2'] = implode(",", $address->getStreet());
			}else{
				$data['Address2'] = $address->getStreet();
			}
        }
        $data['City'] = substr($address->getCity(), 0, 15);
		if ($address->getRegionId()) {
			$data['State'] = substr(Mage::getModel('directory/region')->load($address->getRegionId())->getCode(), 0, 2);
		}else{	
			$data['State'] = substr($address->getRegion(), 0, 2);
		}
		if ($address->getCountryId() == 'PR') {
            $data['Urbanization'] = $address->getRegion();
		}
        $data['Zip5'] = substr($address->getPostcode(), 0, 5);
        $data['Zip4'] = (strlen($address->getPostcode()) >= 9 ? substr($address->getPostcode(), -4) : '');
		
		$r->setAddressId($address->getId());
		$r->setAddress($data);

        $this->_request = $r;
		
		return $this;
    }

	/**
	 *  Perform request to USPS
	 *  Create XML request, send it, returns respnse body
	 *  $address Varien_Object
     *  @return string
     */
    protected function _getXmlNormalizing() {
        $r = $this->_request;

        $xml = new SimpleXMLElement('<?xml version = "1.0" encoding = "UTF-8"?><AddressValidateRequest/>');
        $xml->addAttribute('USERID', $r->getUserId());

        $address = $xml->addChild('Address');
		if ($r->getAddressId()) {
			$address->addAttribute('ID', $r->getAddressId());
		}
		$data = $r->getAddress();
        foreach ($data as $node => $value)
        {
            $address->addChild($node, $value);
        }

        $request = $xml->asXML();
		$url = $this->getConfigData('gateway_url');
        if (!$url) {
            $url = $this->_defaultGatewayUrl;
        }
		$api = $r->getApi();
        $debugData = array('request' => array('uri' => $url, 'api' => $api, 'xml' => $request));
        try {
            $client = new Zend_Http_Client();
            $client->setUri( $url );
            $client->setConfig(array('maxredirects' => 0, 'timeout' => 30));
            $client->setParameterGet('API', $api);
            $client->setParameterGet('XML', $request);
            $response = $client->request();
            $responseBody = $response->getBody();
            $debugData['result'] = $responseBody;
        }
        catch (Exception $e) {
            $debugData['result'] = array('error' => $e->getMessage(), 'code' => $e->getCode(), 'response' =>$responseBody);
            $responseBody = '';
        }
		
        $this->_debug($debugData);

        return $responseBody;
    }

    protected function _parseXmlNormalizingResponse($response) 
	{
        $addressData = array();
        if (strlen(trim($response)) > 0 ) {
            if (strpos(trim($response), '<?xml')===0) {
                $xml = simplexml_load_string($response);
                if (is_object($xml)) {
                    $address = $xml->Address;
                    if (isset($address->Error)) {
						//no address found or multiple with no default
                        $addressData['error'] = (string)$address->Error->Description;
                    } elseif (isset($address->ReturnText)) {
						//multiple address with default
						$addressData['multi_address'] = true;
                        $addressData['return_text']	   = (string)$address->ReturnText;
						$addressData['success'] 	   = true;
						
                        $addressData['address1'] 	   = (string)$address->Address1;
                        $addressData['address2'] 	   = (string)$address->Address2;
                        $addressData['city'] 		   = (string)$address->City;
                        $addressData['region'] 		   = (string)$address->State;
						$addressData['urbanization']   = (string)$address->Urbanization;
                        $addressData['zip5'] 		   = (string)$address->Zip5;
                        $addressData['zip4'] 		   = (string)$address->Zip4;
                    } else {
						//valid address
                        $addressData['success'] 	   = true;
						
                        $addressData['address1'] 	   = (string)$address->Address1;
                        $addressData['address2'] 	   = (string)$address->Address2;
                        $addressData['city'] 		   = (string)$address->City;
                        $addressData['region'] 		   = (string)$address->State;
						$addressData['urbanization']   = (string)$address->Urbanization;
                        $addressData['zip5'] 		   = (string)$address->Zip5;
                        $addressData['zip4'] 		   = (string)$address->Zip4;
                    }
                }else{
					//all bad XML 
					$addressData['no_response'] 	   = true;
					Mage::log("QS_Addressvalidation: Error while USPS verify address. \n" . $response);
				}
            } else{
			//all bad answers like "Your Api is not allowed to verify address" etc.
				$addressData['no_response'] 	   = true;
				Mage::log("QS_Addressvalidation: Error while USPS verify address. \n" . $response);
			}
        }
		else
		{
			$addressData['no_response'] 	   = true;
		}
		$result = new Varien_Object($addressData);
		return $result;
    }
}