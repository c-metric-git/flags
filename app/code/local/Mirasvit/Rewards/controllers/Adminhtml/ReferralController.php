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



class Mirasvit_Rewards_Adminhtml_ReferralController extends Mage_Adminhtml_Controller_Action
{
    protected function _initAction()
    {
        $this->loadLayout()->_setActiveMenu('rewards');

        return $this;
    }

    public function indexAction()
    {
        $this->_title($this->__('Customer Referrals'));
        $this->_initAction();
        $this->_addContent($this->getLayout()
            ->createBlock('rewards/adminhtml_referral'));
        $this->renderLayout();
    }

    public function addAction()
    {
        $this->_title($this->__('New Referral'));

        $this->_initReferral();

        $this->_initAction();
        $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Referral  Manager'),
                Mage::helper('adminhtml')->__('Referral Manager'), $this->getUrl('*/*/'));
        $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Add Referral '), Mage::helper('adminhtml')->__('Add Referral'));

        $this->getLayout()
            ->getBlock('head')
            ->setCanLoadExtJs(true);
        $this->_addContent($this->getLayout()->createBlock('rewards/adminhtml_referral_edit'));
        $this->renderLayout();
    }

    public function editAction()
    {
        $referral = $this->_initReferral();

        if ($referral->getId()) {
            $this->_title($this->__("Edit Referral '%s'", $referral->getName()));
            $this->_initAction();
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Customer Referrals'),
                    Mage::helper('adminhtml')->__('Customer Referrals'), $this->getUrl('*/*/'));
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Edit Referral '),
                    Mage::helper('adminhtml')->__('Edit Referral '));

            $this->getLayout()
                ->getBlock('head')
                ->setCanLoadExtJs(true);
            $this->_addContent($this->getLayout()->createBlock('rewards/adminhtml_referral_edit'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('The Referral does not exist.'));
            $this->_redirect('*/*/');
        }
    }

    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost()) {
            $referral = $this->_initReferral();
            $referral->addData($data);
            //format date to standart
            // $format = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
            // Mage::helper('mstcore/date')->formatDateForSave($referral, 'active_from', $format);
            // Mage::helper('mstcore/date')->formatDateForSave($referral, 'active_to', $format);

            try {
                $referral->save();

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Referral was successfully saved'));
                Mage::getSingleton('adminhtml/session')->setFormData(false);

                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $referral->getId()));

                    return;
                }
                $this->_redirect('*/*/');

                return;
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));

                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Unable to find Referral to save'));
        $this->_redirect('*/*/');
    }

    public function deleteAction()
    {
        if ($this->getRequest()->getParam('id') > 0) {
            try {
                $referral = Mage::getModel('rewards/referral');

                $referral->setId($this->getRequest()
                    ->getParam('id'))
                    ->delete();

                Mage::getSingleton('adminhtml/session')->addSuccess(
                        Mage::helper('adminhtml')->__('Referral was successfully deleted'));
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
        $ids = $this->getRequest()->getParam('referral_id');
        if (!is_array($ids)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select Referral(s)'));
        } else {
            try {
                foreach ($ids as $id) {
                    /** @var Mirasvit_Rewards_Model_Referral $referral */
                    $referral = Mage::getModel('rewards/referral')
                        ->setIsMassDelete(true)
                        ->load($id);
                    $referral->delete();
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

    public function _initReferral()
    {
        $referral = Mage::getModel('rewards/referral');
        if ($this->getRequest()->getParam('id')) {
            $referral->load($this->getRequest()->getParam('id'));
        }

        Mage::register('current_referral', $referral);

        return $referral;
    }

    /************************/
}
