<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Flags
 */
class Amasty_Flags_Block_Adminhtml_Flag_Grid_Renderer_Shipping extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        static $methods = array();
        if (!$methods)
        {
            $methods = Mage::getStoreConfig('carriers');
        }
        $appliedMethods = explode(',', $row->getData('apply_shipping'));
        $output = array();
        foreach ($appliedMethods as $code)
        {
            if (isset($methods[$code]))
            {
                $output[] = $methods[$code]['title'];
            }
        }
        return implode(', ', $output);
    }
}
