<?php
class Wizkunde_ConfigurableBundle_Block_Bundle_Adminhtml_Sales_Order_View_Items_Renderer extends Mage_Bundle_Block_Adminhtml_Sales_Order_View_Items_Renderer{
    
    public function getValueHtml($item)
    {
        $result = $this->htmlEscape($item->getName());
        if (!$this->isShipmentSeparately($item)) {
            if ($attributes = $this->getSelectionAttributes($item)) {
                $result =  sprintf('%d', $attributes['qty']) . ' x ' . $result;
            }
        }
        if (!$this->isChildCalculated($item)) {
            if ($attributes = $this->getSelectionAttributes($item)) {
                $result .= " " . $this->getItem()->getOrder()->formatPrice($attributes['price']);
            }
        }
        $optionvalues = Mage::helper('bundle/catalog_product_configuration')->getBundleOrderItemOptions($item);
        return $result . $optionvalues;
    }
}
