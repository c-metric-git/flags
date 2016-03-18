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
 * @method Mirasvit_Rewards_Model_Resource_Transaction_Collection|Mirasvit_Rewards_Model_Transaction[] getCollection()
 * @method Mirasvit_Rewards_Model_Transaction load(int $id)
 * @method bool getIsMassDelete()
 * @method Mirasvit_Rewards_Model_Transaction setIsMassDelete(bool $flag)
 * @method bool getIsMassStatus()
 * @method Mirasvit_Rewards_Model_Transaction setIsMassStatus(bool $flag)
 * @method Mirasvit_Rewards_Model_Resource_Transaction getResource()
 * @method int getCustomerId()
 * @method Mirasvit_Rewards_Model_Transaction setCustomerId(int $entityId)
 */
class Mirasvit_Rewards_Model_Transaction extends Mage_Core_Model_Abstract
{
    const STATUS_PENDING = 0;
    const STATUS_APPROVED = 1;
    const STATUS_DISCARDED = 2;

    protected function _construct()
    {
        $this->_init('rewards/transaction');
    }

    public function toOptionArray($emptyOption = false)
    {
        return $this->getCollection()->toOptionArray($emptyOption);
    }

    protected $_customer = null;

    /**
     * @return bool|Mirasvit_Rewards_Model_Customer
     */
    public function getCustomer()
    {
        if ($this->_customer === null) {
            if ($this->getCustomerId()) {
                $this->_customer = Mage::getModel('customer/customer')->getCollection()
                    ->addAttributeToSelect('*')
                    ->addFieldToFilter('entity_id', $this->getCustomerId())
                    ->getFirstItem();
            } else {
                $this->_customer = false;
            }
        }

        return $this->_customer;
    }

    /************************/

    public function getExpiresAtFormatted()
    {
        $expires = $this->getData('expires_at');
        if ($expires) {
            return Mage::helper('core')->formatDate($expires, 'medium');
        } else {
            return Mage::helper('rewards')->__('-');
        }
    }

    public function getDaysLeft()
    {
        if ($expires = $this->getData('expires_at')) {
            $diff = strtotime($expires) - time();
            $days = (int) ($diff / 60 / 60 / 24);

            return $days;
        }
    }
}
