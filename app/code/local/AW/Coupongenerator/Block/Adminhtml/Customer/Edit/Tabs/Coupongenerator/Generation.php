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

class AW_Coupongenerator_Block_Adminhtml_Customer_Edit_Tabs_Coupongenerator_Generation extends Mage_Adminhtml_Block_Widget_Form
{
    public function initForm()
    {
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('_aw_coupongenerator_');

        $fieldset = $form->addFieldset(
            'notification_fieldset', array('legend' => Mage::helper('coupongenerator')->__('Coupon Code Generation'))
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
                 'label'   => Mage::helper('coupongenerator')->__('Send notification to the customer'),
                 'name'    => 'coupon_generation_notification',
                 'id'      => 'generation_notification',
                 'value'   => 0,
                 'checked' => 0,
            )
        );

        $fieldset->addField(
            'coupon_generation_execute', 'label',
            array(
                'after_element_html' => $this->_getGenerationButton()->toHtml()
            )
        );

        $this->setForm($form);
        return $this;
    }

    /**
     * @return Mage_Adminhtml_Block_Widget_Button
     */
    protected function _getGenerationButton()
    {
        $urlToCouponGeneration = $this->getUrl('adminhtml/awqcg_coupons/generateAjax');
        $button = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData(
                array(
                    'id' => 'coupon_generation',
                    'label' => $this->__('Generate'),
                    'type' => 'button',
                    'onclick' => "awQuickCouponGeneration.runFromCustomer('$urlToCouponGeneration')"
                )
            )
        ;
        return $button;
    }
}
