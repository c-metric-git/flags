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



class Mirasvit_Rewards_Block_Adminhtml_Spending_Rule_Edit_Tab_General extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        /** @var Mirasvit_Rewards_Model_Spending_Rule $spendingRule */
        $spendingRule = Mage::registry('current_spending_rule');

        $fieldset = $form->addFieldset('edit_fieldset', array('legend' => Mage::helper('rewards')->__('General Information')));
        if ($spendingRule->getId()) {
            $fieldset->addField('spending_rule_id', 'hidden', array(
                'name' => 'spending_rule_id',
                'value' => $spendingRule->getId(),
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
            'value' => $spendingRule->getName(),
        ));
        $fieldset->addField('is_active', 'select', array(
            'label' => Mage::helper('rewards')->__('Is Active'),
            'required' => true,
            'name' => 'is_active',
            'value' => $spendingRule->getIsActive(),
            'values' => Mage::getSingleton('adminhtml/system_config_source_yesno')->toOptionArray(),
        ));
        $fieldset->addField('active_from', 'date', array(
            'label' => Mage::helper('rewards')->__('Active From'),
            'name' => 'active_from',
            'value' => $spendingRule->getActiveFrom(),
            'image' => $this->getSkinUrl('images/grid-cal.gif'),
            'format' => Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT),
            'time' => true,
        ));
        $fieldset->addField('active_to', 'date', array(
            'label' => Mage::helper('rewards')->__('Active To'),
            'name' => 'active_to',
            'value' => $spendingRule->getActiveTo(),
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
                'value' => $spendingRule->getWebsiteIds(),
            ));
        } else {
            $fieldset->addField('website_ids', 'hidden', array(
                'name' => 'website_ids',
                'value' => Mage::app()->getStore(true)->getWebsiteId(),
            ));
            $spendingRule->setWebsiteId(Mage::app()->getStore(true)->getWebsiteId());
        }
        $fieldset->addField('customer_group_ids', 'multiselect', array(
            'label' => Mage::helper('rewards')->__('Customer Groups'),
            'required' => true,
            'name' => 'customer_group_ids[]',
            'value' => $spendingRule->getCustomerGroupIds(),
            'values' => Mage::getModel('customer/group')->getCollection()->toOptionArray(),
        ));
        $fieldset->addField('is_stop_processing', 'select', array(
            'label' => Mage::helper('rewards')->__('Stop further rules processing'),
            'name' => 'is_stop_processing',
            'value' => $spendingRule->getIsStopProcessing(),
            'values' => Mage::getSingleton('adminhtml/system_config_source_yesno')->toOptionArray(),
        ));
        $fieldset->addField('sort_order', 'text', array(
            'label' => Mage::helper('rewards')->__('Priority'),
            'name' => 'sort_order',
            'value' => $spendingRule->getSortOrder(),
            'note' => Mage::helper('rewards')->__('Arranged in the ascending order. 0 is the highest.'),
        ));

        return parent::_prepareForm();
    }

    /************************/
}
