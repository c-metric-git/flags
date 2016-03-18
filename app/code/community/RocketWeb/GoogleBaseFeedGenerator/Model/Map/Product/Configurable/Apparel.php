<?php

/**
 * RocketWeb
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category  RocketWeb
 * @package   RocketWeb_GoogleBaseFeedGenerator
 * @copyright Copyright (c) 2012 RocketWeb (http://rocketweb.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author    RocketWeb
 */
class RocketWeb_GoogleBaseFeedGenerator_Model_Map_Product_Configurable_Apparel extends RocketWeb_GoogleBaseFeedGenerator_Model_Map_Product_Simple_Apparel
{

    protected $_assoc_ids;
    protected $_assocs;
    protected $_cache_configurable_attribute_codes;

    public function initialize()
    {
        parent::initialize();
        $this->setApparelCategories();
    }

    /**
     * Moved away from _beforeMap and _map to eliminate memory consumed for passing
     * assocMaps object and $assoc product objects as $this properties. This has a huge impact on
     * memory used by configurable products with large number of associated items
     *
     * @return array
     */
    public function map()
    {
        $rows = array();
        $parentRow = null;
        $this->_assocs = array();

        $stockStatusFlag = false;
        $stockStatus = false;
        $assocIds = $this->getAssocIds();

        foreach ($assocIds as $assocId) {

            $is_skip = false;
            $assoc = Mage::getModel('catalog/product');
            $assoc->setStoreId($this->getStoreId());
            $assoc->getResource()->load($assoc, $assocId);

            if ($this->getGenerator()->getData('verbose')) {
                echo $this->getGenerator()->formatMemory(memory_get_usage(true)) . " - Configurable apparel associated SKU " . $assoc->getSku() . ", ID " . $assoc->getId() . "\n";
            }

            $stockItem = Mage::getModel('cataloginventory/stock_item');
            $stockItem->setStoreId($this->getStoreId());
            $stockItem->getResource()->loadByProductId($stockItem, $assoc->getId());
            $stockItem->setOrigData();

            if ($stockItem->getId() && $stockItem->getIsInStock()) {
                $assoc->setData('quantity', $stockItem->getQty());
                $stock = $this->getConfig()->getInStockStatus();
            } else {
                $assoc->setData('quantity', 0);
                $stock = $this->getConfig()->getOutOfStockStatus();
            }

            // Skip assoc considering the appropriate stock status
            if (!$this->getConfigVar('add_out_of_stock', 'configurable_products') && $stock != $this->getConfig()->getInStockStatus()) {
                $is_skip = true;
                if ($this->getConfigVar('log_skip')) {
                    $this->log(sprintf("product id %d sku %s, configurable item, skipped - out of stock", $assocId, $assoc->getSku()));
                }
            }

            if (!$is_skip) {
                $this->_assocs[$assocId] = $assoc;
            }

            // Set stock status of the current item and check if the status has changed
            if ($stockStatus != false && $stock != $stockStatus) {
                $stockStatusFlag = true;
            }
            $stockStatus = $stock;
        }

        if ($this->getConfig()->isAllowApparelConfigurableMode($this->getStoreId())) {
            if (!$this->isSkip()) {
                $data = $this->getData();
                $data['is_apparel'] = false;

                $pMap = $this->getGenerator()->getProductMapModel($this->getProduct()->getTypeId())
                    ->setData($data)
                    ->setSkipAssocs(true)
                    ->setIsApparel(true)
                    ->setColumnsMap($this->_columns_map)
                    ->setEmptyColumnsReplaceMap($this->getEmptyColumnsReplaceMap());

                // Set configurable stock status if all assocs have the same stock status
                if ($stockStatus && !$stockStatusFlag) {
                    $pMap->setAssociatedStockStatus($stockStatus);
                    if ($stockStatus == $this->getConfig()->getOutOfStockStatus() && !$this->getConfigVar('add_out_of_stock', 'filters')) {
                        $this->setSkip(sprintf("product id %d sku %s, configurable apparel, skipped - out of stock.", $this->getProduct()->getId(), $this->getProduct()->getSku()));
                    }
                }

                if (!$this->isSkip()) {
                    $row = $pMap->map();
                    if (count($row)) {
                        reset($row);
                        $row = $this->formOtherConfigurableNonVariant($row);
                        $parentRow = current($row);
                    }
                }
            }
        }

        // Start BeforeMap
        $this->flushCacheAssociatedPrice();

        foreach ($this->_assocs as $assoc) {
            $assocId = $assoc->getId();
            $assocSku = $assoc->getSku();

            if (!$this->getConfigVar('add_out_of_stock', 'configurable_products') && !$assoc->getData('quantity')) {
                if ($this->getConfigVar('log_skip')) {
                    $this->log(sprintf("product id %d sku %s, configurable apparel item, skipped - out of stock", $assocId, $assocSku));
                }
                unset($assoc);
                continue;
            }

            if (!($this->setCacheAssociatedPricesByProduct($assoc) === true)) {
                if ($this->getConfigVar('log_skip')) {
                    $this->log(sprintf("product id %d sku %s, configurable apparel item, skipped - could not set price cache", $assocId, $assocSku));
                }
                unset($assoc);
                continue;
            }

            $assocMap = $this->getAssocMapModel($assoc);
            if ($assocMap->checkSkipSubmission()->isSkip()) {
                continue;
            }

            // Start Map
            $row = $assocMap->map();

            if (!$assocMap->isSkip()) {
                reset($row);
                $row = current($row);

                if (!Mage::getSingleton('googlebasefeedgenerator/config')->isSimplePricingEnabled($this->getData('store_id'))) {
                    // Overwrite price with configurable price if no option price set
                    if ($assocMap->getProduct()->hasOptionPrice()
                        && !$assocMap->getProduct()->getOptionPrice() && $parentRow
                    ) {
                        $row['price'] = $parentRow['price'];
                    }
                }

                if (!$assocMap->isSkip()) {
                    $rows[] = $row;
                }

            }
        }
        // End BeforeMap

        // Fill in parent columns specified in $inherit_columns with values list from associated items
        if ($parentRow && count($parentRow)) {
            $this->mergeVariantValuesToParent($parentRow, $rows);
            array_unshift($rows, $parentRow);
        }

        return $this->_afterMap($rows);
    }

