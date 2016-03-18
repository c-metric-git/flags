<?php
class IWD_OrderManager_Model_System_Config_Gridcolumn_Order
{
    protected function getSelectedColumnsArray()
    {
        return Mage::getModel('iwd_ordermanager/order_grid')->getSelectedColumnsArray(IWD_OrderManager_Model_Order_Grid::XML_PATH_ORDER_GRID_COLUMN);
    }

    public function toOptionArray()
    {
        $selected = $this->getSelectedColumnsArray();
        $columns = Mage::getModel('iwd_ordermanager/order_grid')->getOrderGridColumns();

        $options = array();
        foreach ($selected as $sel)
        {
            if(isset($columns[$sel]))
            {
                $options[] = array(
                    'value' => $sel,
                    'label' => $columns[$sel]
                );
                unset($columns[$sel]);
            }
        }

        foreach ($columns as $code => $label) {
            $options[] = array(
                'value' => $code,
                'label' => $label
            );
        }

        return $options;
    }
}