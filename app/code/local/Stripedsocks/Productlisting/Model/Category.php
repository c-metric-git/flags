<?php
  class Stripedsocks_Productlisting_Model_Category extends Mage_Catalog_Model_Category 
  {
      /**
     * Format URL key from name or defined key
     *
     * @param string $str
     * @return string
     */
    public function formatUrlKey($str)
    {
        $str = Mage::helper('catalog/product_url')->format($str);
        $urlKey = preg_replace('#[^0-9a-z]+#i', '-', $str);
        $urlKey = strtolower($urlKey);
        $urlKey = trim($urlKey, '-');
        
        if($urlKey != $str) {
            /*$str = Mage::helper('catalog/product_url')->format($str);   
            $urlKey = str_replace("&","and",$str);
            $urlKey = preg_replace('#[^0-9a-z]+#i', '-', $urlKey);
            $urlKey = strtolower($urlKey);
            $urlKey = trim($urlKey, '-'); */ 
            $urlKey = str_replace(' ', '-', $str);  
            $urlKey = trim($urlKey); 
        }
        return $urlKey;    
    }         
  }    
?>
