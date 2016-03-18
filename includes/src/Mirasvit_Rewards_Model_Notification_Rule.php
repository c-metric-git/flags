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
 * @method Mirasvit_Rewards_Model_Resource_Notification_Rule_Collection|Mirasvit_Rewards_Model_Notification_Rule[] getCollection()
 * @method Mirasvit_Rewards_Model_Notification_Rule load(int $id)
 * @method bool getIsMassDelete()
 * @method Mirasvit_Rewards_Model_Notification_Rule setIsMassDelete(bool $flag)
 * @method bool getIsMassStatus()
 * @method Mirasvit_Rewards_Model_Notification_Rule setIsMassStatus(bool $flag)
 * @method Mirasvit_Rewards_Model_Resource_Notification_Rule getResource()
 */
class Mirasvit_Rewards_Model_Notification_Rule extends Mage_Rule_Model_Rule
{
    const TYPE_PRODUCT = 'product';
    const TYPE_CART = 'cart';
    const TYPE_CUSTOM = 'custom';

    protected function _construct()
    {
        $this->_init('rewards/notification_rule');
    }

    public function toOptionArray($emptyOption = false)
    {
        return $this->getCollection()->toOptionArray($emptyOption);
    }

    /** Rule Methods **/
    public function getConditionsInstance()
    {
        return Mage::getModel('rewards/notification_rule_condition_combine');
    }

    public function getActionsInstance()
    {
        return Mage::getModel('salesrule/rule_condition_product_combine');
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

    public function _beforeSave()
    {
        if (is_array($this->getData('type'))) {
            $this->setData('type', implode(',', $this->getData('type')));
        }
        parent::_beforeSave();
    }

    public function getType()
    {
        $type = parent::getType();
        if (is_string($type)) {
            return explode(',', $type);
        }

        return $type;
    }
}
