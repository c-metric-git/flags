<?php
class IWD_OrderManager_Helper_Composite extends Mage_Adminhtml_Helper_Catalog_Product_Composite
{
    protected function _initConfigureResultLayout($controller, $isOk, $productType)
    {
        $update = $controller->getLayout()->getUpdate();
        if ($isOk) {
            if($productType == 'configurable'){
                $update->addHandle('IWD_OM_ADMINHTML_CATALOG_PRODUCT_COMPOSITE_CONFIGURE')
                    ->addHandle('IWD_OM_PRODUCT_TYPE_configurable');
            }else{
                $update->addHandle('IWD_OM_ADMINHTML_CATALOG_PRODUCT_COMPOSITE_CONFIGURE')
                    ->addHandle('PRODUCT_TYPE_' . $productType);
            }
        } else {
            $update->addHandle('ADMINHTML_CATALOG_PRODUCT_COMPOSITE_CONFIGURE_ERROR');
        }
        $controller->loadLayoutUpdates()->generateLayoutXml()->generateLayoutBlocks();
        return $this;
    }
}