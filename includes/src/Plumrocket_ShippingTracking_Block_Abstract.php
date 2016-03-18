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


abstract class Plumrocket_ShippingTracking_Block_Abstract extends Mage_Core_Block_Template
{   
	protected $_carrierCore = 'none';

	protected $_number = null;
	protected $_shipmentTrack = null;
	protected $_order = null;
	
	/*
	public function getCarrier()
	{
		return $this->_carrierCore;
	}

	public function setCarrier($carrier)
	{
		$this->_carrierCore = true;
		return $this;
	}
	*/

	public function getNumber()
	{
		if (is_null($this->_number)) {
			$this->_number = Mage::app()->getRequest()->getParam('number');
		}
		return $this->_number;
	}

	public function getShipmentTrack()
	{
		if (is_null($this->_shipmentTrack)) {
			$this->_shipmentTrack = Mage::getModel('sales/order_shipment_track');

			if ($tackNumber = $this->getNumber()) {
				$this->_shipmentTrack = $this->_shipmentTrack->getCollection()
					->addFieldToFilter('track_number', $tackNumber)
					->addFieldToFilter('carrier_code', $this->_carrierCore)
					->setPageSize(1)
					->getFirstItem();
			}
		}

		return $this->_shipmentTrack;
	}

	public function getOrder()
	{
		if (is_null($this->_order)) {
			$this->_order = Mage::getModel('sales/order');
			if ($orderId = $this->getShipmentTrack()->getOrderId()) {
				$this->_order->load($orderId);
			}
		}
		return $this->_order;
	}

}