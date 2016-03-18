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
class AdjustWare_Ordernum_Block_Rewrite_AdminhtmlSalesCreditmemoGrid extends Mage_Adminhtml_Block_Sales_Creditmemo_Grid
{

    public function __construct()
    {
        parent::__construct();
    }

    protected function _prepareColumns()
    {
        $res = parent::_prepareColumns();
        
        $col = $this->getColumn('increment_id');
        $col->setType('text');
        
        $col = $this->getColumn('order_increment_id');
        $col->setType('text');
        
        return $res;
    }
}