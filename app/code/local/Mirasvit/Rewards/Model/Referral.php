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
 * @method Mirasvit_Rewards_Model_Resource_Referral_Collection|Mirasvit_Rewards_Model_Referral[] getCollection()
 * @method Mirasvit_Rewards_Model_Referral load(int $id)
 * @method bool getIsMassDelete()
 * @method Mirasvit_Rewards_Model_Referral setIsMassDelete(bool $flag)
 * @method bool getIsMassStatus()
 * @method Mirasvit_Rewards_Model_Referral setIsMassStatus(bool $flag)
 * @method Mirasvit_Rewards_Model_Resource_Referral getResource()
 * @method int getStoreId()
 * @method Mirasvit_Rewards_Model_Referral setStoreId(int $storeId)
 * @method int getCustomerId()
 * @method Mirasvit_Rewards_Model_Referral setCustomerId(int $entityId)
 * @method int getNewCustomerId()
 * @method Mirasvit_Rewards_Model_Referral setNewCustomerId(int $entityId)
 */
class Mirasvit_Rewards_Model_Referral extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('rewards/referral');
    }

    public function toOptionArray($emptyOption = false)
    {
        return $this->getCollection()->toOptionArray($emptyOption);
    }

    protected $_store = null;

    /**
     * @return bool|Mirasvit_Rewards_Model_Store
     */
    public function getStore()
    {
        if (!$this->getStoreId()) {
            return false;
        }
        if ($this->_store === null) {
            $this->_store = Mage::getModel('core/store')->load($this->getStoreId());
        }

        return $this->_store;
    }

    protected $_customer = null;

    /**
     * @return bool|Mirasvit_Rewards_Model_Customer
     */
    public function getCustomer()
    {
        if (!$this->getCustomerId()) {
            return false;
        }
        if ($this->_customer === null) {
            // $this->_customer = Mage::getModel('customer/customer')->load($this->getCustomerId());
            $this->_customer = Mage::getModel('customer/customer')->getCollection()
                ->addAttributeToSelect('*')
                ->addFieldToFilter('entity_id', $this->getCustomerId())
                ->getFirstItem();
        }

        return $this->_customer;
    }

    protected $_new_customer = null;

    /**
     * @return bool|Mirasvit_Rewards_Model_New_Customer
     */
    public function getNewCustomer()
    {
        if (!$this->getNewCustomerId()) {
            return false;
        }
        if ($this->_new_customer === null) {
            $this->_new_customer = Mage::getModel('customer/customer')->load($this->getNewCustomerId());
        }

        return $this->_new_customer;
    }

    /************************/

    public function sendInvitation($message)
    {
        Mage::helper('rewards/mail')->sendReferralInvitationEmail($this, $message);
        $this->setStatus(Mirasvit_Rewards_Model_Config::REFERRAL_STATUS_SENT);
        $this->save();
    }

    public function getInvitationUrl()
    {
        return Mage::getUrl('rewards/referral/invite', array('id' => $this->getId()));
    }

    public function getStatusName()
    {
        $statuses = Mage::getModel('rewards/config_source_referral_status')->toArray();
        if (isset($statuses[$this->getStatus()])) {
            return $statuses[$this->getStatus()];
        }
    }

    public function finish($status, $newCustomerId = false, $transaction = false)
    {
        $this->setStatus($status);
        if ($newCustomerId) {
            $this->setNewCustomerId($newCustomerId);
        }
        if ($transaction) {
            $this->setLastTransactionId($transaction->getId())
                ->setPointsAmount($transaction->getAmount() + (int) $this->getPointsAmount());
        }
        $this->save();
        Mage::getSingleton('core/session')->setReferral(0);
    }
}
