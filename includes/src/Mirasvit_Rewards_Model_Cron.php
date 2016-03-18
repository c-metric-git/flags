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



class Mirasvit_Rewards_Model_Cron
{
    public function run()
    {
        $this->calculateUsedPoints();
        $this->expirePoints();
        $this->sendPointsExpireEmail();
        $this->earnBirthdayPoints();
        $this->earnMilestonePoints();
    }

    public function calculateUsedPoints()
    {
        //get collection of spend transactions
        $spendTransactions = Mage::getModel('rewards/transaction')->getCollection()
            ->addFieldToFilter('amount', array('lt' => 0))
            ->addFieldToFilter('amount_used', array('null' => true));

        foreach ($spendTransactions as $spend) {
            if (strpos($spend->getCode(), 'expired-') !== false) {
                continue;
            }
            $earnTransactions = Mage::getModel('rewards/transaction')->getCollection()
                    ->addFieldToFilter('is_expired', 0)
                    ->addFieldToFilter('amount', array('gt' => 0));
            $earnTransactions->getSelect()->where('expires_at > "'.Mage::getSingleton('core/date')->gmtDate().'" OR expires_at IS NULL')
                                          ->where('amount > amount_used OR amount_used IS NULL');

            //get collection of earn transactions before current spend transaction
            $earnTransactions->addFieldToFilter('customer_id', $spend->getCustomerId())
                ->addFieldToFilter('main_table.created_at', array('lt' => $spend->getCreatedAt()))
                ->setOrder('created_at', 'asc');
            foreach ($earnTransactions as $earn) {
                $avaliablePoints = $earn->getAmount() - $earn->getAmountUsed();
                if ($avaliablePoints >= abs($spend->getAmount())) {
                    $earn->setAmountUsed($earn->getAmountUsed() + abs($spend->getAmount()));
                    $spend->setAmountUsed($spend->getAmount());
                } else {
                    $spend->setAmountUsed($spend->getAmountUsed() + $avaliablePoints);
                    $earn->setAmountUsed($earn->getAmountUsed() + $avaliablePoints);
                }
                $earn->save();
                $spend->save();

                if ($spend->getAmount() == $spend->getAmountUsed()) {
                    break;
                }
            }
        }
    }

    public function expirePoints()
    {
        $transactions = Mage::getModel('rewards/transaction')->getCollection()
                ->addFieldToFilter('is_expired', 0);
        $transactions->getSelect()->where('expires_at < "'.Mage::getSingleton('core/date')->gmtDate().'"')
                                  ->where('amount > amount_used OR amount_used IS NULL');
        foreach ($transactions as $transaction) {
            Mage::helper('rewards/balance')->changePointsBalance($transaction->getCustomerId(), -abs($transaction->getAmount() - $transaction->getAmountUsed()), Mage::helper('rewards')->__('Transaction #%s is expired', $transaction->getId()), 'expired-'.$transaction->getId());
            $transaction->setIsExpired(true)
                        ->save();
        }
    }

    public function sendPointsExpireEmail()
    {
        $config = Mage::getSingleton('rewards/config');
        if ($config->getNotificationPointsExpireEmailTemplate() == 'none') {
            return;
        }
        $days = $config->getNotificationSendBeforeExpiringDays();
        $transactions = Mage::getModel('rewards/transaction')->getCollection()
                ->addFieldToFilter('expires_at', array('lt' => Mage::getSingleton('core/date')->gmtDate(null, time() + 60 * 60 * 24 * $days)))
                ->addFieldToFilter('is_expired', false)
                ->addFieldToFilter('is_expiration_email_sent', false);
        $transactions->getSelect()->where('amount > amount_used OR amount_used IS NULL');

        foreach ($transactions as $transaction) {
            Mage::helper('rewards/mail')->sendNotificationPointsExpireEmail($transaction);
            $transaction->setIsExpirationEmailSent(true)
                        ->save();
        }
    }

    public function earnBirthdayPoints()
    {
        $customers = Mage::getResourceModel('customer/customer_collection')
            ->joinAttribute('dob', 'customer/dob', 'entity_id');
        $customers->getSelect()->where('extract(month from `at_dob`.`value`) = ?', Mage::getModel('core/date')->date('m'))
            ->where('extract(day from `at_dob`.`value`) = ?', Mage::getModel('core/date')->date('d'));
        foreach ($customers as $customer) {
            $transaction = Mage::helper('rewards/behavior')->processRule(Mirasvit_Rewards_Model_Config::BEHAVIOR_TRIGGER_BIRTHDAY, $customer, $customer->getWebsiteId(), Mage::getModel('core/date')->date('Y'));
        }
    }

    public function earnMilestonePoints()
    {
        $resource = Mage::getSingleton('core/resource');
        $connection = $resource->getConnection('core_write');

        $rules = Mage::getModel('rewards/earning_rule')->getCollection()
                    ->addIsActiveFilter()
                    ->addFieldToFilter('behavior_trigger',  Mirasvit_Rewards_Model_Config::BEHAVIOR_TRIGGER_INACTIVITY);
        foreach ($rules as $rule) {
            $rule->afterLoad();
            $customers = Mage::getModel('customer/customer')->getCollection()
                            ->addFieldToFilter('website_id', $rule->getWebsiteIds())
                            ->addFieldToFilter('group_id', $rule->getCustomerGroupIds());
            switch ($rule->getType()) {
                case Mirasvit_Rewards_Model_Earning_Rule::TYPE_BEHAVIOR:
                    foreach ($customers as $customer) {
                        $query = "SELECT DATEDIFF(NOW(), last_visit_at) FROM {$resource->getTableName('log/customer')} lc
                        LEFT JOIN {$resource->getTableName('log/visitor')} lv ON lc.visitor_id = lv.visitor_id
                        WHERE lc.customer_id={$customer->getId()} order by log_id desc LIMIT 1";
                        $daysFromLastVisit = $connection->fetchOne($query);
                        if ($daysFromLastVisit > $rule->getParam1()) {
                            $transaction = Mage::helper('rewards/behavior')->processRule(Mirasvit_Rewards_Model_Config::BEHAVIOR_TRIGGER_INACTIVITY, $customer, false, $rule->getId());
                        }
                    }
                    break;
            }
        }
    }
}
