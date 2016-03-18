<?php
/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This software is designed to work with Magento community edition and
 * its use on an edition other than specified is prohibited. aheadWorks does not
 * provide extension support in case of incorrect edition use.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Coupongenerator
 * @version    1.0.2
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE.txt
 */

require_once Mage::getModuleDir('controllers', 'Mage_Adminhtml') . DS . 'Promo' . DS . 'QuoteController.php';

class AW_Coupongenerator_Adminhtml_Awqcg_RulesController extends Mage_Adminhtml_Promo_QuoteController
{
    protected function _initAction()
    {
        $this->_title($this->__('Promotions'))
            ->_title($this->__('Coupon Code Generator'))
            ->_title($this->__('Rules'))
        ;
        return $this->loadLayout()->_setActiveMenu('promo/items');
    }

    public function editAction()
    {
        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('salesrule/rule');
        if ($id) {
            $model->load($id);
            if (! $model->getRuleId()) {
                Mage::getSingleton('adminhtml/session')->addError(
                    $this->__('This rule no longer exists.'));
                $this->_redirect('*/*');
                return;
            }
        }
        // set entered data if was error when we do save
        $data = Mage::getSingleton('adminhtml/session')->getPageData(true);
        if (!empty($data)) {
            $model->addData($data);
        }
        $model->getConditions()->setJsFormObject('rule_conditions_fieldset');
        $model->getActions()->setJsFormObject('rule_actions_fieldset');
        Mage::register('current_promo_quote_rule', $model);
        $this->_initAction()->getLayout()->getBlock('promo_quote_edit')
            ->setData('action', $this->getUrl('*/*/save'));
        $this->_title($model->getRuleId() ? $model->getName() : $this->__('New Rule'));
        $this
            ->_addBreadcrumb(
                $id ? $this->__('Edit Rule')
                    : $this->__('New Rule'),
                $id ? $this->__('Edit Rule')
                    : $this->__('New Rule'))
            ->renderLayout()
        ;
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('promo/coupongenerator/coupongenerator_rules');
    }

    public function indexAction()
    {
        $this->_initAction()->renderLayout();
    }

}
