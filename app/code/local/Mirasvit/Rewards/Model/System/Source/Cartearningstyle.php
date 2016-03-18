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


class Mirasvit_Rewards_Model_System_Source_CartEarningStyle
{
    public static function toArray()
    {
        $result = array(
            Mirasvit_Rewards_Model_Config::EARNING_STYLE_GIVE => Mage::helper('rewards')->__('Give X points to customer'),
            Mirasvit_Rewards_Model_Config::EARNING_STYLE_AMOUNT_SPENT => Mage::helper('rewards')->__('Give X points for every spent Y'),
            Mirasvit_Rewards_Model_Config::EARNING_STYLE_QTY_SPENT => Mage::helper('rewards')->__('Give X points for every Z quantity'),
        );
        return $result;
    }

    public static function toOptionArray()
    {
        $options = self::toArray();
        $result  = array();

        foreach ($options as $key => $value)
            $result[] = array(
                'value' => $key,
                'label' => $value
            );

        return $result;
    }
}