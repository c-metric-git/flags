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


class AW_Coupongenerator_Model_Salesrule extends Mage_Core_Model_Abstract
{
    /**
     * Mage_SalesRule_Model_Rule::COUPON_TYPE_NO_COUPON = 1;
     * Mage_SalesRule_Model_Rule::COUPON_TYPE_SPECIFIC  = 2;
     * Mage_SalesRule_Model_Rule::COUPON_TYPE_AUTO      = 3;
     */
    const COUPON_TYPE_QUICK_GENERATED = 4;

    /**
     * Store coupon code generator instance
     *
     * @var Mage_SalesRule_Model_Coupon_CodegeneratorInterface
     */
    protected $_couponCodeGenerator;

    public function _construct()
    {
        $this->_init('coupongenerator/salesrule');
    }

    /**
     * @param $ruleId int
     *
     * @return $this
     */
    public function loadByRuleId($ruleId)
    {
        $this->load($ruleId, 'rule_id');
        return $this;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $rulesCollection = Mage::getModel('salesrule/rule')->getResourceCollection()
            ->addFieldToFilter('is_active', 1)
            ->addFieldToFilter('coupon_type', $this->getCouponType())
        ;
        $rulesCollection->getSelect()
            ->join(array('aw_rule' => Mage::getSingleton('core/resource')->getTableName('coupongenerator/salesrule')),
                'main_table.rule_id = aw_rule.rule_id', array());

        $result = array('' => Mage::helper('coupongenerator')->__('Please select a rule'));
        foreach ($rulesCollection as $rule) {
            $result[$rule->getRuleId()] = $rule->getName();
        }

        return $result;
    }

    /**
     * Returns code generator instance for auto generated coupons
     *
     * @return Mage_SalesRule_Model_Coupon_CodegeneratorInterface
     */
    public function getCouponCodeGenerator()
    {
        if ( ! $this->_couponCodeGenerator) {
            return Mage::getSingleton('coupongenerator/salesrule_coupon_codegenerator', array('prefix' => $this->getCouponPrefix()));
        }
        return $this->_couponCodeGenerator;
    }

    /**
     * Set code generator instance for auto generated coupons
     *
     * @param Mage_SalesRule_Model_Coupon_CodegeneratorInterface
     */
    public function setCouponCodeGenerator(Mage_SalesRule_Model_Coupon_CodegeneratorInterface $codeGenerator)
    {
        $this->_couponCodeGenerator = $codeGenerator;
        return $this;
    }

    /**
     * @return mix (null|object Zend_Date)
     */
    public function generateExpirationDate()
    {
        $date = new Zend_Date();
        if ( ! $this->getData() || 0 == $this->getExpirationDays()) {
            return null;
        }
        $date->addDay( (int) $this->getExpirationDays());
        $date->setHour(0)->setMinute(0)->setSecond(0);
        return $date;
    }

    /**
     * @return string
     */
    public function generateCouponCode()
    {
        $coupon = Mage::getModel('coupongenerator/salesrule_coupon');
        do {
            $uniqueCode = $this->getCouponCodeGenerator()->generateCode();
            $coupon->loadByCode($uniqueCode);
        } while ($coupon->getId());
        return $uniqueCode;
    }

    /**
     * @return int
     */
    public function getCouponType()
    {
        return self::COUPON_TYPE_QUICK_GENERATED;
    }

    /**
     * @return array
     */
    public function getOtherCouponTypes()
    {
        $couponTypes = array_keys(Mage::getModel('salesrule/rule')->getCouponTypes());
        return array_values(array_diff($couponTypes, array($this->getCouponType())));
    }
}
