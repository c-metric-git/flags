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

class AW_Coupongenerator_Helper_Config extends Mage_Core_Helper_Abstract
{
    /**
     * "Sender" from system config
     */
    const NOTIFICATION_SENDER = 'coupongenerator/notifications/sender';

    /**
     * Email template from system config
     */
    const NOTIFICATION_TEMPLATE = 'coupongenerator/notifications/coupon_generation_template';

    /**
     * @param null|int|Mage_Core_Model_Store $store
     * @return array
     */
    public function getSender($store = null)
    {
        $senderCode = Mage::getStoreConfig(self::NOTIFICATION_SENDER, $store);
        return $this->_getStoreEmailAddressByCode($senderCode, $store);
    }

    /**
     * @param string $code
     * @param null|int|Mage_Core_Model_Store $store
     * @return array
     */
    protected function _getStoreEmailAddressByCode($code, $store = null)
    {
        return array(
            'name' => Mage::getStoreConfig('trans_email/ident_' . $code . '/name', $store),
            'mail' => Mage::getStoreConfig('trans_email/ident_' . $code . '/email', $store),
        );
    }

    /**
     * @param null|int|Mage_Core_Model_Store $store
     * @return array
     */
    public function getEmailTemplate($store = null)
    {
        return Mage::getStoreConfig(self::NOTIFICATION_TEMPLATE, $store);
    }

}
