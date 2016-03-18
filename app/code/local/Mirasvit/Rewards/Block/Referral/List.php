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



class Mirasvit_Rewards_Block_Referral_List extends Mage_Core_Block_Template
{
    protected $_collection;

    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if ($headBlock = $this->getLayout()->getBlock('head')) {
            /* @noinspection PhpUndefinedMethodInspection */
            $headBlock->setTitle(Mage::helper('rewards')->__('Referrals List'));
        }
        $toolbar = $this->getLayout()->createBlock('rewards/referral_list_toolbar', 'rewards.toolbar')
            ->setTemplate('mst_rewards/referral/list/toolbar.phtml')
            ->setAvailableOrders(
                array(
                    'created_at' => Mage::helper('rewards')->__('Date'),
                ))
            ->setDefaultOrder('created_at')
            ->setDefaultDirection('desc')
            ->setAvailableListModes('list')
            ;
        $toolbar->setCollection($this->getReferralCollection());
        $this->append($toolbar);
    }

    public function getConfig()
    {
        return Mage::getSingleton('rewards/config');
    }

    public function getReferralCollection()
    {
        if (!$this->_collection) {
            $this->_collection = Mage::getModel('rewards/referral')->getCollection()
                ->addFieldToFilter('main_table.customer_id', $this->getCustomer()->getId())
                ->setOrder('created_at', 'desc');
        }

        return $this->_collection;
    }

    /************************/

    protected function getCustomer()
    {
        return Mage::getSingleton('customer/session')->getCustomer();
    }

    public function getShareUrl()
    {
        return Mage::getUrl('r/'.$this->getCustomer()->getId());
    }

    public function getLocaleCode()
    {
        return Mage::app()->getLocale()->getLocaleCode();
    }

    public function getAppId()
    {
        return Mage::getSingleton('rewardssocial/config')->getFacebookAppId();
    }
}
