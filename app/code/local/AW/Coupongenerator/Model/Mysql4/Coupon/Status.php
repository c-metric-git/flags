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

class AW_Coupongenerator_Model_Mysql4_Coupon_Status extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct(){}

    /**
     * @return string
     */
    public function getColumnExpression()
    {
        $gmtDateTime = Mage::getSingleton('core/date')->gmtDate();
        $expired = AW_Coupongenerator_Model_Source_Coupon_Status::EXPIRED_VALUE;
        $used = AW_Coupongenerator_Model_Source_Coupon_Status::USED_VALUE;
        $available = AW_Coupongenerator_Model_Source_Coupon_Status::AVAILABLE_VALUE;
        $select = "SELECT CASE WHEN `expiration_date` <= '{$gmtDateTime}' THEN '{$expired}'"
            . "WHEN `main_table`.`times_used` >= `usage_limit` THEN '{$used}'"
            . "ELSE '{$available}' END"
        ;
        return '(' . new Zend_Db_Expr($select) . ')';
    }

    /**
     * @param $collection Mage_SalesRule_Model_Resource_Coupon_Collection
     * @param $column Mage_Adminhtml_Block_Widget_Grid_Column
     *
     * @return $this
     */
    public function filter($collection, $column)
    {
        if ( ! $value = trim($column->getFilter()->getValue())) {
            return $this;
        }
        $expression = $this->getColumnExpression();
        $collection->getSelect()->where("{$expression} = '{$value}'");
        return $this;
    }
}
