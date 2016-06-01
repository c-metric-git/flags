<?php 

  class Stripedsocks_Adminhtml_Model_Observer {
  	
    public function save_price_type($observer)
    {
         $product = $observer->getProduct();
         $bundle_parmas=Mage::app()->getRequest()->getParams(); 
         if(empty($bundle_parmas['id'])) {
             if($bundle_parmas['product']['price_type'] == 0) //dynamice
             {
                $product->setPriceType(0);	
             }
             else
             {
                $product->setPriceType(1); //fixed		
             }
         }

    }

}