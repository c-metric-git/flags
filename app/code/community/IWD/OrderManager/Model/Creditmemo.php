<?php
class IWD_OrderManager_Model_Creditmemo extends Mage_Sales_Model_Order_Creditmemo
{
    const XML_PATH_SALES_ALLOW_DEL_CREDITMEMOS = 'iwd_ordermanager/iwd_delete_creditmemos/allow_del_creditmemos';
    const XML_PATH_SALES_STATUS_CREDITMEMO = 'iwd_ordermanager/iwd_delete_creditmemos/creditmemo_status';

    public function isAllowDeleteCreditmemos()
    {
        $conf_allow = Mage::getStoreConfig(self::XML_PATH_SALES_ALLOW_DEL_CREDITMEMOS, Mage::app()->getStore());
        $permission_allow = Mage::getSingleton('admin/session')->isAllowed('iwd_ordermanager/creditmemo/actions/delete');
        $engine = Mage::helper('iwd_ordermanager')->CheckCreditmemoTableEngine();
        return ($conf_allow && $permission_allow && $engine);
    }

    public function isAllowCreateCreditmemo()
    {
        return true;
    }

    public function getCreditmemoStatusesForDeleteIds()
    {
        return explode(',', Mage::getStoreConfig(self::XML_PATH_SALES_STATUS_CREDITMEMO));
    }

    public function checkCreditmemoStatusForDeleting()
    {
        return (in_array($this->getState(), $this->getCreditmemoStatusesForDeleteIds()));
    }

    public function canDelete()
    {
        return ($this->isAllowDeleteCreditmemos() && $this->checkCreditmemoStatusForDeleting());
    }

    public function deleteCreditmemo()
    {
        if (!$this->canDelete()) {
            $message = 'Maybe, you can not delete items with some statuses. Please, check <a href="'
                . Mage::helper("adminhtml")->getUrl("adminhtml/system_config/edit", array("section" => "iwd_ordermanager"))
                . '" target="_blank" title="System - Configuration - IWD Extensions - Order Manager">configuration</a> of IWD OrderManager';

            Mage::getSingleton('iwd_ordermanager/logger')->addNoticeMessage('check_credit_memo_status', $message);
            Mage::getSingleton('iwd_ordermanager/logger')->itemDeleteError('creditmemo', $this->getIncrementId());
            return false;
        }

        Mage::dispatchEvent('iwd_ordermanager_sales_creditmemo_delete_after', array('creditmemo' => $this, 'creditmemo_items' => $this->getItemsCollection()));

        if ($this->getState() != Mage_Sales_Model_Order::STATE_CANCELED){
            $this->cancel()->save()->getOrder()->save();
        }

        $order_id = $this->getOrderId();
        $order = Mage::getModel('sales/order')->load($order_id);

        Mage::getSingleton('iwd_ordermanager/report')->addRefundedPeriod($this->getCreatedAt(), $this->getUpdatedAt(), $order->getCreatedAt());

        $creditmemo_items = $this->getItemsCollection();
        foreach ($creditmemo_items as $creditmemo_item) {
            $creditmemo_item->getProductId();
            $order_items = Mage::getResourceModel('sales/order_item_collection')
                ->addFieldToFilter('order_id', $order_id)
                ->addFieldToFilter('product_id', $creditmemo_item->getProductId());

            /* @var $order_item Mage_Sales_Model_Order_Item */
            foreach ($order_items as $order_item) {
                $amount_refunded        = $order_item->getAmountRefunded() - $creditmemo_item->getRowTotal();
                $base_amount_refunded   = $order_item->getBaseAmountRefunded() - $creditmemo_item->getRowTotal();
                $tax_refunded           = $order_item->getTaxRefunded() - $creditmemo_item->getTaxAmount();
                $base_tax_refunded      = $order_item->getBaseTaxRefunded() - $creditmemo_item->getBaseTaxAmount();
                $discount_refunded      = $order_item->getDiscountRefunded() - $creditmemo_item->getDiscountAmount();
                $base_discount_refunded = $order_item->getBaseDiscountRefunded() - $creditmemo_item->getBaseDiscountAmount();
                $hidden_tax_refunded    = $order_item->getHiddenTaxRefunded() - $creditmemo_item->getHiddenTaxAmount();
                $base_hidden_tax_refunded = $order_item->getBaseHiddenTaxRefunded() - $creditmemo_item->getBaseHiddenTaxAmount();


                if($amount_refunded >= 0){$order_item->setAmountRefunded($amount_refunded);}
                if($base_amount_refunded >= 0){$order_item->setBaseAmountRefunded($base_amount_refunded);}
                if($tax_refunded >= 0){$order_item->setTaxRefunded($tax_refunded);}
                if($base_tax_refunded >= 0){$order_item->setBaseTaxRefunded($base_tax_refunded);}
                if($discount_refunded >= 0){$order_item->setDiscountRefunded($discount_refunded);}
                if($base_discount_refunded >= 0){$order_item->setBaseDiscountRefunded($base_discount_refunded);}
                if($hidden_tax_refunded >= 0){$order_item->setHiddenTaxRefunded($hidden_tax_refunded);}
                if($base_hidden_tax_refunded >= 0){$order_item->setBaseHiddenTaxRefunded($base_hidden_tax_refunded);}

                $order_item->save();
            }
        }

        if ($order->hasInvoices() && $order->hasShipments()){
            $state = Mage_Sales_Model_Order::STATE_COMPLETE;
        } else if ($order->hasInvoices()) {
            $state = Mage_Sales_Model_Order::STATE_PROCESSING;
        } else {
            $state = $order->getState();
        }
        $order->setData('state', $state);
        $order->setStatus($order->getConfig()->getStateDefaultStatus($state));

        $total_refunded = $order->getTotalRefunded() - $this->getBaseGrandTotal();
        $base_total_refunded = $order->getTotalRefunded() - $this->getBaseGrandTotal();
        $order->setTotalRefunded($total_refunded);
        $order->setBaseTotalRefunded($base_total_refunded);
        $order->save();

        Mage::getSingleton('iwd_ordermanager/logger')->itemDeleteSuccess('creditmemo', $this->getIncrementId());

        $items = $this->getItemsCollection();
        $obj = $this;

        Mage::register('isSecureArea', true);
        $this->delete();
        Mage::unregister('isSecureArea');

        Mage::dispatchEvent('iwd_ordermanager_sales_creditmemo_delete_before', array('creditmemo' => $obj, 'creditmemo_items' => $items));

        return true;
    }

