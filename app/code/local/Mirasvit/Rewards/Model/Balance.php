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


class Mirasvit_Rewards_Model_Balance extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('rewards/balance');
    }

    public function toOptionArray($emptyOption = false)
    {
    	return $this->getCollection()->toOptionArray($emptyOption);
    }

	/************************/

    public function recalculate()
    {
        $transactions = Mage::getModel('rewards/transaction')->getCollection()
            ->addCustomerFilter($this->getCustomerId())
            ->addActiveFilter();

        $amount = 0;
        foreach ($transactions as $transaction) {
            $amount += $transaction->getAmount();
        }

        $this->setAmount($amount)
            ->save();
    }
}