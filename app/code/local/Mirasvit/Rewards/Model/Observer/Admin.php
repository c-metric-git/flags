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


class Mirasvit_Rewards_Model_Observer_Admin
{
    public function customerSaveAfter($observer){
        $request = $observer->getEvent()->getRequest();
        $customer = $observer->getEvent()->getCustomer();
        $amount = (int)$request->getParam('rewards_change_balance');
        $message = $request->getParam('rewards_message');
        $emailMessage = '';
        if ($amount !== 0) {
            $amountBalace = Mage::helper('rewards/balance')->getBalancePoints($customer);
            if ($amountBalace + $amount < 0) {
                $amount = -$amountBalace;
            }

            if ($amount != 0) {
                Mage::helper('rewards/balance')->changePointsBalance($customer->getId(), $amount, $message, false, true, $emailMessage);
            }

            $formattedAmount = Mage::helper('rewards')->formatPoints($amount);
            if ($amount > 0) {
                $alertMessage = Mage::helper('adminhtml')->__('%s has been added.', $formattedAmount);
            } else {
                $alertMessage = Mage::helper('adminhtml')->__('%s has been deducted.', $formattedAmount);
            }
            Mage::getSingleton('adminhtml/session')->addSuccess($alertMessage);
        }
    }
}