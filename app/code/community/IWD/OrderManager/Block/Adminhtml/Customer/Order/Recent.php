<?php
class IWD_OrderManager_Block_Adminhtml_Customer_Order_Recent extends Mage_Adminhtml_Block_Customer_Edit_Tab_View_Orders
{
    const XML_PATH_CUSTOMER_RESENT_ORDERS_COUNT = 'iwd_ordermanager/customer_orders/resent_orders_count';

    protected function _preparePage()
    {
        $selected_columns = Mage::getStoreConfig(self::XML_PATH_CUSTOMER_RESENT_ORDERS_COUNT);

        $this->getCollection()
            ->setPageSize($selected_columns)
            ->setCurPage(1);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('iwd_ordermanager/customer_order')->getRecentOrdersCollectionForCurrentCustomer();
        $this->setCollection($collection);
        return Mage_Adminhtml_Block_Widget_Grid::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $selected_columns_grid = Mage::getModel('iwd_ordermanager/customer_order')->getSelectedColumnsForRecentOrderGrid();

        $grid = Mage::getModel('iwd_ordermanager/order_grid')->prepareColumns($this, $selected_columns_grid);
        Mage::getModel('iwd_ordermanager/order_grid')->addHiddenColumnWithStatus($grid);
        Mage::getModel('iwd_ordermanager/order_grid')->addReorderColumn($grid);

        $this->sortColumnsByOrder();
        return $this;
    }

    public function _toHtml(){
        $script = '<script type="text/javascript">
                    if(typeof(jQueryIWD) == "undefined"){if(typeof(jQuery) != "undefined") {jQueryIWD = jQuery;}} $ji = jQueryIWD;
                    if($ji("#customer_view_orders_grid_table").length) {
                        IWD.OrderManager.Grid.ColorGridRow();
                        if($ji.isFunction($ji.fn.stickyTableHeaders)){$ji("#customer_view_orders_grid_table").stickyTableHeaders();}
                    }
                 </script>';

        return parent::_toHtml() . $script;
    }
}