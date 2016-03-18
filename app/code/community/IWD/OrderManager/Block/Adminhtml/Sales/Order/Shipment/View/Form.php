<?php
class IWD_OrderManager_Block_Adminhtml_Sales_Order_Shipment_View_Form extends Mage_Adminhtml_Block_Sales_Order_Shipment_View_Form
{
    public function canCreateShippingLabel()
    {
        $carrier = $this->getOrder()->getShippingCarrier();
        if (method_exists($carrier, 'isShippingLabelsAvailable')) {
            return $carrier->isShippingLabelsAvailable();
        } else {
            return false;
        }
    }
}