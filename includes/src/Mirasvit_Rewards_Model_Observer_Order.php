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



class Mirasvit_Rewards_Model_Observer_Order extends Mirasvit_Rewards_Model_Observer_Abstract
{
    protected function refreshPoints($quote)
    {
        if ($quote->getIsPurchaseSave()) {
            return;
        }

        if (!$purchase = Mage::helper('rewards/purchase')->getByQuote($quote)) {
            return;
        }

        if (!(Mage::getModel('customer/session')->isLoggedIn() && Mage::getModel('customer/session')->getId())) {
            $purchase->setSpendPoints(0);
        }
        $purchase->refreshPointsNumber();
        $purchase->save();

        Mage::helper('rewards/referral')->rememberReferal($quote);
    }

    /**
     * we fire this method only in  the backend.
     */
    public function quoteAfterSaveBackend($observer)
    {
        if (Mage::getSingleton('rewards/config')->getQuoteSaveFlag()) {
            return;
        }
        if (!$quote = $observer->getQuote()) {
            return;
        }
        Mage::getSingleton('rewards/config')->setQuoteSaveFlag(true);
        $this->refreshPoints($quote);
        Mage::getSingleton('rewards/config')->setQuoteSaveFlag(false);
    }

    /**
     * we fire this method only in  the frontend.
     */
    public function quoteAfterSave()
    {
        if (!$quote = Mage::getModel('checkout/cart')->getQuote()) {
            return;
        }

        $this->refreshPoints($quote);
    }

    /**
     * we fire this method only in  the frontend.
     */
    public function actionPredispatch($observer)
    {
        $uri = $observer->getControllerAction()->getRequest()->getRequestUri();
        if (strpos($uri, 'checkout') === false) {
            return;
        }
        if (!$quote = Mage::getModel('checkout/cart')->getQuote()) {
            return;
        }
        //this does not calculate quote correctly
        if (strpos($uri, '/checkout/cart/add/') !== false) {
            return;
        }

        //this does not calculate quote correctly with firecheckout
        if (strpos($uri, '/firecheckout/') !== false) {
            return;
        }

        //this does not calculate quote correctly with gomage
        if (strpos($uri, '/gomage_checkout/onepage/save/') !== false) {
            return;
        }
        $this->refreshPoints($quote);
    }

    public function orderPlaceAfter($observer)
    {
        $order = $observer->getEvent()->getOrder();
        if ($this->_isOrderPaidNow($order)) {
            if ($order->getCustomerId()) {
                ;
                Mage::helper('rewards/balance_order')->spendOrderPoints($order);
            }
        }
    }

    public function orderCancelAfter($observer)
    {
        $order = $observer->getEvent()->getOrder();
        if ($this->_isOrderPaidNow($order)) {
            if ($order->getCustomerId()) {
                Mage::helper('rewards/balance_order')->restoreSpendPoints($order);
            }
        }
    }

    public function checkoutSuccess($observer)
    {
        $session = Mage::getSingleton('checkout/type_onepage')->getCheckout();
        $orderId = $session->getLastOrderId();
        if (!$session->getLastSuccessQuoteId() || !$orderId) {
            return;
        }
        $order = Mage::getModel('sales/order')->load($orderId);
        $this->addPointsNotifications($order);
    }

    public function addPointsNotifications($order)
    {
        if (!$order->getCustomerId()) {
            return;
        }

        $quote = Mage::getModel('sales/quote')->getCollection()
                ->addFieldToFilter('entity_id', $order->getQuoteId())
                ->getFirstItem(); //we need this for correct work if we create orders via backend
        $totalEarnedPoints = Mage::helper('rewards/balance_earn')->getPointsEarned($quote);
        $purchase = Mage::helper('rewards/purchase')->getByOrder($order);
        $totalSpendPoints = $purchase->getPointsNumber();

        if ($totalEarnedPoints && $totalSpendPoints) {
            $this->addNotificationMessage(Mage::helper('rewards')->__('You earned %s and spent %s for this order.',
                Mage::helper('rewards')->formatPoints($totalEarnedPoints),
                Mage::helper('rewards')->formatPoints($totalSpendPoints)));
        } elseif ($totalSpendPoints) {
            $this->addNotificationMessage(Mage::helper('rewards')->__('You spent %s for this order.',
                Mage::helper('rewards')->formatPoints($totalSpendPoints)));
        } elseif ($totalEarnedPoints) {
            $this->addNotificationMessage(Mage::helper('rewards')->__('You earned %s for this order.',
                Mage::helper('rewards')->formatPoints($totalEarnedPoints)));
        }
        if ($totalEarnedPoints) {
            $this->addNotificationMessage(Mage::helper('rewards')->__('Earned points will be enrolled to your account after we finish processing your order.'));
        }
    }

    private function addNotificationMessage($message)
    {
        $message = Mage::getSingleton('core/message')->success($message);
        Mage::getSingleton('core/session')->addMessage($message);
    }

    protected function _isOrderPaidNow($order)
    {
        if (!Mage::registry('mst_ordercompleted_done')) {
            Mage::register('mst_ordercompleted_done', true);

            return true;
        }

        return false;
    }

    public function afterInvoiceSave($observer)
    {
        /** @var Mage_Sales_Model_Order_Invoice $invoice */
        $invoice = $observer->getEvent()->getInvoice();
        $order = $invoice->getOrder();
        if ($invoice->getState() != Mage_Sales_Model_Order_Invoice::STATE_PAID) {
            return;
        }

        if ($order && $this->getConfig()->getGeneralIsEarnAfterInvoice()) {
            $this->earnOrderPoints($order);
        }
    }

    public function afterShipmentSave($observer)
    {
        $object = $observer->getObject();
        if (!($object && ($object instanceof Mage_Sales_Model_Order_Shipment))) {
            return $this;
        }

        $order = Mage::getModel('sales/order')->load((int) $object->getOrderId());

        if ($order && $this->getConfig()->getGeneralIsEarnAfterShipment()) {
            $this->earnOrderPoints($order);
        }

        return $this;
    }

    /**
     * @param Varien_Event_Observer $observer
     */
    public function orderSaveAfter($observer)
    {
        /** @var Mage_Sales_Model_Order $order */
        if (!$order = $observer->getEvent()->getOrder()) {
            return;
        }
        $status = $order->getStatus();

        if (in_array($status, $this->getConfig()->getGeneralEarnInStatuses())) {
            $this->earnOrderPoints($order);
        }
    }

    protected function earnOrderPoints($order)
    {
        if ($order->getCustomerId()) {
            Mage::helper('rewards/balance_order')->earnOrderPoints($order);
        }
        Mage::helper('rewards/referral')->processReferralOrder($order);
    }

    /**
     * @param Varien_Event_Observer $observer
     */
    public function afterRefundSave($observer)
    {
        /** @var Mage_Sales_Model_Order_Creditmemo $creditMemo */
        if (!$creditMemo = $observer->getEvent()->getCreditmemo()) {
            return;
        }
        /** @var Mage_Sales_Model_Order $order */
        $order = Mage::getModel('sales/order')->load($creditMemo->getOrderId());
        if ($this->getConfig()->getGeneralIsCancelAfterRefund()) {
            Mage::helper('rewards/balance_order')->cancelEarnedPoints($order, $creditMemo);
        }

        if ($this->getConfig()->getGeneralIsRestoreAfterRefund()) {
            Mage::helper('rewards/balance_order')->restoreSpendPoints($order, $creditMemo);
        }
    }
}
