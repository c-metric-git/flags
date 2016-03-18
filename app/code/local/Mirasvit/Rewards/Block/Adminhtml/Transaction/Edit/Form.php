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



class Mirasvit_Rewards_Block_Adminhtml_Transaction_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(
            array(
                'id' => 'edit_form',
                'action' => $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id'))),
                'method' => 'post',
                'enctype' => 'multipart/form-data',
            )
        );

        /** @var Mirasvit_Rewards_Model_Transaction $transaction */
        $transaction = Mage::registry('current_transaction');

        $fieldset = $form->addFieldset('edit_fieldset', array('legend' => Mage::helper('rewards')->__('General Information')));
        if ($transaction->getId()) {
            $fieldset->addField('transaction_id', 'hidden', array(
                'name' => 'transaction_id',
                'value' => $transaction->getId(),
            ));
        }
        $fieldset->addField('amount', 'text', array(
            'label' => Mage::helper('rewards')->__('Points Balance Change'),
            'required' => true,
            'name' => 'amount',
        ));
        $element = $fieldset->addField('history_message', 'text', array(
            'label' => Mage::helper('rewards')->__('Message in the rewards history'),
            'required' => true,
            'name' => 'history_message',
            'note' => Mage::helper('rewards')->__('Customer will see this in his account'),
        ));
        $fieldset->addField('email_message', 'editor', array(
            'label' => Mage::helper('rewards')->__('Message for customer notification email'),
            'name' => 'email_message',
            'config' => Mage::getSingleton('rewards/config_wysiwyg')->getConfig(),
            'wysiwyg' => true,
            'note' => Mage::helper('rewards/message')->getNoteWithVariables(),
            'style' => 'width: 600px; height: 300px;',
        ));

        $fieldset->addField('in_transaction_user', 'hidden',
            array(
                'name' => 'in_transaction_user',
                'id' => 'in_transaction_userz',
            )
        );

        $fieldset->addField('in_transaction_user_old', 'hidden', array('name' => 'in_transaction_user_old'));

        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
    /************************/
}
