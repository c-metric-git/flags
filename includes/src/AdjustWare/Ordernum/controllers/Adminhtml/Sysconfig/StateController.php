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
class AdjustWare_Ordernum_Adminhtml_Sysconfig_StateController extends Mage_Adminhtml_Controller_Action
{
    public function getWarningAction()
    {
        $groups = $this->getRequest()->getParam('groups');
        $confState = $groups['general']['fields']['settings_level']['value'];
        $required = $this->_isRequireWarning($confState);
        if($required) {
            $this->getResponse()->setBody(Zend_Json::encode(array('warning'=>'true')));
        }
    }
    
    private function _isRequireWarning($requiredState)
    {
        $currentState = Mage::getStoreConfig('ordernum/general/settings_level');
        $hlpr = Mage::helper('adjordernum/config_state');
        $currentLevel = $hlpr->getLevel($currentState);
        $requiredLevel = $hlpr->getLevel($requiredState);
        return ($requiredLevel > $currentLevel);
    }
}