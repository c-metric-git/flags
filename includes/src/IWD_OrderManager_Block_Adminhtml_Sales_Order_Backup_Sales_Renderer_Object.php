<?php
class IWD_OrderManager_Block_Adminhtml_Sales_Order_Backup_Sales_Renderer_Object extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    private $result = "";

    public function render(Varien_Object $row)
    {
        $deleted_object = unserialize($row['object']);

        $this->result = "";

        if (isset($deleted_object['increment_id']))
            $this->result .= '<b>#' . $deleted_object['increment_id'] . ' (id ' . $deleted_object['entity_id'] . ') </b>';
        if (isset($deleted_object['status']))
            $this->result .= '[' . $deleted_object['status'] . ']';
        if (isset($deleted_object['order_id']))
            $this->result .= '<br>OrderID: ' . $deleted_object['order_id'] . '';

        if (isset($deleted_object['customer_email']))
            $this->result .= '<br><b>' . Mage::helper('core')->__('User Login') . ': ' . $deleted_object['customer_email'] . '</b>';

        $this->_arrPriceRowToDescription('Subtotal', $deleted_object, 'subtotal');
        $this->_arrPriceRowToDescription('Shipping Amount', $deleted_object, 'shipping_amount');
        $this->_arrPriceRowToDescription('Discount Amount', $deleted_object, 'discount_amount');
        $this->_arrPriceRowToDescription('Tax Amount', $deleted_object, 'tax_amount');
        $this->_arrPriceRowToDescription('Grand Total', $deleted_object, 'grand_total');
        $this->_arrPriceRowToDescription('Total Paid', $deleted_object, 'total_paid');
        $this->_arrPriceRowToDescription('Total Refunded', $deleted_object, 'total_refunded');
        $this->_arrPriceRowToDescription('Total Due', $deleted_object, 'total_due');

        return $this->result;
    }

    protected function _arrPriceRowToDescription($param, $val, $index){
        if (isset($val[$index]))
            $this->result .= '<br>' . Mage::helper('core')->__($param) . ': ' . '<span class="price">'
                . Mage::helper('core')->currency($val[$index], true, false) . '</span>';
    }
}