    /**
     * Array with associated products ids in current store.
     *
     * @return array
     */
    public function getAssocIds()
    {
        if (is_null($this->_assoc_ids)) {
            $this->_assoc_ids = $this->loadAssocIds($this->getProduct(), $this->getStoreId());
        }
        return $this->_assoc_ids;
    }

    /**
     * @param $rows
     * @return $this
     */
    public function _afterMap($rows)
    {
        // Free some memory
        foreach ($this->_assocs as $assoc) {
            if ($assoc->getEntityid()) {
                $this->getTools()->clearNestedObject($assoc);
            }
        }
        $this->flushCacheAssociatedPrice();

        return parent::_afterMap($rows);
    }

    /**
     * @param Mage_Catalog_Model_Product $product
     * @return RocketWeb_GoogleBaseFeedGenerator_Model_Map_Product_Abstract
     */
    protected function getAssocMapModel($product)
    {
        $params = array(
            'store_code' => $this->getData('store_code'),
            'store_id' => $this->getData('store_id'),
            'website_id' => $this->getData('website_id'),
        );

        $productMap = Mage::getModel('googlebasefeedgenerator/map_product_associated_apparel', $params);
        $productMap->setProduct($product)
            ->setColumnsMap($this->_columns_map)
            ->setEmptyColumnsReplaceMap($this->getEmptyColumnsReplaceMap())
            ->setParentMap($this)
            ->setCacheAssociatedPrices($this->getCacheAssociatedPrices())
            ->initialize();

        return $productMap;
    }

    /**
     * @param array $params
     * @return string
     */
    protected function mapDirectiveShippingWeight($params = array())
    {
        $map = $params['map'];
        $map['attribute'] = 'weight';
        $unit = $map['param'];

        // @var $product Mage_Catalog_Model_Product
        $product = $this->getProduct();

        // Get attribute value
        $weight_attribute = $this->getGenerator()->getAttribute($map['attribute']);
        if ($weight_attribute === false) {
            Mage::throwException(sprintf('Couldn\'t find attribute \'%s\'.', $map['attribute']));
        }

        $weight = $this->getAttributeValue($product, $weight_attribute);
        if ($weight != "") {
            $weight = number_format((float)$weight, 2). ' '. $unit;
        }

        // Configurable doesn't have weight of it's own.
        if ($weight == "") {
            $min_price = PHP_INT_MAX;
            foreach ($this->_assocs as $assoc) {
                if ($this->getCacheAssociatedPrice($assoc->getId()) !== false && $min_price > $this->getCacheAssociatedPrice($assoc->getId())) {
                    $weight = $this->getAttributeValue($assoc, $weight_attribute);
                    break;
                }
            }
        }

        if ($weight != "") {
            if (strpos($weight, $unit) === false) {
                $weight = number_format((float)$weight, 2). ' '. $unit;
            }

        }

        return $this->cleanField($weight, $params);
    }

