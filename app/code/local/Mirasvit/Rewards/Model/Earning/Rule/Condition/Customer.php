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



class Mirasvit_Rewards_Model_Earning_Rule_Condition_Customer extends Mage_Rule_Model_Condition_Abstract
{
    const OPTION_GROUP_ID = 'group_id';
    const OPTION_ORDERS_SUM = 'orders_sum';
    const OPTION_ORDERS_NUMBER = 'orders_number';
    const OPTION_IS_SUBSCRIBER = 'is_subscriber';
    const OPTION_REVIEWS_NUMBER = 'reviews_number';
    const OPTION_REFERRED_SIGNUPS_NUMBER = 'referred_signups_number';
    const OPTION_REFERRED_ORDERS_NUMBER = 'referred_orders_number';
    const OPTION_REFERRED_ORDERS_SUM = 'referred_orders_sum';
    const OPTION_REFERRED_NUMBER__ORDERED_AT_LEAST_ONCE = 'referred_ordered_number_at_least_once';

    public function getConfig()
    {
        return Mage::getSingleton('rewards/config');
    }

    public function loadAttributeOptions()
    {
        $attributes = array(
            self::OPTION_GROUP_ID => Mage::helper('rewards')->__('Group'),
            self::OPTION_ORDERS_SUM => Mage::helper('rewards')->__('Lifetime Sales'),
            self::OPTION_ORDERS_NUMBER => Mage::helper('rewards')->__('Number of Orders'),
            self::OPTION_IS_SUBSCRIBER => Mage::helper('rewards')->__('Is subscriber of newsletter'),
            self::OPTION_REVIEWS_NUMBER => Mage::helper('rewards')->__('Number of reviews'),
            self::OPTION_REFERRED_SIGNUPS_NUMBER => Mage::helper('rewards')->__('Number of referred friends signups'),
            self::OPTION_REFERRED_ORDERS_NUMBER => Mage::helper('rewards')->__('Number of referred friends orders'),
            self::OPTION_REFERRED_ORDERS_SUM => Mage::helper('rewards')->__('Sum of referred friends orders'),
            self::OPTION_REFERRED_NUMBER__ORDERED_AT_LEAST_ONCE => Mage::helper('rewards')->__('Number of referred friends ordered at least once'),
        );

        $this->setAttributeOption($attributes);

        return $this;
    }

    public function getInputType()
    {
        $type = 'string';

        switch ($this->getAttribute()) {
            case self::OPTION_GROUP_ID:
                $type = 'multiselect';
                break;

            case self::OPTION_IS_SUBSCRIBER:
                $type = 'select';
                break;
        }

        return $type;
    }

    public function getValueElementType()
    {
        $type = 'text';

        switch ($this->getAttribute()) {
            case self::OPTION_GROUP_ID:
                $type = 'multiselect';
                break;

            case self::OPTION_IS_SUBSCRIBER:
                $type = 'select';
                break;
        }

        return $type;
    }

    protected function _prepareValueOptions()
    {
        $selectOptions = array();

        if ($this->getAttribute() === self::OPTION_GROUP_ID) {
            $selectOptions = Mage::helper('customer')->getGroups()->toOptionArray();

            array_unshift($selectOptions, array('value' => 0, 'label' => Mage::helper('rewards')->__('Not registered')));
        }

        if ($this->getAttribute() === self::OPTION_IS_SUBSCRIBER) {
            $selectOptions = array(
                array('value' => 0, 'label' => Mage::helper('rewards')->__('No')),
                array('value' => 1, 'label' => Mage::helper('rewards')->__('Yes')),
            );
        }

        $this->setData('value_select_options', $selectOptions);

        $hashedOptions = array();
        foreach ($selectOptions as $o) {
            $hashedOptions[$o['value']] = $o['label'];
        }
        $this->setData('value_option', $hashedOptions);

        return $this;
    }

