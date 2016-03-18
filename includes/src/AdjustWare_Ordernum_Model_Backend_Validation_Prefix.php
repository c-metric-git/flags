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
class AdjustWare_Ordernum_Model_Backend_Validation_Prefix extends  Mage_Core_Model_Config_Data
{
    public function save()
    {
        $value = $this->getValue();
		$data = $this->getData();
        $field = $data['field_config'];
		
        if(!empty($value)) {
            if(!ctype_print($value)) {
                Mage::throwException($field->label . Mage::helper('adjordernum')->__(' should contain printable characters'));
            }
            $charlist = '!@#$%-_&';
            if(!preg_match("/^[a-zA-Z0-9!@#$%\-_&]+$/",$value)) {
                Mage::throwException(Mage::helper('adjordernum')->__('Every character in ' . $field->label . ' text is either a letter, a digit or symbols: ' . $charlist));
            }
        }
        return parent::save();
    }   
}