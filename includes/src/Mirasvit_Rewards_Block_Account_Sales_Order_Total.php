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



class Mirasvit_Rewards_Block_Account_Sales_Order_Total extends Mage_Core_Block_Template
{
    public function getLabelProperties()
    {
        return $this->getParentBlock()->getLabelProperties();
    }

    public function getOrder()
    {
        return $this->getParentBlock()->getOrder();
    }

    public function getSource()
    {
        return $this->getParentBlock()->getSource();
    }

    public function getValueProperties()
    {
        return $this->getParentBlock()->getValueProperties();
    }


    public function initTotals()
    {
        if ((float) $this->getOrder()->getRewardsAmount()) {
            $source = $this->getSource();
            $value  = - $source->getRewardsAmount();

            $this->getParentBlock()->addTotal(new Varien_Object(array(
                'code'   => 'reward_points',
                'strong' => false,
                'label'  => Mage::helper('rewards')->formatPoints($source->getRewardsPointsNumber()),
                'value'  => $source instanceof Mage_Sales_Model_Order_Creditmemo ? - $value : $value
            )));
        }

        return $this;
    }
}
