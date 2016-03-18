<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Flags
 */
class Amasty_Flags_Block_Adminhtml_Flag_Edit_Tab_Main extends Mage_Adminhtml_Block_Widget_Form implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    protected function _prepareForm()
    {
        /* @var $model Amasty_Flags_Model_Flag */
        $model = Mage::registry('amflags_flag');

        $form = new Varien_Data_Form();

        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>Mage::helper('cms')->__('Flag Information')));

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
            'note'  => Mage::helper('amflags')->__('Only letters, numbers and underscore'),
        ));
        
        
        $iconUrl  = $model->getId() ? Amasty_Flags_Model_Flag::getUploadUrl() . $model->getId() . '.jpg' : '';
        $iconHtml = $model->getId() ? '<img src="' . $iconUrl . '" title="" alt="" border="0" style="position: relative; left: 6px; top: 6px;" />' : '';
        $size = Mage::helper('amflags')->getIconSize();
        $fieldset->addField('flag_image', 'file', array(
            'label'     => Mage::helper('amflags')->__('Icon Image'),
            'name'      => 'flag_image',
            'note'      => Mage::helper('amflags')->__("JPG, PNG or GIF. {$size}x{$size} pixels strongly recommended. Images of different size will break design."),
            
            'after_element_html' => $iconHtml,
        ));
      
        $fieldset->addField('priority', 'text', array(
            'name'      => 'priority',
            'label'     => Mage::helper('amflags')->__('Priority'),
            'title'     => Mage::helper('amflags')->__('Priority'),
            'class'     => 'validate-number',
            'note'      => Mage::helper('amflags')->__('Numeric value for internal use.'),
        ));
        
        $fieldset->addField('comment', 'textarea', array(
            'name'      => 'comment',
            'label'     => Mage::helper('amflags')->__('Comments'),
            'title'     => Mage::helper('amflags')->__('Comments'),
            'note'      => Mage::helper('amflags')->__('Only letters, numbers, spaces and underscore'),
        ));

        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }
    
    public function getTabLabel()
    {
        return Mage::helper('amflags')->__('Flag Information');
    }
    
    public function getTabTitle()
    {
        return Mage::helper('amflags')->__('Flag Information');
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