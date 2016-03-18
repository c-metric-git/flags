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


class Mirasvit_Rewards_Model_Config
{
    const BEHAVIOR_TRIGGER_SIGNUP = 'signup';
    const BEHAVIOR_TRIGGER_VOTE = 'vote';
    const BEHAVIOR_TRIGGER_SEND_LINK = 'send_link';
    const BEHAVIOR_TRIGGER_NEWSLETTER_SIGNUP = 'newsletter_signup';
    const BEHAVIOR_TRIGGER_TAG = 'tag';
    const BEHAVIOR_TRIGGER_REVIEW = 'review';
    const BEHAVIOR_TRIGGER_BIRTHDAY = 'birthday';
    const BEHAVIOR_TRIGGER_INACTIVITY = 'inactivity';
    const BEHAVIOR_TRIGGER_FACEBOOK_LIKE = 'facebook_like';
    const BEHAVIOR_TRIGGER_TWITTER_TWEET = 'twitter_tweet';
    const BEHAVIOR_TRIGGER_GOOGLEPLUS_ONE = 'googleplus_one';
    const BEHAVIOR_TRIGGER_PINTEREST_PIN = 'pinterest_pin';
    const BEHAVIOR_TRIGGER_REFERRED_CUSTOMER_SIGNUP = 'referred_customer_signup';
//    const BEHAVIOR_TRIGGER_REFERRED_CUSTOMER_FIRST_ORDER = 'referred_customer_first_order';
    const BEHAVIOR_TRIGGER_REFERRED_CUSTOMER_ORDER = 'referred_customer_order';
    const TYPE_PRODUCT = 'product';
    const TYPE_CART = 'cart';
    const TYPE_BEHAVIOR = 'behavior';
    const NOTIFICATION_POSITION_ACCOUNT_REWARDS = 'account_rewards';
    const NOTIFICATION_POSITION_ACCOUNT_REFERRALS = 'account_referrals';
    const NOTIFICATION_POSITION_CART = 'cart';
    const NOTIFICATION_POSITION_CHECKOUT = 'checkout';
    const REFERRAL_STATUS_SENT = 'sent';
    const REFERRAL_STATUS_VISITED = 'visited';
    const REFERRAL_STATUS_SIGNUP = 'signup';
    const REFERRAL_STATUS_MADE_ORDER = 'referred_customer_order';
    const TOTAL_TYPE_SUBTOTAL_TAX = 'subtotal_tax';
    const TOTAL_TYPE_SUBTOTAL_TAX_SHIPPING = 'subtotal_tax_shipping';

    const EARNING_STYLE_GIVE = 'earning_style_give';
    const EARNING_STYLE_AMOUNT_SPENT = 'earning_style_amount_spent';
    const EARNING_STYLE_QTY_SPENT = 'earning_style_qty_spent';
    const EARNING_STYLE_AMOUNT_PRICE = 'earning_style_amount_price';


    public function getGeneralPointUnitName($store = null)
    {
        return Mage::getStoreConfig('rewards/general/point_unit_name', $store);
    }

    public function getGeneralExpiresAfterDays($store = null)
    {
        return Mage::getStoreConfig('rewards/general/expires_after_days', $store);
    }

    public function getGeneralIsEarnAfterInvoice($store = null)
    {
        return Mage::getStoreConfig('rewards/general/is_earn_after_invoice', $store);
    }

    public function getGeneralIsEarnAfterShipment($store = null)
    {
        return Mage::getStoreConfig('rewards/general/is_earn_after_shipment', $store);
    }

    public function getGeneralEarnInStatuses($store = null)
    {
        $value = Mage::getStoreConfig('rewards/general/earn_in_statuses', $store);
        return explode(',', $value);
    }

    public function getGeneralIsCancelAfterRefund($store = null)
    {
        return Mage::getStoreConfig('rewards/general/is_cancel_after_refund', $store);
    }

    public function getGeneralIsRestoreAfterRefund($store = null)
    {
        return Mage::getStoreConfig('rewards/general/is_restore_after_refund', $store);
    }

    public function getGeneralIsEarnShipping($store = null)
    {
        return Mage::getStoreConfig('rewards/general/is_earn_shipping', $store);
    }

    public function getGeneralIsSpendShipping($store = null)
    {
        return Mage::getStoreConfig('rewards/general/is_spend_shipping', $store);
    }

    public function getGeneralIsAllowZeroOrders($store = null)
    {
        return Mage::getStoreConfig('rewards/general/is_allow_zero_orders', $store);
    }

    public function getNotificationSenderEmail($store = null)
    {
        return Mage::getStoreConfig('rewards/notification/sender_email', $store);
    }

    public function getNotificationBalanceUpdateEmailTemplate($store = null)
    {
        return Mage::getStoreConfig('rewards/notification/balance_update_email_template', $store);
    }

    public function getNotificationPointsExpireEmailTemplate($store = null)
    {
        return Mage::getStoreConfig('rewards/notification/points_expire_email_template', $store);
    }

    public function getNotificationSendBeforeExpiringDays($store = null)
    {
        return Mage::getStoreConfig('rewards/notification/send_before_expiring_days', $store);
    }

    public function getReferralIsActive($store = null)
    {
        return Mage::getStoreConfig('rewards/referral/is_active', $store);
    }

    public function getReferralInvitationEmailTemplate($store = null)
    {
        return Mage::getStoreConfig('rewards/referral/invitation_email_template', $store);
    }


    /************************/
    //by default we must allow including discounts in total.
    // otherwise we don't apply discount on the last step of paypal express checkout (in the result order)
    protected $_calculateTotalFlag = true;

    public function getCalculateTotalFlag()
    {
        return $this->_calculateTotalFlag;
    }

    public function setCalculateTotalFlag($value)
    {
        $this->_calculateTotalFlag = $value;
        return $this;
    }

    protected $_spendTotalAppliedFlag = false;

    public function getSpendTotalAppliedFlag()
    {
        return $this->_spendTotalAppliedFlag;
    }

    public function setSpendTotalAppliedFlag($value)
    {
        $this->_spendTotalAppliedFlag = $value;
        return $this;
    }

    protected $_quoteSaveFlag = false;

    public function getQuoteSaveFlag()
    {
        return $this->_quoteSaveFlag;
    }

    public function setQuoteSaveFlag($value)
    {
        $this->_quoteSaveFlag = $value;
        return $this;
    }
}