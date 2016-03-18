<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Flags
 */

    if ('true' == (string)Mage::getConfig()->getNode('modules/Amasty_Ogrid/active'))
    {
        class Amasty_Flags_Model_Mysql4_Order_Collection_Pure extends Amasty_Ogrid_Model_Mysql4_Order_Grid_Collection {}
        
    } else
    {
        class Amasty_Flags_Model_Mysql4_Order_Collection_Pure extends Mage_Sales_Model_Mysql4_Order_Grid_Collection {
            public function getSelectCountSql()
            {
                $this->_renderFilters();

                $countSelect = clone $this->getSelect();
                $countSelect->reset(Zend_Db_Select::ORDER);
                $countSelect->reset(Zend_Db_Select::LIMIT_COUNT);
                $countSelect->reset(Zend_Db_Select::LIMIT_OFFSET);
                $countSelect->reset(Zend_Db_Select::COLUMNS);

                $countSelect->columns('COUNT(*)');

                return $countSelect;
            }
        }
    }
?>