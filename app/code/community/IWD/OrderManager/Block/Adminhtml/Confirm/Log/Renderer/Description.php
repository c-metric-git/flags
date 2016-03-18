<?php
class IWD_OrderManager_Block_Adminhtml_Confirm_Log_Renderer_Description extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Action
{
    public function render(Varien_Object $row)
    {
        return $row['log_operations'];
    }
}