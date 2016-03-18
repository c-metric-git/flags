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



class Mirasvit_Rewards_Block_Adminhtml_Earning_Rule_Edit_Tab_Product extends Mage_Adminhtml_Block_Widget_Form
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

        $renderer = Mage::getBlockSingleton('adminhtml/widget_form_renderer_fieldset')
            ->setTemplate('promo/fieldset.phtml')
            ->setNewChildUrl(Mage::getModel('adminhtml/url')->getUrl('*/adminhtml_earning_rule/newConditionHtml/form/rule_conditions_fieldset', array('rule_type' => $earningRule->getType())));
        $fieldset->setRenderer($renderer);

        $fieldset->addField('conditions', 'text', array(
            'name' => 'conditions',
            'label' => Mage::helper('rewards')->__('Filters'),
            'title' => Mage::helper('rewards')->__('Filters'),
            'required' => true,
        ))->setRule($earningRule)
            ->setRenderer(Mage::getBlockSingleton('rule/conditions'));

        $fieldset = $form->addFieldset('action_fieldset', array('legend' => Mage::helper('rewards')->__('Actions')));

        $fieldset->addField('earning_style', 'select', array(
            'label' => Mage::helper('rewards')->__('Customer Earning Style'),
            'required' => true,
            'name' => 'earning_style',
            'value' => $earningRule->getEarningStyle(),
            'values' => Mage::getSingleton('rewards/system_source_productearningstyle')->toArray(),
        ));
        $fieldset->addField('earn_points', 'text', array(
            'label' => Mage::helper('rewards')->__('Number of Points (X)'),
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
        $fieldset->addField('points_limit', 'text', array(
            'label' => Mage::helper('rewards')->__('Maximum Distributed Points'),
            'name' => 'points_limit',
            'value' => $earningRule->getPointsLimit(),
        ));

        $this->setChild('form_after',
            $this->getLayout()->createBlock('adminhtml/widget_form_element_dependence')
            ->addFieldMap('earning_style', 'earning_style')
            ->addFieldMap('monetary_step', 'monetary_step')
            ->addFieldMap('points_limit', 'points_limit')
            ->addFieldDependence('monetary_step', 'earning_style', Mirasvit_Rewards_Model_Config::EARNING_STYLE_AMOUNT_PRICE)
            ->addFieldDependence('points_limit', 'earning_style', Mirasvit_Rewards_Model_Config::EARNING_STYLE_AMOUNT_PRICE)
        );

        return parent::_prepareForm();
    }

    /************************/
}
