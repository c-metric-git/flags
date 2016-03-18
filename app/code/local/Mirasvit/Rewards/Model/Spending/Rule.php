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



/**
 * @method Mirasvit_Rewards_Model_Resource_Spending_Rule_Collection|Mirasvit_Rewards_Model_Spending_Rule[] getCollection()
 * @method Mirasvit_Rewards_Model_Spending_Rule load(int $id)
 * @method bool getIsMassDelete()
 * @method Mirasvit_Rewards_Model_Spending_Rule setIsMassDelete(bool $flag)
 * @method bool getIsMassStatus()
 * @method Mirasvit_Rewards_Model_Spending_Rule setIsMassStatus(bool $flag)
 * @method Mirasvit_Rewards_Model_Resource_Spending_Rule getResource()
 */
class Mirasvit_Rewards_Model_Spending_Rule extends Mage_Rule_Model_Rule
{
    const TYPE_PRODUCT = 'product';
    const TYPE_CART = 'cart';
    const TYPE_CUSTOM = 'custom';

    protected function _construct()
    {
        $this->_init('rewards/spending_rule');
    }

    public function toOptionArray($emptyOption = false)
    {
        return $this->getCollection()->toOptionArray($emptyOption);
    }

    /** Rule Methods **/
    public function getConditionsInstance()
    {
        return Mage::getModel('rewards/spending_rule_condition_combine');
    }

    public function getActionsInstance()
    {
        return Mage::getModel('salesrule/rule_condition_product_combine');
        // return Mage::getModel('rewards/spending_rule_action_collection');
    }

    public function getProductIds()
    {
        return $this->_getResource()->getRuleProductIds($this->getId());
    }

    public function toString($format = '')
    {
        $this->load($this->getId());
        $string = $this->getConditions()->asStringRecursive();

        $string = nl2br(preg_replace('/ /', '&nbsp;', $string));

        return $string;
    }
    /************************/

    public function applyAll()
    {
        $this->_getResource()->applyAllRulesForDateRange();
        // $this->_invalidateCache();
    }

    public function getWebsiteIds()
    {
        return $this->getData('website_ids');
    }

    public function getSpendMinPointsNumber()
    {
        $min = parent::getSpendMinPoints();
        if (strpos($min, '%') === false) {
            return $min;
        }

        return false;
    }

    public function getSpendMinAmount($subtotal)
    {
        $min = parent::getSpendMinPoints();
        if (strpos($min, '%') === false) {
            return false;
        }
        $min = str_replace('%', '', $min);

        return $subtotal * $min / 100;
    }

    public function getSpendMaxPointsNumber()
    {
        $max = parent::getSpendMaxPoints();
        if (strpos($max, '%') === false) {
            return $max;
        }

        return false;
    }

    public function getSpendMaxAmount($subtotal)
    {
        $max = parent::getSpendMaxPoints();
        if (strpos($max, '%') === false) {
            return false;
        }
        $max = str_replace('%', '', $max);

        return $subtotal * $max / 100;
    }
}
