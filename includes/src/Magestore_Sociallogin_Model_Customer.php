<?php
class Magestore_Sociallogin_Model_Customer extends Mage_Core_Model_Abstract
{
    public function _construct()
    {    
        // Note that the membership_id refers to the key field in your database table.
        $this->_init('sociallogin/customer', 'twitter_customer_id');
    }
}