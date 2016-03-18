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


class Mirasvit_Rewards_Model_System_Source_Transaction_Status
{
    public static function toArray()
    {
        $result = array(
            Mirasvit_Rewards_Model_Transaction::STATUS_PENDING   => __('Pending'),
            Mirasvit_Rewards_Model_Transaction::STATUS_APPROVED  => __('Approved'),
            Mirasvit_Rewards_Model_Transaction::STATUS_DISCARDED => __('Discarded'),
        );

        return $result;
    }

    public function toOptionArray()
    {
        $result = array();
        foreach($this->toArray() as $k=>$v) {
            $result[] = array('value'=>$k, 'label'=>$v);
        }
        return $result;
    }
}