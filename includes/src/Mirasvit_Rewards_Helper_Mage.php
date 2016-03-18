<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at http://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   Reward Points + Referral program
 * @version   1.1.2
 * @build     928
 * @copyright Copyright (C) 2015 Mirasvit (http://mirasvit.com/)
 */


class Mirasvit_Rewards_Helper_Mage extends Mirasvit_MstCore_Helper_Help
{
    public function getBackendCustomerUrl($customerId)
    {
        // if (Mage::getVersion() <= '1.4.1.1') {
            return Mage::helper("adminhtml")->getUrl('adminhtml/customer/edit', array('id'=>$customerId));
        // } else {
        //     return Mage::helper("adminhtml")->getUrl('adminhtml/customer/edit', array('customer_id'=>$customerId));
        // }
    }

    public function getBackendOrderUrl($orderId)
    {
        return Mage::helper("adminhtml")->getUrl('adminhtml/sales_order/view', array('order_id'=>$orderId));
    }

    public function getOrderCollection()
    {
        $collection = Mage::getResourceModel('sales/order_grid_collection')
            ->setOrder('entity_id');
        return $collection;
    }

}