<?php
class IWD_OrderManager_Block_Adminhtml_Sales_Order_Grid_Renderer_Creditmemo extends IWD_OrderManager_Block_Adminhtml_Sales_Order_Grid_Renderer_Abstract
{
    protected function loadCreditMemos()
    {
        $order_id = $this->getOrderId();

        return Mage::getResourceModel('sales/order_creditmemo_grid_collection')
            ->addFieldToSelect('increment_id')
            ->addFieldToFilter('main_table.order_id', $order_id)
            ->load();
    }

    protected function prepareCreditMemoIds()
    {
        $credit_memos = $this->loadCreditMemos();
        $increment_ids = array();

        foreach ($credit_memos as $creditmemo) {
            $increment_ids[] = $creditmemo->getIncrementId();
        }

        return $increment_ids;
    }

    protected function Grid()
    {
        $increment_ids = $this->prepareCreditMemoIds();
        return $this->formatBigData($increment_ids);
    }

    protected function Export()
    {
        $increment_ids = $this->prepareCreditMemoIds();
        return implode(',', $increment_ids);
    }
}