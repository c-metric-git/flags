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



class Mirasvit_Rewards_Adminhtml_Earning_RuleController extends Mage_Adminhtml_Controller_Action
{
    protected function _initAction()
    {
        $this->loadLayout()->_setActiveMenu('rewards');

        return $this;
    }

    public function indexAction()
    {
        $this->_title($this->__('Earning Rules'));
        $this->_initAction();
        $this->_addContent($this->getLayout()
            ->createBlock('rewards/adminhtml_earning_rule'));
        $this->renderLayout();
    }

    public function addAction()
    {
        $this->_title($this->__('New Earning Rule'));

        $this->_initEarningRule();

        $this->_initAction();
        $this->_addBreadcrumb(Mage::helper('adminhtml')->__('EarningRule  Manager'),
                Mage::helper('adminhtml')->__('EarningRule Manager'), $this->getUrl('*/*/'));
        $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Add Earning Rule '), Mage::helper('adminhtml')->__('Add Earning Rule'));

        $this->getLayout()
            ->getBlock('head')
            ->setCanLoadExtJs(true);
        $this->_addContent($this->getLayout()->createBlock('rewards/adminhtml_earning_rule_edit'))
                ->_addLeft($this->getLayout()->createBlock('rewards/adminhtml_earning_rule_edit_tabs'));
        $this->getLayout()->getBlock('head')->setCanLoadRulesJs(true);
        $this->renderLayout();
    }

    public function editAction()
    {
        $earningRule = $this->_initEarningRule();

        if ($earningRule->getId()) {
            $this->_title($this->__("Edit Earning Rule '%s'", $earningRule->getName()));
            $this->_initAction();
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Earning Rules'),
                    Mage::helper('adminhtml')->__('Earning Rules'), $this->getUrl('*/*/'));
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Edit Earning Rule '),
                    Mage::helper('adminhtml')->__('Edit Earning Rule '));

            $this->getLayout()
                ->getBlock('head')
                ->setCanLoadExtJs(true);
            $this->_addContent($this->getLayout()->createBlock('rewards/adminhtml_earning_rule_edit'))
                    ->_addLeft($this->getLayout()->createBlock('rewards/adminhtml_earning_rule_edit_tabs'));
            $this->getLayout()->getBlock('head')->setCanLoadRulesJs(true);

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('The Earning Rule does not exist.'));
            $this->_redirect('*/*/');
        }
    }

    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost()) {
            $earningRule = $this->_initEarningRule();
            $earningRule->addData($data);
            if (isset($data['rule'])) {
                $earningRule->loadPost($data['rule']);
            }
            //format date to standart
            $format = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
            Mage::helper('mstcore/date')->formatDateForSave($earningRule, 'active_from', $format);
            Mage::helper('mstcore/date')->formatDateForSave($earningRule, 'active_to', $format);

            try {
                $earningRule->save();

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Earning Rule was successfully saved'));
                Mage::getSingleton('adminhtml/session')->setFormData(false);

                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $earningRule->getId(), 'store' => $earningRule->getStoreId()));

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
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Unable to find Earning Rule to save'));
        $this->_redirect('*/*/');
    }

    public function deleteAction()
    {
        if ($this->getRequest()->getParam('id') > 0) {
            try {
                $earningRule = Mage::getModel('rewards/earning_rule');

                $earningRule->setId($this->getRequest()
                    ->getParam('id'))
                    ->delete();

                Mage::getSingleton('adminhtml/session')->addSuccess(
                        Mage::helper('adminhtml')->__('Earning Rule was successfully deleted'));
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
        $ids = $this->getRequest()->getParam('earning_rule_id');
        if (!is_array($ids)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select Earning Rule(s)'));
        } else {
            try {
                $isActive = $this->getRequest()->getParam('is_active');
                foreach ($ids as $id) {
                    /** @var Mirasvit_Rewards_Model_Earning_Rule $earning_rule */
                    $earning_rule = Mage::getModel('rewards/earning_rule')->load($id);
                    $earning_rule->setIsActive($isActive);
                    $earning_rule->save();
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
        $ids = $this->getRequest()->getParam('earning_rule_id');
        if (!is_array($ids)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select Earning Rule(s)'));
        } else {
            try {
                foreach ($ids as $id) {
                    /** @var Mirasvit_Rewards_Model_Earning_Rule $earningRule */
                    $earningRule = Mage::getModel('rewards/earning_rule')
                        ->setIsMassDelete(true)
                        ->load($id);
                    $earningRule->delete();
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

    public function _initEarningRule()
    {
        $earningRule = Mage::getModel('rewards/earning_rule');
        if ($this->getRequest()->getParam('id')) {
            $earningRule->load($this->getRequest()->getParam('id'));
            if ($storeId = (int) $this->getRequest()->getParam('store')) {
                $earningRule->setStoreId($storeId);
            }
        }

        Mage::register('current_earning_rule', $earningRule);

        return $earningRule;
    }
    public function newConditionHtmlAction()
    {
        $id = $this->getRequest()->getParam('id');
        $typeArr = explode('|', str_replace('-', '/', $this->getRequest()->getParam('type')));
        $type = $typeArr[0];

        $model = Mage::getModel($type)
            ->setId($id)
            ->setType($type)
            ->setRule(Mage::getModel('rewards/earning_rule'))
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

    public function applyRulesAction()
    {
        try {
            Mage::getModel('rewards/earning_rule')->applyAll();
            Mage::app()->removeCache('catalog_rules_dirty');
            Mage::getSingleton('adminhtml/session')->addSuccess(
                Mage::helper('catalogrule')->__('The rules have been applied.')
            );
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('catalogrule')->__('Unable to apply rules.')
            );
            throw $e;
        }
        $this->_redirect('*/*');
    }
}
