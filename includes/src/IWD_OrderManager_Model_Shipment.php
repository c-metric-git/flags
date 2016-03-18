<?php
class IWD_OrderManager_Model_Shipment extends Mage_Sales_Model_Order_Shipment
{
    const XML_PATH_SALES_ALLOW_DEL_SHIPMENTS = 'iwd_ordermanager/iwd_delete_shipments/allow_del_shipments';

    public function isAllowDeleteShipments()
    {
        $conf_allow = Mage::getStoreConfig(self::XML_PATH_SALES_ALLOW_DEL_SHIPMENTS, Mage::app()->getStore());
        $permission_allow = Mage::getSingleton('admin/session')->isAllowed('iwd_ordermanager/shipment/actions/delete');
        $engine = Mage::helper('iwd_ordermanager')->CheckShipmentTableEngine();

        return ($conf_allow && $permission_allow && $engine);
    }

    public function DeleteShipment()
    {
        if (!$this->isAllowDeleteShipments()) {
            Mage::getSingleton('iwd_ordermanager/logger')->itemDeleteError('shipment', $this->getIncrementId());
            return false;
        }

        Mage::dispatchEvent('iwd_ordermanager_sales_shipment_delete_after', array('shipment' => $this, 'shipment_items' => $this->getItemsCollection()));

        $order = Mage::getModel('sales/order')->load($this->getOrderId());

        $this->updateOrderShippedQty();

        Mage::getSingleton('iwd_ordermanager/logger')->itemDeleteSuccess('shipment', $this->getIncrementId());

        $items = $this->getItemsCollection();
        $obj = $this;

        Mage::register('isSecureArea', true);
        $this->delete();
        Mage::unregister('isSecureArea');

        $this->updateShippingReport($order, $obj);
        $this->changeOrderStatusAfterDeleteShipment($order);

        Mage::dispatchEvent('iwd_ordermanager_sales_shipment_delete_before', array('shipment' => $obj, 'shipment_items' => $items));

        return true;
    }

    protected function updateShippingReport($order, $shipping){
        Mage::getSingleton('iwd_ordermanager/report')
            ->addShippingPeriod($shipping->getCreatedAt(), $shipping->getUpdatedAt(), $order->getCreatedAt());
    }

    protected function updateOrderShippedQty()
    {
        $order_id = $this->getOrderId();
        $ship_id = $this->getEntityId();

        $shipment_items = Mage::getResourceModel('sales/order_shipment_item_collection')
            ->addFieldToFilter('parent_id', $ship_id)
            ->load();

        foreach ($shipment_items as $shipment_item) {
            $order_items = Mage::getModel('sales/order_item')->getCollection()
                ->addFieldToFilter('order_id', $order_id)
                ->addFieldToFilter('item_id', $shipment_item->getOrderItemId());

            foreach ($order_items as $order_item) {
                $qty = $order_item->getQtyShipped() - $shipment_item['qty'];
                $order_item->setQtyShipped($qty);
                $order_item->save();
            }
        }
    }

    protected function changeOrderStatusAfterDeleteShipment($order)
    {
        $message = Mage::helper('iwd_ordermanager')->__('State was changed after deletion shipment');
        $state = ($order->hasInvoices()) ? Mage_Sales_Model_Order::STATE_PROCESSING : Mage_Sales_Model_Order::STATE_NEW;

        $order->setState($state, true, $message);
        $order->save();
    }

    /*public function canDelete()
    {
        return $this->isAllowDeleteShipments();
    }*/
    /*const XML_PATH_CREATE_SHIPMENT = 'iwd_ordermanager/edit/create_shipment';
    public function getAllowCreateShipment()
    {
        return Mage::getStoreConfig(self::XML_PATH_CREATE_SHIPMENT);
    }*/
    /*public function CreateShipment($orderId, $itemQty = array())
    {
        $order = Mage::getModel('sales/order')->load($orderId);

        if ($order->canShip()) {
            $shipment = Mage::getModel('sales/service_order', $order)->prepareShipment($itemQty);
            $shipmentId = $shipment->create($order->getIncrementId(), array(), 'Shipment created automatically');
        }
    }*/
}
