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



class Mirasvit_Rewards_Block_Adminhtml_Earning_Rule_Edit_Tab_Behavior extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        /** @var Mirasvit_Rewards_Model_Earning_Rule $earningRule */
        $earningRule = Mage::registry('current_earning_rule');

        $fieldset = $form->addFieldset('rule_conditions_fieldset', array('legend' => Mage::helper('rewards')->__('Conditions')));
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

        $fieldset->addField('behavior_trigger', 'select', array(
            'label' => Mage::helper('rewards')->__('Event'),
            'required' => true,
            'name' => 'behavior_trigger',
            'value' => $earningRule->getBehaviorTrigger(),
            'values' => Mage::getSingleton('rewards/config_source_behavior_trigger')->toOptionArray(),
        ));
        $fieldset->addField('inactivity_period', 'text', array(
            'label' => Mage::helper('rewards')->__('Number of Inactive Days'),
            'required' => true,
            'name' => 'param1',
            'value' => $earningRule->getParam1(),
        ));
        $fieldset = $form->addFieldset('action_fieldset', array('legend' => Mage::helper('rewards')->__('Actions')));

        $fieldset->addField('earning_style', 'select', array(
            'label' => Mage::helper('rewards')->__('Customer Earning Style'),
            'required' => true,
            'name' => 'earning_style',
            'value' => $earningRule->getEarningStyle(),
            'values' => Mage::getSingleton('rewards/system_source_cartearningstyle')->toArray(),
        ));

        $fieldset->addField('earn_points', 'text', array(
            'label' => Mage::helper('rewards')->__('Number of points to give (X)'),
            'required' => true,
            'name' => 'earn_points',
            'value' => $earningRule->getEarnPoints(),
        ));

        $fieldset->addField('monetary_step', 'text', array(
            'label' => Mage::helper('rewards')->__('Step (Y)'),
            'required' => true,
            'name' => 'monetary_step',
            'value' => $earningRule->getMonetaryStep(),
            'note' => 'in base currency',
        ));

        $fieldset->addField('qty_step', 'text', array(
            'label' => Mage::helper('rewards')->__('Quantity Step (Z)'),
            'required' => true,
            'name' => 'qty_step',
            'value' => $earningRule->getQtyStep(),
        ));

        $fieldset->addField('points_limit', 'text', array(
            'label' => Mage::helper('rewards')->__('Maximum number of earned points for one customer per day'),
            'name' => 'points_limit',
            'value' => $earningRule->getPointsLimit(),
            'note' => Mage::helper('rewards')->__('Set 0 to disable limit'),
        ));

        $renderer = Mage::getBlockSingleton('adminhtml/widget_form_renderer_fieldset')
            ->setTemplate('promo/fieldset.phtml')
            ->setNewChildUrl($this->getUrl('*/*/newConditionHtml/form/rule_conditions_fieldset'));

        $fieldset = $form->addFieldset('conditions_fieldset', array(
            'legend' => Mage::helper('salesrule')->__('Apply the rule only if the following conditions are met'),
        ))->setRenderer($renderer);

        $fieldset->addField('conditions', 'text', array(
            'name' => 'conditions',
            'label' => Mage::helper('salesrule')->__('Conditions'),
            'title' => Mage::helper('salesrule')->__('Conditions'),
        ))->setRule($earningRule)->setRenderer(Mage::getBlockSingleton('rule/conditions'));

        return parent::_prepareForm();
    }

    /************************/

    protected function _toHtml()
    {
        $html = parent::_toHtml();
        $block = $this->getLayout()->createBlock('adminhtml/widget_form_element_dependence')
            ->addFieldMap('behavior_trigger', 'behavior_trigger')
            ->addFieldMap('inactivity_period', 'inactivity_period')
            ->addFieldMap('earning_style', 'earning_style')
            ->addFieldDependence('inactivity_period', 'behavior_trigger', Mirasvit_Rewards_Model_Config::BEHAVIOR_TRIGGER_INACTIVITY)
            ->addFieldDependence('earning_style', 'behavior_trigger', Mirasvit_Rewards_Model_Config::BEHAVIOR_TRIGGER_REFERRED_CUSTOMER_ORDER)
            ;
        $html .= $block->toHtml();

        $block = $this->getLayout()->createBlock('adminhtml/widget_form_element_dependence')
            ->addFieldMap('earning_style', 'earning_style')
            ->addFieldMap('monetary_step', 'monetary_step')
            ->addFieldMap('qty_step', 'qty_step')
            ->addFieldMap('behavior_trigger', 'behavior_trigger')
            ->addFieldMap('points_limit', 'points_limit')
            ->addFieldDependence('monetary_step', 'behavior_trigger', Mirasvit_Rewards_Model_Config::BEHAVIOR_TRIGGER_REFERRED_CUSTOMER_ORDER)
            ->addFieldDependence('qty_step', 'behavior_trigger', Mirasvit_Rewards_Model_Config::BEHAVIOR_TRIGGER_REFERRED_CUSTOMER_ORDER)
            ->addFieldDependence('monetary_step', 'earning_style', Mirasvit_Rewards_Model_Config::EARNING_STYLE_AMOUNT_SPENT)
            ->addFieldDependence('qty_step', 'earning_style', Mirasvit_Rewards_Model_Config::EARNING_STYLE_QTY_SPENT)
            ;
        $html .= $block->toHtml();

        return $html;
    }
}
