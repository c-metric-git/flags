<?php

class  Stripedsocks_Layercategory_Model_Layer_Filter_Item extends Mage_Catalog_Model_Layer_Filter_Item  
{
   
    public function getUrl()
    {
        $query = array(
            $this->getFilter()->getRequestVar()=>$this->getValue(),
            Mage::getBlockSingleton('page/html_pager')->getPageVarName() => null // exclude current page from urls
        );
        if(Mage::getBlockSingleton('page/html_header')->getIsHomePage()) {
            return Mage::getUrl('face-paints-by-brand-12-1-c.html', array('_query'=>$query));
        } else {
           return Mage::getUrl('*/*/*', array('_current'=>true, '_use_rewrite'=>true, '_query'=>$query));
        }
        
    }
}
