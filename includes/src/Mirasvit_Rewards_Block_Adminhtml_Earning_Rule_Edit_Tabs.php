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



class Mirasvit_Rewards_Block_Adminhtml_Earning_Rule_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('earning_rule_tabs');
        $this->setDestElementId('edit_form');
    }

    protected function _beforeToHtml()
    {
        $this->addTab('general_section', array(
            'label' => Mage::helper('rewards')->__('General Information'),
            'title' => Mage::helper('rewards')->__('General Information'),
            'content' => $this->getLayout()->createBlock('rewards/adminhtml_earning_rule_edit_tab_general')->toHtml(),
        ));
        $earningRule = Mage::registry('current_earning_rule');
        if (!$earningRule->getType()) {
            return parent::_beforeToHtml();
        }
        switch ($earningRule->getType()) {
            case Mirasvit_Rewards_Model_Earning_Rule::TYPE_PRODUCT:
                $this->addTab('product_section', array(
                    'label' => Mage::helper('rewards')->__('Conditions & Actions'),
                    'title' => Mage::helper('rewards')->__('Conditions & Actions'),
                    'content' => $this->getLayout()->createBlock('rewards/adminhtml_earning_rule_edit_tab_product')->toHtml(),
                ));
                break;
            case Mirasvit_Rewards_Model_Earning_Rule::TYPE_CART:
                $this->addTab('conditions_section', array(
                    'label' => Mage::helper('rewards')->__('Conditions'),
                    'title' => Mage::helper('rewards')->__('Conditions'),
                    'content' => $this->getLayout()->createBlock('rewards/adminhtml_earning_rule_edit_tab_conditions')->toHtml(),
                ));
                $this->addTab('cart_section', array(
                    'label' => Mage::helper('rewards')->__('Actions'),
                    'title' => Mage::helper('rewards')->__('Actions'),
                    'content' => $this->getLayout()->createBlock('rewards/adminhtml_earning_rule_edit_tab_cart')->toHtml(),
                ));
                break;
            case Mirasvit_Rewards_Model_Earning_Rule::TYPE_BEHAVIOR:
                $this->addTab('behavior_section', array(
                    'label' => Mage::helper('rewards')->__('Conditions & Actions'),
                    'title' => Mage::helper('rewards')->__('Conditions & Actions'),
                    'content' => $this->getLayout()->createBlock('rewards/adminhtml_earning_rule_edit_tab_behavior')->toHtml(),
                ));
                $this->addTab('notification_section', array(
                    'label' => Mage::helper('rewards')->__('Notifications'),
                    'title' => Mage::helper('rewards')->__('Notifications'),
                    'content' => $this->getLayout()->createBlock('rewards/adminhtml_earning_rule_edit_tab_notification')->toHtml(),
                ));
                break;
        }

        return parent::_beforeToHtml();
    }

    /************************/
}
