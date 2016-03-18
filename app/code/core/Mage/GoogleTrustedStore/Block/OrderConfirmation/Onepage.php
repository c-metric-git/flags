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

class Mage_GoogleTrustedStore_Block_OrderConfirmation_Onepage extends Mage_Core_Block_Template
{
    /**
     * Constant for "yes" value in Google provided format
     *
     * @var string
     */
    const YES = 'Y';

    /**
     * Constant for "no" value in Google provided format
     *
     * @var string
     */
    const NO = 'N';

    /**
     * Placed order
     *
     * @var Mage_Sales_Model_Order
     */
    private $_order;

    /**
     * Placed order items
     *
     * @var array
     */
    private $_orderItems = null;

    /**
     * Returns order placed
     *
     * @return Mage_Sales_Model_Order
     * @throws RuntimeException If unable to load order
     */
    protected function _getOrder()
    {
        if (!$this->_order) {
            $orderId = Mage::getSingleton('checkout/session')->getLastOrderId();
            $order = Mage::getModel('sales/order')->load($orderId);
            if (!$order->getId()) {
                throw new RuntimeException('Unable to load last order.');
            }
            $this->_order = $order;
        }

        return $this->_order;
    }

    /**
     * Sets order
     *
     * @param Mage_Sales_Model_Order $order
     * @return Mage_GoogleTrustedStore_Block_OrderConfirmation_Onepage
     */
    public function setOrder($order)
    {
        $this->_order = $order;
        return $this;
    }

    /**
     * Returns order items placed
     *
     * @return array
     */
    protected function _getItems()
    {
        if (!$this->_orderItems) {
            $this->_orderItems = $this->_getOrder()->getAllVisibleItems();
        }
        return $this->_orderItems;
    }

    /**
     * Formats price into Google specified format (2 digits after dot)
     * It's expected that Magento manages rounding prices before order saving, so this method shouldn't care
     * about third and fourth digits after point
     *
     * @param float $price
     * @return string
     */
    protected function _formatPrice($price)
    {
        return sprintf("%01.2F", $price);
    }

    /**
     * Returns order increment id
     *
     * @return string
     */
    public function getOrderIncrementId()
    {
        return $this->_getOrder()->getIncrementId();
    }

    /**
     * Returns domain where order was placed
     *
     * @return string
     */
    public function getOrderDomain()
    {
        return Mage::getModel('core/url')->parseUrl(Mage::getBaseUrl())->getHost();
    }

    /**
     * Returns order's customer email
     *
     * @return string
     */
    public function getCustomerEmail()
    {
        return $this->_getOrder()->getCustomerEmail();
    }

    /**
     * Returns order's customer country
     *
     * @return string
     */
    public function getCustomerCountry()
    {
        if ($address = $this->_getOrder()->getShippingAddress()) {
            return $address->getCountry();
        }
        return $this->_getOrder()->getBillingAddress()->getCountry();
    }

    /**
     * Returns order's currency code
     *
     * @return string
     */
    public function getCurrencyCode()
    {
        return $this->_getOrder()->getOrderCurrencyCode();
    }

    /**
     * Returns order total
     *
     * @return string
     */
    public function getOrderTotal()
    {
        return $this->_formatPrice($this->_getOrder()->getGrandTotal());
    }

    /**
     * Returns order discounts total
     *
     * @return string
     */
    public function getOrderDiscount()
    {
        return $this->_formatPrice($this->_getOrder()->getDiscountAmount());
    }

    /**
     * Returns order shipping total
     *
     * @return string
     */
    public function getOrderShipping()
    {
        return $this->_formatPrice($this->_getOrder()->getShippingAmount());
    }

    /**
     * Returns order tax total
     *
     * @return string
     */
    public function getOrderTax()
    {
        return $this->_formatPrice($this->_getOrder()->getTaxAmount());
    }

    /**
     * Returns order ship date
     *
     * @returns string in format YYYY-MM-DD
     */
    public function getOrderShipDate()
    {
        $order = $this->_getOrder();
        return $this->_getOrder()
            ->getCreatedAtDate()
            ->addDay(
                Mage::getSingleton('googletrustedstore/config')->getEstimatedShippingPeriod($order->getStoreId())
            )
            ->toString('yyyy-MM-dd');
    }

    /**
     * Checks if order contains backordered items
     *
     * @returns string ('Y' or 'N')
     */
    public function hasBackorderPreorder()
    {
        foreach ($this->_getItems() as $item) {
            if($item->getQtyBackordered() > 0) {
                return self::YES;
            }
        }
        return self::NO;
    }

    /**
     * Checks if order contains virtual items
     *
     * @returns string ('Y' or 'N')
     */
    public function hasDigitalGoods()
    {
        foreach ($this->_getItems() as $item) {
            if($item->getIsVirtual()) {
                return self::YES;
            }
        }
        return self::NO;
    }

    /**
     * Prepares array with information about each order item - name, price, quantity
     *
     * @return array
     */
    public function getItemsInformation()
    {
        $items = array();
        foreach ($this->_getItems() as $item) {
            $itemInfo = array(
                'name'  => $this->escapeHtml($item->getName()),
                'price' => $this->_formatPrice($item->getPrice()),
                'qty'   => sprintf($item->getIsQtyDecimal() ? '%F' : '%d', $item->getQtyOrdered()),
            );
            if ($gsInfo = $this->_getGoogleShoppingItemInfo($item)) {
                $itemInfo['gs'] = $gsInfo;
            }
            $items[] = $itemInfo;
        }

        return $items;
    }

    /**
     * Tries to load Google shopping info related to order item
     *
     * @param Mage_Sales_Model_Order_Item $item
     * @return array|null Null if cannot load; array if can
     */
    protected function _getGoogleShoppingItemInfo(Mage_Sales_Model_Order_Item $item)
    {
        $helper = Mage::getSingleton('googletrustedstore/googleShoppingAdapter');
        if ($helper->isActive()) {
            $product = Mage::getModel('catalog/product')->load($item->getProductId());
            if ($product->getId()) {
                $storeId = $item->getStoreId();
                $gsItemId = $helper->getItemId($product, $storeId);
                if ($gsItemId) {
                    return array(
                        'id'         => $this->escapeHtml($gsItemId),
                        'account_id' => $this->escapeHtml($helper->getAccountId($storeId)),
                        'country'    => $helper->getTargetCountry($storeId),
                        'language'   => $helper->getTargetLanguage($storeId),
                    );
                }
            }
        }
    }

    /**
     * Render block HTML if only extension is enabled
     *
     * @return string|null
     */
    protected function _toHtml()
    {
        if (Mage::getSingleton('googletrustedstore/config')->isEnabled(Mage::app()->getStore())) {
            return parent::_toHtml();
        }
    }
}