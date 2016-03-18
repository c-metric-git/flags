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
 */

class AdjustWare_Ordernum_Block_Adminhtml_Ordernum extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
        {
            parent::__construct();

            $this->_controller = 'adminhtml_ordernum';
            $this->_blockGroup = 'adjordernum';
            $this->_headerText = Mage::helper('adjordernum')->__('Custom Number Settings');
            $this->_removeButton('add');
        }
}