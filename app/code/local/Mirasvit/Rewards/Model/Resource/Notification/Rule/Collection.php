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
 * @method Mirasvit_Rewards_Model_Notification_Rule getFirstItem()
 * @method Mirasvit_Rewards_Model_Notification_Rule getLastItem()
 * @method Mirasvit_Rewards_Model_Resource_Notification_Rule_Collection|Mirasvit_Rewards_Model_Notification_Rule[] addFieldToFilter
 * @method Mirasvit_Rewards_Model_Resource_Notification_Rule_Collection|Mirasvit_Rewards_Model_Notification_Rule[] setOrder
 */
class Mirasvit_Rewards_Model_Resource_Notification_Rule_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('rewards/notification_rule');
    }

    public function toOptionArray($emptyOption = false)
    {
        $arr = array();
        if ($emptyOption) {
            $arr[0] = array('value' => 0, 'label' => Mage::helper('rewards')->__('-- Please Select --'));
        }
        /** @var Mirasvit_Rewards_Model_Notification_Rule $item */
        foreach ($this as $item) {
            $arr[] = array('value' => $item->getId(), 'label' => $item->getName());
        }

        return $arr;
    }

    public function getOptionArray($emptyOption = false)
    {
        $arr = array();
        if ($emptyOption) {
            $arr[0] = Mage::helper('rewards')->__('-- Please Select --');
        }
        /** @var Mirasvit_Rewards_Model_Notification_Rule $item */
        foreach ($this as $item) {
            $arr[$item->getId()] = $item->getName();
        }

        return $arr;
    }

    public function addWebsiteFilter($websiteId)
    {
        $this->getSelect()
            ->where("EXISTS (SELECT * FROM `{$this->getTable('rewards/notification_rule_website')}`
                AS `notification_rule_website_table`
                WHERE main_table.notification_rule_id = notification_rule_website_table.notification_rule_id
                AND notification_rule_website_table.website_id in (?))", array(-1, $websiteId));

        return $this;
    }

    public function addCustomerGroupFilter($customerGroupId)
    {
        $this->getSelect()
            ->where("EXISTS (SELECT * FROM `{$this->getTable('rewards/notification_rule_customer_group')}`
                AS `notification_rule_customer_group_table`
                WHERE main_table.notification_rule_id = notification_rule_customer_group_table.notification_rule_id
                AND notification_rule_customer_group_table.customer_group_id in (?))", array(-1, $customerGroupId));

        return $this;
    }

    protected function initFields()
    {
        /* @noinspection PhpUnusedLocalVariableInspection */
        $select = $this->getSelect();
        // $select->columns(array('is_replied' => new Zend_Db_Expr("answer <> ''")));
    }

    protected function _initSelect()
    {
        parent::_initSelect();
        $this->initFields();
    }

     /************************/

    public function addIsActiveFilter()
    {
        $this->addFieldToFilter('is_active', true);

        return $this;
    }

    public function addCurrentFilter()
    {
        $this->addIsActiveFilter();
        $now = Mage::getSingleton('core/date')->gmtDate();
        $this->getSelect()->where("(main_table.active_from <= '$now' OR isnull(main_table.active_from)) AND ('$now' <= main_table.active_to OR isnull(main_table.active_to))");

        return $this;
    }

    public function addTypeFiler($type)
    {
        $this->getSelect()->where("CONCAT(',', main_table.type,',') LIKE '%,$type,%'");

        return $this;
    }
}
