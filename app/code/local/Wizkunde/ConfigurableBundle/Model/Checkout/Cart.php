<?php

class Wizkunde_ConfigurableBundle_Model_Checkout_Cart extends Mage_Checkout_Model_Cart {

    /**
     * Get request for product add to cart procedure
     *
     * @param   mixed $requestInfo
     * @return  Varien_Object
     */
    protected function _getProductRequest($requestInfo)
    {
        if ($requestInfo instanceof Varien_Object) {
            $request = $requestInfo;
        } elseif (is_numeric($requestInfo)) {
            $request = new Varien_Object(array('qty' => $requestInfo));
        } else {

             if(isset($requestInfo['bundle_has_custom_options'])){
                if(!empty($requestInfo['options'])){
                    foreach($requestInfo['bundle_simple_custom_options'] as $id => $opt){
                        foreach($opt['options'] as $key => $o){
                            $opt['options'][$key] = $requestInfo['options'][$key];
                            $requestInfo['bundle_simple_custom_options'][$id] = $opt;
                        }
                    }
                }
                unset($requestInfo['options']);
            }            
            $request = new Varien_Object($requestInfo);
        }

        if (!$request->hasQty()) {
            $request->setQty(1);
        }

        return $request;
    }
}