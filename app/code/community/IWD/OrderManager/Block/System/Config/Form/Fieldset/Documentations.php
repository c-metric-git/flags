<?php
class IWD_OrderManager_Block_System_Config_Form_Fieldset_Documentations extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    const USER_GUIDE_URL = "https://docs.google.com/document/d/1q4suHEQkp62dh2N2ltHlf7HBUjhx2p-Pp_cGXZUleb8/";

    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $user_guide = Mage::helper('iwd_ordermanager')->__('User Guide');
        return '<span class="notice"><a href="' . self::USER_GUIDE_URL . '" target="_blank">' . $user_guide . '</a></span>'
            . $this->conflicts();
    }

    protected function conflicts(){
        $rewrites = Mage::getModel('iwd_ordermanager/conflicts')->getRewritesClasses();
        $conflicts = "";
        foreach($rewrites as $base => $classes){
            $conflicts .= '<li>'.$base.'<ul style="margin-left:40px;">';
            foreach($classes as $class){
                $conflicts .= '<li>'.$class.'</li>';
            }
            $conflicts .= "</ul></li>";
        }

        if(empty($conflicts)){
            return "";
        }

        return '<div style="border:1px dotted red;margin:5px 0;padding:0 5px;"><p class="error">WARNING! Extension has conflicts:</p>'
        . $conflicts
        . '</div><p class="note error"><span>Please, resolve conflicts for correct work.</span></p>';
    }
}