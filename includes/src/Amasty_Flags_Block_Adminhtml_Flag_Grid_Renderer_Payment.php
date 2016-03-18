<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Flags
 */
class Amasty_Flags_Block_Adminhtml_Flag_Grid_Renderer_Payment extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $methods = Mage::getStoreConfig('payment');
        $appliedMethods = explode(',', $row->getData('apply_payment'));
        $output = array();
        foreach ($appliedMethods as $code) {
            if (isset($methods[$code])) {
                if (isset($methods[$code]['title'])) {
                    $output[] = $methods[$code]['title'];
                } else {
                    $output[] = $code;
                }
            }
        }
        return implode(', ', $output);
    }
}