<?php
class Wizkunde_ConfigurableBundle_Model_Catalog_Type_Simple extends Mage_Catalog_Model_Product_Type_Simple{    
    
    /**
     * Need to add some simple product with custom options into configurable product 
     * Retrieve array of "subproducts"     
     * @param  array
     * @param  Mage_Catalog_Model_Product $product
     * @return array
     */
    protected function _prepareProduct(Varien_Object $buyRequest, $product, $processMode)
    {   
        $product = $this->getProduct($product);
        /* @var Mage_Catalog_Model_Product $product */
        // try to add custom options
        try {
            $options = $this->_prepareOptions($buyRequest, $product, $processMode);
        } catch (Mage_Core_Exception $e) {
            return $e->getMessage();
        }

        if (is_string($options)) {
            return $options;
        }
        // try to found super product configuration
        // (if product was buying within grouped product)
        $superProductConfig = $buyRequest->getSuperProductConfig();
        if (!empty($superProductConfig['product_id'])
            && !empty($superProductConfig['product_type'])
        ) {
            $superProductId = (int) $superProductConfig['product_id'];
            if ($superProductId) {
                if (!$superProduct = Mage::registry('used_super_product_'.$superProductId)) {
                    $superProduct = Mage::getModel('catalog/product')->load($superProductId);
                    Mage::register('used_super_product_'.$superProductId, $superProduct);
                }
                if ($superProduct->getId()) {
                    $assocProductIds = $superProduct->getTypeInstance(true)->getAssociatedProductIds($superProduct);
                    if (in_array($product->getId(), $assocProductIds)) {
                        $productType = $superProductConfig['product_type'];
                        $product->addCustomOption('product_type', $productType, $superProduct);

                        $buyRequest->setData('super_product_config', array(
                            'product_type' => $productType,
                            'product_id'   => $superProduct->getId()
                        ));
                    }
                }
            }
        }

        $product->prepareCustomOptions();
        $buyRequest->unsetData('_processing_params'); // One-time params only
        $product->addCustomOption('info_buyRequest', serialize($buyRequest->getData()));
                
        $custom_options = $buyRequest->getBundleSimpleCustomOptions();
        if(!empty($custom_options)){
            foreach($custom_options as $id => $info){
                if($product->getEntityId() == $id){
                    foreach($custom_options[$id]['options'] as $optionId=>$value){                        
                        $optionValue = is_array($value)? implode(',', $value) : $value;
                        $product->addCustomOption(self::OPTION_PREFIX . $optionId, $optionValue);                       
                    }
                }
            }
        }
        
        if ($options) {
            $optionIds = array_keys($options);
            $product->addCustomOption('option_ids', implode(',', $optionIds));
            foreach ($options as $optionId => $optionValue) {
                $product->addCustomOption(self::OPTION_PREFIX . $optionId, $optionValue);
            }
        }

        // set quantity in cart
        if ($this->_isStrictProcessMode($processMode)) {
            $product->setCartQty($buyRequest->getQty());
        }
        $product->setQty($buyRequest->getQty());

        return array($product);
    }
}