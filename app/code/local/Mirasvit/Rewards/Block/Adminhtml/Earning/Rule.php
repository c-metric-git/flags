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



class Mirasvit_Rewards_Block_Adminhtml_Earning_Rule extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        // $this->_addButton('apply_rules', array(
        //     'label'     => Mage::helper('catalogrule')->__('Apply Rules'),
        //     'onclick'   => "location.href='".$this->getUrl('*/*/applyRules')."'",
        //     'class'     => '',
        // ));

        parent::__construct();
        $this->_controller = 'adminhtml_earning_rule';
        $this->_blockGroup = 'rewards';
        $this->_headerText = Mage::helper('rewards')->__('Earning Rules');
        $this->_addButtonLabel = Mage::helper('rewards')->__('Add New Earning Rule');
    }

    public function getCreateUrl()
    {
        return $this->getUrl('*/*/add');
    }

    /************************/
}
