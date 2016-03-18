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
 * @method Mirasvit_Rewards_Model_Earning_Rule getFirstItem()
 * @method Mirasvit_Rewards_Model_Earning_Rule getLastItem()
 * @method Mirasvit_Rewards_Model_Resource_Earning_Rule_Collection|Mirasvit_Rewards_Model_Earning_Rule[] addFieldToFilter
 * @method Mirasvit_Rewards_Model_Resource_Earning_Rule_Collection|Mirasvit_Rewards_Model_Earning_Rule[] setOrder
 */
class Mirasvit_Rewards_Model_Resource_Earning_Rule_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('rewards/earning_rule');
    }

    public function toOptionArray($emptyOption = false)
    {
        $arr = array();
        if ($emptyOption) {
            $arr[0] = array('value' => 0, 'label' => Mage::helper('rewards')->__('-- Please Select --'));
        }
        /** @var Mirasvit_Rewards_Model_Earning_Rule $item */
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
        /** @var Mirasvit_Rewards_Model_Earning_Rule $item */
        foreach ($this as $item) {
            $arr[$item->getId()] = $item->getName();
        }

        return $arr;
    }

    public function addWebsiteFilter($websiteId)
    {
        $this->getSelect()
            ->where("EXISTS (SELECT * FROM `{$this->getTable('rewards/earning_rule_website')}`
                AS `earning_rule_website_table`
                WHERE main_table.earning_rule_id = earning_rule_website_table.earning_rule_id
                AND earning_rule_website_table.website_id in (?))", array(-1, $websiteId));

        return $this;
    }

    public function addCustomerGroupFilter($customerGroupId)
    {
        $this->getSelect()
            ->where("EXISTS (SELECT * FROM `{$this->getTable('rewards/earning_rule_customer_group')}`
                AS `earning_rule_customer_group_table`
                WHERE main_table.earning_rule_id = earning_rule_customer_group_table.earning_rule_id
                AND earning_rule_customer_group_table.customer_group_id in (?))", array(-1, $customerGroupId));

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
    protected $storeId;
    public function setStoreId($storeId)
    {
        $this->storeId = $storeId;

        return $this;
    }

    public function _afterLoad()
    {
        if ($this->storeId) {
            foreach ($this as $item) {
                $item->setStoreId($this->storeId);
            }
        }
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
}
