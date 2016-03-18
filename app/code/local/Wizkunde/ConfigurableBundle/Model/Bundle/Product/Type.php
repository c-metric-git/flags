<?php

class Wizkunde_ConfigurableBundle_Model_Bundle_Product_Type extends Mage_Bundle_Model_Product_Type
{
    /**
     * @var Varien_Object buyRequest
     */
    protected $_buyRequest = null;

    /**
     * Retrive bundle selections collection based on used options
     *
     * @param array $optionIds
     * @param Mage_Catalog_Model_Product $product
     * @return Mage_Bundle_Model_Mysql4_Selection_Collection
     */
    public function getSelectionsCollection($optionIds, $product = null)
    {
        if (!$this->getProduct($product)->hasData($this->_keySelectionsCollection)) {
            $selectionsCollection = Mage::getResourceModel('bundle/selection_collection')
                ->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
                ->setFlag('require_stock_items', true)
                ->setPositionOrder()
                ->addStoreFilter($this->getStoreFilter($product))
                ->setOptionIdsFilter($optionIds);

            $this->getProduct($product)->setData($this->_keySelectionsCollection, $selectionsCollection);
        }

        return $this->getProduct($product)->getData($this->_keySelectionsCollection);
    }

    /**
     * Get Configurable Options in JSON format
     *
     * @param $_product
     * @return string
     */
    public function getOptionsJSON($_product)
    {
        $optionsCollection = $this->getOptionsCollection($_product);

        $json = array();

        foreach($this->getSelectionsCollection($this->getOptionsIds($_product)) as $selection)
        {
            foreach($optionsCollection as $option) {
                if($option->getId() == $selection->getOptionId()) {
                    $currentOption = $option;
                }
            }

            if($selection->isConfigurable()) {
                $configurableProduct = new Mage_Catalog_Block_Product_View_Type_Configurable();
                $configurableProduct->setData('product', $selection);

                // Have to decode to prevent double json encoding
                // Collect options applicable to the configurable product
                $productAttributeOptions = $selection->getTypeInstance(true)->getConfigurableAttributesAsArray($selection);
                $attributeOptions = array();
                foreach ($productAttributeOptions as $productAttribute) {
                    foreach ($productAttribute['values'] as $attribute) {
                        $attributeOptions[$productAttribute['label']][$attribute['value_index']] = $attribute['store_label'];
                    }
                }

                $json[$selection->getOptionId()]['option']['required'] = (bool)$currentOption->getRequired();
                $json[$selection->getOptionId()]['attributes'] = $attributeOptions;
                $json[$selection->getOptionId()]['product'] = Zend_Json::decode($configurableProduct->getJsonConfig());
            } else {
                $json[$selection->getOptionId()]['option']['required'] = (bool)$currentOption->getRequired();
                $json[$selection->getOptionId()]['product'] = $selection->getData();
            }
        }

        return Zend_Json::encode($json);
    }

    /**
     * Retrieve bundle selections collection based on ids
     *
     * @param array $selectionIds
     * @param Mage_Catalog_Model_Product $product
     * @return Mage_Bundle_Model_Mysql4_Selection_Collection
     */
    public function getSelectionsByIds($selectionIds, $product = null)
    {
        sort($selectionIds);

        $storeId = $this->getProduct($product)->getStoreId();
        $usedSelections = Mage::getResourceModel('bundle/selection_collection')
            ->addAttributeToSelect('*')
            ->setFlag('require_stock_items', true)
            ->addStoreFilter($this->getStoreFilter($product))
            ->setStoreId($storeId)
            ->setPositionOrder()
            ->setSelectionIdsFilter($selectionIds)
        ;

        if (!Mage::helper('catalog')->isPriceGlobal() && $storeId) {
            $websiteId = Mage::app()->getStore($storeId)->getWebsiteId();
            $usedSelections->joinPrices($websiteId);
        }

        $this->getProduct($product)->setData($this->_keyUsedSelections, $usedSelections);
        $this->getProduct($product)->setData($this->_keyUsedSelectionsIds, $selectionIds);

        return $usedSelections;
    }

