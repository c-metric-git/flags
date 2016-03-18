<?php

class PostcodeAnywhere_CapturePlus_Block_ScriptAccount extends Mage_Adminhtml_Block_Template
{
    protected function _prepareLayout()
    {
        $section = $this->getAction()->getRequest()->getParam('section', false);
        if ($section == 'captureplus') {
            $head = $this->getLayout()->getBlock('head');
            if ($head) {
                $head->addCss('captureplus/address-3.40.min.css');
                $head->addJs('captureplus/address-3.40.min.js');
            }
        }
        parent::_prepareLayout();
    }

    protected function _toHtml()
    {
        $section = $this->getAction()->getRequest()->getParam('section', false);
        if ($section == 'captureplus') {
            return parent::_toHtml();
        } else {
           return '';
        }
    }
}
