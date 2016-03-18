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
class  AdjustWare_Ordernum_Helper_Config_State 
{
    /**
     * get level by value
     * @param type $value
     * @return string 
     */
    public function getLevel($value)
    {
        $options = Mage::getModel('adjordernum/config_state_source')->toOptionArray();
        foreach ($options as $content)
        {
            if ($content['value'] == $value)
            {
                return $content['level'];
            }
        }
        
        Mage::throwException('Invalid Config State value');
    }
}