<?php
class IWD_OrderManager_Model_System_Config_Status_Order
{
    public function toOptionArray()
    {
        $statuses = Mage::getSingleton('sales/order_config')->getStatuses();
        
        $options = array(
            array(
               'value' => false,
               'label' => Mage::helper('adminhtml')->__('-- Not select --')
            )
        );
            
        foreach ($statuses as $code=>$label) {
            $options[] = array(
               'value' => $code,
               'label' => $label
            );
        }
        
        return $options;
    }
}
