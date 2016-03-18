<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_GoogleTrustedStore
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Form for order cancellation reason
 *
 */
class Mage_GoogleTrustedStore_Block_Adminhtml_OrderCancellationConfirmation extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Create form with one field for cancellation reason
     * and sets it to widget
     *
     * @see Mage_Adminhtml_Block_Widget_Form::_prepareForm()
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('order_cancellation_');
        $fieldset = $form->addFieldset('base', array());
        $source = Mage::getSingleton('googletrustedstore/source_orderCancellationReason');
        $fieldset->addField('reason', 'select', array(
            'name'   => 'cancellation_reason',
            'label'  => Mage::helper('googletrustedstore')->__('Cancellation Reason'),
            'values' => $source->toOptionArray(),
            'value'  => $source->getDefaultCode(),
        ));
        $this->setForm($form);
    }
}