<?php
/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This software is designed to work with Magento community edition and
 * its use on an edition other than specified is prohibited. aheadWorks does not
 * provide extension support in case of incorrect edition use.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Coupongenerator
 * @version    1.0.2
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE.txt
 */


class AW_Coupongenerator_Model_Source_Coupon_Status extends Varien_Object
{
    const AVAILABLE_VALUE = 'Available';
    const USED_VALUE      = 'Used';
    const EXPIRED_VALUE   = 'Expired';

    const AVAILABLE_LABEL = 'Available';
    const USED_LABEL      = 'Used';
    const EXPIRED_LABEL   = 'Expired';

    /**
     * @return array
     */
    public static function toOptionArray()
    {
        return array(
            array(
                'value' => self::AVAILABLE_VALUE,
                'label' => Mage::helper('coupongenerator')->__(self::AVAILABLE_LABEL)
            ),
            array(
                'value' => self::USED_VALUE,
                'label' => Mage::helper('coupongenerator')->__(self::USED_LABEL)
            ),
            array(
                'value' => self::EXPIRED_VALUE,
                'label' => Mage::helper('coupongenerator')->__(self::EXPIRED_LABEL)
            ),
        );
    }

    /**
     * @return array
     */
    public static function toOptionHash()
    {
        return array(
            self::AVAILABLE_VALUE => Mage::helper('coupongenerator')->__(self::AVAILABLE_LABEL),
            self::USED_VALUE      => Mage::helper('coupongenerator')->__(self::USED_LABEL),
            self::EXPIRED_VALUE   => Mage::helper('coupongenerator')->__(self::EXPIRED_LABEL),
        );
    }
}
