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


class Plumrocket_ShippingTracking_IndexController extends Mage_Core_Controller_Front_Action
{
	
	public function indexAction()
	{
		if (!Mage::helper('shippingtracking')->moduleEnabled()) {
			$this->_forward('noRoute');
			return;
		}

		$this->loadLayout();
		if ($head = $this->getLayout()->getBlock('head')) {
			$head->setTitle($this->__('Check Order Status'));
		}
		$this->_initLayoutMessages('customer/session');
		$this->renderLayout();	
	}

	public function infoAction()
	{
		if (!Mage::helper('shippingtracking')->moduleEnabled()) {
			$this->_forward('noRoute');
			return;
		}
		
		$_request 	= $this->getRequest();
		$_session 	= Mage::getSingleton('customer/session');
		
		$orderId 	= $_request->getParam('order');
		$info 		= $_request->getParam('info');


		if (empty($info) || empty($orderId)) {
			$_session->addError($this->__('Make sure that you have entered the Order Number and phone number (or email address) correctly.'));
			$this->_redirect('*/*/');
			return;
		} else {
			$order = Mage::getModel('sales/order')->load($orderId, 'increment_id');
			if ($order->getId()) {
				$bAddress = $order->getBillingAddress();
				$sAddress = $order->getShippingAddress();
				if ($bAddress->getEmail() == $info || $bAddress->getTelephone() == $info ||
					($sAddress && (
						$sAddress->getEmail() == $info || $sAddress->getTelephone() == $info
					))
				) {

					$trackingInfo = $this->_getTrackingInfoByOrder($order);
					$shippingInfoModel = new Varien_Object;
					$shippingInfoModel->setTrackingInfo($trackingInfo)->setOrderId($order->getId());
			        Mage::register('current_shipping_info', $shippingInfoModel);

			        if (count($trackingInfo) == 1) {
			        	foreach($trackingInfo as $shipid => $_result) {
			        		if (count($_result) == 1) {
				                foreach($_result as $key => $track) {
				                    
				                    $carrier = $track->getCarrier();
				                    if (Mage::getStoreConfig('shippingtracking/'.$carrier.'_api/enabled')) {
				                    	$this->_redirect('*/*/'.$carrier, array(
				                    		'number' 	=> $track->getTracking(),
				                    		//'order'		=> $order->getIncrementId(),
				                    	));
				                    	return;
				                    }

				                }
				            }
			            }
			        }

			        Mage::register('current_order', $order);
			        	
			        $this->loadLayout();
					if ($head = $this->getLayout()->getBlock('head')) {
						$head->setTitle($this->__('Order Status'));
					}
					$this->_initLayoutMessages('customer/session');
					$this->renderLayout();

					return;
				}
			}
		}

		$_session->addError($this->__('Make sure that you have entered the Order Number and phone number (or email address) correctly.'));
		$this->_redirect('*/*/index', array(
			'order' => $orderId,
			'info' 	=> $info,
		));


	}

	protected function _getTrackingInfoByOrder($order)
    {
        $shipTrack = array();
            $shipments = $order->getShipmentsCollection();
            foreach ($shipments as $shipment){
                $increment_id = $shipment->getIncrementId();
                $tracks = $shipment->getTracksCollection();

                $trackingInfos=array();
                foreach ($tracks as $track){
                    $trackingInfos[] = $track->getNumberDetail();
                }
                $shipTrack[$increment_id] = $trackingInfos;
            }

        
        return $shipTrack;
    }



	public function upsAction()
	{
		$this->_processTrackingAction('ups', $this->__('UPS Tracking Number'));
	}


	public function fedexAction()
	{
    	$this->_processTrackingAction('fedex', $this->__('FedEx Tracking Number'));
	}

	public function uspsAction()
	{
    	$this->_processTrackingAction('usps', $this->__('USPS Tracking Number'));
	}


	protected function _processTrackingAction($carrier, $pageTitle)
	{
		$rCarrier = $this->getRequest()->getParam('carrier');
		//if ($rCarrier && $carrier != $rCarrier) {
		if ($rCarrier) {
			$this->_redirect('*/*/'.strtolower($rCarrier), array('number' => $this->getRequest()->getParam('number')));
			return;
		}

		if (!Mage::helper('shippingtracking')->moduleEnabled() || !Mage::getStoreConfig('shippingtracking/'.$carrier.'_api/enabled')) {
			return $this->_forward('noRoute');
		}

		if (Mage::getSingleton('plumbase/observer')->customer() != Mage::getSingleton('plumbase/product')->currentCustomer()) {
			return $this->_forward('noRoute');
		}

		$this->loadLayout();
		if ($head = $this->getLayout()->getBlock('head')) {
			$head->setTitle($pageTitle);
		}
		$this->_initLayoutMessages('customer/session');
		$this->renderLayout();	
	}

	
}