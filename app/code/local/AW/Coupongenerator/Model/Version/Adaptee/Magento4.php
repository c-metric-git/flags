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

class AW_Coupongenerator_Model_Version_Adaptee_Magento4
{
    /**
     * @param AW_Coupongenerator_Block_Adminhtml_Coupons_Grid $grid
     *
     * @return AW_Coupongenerator_Block_Adminhtml_Coupons_Grid
     */
    public function addColumnsToCouponsGrid(AW_Coupongenerator_Block_Adminhtml_Coupons_Grid $grid)
    {
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
        foreach ($rule->getCoupons() as $coupon) {
            $coupon->setUsageLimit(0 === $usageLimit ? null : $usageLimit)->save();
        }
        return $rule;
    }

    /**
     * @return string
     */
    public function getRuleSaveAndContinueButtonId()
    {
        return 'save_and_continue';
    }

    /**
     * @return string
     */
    public function getByPercentSalesRuleAction()
    {
        return 'by_percent';
    }

    /**
     * @return string
     */
    public function getByFixedSalesRuleAction()
    {
        return 'by_fixed';
    }

    /**
     * @return string
     */
    public function getCartFixedSalesRuleAction()
    {
        return 'cart_fixed';
    }

    /**
     * @return string
     */
    public function getXYSalesRuleAction()
    {
        return 'buy_x_get_y';
    }

    /**
     * @return AW_Coupongenerator_Block_Adminhtml_Widget_Form_Element_Dependence
     */
    public function getFormDependenciesBlock()
    {
        return Mage::app()->getLayout()->createBlock('coupongenerator/adminhtml_widget_form_element_dependence');
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
        $dependenceBlock = $this->getFormDependenciesBlock()
            ->addFieldMap('rule_uses_per_coupon', 'rule_uses_per_coupon')
            ->addFieldMap('rule_coupon_type', 'rule_coupon_type')
            ->addFieldMap('rule_from_date', 'rule_from_date')
            ->addFieldMap('rule_to_date', 'rule_to_date')
            ->addFieldMap('rule_sort_order', 'rule_sort_order')
            ->addFieldMap($couponNativeHintField->getHtmlId(), $couponNativeHintField->getName())
            ->addFieldMap($emailTemplateField->getHtmlId(), $emailTemplateField->getName())
            ->addFieldDependence(
                $emailTemplateField->getName(),
                'rule_coupon_type',
                $awRule->getCouponType())
            ->addFieldDependence(
                $couponNativeHintField->getName(),
                'rule_coupon_type',
                array_map('strval', array(
                    Mage_SalesRule_Model_Rule::COUPON_TYPE_NO_COUPON,
                    Mage_SalesRule_Model_Rule::COUPON_TYPE_SPECIFIC
                )))
            ->addFieldDependence(
                'rule_uses_per_coupon',
                'rule_coupon_type',
                array(
                    (string) Mage_SalesRule_Model_Rule::COUPON_TYPE_SPECIFIC,
                    (string) $awRule->getCouponType(),
                ))
            ->addFieldDependence(
                'rule_from_date',
                'rule_coupon_type',
                array_map('strval', $couponTypes))
            ->addFieldDependence(
                'rule_to_date',
                'rule_coupon_type',
                array_map('strval', $couponTypes))
            ->addFieldDependence(
                'rule_sort_order',
                'rule_coupon_type',
                array_map('strval', $couponTypes))
            ->setChild('native_dependence', $formBlock->getChild('form_after'))
        ;
        $formBlock->setChild('form_after', $dependenceBlock);
        return $this;
    }

    /**
     * @param $storeId int
     *
     * @return Varien_Object
     */
    public function startEnvironmentEmulation($storeId)
    {
        $initialInfo = new Varien_Object();
        $initialInfo->setInitialStoreId(Mage::app()->getStore()->getId())
            ->setInitialLocaleCode(Mage::app()->getLocale()->getLocaleCode())
        ;
        Mage::app()->setCurrentStore($storeId)
            ->getLocale()
            ->setLocaleCode(Mage::getStoreConfig(Mage_Core_Model_Locale::XML_PATH_DEFAULT_LOCALE, $storeId))
            ->emulate($storeId)
        ;
        return $initialInfo;
    }

    /**
     * @param $initialInfo Varien_Object
     *
     * @return $this
     */
    public function stopEnvironmentEmulation($initialInfo)
    {
        Mage::app()->setCurrentStore($initialInfo->getInitialStoreId());
        Mage::app()->getLocale()->setLocaleCode($initialInfo->getInitialLocaleCode());
        Mage::app()->getTranslator()->setLocale($initialInfo->getInitialLocaleCode())->init('adminhtml', true);
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
        $charsets = array(
            'alphanum' => 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789',
            'num'      => '0123456789',
            'alpha'    => 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'
        );
        if (array_key_exists($format, $charsets)) {
            return str_split($charsets[$format]);
        }
        return false;
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
        if (is_null($now)) {
            $now = Mage::getModel('core/date')->date('Y-m-d');
        }
        $awCouponType = Mage::getSingleton('coupongenerator/salesrule')->getCouponType();
        $collection->addFieldToFilter('website_ids', array('finset' => (int)$websiteId))
            ->addFieldToFilter('customer_group_ids', array('finset' => (int)$customerGroupId))
            ->addFieldToFilter('is_active', 1)
        ;
        if ($couponCode) {
            $collection->getSelect()
                ->joinLeft(
                    array('rule_coupons' => $collection->getTable('salesrule/coupon')),
                    'main_table.rule_id = rule_coupons.rule_id ',
                    array('code')
                );
            $collection->getSelect()->where(
                '(main_table.coupon_type = ? ',  Mage_SalesRule_Model_Rule::COUPON_TYPE_NO_COUPON
            );
            $collection->getSelect()->orWhere(
                'rule_coupons.code = ?
                AND main_table.coupon_type <> ' . $awCouponType,
                $couponCode
            );
            // + ADD A NEW CONDITION FOR AW QUICK COUPONS
            $gmtDateTime = Mage::getSingleton('core/date')->gmtDate();
            $collection->getSelect()->orWhere(
                'rule_coupons.code = ?
                AND main_table.coupon_type = ' . $awCouponType . '
                AND (rule_coupons.expiration_date >= "' . $gmtDateTime . '"
                OR rule_coupons.expiration_date IS NULL))',
                $couponCode
            );
            // - ADD A NEW CONDITION FOR AW QUICK COUPONS
        } else {
            $collection->addFieldToFilter('main_table.coupon_type', Mage_SalesRule_Model_Rule::COUPON_TYPE_NO_COUPON);
        }
        $collection->getSelect()->where('from_date is null or from_date <= ?', $now);
        $collection->getSelect()->where('to_date is null or to_date >= ?', $now);
        $collection->getSelect()->order('sort_order');
        return $collection;
    }
}