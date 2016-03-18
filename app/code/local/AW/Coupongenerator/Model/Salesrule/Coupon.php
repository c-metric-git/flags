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

class AW_Coupongenerator_Model_Salesrule_Coupon extends Mage_SalesRule_Model_Coupon
{

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'salesrule_coupon';

    /**
     * Parameter name in event
     *
     * In observe method you can use $observer->getEvent()->getRule() in this case
     *
     * @var string
     */
    protected $_eventObject = 'coupon';

    public function loadByCode($code)
    {
        $this->load($code, 'code');
        return $this;
    }

    public function createNewOne($ruleId)
    {
        $salesRule = Mage::getModel('salesrule/rule')->load($ruleId);
        $awSalesRule = Mage::getModel('coupongenerator/salesrule')->loadByRuleId($salesRule->getId());
        if ($salesRule->getData() && $awSalesRule->getCouponType() == $salesRule->getCouponType()) {
            $__usagePerCustomer = $salesRule->getUsesPerCustomer() && is_numeric($salesRule->getUsesPerCustomer())
                ? $salesRule->getUsesPerCustomer() : null;

            $this->setRuleId($ruleId)
                ->setCreatedAt(Mage::getSingleton('core/date')->gmtDate())
                ->setExpirationDate($awSalesRule->generateExpirationDate())
                ->setCode($awSalesRule->generateCouponCode())
                ->setUsagePerCustomer($__usagePerCustomer)
                ->setUsageLimit($this->getUsageLimitByRule($salesRule))
            ;
            $this->save()->load($this->getCouponId());
        }
        return $this;
    }

    public function getUsageLimitByRule(Mage_SalesRule_Model_Rule $rule)
    {
        if ( ! $usageLimit = $rule->getUsesPerCoupon()) {
            $awRule = Mage::getModel('coupongenerator/salesrule')->loadByRuleId($rule->getId());
            $usageLimit = $awRule->getExtraUsesPerCoupon();
        }
        return $usageLimit ?  (int) $usageLimit : null;
    }

    /**
     * @return string
     */
    public function getLocaleExpirationDate()
    {
        return $this->_toLocaleDateFormat($this->getExpirationDate());
    }

    /**
     * @param string $mysqlDate
     *
     * @return string
     */
    protected function _toLocaleDateFormat($mysqlDate, $locale = Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM)
    {
        $dateFormat = Mage::app()->getLocale()->getDateFormat($locale);
        $date = Mage::getSingleton('core/locale')->date(
            $mysqlDate,
            Zend_Date::ISO_8601
        );
        return $date->toString($dateFormat);
    }

}
