<?php
class IWD_OrderManager_Block_Adminhtml_Customer_Order_Orders extends Mage_Adminhtml_Block_Customer_Edit_Tab_Orders
{
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('iwd_ordermanager/customer_order')->getOrdersCollectionForCurrentCustomer();
        $this->setCollection($collection);
        return Mage_Adminhtml_Block_Widget_Grid::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $selected_columns_grid = Mage::getModel('iwd_ordermanager/customer_order')->getSelectedColumnsForOrderGrid();
        $grid = Mage::getModel('iwd_ordermanager/order_grid')->prepareColumns($this, $selected_columns_grid);
        Mage::getModel('iwd_ordermanager/order_grid')->addHiddenColumnWithStatus($grid);
        Mage::getModel('iwd_ordermanager/order_grid')->addReorderColumn($grid);

        $this->sortColumnsByOrder();
        return $this;
    }

    public function _toHtml(){
        $script = '<script type="text/javascript">
                    if(typeof(jQueryIWD) == "undefined"){if(typeof(jQuery) != "undefined") {jQueryIWD = jQuery;}} $ji = jQueryIWD;
                    if($ji("#customer_orders_grid_table").length) {
                        IWD.OrderManager.Grid.ColorGridRow();
                        if($ji.isFunction($ji.fn.stickyTableHeaders)){$ji("#customer_orders_grid_table").stickyTableHeaders();}
                    }
                 </script>';

        return parent::_toHtml() . $script;
    }
}