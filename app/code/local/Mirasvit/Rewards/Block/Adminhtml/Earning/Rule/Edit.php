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



class Mirasvit_Rewards_Block_Adminhtml_Earning_Rule_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
        $this->_objectId = 'earning_rule_id';
        $this->_controller = 'adminhtml_earning_rule';
        $this->_blockGroup = 'rewards';

        $earningRule = Mage::registry('current_earning_rule');
        if ($earningRule->getType()) {
            $this->_updateButton('save', 'label', Mage::helper('rewards')->__('Save'));
        } else {
            $this->_removeButton('save');
        }
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

    public function getEarningRule()
    {
        if (Mage::registry('current_earning_rule') && Mage::registry('current_earning_rule')->getId()) {
            return Mage::registry('current_earning_rule');
        }
    }

    public function getHeaderText()
    {
        if ($earningRule = $this->getEarningRule()) {
            return Mage::helper('rewards')->__("Edit Earning Rule '%s'", $this->htmlEscape($earningRule->getName()));
        } else {
            return Mage::helper('rewards')->__('Create New Earning Rule');
        }
    }

    public function _toHtml()
    {
        $html = parent::_toHtml();
        if ($this->getEarningRule() && $this->getEarningRule()->getType() == Mirasvit_Rewards_Model_Earning_Rule::TYPE_BEHAVIOR) {
            $switcher = $this->getLayout()->createBlock('adminhtml/store_switcher');
            $switcher->setUseConfirm(false)->setSwitchUrl(
                $this->getUrl('*/*/*/', array('store' => null, '_current' => true))
            );
            $html = $switcher->toHtml().$html;
        }

        return $html;
    }

    /************************/
}
