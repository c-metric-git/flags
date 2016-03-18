<?php
require_once 'Mage/Catalog/controllers/ProductController.php';
class Wizkunde_ConfigurableBundle_AjaxController extends Mage_Catalog_ProductController
{
    /**
     * Get the HTML of the attributes that are possible for a specific configurable in an option
     */
    public function attributesAction()
    {
        $configurableId = (int) $this->getRequest()->getParam('id');
        $optionId = (int) $this->getRequest()->getParam('option_id');
        $bundleId = (int) $this->getRequest()->getParam('bundle_id');

        $bundleOption = Mage::getModel('bundle/option')->load($optionId);
        $configurableProduct = Mage::getModel('catalog/product')->load($configurableId);

        // Collect options applicable to the configurable product
        $productAttributeOptions = $configurableProduct->getTypeInstance(true)->getConfigurableAttributesAsArray($configurableProduct);
        $returnData = '';

        if($bundleOption->getType() == 'checkbox' || $bundleOption->getType() == 'multi') {
            if (Mage::helper('core')->isModuleEnabled('Wizkunde_ConfigurableGrid') == true && Mage::helper('configurablegrid/grid')->gridIsEnabled($configurableProduct)) {
                $block = $this->getLayout()->createBlock('Wizkunde_ConfigurableGrid_Block_Product_View_Type_Configurable')->setProduct($configurableProduct);
                $block->setTemplate('configurablegrid/product/view/type/bundle/option/partials/grid.phtml');
            } else {
                $block = $this->getLayout()->createBlock('Mage_Core_Block_Template');
                $block->setTemplate('configurablebundle/catalog/product/view/type/bundle/option/partials/nogrid.phtml');
            }

            $block->setData(array(
                'product'            => $configurableProduct,
                'option_id'          => $optionId,
                'bundle_id'          => $bundleId
            ));

            $returnData .= $block->renderView();
        } else {
            foreach ($productAttributeOptions as $i => $productAttribute) {
                $block = $this->getLayout()->createBlock('Mage_Core_Block_Template');
                $block->setTemplate('configurablebundle/catalog/product/view/type/bundle/option/partials/' . $bundleOption->getType() . '.phtml');

                $block->setData(array(
                    'option_id'          => $optionId,
                    'attribute'         => $productAttribute,
                    'attribute_order'   => $i
                ));

                $returnData .= $block->renderView();
            }
        }

        $this->getResponse()->setBody($returnData);
    }

    public function resolveBundleSelectionId($bundleId, $selectionId)
    {
        $product = Mage::getModel('catalog/product')->load($bundleId);

        $selections = $product->getTypeInstance(true)
            ->getSelectionsCollection($product->getTypeInstance(true)
                ->getOptionsIds($product), $product);

        foreach($selections as $selection){
            if($selection->getSelectionId() == $selectionId) {
                return $selection->getProductId();
            }
        }
    }

    public function nextattributeidAction()
    {
        $selectionId = (int) $this->getRequest()->getParam('id');
        $bundleId = (int) $this->getRequest()->getParam('bundle_id');
        $attributeId = (int) $this->getRequest()->getParam('attribute_id');

        $configurableId = $this->resolveBundleSelectionId($bundleId, $selectionId);

        if($configurableId > 0) {
            $configurableProduct = Mage::getModel('catalog/product')->load($configurableId);

            // Collect options applicable to the configurable product
            $productAttributeOptions = $configurableProduct->getTypeInstance(true)->getConfigurableAttributesAsArray($configurableProduct);

            foreach($productAttributeOptions as $i => $attribute) {
                if($attribute['attribute_id'] == $attributeId) {
                    $productAttribute = $productAttributeOptions[$i+1];
                    break;
                }
            }

            $this->getResponse()->setBody($productAttribute['attribute_id']);
        } else {
            $this->getResponse()->setBody(0);
        }
    }

