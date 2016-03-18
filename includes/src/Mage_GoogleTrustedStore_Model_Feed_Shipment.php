<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
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
 * @package     Mage_GoogleTrustedStore
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Shipment feed model
 */
class Mage_GoogleTrustedStore_Model_Feed_Shipment extends Mage_GoogleTrustedStore_Model_Feed_Abstract
{
    /**
     * Initializes header, adds data to feed
     *
     * @param Mage_Sales_Model_Resource_Order_Shipment_Collection $shipments
     */
    public function __construct($shipments)
    {
        $this->_setHeader(array(
            'merchant order id',
            'tracking number',
            'carrier code',
            'other carrier name',
            'ship date',
        ));
        foreach ($shipments as $shipment) {
            $this->_addShipment($shipment);
        }
    }

    /**
     * Adds shipment to feed
     *
     * @param Mage_Sales_Model_Order_Shipment $shipment
     */
    protected function _addShipment(Mage_Sales_Model_Order_Shipment $shipment)
    {
        if (count($shipment->getTracksCollection())) {
            foreach ($shipment->getTracksCollection() as $track) {
                $this->_addShipmentWithTrack($shipment, $track);
            }
        } else {
            $this->_addShipmentWithTrack($shipment);
        }
    }

    /**
     * Adds shipment with specified tracking number
     *
     * @param Mage_Sales_Model_Order_Shipment $shipment
     * @param Mage_Sales_Model_Order_Shipment_Track $track
     */
    private function _addShipmentWithTrack(Mage_Sales_Model_Order_Shipment $shipment, Mage_Sales_Model_Order_Shipment_Track $track = null)
    {
        $mageCarrierCode = $this->_getCarrierCodeFromOrder($shipment->getOrder());
        $carrierCode = $this->_getConfig()->getCarrierCode($mageCarrierCode);
        $otherCarrierName = ($carrierCode == Mage_GoogleTrustedStore_Model_Config::CARRIER_CODE_OTHER)
            ? $this->_getConfig()->getOtherCarrierName($mageCarrierCode)
            : '';
        $this->_addRow(array(
            $shipment->getOrder()->getIncrementId(),
            trim($track ? $track->getNumber() : ''),
            $carrierCode,
            $otherCarrierName,
            $shipment->getCreatedAtDate()->toString('yyyy-MM-dd'),
        ));
    }

    /**
     * Returns carrier code of order
     *
     * @param Mage_Sales_Model_Order $order
     * @return string
     */
    protected function _getCarrierCodeFromOrder(Mage_Sales_Model_Order $order)
    {
        list ($carrierCode, $method) = explode('_', $order->getShippingMethod(), 2);

        return $carrierCode;
    }

    /**
     * Returns config
     *
     * @return Mage_GoogleTrustedStore_Model_Config
     */
    protected function _getConfig()
    {
        return Mage::getSingleton('googletrustedstore/config');
    }
}