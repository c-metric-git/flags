<?php
/**
 * MageWorx
 * Others Also Bought Extension
 *
 * @category   MageWorx
 * @package    MageWorx_AlsoBought
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_AlsoBought_Model_Sortorder
{
    protected $_options;

    public function toOptionArray()
    {
        $options = array(
            array('value' => '1', 'label' => Mage::helper('mageworx_alsobought')->__('Popularity')),
            array('value' => '2', 'label' => Mage::helper('mageworx_alsobought')->__('Random')),
        );
        return $options;
    }
}