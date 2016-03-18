<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Flags
 */
class Amasty_Flags_Block_Adminhtml_Flag_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('amflags_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('cms')->__('Flag Information'));
    }
    
    protected function _beforeToHtml()
    {
        $this->addTab('form_section', array(
            'label'     => Mage::helper('amflags')->__('General Information'),
            'title'     => Mage::helper('amflags')->__('General Information'),
            'content'   => $this->getLayout()->createBlock('amflags/adminhtml_flag_edit_tab_main')->toHtml(),
        ));
        $this->addTab('apply_section', array(
            'label'     => Mage::helper('amflags')->__('Automatic Apply'),
            'title'     => Mage::helper('amflags')->__('Automatic Apply'),
            'content'   => $this->getLayout()->createBlock('amflags/adminhtml_flag_edit_tab_auto')->toHtml(),
        ));
        return parent::_beforeToHtml();
    }
}