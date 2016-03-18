<?php
/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This software is designed to work with Magento community edition and
 * its use on an edition other than specified is prohibited. aheadWorks does not
 * provide extension support in case of incorrect edition use.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Coupongenerator
 * @version    1.0.2
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE.txt
 */

class AW_Coupongenerator_Model_Version_Adaptee_Magento7
{
    /**
     * @param AW_Coupongenerator_Block_Adminhtml_Coupons_Grid $grid
     *
     * @return AW_Coupongenerator_Block_Adminhtml_Coupons_Grid
     */
    public function addColumnsToCouponsGrid(AW_Coupongenerator_Block_Adminhtml_Coupons_Grid $grid)
    {
        $grid->addColumn(
            'created_at',
            array(
                'index' => 'created_at',
                'header' => Mage::helper('coupongenerator')->__('Created On'),
                'type' => 'datetime',
                'width' => '150px'
            )
        );
        return $grid;
    }

    /**
     * @param Mage_SalesRule_Model_Rule $rule
     * @param int $usageLimit
     *
     * @return Mage_SalesRule_Model_Rule
     */
    public function updateCouponUsageLimit(Mage_SalesRule_Model_Rule $rule, $usageLimit)
    {
        return $rule;
    }

    /**
     * @return string
     */
    public function getRuleSaveAndContinueButtonId()
    {
        return 'save_and_continue_edit';
    }

    /**
     * @return Mage_Adminhtml_Block_Widget_Form_Element_Dependence
     */
    public function getFormDependenciesBlock()
    {
        return Mage::app()->getLayout()->createBlock('adminhtml/widget_form_element_dependence');
    }

    /**
     * @param AW_Coupongenerator_Model_Salesrule $awRule
     * @param Varien_Data_Form_Element_Abstract $couponNativeHintField
     * @param Varien_Data_Form_Element_Abstract $emailTemplateField
     *
     * @return $this
     */
    public function updateCouponTypeFormDependencies(
        AW_Coupongenerator_Model_Salesrule $awRule,
        Varien_Data_Form_Element_Abstract $couponNativeHintField,
        Varien_Data_Form_Element_Abstract $emailTemplateField
    ) {
        $couponTypes = $awRule->getOtherCouponTypes();
        $formBlock = Mage::app()->getLayout()->getBlock('promo_quote_edit_tab_main');
        $formBlock->getChild('form_after')
            ->addFieldMap('rule_from_date', 'rule_from_date')
            ->addFieldMap('rule_to_date', 'rule_to_date')
            ->addFieldMap('rule_sort_order', 'rule_sort_order')
            ->addFieldMap($couponNativeHintField->getHtmlId(), $couponNativeHintField->getName())
            ->addFieldMap($emailTemplateField->getHtmlId(), $emailTemplateField->getName())
            ->addFieldDependence(
                $emailTemplateField->getName(),
                'coupon_type',
                $awRule->getCouponType())
            ->addFieldDependence(
                $couponNativeHintField->getName(),
                'coupon_type',
                array_map('strval', array(
                    Mage_SalesRule_Model_Rule::COUPON_TYPE_NO_COUPON,
                    Mage_SalesRule_Model_Rule::COUPON_TYPE_SPECIFIC
                )))
            ->addFieldDependence(
                'uses_per_coupon',
                'coupon_type',
                array(
                    // see FormElementDependenceController.prototype.trackChange()
                    // you should convert values to string
                    (string) Mage_SalesRule_Model_Rule::COUPON_TYPE_SPECIFIC,
                    (string) $awRule->getCouponType(),
                ))
            ->addFieldDependence(
                'rule_from_date',
                'coupon_type',
                array_map('strval', $couponTypes))
            ->addFieldDependence(
                'rule_to_date',
                'coupon_type',
                array_map('strval', $couponTypes))
            ->addFieldDependence(
                'rule_sort_order',
                'coupon_type',
                array_map('strval', $couponTypes))
        ;
        return $this;
    }

    /**
     * Get Coupon's alphabet as array of chars
     *
     * @param $format string
     *
     * @return bool|array
     */
    public function getCouponCharset($format)
    {
        return Mage::helper('salesrule/coupon')->getCharset($format);
    }

    /**
     * @param AW_Coupongenerator_Model_Mysql4_Salesrule_Rule_Collection $collection
     * @param $websiteId
     * @param $customerGroupId
     * @param $couponCode
     * @param $now
     *
     * @return AW_Coupongenerator_Model_Mysql4_Salesrule_Rule_Collection
     */
    public function setSalesRuleValidationFilter(
        AW_Coupongenerator_Model_Mysql4_Salesrule_Rule_Collection $collection,
        $websiteId,
        $customerGroupId,
        $couponCode,
        $now
    ) {
        $collection->addWebsiteGroupDateFilter($websiteId, $customerGroupId, $now);
        $select = $collection->getSelect();
        if (strlen($couponCode)) {
            $select->joinLeft(
                array('rule_coupons' => $collection->getTable('salesrule/coupon')),
                'main_table.rule_id = rule_coupons.rule_id ',
                array('code')
            );
            $gmtDateTime = Mage::getSingleton('core/date')->gmtDate();
            $select->where('(main_table.coupon_type = ? ', Mage_SalesRule_Model_Rule::COUPON_TYPE_NO_COUPON)
                ->orWhere('(main_table.coupon_type = ? AND rule_coupons.type = 0',
                    Mage_SalesRule_Model_Rule::COUPON_TYPE_AUTO)
                ->orWhere('main_table.coupon_type = ? AND main_table.use_auto_generation = 1 ' .
                    'AND rule_coupons.type = 1', Mage_SalesRule_Model_Rule::COUPON_TYPE_SPECIFIC)
                ->orWhere('main_table.coupon_type = ? AND main_table.use_auto_generation = 0 ' .
                    'AND rule_coupons.type = 0', Mage_SalesRule_Model_Rule::COUPON_TYPE_SPECIFIC)
                // + ADD A NEW CONDITION FOR AW QUICK COUPONS
                ->orWhere(
                    'main_table.coupon_type = ?
                        AND rule_coupons.type = 0
                        AND (rule_coupons.expiration_date >= "' . $gmtDateTime . '"
                        OR rule_coupons.expiration_date IS NULL))',
                    Mage::getSingleton('coupongenerator/salesrule')->getCouponType())
                // - ADD A NEW CONDITION FOR AW QUICK COUPONS
                ->where('rule_coupons.code = ?)', $couponCode)
            ;
        } else {
            $collection->addFieldToFilter('main_table.coupon_type', Mage_SalesRule_Model_Rule::COUPON_TYPE_NO_COUPON);
        }
        $collection->setOrder('sort_order', AW_Coupongenerator_Model_Mysql4_Salesrule_Rule_Collection::SORT_ORDER_ASC);
        $collection->setFlag('validation_filter', true);
        return $collection;
    }
}