    /*public function createCreditmemo($order_id, $qtys)
    {
        $order = Mage::getModel('sales/order')->load($order_id);

        if (!$order->getId()){
            Mage::throwException('Order not exists');
        }

        if (!$order->canCreditmemo()){
            Mage::throwException('Cannot create creditmemo');
        }

        if (!is_array($qtys)){
            Mage::throwException('Empty items for credit memo');
        }

        $service = Mage::getModel('sales/service_order', $order);

        $data = array('qtys' => $qtys,
            "do_offline" => 1,
            "shipping_amount" => 0.0,
            "adjustment_positive" => 0.0,
            "adjustment_negative" => 0.0,
        );

        $creditmemo = $service->prepareCreditmemo($data);

        $creditmemo->setPaymentRefundDisallowed(true)
            ->setAutomaticallyCreated(true)
            ->register()
            ->addComment(Mage::helper('iwd_ordermanager')->__('Credit memo has been created automatically'));

        Mage::getModel('core/resource_transaction')
            ->addObject($creditmemo)
            ->addObject($creditmemo->getOrder())
            ->save();

        return $creditmemo;
    }*/

    /*public function createCreditmemoAdjustmentRefund($order, $refund_amount, $base_refund_amount, $items = array())
    {
        $refund_amount = ($refund_amount <= 0) ? $refund_amount : (-1 * $refund_amount);
        $base_refund_amount = ($base_refund_amount <= 0) ? $base_refund_amount : (-1 * $base_refund_amount);

        /* @var $order Mage_Sales_Model_Order * /
        if (!$order->canCreditmemo()) {
            Mage::throwException('Cannot create creditmemo');
        }

        /* @var $creditmemo Mage_Sales_Model_Order_Creditmemo * /
        $creditmemo = Mage::getModel('sales/convert_order')->toCreditmemo($order);
        $creditmemo->setTotalQty(0)
            ->setBaseShippingAmount(0.0)
            ->setAdjustmentNegative($refund_amount)
            ->setBaseAdjustmentNegative($base_refund_amount);

        $creditmemo->collectTotals();

        $creditmemo->setPaymentRefundDisallowed(true)
            ->setAutomaticallyCreated(true)
            ->register()
            ->addComment(Mage::helper('iwd_ordermanager')->__('Credit memo has been created automatically'));

        Mage::getModel('core/resource_transaction')
            ->addObject($creditmemo)
            ->addObject($creditmemo->getOrder())
            ->save();

        $this->updateOrderItems($items);
    }*/

    protected function updateOrderItems($items)
    {
        foreach($items as $id => $amount){
            /* @var $order_item Mage_Sales_Model_Order_Item */
            $order_item = Mage::getModel('sales/order_item')->load($id);

            if(isset($amount['row_total']) && $amount['row_total'] > 0){
                $order_item->setAmountRefunded($order_item->getAmountRefunded() + $amount['row_total']);
            }
            if(isset($amount['base_row_total']) && $amount['base_row_total'] > 0){
                $order_item->setBaseAmountRefunded($order_item->getBaseAmountRefunded() + $amount['base_row_total']);
            }
            if(isset($amount['tax_refunded']) && $amount['tax_refunded'] > 0){
                $order_item->setTaxRefunded($order_item->getTaxRefunded() + $amount['tax_refunded']);
            }
            if(isset($amount['base_tax_amount']) && $amount['base_tax_amount'] > 0){
                $order_item->setBaseTaxRefunded($order_item->getBaseTaxRefunded() + $amount['base_tax_amount']);
            }
            if(isset($amount['hidden_tax_amount']) && $amount['hidden_tax_amount'] > 0){
                $order_item->setHiddenTaxRefunded($order_item->getHiddenTaxRefunded() + $amount['hidden_tax_amount']);
            }
            if(isset($amount['base_hidden_tax_amount']) && $amount['base_hidden_tax_amount'] > 0){
                $order_item->setBaseHiddenTaxRefunded($order_item->getBaseHiddenTaxRefunded() + $amount['base_hidden_tax_amount']);
            }
            if(isset($amount['discount_amount']) && $amount['discount_amount'] > 0){
                $order_item->setDiscountRefunded($order_item->getDiscountRefunded() + $amount['discount_amount']);
            }
            if(isset($amount['base_discount_amount']) && $amount['base_discount_amount'] > 0){
                $order_item->setBaseDiscountRefunded($order_item->getBaseDiscountRefunded() + $amount['base_discount_amount']);
            }

            $order_item->save();
        }
    }
}