    /**
     * Prepare product and its configuration to be added to some products list.
     * Perform standard preparation process and then prepare of bundle selections options.
     *
     * @param Varien_Object $buyRequest
     * @param Mage_Catalog_Model_Product $product
     * @param string $processMode
     * @return array|string
     */
    protected function _prepareProduct(Varien_Object $buyRequest, $product, $processMode)
    {
        $result = $this->bundlePrepareProduct($buyRequest, $product, $processMode);

        if (is_string($result)) {
            return $result;
        }

        $selections = array();
        $product = $this->getProduct($product);
        $isStrictProcessMode = $this->_isStrictProcessMode($processMode);

        $skipSaleableCheck = Mage::helper('catalog/product')->getSkipSaleableCheck();
        $_appendAllSelections = (bool)$product->getSkipCheckRequiredOption() || $skipSaleableCheck;

        $options = $buyRequest->getBundleOption();
        if (is_array($options)) {
            $options = array_filter($options, 'intval');
            $qtys = $buyRequest->getBundleOptionQty();
            foreach ($options as $_optionId => $_selections) {
                if (empty($_selections)) {
                    unset($options[$_optionId]);
                }
            }
            $optionIds = array_keys($options);

            if (empty($optionIds) && $isStrictProcessMode) {
                return Mage::helper('bundle')->__('Please select options for product.');
            }

            $product->getTypeInstance(true)->setStoreFilter($product->getStoreId(), $product);

            $optionsCollection = $this->getOptionsCollection($product);
            if (!$this->getProduct($product)->getSkipCheckRequiredOption() && $isStrictProcessMode) {
                foreach ($optionsCollection->getItems() as $option) {
                    if ($option->getRequired() && !isset($options[$option->getId()])) {
                        return Mage::helper('bundle')->__('Required options are not selected.');
                    }
                }
            }
            $selectionIds = array();

            foreach ($options as $optionId => $selectionId) {
                if (!is_array($selectionId)) {
                    if ($selectionId != '') {
                        $selectionIds[] = (int)$selectionId;
                    }
                } else {
                    foreach ($selectionId as $id) {
                        if ($id != '') {
                            $selectionIds[] = (int)$id;
                        }
                    }
                }
            }

            // If product has not been configured yet then $selections array should be empty
            if (!empty($selectionIds)) {
                $selections = $this->getSelectionsByIds($selectionIds, $product);


                // Check if added selections are still on sale
                foreach ($selections->getItems() as $key => $selection) {
                    if (!$selection->isSalable() && !$skipSaleableCheck) {
                        $_option = $optionsCollection->getItemById($selection->getOptionId());
                        if (is_array($options[$_option->getId()]) && count($options[$_option->getId()]) > 1) {
                            $moreSelections = true;
                        } else {
                            $moreSelections = false;
                        }
                        if ($_option->getRequired()
                            && (!$_option->isMultiSelection() || ($_option->isMultiSelection() && !$moreSelections))
                        ) {
                            return Mage::helper('bundle')->__('Selected required options are not available.');
                        }
                    }
                }

                $optionsCollection->appendSelections($selections, false, $_appendAllSelections);

                $selections = $selections->getItems();
            } else {
                $selections = array();
            }
        } else {
            $product->setOptionsValidationFail(true);
            $product->getTypeInstance(true)->setStoreFilter($product->getStoreId(), $product);

            $optionCollection = $product->getTypeInstance(true)->getOptionsCollection($product);

            $optionIds = $product->getTypeInstance(true)->getOptionsIds($product);
            $selectionIds = array();

            $selectionCollection = $product->getTypeInstance(true)
                ->getSelectionsCollection(
                    $optionIds,
                    $product
                );

            $options = $optionCollection->appendSelections($selectionCollection, false, $_appendAllSelections);

            foreach ($options as $option) {
                if ($option->getRequired() && count($option->getSelections()) == 1) {
                    $selections = array_merge($selections, $option->getSelections());
                } else {
                    $selections = array();
                    break;
                }
            }
        }
        if (count($selections) > 0 || !$isStrictProcessMode) {
            $uniqueKey = array($product->getId());
            $selectionIds = array();

            /**
             * Provided by Wizkunde_ConfigurableGrid
             * 0 = dont do anything with the qty, because of the typecast, this is the default, even if the module does not exist
             * 1 = the qty of simple products, is the same as the totals of the first grid
             */
            $gridQtyAccumulate = (int) $product->getBundleGridAccumulate();

            /**
             * Provided by Wizkunde_ConfigurableGrid
             * 0 = when the above option is 1, the qty of simple products, is the same as the totals of the first grid
             * 1 = when the above option is 1, the qty of simple products is the same as the totals of all the grids
             */
            $gridQtyAccumulateTotals = (int) $product->getBundleGridAccumulateAll();

            // Shuffle selection array by option position
            usort($selections, array($this, 'shakeSelections'));

            // Not doing this, will overwrite the pointer of the foreach on line 264, therefor we clone it
            $selectionsCopy = $selections;

            foreach ($selections as $selection) {
                if ((int)$selection->getSelectionCanChangeQty() > 0 && isset($qtys[$selection->getOptionId()])) {

                    if(is_array($qtys[$selection->getOptionId()])) {
                        $qty = (float)$qtys[$selection->getOptionId()][$selection->getSelectionId()] > 0 ? $qtys[$selection->getOptionId()][$selection->getSelectionId()] : 1;
                    } else {
                        $qty = (float)$qtys[$selection->getOptionId()] > 0 ? $qtys[$selection->getOptionId()] : 1;
                    }
                } else {
                    if($gridQtyAccumulate == 1) {
                        $totalQty = 0;
                        foreach($selectionsCopy as $selection) {
                            // If its an array (grid product), and qty mode = 2 (all grids) or total qty = 0, do your magic
                            if(is_array($qtys[$selectionsCopy->getOptionId()]) && ($gridQtyAccumulateTotals == 1 || $totalQty == 0)) {
                                foreach($qtys[$selectionsCopy->getOptionId()] as $selectionId => $amount) {
                                    $totalQty += (float) $amount;
                                }
                            }
                        }
                        $qty = (float)$totalQty > 0 ? $totalQty : 1;

                    } else {
                        $qty = (float)$selection->getSelectionQty() ? $selection->getSelectionQty() : 1;
                    }
                }

                $qty = (float)$qty;

                $product->addCustomOption('selection_qty_' . $selection->getSelectionId(), $qty, $selection);
                $selection->addCustomOption('selection_id', $selection->getSelectionId());

                $beforeQty = 0;
                $customOption = $product->getCustomOption('product_qty_' . $selection->getId());
                if ($customOption) {
                    $beforeQty = (float)$customOption->getValue();
                }
                $product->addCustomOption('product_qty_' . $selection->getId(), $qty + $beforeQty, $selection);

                /*
                 * Create extra attributes that will be converted to product options in order item
                 * for selection (not for all bundle)
                 */
                $price = $product->getPriceModel()->getSelectionFinalTotalPrice($product, $selection, 0, $qty);
                $attributes = array(
                    'price'         => Mage::app()->getStore()->convertPrice($price),
                    'qty'           => $qty,
                    'option_label'  => $selection->getOption()->getTitle(),
                    'option_id'     => $selection->getOption()->getId()
                );

                $_result = $selection->getTypeInstance(true)->prepareForCart($buyRequest, $selection);
                if (is_string($_result) && !is_array($_result)) {
                    return $_result;
                }

                if (!isset($_result[0])) {
                    return Mage::helper('checkout')->__('Cannot add item to the shopping cart.');
                }

                $result[] = $_result[0]->setParentProductId($product->getId())
                    ->addCustomOption('bundle_option_ids', serialize(array_map('intval', $optionIds)))
                    ->addCustomOption('bundle_selection_attributes', serialize($attributes));

                if ($isStrictProcessMode) {
                    $_result[0]->setCartQty($qty);
                }

                $selectionIds[] = $_result[0]->getSelectionId();
                $uniqueKey[] = $_result[0]->getSelectionId();
                $uniqueKey[] = $qty;
            }

            // "unique" key for bundle selection and add it to selections and bundle for selections
            $uniqueKey = implode('_', $uniqueKey);
            foreach ($result as $item) {
                $item->addCustomOption('bundle_identity', $uniqueKey);
            }
            $product->addCustomOption('bundle_option_ids', serialize(array_map('intval', $optionIds)));
            $product->addCustomOption('bundle_selection_ids', serialize($selectionIds));
            $product->addCustomOption('bundle_simple_custom_options',serialize($buyRequest->getBundleSimpleCustomOptions()));

            return $result;
        }

        return $this->getSpecifyOptionMessage();
    }

    protected function bundlePrepareProduct(Varien_Object $buyRequest, $product, $processMode)
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
