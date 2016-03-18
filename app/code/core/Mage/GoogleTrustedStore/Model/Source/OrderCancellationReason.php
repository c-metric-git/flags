<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_GoogleTrustedStore
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

class Mage_GoogleTrustedStore_Model_Source_OrderCancellationReason
{
    private $_options;

    /**
     * Prepares array value=>label for available order cancellation reasons
     *
     * @return array
     * @throws RuntimeException If cannot read the reasons from the config
     */
    public function toOptionArray()
    {
        if (!is_array($this->_options)) {
            $this->_options = array();
            $reasons = Mage::getSingleton('googletrustedstore/config')->getCancellationReasons();
            foreach ($reasons as $code => $description) {
                $this->_options[] = array(
                    'value' => $code,
                    'label' => Mage::helper('googletrustedstore')->__((string)$description),
                );
            }
        }

        return $this->_options;
    }

    /**
     * Returns code of default cancelation reason
     *
     * @throws RuntimeException If cannot read the reason or reason is not listed
     * @return string Reason
     */
    public function getDefaultCode()
    {
        return Mage::getSingleton('googletrustedstore/config')->getDefaultCancellationReasonCode();
    }

    /**
     * Returns reason text description by specified reason code
     *
     * @param string $code
     * @return string
     */
    public function getDescriptionByCode($code)
    {
        return Mage::getSingleton('googletrustedstore/config')->getDescriptionOfCancellationReasonByCode($code);
    }

}
