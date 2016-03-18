<?php
class IWD_OrderManager_Adminhtml_Sales_ShipmentController extends Mage_Adminhtml_Sales_ShipmentController
{
    public function deleteAction()
    {
        if (Mage::getModel('iwd_ordermanager/shipment')->isAllowDeleteShipments()) {
            $checkedShipments = $this->getRequest()->getParam('shipment_ids');
            if (!is_array($checkedShipments))
                $checkedShipments = array($checkedShipments);

            try {
                foreach ($checkedShipments as $shipmentId) {
                    $shipment = Mage::getModel('iwd_ordermanager/shipment')->load($shipmentId);
                    if ($shipment->getId()) {
                        $shipment->DeleteShipment();
                    }
                }

                Mage::getSingleton('iwd_ordermanager/report')->AggregateSales();
                Mage::getSingleton('iwd_ordermanager/logger')->addMessageToPage();
            } catch (Exception $e) {
                Mage::log($e->getMessage(), null, 'iwd_order_manager.log');
                $this->_getSession()->addError($this->__('An error arose during the deletion. %s', $e));
                $this->_redirect('*/*/');
                return;
            }
        } else {
            $this->_getSession()->addError($this->__('This feature was deactivated.'));
            $this->_redirect('*/*/');
            return;
        }

        $this->_redirect('*/*/index');
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('iwd_ordermanager/shipment/actions/delete');
    }
}