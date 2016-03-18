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


class AW_Coupongenerator_Block_Adminhtml_Rule_Edit_Tab_Actions
    extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * Prepare content for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return $this->__('Actions');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->__('Actions');
    }

    /**
     * Returns status flag about this tab can be showen or not
     *
     * @return true
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Returns status flag about this tab hidden or not
     *
     * @return true
     */
    public function isHidden()
    {
        return false;
    }

    protected function _prepareForm()
    {
        $versionAdapter = Mage::getSingleton('coupongenerator/version_adapter');
        $model = Mage::registry('current_promo_quote_rule');
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('rule_');
        $fieldset = $form->addFieldset('action_fieldset', array(
            'legend' => $this->__('Update prices using the following information'))
        );

        $simpleActionField = $fieldset->addField('simple_action', 'select', array(
            'label'     => $this->__('Apply'),
            'name'      => 'simple_action',
            'options'    => array(
                $versionAdapter->call('getByPercentSalesRuleAction')
                    => $this->__('Percent of product price discount'),
                $versionAdapter->call('getByFixedSalesRuleAction')
                    => $this->__('Fixed amount discount'),
                $versionAdapter->call('getCartFixedSalesRuleAction')
                    => $this->__('Fixed amount discount for whole cart'),
                $versionAdapter->call('getXYSalesRuleAction')
                    => $this->__('Buy X get Y free (discount amount is Y)'),
            ),
        ));
        $fieldset->addField('discount_amount', 'text', array(
            'name' => 'discount_amount',
            'required' => true,
            'class' => 'validate-not-negative-number',
            'label' => $this->__('Discount Amount'),
        ));
        $model->setDiscountAmount($model->getDiscountAmount()*1);

        $discountQtyField = $fieldset->addField('discount_qty', 'text', array(
            'name' => 'discount_qty',
            'label' => $this->__('Maximum Qty Discount is Applied To'),
        ));
        $model->setDiscountQty($model->getDiscountQty()*1);

        $discountStepField = $fieldset->addField('discount_step', 'text', array(
            'name' => 'discount_step',
            'label' => $this->__('Discount Qty Step (Buy X)'),
        ));

        $fieldset->addField('apply_to_shipping', 'select', array(
            'label'     => $this->__('Apply to Shipping Amount'),
            'title'     => $this->__('Apply to Shipping Amount'),
            'name'      => 'apply_to_shipping',
            'values'    => Mage::getSingleton('adminhtml/system_config_source_yesno')->toOptionArray(),
        ));

        $fieldset->addField('simple_free_shipping', 'select', array(
            'label'     => $this->__('Free Shipping'),
            'title'     => $this->__('Free Shipping'),
            'name'      => 'simple_free_shipping',
            'options'    => array(
                0 => Mage::helper('salesrule')->__('No'),
                Mage_SalesRule_Model_Rule::FREE_SHIPPING_ITEM => $this->__('For matching items only'),
                Mage_SalesRule_Model_Rule::FREE_SHIPPING_ADDRESS => $this->__('For shipment with matching items'),
            ),
        ));

        $stopRulesField = $fieldset->addField('stop_rules_processing', 'select', array(
            'label'     => $this->__('Stop Further Rules Processing'),
            'title'     => $this->__('Stop Further Rules Processing'),
            'name'      => 'stop_rules_processing',
            'options'    => array(
                '1' => $this->__('Yes'),
                '0' => $this->__('No'),
            ),
        ));

        $renderer = Mage::getBlockSingleton('adminhtml/widget_form_renderer_fieldset')
            ->setTemplate('promo/fieldset.phtml')
            ->setNewChildUrl($this->getUrl('*/*/newActionHtml/form/rule_actions_fieldset'));

        $fieldset = $form->addFieldset('actions_fieldset', array(
            'legend' => $this->__(
                'Apply the rule only to cart items matching the following conditions (leave blank for all items)'
            )
        ))->setRenderer($renderer);

        $fieldset->addField('actions', 'text', array(
            'name' => 'actions',
            'label' => $this->__('Apply To'),
            'title' => $this->__('Apply To'),
            'required' => true,
        ))->setRule($model)->setRenderer(Mage::getBlockSingleton('rule/actions'));

        Mage::dispatchEvent('adminhtml_block_salesrule_actions_prepareform', array('form' => $form));

        $form->setValues($model->getData());

        if ($model->isReadonly()) {
            foreach ($fieldset->getElements() as $element) {
                $element->setReadonly(true, true);
            }
        }
        //$form->setUseContainer(true);

        $this->setForm($form);

        // field dependencies
        $mainTabForm = $this->getLayout()->getBlock('promo_quote_edit_tab_main')->getForm();
        $couponTypesField = $mainTabForm->getElement('coupon_type');
        $couponTypes = Mage::getModel('coupongenerator/salesrule')->getOtherCouponTypes();
        $dependenceBlock = $versionAdapter->call('getFormDependenciesBlock')
            ->addFieldMap($couponTypesField->getHtmlId(), $couponTypesField->getName())
            ->addFieldMap($discountStepField->getHtmlId(), $discountStepField->getName())
            ->addFieldMap($simpleActionField->getHtmlId(), $simpleActionField->getName())
            ->addFieldMap($discountQtyField->getHtmlId(), $discountQtyField->getName())
            ->addFieldMap($stopRulesField->getHtmlId(), $stopRulesField->getName())
            ->addFieldDependence(
                $stopRulesField->getName(),
                $couponTypesField->getName(),
                array_map('strval', $couponTypes))
            ->addFieldDependence(
                $discountStepField->getName(),
                $simpleActionField->getName(),
                $versionAdapter->call('getXYSalesRuleAction'))
            ->addFieldDependence(
                $discountQtyField->getName(),
                $simpleActionField->getName(),
                array(
                    $versionAdapter->call('getByPercentSalesRuleAction'),
                    $versionAdapter->call('getByFixedSalesRuleAction')
                )
            )
        ;
        $this->setChild('form_after', $dependenceBlock);

        return parent::_prepareForm();
    }

}
