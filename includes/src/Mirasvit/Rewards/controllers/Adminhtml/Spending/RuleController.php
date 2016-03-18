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



class Mirasvit_Rewards_Adminhtml_Spending_RuleController extends Mage_Adminhtml_Controller_Action
{
    protected function _initAction()
    {
        $this->loadLayout()->_setActiveMenu('rewards');

        return $this;
    }

    public function indexAction()
    {
        $this->_title($this->__('Spending Rules'));
        $this->_initAction();
        $this->_addContent($this->getLayout()
            ->createBlock('rewards/adminhtml_spending_rule'));
        $this->renderLayout();
    }

    public function addAction()
    {
        $this->_title($this->__('New Spending Rule'));

        $this->_initSpendingRule();

        $this->_initAction();
        $this->_addBreadcrumb(Mage::helper('adminhtml')->__('SpendingRule  Manager'),
                Mage::helper('adminhtml')->__('SpendingRule Manager'), $this->getUrl('*/*/'));
        $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Add Spending Rule '), Mage::helper('adminhtml')->__('Add Spending Rule'));

        $this->getLayout()
            ->getBlock('head')
            ->setCanLoadExtJs(true);
        $this->_addContent($this->getLayout()->createBlock('rewards/adminhtml_spending_rule_edit'))
                ->_addLeft($this->getLayout()->createBlock('rewards/adminhtml_spending_rule_edit_tabs'));
        $this->getLayout()->getBlock('head')->setCanLoadRulesJs(true);
        $this->renderLayout();
    }

    public function editAction()
    {
        $spendingRule = $this->_initSpendingRule();

        if ($spendingRule->getId()) {
            $this->_title($this->__("Edit Spending Rule '%s'", $spendingRule->getName()));
            $this->_initAction();
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Spending Rules'),
                    Mage::helper('adminhtml')->__('Spending Rules'), $this->getUrl('*/*/'));
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Edit Spending Rule '),
                    Mage::helper('adminhtml')->__('Edit Spending Rule '));

            $this->getLayout()
                ->getBlock('head')
                ->setCanLoadExtJs(true);
            $this->_addContent($this->getLayout()->createBlock('rewards/adminhtml_spending_rule_edit'))
                    ->_addLeft($this->getLayout()->createBlock('rewards/adminhtml_spending_rule_edit_tabs'));
            $this->getLayout()->getBlock('head')->setCanLoadRulesJs(true);

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('The Spending Rule does not exist.'));
            $this->_redirect('*/*/');
        }
    }

    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost()) {
            $spendingRule = $this->_initSpendingRule();
            $spendingRule->addData($data);
            if (isset($data['rule'])) {
                $spendingRule->loadPost($data['rule']);
            }
            //format date to standart
            // $format = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
            // Mage::helper('mstcore/date')->formatDateForSave($spendingRule, 'active_from', $format);
            // Mage::helper('mstcore/date')->formatDateForSave($spendingRule, 'active_to', $format);

            try {
                $spendingRule->save();

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Spending Rule was successfully saved'));
                Mage::getSingleton('adminhtml/session')->setFormData(false);

                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $spendingRule->getId(), 'store' => $spendingRule->getStoreId()));

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
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Unable to find Spending Rule to save'));
        $this->_redirect('*/*/');
    }

    public function deleteAction()
    {
        if ($this->getRequest()->getParam('id') > 0) {
            try {
                $spendingRule = Mage::getModel('rewards/spending_rule');

                $spendingRule->setId($this->getRequest()
                    ->getParam('id'))
                    ->delete();

                Mage::getSingleton('adminhtml/session')->addSuccess(
                        Mage::helper('adminhtml')->__('Spending Rule was successfully deleted'));
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
        $ids = $this->getRequest()->getParam('spending_rule_id');
        if (!is_array($ids)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select Spending Rule(s)'));
        } else {
            try {
                $isActive = $this->getRequest()->getParam('is_active');
                foreach ($ids as $id) {
                    /** @var Mirasvit_Rewards_Model_Spending_Rule $spending_rule */
                    $spending_rule = Mage::getModel('rewards/spending_rule')->load($id);
                    $spending_rule->setIsActive($isActive);
                    $spending_rule->save();
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
        $ids = $this->getRequest()->getParam('spending_rule_id');
        if (!is_array($ids)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select Spending Rule(s)'));
        } else {
            try {
                foreach ($ids as $id) {
                    /** @var Mirasvit_Rewards_Model_Spending_Rule $spendingRule */
                    $spendingRule = Mage::getModel('rewards/spending_rule')
                        ->setIsMassDelete(true)
                        ->load($id);
                    $spendingRule->delete();
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

    public function _initSpendingRule()
    {
        $spendingRule = Mage::getModel('rewards/spending_rule');
        if ($this->getRequest()->getParam('id')) {
            $spendingRule->load($this->getRequest()->getParam('id'));
            if ($storeId = (int) $this->getRequest()->getParam('store')) {
                $spendingRule->setStoreId($storeId);
            }
        }

        Mage::register('current_spending_rule', $spendingRule);

        return $spendingRule;
    }
    public function newConditionHtmlAction()
    {
        $id = $this->getRequest()->getParam('id');
        $typeArr = explode('|', str_replace('-', '/', $this->getRequest()->getParam('type')));
        $type = $typeArr[0];

        $model = Mage::getModel($type)
            ->setId($id)
            ->setType($type)
            ->setRule(Mage::getModel('rewards/spending_rule'))
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
