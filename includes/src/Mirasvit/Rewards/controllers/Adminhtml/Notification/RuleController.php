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



class Mirasvit_Rewards_Adminhtml_Notification_RuleController extends Mage_Adminhtml_Controller_Action
{
    protected function _initAction()
    {
        $this->loadLayout()->_setActiveMenu('rewards');

        return $this;
    }

    public function indexAction()
    {
        $this->_title($this->__('Notification Rules'));
        $this->_initAction();
        $this->_addContent($this->getLayout()
            ->createBlock('rewards/adminhtml_notification_rule'));
        $this->renderLayout();
    }

    public function addAction()
    {
        $this->_title($this->__('New Notification Rule'));

        $this->_initNotificationRule();

        $this->_initAction();
        $this->_addBreadcrumb(Mage::helper('adminhtml')->__('NotificationRule  Manager'),
                Mage::helper('adminhtml')->__('NotificationRule Manager'), $this->getUrl('*/*/'));
        $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Add Notification Rule '), Mage::helper('adminhtml')->__('Add Notification Rule'));

        $this->getLayout()
            ->getBlock('head')
            ->setCanLoadExtJs(true);
        $this->_addContent($this->getLayout()->createBlock('rewards/adminhtml_notification_rule_edit'))
                ->_addLeft($this->getLayout()->createBlock('rewards/adminhtml_notification_rule_edit_tabs'));
        $this->getLayout()->getBlock('head')->setCanLoadRulesJs(true);
        $this->renderLayout();
    }

    public function editAction()
    {
        $notificationRule = $this->_initNotificationRule();

        if ($notificationRule->getId()) {
            $this->_title($this->__("Edit Notification Rule '%s'", $notificationRule->getName()));
            $this->_initAction();
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Notification Rules'),
                    Mage::helper('adminhtml')->__('Notification Rules'), $this->getUrl('*/*/'));
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Edit Notification Rule '),
                    Mage::helper('adminhtml')->__('Edit Notification Rule '));

            $this->getLayout()
                ->getBlock('head')
                ->setCanLoadExtJs(true);
            $this->_addContent($this->getLayout()->createBlock('rewards/adminhtml_notification_rule_edit'))
                    ->_addLeft($this->getLayout()->createBlock('rewards/adminhtml_notification_rule_edit_tabs'));
            $this->getLayout()->getBlock('head')->setCanLoadRulesJs(true);

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('The Notification Rule does not exist.'));
            $this->_redirect('*/*/');
        }
    }

    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost()) {
            $notificationRule = $this->_initNotificationRule();
            $notificationRule->addData($data);
            if (isset($data['rule'])) {
                $notificationRule->loadPost($data['rule']);
            }
            //format date to standart
            // $format = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
            // Mage::helper('mstcore/date')->formatDateForSave($notificationRule, 'active_from', $format);
            // Mage::helper('mstcore/date')->formatDateForSave($notificationRule, 'active_to', $format);

            try {
                $notificationRule->save();

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Notification Rule was successfully saved'));
                Mage::getSingleton('adminhtml/session')->setFormData(false);

                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $notificationRule->getId(), 'store' => $notificationRule->getStoreId()));

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
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Unable to find Notification Rule to save'));
        $this->_redirect('*/*/');
    }

    public function deleteAction()
    {
        if ($this->getRequest()->getParam('id') > 0) {
            try {
                $notificationRule = Mage::getModel('rewards/notification_rule');

                $notificationRule->setId($this->getRequest()
                    ->getParam('id'))
                    ->delete();

                Mage::getSingleton('adminhtml/session')->addSuccess(
                        Mage::helper('adminhtml')->__('Notification Rule was successfully deleted'));
                $this->_redirect('*/*/');
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()
                    ->getParam('id'), ));
            }
        }
        $this->_redirect('*/*/');
    }

    public function massChangeAction()
    {
        $ids = $this->getRequest()->getParam('notification_rule_id');
        if (!is_array($ids)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select Notification Rule(s)'));
        } else {
            try {
                $isActive = $this->getRequest()->getParam('is_active');
                foreach ($ids as $id) {
                    /** @var Mirasvit_Rewards_Model_Notification_Rule $notification_rule */
                    $notification_rule = Mage::getModel('rewards/notification_rule')->load($id);
                    $notification_rule->setIsActive($isActive);
                    $notification_rule->save();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully updated', count($ids)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    public function massDeleteAction()
    {
        $ids = $this->getRequest()->getParam('notification_rule_id');
        if (!is_array($ids)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select Notification Rule(s)'));
        } else {
            try {
                foreach ($ids as $id) {
                    /** @var Mirasvit_Rewards_Model_Notification_Rule $notificationRule */
                    $notificationRule = Mage::getModel('rewards/notification_rule')
                        ->setIsMassDelete(true)
                        ->load($id);
                    $notificationRule->delete();
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

    public function _initNotificationRule()
    {
        $notificationRule = Mage::getModel('rewards/notification_rule');
        if ($this->getRequest()->getParam('id')) {
            $notificationRule->load($this->getRequest()->getParam('id'));
            if ($storeId = (int) $this->getRequest()->getParam('store')) {
                $notificationRule->setStoreId($storeId);
            }
        }

        Mage::register('current_notification_rule', $notificationRule);

        return $notificationRule;
    }
    public function newConditionHtmlAction()
    {
        $id = $this->getRequest()->getParam('id');
        $typeArr = explode('|', str_replace('-', '/', $this->getRequest()->getParam('type')));
        $type = $typeArr[0];

        $model = Mage::getModel($type)
            ->setId($id)
            ->setType($type)
            ->setRule(Mage::getModel('rewards/notification_rule'))
            ->setPrefix('conditions');

        if (!empty($typeArr[1])) {
            $model->setAttribute($typeArr[1]);
        }

        if ($model instanceof Mage_Rule_Model_Condition_Abstract) {
            $model->setJsFormObject($this->getRequest()->getParam('form'));
            $html = $model->asHtmlRecursive();
        } else {
            $html = '';
        }
        $this->getResponse()->setBody($html);
    }

    /************************/
}
