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



class Mirasvit_Rewards_Block_Account_List extends Mage_Core_Block_Template
{
    protected $_collection;

    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if ($headBlock = $this->getLayout()->getBlock('head')) {
            $headBlock->setTitle(Mage::helper('rewards')->__('My Reward Points'));
        }
        $toolbar = $this->getLayout()->createBlock('rewards/account_list_toolbar', 'rewards.toolbar')
            ->setTemplate('mst_rewards/account/list/toolbar.phtml')
            // ->setAvailableOrders(
            //     array(
            //         'created_at'  => Mage::helper('rewards')->__('Date'),
            //     ))
            // ->setDefaultOrder('created_at', 'desc')
            // ->setAvailableListModes('list')
            ;
        $toolbar->setCollection($this->getTransactionCollection());
        $this->append($toolbar);
    }

    public function getConfig()
    {
        return Mage::getSingleton('rewards/config');
    }

    public function getTransactionCollection()
    {
        if (!$this->_collection) {
            $this->_collection = Mage::getModel('rewards/transaction')->getCollection()
                ->addFieldToFilter('customer_id', $this->getCustomer()->getId())
                ->setOrder('created_at', 'desc')
                ;
        }

        return $this->_collection;
    }

    /************************/

    protected function getCustomer()
    {
        return Mage::getSingleton('customer/session')->getCustomer();
    }

    public function getBalancePoints()
    {
        return Mage::helper('rewards/balance')->getBalancePoints($this->getCustomer());
    }
}
