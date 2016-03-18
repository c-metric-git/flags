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
class AdjustWare_Ordernum_Model_Config_State
{

    public function setCustom()
    {
        $helper = Mage::helper('adjordernum/path');
        $path = $helper->compilePath('general', 'settings_level');
        Mage::getConfig()->saveConfig($path, 'custom', 'default', '0');
        return true;
    }

    public function setGlobal()
    {
        $config = Mage::getModel('adjordernum/config');
        $config->deleteByScope('websites');
        $config->deleteByScope('stores');
        return true;
    }

    public function setWebsites()
    {
        $config = Mage::getModel('adjordernum/config');
        $config->deleteByScope('stores');
        $config->setInheritedByScope('websites');
        return true;
    }

    public function setStoreviews()
    {
        $config = Mage::getModel('adjordernum/config');
        $config->deleteByScope('websites');
        $config->setInheritedByScope('stores');
        return true;
    }
    
}