    /**
     * Get the possible values of the next attribute
     */
    public function singleattributeAction()
    {
        $selectionId = (int) $this->getRequest()->getParam('id');
        $optionId = (int) $this->getRequest()->getParam('option_id');
        $attributeId = (int) $this->getRequest()->getParam('attribute_id');
        $bundleId = (int) $this->getRequest()->getParam('bundle_id');
        $attributes = Zend_Json::decode($this->getRequest()->getPost('attributes'));

        $configurableId = $this->resolveBundleSelectionId($bundleId, $selectionId);

        $currentAttribute = Mage::getModel('catalog/resource_eav_attribute')->load($attributeId);

        $bundleOption = Mage::getModel('bundle/option')->load($optionId);
        $configurableProduct = Mage::getModel('catalog/product')->load($configurableId);

        $col = $configurableProduct->getTypeInstance()
            ->getUsedProductCollection()
            ->addAttributeToSelect('*');
            //->addFilterByRequiredOptions(); show custom options

        // Filter for set attributes
        $returnEmpty = false;
        foreach($attributes as $attributeKey => $attributeValues)
        {
            if(is_numeric(current($attributeValues))) {
                $attribute = Mage::getModel('catalog/resource_eav_attribute')->load($attributeKey);
                $col->addAttributeToFilter($attribute->getName(), array('in', $attributeValues));
            } else {
                $returnEmpty = (count($attributes) == 1) ? true : false;
            }
        }

        $filteredValues = array();

        if($returnEmpty == false) {
            foreach($col as $simple_product){
                $frontendValue = $simple_product->getResource()->getAttribute($currentAttribute->getName())->getFrontend()->getValue($simple_product);

                // Do not add disabled products
                if($simple_product->getStatus() != 2) {
                    $filteredValues[$simple_product->getData($currentAttribute->getName())] = array(
                        'value_index'   => $simple_product->getData($currentAttribute->getName()),
                        'store_label'   => $frontendValue
                    );
                }
            }
        }

        if(isset($currentAttribute) && count($currentAttribute) > 0) {
            $this->getResponse()->setBody(json_encode($filteredValues));
        } else {
            // No new options to return
            $this->getResponse()->setBody('');
        }
    }

    public function realproductsAction()
    {
        $attributes = Zend_Json::decode($this->getRequest()->getPost('attributes')); 
        $optionId = (int) $this->getRequest()->getParam('option_id');
        $bundleId = (int) $this->getRequest()->getParam('bundle_id');
   
        // Load the configurable
        $configurableId = (int) $this->getRequest()->getParam('id');
        $configurableProduct = Mage::getModel('catalog/product')->load($configurableId);

        $products = array();

        $col = $configurableProduct->getTypeInstance()
                                    ->getUsedProductCollection()
                                    ->addAttributeToSelect('*');

        // Filter for set attributes
        foreach($attributes as $attributeKey => $attributeValues)
        {
            $attribute = Mage::getModel('catalog/resource_eav_attribute')->load($attributeKey);
            $col->addAttributeToFilter($attribute->getName(), array('in', $attributeValues));
        }

        foreach($col as $simple_product){
            $products[] = $simple_product->getId();
        }

        $bundle = Mage::getModel('catalog/product')->load($bundleId);
        $selectionCollection = $bundle->getTypeInstance(true)->getSelectionsCollection(
            $bundle->getTypeInstance(true)->getOptionsIds($bundle), $bundle
        );

        // Transform the IDs to selection IDs
        $selectionIds = array();
        foreach($selectionCollection as $selection)
        {
            foreach($products as $productId) {
                if($productId == $selection->getProductId()) {
                    $selectionIds[] = $selection->getSelectionId();
                }
            }
        }   
         
        $this->getResponse()->setBody(Zend_Json::encode($selectionIds));
    }
    
