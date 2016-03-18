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

class AW_Coupongenerator_Model_Mysql4_Salesrule_Rule_Collection extends Mage_SalesRule_Model_Mysql4_Rule_Collection
{
    /**
     * Set filter to select rules that matches current criteria
     *
     * @param unknown_type $websiteId
     * @param unknown_type $customerGroupId
     * @param unknown_type $couponCode
     * @param unknown_type $now
     * @return AW_Coupongenerator_Model_Mysql4_Salesrule_Rule_Collection
     */
    public function setValidationFilter($websiteId, $customerGroupId, $couponCode = '', $now = null)
    {
        if ( ! $this->getFlag('validation_filter')) {
            $this->getSelect()->reset();
            $this->getSelect()->from(array('main_table' => $this->getMainTable()));
            Mage::getSingleton('coupongenerator/version_adapter')->call(
                'setSalesRuleValidationFilter',
                array($this, $websiteId, $customerGroupId, $couponCode, $now)
            );
        }
        return $this;
    }
}
