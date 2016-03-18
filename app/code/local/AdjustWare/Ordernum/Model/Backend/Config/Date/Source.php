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
 * @copyright  Copyright (c) 2011 AITOC, Inc.
 * @package    AdjustWare_Ordernum
 * @author lyskovets
 */
class AdjustWare_Ordernum_Model_Backend_Config_Date_Source
{
    public function toOptionArray()
    {   
        return array(
            array('value' => null, 'label'=>Mage::helper('adminhtml')->__('No')),
            array('value' => 'Ymd', 'label'=>Mage::helper('adminhtml')->__('YYYYMMDD')),
            array('value' => 'mdY', 'label'=>Mage::helper('adminhtml')->__('MMDDYYYY')),
            array('value' => 'dmY', 'label'=>Mage::helper('adminhtml')->__('DDMMYYYY')),
            array('value' => 'ymd', 'label'=>Mage::helper('adminhtml')->__('YYMMDD')),
            array('value' => 'mdy', 'label'=>Mage::helper('adminhtml')->__('MMDDYY')),
            array('value' => 'dmy', 'label'=>Mage::helper('adminhtml')->__('DDMMYY')),
            array('value' => 'ymd-', 'label'=>Mage::helper('adminhtml')->__('YYMMDD-')),
            array('value' => 'ym-', 'label'=>Mage::helper('adminhtml')->__('YYMM-')), 
        );
    }
}