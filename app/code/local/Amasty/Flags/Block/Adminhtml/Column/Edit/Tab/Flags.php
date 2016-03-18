<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Flags
 */
class Amasty_Flags_Block_Adminhtml_Column_Edit_Tab_Flags extends Mage_Adminhtml_Block_Widget_Form implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    protected function _prepareForm()
    {
        /* @var $model Amasty_Flags_Model_Column */
        $model = Mage::registry('amflags_column');

        $form = new Varien_Data_Form();

        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>Mage::helper('cms')->__('Apply Flags To Column')));

        $flags = Mage::getModel('amflags/flag')->getCollection();
        
        $values   = array();
        foreach ($flags as $flag)
        {
            $url = Amasty_Flags_Model_Flag::getUploadUrl() . $flag->getEntityId() . '.jpg';
            $style = 'background-image:url('. $url .'); background-repeat:no-repeat; padding-left:25px;';
            $values[] = array('value' => $flag->getEntityId(), 'label' => $flag->getAlias(), 'style' => $style);
        }
                
        $fieldset->addField('apply_flag', 'multiselect', array(
            'name'      => 'apply_flag',
            'label'     => Mage::helper('amflags')->__('Flags'),
            'title'     => Mage::helper('amflags')->__('Flags'),
            'values'    => $values,
            'note'      => $this->__('Set flags to column'),
        ));
        
        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }
    
    public function getTabLabel()
    {
        return Mage::helper('amflags')->__('Apply Flags To Column');
    }
    
    public function getTabTitle()
    {
        return Mage::helper('amflags')->__('Apply Flags To Column');
    }
    
    public function canShowTab()
    {
        return true;
    }
    
    public function isHidden()
    {
        return false;
    }
}