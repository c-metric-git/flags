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


class AW_Coupongenerator_Block_Adminhtml_Rule_Edit_Tab_Main_Renderer extends Mage_Adminhtml_Block_Abstract
{
    /**
     * @param Varien_Data_Form $form
     * @param AW_Coupongenerator_Model_Salesrule $awRule
     *
     * @return Varien_Data_Form
     */
    public function addAdditionalFields(Varien_Data_Form $form, AW_Coupongenerator_Model_Salesrule $awRule)
    {
        $expirationDays = $awRule->getExpirationDays();
        $couponTypeField = $form->getElement('coupon_type');
        if (
            in_array('adminhtml_awqcg_rules_edit', $this->getLayout()->getUpdate()->getHandles())
            &&  ! $couponTypeField->getValue()
        ) {
            $couponTypeField->setValue($awRule->getCouponType());
        }
        $fieldset = $form->getElement('base_fieldset');
        $couponQCGHintField = $fieldset->addField('aw_coupon_qcg_hint', 'note', array(
            'name' => 'aw_coupon_qcg_hint',
            'note' => $this->__(
                'For usage with Coupon Code Generator<br /> (Promotions -> Coupon Code Generator -> Rules)'
            ),
        ), 'coupon_type');
        $emailTemplateSource = Mage::getSingleton('coupongenerator/source_emailTemplate');
        $emailTemplateFieldOptions = $emailTemplateSource->toOptionHash();
        if (is_null($awRule->getEmailTemplate()) || $awRule->getEmailTemplate() == 0) {
            $emailTemplateFieldValue = $emailTemplateSource->getNodeName();
        } else {
            $emailTemplateFieldValue = $awRule->getEmailTemplate();
        }
        $emailTemplateFieldIsReadOnly = $awRule->getEmailTemplateConfig() || ! $awRule->getId();
        $useConfigSettingsChecked = $emailTemplateFieldIsReadOnly ? 'checked="checked"' : '';
        $emailTemplateFieldNote = "<input type='checkbox' id='aw_email_template_config'"
            . "name='aw_email_template_config' value='1' {$useConfigSettingsChecked}"
            . "onclick='toggleValueElements(this, this.parentNode.parentNode.parentNode);' class='checkbox' />"
            . "<label class='normal'>" . $this->__('Use Config Settings') . "</label>"
            // cheap and dirty workaround to make 'toggleValueElements' compatible with dependencies;
            // see FormElementDependenceController.prototype.trackChange();
            . "<input type='checkbox' id='rule_aw_email_template_inherit' style='display:none;' checked='checked' />"
        ;
        $emailTemplateField = $fieldset->addField('aw_email_template', 'select', array(
            'name'     => 'aw_email_template',
            'label'    => $this->__('Email template'),
            'title'    => $this->__('Email template'),
            'options'  => $emailTemplateFieldOptions,
            'value'    => $emailTemplateFieldValue,
            'note'     => $emailTemplateFieldNote,
            'disabled' => $emailTemplateFieldIsReadOnly
        ), 'aw_coupon_qcg_hint');
        $couponPrefixField = $fieldset->addField('aw_coupon_prefix', 'text', array(
            'name' => 'aw_coupon_prefix',
            'label' => $this->__('Coupon prefix'),
            'required' => false,
            'maxlength' => 16,
            'class' => 'validate-alphanum',
            'value' => $awRule->getCouponPrefix()
        ), 'aw_email_template');
        $couponExpirationFiled = $fieldset->addField('aw_coupon_expiration', 'text', array(
            'name' => 'aw_coupon_expiration',
            'label' => $this->__('Coupon expiration'),
            'required' => false,
            'note' => $this->__('Days: 0 - without limitation'),
            'value' => $expirationDays ? $expirationDays : 0,
        ), 'aw_coupon_prefix');
        $formAfterBlock = $this->getLayout()
            ->getBlock('promo_quote_edit_tab_main')
            ->getChild('form_after')
        ;
        $formAfterBlock
            ->addFieldMap($couponExpirationFiled->getHtmlId(), $couponExpirationFiled->getName())
            ->addFieldDependence(
                $couponExpirationFiled->getName(),
                'coupon_type',
                $awRule->getCouponType())
            ->addFieldMap($couponPrefixField->getHtmlId(), $couponPrefixField->getName())
            ->addFieldDependence(
                $couponPrefixField->getName(),
                'coupon_type',
                $awRule->getCouponType())
            ->addFieldMap($couponQCGHintField->getHtmlId(), $couponQCGHintField->getName())
            ->addFieldDependence(
                $couponQCGHintField->getName(),
                'coupon_type',
                $awRule->getCouponType())
        ;
        $couponNativeHintField = $fieldset->addField('aw_coupon_native_hint', 'note', array(
            'name' => 'aw_coupon_native_hint',
            'note' => $this->__('Native Magento Option'),
        ), 'coupon_type');
        Mage::getSingleton('coupongenerator/version_adapter')->call(
            'updateCouponTypeFormDependencies',
            array($awRule, $couponNativeHintField, $emailTemplateField, $emailTemplateField)
        );
        return $form;
    }
}
