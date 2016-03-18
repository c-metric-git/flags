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



class Mirasvit_Rewards_Block_Adminhtml_Customer_Edit_Tabs_Rewards extends Mage_Adminhtml_Block_Widget
implements Mage_Adminhtml_Block_Widget_Tab_Interface
{

    public function getAfter(){
        return 'wishlist';
    }

    public function getTabLabel()
    {
        return Mage::helper('rewards')->__('Reward Points');
    }

    public function getTabTitle()
    {
        return Mage::helper('rewards')->__('Reward Points');
    }

    public function canShowTab()
    {
        return $this->getId() ? true : false;
    }

    public function getId()
    {
        return $this->getRequest()->getParam('id');
    }

    public function isHidden()
    {
        return false;
    }

    protected function _toHtml()
    {
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('_rewards');
        $customer = Mage::registry('current_customer');
        $amount = Mage::helper('rewards/balance')->getBalancePoints($customer);

        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>Mage::helper('rewards')->__('Rewards Information')));

        $fieldset->addField('balance', 'label',
            array(
                'label' => Mage::helper('rewards')->__('Available Points Balance'),
                'name'  => 'balance',
                'value' => Mage::helper('rewards')->formatPoints($amount)
            )
        );

        $fieldset->addField('rewards_change_balance', 'text',
            array(
                'label' => Mage::helper('rewards')->__('Change Balance'),
                'name'  => 'rewards_change_balance',
                'note' => Mage::helper('rewards')->__('Enter positive or negative number of points. E.g. 10 or -10')
            )
        );

        $fieldset->addField('rewards_message', 'text',
            array(
                'label' => Mage::helper('rewards')->__('Message in the rewards history'),
                'name'  => 'rewards_message',
                'note' => Mage::helper('rewards')->__('Customer will see this in his account'),
//                'value' => Mage::helper('rewards')->__('Changed by store administrator')
            )
        );

        $grid = $this->getLayout()->createBlock('rewards/adminhtml_customer_edit_tabs_transaction_grid','rewards.grid');

        $html = "
<div class=\"entry-edit\">
{$form->toHtml()}
</div>
<div class=\"entry-edit\">
<div class=\"entry-edit-head\">
    <h4 class=\"icon-head head-edit-form fieldset-legend\">Transactions</h4>
</div>
<div class=\"fieldset \">
    <div class=\"hor-scroll\">
        {$grid->toHtml()}
    </div>
</div>
</div>
        ";
        return $html;
    }
}