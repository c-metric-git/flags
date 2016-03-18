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
class RocketWeb_GoogleBaseFeedGenerator_Model_Map_Product_Simple_Apparel extends RocketWeb_GoogleBaseFeedGenerator_Model_Map_Product_Simple
{

    /**
     * @return $this|void
     */
    public function initialize()
    {
        parent::initialize();
        $this->setApparelCategories();
    }

    /**
     * @return array
     */
    public function map()
    {
        $rows = parent::map();
        reset($rows);
        return $rows;
    }

    /**
     * @param $rows
     * @return $this
     */
    public function _afterMap($rows)
    {
        reset($rows);
        $fields = current($rows);

        if (!$this->getConfigVar('allow_empty_color_size', 'apparel')) {
            if (!$this->checkColorSizeRequired($fields)) {
                $this->setSkip(sprintf("product id %d product sku %s, skipped - apparel product non variant, color column is empty.", $this->getProduct()->getId(), $this->getProduct()->getSku()));
            }
        }

        return parent::_afterMap($rows);
    }

    /**
     * This function has been converted from 'US Feed' to 'Allow Apparel without Color or Size'
     *
     * @param  $fields
     * @return bool
     */
    protected function checkColorSizeRequired($fields)
    {
        foreach ($fields as $k => $v) {
            $fields[$k] = trim($v);
        }
        $gb_category = $this->mapColumn('google_product_category');

        $ret = true;
        $empties = array();
        $columns = array($this->_color_column_name, 'gender', 'age_group');
        if ($this->getIsApparelClothing() || $this->getIsApparelShoes()) {
            $columns[] = 'size';
        }

        foreach ($columns as $column) {
            if (!isset($fields[$column]) || (isset($fields[$column]) && $fields[$column] == "")) {
                if ($column == 'gender' || $column == 'age_group') {
                    // not required for some subcategories
                    $f = false;
                    foreach ($this->getConfig()->getMultipleSelectVar($column . '_not_req_categories', $this->getStoreId(), 'apparel') as $categ) {
                        if ($this->matchGoogleCategory($gb_category, $categ)) {
                            $f = true;
                        }
                    }
                    if (!$f) {
                        $empties[] = $column;
                        $ret = false;
                    }
                } else {
                    $empties[] = $column;
                    $ret = false;
                }
            }
        }

        return $ret;
    }
}
