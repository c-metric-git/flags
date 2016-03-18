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


class Plumrocket_ShippingTracking_Block_Shipping_Tracking_Popup extends Mage_Shipping_Block_Tracking_Popup
{
    public function getTrackingInfo()
    {
        $_results = parent::getTrackingInfo();

        //$order = Mage::getModel('sales/order')->load(Mage::registry('current_shipping_info')->getOrderId());

        if (Mage::helper('shippingtracking')->moduleEnabled()) {
            foreach($_results as $shipid => $_result) {
                foreach($_result as $key => $track) {
                    if (!is_object($track)) {
                        continue;
                    }
                    $carrier = $track->getCarrier();
                    if (Mage::getStoreConfig('shippingtracking/'.$carrier.'_api/enabled')) {

                        $_results[$shipid][$key] = Mage::getModel('shipping/tracking_result_status')->setData($track->getAllData())
                            ->setErrorMessage(null)
                            ->setUrl($this->getUrl('shippingtracking/index/'.$carrier, array(
                                'number' => $track->getTracking(),
                                //'order'  => $order->getIncrementId(), //Mage::app()->getRequest()->getParam('order'),
                            )));
                    }

                }
            }
        }

        return $_results;
    }

}
