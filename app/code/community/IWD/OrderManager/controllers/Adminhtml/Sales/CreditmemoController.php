<?php
class IWD_OrderManager_Adminhtml_Sales_CreditmemoController extends Mage_Adminhtml_Sales_CreditmemoController
{
    public function deleteAction()
    {
        if (Mage::getModel('iwd_ordermanager/creditmemo')->isAllowDeleteCreditmemos()) {
            $checkedCreditmemos = $this->getRequest()->getParam('creditmemo_ids');
            if (!is_array($checkedCreditmemos))
                $checkedCreditmemos = array($checkedCreditmemos);

            try {
                foreach ($checkedCreditmemos as $creditmemoId) {
                    $creditmemo = Mage::getModel('iwd_ordermanager/creditmemo')->load($creditmemoId);
                    if($creditmemo->getId()){
                        $creditmemo->deleteCreditmemo();
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
        return Mage::getSingleton('admin/session')->isAllowed('iwd_ordermanager/creditmemo/actions/delete');
    }
}