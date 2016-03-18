<?php
abstract class IWD_OrderManager_Model_Confirm_Options_Abstract
{
    public abstract function toOption();

    public function toOptionArray()
    {
        $options_array = array();

        $options = $this->toOption();
        foreach($options as $value=>$label){
            $options_array[] = array('value' => $value, 'label' => $label);
        }

        return $options_array;
    }
}
