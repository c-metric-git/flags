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

class Mage_GoogleTrustedStore_Block_Adminhtml_System_Config_Button
    extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    /**
     * Remove scope label
     *
     * @param  Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
        return parent::render($element);
    }
    
    /**
     * Generates URL for onclick action
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @param string $actionName
     * @return string
     */
    protected function _getOnclickUrl(Varien_Data_Form_Element_Abstract $element, $actionName)
    {
        $params = array();
        $configForm = $element->getForm()->getParent();
        if ($configForm->getScope() == 'websites') {
            $params['website_id'] = $configForm->getScopeId();
        } elseif ($configForm->getScope() == 'stores') {
        	$params['store_id'] = $configForm->getScopeId();
        }
        return $this->getUrl('*/googletrustedstore_feed/'.$actionName, $params);
    }
}
