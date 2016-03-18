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
class AdjustWare_Ordernum_Model_Backend_Validation_Letter extends  Mage_Core_Model_Config_Data
{
    public function save()
    {
        $value = $this->getValue();
		$data = $this->getData();
        $field = $data['field_config'];
		
        if(empty($value) && ((int)$this->getFieldsetDataValue('suffixlength') > 0)) {
            Mage::throwException($field->label . Mage::helper('adjordernum')->__(' is not defined'));
        }
        if(!empty($value) && !preg_match("/^[a-zA-Z]+$/",$value)) {
            Mage::throwException($field->label . Mage::helper('adjordernum')->__(' should contain letters only'));
        }
        return parent::save();
    }
}