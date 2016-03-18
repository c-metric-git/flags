<?php
class IWD_OrderManager_Block_Adminhtml_Confirm_Log_Renderer_Actions extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Action
{
    public function render(Varien_Object $row)
    {
        if($this->isShowElement($row)){
            return parent::render($row);
        }

        return "";
    }

    protected function isShowElement($row)
    {
        return !Mage::helper('iwd_ordermanager')->isGridExport()
            && isset($row['confirm_link']) && !empty($row['confirm_link'])
            && isset($row['status'])
            && $row['status'] == IWD_OrderManager_Model_Confirm_Options_Status::WAIT_CONFIRM;
    }
}
