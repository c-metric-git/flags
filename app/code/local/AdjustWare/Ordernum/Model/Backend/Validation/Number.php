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
class AdjustWare_Ordernum_Model_Backend_Validation_Number extends  Mage_Core_Model_Config_Data
{
    public function save()
    {
        $value = $this->getValue();
		$data = $this->getData();
        $field = $data['field_config'];
		
        if(empty($value)) {
            Mage::throwException($field->label . Mage::helper('adjordernum')->__(' is not defined'));
        }
        if(!ctype_digit($value)) {
            Mage::throwException($field->label . Mage::helper('adjordernum')->__(' should be numeric only'));
        }
        return parent::save();
    }
}