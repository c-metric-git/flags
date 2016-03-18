<?php

class Wizkunde_ConfigurableBundle_Block_Sales_Order_Items_Renderer extends Mage_Bundle_Block_Sales_Order_Items_Renderer{
    
    public function getValueHtml($item)
    {
        $optionvalues = Mage::helper('bundle/catalog_product_configuration')->getBundleOrderItemOptions($item);
        if ($attributes = $this->getSelectionAttributes($item)) {
            return  sprintf('%d', $attributes['qty']) . ' x ' .
                $this->htmlEscape($item->getName()) .
                " " . $this->getOrder()->formatPrice($attributes['price'])
                    . $optionvalues;            
        } else {
            return $this->htmlEscape($item->getName());
        }
    }
    
}