<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Flags
 */
class Amasty_Flags_Block_Adminhtml_Column_Edit_Tab_Main extends Mage_Adminhtml_Block_Widget_Form implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    protected function _prepareForm()
    {
        /* @var $model Amasty_Flags_Model_Column */
        $model = Mage::registry('amflags_column');
        
        $form = new Varien_Data_Form();

        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>Mage::helper('cms')->__('Column Information')));

        if ($model->getId()) {
            $fieldset->addField('entity_id', 'hidden', array(
                'name' => 'entity_id',
            ));
        }

        $fieldset->addField('alias', 'text', array(
            'name'      => 'alias',
            'label'     => Mage::helper('amflags')->__('Alias'),
            'title'     => Mage::helper('amflags')->__('Alias'),
            'required'  => true,
        ));
        
        
        $fieldset->addField('pos', 'text', array(
            'name'      => 'pos',
            'label'     => Mage::helper('amflags')->__('Position'),
            'title'     => Mage::helper('amflags')->__('Position'),
            'class'     => 'validate-number',
            'note'      => Mage::helper('amflags')->__('Numeric value for internal use.'),
        ));
        
        $fieldset->addField('comment', 'textarea', array(
            'name'      => 'comment',
            'label'     => Mage::helper('amflags')->__('Comments'),
            'title'     => Mage::helper('amflags')->__('Comments'),
        ));

        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }
    
    public function getTabLabel()
    {
        return Mage::helper('amflags')->__('Column Information');
    }
    
    public function getTabTitle()
    {
        return Mage::helper('amflags')->__('Column Information');
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