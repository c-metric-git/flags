<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Flags
 */
class Amasty_Flags_Block_Adminhtml_Flag_Grid_Renderer_Flag extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $url = Amasty_Flags_Model_Flag::getUploadUrl() . $row->getId() . '.jpg';
        $html = '<img src="' . $url . '" title="'.$row->getComment().'" alt="'.$row->getAlias().'" border="0" />';
        return $html;
    }
}