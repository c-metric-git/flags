<?php
class IWD_OrderManager_Adminhtml_Sales_Backup_SalesController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('system')
            ->_title($this->__('IWD Order Manager - Backups'));

        $this->_addBreadcrumb(
            Mage::helper('iwd_ordermanager')->__('IWD Order Manager - Backup - Sales'),
            Mage::helper('iwd_ordermanager')->__('IWD Order Manager - Backup - Sales')
        );

        $this->_addContent($this->getLayout()->createBlock('iwd_ordermanager/adminhtml_sales_order_backup_sales'));
        $this->renderLayout();
    }

    public function massDeleteAction()
    {
        $backupIds = $this->getRequest()->getParam('backup');
        if (!is_array($backupIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($backupIds as $id) {
                    $model = Mage::getModel('iwd_ordermanager/backup_sales')->load($id);
                    $model->delete();
                }
                Mage::getSingleton('adminhtml/session')
                    ->addSuccess(Mage::helper('adminhtml')->__('Total of %d record(s) were successfully deleted', count($backupIds)));
            } catch (Exception $e) {
                Mage::log($e->getMessage(), null, 'iwd_order_manager.log');
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    public function gridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('iwd_ordermanager/adminhtml_sales_order_backup_sales_grid')->toHtml()
        );
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('sales/iwd_ordermanager_backups/sales');
    }
}