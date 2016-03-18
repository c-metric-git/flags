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
 * Adds Google provided JavaScript to admin panel order success page
 *
 */
class Mage_GoogleTrustedStore_Block_Adminhtml_OrderCreatedConfirmation
    extends Mage_GoogleTrustedStore_Block_OrderConfirmation_Onepage
{
    /**
     * Placed order
     *
     * @var Mage_Sales_Model_Order
     */
    private $_order;

    /**
     * Returns order placed
     *
     * @return Mage_Sales_Model_Order
     * @throws RuntimeException If unable to load order
     */
    protected function _getOrder()
    {
        if (!$this->_order) {
            $orderId = Mage::getSingleton('adminhtml/session')->getLastAdminOrderId();
            $order = Mage::getModel('sales/order')->load($orderId);
            Mage::getSingleton('adminhtml/session')->unsLastAdminOrderId();
            if (!$order->getId()) {
                throw new RuntimeException('Unable to load last order.');
            }
            $this->_order = $order;
        }

        return $this->_order;
    }

    /**
     * Return true if session contains ID of recently created order
     *
     * @return bool
     */
    protected function _hasOrder()
    {
        return Mage::getSingleton('adminhtml/session')->hasLastAdminOrderId();
    }

    /**
     * Returns Account ID entered in admin panel to use in template
     *
     * @return number
     */
    public function getAccountId()
    {
        $store = $this->_getOrder()->getStoreId();
        return Mage::getSingleton('googletrustedstore/config')->getAccountId($store);
    }

    /**
     * Render block HTML if only extension is enabled
     *
     * @return string
     */
    protected function _toHtml()
    {
        if ($this->_hasOrder()) {
            $store = $this->_getOrder()->getStoreId();
            if (Mage::getSingleton('googletrustedstore/config')->isEnabled($store)) {
                return parent::_toHtml();
            }
        }
    }

    /**
     * Returns Google shopping account ID
     *
     * @return string
     */
    public function getGoogleShoppingAccountId()
    {
        return Mage::getSingleton('googletrustedstore/googleShoppingAdapter')->getAccountId();
    }
}