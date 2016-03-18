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


class Mirasvit_Rewards_Model_Earning_Rule_Condition_Combine extends Mage_Rule_Model_Condition_Combine
{
    public function __construct()
    {
        parent::__construct();
        $this->setType('rewards/earning_rule_condition_combine');
    }

    public function getNewChildSelectOptions()
    {
        if ($this->getRule()->getType()) {
            $type = $this->getRule()->getType();
        } else {
            $type = Mage::app()->getRequest()->getParam('rule_type');
        }

        if ($type == Mirasvit_Rewards_Model_Earning_Rule::TYPE_CART) {
            return $this->_getCartConditions();
        } elseif ($type == Mirasvit_Rewards_Model_Earning_Rule::TYPE_BEHAVIOR) {
            $itemAttributes = $this->_getCustomerAttributes();
            $attributes = $this->convertToAttributes($itemAttributes, 'customer', 'Customer');
            if ($this->getRule()->getBehaviorTrigger() == Mirasvit_Rewards_Model_Config::BEHAVIOR_TRIGGER_REFERRED_CUSTOMER_ORDER) {
                $itemAttributes = $this->_getReferredCustomerAttributes();
                $attributes2 = $this->convertToAttributes($itemAttributes, 'referred_customer', 'Referred Customer');
                $attributes = array_merge_recursive($attributes, $attributes2);
            }
        } else {
            $itemAttributes = $this->_getProductAttributes();
            $attributes = $this->convertToAttributes($itemAttributes, 'product', 'Product Attributes');
        }

        $conditions = parent::getNewChildSelectOptions();
        $conditions = array_merge_recursive($conditions, array(
            array(
                'value' => 'rewards/earning_rule_condition_combine',
                'label' => Mage::helper('rewards')->__('Conditions Combination')
            )
        ));

        foreach ($attributes as $group => $arrAttributes) {
            $conditions = array_merge_recursive($conditions, array(
                array(
                    'label' => $group,
                    'value' => $arrAttributes
                ),
            ));
        }

        return $conditions;
    }

    protected function convertToAttributes($itemAttributes, $condition, $group) {
        $attributes = array();
        foreach ($itemAttributes as $code => $label) {
            $attributes[$group][] = array(
                'value' => 'rewards/earning_rule_condition_'.$condition.'|'.$code,
                'label' => $label
            );
        }
        return $attributes;
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
        $productCondition  = Mage::getModel('rewards/earning_rule_condition_product');
        $productAttributes = $productCondition->loadAttributeOptions()->getAttributeOption();

        return $productAttributes;
    }

    protected function _getCartConditions()
    {
        $addressCondition = Mage::getModel('salesrule/rule_condition_address');
        $addressAttributes = $addressCondition->loadAttributeOptions()->getAttributeOption();
        $attributes = array();
        foreach ($addressAttributes as $code=>$label) {
            $attributes[] = array('value'=>'salesrule/rule_condition_address|'.$code, 'label'=>$label);
        }


        $conditions = parent::getNewChildSelectOptions();
        $conditions = array_merge_recursive($conditions, array(
            array('value'=>'salesrule/rule_condition_product_found', 'label'=>Mage::helper('salesrule')->__('Product attribute combination')),
            array('value'=>'salesrule/rule_condition_product_subselect', 'label'=>Mage::helper('salesrule')->__('Products subselection')),
            array('value'=>'salesrule/rule_condition_combine', 'label'=>Mage::helper('salesrule')->__('Conditions combination')),
            array('label'=>Mage::helper('salesrule')->__('Cart Attribute'), 'value'=>$attributes),
        ));

        $additional = new Varien_Object();
        Mage::dispatchEvent('salesrule_rule_condition_combine', array('additional' => $additional));
        if ($additionalConditions = $additional->getConditions()) {
            $conditions = array_merge_recursive($conditions, $additionalConditions);
        }

        return $conditions;
    }

    protected function _getCustomerAttributes()
    {
        $customerCondition  = Mage::getModel('rewards/earning_rule_condition_customer');
        $customerAttributes = $customerCondition->loadAttributeOptions()->getAttributeOption();

        return $customerAttributes;
    }

    protected function _getReferredCustomerAttributes()
    {
        $customerCondition  = Mage::getModel('rewards/earning_rule_condition_referred_customer');
        $customerAttributes = $customerCondition->loadAttributeOptions()->getAttributeOption();

        return $customerAttributes;
    }
}