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



/**
 * @method Mirasvit_Rewards_Model_Resource_Earning_Behavior_Collection|Mirasvit_Rewards_Model_Earning_Behavior[] getCollection()
 * @method Mirasvit_Rewards_Model_Earning_Behavior load(int $id)
 * @method bool getIsMassDelete()
 * @method Mirasvit_Rewards_Model_Earning_Behavior setIsMassDelete(bool $flag)
 * @method bool getIsMassStatus()
 * @method Mirasvit_Rewards_Model_Earning_Behavior setIsMassStatus(bool $flag)
 * @method Mirasvit_Rewards_Model_Resource_Earning_Behavior getResource()
 */
class Mirasvit_Rewards_Model_Earning_Behavior extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('rewards/earning_behavior');
    }

    public function toOptionArray($emptyOption = false)
    {
        return $this->getCollection()->toOptionArray($emptyOption);
    }

    /************************/

    public function getByActionCode($code)
    {
        $instance = $this->getCollection()
            ->addFieldToFilter('action_code', $code)
            ->addFieldToFilter('is_active', 1)
            //->add active_from active_to
            //->add store_ids
            ->getFirstItem();

        if ($instance->getId()) {
            return $instance;
        }

        return false;
    }
}
