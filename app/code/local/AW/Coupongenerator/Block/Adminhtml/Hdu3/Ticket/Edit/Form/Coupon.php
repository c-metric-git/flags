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

class AW_Coupongenerator_Block_Adminhtml_Hdu3_Ticket_Edit_Form_Coupon extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(
            array('id' => 'awhdu3-ticket-edit-coupongenerator-form')
        );
        $form->setUseContainer(false);
        $this->setForm($form);

        $fieldset = $form->addFieldset(
            'awhdu3-ticket-edit-coupongenerator-fieldset', array('legend' => $this->__('Coupon Code Generation'))
        );

        $fieldset->addField(
            'coupon_generation_rule',
            'select',
            array(
                'label' => Mage::helper('coupongenerator')->__('Shopping Cart Price Rule'),
                'title' => Mage::helper('coupongenerator')->__('Shopping Cart Price Rule'),
                'name'  => 'coupon_generation_rule',
                'value' => '',
                'values'=> Mage::getSingleton('coupongenerator/salesrule')->toOptionArray()
            )
        );

        $fieldset->addField(
            'coupon_generation_notification',
            'checkbox',
            array(
                'label'   => Mage::helper('coupongenerator')->__('Send coupon in a separate email'),
                'name'    => 'coupon_generation_notification',
                'id'      => 'generation_notification',
                'value'   => 0,
                'checked' => 0,
            )
        );

        $fieldset->addField(
            'coupon_generation_email',
            'hidden',
            array(
                'name'  => 'coupon_generation_email',
                'value' => $this->getTicket()->getCustomerEmail(),
            )
        );

        $fieldset->addField(
            'coupon_generation_customer_id',
            'hidden',
            array(
                'name'  => 'coupon_generation_customer_id',
                'value' => $this->_getCustomerIdByEmail($this->getTicket()->getCustomerEmail()),
            )
        );

        $fieldset->addField(
            'coupon_generation_execute', 'label',
            array(
                'after_element_html' => $this->_getGenerationButton()->toHtml()
            )
        );

        return parent::_prepareForm();
    }

    /**
     * @return Mage_Adminhtml_Block_Widget_Button
     */
    protected function _getGenerationButton()
    {
        $urlToCouponGeneration = $this->getUrl('adminhtml/awqcg_coupons/generateAjax');
        $urlToAddNote = $this->getUrl('helpdesk_admin/adminhtml_ticket/ajaxAddNote', array('id' => $this->getTicket()->getId()));
        $button = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData(
                array(
                    'id' => 'coupon_generation',
                    'label' => $this->__('Generate'),
                    'type' => 'button',
                    'onclick' => "awQuickCouponGeneration.runFromTicket('$urlToCouponGeneration', '{$urlToAddNote}')"
                )
            )
        ;
        return $button;
    }

    /**
     * @return AW_Helpdesk3_Model_Ticket
     */
    public function getTicket()
    {
        return Mage::registry('current_ticket');
    }

    /**
     * @param string $email
     *
     * @return int|null
     */
    protected function _getCustomerIdByEmail($email)
    {
        $websiteId = Mage::app()->getStore($this->getTicket()->getStoreId())->getWebsiteId();
        $customer = Mage::getModel('customer/customer')->setWebsiteId($websiteId)->loadByEmail($email);
        return $customer->getId();
    }
}
