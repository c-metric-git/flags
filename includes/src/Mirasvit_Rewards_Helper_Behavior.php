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



class Mirasvit_Rewards_Helper_Behavior extends Mage_Core_Helper_Abstract
{
    /**
     * @param $ruleType
     * @param bool|int $customerId
     * @param bool $websiteId
     * @param bool $code
     * @param array $options
     * @return bool
     */
    public function processRule($ruleType, $customerId = false, $websiteId = false, $code = false, $options = array())
    {
        if ($code) {
            $code = $ruleType . '-' . $code;
        } else {
            $code = $ruleType;
        }

        if (!$customer = $this->getCustomer($customerId)) {
            return false;
        }

        if (!$this->checkIsAllowToProcessRule($customer->getId(), $code)) {
            return false;
        }

        $rules = $this->getRules($ruleType, $customer, $websiteId, $code);

        $lastTransaction = false;
        foreach ($rules as $rule) {
            /** @var Mirasvit_Rewards_Model_Earning_Rule $rule */
            $rule->afterLoad();

            $object = new Varien_Object();
            $object->setCustomer($customer);
            if (isset($options['referred_customer'])) {
                $object->setReferredCustomer($options['referred_customer']);
            }
            if (!$rule->validate($object)) {
                continue;
            }

            if (!$this->isInLimit($rule, $customer->getId())) {
                continue;
            }
            $total = $rule->getEarnPoints();
            if (isset($options['order'])) {
                /** @var Mage_Sales_Model_Order $order */
                $order = $options['order'];
                switch ($rule->getEarningStyle()) {
                    case Mirasvit_Rewards_Model_Config::EARNING_STYLE_AMOUNT_SPENT:
                        $subtotal = $order->getGrandTotal();
                        $steps = (int)($subtotal / $rule->getMonetaryStep());
                        $amount = $steps * $rule->getEarnPoints();
                        if ($rule->getPointsLimit() && $amount > $rule->getPointsLimit()) {
                            $amount = $rule->getPointsLimit();
                        }
                        $total = $amount;
                        break;
                    case Mirasvit_Rewards_Model_Config::EARNING_STYLE_QTY_SPENT:
                        $steps = (int)($order->getQuote()->getItemsQty() / $rule->getQtyStep());
                        $amount = $steps * $rule->getEarnPoints();
                        if ($rule->getPointsLimit() && $amount > $rule->getPointsLimit()) {
                            $amount = $rule->getPointsLimit();
                        }
                        $total = $amount;
                        break;
                }
            }

            $lastTransaction = Mage::helper('rewards/balance')->changePointsBalance($customer, $total, $rule->getHistoryMessage(), $code, true, $rule->getEmailMessage());
            if ($lastTransaction) {
                $this->addSuccessMessage($rule->getEarnPoints(), $ruleType);
            }
            if ($rule->getIsStopProcessing()) {
                break;
            }
        }
        return $lastTransaction;
    }

    public function getEstimatedEarnPoints($ruleType, $customer, $websiteId = false, $code = false)
    {
        if (!$this->checkIsAllowToProcessRule($customer->getId(), $code)) {
            return false;
        }
        if ($code) {
            $code = $ruleType . '-' . $code;
        } else {
            $code = $ruleType;
        }

        $rules = $this->getRules($ruleType, $customer, $websiteId, $code);
        $amount = 0;
        foreach ($rules as $rule) {
            if (!$this->isInLimit($rule, $customer->getId())) {
                continue;
            }
            $amount += $rule->getEarnPoints();
        }
        return $amount;
    }

    protected function checkIsAllowToProcessRule($customerId, $code)
    {
        $collection = Mage::getModel('rewards/transaction')->getCollection()
            ->addFieldToFilter('code', $code)
            ->addFieldToFilter('customer_id', $customerId);
        $isAllow = $collection->count() == 0;
        return $isAllow;
    }

