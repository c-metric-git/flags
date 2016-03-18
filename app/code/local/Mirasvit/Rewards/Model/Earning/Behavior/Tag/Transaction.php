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


class Mirasvit_Rewards_Model_Earning_Behavior_Tag_Transaction extends Mirasvit_Rewards_Model_Transaction
{
    public function make($behavior, $tag)
    {
        $customer    = Mage::getSingleton('rewards/customer')->getCurrentCustomer();

        if ($customer) {
            if (!$customer->getTransaction($this->getCode($tag))) {
                $this->setAmount($behavior->getPointsAmount())
                    ->setComment('Tag a product. Tag "'.$tag->getName().'"')
                    ->setExpiresDays($behavior->getPointsExpiresAfter())
                    ->setCode($this->getCode($tag));
                if ($tag->getStatus() == Mage_Tag_Model_Tag::STATUS_PENDING) {
                    $this->setStatus(self::STATUS_PENDING);
                } elseif ($tag->getStatus() == Mage_Tag_Model_Tag::STATUS_APPROVED) {
                    $this->setStatus(self::STATUS_APPROVED);
                }

                $this->save();
            }
        }

        $transactions = $this->getCollection()->addFieldToFilter('code', $this->getCode($tag));
        // change status of all transactions related with this transaction code
        foreach ($transactions as $transaction) {
            if ($tag->getStatus() == Mage_Tag_Model_Tag::STATUS_PENDING) {
                $transaction->setStatus(self::STATUS_PENDING);
            } elseif ($tag->getStatus() == Mage_Tag_Model_Tag::STATUS_APPROVED) {
                $transaction->setStatus(self::STATUS_APPROVED);
            } elseif ($tag->getStatus() == Mage_Tag_Model_Tag::STATUS_DISABLED) {
                $transaction->setStatus(self::STATUS_DISCARDED);
            }
            $transaction->save();
        }


        return $this->save();
    }

    public function getCode($tag)
    {
        return Mirasvit_Rewards_Model_Earning_Behavior_Tag_Action::ACTION_CODE.'_'.$tag->getId();
    }
}