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



class Mirasvit_Rewards_Block_Adminhtml_Notification_Rule_Edit_Tab_Action extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        /** @var Mirasvit_Rewards_Model_Notification_Rule $notificationRule */
        $notificationRule = Mage::registry('current_notification_rule');

        $fieldset = $form->addFieldset('action_fieldset', array('legend' => Mage::helper('rewards')->__('Actions')));
        if ($notificationRule->getId()) {
            $fieldset->addField('notification_rule_id', 'hidden', array(
                'name' => 'notification_rule_id',
                'value' => $notificationRule->getId(),
            ));
        }
        $fieldset->addField('store_id', 'hidden', array(
            'name' => 'store_id',
            'value' => (int) $this->getRequest()->getParam('store'),
        ));

        $fieldset->addField('type', 'multiselect', array(
            'label' => Mage::helper('rewards')->__('Show message on'),
            'required' => true,
            'name' => 'type[]',
            'value' => $notificationRule->getType(),
            'values' => Mage::getSingleton('rewards/config_source_notification_position')->toOptionArray(),
        ));
        $fieldset->addField('message', 'editor', array(
            'label' => Mage::helper('rewards')->__('Message'),
            'required' => false,
            'name' => 'message',
            'value' => $notificationRule->getMessage(),
            'config' => Mage::getSingleton('mstcore/wysiwyg_config')->getConfig(),
            'wysiwyg' => true,
            'style' => 'height:15em',
        ));

        return parent::_prepareForm();
    }

    /************************/
}
