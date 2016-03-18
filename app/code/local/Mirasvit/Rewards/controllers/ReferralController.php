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


class Mirasvit_Rewards_ReferralController extends Mage_Core_Controller_Front_Action
{
    protected function _getSession()
    {
        return Mage::getSingleton('customer/session');
    }

    public function preDispatch()
    {
        parent::preDispatch();
        $action = $this->getRequest()->getActionName();
        if ($action != 'invite' && $action != 'referralVisit') {
            if (!Mage::getSingleton('customer/session')->authenticate($this)) {
                $this->setFlag('', 'no-dispatch', true);
            }
        }
    }

    public function indexAction()
    {
        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $this->renderLayout();
    }

    protected function _initReferral()
    {
        if ($id = $this->getRequest()->getParam('id')) {
            $referral = Mage::getModel('rewards/referral')->load($id);
            if ($referral->getId() > 0) {
                Mage::register('current_referral', $referral);
                return $referral;
            }
        }
    }

    /************************/

    public function postAction()
    {
        $customer = Mage::getSingleton('rewards/customer')->getCurrentCustomer();
        $names = $this->getRequest()->getParam('name');
        $emails = $this->getRequest()->getParam('email');
        $invitations = array();
        foreach ($emails as $key => $email) {
            if (empty($email) || empty($names[$key])) {
                continue;
            }
            $invitations[$email] = $names[$key];
        }
        $message = $this->getRequest()->getParam('message');
        $rejectedEmails = Mage::helper('rewards/referral')->frontendPost($customer, $invitations, $message);
        if (count($rejectedEmails)) {
            foreach ($rejectedEmails as $email) {
                $this->_getSession()->addNotice($this->__("Customer with email %s has been already invited to our store", $email));
            }
        }
        if (count($rejectedEmails) < count($invitations)) {
            $this->_getSession()->addSuccess($this->__('Your invitations were sent. Thanks!'));
        }
        $this->_redirect('*/*/');
    }

    public function inviteAction()
    {
        $id = (int)$this->getRequest()->getParam('id');
        $referral = Mage::getModel('rewards/referral')->load($id);
        if ($referral->getStatus() != Mirasvit_Rewards_Model_Config::REFERRAL_STATUS_SENT) {
            $this->_redirect('/');
            return;
        }
        $referral->setStatus(Mirasvit_Rewards_Model_Config::REFERRAL_STATUS_VISITED)
                 ->save();
        Mage::getSingleton('core/session')->setReferral($referral->getId());
        $this->_redirect('/');
    }

    public function referralVisitAction() {
        $customerId = (int)$this->getRequest()->getParam('customer_id');
        if (Mage::getSingleton('core/session')->getReferral()) {
            $this->_redirect('/');
            return;
        }
        $referral = Mage::getModel('rewards/referral')
            ->setCustomerId($customerId)
            ->setStatus(Mirasvit_Rewards_Model_Config::REFERRAL_STATUS_VISITED)
            ->setStoreId(Mage::app()->getStore()->getId())
            ->save();
        Mage::getSingleton('core/session')->setReferral($referral->getId());

        $this->_redirect('/');
    }
}