<?php
class IWD_OrderManager_Model_Order_Info extends IWD_OrderManager_Model_Order
{
    protected $params;

    public function updateOrderInfo($params)
    {
        $this->init($params);

        if (isset($params['confirm_edit']) && !empty($params['confirm_edit'])) {
            $this->addChangesToConfirm();
        } else {
            $this->editInfo();
            $this->updateOrderAmounts();
            $this->addChangesToLog();
            $this->notifyEmail();
        }
    }

    public function execUpdateOrderInfo($params)
    {
        $this->init($params);
        $this->editInfo();
        $this->updateOrderAmounts();
        $this->notifyEmail();
    }

    protected function init($params){
        $this->params = $params;

        if (empty($this->params) || !isset($this->params['order_id'])){
            throw new Exception("Order id is not defined");
        }

        $this->load($this->params['order_id']);
    }

    protected function addChangesToLog()
    {
        $logger = Mage::getSingleton('iwd_ordermanager/logger');
        $order_id = $this->params['order_id'];

        $logger->addCommentToOrderHistory($order_id);
        $logger->addLogToLogTable(IWD_OrderManager_Model_Confirm_Options_Type::ORDER_INFO, $order_id);
    }

    protected function editInfo()
    {
        $this->load($this->params['order_id']);

        $this->updateOrderItemsState();

        $this->updateOrderState();
        $this->updateOrderStatus();

        $this->updateOrderStoreId();

        $this->updateOrderDate();
        $this->updateInvoiceDate();
        $this->updateCreditmemoDate();
        $this->updateShippingDate();
    }

    protected function notifyEmail(){
        $notify = isset($this->params['notify']) ? $this->params['notify'] : null;
        $order_id = $this->params['order_id'];

        if ($notify) {
            $message = isset($this->params['comment_text']) ? $this->params['comment_text'] : null;
            $email = isset($this->params['comment_email']) ? $this->params['comment_email'] : null;
            $result['notify'] = Mage::getModel('iwd_ordermanager/notify_notification')->sendNotifyEmail($order_id, $email, $message);
        }
    }

    protected function addChangesToConfirm()
    {
        $logger = Mage::getSingleton('iwd_ordermanager/logger');
        $order_id = $this->params['order_id'];

        $this->estimateOrderInfoChanges();
        $this->estimateOrderAmounts();

        $logger->addCommentToOrderHistory($order_id, 'wait');
        $logger->addLogToLogTable(IWD_OrderManager_Model_Confirm_Options_Type::ORDER_INFO, $order_id, $this->params);

        $message = Mage::helper('iwd_ordermanager')
            ->__('Updates was not applied now! Customer get email with confirm link. Order will be updated after confirm.');

        Mage::getSingleton('adminhtml/session')->addNotice($message);
    }

    protected function estimateOrderInfoChanges(){
        $logger = Mage::getSingleton('iwd_ordermanager/logger');

        if (isset($this->params['status']) && !empty($this->params['status']) && $this->getStatus() != $this->params['status']) {
            $logger->addChangesToLog('order_status', $this->getStatus(), $this->params['status']);
        }

        $allow_change_state = $this->isAllowChangeOrderState();
        if (isset($this->params['state']) && !empty($this->params['state']) && $this->getState() != $this->params['state'] && $allow_change_state) {
            $logger->addChangesToLog('order_state', $this->getState(), $this->params['state']);
        }

        if (isset($this->params['store_id']) && !empty($this->params['store_id']) && $this->getStoreId() != $this->params['store_id']) {
            $new_store = Mage::app()->getStore($this->params['store_id']);
            $old_store = Mage::app()->getStore($this->getStoreId());
            $logger->addChangesToLog('order_store_name', $old_store->getName(), $new_store->getName());
        }
    }

    protected function updateOrderStatus()
    {
        $status_id = $this->params['status'];

        if (!empty($status_id) && $this->getStatus() != $status_id && $status_id !== 'false' && $status_id != false) {
            Mage::getSingleton('iwd_ordermanager/logger')->addChangesToLog('order_status', $this->getStatus(), $status_id);
            $this->setData('status', $status_id)->save();
        }
    }

    protected function updateOrderStoreId()
    {
        $store_id = $this->params['store_id'];

        $new_store = Mage::app()->getStore($store_id);
        $old_store = Mage::app()->getStore($this->getStoreId());

        if (!empty($store_id) && $this->getStoreId() != $store_id) {
            Mage::getSingleton('iwd_ordermanager/logger')->addChangesToLog('order_store_name', $old_store->getName(), $new_store->getName());
            $this->setData('store_id', $store_id)->save();
        }
    }

    protected function updateOrderState()
    {
        if(isset($this->params['state'])) {
            $state_id = $this->params['state'];

            $allow_change_state = $this->isAllowChangeOrderState();

            if (!empty($state_id) && $this->getState() != $state_id && $allow_change_state) {
                Mage::getSingleton('iwd_ordermanager/logger')->addChangesToLog('order_state', $this->getState(), $state_id);
                $this->setData('state', $state_id)->save();
            }
        }
    }

