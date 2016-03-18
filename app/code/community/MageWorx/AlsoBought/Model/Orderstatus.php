<?php
/**
 * MageWorx
 * Others Also Bought Extension
 *
 * @category   MageWorx
 * @package    MageWorx_AlsoBought
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_AlsoBought_Model_Orderstatus extends Mage_Adminhtml_Model_System_Config_Source_Order_Status
{
    public function toOptionArray()
    {
        $options = parent::toOptionArray();
        array_shift($options);
        return $options;
    }
}
