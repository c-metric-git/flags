<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Flags
 */
class Amasty_Flags_Block_Adminhtml_Flag_Grid_Renderer_Status extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        static $statuses = array();
        if (!$statuses)
        {
            $statuses = Mage::getSingleton('sales/order_config')->getStatuses();
        }
        $appliedStatuses = explode(',', $row->getData('apply_status'));
        $output = array();
        foreach ($appliedStatuses as $code)
        {
            if (isset($statuses[$code]))
            {
                $output[] = $statuses[$code];
            }
        }
        return implode(', ', $output);
    }
}