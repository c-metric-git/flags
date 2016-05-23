<?php

/**

 * ShopperApproved Module for Magento 

 * @package     ShprAprvd_ShopperApproved

 * @author      ShprAprvd (http://www.shopperapproved.com/)

 * @copyright   Copyright (c) 2014 ShprAprvd (http://www.shopperapproved.com/)

 * @license     Open Software License

 */



class ShprAprvd_ShopperApproved_Helper_Data extends Mage_Core_Helper_Abstract

{

    public function getAccountId()

    {

        return Mage::getStoreConfig('shpraprvd/shopperapproved/account_id');

    }



    public function getEnabled()

    {

        return (bool)Mage::getStoreConfig('shpraprvd/shopperapproved/enabled');

    }

	

	public function getAccountToken()

    {

        return Mage::getStoreConfig('shpraprvd/shopperapprovedschema/account_token');

    }

}