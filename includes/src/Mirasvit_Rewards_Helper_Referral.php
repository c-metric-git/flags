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


class Mirasvit_Rewards_Helper_Referral
{
	public function frontendPost($customer, $invitations, $message)
	{
		$rejectedEmails = array();
		foreach ($invitations as $email => $name) {
			$referrals = Mage::getModel('rewards/referral')->getCollection()
				->addFieldToFilter('email', $email);
			if ($referrals->count()) {
				$rejectedEmails[] = $email;
				continue;
			}

			$referral = Mage::getModel('rewards/referral')
						->setName($name)
						->setEmail($email)
						->setCustomerId($customer->getId())
						->setStoreId(Mage::app()->getStore()->getId())
						->save();
			$referral->sendInvitation($message);
		}
		return $rejectedEmails;
	}

	/**
	 * remember referer when customer adds product to cart
	 */
	public function rememberReferal($quote)
	{
        //if we have referral, we save quote id
        if ($id = (int)Mage::getSingleton('core/session')->getReferral()) {
            $referral = Mage::getModel('rewards/referral')->load($id);
            if (!$referral->getQuoteId()) {
                $referral->setQuoteId($quote->getId());
                $referral->save();
            }
        }
	}

	/**
     * Find possible Mirasvit_Rewards_Model_Referral for this order
     *
	 * @param Mage_Sales_Model_Order $order
	 * @return Mirasvit_Rewards_Model_Referral
	 */
	public function loadReferral($order)
	{
		$quoteId = $order->getQuoteId();
		$referrals = Mage::getModel('rewards/referral')->getCollection()
						->addFieldToFilter('quote_id', $quoteId);
		if ($referrals->count()) {
			return $referrals->getFirstItem();
		}

		$referrals = Mage::getModel('rewards/referral')->getCollection()
			->addFieldToFilter('email', $order->getCustomerEmail());
		if ($referrals->count()) {
			return $referrals->getFirstItem();
		}

		$customerId = $order->getCustomerId();
		$referrals = Mage::getModel('rewards/referral')->getCollection()
						->addFieldToFilter('new_customer_id', $customerId);
		if ($referrals->count()) {
			return $referrals->getFirstItem();
		}
		return false;
	}

	/**
     * Customer A refers customer B. Customer B has placed this order.
     * This function can give points to customer A.
     *
	 * @param Mage_Sales_Model_Order $order
	 * @return null
	 */
	public function processReferralOrder($order)
	{
		if (!$referral = $this->loadReferral($order)) {
			return false;
		}
        /** @var  Mage_Customer_Model_Customer $customer - customer A */
		if ($customerId = $order->getCustomerId()) {
			$customer = Mage::getModel('customer/customer')->load($customerId);
		} else {
			$customer = new Varien_Object();
			$customer->setIsGuest(true)
					->setEmail($order->getCustomerEmail())
					->setFirstname($order->getCustomerFirstname())
					->setLastname($order->getCustomerLastname());
		}

		$websiteId = Mage::getModel('core/store')->load($order->getStoreId())->getWebsiteId();
		$transaction = Mage::helper('rewards/behavior')->processRule(Mirasvit_Rewards_Model_Config::BEHAVIOR_TRIGGER_REFERRED_CUSTOMER_ORDER, $referral->getCustomerId(), $websiteId, $order->getId(), array('referred_customer' => $customer,'order' => $order));
		$referral->finish(Mirasvit_Rewards_Model_Config::BEHAVIOR_TRIGGER_REFERRED_CUSTOMER_ORDER, $customerId, $transaction);
	}
}