<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at http://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   Reward Points + Referral program
 * @version   1.1.2
 * @build     928
 * @copyright Copyright (C) 2015 Mirasvit (http://mirasvit.com/)
 */



class Mirasvit_Rewards_Adminhtml_TransactionController extends Mage_Adminhtml_Controller_Action
{
    protected function _initAction()
    {
        $this->loadLayout()->_setActiveMenu('rewards');

        return $this;
    }

    public function indexAction()
    {
        $this->_title($this->__('Transactions'));
        $this->_initAction();
        $this->_addContent($this->getLayout()
            ->createBlock('rewards/adminhtml_transaction'));
        $this->renderLayout();
    }

    public function addAction()
    {
        $this->_title($this->__('New Transaction'));

        $this->_initTransaction();

        $this->_initAction();
        $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Transaction  Manager'),
                Mage::helper('adminhtml')->__('Transaction Manager'), $this->getUrl('*/*/'));
        $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Add Transaction '), Mage::helper('adminhtml')->__('Add Transaction'));

        $this->getLayout()
            ->getBlock('head')
            ->setCanLoadExtJs(true);
        $this->_addContent($this->getLayout()->createBlock('rewards/adminhtml_transaction_edit'));

        $grid = $this->getLayout()->createBlock('rewards/adminhtml_transaction_edit_customer_grid', 'customerGrid');
        $this->_addContent($grid);
        $this->_addJs($this->getLayout()->createBlock('adminhtml/template')->setTemplate('mst_rewards/transaction/customer/grid/js.phtml'));
        $this->renderLayout();
    }

    // public function editAction ()
    // {
    //     $transaction = $this->_initTransaction();

    //     if ($transaction->getId()) {
    //         $this->_title($this->__("Edit Transaction '%s'", $transaction->getName()));
    //         $this->_initAction();
    //         $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Transactions'),
    //                 Mage::helper('adminhtml')->__('Transactions'), $this->getUrl('*/*/'));
    //         $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Edit Transaction '),
    //                 Mage::helper('adminhtml')->__('Edit Transaction '));

    //         $this->getLayout()
    //             ->getBlock('head')
    //             ->setCanLoadExtJs(true);
    //         $this->_addContent($this->getLayout()->createBlock('rewards/adminhtml_transaction_edit'));

    //         $this->renderLayout();
    //     } else {
    //         Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('The Transaction does not exist.'));
    //         $this->_redirect('*/*/');
    //     }
    // }

    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost()) {
            $customers = $data['in_transaction_user'];
            parse_str($customers, $customers);
            $customers = array_keys($customers);

            try {
                foreach ($customers as $customerId) {
                    if ((int) $customerId > 0) {
                        $emailMessage = '';
                        if (isset($data['email_message'])) {
                            $emailMessage = $data['email_message'];
                        }
                        Mage::helper('rewards/balance')->changePointsBalance($customerId, $data['amount'], $data['history_message'], false, true, $emailMessage);
                    }
                }

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Transaction was successfully saved'));
                Mage::getSingleton('adminhtml/session')->setFormData(false);

                $this->_redirect('*/*/index');

                return;
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/index');

                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Unable to find Transaction to save'));
        $this->_redirect('*/*/');
    }

    public function deleteAction()
    {
        if ($this->getRequest()->getParam('id') > 0) {
            try {
                $transaction = Mage::getModel('rewards/transaction');

                $transaction->setId($this->getRequest()
                    ->getParam('id'))
                    ->delete();

                Mage::getSingleton('adminhtml/session')->addSuccess(
                        Mage::helper('adminhtml')->__('Transaction was successfully deleted'));
                $this->_redirect('*/*/');
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()
                    ->getParam('id'), ));
            }
        }
        $this->_redirect('*/*/');
    }

    public function massDeleteAction()
    {
        $ids = $this->getRequest()->getParam('transaction_id');
        if (!is_array($ids)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select Transaction(s)'));
        } else {
            try {
                foreach ($ids as $id) {
                    /** @var Mirasvit_Rewards_Model_Transaction $transaction */
                    $transaction = Mage::getModel('rewards/transaction')
                        ->setIsMassDelete(true)
                        ->load($id);
                    $transaction->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($ids)
                    )
                );
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    public function _initTransaction()
    {
        $transaction = Mage::getModel('rewards/transaction');
        if ($this->getRequest()->getParam('id')) {
            $transaction->load($this->getRequest()->getParam('id'));
        }

        Mage::register('current_transaction', $transaction);

        return $transaction;
    }

    /************************/

    /**
     * Grid Action
     * Display list of products related to current category.
     */
    public function gridAction()
    {
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('rewards/adminhtml_transaction_edit_customer_grid', 'customer.grid')
                ->toHtml()
        );
    }

    public function customergridAction()
    {
        $id = $this->getRequest()->getParam('id');
        $customer = Mage::getModel('customer/customer')->load($id);
        Mage::register('current_customer', $customer);

        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('rewards/adminhtml_customer_edit_tabs_transaction_grid', 'rewards.grid')
                ->toHtml()
        );
    }
}
