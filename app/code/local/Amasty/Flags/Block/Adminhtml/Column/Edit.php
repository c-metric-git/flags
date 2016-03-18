<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Flags
 */
class Amasty_Flags_Block_Adminhtml_Column_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        $this->_objectId   = 'column_id';
        $this->_blockGroup = 'amflags';
        $this->_controller = 'adminhtml_column';

        parent::__construct();

        $this->_updateButton('save', 'label', Mage::helper('amflags')->__('Save Column'));
        $this->_updateButton('delete', 'label', Mage::helper('amflags')->__('Delete Column'));
    }
    
    public function getHeaderText()
    {
        if (Mage::registry('amflags_column')->getId())
        {
            return Mage::helper('cms')->__("Edit Column '%s'", $this->htmlEscape(Mage::registry('amflags_column')->getAlias()));
        }
        else
        {
            return Mage::helper('cms')->__('New Column');
        }
    }

    public function getSaveUrl()
    {
        return $this->getUrl('*/*/save', array('_current'=>true, 'back'=>null));
    }
}