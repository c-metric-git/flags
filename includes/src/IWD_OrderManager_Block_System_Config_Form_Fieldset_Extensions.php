<?php
class IWD_OrderManager_Block_System_Config_Form_Fieldset_Extensions extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $helper = Mage::helper('iwd_ordermanager');
        $available = $helper->isAvailableVersion();
        $version = $helper->getExtensionVersion();

        if ($available) {
            return '<span class="notice">' . $version . '</span>';
        } else {
            return sprintf('<span class="error">%s<br />%s<br />%s<br /> <a href="http://www.iwdextensions.com" target="_blank">www.iwdextensions.com</a></span>',
                $version,
                $helper->__("This module is available for Magento CE only."),
                $helper->__("You are using Enterprise version of Magento."),
                $helper->__("Please obtain Enterprise copy of the module at")
            );
        }
    }
}