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



class Mirasvit_Rewards_Block_Adminhtml_Spending_Rule_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
        $this->_objectId = 'spending_rule_id';
        $this->_controller = 'adminhtml_spending_rule';
        $this->_blockGroup = 'rewards';

        $this->_updateButton('save', 'label', Mage::helper('rewards')->__('Save'));
        $this->_updateButton('delete', 'label', Mage::helper('rewards')->__('Delete'));

        $this->_addButton('saveandcontinue', array(
            'label' => Mage::helper('rewards')->__('Save And Continue Edit'),
            'onclick' => 'saveAndContinueEdit()',
            'class' => 'save',
        ), -100);

        $this->_formScripts[] = "
            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action + 'back/edit/');
            }
        ";
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        }
    }

    public function getSpendingRule()
    {
        if (Mage::registry('current_spending_rule') && Mage::registry('current_spending_rule')->getId()) {
            return Mage::registry('current_spending_rule');
        }
    }

    public function getHeaderText()
    {
        if ($spendingRule = $this->getSpendingRule()) {
            return Mage::helper('rewards')->__("Edit Spending Rule '%s'", $this->htmlEscape($spendingRule->getName()));
        } else {
            return Mage::helper('rewards')->__('Create New Spending Rule');
        }
    }

    public function _toHtml()
    {
        $html = parent::_toHtml();
        $switcher = $this->getLayout()->createBlock('adminhtml/store_switcher');
        $switcher->setUseConfirm(false)->setSwitchUrl(
            $this->getUrl('*/*/*/', array('store' => null, '_current' => true))
        );
        $html = $switcher->toHtml().$html;

        return $html;
    }

    /************************/
}
