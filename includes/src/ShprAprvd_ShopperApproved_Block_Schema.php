<?php
/**
 * ShopperApproved Module for Magento 
 * @package     ShprAprvd_ShopperApproved
 * @author      ShprAprvd (http://www.shopperapproved.com/)
 * @copyright   Copyright (c) 2014 ShprAprvd (http://www.shopperapproved.com/)
 * @license     Open Software License
 */
 
class ShprAprvd_ShopperApproved_Block_Schema extends Mage_Core_Block_Template
{
    /*
     * Constructor method
     *
     * @access public
     * @param null
     * @return null
     */
    public function _construct()
    {
        parent::_construct();
        $website_id = Mage::helper('shopperapproved')->getAccountId();
        if(Mage::helper('shopperapproved')->getEnabled() == true && !empty($website_id)) {
            $this->setTemplate('shopperapproved/schema.phtml');
        }
    }

    /*
     * Helper method
     *
     * @access public
     * @param string $key
     * @return string
     */
    public function getSetting($key = null)
    {
        static $data;
        if(empty($data)) {
            $data = array(
                'account' => Mage::helper('shopperapproved')->getAccountId(),
				'token' => Mage::helper('shopperapproved')->getAccountToken(),
                'enabled' => Mage::helper('shopperapproved')->getEnabled()
            );

            $customer = Mage::getSingleton('customer/session')->getCustomer();
            if(!empty($customer)) {
                    $data['name'] = $customer->getName();
                    $data['email'] = $customer->getEmail();
                $address = $customer->getDefaultBillingAddress();
                if(!empty($address)) {
                    $address = $customer->getDefaultShippingAddress();
                }
                if(!empty($address)) {
                    $country = Mage::getModel('directory/country')->loadByCode($address->getCountry());
                    $data['country'] = $country->getName();
                    $data['state'] = $address->getRegion();
                }
            }

            $lastOrderId = Mage::getSingleton('checkout/session')->getLastRealOrderId();
            if (!empty($lastOrderId)) {
                $data['orderid'] = $lastOrderId;
            }
        }
        if(isset($data[$key])) {
            return $data[$key];
        } else {
            return null;
        }
    }
}