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



class Mirasvit_Rewards_Block_Adminhtml_Earning_Rule_Edit_Tab_Cart extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        /** @var Mirasvit_Rewards_Model_Earning_Rule $earningRule */
        $earningRule = Mage::registry('current_earning_rule');

        $fieldset = $form->addFieldset('action_fieldset', array('legend' => Mage::helper('rewards')->__('Actions')));
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

        $fieldset->addField('earning_style', 'select', array(
            'label' => Mage::helper('rewards')->__('Customer Earning Style'),
            'required' => true,
            'name' => 'earning_style',
            'value' => $earningRule->getEarningStyle(),
            'values' => Mage::getSingleton('rewards/system_source_cartearningstyle')->toArray(),
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
        $fieldset->addField('qty_step', 'text', array(
            'label' => Mage::helper('rewards')->__('Quantity Step (Z)'),
            'required' => true,
            'name' => 'qty_step',
            'value' => $earningRule->getQtyStep(),
        ));
        $fieldset->addField('points_limit', 'text', array(
            'label' => Mage::helper('rewards')->__('Earn Maximum'),
            'name' => 'points_limit',
            'value' => $earningRule->getPointsLimit() == 0 ? '' : $earningRule->getPointsLimit(),
            'note' => 'You can enter amount of points or percent. Leave empty to disable.',
        ));
        $this->setChild('form_after',
            $this->getLayout()->createBlock('adminhtml/widget_form_element_dependence')
            ->addFieldMap('earning_style', 'earning_style')
            ->addFieldMap('monetary_step', 'monetary_step')
            ->addFieldMap('qty_step', 'qty_step')
            ->addFieldMap('points_limit', 'points_limit')
            ->addFieldDependence('monetary_step', 'earning_style', Mirasvit_Rewards_Model_Config::EARNING_STYLE_AMOUNT_SPENT)
            ->addFieldDependence('qty_step', 'earning_style', Mirasvit_Rewards_Model_Config::EARNING_STYLE_QTY_SPENT)
        );

        //Apply the rule only to cart items matching the following conditions (leave blank for all items)
        $renderer = Mage::getBlockSingleton('adminhtml/widget_form_renderer_fieldset')
            ->setTemplate('promo/fieldset.phtml')
            ->setNewChildUrl($this->getUrl('adminhtml/promo_quote/newActionHtml/form/rule_actions_fieldset'));

        $fieldset = $form->addFieldset('rule_actions_fieldset', array(
            'legend' => Mage::helper('salesrule')->__('Apply the rule only to cart items matching the following conditions (leave blank for all items)'),
        ))->setRenderer($renderer);

        $fieldset->addField('actions', 'text', array(
            'name' => 'actions',
            'label' => Mage::helper('salesrule')->__('Apply To'),
            'title' => Mage::helper('salesrule')->__('Apply To'),
            'required' => true,
        ))->setRule($earningRule)->setRenderer(Mage::getBlockSingleton('rule/actions'));

        return parent::_prepareForm();
    }

    /************************/
}
