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
class RocketWeb_GoogleBaseFeedGenerator_Model_Map_Product_Associated_Apparel extends RocketWeb_GoogleBaseFeedGenerator_Model_Map_Product_Simple_Apparel
{

    /**
     * Overriden here because subclass implements custom skip code, which we don't want in this class
     *
     * @param  $rows
     * @return array $rows
     */
    public function _afterMap($rows)
    {
        $this->_checkEmptyColumns($rows);
        $this->_cache_map_values = array();
        return $rows;
    }

    /**
     * @param array $params
     * @return mixed|string
     */
    public function mapDirectiveUrl($params = array())
    {
        $args = array('map' => $params['map']);
        $product = $this->getProduct();

        switch ($this->getConfigVar('associated_products_link', 'configurable_products')) {
            case RocketWeb_GoogleBaseFeedGenerator_Model_Source_Assocprodslink::FROM_PARENT:
                $value = $this->getParentMap()->mapColumn($args['map']['column']);
                break;
            case RocketWeb_GoogleBaseFeedGenerator_Model_Source_Assocprodslink::FROM_ASSOCIATED_PARENT:
                if ($product->isVisibleInSiteVisibility()) {
                    return parent::mapDirectiveUrl($params);
                } else {
                    $value = $this->getParentMap()->mapColumn($args['map']['column']);
                }
                break;

            default:
                $value = $this->getParentMap()->mapColumn($args['map']['column']);
        }

        $typeId = $this->getParentMap()->getProduct()->getTypeId();
        $linkAddUnique = $this->getConfigVar('associated_products_link_add_unique', 'apparel') && $this->getParentMap()
            && ($typeId == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE || $typeId == Mage_Catalog_Model_Product_Type::TYPE_BUNDLE);

        if ($linkAddUnique) {
            $value = $this->addUrlUniqueParams($value, $this->getProduct(), $this->getParentMap()->getOptionCodes(), $typeId);
        }

        return $value;
    }

    /**
     * @param null $product
     * @return mixed
     */
    public function getPrice($product = null)
    {
        if (is_null($product)) {
            $product = $this->getProduct();
        }

        if (Mage::helper('googlebasefeedgenerator')->isModuleEnabled('Aitoc_Aitcbp')) {
            $product = $product->load($product->getid());
        }

        return $this->getParentMap()->getCacheAssociatedPrice($product->getId());
    }

    /**
     * @param array $params
     * @return mixed
     */
    public function mapColumnAvailability($params = array())
    {
        $args = array('map' => $params['map']);
        $value = $this->hasParentMap() ? $this->getParentMap()->mapColumn('availability') : '';

        // Gets out of stock if parent is out of stock
        if ($this->getConfigVar('inherit_parent_out_of_stock', 'configurable_products') && strcasecmp($this->getConfig()->getOutOfStockStatus(), $value) == 0) {
            return $value;
        }

        return $this->getCellValue($args);
    }

    /**
     * @param array $params
     * @return mixed
     */
    public function mapColumnBrand($params = array())
    {
        $args = array('map' => $params['map']);

        // get value from parent first
        $value = $this->hasParentMap() ? $this->getParentMap()->mapColumn('brand') : '';
        if (empty($value)) {
            $value = $this->getCellValue($args);
        }

        return $value;
    }

    /**
     * @param array $params
     * @return string
     */
    public function mapDirectiveGoogleCategoryByCategory($params = array())
    {
        // try to get value from parent first
        $value = $this->hasParentMap() ? $this->getParentMap()->mapDirectiveGoogleCategoryByCategory($params) : '';
        if (empty($value)) {
            $value = parent::mapDirectiveGoogleCategoryByCategory($params);
        }
        return $value;
    }

    /**
     * @param array $params
     * @return string
     */
    public function mapDirectiveProductTypeByCategory($params = array())
    {
        // try to get value from parent first
        $value = $this->hasParentMap() ? $this->getParentMap()->mapDirectiveProductTypeByCategory($params) : '';
        if (empty($value)) {
            $value = parent::mapDirectiveProductTypeByCategory($params);
        }
        return $value;
    }

    /**
     * @param $params
     * @param $attributes_codes
     * @return string
     */
    public function mapDirectiveVariantAttributes($params = array())
    {
        // try to get value from parent first
        $value = $this->hasParentMap() ? $this->getParentMap()->mapDirectiveVariantAttributes($params) : '';
        if (empty($value)) {
            $value = parent::mapDirectiveVariantAttributes($params);
        }
        return $value;
    }
}
