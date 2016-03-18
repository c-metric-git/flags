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


class Mirasvit_Rewards_Model_Customer extends Mage_Customer_Model_Customer
{
    public function getCurrentCustomer()
    {
        $customer = $this->load(Mage::getSingleton('customer/session')->getCustomerId());

        if ($customer->getId()) {
            return $customer;
        }

        return false;
    }

    public function getTransactions()
    {
        $transactions = Mage::getModel('rewards/transaction')->getCollection()
            ->addCustomerFilter($this->getId());

        return $transactions;
    }

    public function getTransaction($transactionCode)
    {
        $transaction = Mage::getModel('rewards/transaction')->getCollection()
            ->addFieldToFilter('customer_id', $this->getId())
            ->addFieldToFilter('code', $transactionCode)
            ->getFirstItem();

        if ($transaction->getId()) {
            return $transaction;
        }

        return false;
    }

    public function getBalance()
    {
        $balance = Mage::getModel('rewards/balance')->getCollection()
            ->addFieldToFilter('customer_id', $this->getId())
            ->getFirstItem();

        $balance->setCustomerId($this->getId());

        return $balance;
    }

    /**
     * Call this method any time, when we update customer transactions
     */
    public function update()
    {
        $this->getBalance()->recalculate();
    }
}