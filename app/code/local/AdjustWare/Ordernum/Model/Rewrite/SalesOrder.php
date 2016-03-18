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
class AdjustWare_Ordernum_Model_Rewrite_SalesOrder extends Mage_Sales_Model_Order
{
    protected function _beforeSave()
    {
        Mage::dispatchEvent('adjordernum_sales_order_save_before', array('order' => $this));
        return parent::_beforeSave();
    }
}