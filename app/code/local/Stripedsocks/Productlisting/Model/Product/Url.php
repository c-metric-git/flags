<?php
  class Stripedsocks_Productlisting_Model_Product_Url extends Mage_Catalog_Model_Product_Url 
  {
      /**
     * Format Key for URL
     *
     * @param string $str
     * @return string
     */
    public function formatUrlKey($str)
    {         
        //$urlKey = preg_replace('#[^0-9a-z]+#i', '-', Mage::helper('catalog/product_url')->format($str));   commented by dhiraj for custom product url
        //$urlKey = strtolower($urlKey);   commented by dhiraj  for custom product url        
        $urlKey = preg_replace('#()*!~-=+|\/[^0-9a-z%]+#i', '-', Mage::helper('catalog/product_url')->format($str));  // added by dhiraj for custom product url
        $urlKey = str_replace(' ', '-', $urlKey);   // added by dhiraj for custom product url
        $urlKey = mb_strtolower($urlKey,'UTF-8');  // added by dhiraj for custom product url
        $urlKey = trim($urlKey, '-');
         
        return $urlKey;
    }    
  }    
?>
