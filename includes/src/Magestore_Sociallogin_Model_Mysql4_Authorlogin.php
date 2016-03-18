<?php

class Magestore_Sociallogin_Model_Mysql4_Authorlogin extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the membership_id refers to the key field in your database table.
        $this->_init('sociallogin/authorlogin', 'author_customer_id');
    }
}