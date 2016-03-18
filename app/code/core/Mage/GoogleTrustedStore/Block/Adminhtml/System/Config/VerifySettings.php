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

class Mage_GoogleTrustedStore_Block_Adminhtml_System_Config_VerifySettings
    extends Mage_GoogleTrustedStore_Block_Adminhtml_System_Config_Button
{
    /**
     * Return element html and configuration for the verify settings button
     * Loadsgoogletrustedstore/settings_js.phtml
     *
     * @param  Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $url = $this->_getOnclickUrl($element,'verifyConfig');
        $button = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData(array(
                'id'        => 'gts_verify',
                'label'     => Mage::helper('googletrustedstore')->__('Verify Settings'),
                'onclick'   => "verifyGoogleTrustedStoresSettings('{$url}')",
            ));
        $script = $this->getLayout()
            ->createBlock('core/template')
            ->setTemplate('googletrustedstore/settings_js.phtml')
            ->toHtml();
        return $script.$button->toHtml();
    }
}
