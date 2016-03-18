<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Flags
 */
class Amasty_Flags_Block_Adminhtml_Flag_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        $this->_objectId   = 'flag_id';
        $this->_blockGroup = 'amflags';
        $this->_controller = 'adminhtml_flag';

        parent::__construct();

        $this->_updateButton('save', 'label', Mage::helper('amflags')->__('Save Flag'));
        $this->_updateButton('delete', 'label', Mage::helper('amflags')->__('Delete Flag'));
    }
    
    public function getHeaderText()
    {
        if (Mage::registry('amflags_flag')->getId()) {
            return Mage::helper('cms')->__("Edit Flag '%s'", $this->htmlEscape(Mage::registry('amflags_flag')->getAlias()));
        }
        else {
            return Mage::helper('cms')->__('New Flag');
        }
    }

    public function getSaveUrl()
    {
        return $this->getUrl('*/*/save', array('_current'=>true, 'back'=>null));
    }
}