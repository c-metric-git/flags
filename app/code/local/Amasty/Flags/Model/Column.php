<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Flags
 */
class Amasty_Flags_Model_Column extends Mage_Core_Model_Abstract
{
    const FLAGS_FOLDER = 'amflags';
    
    protected function _construct()
    {
        $this->_init('amflags/column');
    }
    
    public function delete()
    {
        // removing links with orders
        Mage::getModel('amflags/order_flag')->removeLinksByColumnId($this);
        return parent::delete();
    }
}