    protected function updateOrderItemsState()
    {
        try {
            if($this->getState() == Mage_Sales_Model_Order::STATE_CANCELED) {
                if (isset($this->params['state']) && !empty($this->params['state'])){
                    if( $this->getState() != $this->params['state']){
                        $this->restoreCanceledItems();
                    }
                } elseif (isset($this->params['status']) && !empty($this->params['status'])) {
                    $status_code = $this->params['status'];
                    $state = Mage::getModel('sales/order_status')->getCollection()
                        ->addFieldToFilter('main_table.status', $status_code)
                        ->joinStates()
                        ->getFirstItem()
                        ->getData("state");

                    if($this->getState() != $state && Mage_Sales_Model_Order::STATE_CANCELED != $state){
                        $this->restoreCanceledItems();
                    }
                }
            }
        }catch(Exception $e){

        }
    }

    protected function restoreCanceledItems()
    {
        $items = $this->getItemsCollection();
        foreach ($items as $item) {
            $qty = $item->getQtyCanceled();
            $product_id = $item->getProductId();

            $item->setQtyCanceled(0.0)
                ->setHiddenTaxCanceled(0.0)
                ->setTaxCanceled(0.0)
                ->save();

            $stock_item = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product_id);

            if (Mage::helper('cataloginventory')->isQty($stock_item->getTypeId())) {
                if ($item->getStoreId()) {
                    $stock_item->setStoreId($item->getStoreId());
                }
                if ($stock_item->checkQty($qty)) {
                    $stock_item->subtractQty($qty)->save();
                }
            }
        }
    }

    protected function updateOrderDate()
    {
        if(isset($this->params['created_at'])) {
            $created_at = $this->params['created_at'];

            if (!empty($created_at) && $this->getCreatedAt() != $created_at) {
                $created_at = $this->prepareDate($created_at);
                Mage::getSingleton('iwd_ordermanager/logger')->addChangesToLog('created_at', $this->getCreatedAt(), $created_at);
                $this->setData('created_at', $created_at)->save();
            }
        }
    }

    protected function updateInvoiceDate()
    {
        if(isset($this->params['invoice_created_at']) && isset($this->params['invoice_id'])) {
            $created_at = $this->params['invoice_created_at'];
            $id = $this->params['invoice_id'];

            if (!empty($created_at) && !empty($id)) {
                $invoice = Mage::getModel('sales/order_invoice')->load($id);
                $created_at = $this->prepareDate($created_at);
                $invoice->setData('created_at', $created_at)->save();
            }
        }
    }

    protected function updateCreditmemoDate()
    {
        if(isset($this->params['creditmemo_created_at']) && isset($this->params['creditmemo_id'])) {
            $created_at = $this->params['creditmemo_created_at'];
            $id = $this->params['creditmemo_id'];

            if (!empty($created_at) && !empty($id)) {
                $creditmemo = Mage::getModel('sales/order_creditmemo')->load($id);
                $created_at = $this->prepareDate($created_at);
                $creditmemo->setData('created_at', $created_at)->save();
            }
        }
    }

    protected function updateShippingDate()
    {
        if(isset($this->params['shipping_created_at']) && isset($this->params['shipping_id'])) {
            $created_at = $this->params['shipping_created_at'];
            $id = $this->params['shipping_id'];

            if (!empty($created_at) && !empty($id)) {
                $shipping = Mage::getModel('sales/order_shipment')->load($id);
                $created_at = $this->prepareDate($created_at);
                $shipping->setData('created_at', $created_at)->save();
            }
        }
    }

    protected function prepareDate($created_at)
    {
        $myDateTime = DateTime::createFromFormat(Mage::helper('iwd_ordermanager')->getDataTimeFormat(), $created_at);
        $created_at = $myDateTime->format('Y-m-d H:i:s');
        return Mage::getModel('core/date')->gmtDate('Y-m-d H:i:s', $created_at);
    }



    protected function updateOrderAmounts()
    {
        if (isset($this->params['recalculate_amount']) && !empty($this->params['recalculate_amount'])) {
            //TODO: add!!!
        }
    }

    protected function estimateOrderAmounts()
    {
        if (isset($this->params['recalculate_amount']) && !empty($this->params['recalculate_amount']))
        {
            $order_id = $this->params['order_id'];
            $order = Mage::getModel('sales/order')->load($order_id);
            Mage::getSingleton('adminhtml/session_quote')->clear();

            $sales_order_create = Mage::getModel('adminhtml/sales_order_create')->initFromOrder($order);
            $quote = $sales_order_create->getQuote();

            $quote->setData('store_id', $this->params['store_id'])->save();

            $quote = $quote->setTotalsCollectedFlag(false)->collectTotals();

            $totals = array(
                'grand_total' => $quote->getGrandTotal(),
                'base_grand_total' => $quote->getBaseGrandTotal(),
            );

            Mage::getSingleton('iwd_ordermanager/logger')->addNewTotalsToLog($totals);
        }
    }
}