    /**
     * @param $rows
     * @return array
     */
    protected function formOtherConfigurableNonVariant($rows)
    {
        reset($rows);
        $fields = current($rows);

        // compact apparel fields
        $varies = array($this->_color_column_name, 'size', 'material', 'pattern', 'gender', 'age_group', 'size_type', 'size_system');
        foreach ($varies as $column) {
            if (isset($fields[$column])) {
                $values = array();
                if ($fields[$column] != "") {
                    $arr = explode(",", $fields[$column]);
                    foreach ($arr as $v) {
                        $values[trim($v)] = trim($v);
                    }
                }
                $fields[$column] = implode(",", $values);
            }
        }

        return array($fields);
    }

    /**
     * Redundant code with RocketWeb_GoogleBaseFeedGenerator_Model_Map_Product_Configurable
     *
     * @return float
     */
    public function getPrice($product = null)
    {
        if (is_null($product)) {
            $product = $this->getProduct();
        }

        if (Mage::helper('googlebasefeedgenerator')->isModuleEnabled('Aitoc_Aitcbp')) {
            $product = $product->load($product->getid());
        }

        $price = $product->getPrice();

        if ($this->getGenerator()->getConfig()->isSimplePricingEnabled($this->getGenerator()->getStoreId())) {
            $price = $this->calcMinimalPrice($product);
        }

        return $price;
    }

    /**
     * Wrapper to get special price.
     * Redundant code with RocketWeb_GoogleBaseFeedGenerator_Model_Map_Product_Configurable
     *
     * @param  Mage_Catalog_Model_Product $product
     * @return float
     */
    public function getSpecialPrice($product = null)
    {
        if ($this->getGenerator()->getConfig()->isSimplePricingEnabled($this->getGenerator()->getStoreId())) {
            //if simple pricing is on, we need to take the special price from the same product we used to take the price
            $priceSource = $this->getPriceSourceMap();
            if (!empty($priceSource)) {
                return $priceSource->getSpecialPrice();
            }
        }
        else {
            return parent::getSpecialPrice($product);
        }
    }

    /**
     * Redundant code with RocketWeb_GoogleBaseFeedGenerator_Model_Map_Product_Configurable
     *
     * @param array $params
     * @return string
     */
    protected function mapDirectiveSalePriceEffectiveDate($params = array()) {
        if ($this->getGenerator()->getConfig()->isSimplePricingEnabled($this->getGenerator()->getStoreId())) {
            //if simple pricing is on, we need to take the dates from the same product we used to take the price
            $priceSource = $this->getPriceSourceMap();
            if (!empty($priceSource)) {
                return $priceSource->mapDirectiveSalePriceEffectiveDate($params);
            }
        }
        else {
            return parent::mapDirectiveSalePriceEffectiveDate($params);
        }
    }

    /**
     * Redundant code with RocketWeb_GoogleBaseFeedGenerator_Model_Map_Product_Configurable
     *
     * @return mixed
     */
    public function getPriceSourceMap() {
        if (!isset($this->_price_source_map)) {
            $prices = $this->getCacheAssociatedPrices();
            $min_prices = array_keys($prices, min($prices));
            $assoc_id = end($min_prices);

            $assoc_maps = $this->getAssocMaps();
            $this->_price_source_map = $assoc_maps[$assoc_id];
        }
        return $this->_price_source_map;
    }

    /**
     * Redundant code with RocketWeb_GoogleBaseFeedGenerator_Model_Map_Product_Configurable
     *
     * @return float
     */
    public function calcMinimalPrice($product)
    {
        $price = 0.0;
        $minimal_price = PHP_INT_MAX;
        foreach ($this->_assocs as $assoc) {
            if ($minimal_price > $this->getCacheAssociatedPrice($assoc->getId())) {
                $minimal_price = $this->getCacheAssociatedPrice($assoc->getId());
            }
        }
        if ($minimal_price < PHP_INT_MAX) {
            $price = $minimal_price;
        }

        return $price;
    }

    /**
     * @param array $params
     * @return string
     */
    protected function mapDirectiveAvailability($params = array())
    {
        // Set the computed configurable stock status
        if ($this->hasAssociatedStockStatus() && $this->getAssociatedStockStatus() == $this->getConfig()->getOutOfStockStatus()) {
            return $this->cleanField($this->getAssociatedStockStatus(), $params);
        }

        return parent::mapDirectiveAvailability($params);
    }

    /**
     * @param null $assoc_id
     * @return array
     */
    public function getOptionCodes($assoc_id = null)
    {
        if (is_null($this->_cache_configurable_attribute_codes)) {
            $this->_cache_configurable_attribute_codes = $this->getTools()
                ->getOptionCodes($this->getProduct()->getId());
        }
        return $this->_cache_configurable_attribute_codes;
    }
}