    public function productoptionsAction()
    {
        $attributes = Zend_Json::decode($this->getRequest()->getPost('attributes')); 
        $optionId = (int) $this->getRequest()->getParam('option_id');       
   
        // Load the configurable
        $configurableId = (int) $this->getRequest()->getParam('id');
        $configurableProduct = Mage::getModel('catalog/product')->load($configurableId);
        if (!$configurableId) {
            return false;
        }
        
        if (!Mage::helper('catalog/product')->canShow($configurableProduct)) {
            return false;
        }
        
        $super_attribute = array();
        foreach($attributes as $ky => $option){
            if($option[0] != 'Choose an Option...'){
                $super_attribute[$ky] = $option;
            }
        }        
        
        $attrs  = $configurableProduct->getTypeInstance(true)->getConfigurableAttributesAsArray($configurableProduct); 
        if(empty($super_attribute) || (count($attrs) != count($super_attribute))) return false;
        if(count($attrs) == count($super_attribute)){
            $childProduct = Mage::getModel('catalog/product_type_configurable')->getProductByAttributes($super_attribute, $configurableProduct);            
            if ($childProduct == false || !$childProduct->getId()) {
                    return false;
            }
        } 
        $childProduct = Mage::getModel('catalog/product')->load($childProduct->getId()); 
        Mage::register('current_product', $childProduct);
        Mage::register('product', $childProduct);

        Mage::helper('catalog/product_view')->initProductLayout($childProduct, $this);
        $this->loadLayout(false);
        $this->renderLayout();
    }

    /**
     * Get the first attribute id
     */
    public function getfirstattributeAction()
    {
        $configurableId = (int) $this->getRequest()->getParam('id');

        if($configurableId <= 0) {
            $this->getResponse()->setBody('Invalid configurable id');
        }

        $configurableProduct = Mage::getModel('catalog/product')->load($configurableId);
        $firstAttribute =  reset($configurableProduct->getTypeInstance()->getConfigurableAttributes());

        $this->getResponse()->setBody(Zend_Json::encode($firstAttribute->getProductAttribute()->getAttributeId()));
    }

    /**
     * Get the required information of a product
     */
    public function realproductinfoAction()
    {
        if((int) $this->getRequest()->getParam('product_id') <= 0) {
            $this->getResponse()->setBody('Invalid product id');
        }

        $productId = (int) $this->getRequest()->getParam('product_id');

        // Transform the selection to a product
        $product = Mage::getModel('catalog/product')->load($productId);
        $stocklevel = (int)Mage::getModel('cataloginventory/stock_item')->loadByProduct($product)->getQty();

        $this->getResponse()->setBody(
            Zend_Json::encode(
                array(
                    'image' => (string)Mage::helper('catalog/image')->init($product, 'image')->resize(100),
                    'stock' => $stocklevel,
                    'name' => $product->getName(),
                    'description' => $product->getDescription()
                )
            )
        );
    }

    /**
     * Get the required information of a product
     */
    public function productinfoAction()
    {
        if((int) $this->getRequest()->getParam('bundle_id') <= 0) {
            $this->getResponse()->setBody('Invalid selection id');
        }

        if((int) $this->getRequest()->getParam('selection_id') <= 0) {
            $this->getResponse()->setBody('Invalid selection id');
        }

        $selectionId = (int) $this->getRequest()->getParam('selection_id');
        $bundleId = (int) $this->getRequest()->getParam('bundle_id');

        // Transform the selection to a product
        $bundle = Mage::getModel('catalog/product')->load($bundleId);
        $selectionCollection = $bundle->getTypeInstance(true)->getSelectionsCollection(
            $bundle->getTypeInstance(true)->getOptionsIds($bundle), $bundle
        );

        foreach($selectionCollection as $selection)
        {
            if($selectionId == $selection->getSelectionId()) {
              $productId = $selection->getProductId();
            }
        }

        $product = Mage::getModel('catalog/product')->load($productId);
        $stocklevel = (int)Mage::getModel('cataloginventory/stock_item')->loadByProduct($product)->getQty();

        $this->getResponse()->setBody(
            Zend_Json::encode(
                array(
                    'image' => (string)Mage::helper('catalog/image')->init($product, 'image')->resize(100),
                    'stock' => $stocklevel,
                    'name' => $product->getName(),
                    'description' => $product->getDescription()
                )
            )
        );
    }
}
