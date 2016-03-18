<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Flags
 */
class Amasty_Flags_Block_Adminhtml_Column_Grid_Renderer_Flag extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        static $flags = array();
        if (!$flags)
        {
            $flags = Mage::getModel('amflags/flag')->getCollection();
        }
        $appliedFlags = explode(',', $row->getData('apply_flag'));
        
        $output = array();
        foreach ($appliedFlags as $flagId)
        {
            foreach ($flags as $flag)
            {
                if ($flagId == $flag->getEntityId())
                {
                    $url = Amasty_Flags_Model_Flag::getUploadUrl() . $flagId . '.jpg';
                    $output[] = '<img src="' . $url . '" title="' . $flag->getComment() . '" alt="' . $flag->getAlias() . '" border="0" />'.$flag->getAlias();
                    break;
                }
            }
        }
        return implode(', ', $output);
    }
}