    protected function getRules($ruleType, $customer, $websiteId = false)
    {
        if (!$websiteId) {
            $websiteId = Mage::app()->getWebsite()->getId();
        }
        $customerGroupId = $customer->getGroupId();
        $rules = Mage::getModel('rewards/earning_rule')->getCollection()
            ->addWebsiteFilter($websiteId)
            ->addCustomerGroupFilter($customerGroupId)
            ->addIsActiveFilter()
            ->addFieldToFilter('type', Mirasvit_Rewards_Model_Earning_Rule::TYPE_BEHAVIOR)
            ->addFieldToFilter('behavior_trigger', $ruleType);
        return $rules;
    }

    protected function isInLimit($rule, $customerId)
    {
        if (!$rule->getPointsLimit()) {
            return true;
        }
        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');
        $table = $resource->getTableName('rewards/transaction');
        $date1 = Mage::getModel('core/date')->gmtDate('Y-m-d 00:00:00');
        $sum = (int)$readConnection->fetchOne("SELECT SUM(amount) FROM $table WHERE customer_id=" . (int)$customerId . " AND code LIKE '{$rule->getBehaviorTrigger()}-%' AND created_at > '$date1'");
        if ($rule->getPointsLimit() > $sum) {
            return true;
        }
        return false;
    }

    protected function getCustomer($customerId = false)
    {
        if (is_object($customerId)) {
            $customerId = $customerId->getId();
        }
        if (!$customerId) {
            $customerId = Mage::getSingleton('customer/session')->getCustomerId();
            if (!$customerId) {
                return;
            }
        }
        $customer = Mage::getModel('customer/customer')->load($customerId);
        return $customer;
    }

    /**
     * adds a success message in the frontend (via session)
     * @param int $points
     * @param string $ruleType
     */
    protected function addSuccessMessage($points, $ruleType)
    {
        $comments = array(
            Mirasvit_Rewards_Model_Config::BEHAVIOR_TRIGGER_SIGNUP => $this->__("You received %s for signing up"),
            Mirasvit_Rewards_Model_Config::BEHAVIOR_TRIGGER_VOTE => $this->__("You received %s for voting"),
            Mirasvit_Rewards_Model_Config::BEHAVIOR_TRIGGER_SEND_LINK => $this->__("You received %s for sending this product"),
            Mirasvit_Rewards_Model_Config::BEHAVIOR_TRIGGER_NEWSLETTER_SIGNUP => $this->__("You received %s for sign up for newsletter"),
            Mirasvit_Rewards_Model_Config::BEHAVIOR_TRIGGER_TAG => $this->__("You will receive %s after approving of this tag"),
            Mirasvit_Rewards_Model_Config::BEHAVIOR_TRIGGER_REVIEW => $this->__("You will receive %s after approving of this review"),
            Mirasvit_Rewards_Model_Config::BEHAVIOR_TRIGGER_REFERRED_CUSTOMER_SIGNUP => $this->__("You received %s for sign up of referral customer."),
            Mirasvit_Rewards_Model_Config::BEHAVIOR_TRIGGER_REFERRED_CUSTOMER_ORDER => $this->__("You received %s for order of referral customer."),
            Mirasvit_Rewards_Model_Config::BEHAVIOR_TRIGGER_BIRTHDAY => $this->__("Happy birthday! You received %s."),
        );
        $hiddenPoints = array(
            Mirasvit_Rewards_Model_Config::BEHAVIOR_TRIGGER_REFERRED_CUSTOMER_SIGNUP,
            Mirasvit_Rewards_Model_Config::BEHAVIOR_TRIGGER_REFERRED_CUSTOMER_ORDER
        );
        if (isset($comments[$ruleType])) {
            $notification = $this->__($comments[$ruleType], Mage::helper('rewards')->formatPoints($points));
            if (!in_array($ruleType, $hiddenPoints)) {
                Mage::getSingleton('core/session')->addSuccess($notification);
            }
        }
    }
}