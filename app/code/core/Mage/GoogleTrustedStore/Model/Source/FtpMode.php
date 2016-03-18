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

class Mage_GoogleTrustedStore_Model_Source_FtpMode
{
    private $_options;
    /**
     * Prepares array value=>label for available ftp modes
     *
     * @return array
     */
    public function toOptionArray()
    {
        if (!is_array($this->_options)) {
            $this->_options = array(
                array('value' => 0, 'label' => Mage::helper('googletrustedstore')->__('Active')),
                array('value' => 1, 'label' => Mage::helper('googletrustedstore')->__('Passive')),
            );
        }
        return $this->_options;
    }
}
