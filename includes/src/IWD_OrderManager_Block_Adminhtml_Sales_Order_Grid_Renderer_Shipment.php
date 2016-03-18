<?php
class IWD_OrderManager_Block_Adminhtml_Sales_Order_Grid_Renderer_Shipment extends IWD_OrderManager_Block_Adminhtml_Sales_Order_Grid_Renderer_Abstract
{
    protected function loadShipments()
    {
        $order_id = $this->getOrderId();

        return Mage::getResourceModel('sales/order_shipment_grid_collection')
            ->addFieldToSelect('increment_id')
            ->addFieldToFilter('main_table.order_id', $order_id)
            ->load();
    }

    protected function prepareShipmentIds()
    {
        $shipments = $this->loadShipments();
        $increment_ids = array();

        foreach ($shipments as $shipment) {
            $increment_ids[] = $shipment->getIncrementId();
        }

        return $increment_ids;
    }

    protected function Grid()
    {
        $increment_ids = $this->prepareShipmentIds();
        return $this->formatBigData($increment_ids);
    }

    protected function Export()
    {
        $increment_ids = $this->prepareShipmentIds();
        return implode(',', $increment_ids);
    }
}