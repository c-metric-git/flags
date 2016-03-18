<?php
class IWD_OrderManager_Block_Adminhtml_Sales_Order_Grid_Renderer_Invoice extends IWD_OrderManager_Block_Adminhtml_Sales_Order_Grid_Renderer_Abstract
{
    protected function loadInvoices()
    {
        $order_id = $this->getOrderId();

        return Mage::getResourceModel('sales/order_invoice_grid_collection')
            ->addFieldToSelect('increment_id')
            ->addFieldToFilter('main_table.order_id', $order_id)
            ->load();
    }

    protected function prepareInvoiceIds()
    {
        $invoices = $this->loadInvoices();
        $increment_ids = array();

        foreach ($invoices as $invoice) {
            $increment_ids[] = $invoice->getIncrementId();
        }

        return $increment_ids;
    }

    protected function Grid()
    {
        $increment_ids = $this->prepareInvoiceIds();
        return $this->formatBigData($increment_ids);
    }

    protected function Export()
    {
        $increment_ids = $this->prepareInvoiceIds();
        return implode(',', $increment_ids);
    }
}