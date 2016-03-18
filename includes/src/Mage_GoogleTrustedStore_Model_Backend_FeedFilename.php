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

class Mage_GoogleTrustedStore_Model_Backend_FeedFilename extends Mage_Core_Model_Config_Data
{
    /**
     * Validates value before saving.  The values must not contain non-word characters, except for a period (.)
     *
     */
    protected function _beforeSave()
    {
        if (preg_match('/[^a-z0-9_.]+/i', $this->getValue())) {
            throw new Exception(Mage::helper('googletrustedstore')->__(
                'Please use only letters (a-z or A-Z), numbers (0-9), underscore (_) or dot (.) in feed filename field. No spaces or other characters are allowed.'
            ));
        }
    }
}