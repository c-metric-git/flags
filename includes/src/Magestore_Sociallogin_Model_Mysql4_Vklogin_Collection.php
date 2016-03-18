<?php

class Magestore_Sociallogin_Model_Mysql4_Vklogin_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('sociallogin/vklogin');
    }
}