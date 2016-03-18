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

class AW_Coupongenerator_Model_Filter extends Mage_Core_Model_Email_Template_Filter
{

    public function filter($value)
    {
        $this->_modifiers['format'] = array($this, 'formatDiscount');
        return parent::filter($value);
    }

    public function formatDiscount($value)
    {
        $versionAdapter = Mage::getSingleton('coupongenerator/version_adapter');
        switch ($this->_templateVars['discount_type']) {
            case $versionAdapter->call('getByPercentSalesRuleAction'):
                $value = $this->formatPercent($value);
                break;

            default:
                $value = $this->formatPrice($value);
                break;
        }
        return $value;
    }

    public function formatPrice($value)
    {
        if (is_numeric($value)) {
            $value = Mage::app()->getStore()->convertPrice($value, true);
        }
        return $value;
    }

    public function formatPercent($value)
    {
        return sprintf('%s%%', $value + 0);
    }

}
