<?php

class Chapagain_ClearPrint_Helper_Data extends Mage_Core_Helper_Abstract
{	
	public function isEnabledPrintProduct()
    { 		
        return Mage::getStoreConfig('catalog/clearprint/enable_print_product');
    }
    
    public function isEnabledPrintCart()
    { 		
        return Mage::getStoreConfig('catalog/clearprint/enable_print_cart');
    }
    
    public function getVersion()
    {
		return Mage::getVersion();
	}
	
	public function getVersion19() 
	{
		if (version_compare($this->getVersion(), '1.9', '>=')){
			return true;
		} 
		return false;
	}
}
