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


class Plumrocket_ShippingTracking_Block_Ups extends Plumrocket_ShippingTracking_Block_Abstract
{
	protected $_carrierCore = 'ups';

	public function getInfo()
	{
		return $this->helper('shippingtracking')->getUpsTrackingInfo($this->getNumber());
	}

	public function getTimeFromStr($str)
	{
		return mktime(0, 0, 0, (int)substr($str, 4, 2), (int)substr($str, 6, 2), (int)substr($str, 0, 4));
	}


	public function getLocalTime($str)
	{
		return mktime((int)substr($str, 0, 2), (int)substr($str, 2, 2), (int)substr($str, 4, 2));
	}
}