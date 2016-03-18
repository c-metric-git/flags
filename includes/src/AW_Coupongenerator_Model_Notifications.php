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

class AW_Coupongenerator_Model_Notifications extends Mage_Core_Model_Abstract
{
    protected $_emailSubject = 'New coupon notification';

    /**
     * @param $requestData array
     *
     * @return bool
     */
    public function send($requestData)
    {
        $recipient = array(
            'name' => $requestData['customer']['name'],
            'mail' => $requestData['customer']['email']
        );

        // Start Store & Locale Emulation
        $versionAdapter = Mage::getSingleton('coupongenerator/version_adapter');
        $initialEnvironmentData = $versionAdapter->call(
            'startEnvironmentEmulation',
            array($requestData['store_id'])
        );
        $variables = $this->_prepareVariables($requestData);
        $sender = Mage::helper('coupongenerator/config')->getSender();

        $awRule = Mage::getModel('coupongenerator/salesrule')->loadByRuleId($requestData['coupon']->getRuleId());
        if ($awRule->getEmailTemplateConfig()) {
            $templateId = Mage::helper('coupongenerator/config')->getEmailTemplate();
        } elseif ($awRule->getEmailTemplate() == 0) {
            $templateId = Mage::getSingleton('coupongenerator/source_emailTemplate')->getNodeName();
        } else {
            $templateId = $awRule->getEmailTemplate();
        }

        $emailTemplate = $this->_getTemplate($templateId);
        $emailTemplate->getProcessedTemplate($variables);

        $emailTemplate
            ->setSenderName($sender['name'])
            ->setSenderEmail($sender['mail'])
            ->setDesignConfig(array('area' => 'frontend', 'store' => $requestData['store_id']))
            ->setTemplateFilter(Mage::getSingleton('coupongenerator/filter'))
        ;
        if ( ! $emailTemplate->getTemplateSubject()) {
            $emailTemplate->setTemplateSubject($this->_getDefaultEmailSubject());
        }

        // Stop Store & Locale Emulation
        $versionAdapter->call('stopEnvironmentEmulation', array($initialEnvironmentData));

        return $emailTemplate->send($recipient['mail'], $recipient['name'], $variables);
    }

    protected function _getTemplate($templateId)
    {
        is_numeric($templateId) ? $method = 'load' : $method = 'loadDefault';
        return call_user_func(array(Mage::getModel('core/email_template'), $method), $templateId);
    }

    protected function _getDefaultEmailSubject()
    {
        return Mage::helper('coupongenerator')->__($this->_emailSubject);
    }

    protected function _prepareVariables($requestData)
    {
        if ($expirationDate = $requestData['coupon']->getExpirationDate()) {
            $formatDate = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_LONG);
            $expirationDate = Mage::app()->getLocale()->date(strtotime($expirationDate))->toString($formatDate);
        } else {
            $expirationDate = false;
        }

        $salesRule = Mage::getModel('salesrule/rule')->load($requestData['coupon']->getRuleId());

        $variables = array(
            'name'                      => $requestData['customer']['name'],
            'quickCoupon'               => $requestData['coupon']->getCode(),
            'quickCouponExpirationDate' => $expirationDate,
            'quickCouponDiscount'       => $salesRule->getDiscountAmount(),
            'discount_type'             => $salesRule->getSimpleAction(),
        );
        return $variables;
    }
}
