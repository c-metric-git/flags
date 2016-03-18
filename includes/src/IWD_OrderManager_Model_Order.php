<?php
class IWD_OrderManager_Model_Order extends Mage_Sales_Model_Order
{
    const XML_PATH_SALES_ALLOW_DEL_ORDERS   = 'iwd_ordermanager/iwd_delete_orders/allow_del_orders';
    const XML_PATH_SALES_STATUS_ORDER       = 'iwd_ordermanager/iwd_delete_orders/order_status';
    const XML_PATH_DELETE_DOWNLOADABLE      = 'iwd_ordermanager/iwd_delete_orders/delete_downloadable';
    const XML_PATH_CHANGE_ORDER_STATE       = 'iwd_ordermanager/edit/change_order_state';

    public function getShippingMethod($asObject = false)
    {
        $shippingMethod = $this->getData('shipping_method');
        if (!$asObject) {
            return $shippingMethod;
        } else {
            list($carrierCode, $method) = explode('_', $shippingMethod, 2);
            return new Varien_Object(array(
                'carrier_code' => $carrierCode,
                'method'       => $method
            ));
        }
    }

    public function isAllowChangeOrderState()
    {
        return Mage::getStoreConfig(self::XML_PATH_CHANGE_ORDER_STATE);
    }

    public function isAllowDeleteOrders()
    {
        $conf_allow = Mage::getStoreConfig(self::XML_PATH_SALES_ALLOW_DEL_ORDERS, Mage::app()->getStore());
        $permission_allow = Mage::getSingleton('admin/session')->isAllowed('iwd_ordermanager/order/actions/delete');
        $engine = Mage::helper('iwd_ordermanager')->CheckOrderTableEngine();
        return ($conf_allow && $permission_allow && $engine);
    }

    public function isAllowChangeOrderStatus()
    {
        $conf_allow = 1;
        $permission_allow = Mage::getSingleton('admin/session')->isAllowed('iwd_ordermanager/order/actions/update_status');
        return ($conf_allow && $permission_allow);
    }

    public function getOrderStatusesForDeleteIds()
    {
        return explode(',', Mage::getStoreConfig(self::XML_PATH_SALES_STATUS_ORDER));
    }

    public function checkOrderStatusForDeleting()
    {
        return (in_array($this->getStatus(), $this->getOrderStatusesForDeleteIds()));
    }

    public function canDelete()
    {
        return ($this->isAllowDeleteOrders() && $this->checkOrderStatusForDeleting());
    }

    public function deleteOrder()
    {
        if (!$this->canDelete()) {
            $message = 'Maybe, you can not delete items with some statuses. Please, check <a href="'
                . Mage::helper("adminhtml")->getUrl("adminhtml/system_config/edit", array("section" => "iwd_ordermanager"))
                . '" target="_blank" title="System - Configuration - IWD Extensions - Order Manager">configuration</a> of IWD OrderManager';

            Mage::getSingleton('iwd_ordermanager/logger')->addNoticeMessage('check_order_status', $message);
            Mage::getSingleton('iwd_ordermanager/logger')->itemDeleteError('order', $this->getIncrementId());
            return false;
        }

        Mage::dispatchEvent('iwd_ordermanager_sales_order_delete_after', array('order' => $this, 'order_items' => $this->getItemsCollection()));

        $this->returnQtyProducts();

        Mage::getSingleton('iwd_ordermanager/report')->addOrderPeriod($this->getCreatedAt(), $this->getUpdatedAt());

        $this->deleteInvoices();
        $this->deleteShipments();
        $this->deleteCreditmemos();
        $this->deleteQuote();
        $this->deleteDownloadable();

        Mage::getSingleton('iwd_ordermanager/logger')->itemDeleteSuccess('order', $this->getIncrementId());

        Mage::register('isSecureArea', true);
        $this->delete();
        Mage::unregister('isSecureArea');

        $items = $this->getItemsCollection();
        $obj = $this;
        Mage::dispatchEvent('iwd_ordermanager_sales_order_delete_before', array('order' => $obj, 'order_items' => $items));

        return true;
    }

