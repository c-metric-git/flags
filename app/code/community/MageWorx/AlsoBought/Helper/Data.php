<?php
/**
 * MageWorx
 * Others Also Bought Extension
 *
 * @category   MageWorx
 * @package    MageWorx_AlsoBought
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_AlsoBought_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * @return string
     */
    public function getTitle()
    {
        return Mage::getStoreConfig('mageworx_alsobought/main/title');
    }

    /**
     * Get Products number in block to display
     * @return int
     */
    public function getProductsNumber()
    {
        return (int)Mage::getStoreConfig('mageworx_alsobought/main/products_number');
    }

    /**
     * Get Order statuses
     * @return array
     */
    public function getOrderStatus()
    {
        return explode(',', Mage::getStoreConfig('mageworx_alsobought/main/order_status'));
    }

    /**
     * @return mixed
     */
    public function getSortOrder()
    {
        return Mage::getStoreConfig('mageworx_alsobought/main/sort_order');
    }
}