    public function validate(Varien_Object $object)
    {
        if (!$object->getCustomer()) { //we don't check referred customers
            return true;
        }

        $customer = $object->getCustomer();
        $this->setReferredAttributes($customer);
        $result = $this->validateCustomer($customer);
//        echo 'Regular:'; var_dump($result); echo '<br>';
        return $result;
    }

    /**
     * @param Varien_Object $customer - can be registered customer and guest(!)
     *
     * @return bool
     */
    protected function validateCustomer(Varien_Object $customer)
    {
        $attrCode = $this->getAttribute();

        $subscriber = Mage::getModel('newsletter/subscriber')->loadByEmail($customer->getEmail());

        $reviews = Mage::getModel('review/review')->getCollection()
            ->addFieldToFilter('customer_id', $customer->getId());
        $reviewsCount = $reviews->count();

        $lifetimeSales = $this->getSumOfOrdersByCustomer($customer);
        $numberOfOrders = $this->getNumberOfOrdersByCustomer($customer);

        $customer
            ->setData(self::OPTION_IS_SUBSCRIBER, $subscriber->getId() ? 1 : 0)
            ->setData(self::OPTION_REVIEWS_NUMBER, $reviewsCount)
            ->setData(self::OPTION_ORDERS_SUM, $lifetimeSales)
                ->setData(self::OPTION_ORDERS_NUMBER, $numberOfOrders);

        $value = $customer->getData($attrCode);
        $res = $this->validateAttribute($value);
//        pr($customer->getData());
//        var_dump($res);
//        die;
        return $res;
    }

    /**
     * @param Mage_Customer_Model_Customer
     *
     * @return Mage_Customer_Model_Customer
     */
    protected function setReferredAttributes($customer)
    {
        $referrals = Mage::getModel('rewards/referral')->getCollection()
            ->addFieldToFilter('customer_id', $customer->getId())
            ->addFieldToFilter('new_customer_id', array('neq' => null));
        $customer->setData('referred_signups_number', $referrals->count());

        $numberOfOrders = 0;
        $numberOfOrdersAtLeastOnce = 0;
        $lifetimeSales = 0;
        foreach ($referrals as $referral) {
            $newCustomer = $referral->getNewCustomer();
            $lifetimeSales += $this->getSumOfOrdersByCustomer($newCustomer);
            $ordersNum = $this->getNumberOfOrdersByCustomer($newCustomer);
            $numberOfOrders += $ordersNum;
            if ($ordersNum > 0) {
                $numberOfOrdersAtLeastOnce++;
            }
        }
        $customer->setData(self::OPTION_REFERRED_ORDERS_SUM, $lifetimeSales);
        $customer->setData(self::OPTION_REFERRED_ORDERS_NUMBER, $numberOfOrders);
        $customer->setData(self::OPTION_REFERRED_NUMBER__ORDERED_AT_LEAST_ONCE, $numberOfOrdersAtLeastOnce);

        return $customer;
    }

    /**
     * @param Mage_Customer_Model_Customer $customer
     *
     * @return int
     */
    protected function getNumberOfOrdersByCustomer($customer)
    {
        $numberOfOrders = Mage::getModel('sales/order')->getCollection()
            ->addFieldToFilter('customer_email', $customer->getEmail())
            ->addFieldToFilter('status', $this->getConfig()->getGeneralEarnInStatuses())
            ->count();

        return $numberOfOrders;
    }

    /**
     * @param Mage_Customer_Model_Customer $customer
     *
     * @return float
     */
    protected function getSumOfOrdersByCustomer($customer)
    {
        /** @var Mage_Sales_Model_Resource_Sale_Collection $customerTotals */
        $customerTotals = Mage::getResourceModel('sales/sale_collection');
        $customerTotals
            ->addFieldToFilter('sales.customer_email', $customer->getEmail())
            ->setOrderStateFilter(Mage_Sales_Model_Order::STATE_CANCELED, true)
            ->addFieldToFilter('status', $this->getConfig()->getGeneralEarnInStatuses())
            ->load();
        $customerTotals = $customerTotals->getTotals();
        $lifetimeSales = floatval($customerTotals['lifetime']);

        return $lifetimeSales;
    }
}
