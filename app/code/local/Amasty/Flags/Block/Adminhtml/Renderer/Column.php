<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Flags
 */
class Amasty_Flags_Block_Adminhtml_Renderer_Column extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $columnBlock = Mage::app()->getLayout()->createBlock('amflags/adminhtml_sales_order_grid_column');
        return $columnBlock->setOrder($row)->toHtml();
    }
}