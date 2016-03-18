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



class Mirasvit_Rewards_Block_Adminhtml_Sales_Order_Totals extends Mage_Adminhtml_Block_Sales_Order_Totals
{
    /**
    * Initialize order totals array
    *
    * @return Mage_Sales_Block_Order_Totals
    */
    protected function _initTotals()
    {
        parent::_initTotals();
        $order = $this->getOrder();
        if (!$purchase = Mage::helper('rewards/purchase')->getByOrder($order)) {
            return $this;
        }

        $orderId = $order->getId();
        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');
        $table = $resource->getTableName('rewards/transaction');
//        $sum = (int)$readConnection->fetchOne("SELECT SUM(amount) FROM $table WHERE code='order_spend-{$orderId}'");

        $sum = $purchase->getSpendPoints();
        if ($sum) {
            $this->addTotalBefore(new Varien_Object(array(
                'code'      => 'spend',
                'value'     => $sum,
                'label'     => $this->helper('rewards')->__('%s Spent', Mage::helper('rewards')->getPointsName()),
                'is_formated' => true
            ), array('discount')));
        }

        $sumActual = (int)$readConnection->fetchOne("SELECT SUM(amount) FROM $table WHERE code='order_earn-{$orderId}'");
        $sum = $purchase->getEarnPoints();
        $pending = '';
        if ($sumActual == 0) {
            $pending = ' (pending)';
        }
        if ($sum) {
            $this->addTotalBefore(new Varien_Object(array(
                'code'      => 'earn',
                'value'     => $sum,
                'label'     => $this->helper('rewards')->__('%s Earned'.$pending, Mage::helper('rewards')->getPointsName()),
                'is_formated' => true
            ), array('discount')));
        }

        return $this;
    }

}