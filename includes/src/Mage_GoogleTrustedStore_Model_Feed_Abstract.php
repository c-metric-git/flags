<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_GoogleTrustedStore
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Base abstract class for feeds
 *
 */
abstract class Mage_GoogleTrustedStore_Model_Feed_Abstract
{
    /**
     * Columns delimiter
     *
     * @var string
     */
    const DLM_COLUMN = "\t";

    /**
     * Rows delimiter
     *
     * @var string
     */
    const DLM_ROW = "\n";

    /**
     * Column names of header
     *
     * @var array|null
     */
    private $_header;

    /**
     * Rows
     *
     * @var array
     */
    private $_data = array();

    /**
     * Defines header of feed
     *
     * @param array $columnNames
     */
    protected function _setHeader(array $columnNames)
    {
        $this->_header = $columnNames;
    }

    /**
     * Adds row to feed
     *
     * @param array $fields    Field data
     * @throws RuntimeException If header was not initialized before
     * @throws InvalidArgumentException If size of header does not match size of data
     */
    protected function _addRow(array $fields)
    {
        if (!is_array($this->_header)) {
            throw new RuntimeException('Header is not initialized; define it with _setHeader method first.');
        }
        if (count($fields) != count($this->_header)) {
            throw new InvalidArgumentException('Size of row does not match size of header.');
        }

        $this->_data[] = $fields;
    }

    /**
     * Return string representation of feed
     *
     * @param bool $withHeader add header or not
     */
    final public function toString($withHeader = true)
    {
        $str = ($withHeader && $this->_header)
            ? implode(self::DLM_COLUMN, $this->_header) . self::DLM_ROW
            : '';

        foreach ($this->_data as $row) {
            $str .= implode(self::DLM_COLUMN, $row) . self::DLM_ROW;
        }

        return $str;
    }
}