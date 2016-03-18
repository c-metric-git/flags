<?php
/**
 * Custom Order Number Pro
 *
 * @category:    AdjustWare
 * @package:     AdjustWare_Ordernum
 * @version      5.1.5
 * @license:     d0NyTkcYcW64yuyl9Cf2M6q3gBilLUVMAwQSumkwPP
 * @copyright:   Copyright (c) 2015 AITOC, Inc. (http://www.aitoc.com)
 */
/**
 *
 * @copyright  Copyright (c) 2011 AITOC, Inc.
 * @package    AdjustWare_Ordernum
 * @author lyskovets
 */
class AdjustWare_Ordernum_Block_Adminhtml_Config_State
    extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $this->setTemplate('adjordernum/sysconfig/state/js.phtml');
        $this->_initControllerUrl();
    }
    
    protected function _initControllerUrl()
    {
        $route = 'adjordernum/adminhtml_sysconfig_state/getWarning';
        $url = $this->getUrl($route);
        $this->setWarningUrl($url);
    }

    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $html = parent::render($element);
        $js = $this->_toHtml();
        $html .= $js;
        return $html;
    }
}