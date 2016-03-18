<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at http://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   Reward Points + Referral program
 * @version   1.1.2
 * @build     928
 * @copyright Copyright (C) 2015 Mirasvit (http://mirasvit.com/)
 */



class Mirasvit_Rewards_Block_Adminhtml_Earning_Rule_Edit_Tab_General extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        /** @var Mirasvit_Rewards_Model_Earning_Rule $earningRule */
        $earningRule = Mage::registry('current_earning_rule');

        $fieldset = $form->addFieldset('edit_fieldset', array('legend' => Mage::helper('rewards')->__('General Information')));
        if ($earningRule->getId()) {
            $fieldset->addField('earning_rule_id', 'hidden', array(
                'name' => 'earning_rule_id',
                'value' => $earningRule->getId(),
            ));
        }
        $fieldset->addField('store_id', 'hidden', array(
            'name' => 'store_id',
            'value' => (int) $this->getRequest()->getParam('store'),
        ));

        $fieldset->addField('name', 'text', array(
            'label' => Mage::helper('rewards')->__('Rule Name'),
            'required' => true,
            'name' => 'name',
            'value' => $earningRule->getName(),
        ));
        $fieldset->addField('type', 'select', array(
            'label' => Mage::helper('rewards')->__('Type'),
            'required' => true,
            'name' => 'type',
            'value' => $earningRule->getType(),
            'values' => Mage::getSingleton('rewards/config_source_type')->toOptionArray(),
            'disabled' => $earningRule->getType() != false,
        ));
        $fieldset->addField('is_active', 'select', array(
            'label' => Mage::helper('rewards')->__('Is Active'),
            'required' => true,
            'name' => 'is_active',
            'value' => $earningRule->getIsActive(),
            'values' => Mage::getSingleton('adminhtml/system_config_source_yesno')->toOptionArray(),
        ));
        $fieldset->addField('active_from', 'date', array(
            'label' => Mage::helper('rewards')->__('Active From'),
            'name' => 'active_from',
            'value' => $earningRule->getActiveFrom(),
            'image' => $this->getSkinUrl('images/grid-cal.gif'),
            'format' => Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT),
            'time' => true,
        ));
        $fieldset->addField('active_to', 'date', array(
            'label' => Mage::helper('rewards')->__('Active To'),
            'name' => 'active_to',
            'value' => $earningRule->getActiveTo(),
            'image' => $this->getSkinUrl('images/grid-cal.gif'),
            'format' => Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT),
            'time' => true,
        ));
        if (!Mage::app()->isSingleStoreMode()) {
            $fieldset->addField('website_ids', 'multiselect', array(
                'name' => 'website_ids[]',
                'label' => Mage::helper('rewards')->__('Websites'),
                'title' => Mage::helper('rewards')->__('Websites'),
                'required' => true,
                'values' => Mage::getSingleton('adminhtml/system_config_source_website')->toOptionArray(),
                'value' => $earningRule->getWebsiteIds(),
            ));
        } else {
            $fieldset->addField('website_ids', 'hidden', array(
                'name' => 'website_ids',
                'value' => Mage::app()->getStore(true)->getWebsiteId(),
            ));
            $earningRule->setWebsiteId(Mage::app()->getStore(true)->getWebsiteId());
        }
        $fieldset->addField('customer_group_ids', 'multiselect', array(
            'label' => Mage::helper('rewards')->__('Customer Groups'),
            'required' => true,
            'name' => 'customer_group_ids[]',
            'value' => $earningRule->getCustomerGroupIds(),
            'values' => Mage::getModel('customer/group')->getCollection()->toOptionArray(),
        ));
        $fieldset->addField('is_stop_processing', 'select', array(
            'label' => Mage::helper('rewards')->__('Stop further rules processing'),
            'name' => 'is_stop_processing',
            'value' => $earningRule->getIsStopProcessing(),
            'values' => Mage::getSingleton('adminhtml/system_config_source_yesno')->toOptionArray(),
        ));
        $fieldset->addField('sort_order', 'text', array(
            'label' => Mage::helper('rewards')->__('Priority'),
            'name' => 'sort_order',
            'value' => (int) $earningRule->getSortOrder(),
            'note' => Mage::helper('rewards')->__('Arranged in the ascending order. 0 is the highest.'),
        ));

        return parent::_prepareForm();
    }

    /************************/
}
