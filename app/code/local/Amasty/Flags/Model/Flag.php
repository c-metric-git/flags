<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Flags
 */
class Amasty_Flags_Model_Flag extends Mage_Core_Model_Abstract
{
    const FLAGS_FOLDER = 'amflags';
    
    protected function _construct()
    {
        $this->_init('amflags/flag');
    }
    
    public function getUrl()
    {
        $url = Mage::getBaseUrl('media') . self::FLAGS_FOLDER . '/' . $this->getId() . '.jpg';
        return $url;
    }
    
    public static function getUploadUrl()
    {
        $url = Mage::getBaseUrl('media') . self::FLAGS_FOLDER . '/';
        return $url;
    }
    
    public static function getUploadDir()
    {
        $dir = Mage::getBaseDir('media') . DS . self::FLAGS_FOLDER . DS;
        return $dir;
    }
    
    public function delete()
    {
        // removing links with orders
        Mage::getModel('amflags/order_flag')->removeLinks($this);
        return parent::delete();
    }
}
