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
 * Cancellation feed model
 */

class Mage_GoogleTrustedStore_Model_Feed_Cancellation extends Mage_GoogleTrustedStore_Model_Feed_Abstract
{
    /**
     * Initializes header, adds data to feed
     *
     * @param Mage_Sales_Model_Resource_Order_Collection      $orders     canceled orders collection
     */
    public function __construct($orders)
    {
        $this->_setHeader(
            array(
                'merchant order id',
                'reason'
            )
        );
        foreach ($orders as $order) {
            $this->_addCanceledOrder($order);
        }
    }

     /**
     * Adds canceled order to feed
     *
     * @param Mage_Sales_Model_Order $order
     */
    protected function _addCanceledOrder(Mage_Sales_Model_Order $order)
    {
        if ($order->getCancellationReason()) {
            $this->_addRow(
                array(
                    $order->getIncrementId(),
                    $order->getCancellationReason()
                )
            );
        }
    }
}