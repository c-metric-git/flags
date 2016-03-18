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

class Mage_GoogleTrustedStore_Model_GoogleShoppingAdapter
{
    /**
     * @return Mage_GoogleShopping_Model_Config
     */
    protected function _getConfig()
    {
        return Mage::getSingleton('googleshopping/config');
    }

    /**
     * @throws RuntimeException if module is not active
     */
    protected function _checkIsActive()
    {
        if (!$this->isActive()) {
            throw new RuntimeException('You can use this method only if Mage_GoogleShopping is active.');
        }
    }

    /**
     * If Mage_GoogleShopping is active returns true
     *
     * @return bool
     */
    public function isActive()
    {
        return Mage::getConfig()->getModuleConfig('Mage_GoogleShopping')->is('active');
    }

    /**
     * Returns Google merchant account ID
     * If Mage_GoogleShopping active returns ID from it's config; returns value from own config
     * otherwise.
     *
     * @param integer Store ID
     * @return string ID of Google merchant account; null for current store
     */
    public function getAccountId($storeId = null)
    {
        return $this->isActive()
            ? $this->_getConfig()->getAccountId($storeId)
            : Mage::getSingleton('googletrustedstore/config')->getGoogleShoppingAccountId();
    }

    /**
     * Returns ISO code of target countryof store
     *
     * @param integer $storeId store ID; null for current store
     * @return string ISO code
     * @throws RuntimeException If Mage_GoogleShopping is disabled
     */
    public function getTargetCountry($storeId = null)
    {
        $this->_checkIsActive();

        return $this->_getConfig()->getTargetCountry($storeId);
    }

    /**
     * Returns language's ISO code of target country
     *
     * @param integer $storeId Store ID null for current store
     * @return string ISO code
     * @throws RuntimeException If Mage_GoogleShopping is disabled
     */
    public function getTargetLanguage($storeId = null)
    {
        $this->_checkIsActive();

        return $this->_getConfig()->getCountryInfo($this->getTargetCountry($storeId), 'language', $storeId);
    }

    /**
     * Return Google shopping item ID
     *
     * @param Mage_Catalog_Model_Product $product
     * @param integer $storeId ID of store in which product was published; null for current store
     * @return string|null ID or null if no such item
     * @throws RuntimeException If Mage_GoogleShopping is disabled
     */
    public function getItemId(Mage_Catalog_Model_Product $product, $storeId = null)
    {
        $this->_checkIsActive();
        if ($storeId === null) {
            $storeId = Mage::app()->getStore()->getId();
        }

        return Mage::getModel('googleshopping/item')
            ->loadByProduct($product->setStoreId($storeId))
            ->getGcontentItemId();
    }
}