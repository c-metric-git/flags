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
 * @method Mirasvit_Rewards_Model_Transaction getFirstItem()
 * @method Mirasvit_Rewards_Model_Transaction getLastItem()
 * @method Mirasvit_Rewards_Model_Resource_Transaction_Collection|Mirasvit_Rewards_Model_Transaction[] addFieldToFilter
 * @method Mirasvit_Rewards_Model_Resource_Transaction_Collection|Mirasvit_Rewards_Model_Transaction[] setOrder
 */
class Mirasvit_Rewards_Model_Resource_Transaction_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('rewards/transaction');
    }

    public function toOptionArray($emptyOption = false)
    {
        $arr = array();
        if ($emptyOption) {
            $arr[0] = array('value' => 0, 'label' => Mage::helper('rewards')->__('-- Please Select --'));
        }
        /** @var Mirasvit_Rewards_Model_Transaction $item */
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
        /** @var Mirasvit_Rewards_Model_Transaction $item */
        foreach ($this as $item) {
            $arr[$item->getId()] = $item->getName();
        }

        return $arr;
    }

    protected function initFields()
    {
        /* @noinspection PhpUnusedLocalVariableInspection */
        $select = $this->getSelect();
        // $this->joinCustomerName();
        $select->joinLeft(array('customer' => $this->getTable('customer/entity')), 'main_table.customer_id = customer.entity_id', array('customer_email' => 'customer.email'));
        // $select->columns(array('is_replied' => new Zend_Db_Expr("answer <> ''")));
    }

    protected function _initSelect()
    {
        parent::_initSelect();
        $this->initFields();
    }

     /************************/

    public function joinCustomerName()
    {
        $customer = Mage::getModel('rewards/customer');
        $firstname = $customer->getAttribute('firstname');
        $lastname = $customer->getAttribute('lastname');

        $this->getSelect()
            ->joinLeft(array('clt' => $lastname->getBackend()->getTable()), 'clt.entity_id = main_table.customer_id
                 AND clt.attribute_id = '.$lastname->getAttributeId(), array('customer_lastname' => 'value'))
            ->joinLeft(array('cft' => $firstname->getBackend()->getTable()), 'cft.entity_id = main_table.customer_id
                 AND cft.attribute_id = '.$firstname->getAttributeId(), array('customer_firstname' => 'value'))
            ->columns(array('customer_name' => new Zend_Db_Expr("CONCAT(`cft`.`value`, ' ', `clt`.`value`)")));

        return $this;
    }

    public function addCustomerFilter($customerId)
    {
        $this->addFieldToFilter('customer_id', intval($customerId));

        return $this;
    }

    public function addActiveFilter()
    {
        $this->addFieldToFilter('status', Mirasvit_Rewards_Model_Transaction::STATUS_APPROVED);

        $date = Mage::app()->getLocale()->date();

        $expires = array();
        $expires[] = array('date' => true, 'from' => date($date->toString('YYYY-MM-dd H:mm:ss')));
        $expires[] = array('date' => true, 'eq' => '0000-00-00 00:00:00');
        $expires[] = array('date' => true, 'null' => true);

        $this->addFieldToFilter('expires_at', $expires);

        return $this;
    }
}
