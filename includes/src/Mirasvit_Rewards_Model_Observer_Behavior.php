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


class Mirasvit_Rewards_Model_Observer_Behavior
{
    protected $_customerNew;

    public function customerBeforeSave($observer)
    {
        $customer = $observer->getEvent()->getCustomer();
        $this->_customerNew = false;
        if ($customer->isObjectNew()) {
            $this->_customerNew = true;
        }

        return $this;
    }

    /**
     * customer sign up
     */
    public function customerAfterCommit($observer)
    {
        $customer = $observer->getEvent()->getCustomer();
        if ($customer && $this->_customerNew) {
            Mage::helper('rewards/behavior')->processRule(Mirasvit_Rewards_Model_Config::BEHAVIOR_TRIGGER_SIGNUP, $customer);
            Mage::getSingleton('rewards/observer_referral')->customerAfterCreate($customer);
        }

        return $this;
    }

    /**
     * customer newsletter subscription
     */
    public function customerSubscribed($observer)
    {
        if ($observer->getEvent()->getDataObject()->getSubscriberStatus() == Mage_Newsletter_Model_Subscriber::STATUS_SUBSCRIBED) {
            Mage::helper('rewards/behavior')->processRule(Mirasvit_Rewards_Model_Config::BEHAVIOR_TRIGGER_NEWSLETTER_SIGNUP);
        }
    }

    /**
     * Advanced newsletter subscription
     */
    public function advnCustomerSubscribed($observer)
    {
        Mage::helper('rewards/behavior')->processRule(Mirasvit_Rewards_Model_Config::BEHAVIOR_TRIGGER_NEWSLETTER_SIGNUP);
    }

    /**
     * poll vote
     */
    public function afterPollVoteAdd($observer)
    {
        $poll = $observer->getPoll();
        Mage::helper('rewards/behavior')->processRule(Mirasvit_Rewards_Model_Config::BEHAVIOR_TRIGGER_VOTE,
            false, false, $poll->getId());
    }

    /**
     * customer review submit
     */
    public function reviewSubmit($observer)
    {
        $review = $observer->getEvent()->getObject();
        if ($review->isApproved() && $review->getCustomerId()) {
            Mage::helper('rewards/behavior')->processRule(Mirasvit_Rewards_Model_Config::BEHAVIOR_TRIGGER_REVIEW,
                $review->getCustomerId(), Mage::helper('rewards')->getWebsiteId($review->getStoreId()), $review->getId());
        }
        return $this;
    }

    /**
     * customer tag submit
     */
    public function tagSubmit($observer)
    {
        $tag = $observer->getEvent()->getObject();
        if (($tag->getApprovedStatus() == $tag->getStatus()) && $tag->getFirstCustomerId()) {
            Mage::helper('rewards/behavior')->processRule(Mirasvit_Rewards_Model_Config::BEHAVIOR_TRIGGER_TAG,
                $tag->getFirstCustomerId(), Mage::helper('rewards')->getWebsiteId($tag->getFirstStoreId()), $tag->getId());
        }
        return $this;
    }
}