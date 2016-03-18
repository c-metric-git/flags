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


class Mirasvit_Rewards_Model_Earning_Rule_Condition_Referred_Customer extends Mirasvit_Rewards_Model_Earning_Rule_Condition_Customer
{
    public function loadAttributeOptions()
    {
        $attributes = array(
            self::OPTION_GROUP_ID => Mage::helper('rewards')->__('Referred: Group'),
            self::OPTION_ORDERS_SUM => Mage::helper('rewards')->__('Referred: Lifetime Sales'),
            self::OPTION_ORDERS_NUMBER => Mage::helper('rewards')->__('Referred: Number of Orders'),
            self::OPTION_IS_SUBSCRIBER => Mage::helper('rewards')->__('Referred: Is subscriber of newsletter'),
            self::OPTION_REVIEWS_NUMBER => Mage::helper('rewards')->__('Referred: Number of reviews'),
        );

        $this->setAttributeOption($attributes);
        return $this;
    }

    /**
     * @param Varien_Object $object
     * @return bool
     */
    public function validate(Varien_Object $object) {
        if (!$object->getReferredCustomer()) { //we don't check regular customers
            return true;
        }
        $result = $this->validateCustomer($object->getReferredCustomer());
//        echo 'Referred:'; var_dump($result);echo '<br>';
        return $result;
    }
}