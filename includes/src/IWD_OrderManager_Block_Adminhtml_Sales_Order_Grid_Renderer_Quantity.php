<?php

class IWD_OrderManager_Block_Adminhtml_Sales_Order_Grid_Renderer_Quantity extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $path = $_SERVER['PATH_INFO'];
        $exportCsv = (strstr($path, 'exportCsv') !== false);
        $exportExcel = (strstr($path, 'exportExcel') !== false);

        $order = Mage::getModel('sales/order')->loadByIncrementId($row['increment_id']);
        $items = $order->getAllVisibleItems();

        $qty_ordered = 0;
        $qty_invoiced = 0;
        $qty_shipped = 0;
        $qty_refunded = 0;
        $qty_canceled = 0;

        foreach ($items as $item) {
            $qty_ordered += $item['qty_ordered'];
            $qty_invoiced += $item['qty_invoiced'];
            $qty_shipped += $item['qty_shipped'];
            $qty_refunded += $item['qty_refunded'];
            $qty_canceled += $item['qty_canceled'];
        }

        if ($exportCsv || $exportExcel)
            return $this->Export($qty_ordered, $qty_invoiced, $qty_shipped, $qty_refunded, $qty_canceled);

        return $this->Grid($qty_ordered, $qty_invoiced, $qty_shipped, $qty_refunded, $qty_canceled);
    }

    private function Grid($qty_ordered, $qty_invoiced, $qty_shipped, $qty_refunded, $qty_canceled)
    {
        $helper = Mage::helper('iwd_ordermanager');
        $orderQty = "";
        if ($qty_ordered) $orderQty .= $helper->__('Ordered'). ':&nbsp;' . number_format($qty_ordered, 0).'<br/>';
        if ($qty_invoiced) $orderQty .= $helper->__('Invoiced'). ':&nbsp;'  . number_format($qty_invoiced, 0).'<br/>';
        if ($qty_shipped) $orderQty .= $helper->__('Shipped'). ':&nbsp;'  . number_format($qty_shipped, 0).'<br/>';
        if ($qty_refunded) $orderQty .= $helper->__('Refunded'). ':&nbsp;'  . number_format($qty_refunded, 0).'<br/>';
        if ($qty_canceled) $orderQty .= $helper->__('Cancelled'). ':&nbsp;'  . number_format($qty_canceled, 0);
        return $orderQty;
    }

    private function Export($qty_ordered, $qty_invoiced, $qty_shipped, $qty_refunded, $qty_canceled)
    {
        $helper = Mage::helper('iwd_ordermanager');
        $orderQty = "";
        if ($qty_ordered) $orderQty .= $helper->__('Ordered'). '=' . number_format($qty_ordered, 0);
        if ($qty_invoiced) $orderQty .= $helper->__(' Invoiced'). '=' . number_format($qty_invoiced, 0);
        if ($qty_shipped) $orderQty .= $helper->__(' Shipped'). '='. number_format($qty_shipped, 0);
        if ($qty_refunded) $orderQty .= $helper->__(' Refunded'). '=' . number_format($qty_refunded, 0);
        if ($qty_canceled) $orderQty .= $helper->__(' Cancelled'). '=' . number_format($qty_canceled, 0);
        return $orderQty;
    }
}
