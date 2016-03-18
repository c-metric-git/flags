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


class Mirasvit_Rewards_Helper_Balance extends Mage_Core_Helper_Abstract
{
    /**
     * Change the number of points on the customer balance
     *
     * @param $customer
     * @param $pointsNum
     * @param $historyMessage
     * @param bool $code - optional, if we have code, we will check for uniqness this this transaction
     * @param bool $notifyByEmail
     * @param bool $emailMessage
     * @return bool
     */
	public function changePointsBalance($customer, $pointsNum, $historyMessage, $code = false, $notifyByEmail = false, $emailMessage = false)
	{
		if (is_object($customer)) {
			$customer = $customer->getId();
		}
		if ($code) {
			$collection = Mage::getModel('rewards/transaction')->getCollection()
							->addFieldToFilter('customer_id', $customer)
							->addFieldToFilter('code', $code);
			if ($collection->count()) {
				return false;
			}
		}
		$transaction = Mage::getModel('rewards/transaction')
			->setCustomerId($customer)
			->setAmount($pointsNum);
		if ($code) {
			$transaction->setCode($code);
		}
		$historyMessage = Mage::helper('rewards/mail')->parseVariables($historyMessage, $transaction);
		$transaction->setComment($historyMessage);
		$transaction->save();
		if ($notifyByEmail) {
			Mage::helper('rewards/mail')->sendNotificationBalanceUpdateEmail($transaction, $emailMessage);
		}
		return $transaction;
	}

	public function getBalancePoints($customer)
	{
		if (is_object($customer)) {
			$customer = $customer->getId();
		}
		$resource = Mage::getSingleton('core/resource');
		$readConnection = $resource->getConnection('core_read');
		$table = $resource->getTableName('rewards/transaction');
		$sum = (int)$readConnection->fetchOne("SELECT SUM(amount) FROM $table WHERE customer_id=?", array((int)$customer));
		return $sum;
	}
}