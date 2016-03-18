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
 * @method Mirasvit_Rewards_Model_Referral getFirstItem()
 * @method Mirasvit_Rewards_Model_Referral getLastItem()
 * @method Mirasvit_Rewards_Model_Resource_Referral_Collection|Mirasvit_Rewards_Model_Referral[] addFieldToFilter
 * @method Mirasvit_Rewards_Model_Resource_Referral_Collection|Mirasvit_Rewards_Model_Referral[] setOrder
 */
class Mirasvit_Rewards_Model_Resource_Referral_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('rewards/referral');
    }

    public function toOptionArray($emptyOption = false)
    {
        $arr = array();
        if ($emptyOption) {
            $arr[0] = array('value' => 0, 'label' => Mage::helper('rewards')->__('-- Please Select --'));
        }
        /** @var Mirasvit_Rewards_Model_Referral $item */
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
        /** @var Mirasvit_Rewards_Model_Referral $item */
        foreach ($this as $item) {
            $arr[$item->getId()] = $item->getName();
        }

        return $arr;
    }

    protected function initFields()
    {
        /* @noinspection PhpUnusedLocalVariableInspection */
        $select = $this->getSelect();
        // $select->joinLeft(array('transaction' => $this->getTable('rewards/transaction')), 'main_table.transaction_id = transaction.transaction_id', array('amount' => 'transaction.amount'));

        // $select->joinLeft(array('store' => $this->getTable('core/store')), 'main_table.store_id = store.store_id', array('store_name' => 'store.name'));
        // $select->joinLeft(array('customer' => $this->getTable('customer/customer')), 'main_table.customer_id = customer.entity_id', array('customer_name' => 'customer.name'));
        // $select->joinLeft(array('new_customer' => $this->getTable('customer/customer')), 'main_table.new_customer_id = new_customer.entity_id', array('new_customer_name' => 'new_customer.name'));
        // $select->columns(array('is_replied' => new Zend_Db_Expr("answer <> ''")));
    }

    protected function _initSelect()
    {
        parent::_initSelect();
        $this->initFields();
    }

     /************************/

    /**
     * Add Name to select.
     *
     * @return Mirasvit_Rewards_Model_Resource_Referral_Collection
     */
    public function addNameToSelect()
    {
        $prefix = Mage::getConfig()->getTablePrefix();
        $fn = Mage::getModel('eav/entity_attribute')->loadByCode('1', 'firstname');
        $ln = Mage::getModel('eav/entity_attribute')->loadByCode('1', 'lastname');
        $this->getSelect()
            ->joinLeft(array('ce1' => $prefix.'customer_entity_varchar'), 'ce1.entity_id=main_table.customer_id', array('firstname' => 'value'))
            ->where('ce1.attribute_id='.$fn->getAttributeId())
            ->joinLeft(array('ce2' => $prefix.'customer_entity_varchar'), 'ce2.entity_id=main_table.customer_id', array('lastname' => 'value'))
            ->where('ce2.attribute_id='.$ln->getAttributeId())
            ->columns(new Zend_Db_Expr("CONCAT(`ce1`.`value`, ' ',`ce2`.`value`) AS customer_name"));

        $this->getSelect()
            ->joinLeft(array('ce3' => $prefix.'customer_entity_varchar'), 'ce3.entity_id=main_table.new_customer_id AND ce3.attribute_id='.$fn->getAttributeId(), array('firstname' => 'value'))
            ->joinLeft(array('ce4' => $prefix.'customer_entity_varchar'), 'ce4.entity_id=main_table.new_customer_id AND ce4.attribute_id='.$ln->getAttributeId(), array('lastname' => 'value'))
            ->columns(new Zend_Db_Expr("CONCAT(`ce3`.`value`, ' ',`ce4`.`value`) AS new_customer_name"));

        return $this;
    }
}
