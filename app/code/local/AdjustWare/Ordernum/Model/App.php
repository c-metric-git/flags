<?php
/**
 * Custom Order Number Pro
 *
 * @category:    AdjustWare
 * @package:     AdjustWare_Ordernum
 * @version      5.1.5
 * @license:     d0NyTkcYcW64yuyl9Cf2M6q3gBilLUVMAwQSumkwPP
 * @copyright:   Copyright (c) 2015 AITOC, Inc. (http://www.aitoc.com)
 */
/**
 *
 * @copyright  Copyright (c) 2011 AITOC, Inc.
 * @package    AdjustWare_Ordernum
 * @author lyskovets
 */
class AdjustWare_Ordernum_Model_App 
{
    public function hasStore($code)
    {
        $stores = Mage::app()->getStores();
        return $this->_hasEntity($stores, $code);
    }
    
    public function hasWebsite($code)
    {
        $websites = Mage::app()->getWebsites();
        return $this->_hasEntity($websites, $code);
    }
    
    private function _hasEntity($aEntities,$code)
    {
        foreach($aEntities as $oEntity)
        {
            if($code == $oEntity->getCode())
            {
                return true;
            }    
        }
        return false;
    }  
}