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



class Mirasvit_Rewards_Helper_Balance_Order
{
    /**
     * Returns store ID, bounded with current order. If order is not defined, returns current store.
     *
     * @param Mage_Sales_Model_Order $order
     *
     * @return Mage_Core_Model_Store
     */
    public function getStoreByOrder($order)
    {
        return ($order) ? Mage::getModel('core/store')->load($order->getStoreId()) : Mage::helper('rewards')->getCurrentStore();
    }

    /**
     * Calculates and adds points, based on order items and subtotal.
     *
     * @param Mage_Sales_Model_Order $order
     *
     * @return int
     */
    public function earnOrderPoints($order)
    {
        if ($this->getEarnedPointsTransaction($order)) {
            return false;
        }

        $collection = Mage::getModel('rewards/purchase')->getCollection()
            ->addFieldToFilter('quote_id', $order->getQuoteId());
        if ($collection->count()) { //we have new version of data in DB
            $purchase = Mage::helper('rewards/purchase')->getByOrder($order);
            $totalPoints = $purchase->getEarnPoints();
        } else { //we need this for compability with older versions.
            $collection = Mage::getModel('sales/quote')->getCollection()
                ->addFieldToSelect('*')
                ->addFieldToFilter('entity_id', $order->getQuoteId());
            $quote = $collection->getFirstItem();
            $totalPoints = Mage::helper('rewards/balance_earn')->getPointsEarned($quote);
        }
        if ($totalPoints) {
            Mage::helper('rewards')->setCurrentStore($this->getStoreByOrder($order));
            Mage::helper('rewards/balance')->changePointsBalance($order->getCustomerId(), $totalPoints,
                Mage::helper('rewards')->____('Earned %s for the order #%s.',
                Mage::helper('rewards')->formatPoints($totalPoints), $order->getIncrementId()),
                'order_earn-'.$order->getId(), true);

            return $totalPoints;
        }
    }

    /**
     * Cancels earned points.
     *
     * @param $order
     * @param $creditMemo
     *
     * @return bool
     */
    public function cancelEarnedPoints($order, $creditMemo)
    {
        if (!$earnedTransaction = $this->getEarnedPointsTransaction($order)) {
            return false;
        }
        $proportion = $creditMemo->getSubtotal() / $order->getSubtotal();
        if ($proportion > 1) {
            $proportion = 1;
        }
        $totalPoints = round($earnedTransaction->getAmount() * $proportion);
        Mage::helper('rewards')->setCurrentStore($this->getStoreByOrder($order));
        $totalPoints = $earnedTransaction->getAmount();
        Mage::helper('rewards/balance')->changePointsBalance($order->getCustomerId(), -$totalPoints,
            Mage::helper('rewards')->____('Cancel earned %s for the order #%s.',
            Mage::helper('rewards')->formatPoints($totalPoints), $order->getIncrementId()),
            'order_earn_cancel-'.$order->getId(), false);
    }

    /**
     * Decreases the number of points on the customer account.
     *
     * @param $order
     *
     * @return bool
     */
    public function spendOrderPoints($order)
    {
        if (!$purchase = Mage::helper('rewards/purchase')->getByOrder($order)) {
            return 0;
        }
        if ($totalPoints = $purchase->getSpendPoints()) {
            Mage::helper('rewards')->setCurrentStore($this->getStoreByOrder($order));
            Mage::helper('rewards/balance')->changePointsBalance($order->getCustomerId(), -$totalPoints,
                Mage::helper('rewards')->____('Spent %s for the order #%s.',
                Mage::helper('rewards')->formatPoints($totalPoints), $order->getIncrementId()),
                'order_spend-'.$order->getId(), false);

            return $totalPoints;
        }
    }

    /**
     * @param Mage_Sales_Model_Order                 $order
     * @param Mage_Sales_Model_Order_Creditmemo|bool $creditMemo
     */
    public function restoreSpendPoints($order, $creditMemo = false)
    {
        if (!$spendTransaction = $this->getSpendPointsTransaction($order)) {
            return;
        }
        if ($creditMemo) { //if we create a credit memo
            $proportion = $creditMemo->getSubtotal() / $order->getSubtotal();
            if ($proportion > 1) {
                $proportion = 1;
            }
            $totalPoints = round($spendTransaction->getAmount() * $proportion);
        } else { //if we cancel order via backend
            $totalPoints = $spendTransaction->getAmount();
        }
        Mage::helper('rewards')->setCurrentStore($this->getStoreByOrder($order));
        $totalPoints = $spendTransaction->getAmount();
        Mage::helper('rewards/balance')->changePointsBalance($order->getCustomerId(), -$totalPoints,
            Mage::helper('rewards')->____('Restore spent %s for the order #%s.',
            Mage::helper('rewards')->formatPoints($totalPoints), $order->getIncrementId()),
            'order_spend_restore-'.$order->getId(), false);
    }

    protected function getEarnedPointsTransaction($order)
    {
        $collection = Mage::getModel('rewards/transaction')->getCollection()
            ->addFieldToFilter('code', "order_earn-{$order->getId()}")
        ;
        if ($collection->count()) {
            return $collection->getFirstItem();
        }
    }

    protected function getSpendPointsTransaction($order)
    {
        $collection = Mage::getModel('rewards/transaction')->getCollection()
            ->addFieldToFilter('code', "order_spend-{$order->getId()}")
        ;
        if ($collection->count()) {
            return $collection->getFirstItem();
        }
    }
}
