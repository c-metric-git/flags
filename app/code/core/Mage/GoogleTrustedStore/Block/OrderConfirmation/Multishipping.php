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

class Mage_GoogleTrustedStore_Block_OrderConfirmation_Multishipping extends Mage_Core_Block_Template
{
    /**
     * Returns all order placed during multishipping ordering process
     *
     * @return array
     */
    protected function _getAllOrders()
    {
        $allOrders = array();
        $ids = Mage::getSingleton('checkout/session')->getMultishippingOrderIds(false);
        if ($ids && is_array($ids)) {
            $allOrders = Mage::getModel('sales/order')
                ->getCollection()
                ->addFieldToFilter('entity_id', array('in' => $ids));
        }

        return $allOrders;
    }

    /**
     * Render block HTML if only extension is enabled
     *
     * @return string
     */
    protected function _toHtml()
    {
        $html = '';
        if (Mage::getSingleton('googletrustedstore/config')->isEnabled(Mage::app()->getStore())) {
            foreach ($this->_getAllOrders() as $order) {
                $html .= $this->getChild('googletrustedstore.item.success')->setOrder($order)->toHtml();
                // leave only first order on the success page. May be will be changed in future, when Google starts
                // support multishipping orders.
                break;
            }
        }

        return $html;
    }

}