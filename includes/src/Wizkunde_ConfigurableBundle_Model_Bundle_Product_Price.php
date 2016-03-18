<?php

class Wizkunde_ConfigurableBundle_Model_Bundle_Product_Price extends Mage_Bundle_Model_Product_Price {

    public function getSelectionFinalTotalPrice($bundleProduct, $selectionProduct, $bundleQty, $selectionQty,
                                                $multiplyQty = true, $takeTierPrice = true)
    {
        if (is_null($selectionQty)) {
            $selectionQty = $selectionProduct->getSelectionQty();
        }

        if ($bundleProduct->getPriceType() == self::PRICE_TYPE_DYNAMIC) {
            $price = $selectionProduct->getFinalPrice($takeTierPrice ? $selectionQty : 1);
        } else {
            if ($selectionProduct->getSelectionPriceType()) { // percent
                $product = clone $bundleProduct;
                $product->setFinalPrice($this->getPrice($product));
                Mage::dispatchEvent(
                    'catalog_product_get_final_price',
                    array('product' => $product, 'qty' => $bundleQty)
                );
                $price = $product->getData('final_price') * ($selectionProduct->getSelectionPriceValue() / 100);

            } else { // fixed
                $price = $selectionProduct->getSelectionPriceValue();
            }
        }
        $customOptions = $bundleProduct->getCustomOption('bundle_simple_custom_options');
        $basePrice = $price;
        if($customOptions){
            $custom_options = unserialize($customOptions->getValue());
        }
        if(!empty($custom_options)){
            foreach($custom_options as $id => $info){
                if($selectionProduct->getEntityId() == $id){
                    foreach($custom_options[$id]['options'] as $optionId=>$value){
                        $s_product = Mage::getModel('catalog/product')->load($selectionProduct->getEntityId());
                        $s_option = $s_product->getOptionById($optionId);                       
                        $confItemOption = Mage::getModel('catalog/product_configuration_item_option')
                            ->addData(array(
                                'product_id'=> $s_product->getId(),
                                'product'   => $s_product,
                                'code'      => 'option_'.$optionId,
                                'value'     => is_array($value)? implode(',', $value) : $value,
                            ));
                        $group = $s_option->groupFactory($s_option->getType())
                            ->setOption($s_option)
                            ->setConfigurationItemOption($confItemOption);
                        $price += $group->getOptionPrice($confItemOption->getValue(), $basePrice);
                    }
                }
            }
        }
        if ($multiplyQty) {
            $price *= $selectionQty;
        }

        return min($price,
            $this->_applyGroupPrice($bundleProduct, $price),
            $this->_applyTierPrice($bundleProduct, $bundleQty, $price),
            $this->_applySpecialPrice($bundleProduct, $price)
        );
    }
}