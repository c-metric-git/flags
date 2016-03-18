<?php
/**
 * Plumrocket Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End-user License Agreement
 * that is available through the world-wide-web at this URL:
 * http://wiki.plumrocket.net/wiki/EULA
 * If you are unable to obtain it through the world-wide-web, please 
 * send an email to support@plumrocket.com so we can send you a copy immediately.
 *
 * @package     Plumrocket_Shipping_Tracking
 * @copyright   Copyright (c) 2014 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */


class Plumrocket_ShippingTracking_Helper_Data extends Plumrocket_ShippingTracking_Helper_Main
{
	protected $_trackingInfo = array();

	public function moduleEnabled($store = null)
    {
       return (bool)Mage::getStoreConfig('shippingtracking/general/enabled', $store);
    }

	public function getShippingTrackingUrl()
	{
		return Mage::getUrl('shippingtracking');
	}


	protected function _getDecryptedConfig($key)
	{
		if (empty($key)) {
            return false;
        }
        return Mage::helper('core')->decrypt(
        	Mage::getStoreConfig('shippingtracking/'.$key));
	}

	public function getUpsTrackingInfo($number)
	{
		if (!isset($this->_trackingInfo[$number])) {

			$soap = curl_init("https://www.ups.com/ups.app/xml/Track");
			curl_setopt($soap, CURLOPT_POST, 1);
			curl_setopt($soap, CURLOPT_RETURNTRANSFER, 1);

			$request = ('<?xml version="1.0"?>
<AccessRequest xml:lang="en-US"><AccessLicenseNumber>'.$this->_getDecryptedConfig('ups_api/access_key').'</AccessLicenseNumber>
<UserId>'.$this->_getDecryptedConfig('ups_api/user_id').'</UserId><Password>'.$this->_getDecryptedConfig('ups_api/password').'</Password></AccessRequest>
<?xml version="1.0"?><TrackRequest xml:lang="en-US"><Request><TransactionReference>
<CustomerContext>QAST Track</CustomerContext><XpciVersion>1.0</XpciVersion></TransactionReference>
<RequestAction>Track</RequestAction><RequestOption>activity</RequestOption></Request>
<TrackingNumber>#'.$number.'</TrackingNumber></TrackRequest>');

			curl_setopt($soap, CURLOPT_HTTPHEADER, 
			    array('Content-Type: text/xml; charset=utf-8', 
			            'Content-Length: '.strlen($request)));

			curl_setopt($soap, CURLOPT_POSTFIELDS, $request);
			$response = curl_exec($soap);
			curl_close($soap);

			$this->_trackingInfo[$number] = @simplexml_load_string($response);

		}

		return $this->_trackingInfo[$number];
	}


	public function getFedexTrackingInfo($number)
	{
		if (!isset($this->_trackingInfo[$number])) {


			if (Mage::getStoreConfig('shippingtracking/fedex_api/sandbox')) {
				$soap = curl_init("https://gatewaybeta.fedex.com:443/xml");
			} else {
				$soap = curl_init("https://gateway.fedex.com:443/xml");
			}

			curl_setopt($soap, CURLOPT_POST, 1);
			curl_setopt($soap, CURLOPT_RETURNTRANSFER, 1);
			
	
			$request = ('
				<TrackRequest xmlns="http://fedex.com/ws/track/v3">
					<WebAuthenticationDetail>
						<UserCredential>
							<Key>'.$this->_getDecryptedConfig('fedex_api/key').'</Key>
							<Password>'.$this->_getDecryptedConfig('fedex_api/password').'</Password>
						</UserCredential>
					</WebAuthenticationDetail>
					<ClientDetail>
						<AccountNumber>'.$this->_getDecryptedConfig('fedex_api/account_number').'</AccountNumber>
						<MeterNumber>'.$this->_getDecryptedConfig('fedex_api/meter_number').'</MeterNumber>
					</ClientDetail>
					<TransactionDetail>
						<CustomerTransactionId>ActiveShipping</CustomerTransactionId>
					</TransactionDetail>
					<Version>
						<ServiceId>trck</ServiceId>
						<Major>3</Major>
						<Intermediate>0</Intermediate>
						<Minor>0</Minor>
					</Version>
					<PackageIdentifier>
						<Value>'.$number.'</Value>
						<Type>TRACKING_NUMBER_OR_DOORTAG</Type>
					</PackageIdentifier>
					<IncludeDetailedScans>1</IncludeDetailedScans>
				</TrackRequest>');


			curl_setopt($soap, CURLOPT_HTTPHEADER, 
			    array('Content-Type: text/xml; charset=utf-8', 
			            'Content-Length: '.strlen($request)));

			curl_setopt($soap, CURLOPT_POSTFIELDS, $request);
			$response = curl_exec($soap);
			curl_close($soap);

			$this->_trackingInfo[$number] = @simplexml_load_string($response);

		}

		return $this->_trackingInfo[$number];
	}


	public function getUspsTrackingInfo($number)
	{
		if (!isset($this->_trackingInfo[$number])) {

			$xml = urlencode('<TrackRequest USERID="'.urlencode($this->_getDecryptedConfig('usps_api/user_id')).'"><TrackID ID="'.$number.'"></TrackID></TrackRequest>');
			$response = file_get_contents($url = 'http://production.shippingapis.com/ShippingAPITest.dll?API=TrackV2&XML='.$xml);

			$this->_trackingInfo[$number] = @simplexml_load_string($response);

		}

		return $this->_trackingInfo[$number];
	}

}
	 