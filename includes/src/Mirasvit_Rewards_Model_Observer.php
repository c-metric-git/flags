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


class Mirasvit_Rewards_Model_Observer
{
    public function prepareCatalogProductCollection($e)
    {
        $websiteId = Mage::app()->getWebsite()->getId();
        $groupId   = Mage::getSingleton('customer/session')->getCustomerGroupId();

        $select = $e->getCollection()->getSelect();
        $select->joinLeft(array(
            'earning_rule_product' => Mage::getSingleton('core/resource')->getTableName('rewards/earning_rule_product')),
            "e.entity_id = er_product_id AND er_website_id IN (0, $websiteId) AND er_customer_group_id = $groupId",
            array()
        )->joinLeft(array(
            'earning_rule' => Mage::getSingleton('core/resource')->getTableName('rewards/earning_rule')),
            'earning_rule.earning_rule_id  = earning_rule_product.earning_rule_id',
            array('points_amount')
        );
    }
}