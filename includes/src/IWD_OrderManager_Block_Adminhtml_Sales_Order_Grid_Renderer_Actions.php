<?php
class IWD_OrderManager_Block_Adminhtml_Sales_Order_Grid_Renderer_Actions extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $exportCsv = $exportExcel = false;
        if(isset($_SERVER['PATH_INFO'])){
            $path = $_SERVER['PATH_INFO'];
            $exportCsv = (strstr($path, 'exportCsv') !== false);
            $exportExcel = (strstr($path, 'exportExcel') !== false);
        }

        $order = Mage::getModel('sales/order')->loadByIncrementId($row['increment_id']);

        if ($exportCsv || $exportExcel)
            return "";

        return $this->Grid($order->getId());
    }

    private function Grid($orderId)
    {
        $view_url = $this->getUrl('*/sales_order/view', array('order_id' => $orderId));

        $result = '<div class="ordered_items action_cell">'.
        '<a class="action_icon action_view_ordered_items" href="javascript:void(0);" title="'.Mage::helper('core')->__('Ordered items').'" id="ordered_items_' . $orderId . '"></a>'.
        '<a class="action_icon action_view_product_items" href="javascript:void(0);" title="'.Mage::helper('core')->__('More about products').'" id="product_items_' . $orderId . '"></a>' .
        '<a class="action_icon action_view_order" href="'.$view_url.'" title="'.Mage::helper('core')->__('View order').'"></a>';
        $result .= '</div>';

        return $result;
    }
}
