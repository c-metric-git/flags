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
abstract class AdjustWare_Ordernum_Model_Backend_Validation_Abstract extends Varien_Object
{
    /**
     *
     * @var AdjustWare_Ordernum_Helper_Data
     */
    protected $_helper;

    public function  __construct(Varien_Object $data)
    {
        $this->addData($data->getData());
        $this->_helper = Mage::helper('adjordernum/data');
        $this->_init();
    }

    protected function _init()
    {
        $field = Mage::getModel('adjordernum/backend_config')
            ->getField($this->getScope(),$this->getGroup(),$this->getField());
        $this->setFieldLabel($field['label']);
    }

    abstract public function validate();



}
?>