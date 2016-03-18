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



class Mirasvit_Rewards_Model_Spending_Rule_Condition_Combine extends Mage_Rule_Model_Condition_Combine
{
    public function __construct()
    {
        parent::__construct();
        $this->setType('rewards/spending_rule_condition_combine');
    }

    public function getNewChildSelectOptions()
    {
        // if ($this->getRule()->getType()) {
        //     $type = $this->getRule()->getType();
        // } else {
        //     $type = Mage::app()->getRequest()->getParam('rule_type');
        // }
        $type = Mirasvit_Rewards_Model_Spending_Rule::TYPE_CART;
        if ($type == Mirasvit_Rewards_Model_Spending_Rule::TYPE_CUSTOM) {
            $itemAttributes = $this->_getCustomAttributes();
            $condition = 'custom';
        } elseif ($type == Mirasvit_Rewards_Model_Spending_Rule::TYPE_CART) {
            return $this->_getCartConditions();
        } else {
            $itemAttributes = $this->_getProductAttributes();
            $condition = 'product';
        }

        $attributes = array();
        foreach ($itemAttributes as $code => $label) {
            $group = Mage::helper('rewards/rule')->getAttributeGroup($code);
            $attributes[$group][] = array(
                'value' => 'rewards/spending_rule_condition_'.$condition.'|'.$code,
                'label' => $label,
            );
        }

        $conditions = parent::getNewChildSelectOptions();
        $conditions = array_merge_recursive($conditions, array(
            array(
                'value' => 'rewards/spending_rule_condition_combine',
                'label' => Mage::helper('rewards')->__('Conditions Combination'),
            ),
        ));

        foreach ($attributes as $group => $arrAttributes) {
            $conditions = array_merge_recursive($conditions, array(
                array(
                    'label' => $group,
                    'value' => $arrAttributes,
                ),
            ));
        }

        return $conditions;
    }

    public function collectValidatedAttributes($productCollection)
    {
        foreach ($this->getConditions() as $condition) {
            $condition->collectValidatedAttributes($productCollection);
        }

        return $this;
    }

    protected function _getProductAttributes()
    {
        $productCondition = Mage::getModel('rewards/spending_rule_condition_product');
        $productAttributes = $productCondition->loadAttributeOptions()->getAttributeOption();

        return $productAttributes;
    }

    protected function _getCartConditions()
    {
        $addressCondition = Mage::getModel('salesrule/rule_condition_address');
        $addressAttributes = $addressCondition->loadAttributeOptions()->getAttributeOption();
        $attributes = array();
        foreach ($addressAttributes as $code => $label) {
            $attributes[] = array('value' => 'salesrule/rule_condition_address|'.$code, 'label' => $label);
        }

        $conditions = parent::getNewChildSelectOptions();
        $conditions = array_merge_recursive($conditions, array(
            array('value' => 'salesrule/rule_condition_product_found', 'label' => Mage::helper('salesrule')->__('Product attribute combination')),
            array('value' => 'salesrule/rule_condition_product_subselect', 'label' => Mage::helper('salesrule')->__('Products subselection')),
            array('value' => 'salesrule/rule_condition_combine', 'label' => Mage::helper('salesrule')->__('Conditions combination')),
            array('label' => Mage::helper('salesrule')->__('Cart Attribute'), 'value' => $attributes),
        ));

        $additional = new Varien_Object();
        Mage::dispatchEvent('salesrule_rule_condition_combine', array('additional' => $additional));
        if ($additionalConditions = $additional->getConditions()) {
            $conditions = array_merge_recursive($conditions, $additionalConditions);
        }

        return $conditions;
    }

    protected function _getCustomAttributes()
    {
        $customCondition = Mage::getModel('rewards/spending_rule_condition_custom');
        $customAttributes = $customCondition->loadAttributeOptions()->getAttributeOption();

        return $customAttributes;
    }
}
