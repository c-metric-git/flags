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


class Mirasvit_Rewards_Model_Observer_Referral
{
    /**
     * customer sign up
     */
    public function customerAfterCreate($customer)
    {
        $referral = false;
        if ($id = (int)Mage::getSingleton('core/session')->getReferral()) {
            /** @var Mirasvit_Rewards_Model_Referral $referral */
            $referral = Mage::getModel('rewards/referral')->load($id);
        } else {
            $referrals = Mage::getModel('rewards/referral')->getCollection()
                            ->addFieldToFilter('email', $customer->getEmail());
            if ($referrals->count()) {
                $referral = $referrals->getFirstItem();
            }
        }
        if (!$referral) {
            return;
        }
        /** @var Mirasvit_Rewards_Model_Transaction $transaction */
        $transaction = Mage::helper('rewards/behavior')->processRule(Mirasvit_Rewards_Model_Config::BEHAVIOR_TRIGGER_REFERRED_CUSTOMER_SIGNUP, $referral->getCustomerId(), false, $customer->getId());
        $referral->finish(Mirasvit_Rewards_Model_Config::REFERRAL_STATUS_SIGNUP, $customer->getId(), $transaction);

    }
}