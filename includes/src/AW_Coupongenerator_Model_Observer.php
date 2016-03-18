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

class AW_Coupongenerator_Model_Observer
{
    public function setBlockToTicketPage($event)
    {
        if ('aw_hdu3/adminhtml_ticket_edit_form' == $event->getBlock()->getType()) {
            $event->getBlock()->setChild('aw_coupon',
                $event->getBlock()->getLayout()->createBlock('coupongenerator/adminhtml_hdu3_ticket_edit_form_coupon')
            );
        }
    }

    public function addQuickGeneratedCouponType($event)
    {
        $couponTypes = $event->getTransport()->getCouponTypes();
        $couponTypes[Mage::getSingleton('coupongenerator/salesrule')->getCouponType()] = Mage::helper('coupongenerator')->__(
            'Generated Coupon Code'
        );
        $event->getTransport()->setCouponTypes($couponTypes);
    }

    public function setUsesPerCouponToRule($observer)
    {
        $rule = $observer->getEvent()->getRule();
        $awRule = Mage::getModel('coupongenerator/salesrule')->loadByRuleId($rule->getId());
        if ($rule->getCouponType() == $awRule->getCouponType() &&  ! $rule->getUsesPerCoupon()) {
            $rule->setUsesPerCoupon($awRule->getExtraUsesPerCoupon());
        }
    }

    public function beforeSaveSalesRule($observer)
    {
        $rule = $observer->getEvent()->getRule();
        if ($rule->getCouponType() == Mage::getSingleton('coupongenerator/salesrule')->getCouponType()) {
            $rule->setUseAutoGeneration(true);
        }
        if ('awqcg_rules' == Mage::app()->getRequest()->getControllerName()) {
            $versionAdapter = Mage::getSingleton('coupongenerator/version_adapter');
            if ($rule->getSimpleAction() != $versionAdapter->call('getXYSalesRuleAction')) {
                $rule->setDiscountStep(0);
            }
            if ( ! in_array($rule->getSimpleAction(), array(
                $versionAdapter->call('getByPercentSalesRuleAction'),
                $versionAdapter->call('getByFixedSalesRuleAction')))
            ) {
                $rule->setDiscountQty(0);
            }
        }
    }

    public function saveSalesRule($observer)
    {
        $request = Mage::app()->getRequest();
        $couponPrefix = $request->getPost('aw_coupon_prefix');
        $expirationDays = $request->getPost('aw_coupon_expiration');
        $usesPerCoupon = $request->getPost('uses_per_coupon');
        if (is_null($couponPrefix) || is_null($expirationDays) || is_null($usesPerCoupon)) {
            return;
        }
        $rule = $observer->getEvent()->getRule();
        $awRule = Mage::getModel('coupongenerator/salesrule')->loadByRuleId($rule->getRuleId());
        if ($awRule->getCouponType() == $rule->getCouponType()) {
            $emailTemplate = $request->getPost('aw_email_template');
            $emailTemplateConfig = (int) $request->getPost('aw_email_template_config');
            $defaultLocaleEmailTemplate = Mage::getSingleton('coupongenerator/source_emailTemplate')->getNodeName();
            if ($emailTemplate == $defaultLocaleEmailTemplate) {
                $emailTemplate = 0;
            }
            $ruleData = array(
                'rule_id' => $rule->getRuleId(),
                'expiration_days' => (int) $expirationDays,
                'extra_uses_per_coupon' => (int) $usesPerCoupon,
                'coupon_prefix' => $couponPrefix,
                'email_template' => $emailTemplate,
                'email_template_config' => $emailTemplateConfig
            );
            $awRule->addData($ruleData)->save();
            $versionAdapter = Mage::getSingleton('coupongenerator/version_adapter');
            $versionAdapter->call('updateCouponUsageLimit', array($rule, (int) $usesPerCoupon));
        } else {
            $awRule->delete();
        }
    }

    public function saveSalesRuleCoupon($observer)
    {
        $admin = Mage::getSingleton('admin/session')->getUser();
        $coupon = $observer->getEvent()->getCoupon();
        if ( ! $admin ||  ! $coupon->getCouponId()) {
            return;
        }
        $awCoupon = Mage::getModel('coupongenerator/coupon')->loadByCouponId($coupon->getCouponId());

        $couponData = array(
            'coupon_id' => $coupon->getCouponId(),
            'admin_user_id' => $admin->getUserId(),
        );

        $recipientEmail = Mage::app()->getRequest()->getParam('coupon_generation_email', false);
        $customerId = Mage::app()->getRequest()->getParam('customer_id', false);

        if ($recipientEmail) {
            $couponData['recipient_email'] = trim($recipientEmail);
        }
        if ($customer = Mage::helper('coupongenerator')->getCustomer($customerId, $recipientEmail)) {
            $couponData['customer_id'] = $customer->getId();
        }

        $awCoupon->addData($couponData)->save();
    }

    /**
     * @param Varien_Event_Observer $observer
     */
    public function addExtraFields($observer)
    {
        $form = $observer->getEvent()->getForm();
        $ruleId = Mage::registry('current_promo_quote_rule')->getId();
        $awRule = Mage::getModel('coupongenerator/salesrule')->loadByRuleId($ruleId);
        $mainFormRenderer = Mage::getBlockSingleton('coupongenerator/adminhtml_rule_edit_tab_main_renderer');
        $mainFormRenderer->addAdditionalFields($form, $awRule);
    }

    /**
     * @param Varien_Event_Observer $observer
     */
    public function addBannersBlock($observer)
    {
        if ('adminhtml_awqcg_rules_edit' != $observer->getAction()->getFullActionName()) {
            return;
        }
        $versionAdapter = Mage::getSingleton('coupongenerator/version_adapter');
        $versionAdapter->call('addBannersBlock', array($observer->getLayout()));
    }
}
