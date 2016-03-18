<?php
class IWD_OrderManager_Adminhtml_Sales_GridController extends Mage_Adminhtml_Controller_Action
{
    public function deleteAction()
    {
        $redirect = $this->getRequest()->getParam('redirect');
        $redirect = (empty($redirect)) ? "*/sales_order/index" : "*/{$redirect}/index";

        if (Mage::getModel('iwd_ordermanager/order')->isAllowDeleteOrders()) {
            try {
                $checked_orders = $this->getCheckedOrderIds();
                foreach ($checked_orders as $order_id) {
                    $order = Mage::getModel('iwd_ordermanager/order')->load($order_id);
                    if ($order->getEntityId()) {
                        if($order->deleteOrder()){
                            $this->deleteFromOrderGrid($order_id);
                        }
                    } else {
                        $this->deleteFromOrderGrid($order_id);
                    }
                }

                Mage::getSingleton('iwd_ordermanager/report')->AggregateSales();
                Mage::getSingleton('iwd_ordermanager/logger')->addMessageToPage();
            } catch (Exception $e) {
                Mage::log($e->getMessage(), null, 'iwd_order_manager.log');
                $this->_getSession()->addError($this->__('An error during the deletion. %s', $e));
                $this->_redirect($redirect);
                return;
            }
        } else {
            $this->_getSession()->addError($this->__('This feature was deactivated.'));
            $this->_redirect($redirect);
            return;
        }

        $this->_redirect($redirect);
    }


    public function deleteFromOrderGrid($order_id)
    {
        $connection = Mage::getSingleton('core/resource')->getConnection('core_write');

        /* from order grid table */
        try {
            $sales_flat_order_grid = Mage::getSingleton('core/resource')->getTableName('sales_flat_order_grid');
            $connection->beginTransaction();
            $__condition = array($connection->quoteInto('entity_id=?', $order_id));
            $connection->delete($sales_flat_order_grid, $__condition);
            $connection->commit();
        } catch(Exception $e){

        }

        /* from archive order grid table */
        try{
            $iwd_sales_archive_order_grid = Mage::getSingleton('core/resource')->getTableName('iwd_sales_archive_order_grid');
            $connection->beginTransaction();
            $__condition = array($connection->quoteInto('entity_id=?', $order_id));
            $connection->delete($iwd_sales_archive_order_grid, $__condition);
            $connection->commit();
        } catch(Exception $e){

        }
    }

    public function changeStatusAction(){
        $redirect = "*/sales_order/index";

        if (Mage::getModel('iwd_ordermanager/order')->isAllowChangeOrderStatus()) {
            try {
                $status_id = $this->getRequest()->getParam('status');
                $checked_orders = $this->getCheckedOrderIds();

                foreach ($checked_orders as $order_id) {
                    $order = Mage::getModel('iwd_ordermanager/order')->load($order_id);
                    if ($order->getId()) {
                        $order->setData('status', $status_id)->save();
                    }
                }
                $this->_getSession()->addSuccess($this->__('Status was successfully changed'));
            } catch (Exception $e) {
                Mage::log($e->getMessage(), null, 'iwd_order_manager.log');
                $this->_getSession()->addError($this->__('An error arose during the updating. %s', $e));
            }
        } else {
            $this->_getSession()->addError($this->__('This feature was deactivated.'));
        }
        $this->_redirect($redirect);
    }

    public function orderedItemsAction()
    {
        $result = array('status' => 1);

        try{
            $order_id = $this->getRequest()->getPost('order_id');
            $ordered = Mage::getModel('sales/order')->load($order_id)->getItemsCollection();

            $result['table'] = $this->getLayout()
                ->createBlock('iwd_ordermanager/adminhtml_sales_order_grid_ordereditems')
                ->setData('ordered', $ordered)
                ->setData('order_id', $order_id)
                ->toHtml();
        } catch (Exception $e) {
            Mage::log($e->getMessage(), null, 'iwd_order_manager.log');
            $result = array('status' => 0, 'error' => $e->getMessage());
        }

        $this->getResponse()->setHeader('Content-type', 'application/json', true);
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    public function productItemsAction()
    {
        $result = array('status' => 1);

        try{
            $order_id = $this->getRequest()->getPost('order_id');
            $ordered = Mage::getModel('sales/order')->load($order_id)->getItemsCollection();

            $products = array();
            foreach ($ordered as $item){
                $prod_id = $item->getProductId();
                $products[$prod_id] = Mage::getModel('catalog/product')->load($prod_id);
            }

            $result['table'] = $this->getLayout()
                ->createBlock('iwd_ordermanager/adminhtml_sales_order_grid_productitems')
                ->setData('products', $products)
                ->setData('order_id', $order_id)
                ->toHtml();
        } catch (Exception $e) {
            Mage::log($e->getMessage(), null, 'iwd_order_manager.log');
            $result = array('status' => 0, 'error' => $e->getMessage());
        }

        $this->getResponse()->setHeader('Content-type', 'application/json', true);
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    protected function getCheckedOrderIds(){
        $checked_orders = $this->getRequest()->getParam('order_ids');
        if (!is_array($checked_orders)){
            $checked_orders = array($checked_orders);
        }
        return $checked_orders;
    }

    protected function _isAllowed()
    {
        $action = $this->getRequest()->getActionName();
        $action = strtolower($action);
        if($action == 'delete') {
            return Mage::getSingleton('admin/session')->isAllowed('iwd_ordermanager/order/actions/delete');
        }

        return true;
    }
}