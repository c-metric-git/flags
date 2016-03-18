<?php
class IWD_OrderManager_Adminhtml_ConfirmController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->logAction();
    }

    public function logAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('system')
            ->_title($this->__('IWD Order Manager - Log Operations'));

        $this->_addBreadcrumb(
            Mage::helper('iwd_ordermanager')->__('IWD Order Manager - Log Operations'),
            Mage::helper('iwd_ordermanager')->__('IWD Order Manager - Log Operations')
        );

        $this->_addContent($this->getLayout()->createBlock('iwd_ordermanager/adminhtml_confirm_log'));
        $this->renderLayout();
    }

    public function gridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('iwd_ordermanager/adminhtml_confirm_log_grid')->toHtml()
        );
    }

    /** http://site.com/admin/iwd_ordermanager/confirm/edit/action/confirm/pid/000000000000000 **/
    /** http://site.com/admin/iwd_ordermanager/confirm/edit/action/cancel/pid/000000000000000 **/
    public function editAction()
    {
        $action = $this->getRequest()->getParam('action');
        $id = $this->getRequest()->getParam('id');
        $helper = Mage::helper('iwd_ordermanager');

        /** error **/
        if (empty($action) || empty($id)) {
            $this->_getSession()->addError($helper->__('Error cancel query'));
            $this->_redirect('*/confirm/log');
            return;
        }

        /** confirm **/
        if ($action == 'confirm') {
            $status = Mage::getModel('iwd_ordermanager/confirm_operations')->confirmById($id);

            if ($status) {
                $this->_getSession()->addSuccess($helper->__('Query was confirmed.'));
            } else {
                $this->_getSession()->addError($helper->__('Error confirm query.'));
            }
        }

        /** confirm **/
        else if ($action == 'cancel') {
            $status = Mage::getModel('iwd_ordermanager/confirm_operations')->cancelConfirmById($id);

            if ($status) {
                $this->_getSession()->addSuccess($helper->__('Query was canceled.'));
            } else {
                $this->_getSession()->addError($helper->__('Error cancel query.'));
            }
        }

        $this->_redirect('*/confirm/log');
        return;
    }

    protected function _isAllowed()
    {
        return true;
    }
}