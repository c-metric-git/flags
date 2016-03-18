<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Flags
 */
class Amasty_Flags_Block_Adminhtml_Column extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'amflags';
        $this->_controller = 'adminhtml_column';
        $this->_headerText = Mage::helper('cms')->__('Columns');
        parent::__construct();
        $this->_updateButton('add', 'label', Mage::helper('amflags')->__('Add New Column'));
    }
}