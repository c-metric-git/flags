<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Flags
 */


class Amasty_Flags_Block_Adminhtml_Element_Multiselect extends Varien_Data_Form_Element_Multiselect
{
    /***
     * Add "disabled" attribute support
     *
     * @param $option
     * @param $selected
     *
     * @return string
     */
    protected function _optionToHtml($option, $selected)
    {
        $html = '<option value="'.$this->_escape($option['value']).'"';
        $html.= isset($option['title']) ? 'title="'.$this->_escape($option['title']).'"' : '';
        $html.= isset($option['style']) ? 'style="'.$option['style'].'"' : '';
        $html.= isset($option['disabled']) ? 'disabled=disabled' : '';
        if (in_array((string)$option['value'], $selected)) {
            $html.= ' selected="selected"';
        }
        $html.= '>'.$this->_escape($option['label']). '</option>'."\n";
        return $html;
    }
}