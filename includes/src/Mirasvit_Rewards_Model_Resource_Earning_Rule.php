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



class Mirasvit_Rewards_Model_Resource_Earning_Rule extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('rewards/earning_rule', 'earning_rule_id');
    }

    protected function loadWebsiteIds(Mage_Core_Model_Abstract $object)
    {
        /* @var  Mirasvit_Rewards_Model_Earning_Rule $object */
        $select = $this->_getReadAdapter()->select()
            ->from($this->getTable('rewards/earning_rule_website'))
            ->where('earning_rule_id = ?', $object->getId());
        if ($data = $this->_getReadAdapter()->fetchAll($select)) {
            $array = array();
            foreach ($data as $row) {
                $array[] = $row['website_id'];
            }
            $object->setData('website_ids', $array);
        }

        return $object;
    }

    protected function saveWebsiteIds($object)
    {
        /* @var  Mirasvit_Rewards_Model_Earning_Rule $object */
        $condition = $this->_getWriteAdapter()->quoteInto('earning_rule_id = ?', $object->getId());
        $this->_getWriteAdapter()->delete($this->getTable('rewards/earning_rule_website'), $condition);
        foreach ((array) $object->getData('website_ids') as $id) {
            $objArray = array(
                'earning_rule_id' => $object->getId(),
                'website_id' => $id,
            );
            $this->_getWriteAdapter()->insert(
                $this->getTable('rewards/earning_rule_website'), $objArray);
        }
    }

    protected function loadCustomerGroupIds(Mage_Core_Model_Abstract $object)
    {
        /* @var  Mirasvit_Rewards_Model_Earning_Rule $object */
        $select = $this->_getReadAdapter()->select()
            ->from($this->getTable('rewards/earning_rule_customer_group'))
            ->where('earning_rule_id = ?', $object->getId());
        if ($data = $this->_getReadAdapter()->fetchAll($select)) {
            $array = array();
            foreach ($data as $row) {
                $array[] = $row['customer_group_id'];
            }
            $object->setData('customer_group_ids', $array);
        }

        return $object;
    }

    protected function saveCustomerGroupIds($object)
    {
        if (is_string($object->getData('customer_group_ids'))) {
            $object->setData('customer_group_ids', explode(',', $object->getData('customer_group_ids')));
        }
        $condition = $this->_getWriteAdapter()->quoteInto('earning_rule_id = ?', $object->getId());
        $this->_getWriteAdapter()->delete($this->getTable('rewards/earning_rule_customer_group'), $condition);
        foreach ((array) $object->getData('customer_group_ids') as $id) {
            $objArray = array(
                'earning_rule_id' => $object->getId(),
                'customer_group_id' => $id,
            );
            $this->_getWriteAdapter()->insert(
                $this->getTable('rewards/earning_rule_customer_group'), $objArray);
        }
    }

    protected function _afterLoad(Mage_Core_Model_Abstract $object)
    {
        /** @var  Mirasvit_Rewards_Model_Earning_Rule $object */
        if (!$object->getIsMassDelete()) {
            $this->loadWebsiteIds($object);
            $this->loadCustomerGroupIds($object);
        }

        return parent::_afterLoad($object);
    }

    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        /** @var  Mirasvit_Rewards_Model_Earning_Rule $object */
        if (!$object->getId()) {
            $object->setCreatedAt(Mage::getSingleton('core/date')->gmtDate());
        }
        $object->setUpdatedAt(Mage::getSingleton('core/date')->gmtDate());

        return parent::_beforeSave($object);
    }

    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        /** @var  Mirasvit_Rewards_Model_Earning_Rule $object */
        if (!$object->getIsMassStatus()) {
            $this->saveWebsiteIds($object);
            $this->saveCustomerGroupIds($object);
        }

        return parent::_afterSave($object);
    }

    /************************/

    public function applyAllRulesForDateRange()
    {
        $write = $this->_getWriteAdapter();
        $collection = Mage::getModel('rewards/earning_rule')->getCollection()
                        ->addFieldToFilter('type', Mirasvit_Rewards_Model_Earning_Rule::TYPE_PRODUCT)
                        ;
        foreach ($collection as $rule) {
            $conds = array(
                $write->quoteInto('earning_rule_id=?', $rule->getId()),
            );
            $write->delete($this->getTable('rewards/earning_rule_product'), $conds);
        }

        $products = Mage::getModel('catalog/product')->getCollection()
                        ->addAttributeToSelect('*')
                        ->addAttributeToFilter('status', array('eq' => 1))
                        ;
        foreach ($products as $product) {
            $rules = Mage::getModel('rewards/earning_rule')->getCollection()
                            ->addFieldToFilter('type', Mirasvit_Rewards_Model_Earning_Rule::TYPE_PRODUCT)
                            ->addFieldToFilter('is_active', true)
                            ;

            foreach ($rules as $rule) {
                $rule->afterLoad();
                if (!$rule->validate($product)) {
                    continue;
                }
                $this->loadWebsiteIds($rule);
                $this->loadCustomerGroupIds($rule);

                foreach ($rule->getWebsiteIds() as $websiteId) {
                    foreach ($rule->getCustomerGroupIds() as $groupId) {
                        $objArray = array(
                            'earning_rule_id' => $rule->getId(),
                            'er_website_id' => $websiteId,
                            'er_customer_group_id' => $groupId,
                            'er_product_id' => $product->getId(),
                        );
                        $this->_getWriteAdapter()->insert(
                            $this->getTable('rewards/earning_rule_product'), $objArray);
                    }
                }
            }
        }
    }
}
