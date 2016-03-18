<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Flags
 */
class Amasty_Flags_Block_Adminhtml_Flag_Edit_Tab_Auto extends Mage_Adminhtml_Block_Widget_Form implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    protected function _prepareForm()
    {
        /* @var $model Amasty_Flags_Model_Flag */
        $model = Mage::registry('amflags_flag');

        $form = new Varien_Data_Form();

        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>Mage::helper('cms')->__('Automatically Apply On Order Status Change And Selected Shipping Method')));

        $fieldset->addType(
            'partial_multiselect',
            'Amasty_Flags_Block_Adminhtml_Element_Multiselect'
        );

        $statuses = Mage::getSingleton('sales/order_config')->getStatuses();
        
        $values   = array();
        foreach ($statuses as $code => $name)
        {
            $values[] = array('value' => $code, 'label' => $name);
        }
        
        $fieldset->addField('apply_status', 'multiselect', array(
            'name'      => 'apply_status',
            'label'     => Mage::helper('amflags')->__('Order Status'),
            'title'     => Mage::helper('amflags')->__('Order Status'),
            'values'    => $values,
            'note'      => $this->__('Set flag if order changes to one of selected statuses'),
        ));
        
        // shipping methods
        $methods = Mage::getSingleton('adminhtml/system_config_source_shipping_allmethods')
            ->toOptionArray();

        $flags = Mage::getModel('amflags/flag')->getCollection()->getData();
        
        // disable shipping methods, selected in other flags
        $appliedMethods = array();
        foreach ($flags as $i => $flag) {
            if ($flag['entity_id'] != $model->getData('entity_id') && $flag['apply_shipping']) {
                $appliedMethods = array_merge(
                    $appliedMethods,
                    explode(',',$flag['apply_shipping'])
                );
            }
        }

        foreach ($methods as &$carrier) {
            if (!is_array($carrier['value']))
                continue;

            foreach ($carrier['value'] as &$method) {
                if (in_array($method['value'], $appliedMethods)) {
                    $method['disabled'] = true;
                }
            }
            unset($method);
        }

        $fieldset->addField('apply_shipping', 'partial_multiselect', array(
            'name'      => 'apply_shipping',
            'label'     => Mage::helper('amflags')->__('Order Shipping Method'),
            'title'     => Mage::helper('amflags')->__('Order Shipping Method'),
            'values'    => $methods,
            'note'      => $this->__('Set flag if in the order used one of selected shipping methods. Each shipping method can be selected for only one flag.'),
        ));
        
        // payment methods
        $methods = Mage::getStoreConfig('payment');
        
        // disable payment methods, selected in other flags
        foreach ($flags as $i => $flag) {
            if ($flag['entity_id'] != $model->getData('entity_id')) {
                $appliedMethods = explode(',',$flag['apply_payment']);
                if ($appliedMethods) {
                    foreach ($appliedMethods as $j => $method) {
                        $methods[$method]['disabled'] = true;
                    }
                }
            }
        }
        
        $values   = array();
        foreach ($methods as $code => $method) {
            $value = array('value' => $code);

            if (isset($method['title'])) {
                $value['label'] = $method['title'];
            } else {
                $value['label'] = $code;
            }

            if (isset($method['disabled'])) {
                $value['disabled'] = $method['disabled'];
            }

            $values[] = $value;
        }
        
        $fieldset->addField('apply_payment', 'partial_multiselect', array(
            'name'      => 'apply_payment',
            'label'     => Mage::helper('amflags')->__('Order Payment Method'),
            'title'     => Mage::helper('amflags')->__('Order Payment Method'),
            'values'    => $values,
            'note'      => $this->__('Set flag if in the order used one of selected payment methods. Each payment method can be selected for only one flag.'),
        ));
        
        $columns = Mage::getModel('amflags/column')->getCollection();
    	$values   = array();
        foreach ($columns as $column) {
            $values[] = array('value' => $column->getEntityId(), 'label' => $column->getAlias());
        }
        
        $fieldset->addField('apply_column', 'select', array(
            'name'      => 'apply_column',
            'label'     => Mage::helper('amflags')->__('Column name'),
            'title'     => Mage::helper('amflags')->__('Column name'),
            'values'    => $values,
            'note'      => $this->__('Assign to column'),
        ));
        
        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }
    
    public function getTabLabel()
    {
        return Mage::helper('amflags')->__('Automatic Apply');
    }
    
    public function getTabTitle()
    {
        return Mage::helper('amflags')->__('Automatic Apply');
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
