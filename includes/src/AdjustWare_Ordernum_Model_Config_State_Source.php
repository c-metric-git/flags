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
class AdjustWare_Ordernum_Model_Config_State_Source
{

    public function toOptionArray()
    {
        return array(
            array('value' => 'custom', 'label' => Mage::helper('adminhtml')->__('Custom'), 'level' => 1),
            array('value' => 'stores', 'label' => Mage::helper('adminhtml')->__('Storeviews'), 'level' => 2),
            array('value' => 'websites', 'label' => Mage::helper('adminhtml')->__('Websites'), 'level' => 3),
            array('value' => 'global', 'label' => Mage::helper('adminhtml')->__('Global'), 'level' => 4
            ),
        );
    }
}