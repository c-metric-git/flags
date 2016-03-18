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


/**
 * SalesRule Validator Model
 *
 * Allows dispatching before and after events for each controller action
 */
class AW_Coupongenerator_Model_Salesrule_Validator extends Mage_SalesRule_Model_Validator
{
    /**
     * Init validator
     * Init process load collection of rules for specific website,
     * customer group and coupon code
     *
     * @param   int $websiteId
     * @param   int $customerGroupId
     * @param   string $couponCode
     * @return  Mage_SalesRule_Model_Validator
     */
    public function init($websiteId, $customerGroupId, $couponCode)
    {
        $this->setWebsiteId($websiteId)
            ->setCustomerGroupId($customerGroupId)
            ->setCouponCode($couponCode);

        $key = $websiteId . '_' . $customerGroupId . '_' . $couponCode;
        if (!isset($this->_rules[$key])) {
            if (Mage::helper('coupongenerator')->isModuleEnabled('AW_Followupemail')
                && Mage::helper('followupemail/coupon')->isFUECoupon($couponCode)
            ) {
                $resourceModelName =  'followupemail/salesrule_collection';
            } else {
                $resourceModelName =  'coupongenerator/salesrule_rule_collection';
            }
            $this->_rules[$key] = Mage::getResourceModel($resourceModelName)
                ->setValidationFilter($websiteId, $customerGroupId, $couponCode)
                ->load()
            ;
        }
        return $this;
    }
}
