<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Flags
 */
class Amasty_Flags_Block_Adminhtml_Widget_Grid_Massaction extends Mage_Adminhtml_Block_Widget_Grid_Massaction
{
    public function isAvailable()
    {
        Mage::dispatchEvent('am_grid_massaction_actions', array(
            'block' => $this,
            'page'  => $this->getRequest()->getControllerName(),
        ));  
        
        return parent::isAvailable();
    }    
}
