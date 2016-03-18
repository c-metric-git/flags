<?php
class IWD_OrderManager_Model_Mysql4_Archive_Order_Collection extends IWD_OrderManager_Model_Resource_Archive_Order_Collection
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('iwd_ordermanager/archive_order');
    }

    public function getSelectCountSql()
    {
        $controller_name = Mage::app()->getRequest()->getControllerName();

        if ($controller_name == 'sales_order' || $controller_name == 'sales_archive_order') {
            $this->_renderFilters();

            $unionSelect = clone $this->getSelect();

            $unionSelect->reset(Zend_Db_Select::ORDER);
            $unionSelect->reset(Zend_Db_Select::LIMIT_COUNT);
            $unionSelect->reset(Zend_Db_Select::LIMIT_OFFSET);

            $countSelect = clone $this->getSelect();
            $countSelect->reset();
            $countSelect->from(array('a' => $unionSelect), 'COUNT(*)');
        } else {
            $countSelect = parent::getSelectCountSql();
        }
        return $countSelect;
    }
}