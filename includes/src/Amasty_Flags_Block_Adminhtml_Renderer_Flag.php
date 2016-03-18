<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Flags
 */
class Amasty_Flags_Block_Adminhtml_Renderer_Flag extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $flagBlock = Mage::app()->getLayout()->createBlock('amflags/adminhtml_sales_order_grid_flag');
        return $flagBlock->setOrder($row)->setCurrentColumn($this->getColumn())->toHtml();
    }
}
