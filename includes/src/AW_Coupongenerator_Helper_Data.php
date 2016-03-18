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

class AW_Coupongenerator_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function checkVersion($version)
    {
        return version_compare(Mage::getVersion(), $version, '>=');
    }

    /**
     * @param $id int
     * @param $email string
     *
     * @return bool|Mage_Customer_Model_Customer
     */
    public function getCustomer($id, $email, $websiteId = false)
    {
        $customer = false;
        if ($id) {
            $customer = Mage::getModel('customer/customer')->load($id);
        } elseif ($email) {
            // TODO: multiwebsite issue
            if ( ! $websiteId) {
                $websiteId = Mage::app()->getDefaultStoreView()->getWebsiteId();
            }
            $customers = $this->getCustomerCollectionByEmail($email, 100);
            foreach ($customers as $customer) {
                if ($customer->getWebsiteId() == $websiteId) {
                    break;
                }
            }
        }
        return $customer;
    }

    /**
     * @param $email string
     * @param $limit int
     *
     * @return Mage_Customer_Model_Resource_Customer_Collection
     */
    public function getCustomerCollectionByEmail($email, $limit)
    {
        $collection = Mage::getModel('customer/customer')->getCollection()
            ->addNameToSelect()
            ->addAttributeToSelect('email')
            ->addAttributeToSelect('firstname')
            ->addAttributeToSelect('lastname')
            ->setPageSize($limit)
        ;
        $collection->addAttributeToFilter(
            array(
                array('attribute' => 'email', 'like' => '%' . $email . '%')
            )
        );
        return $collection;
    }
}
