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



class Mirasvit_Rewards_Block_Adminhtml_Spending_Rule_Edit_Tab_Cart extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        /** @var Mirasvit_Rewards_Model_Spending_Rule $spendingRule */
        $spendingRule = Mage::registry('current_spending_rule');

        $fieldset = $form->addFieldset('action_fieldset', array('legend' => Mage::helper('rewards')->__('Actions')));

        $fieldset->addField('spend_points', 'text', array(
            'label' => Mage::helper('rewards')->__('For each spent X points'),
            'required' => true,
            'name' => 'spend_points',
            'value' => $spendingRule->getSpendPoints(),
            'note' => 'number of points.',
        ));

        $fieldset->addField('monetary_step', 'text', array(
            'label' => Mage::helper('rewards')->__('Customer receive Y discount'),
            'required' => true,
            'name' => 'monetary_step',
            'value' => $spendingRule->getMonetaryStep(),
            'note' => 'in base currency.',
        ));

        $fieldset->addField('spend_min_points', 'text', array(
            'label' => Mage::helper('rewards')->__('Spend minimum'),
            'name' => 'spend_min_points',
            'value' => $spendingRule->getSpendMinPoints(),
            'note' => 'You can enter amount of points or percent. e.g. 100 or 5%. Leave empty to disable.',
        ));

        $fieldset->addField('spend_max_points', 'text', array(
            'label' => Mage::helper('rewards')->__('Spend maximum'),
            'name' => 'spend_max_points',
            'value' => $spendingRule->getSpendMaxPoints(),
            'note' => 'You can enter amount of points or percent. e.g. 100 or 5%. Leave empty to disable.',
        ));

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
        ))->setRule($spendingRule)->setRenderer(Mage::getBlockSingleton('rule/actions'));

        return parent::_prepareForm();
    }

    /************************/
}
