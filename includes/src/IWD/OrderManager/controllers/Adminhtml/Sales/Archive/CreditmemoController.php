<?php
class IWD_OrderManager_Adminhtml_Sales_Archive_CreditmemoController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('sales')
            ->_title($this->__('IWD Order Manager - Archive - Credit Memo'));

        $this->_addBreadcrumb(
            Mage::helper('iwd_ordermanager')->__('IWD Order Manager - Archive - Credit Memo'),
            Mage::helper('iwd_ordermanager')->__('IWD Order Manager - Archive - Credit Memo')
        );

        $this->_addContent($this->getLayout()->createBlock('iwd_ordermanager/adminhtml_sales_order_archive_creditmemos'));
        $this->renderLayout();
    }

    public function exportCsvAction()
    {
        $fileName = 'archive_credit_memos.csv';
        $grid = $this->getLayout()->createBlock('iwd_ordermanager/adminhtml_sales_order_archive_creditmemos_grid');

        if (empty($fileName) || empty($grid)) {
            return;
        }

        $this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
    }

    public function exportExcelAction()
    {
        $fileName = 'archive_credit_memos.xml';
        $grid = $this->getLayout()->createBlock('iwd_ordermanager/adminhtml_sales_order_archive_creditmemos_grid');

        if (empty($fileName) || empty($grid)) {
            return;
        }

        $this->_prepareDownloadResponse($fileName, $grid->getExcelFile());
    }

    public function gridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('iwd_ordermanager/adminhtml_sales_order_archive_creditmemos_grid')->toHtml()
        );
    }

    protected function _isAllowed()
    {
        if(Mage::helper('iwd_ordermanager')->isEnterpriseMagentoEdition()){
            return false;
        }

        return Mage::getSingleton('admin/session')->isAllowed('sales/iwd_ordermanager_archive/archive_creditmemos');
    }
}