    public function deleteInvoices()
    {
        if (!$this->hasInvoices()){
            return;
        }

        $invoices = Mage::getResourceModel('sales/order_invoice_collection')->setOrderFilter($this->getEntityId())->load();

        foreach ($invoices as $invoice) {
            $increment_id = $invoice->getIncrementId();
            $create_at = $invoice->getCreatedAt();
            $update_at = $invoice->getUpdatedAt();
            $items = $this->getItemsCollection();

            Mage::dispatchEvent('iwd_ordermanager_sales_invoice_delete_after', array('invoice' => $invoice, 'invoice_items' => $items));

            Mage::getSingleton('iwd_ordermanager/logger')->itemDeleteSuccess('invoice', $increment_id);
            Mage::getSingleton('iwd_ordermanager/report')->addInvoicedPeriod($create_at, $update_at, $create_at);
            $invoice->delete();

            Mage::dispatchEvent('iwd_ordermanager_sales_invoice_delete_before', array('invoice' => $invoice, 'invoice_items' => $items));
        }
    }

    public function deleteShipments()
    {
        if (!$this->hasShipments()) {
            return;
        }

        $objects = Mage::getResourceModel('sales/order_shipment_collection')->setOrderFilter($this->getEntityId())->load();

        foreach ($objects as $obj) {
            Mage::getSingleton('iwd_ordermanager/logger')->itemDeleteSuccess('shipment', $obj->getIncrementId());
            Mage::getSingleton('iwd_ordermanager/report')->addShippingPeriod($obj->getCreatedAt(), $obj->getUpdatedAt(), $obj->getCreatedAt());

            $items = $this->getItemsCollection();

            Mage::dispatchEvent('iwd_ordermanager_sales_shipment_delete_after', array('shipment' => $obj, 'shipment_items' => $items));
            $obj->delete();
            Mage::dispatchEvent('iwd_ordermanager_sales_shipment_delete_before', array('shipment' => $obj, 'shipment_items' => $items));
        }
    }

    public function deleteCreditmemos()
    {
        if (!$this->hasCreditmemos()){
            return;
        }

        $credit_memos = Mage::getResourceModel('sales/order_creditmemo_collection')->setOrderFilter($this->getEntityId())->load();

        foreach ($credit_memos as $creditmemo) {
            $items = $this->getItemsCollection();
            $increment_id = $creditmemo->getIncrementId();
            $create_at = $creditmemo->getCreatedAt();
            $update_at = $creditmemo->getUpdatedAt();

            Mage::getSingleton('iwd_ordermanager/report')->addRefundedPeriod($create_at, $update_at, $create_at);

            Mage::dispatchEvent('iwd_ordermanager_sales_creditmemo_delete_after', array('creditmemo' => $creditmemo, 'creditmemo_items' => $items));
            $creditmemo->delete();
            Mage::dispatchEvent('iwd_ordermanager_sales_creditmemo_delete_before', array('creditmemo' => $creditmemo, 'creditmemo_items' => $items));

            Mage::getSingleton('iwd_ordermanager/logger')->itemDeleteSuccess('creditmemo', $increment_id);
        }
    }

    public function deleteQuote(){
        $quote_id = $this->getQuoteId();
        Mage::getModel('sales/quote')
            ->getCollection()
            ->addFieldToFilter('entity_id', $quote_id)
            ->getFirstItem()
            ->delete();
    }

    public function deleteDownloadable()
    {
        if(!Mage::getStoreConfig(self::XML_PATH_DELETE_DOWNLOADABLE)){
            return;
        }

        $order_id = $this->getEntityId();
        $collection = Mage::getModel('downloadable/link_purchased')
            ->getCollection()
            ->addFieldToFilter('order_id', $order_id);

        foreach($collection as $item){
            $item->delete();
        }
    }

    protected function returnQtyProducts()
    {
        if (($this->getState() == 'new')){
            $this->cancel()->save();
        }
    }

    public function isArchived($order = null){
        $order_id = null;

        if(empty($order)){
            $order_id = $this->getEntityId();
        }else if(is_object($order)){
            $order_id = $order->getEntityId();
        }else if(is_numeric($order) ){
            $order_id = $order;
        }else if(is_string($order) ){
            $order_id = (int)$order;
        }

        $origin = Mage::getResourceModel('sales/order_grid_collection')
            ->addFieldToFilter('entity_id', $order_id)->getSize();
        if($origin <= 0){
            if(Mage::helper('iwd_ordermanager')->isEnterpriseMagentoEdition()){
                return false;
                /*$ee = Mage::getResourceModel('enterprise_salesarchive/order_grid')
                    ->addFieldToFilter('entity_id', $order_id)->getSize();
                if($ee > 0){
                    return true;
                }*/
            } else {

                $om_archive = Mage::getModel('iwd_ordermanager/archive_order')->getCollection()
                    ->addFieldToFilter('entity_id', $order_id)->getSize();
                if($om_archive > 0){
                    return true;
                }
            }
        }

        return false;
    }
}