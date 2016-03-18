<?php

class Magestore_Sociallogin_Model_Mysql4_Vklogin extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {            
        $this->_init('sociallogin/vklogin', 'vk_customer_id');
    }
}