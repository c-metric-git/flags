<?php

/**
 * Helper class for transposing bundle information
 *
 * Class Wizkunde_ConfigurableBundle_Helper_Bundle
 */
class Wizkunde_ConfigurableBundle_Helper_Bundle extends Mage_Core_Helper_Abstract {

    /**
     * @param Mage_Catalog_Model_Product $bundleProduct
     *
     * @return array
     */
    public function getProductToSelectionIds($bundleId) {
        $bundleProduct = Mage::getModel('catalog/product')->load($bundleId);
        $selectionCollection = $bundleProduct->getTypeInstance(true)->getSelectionsCollection(
            $bundleProduct->getTypeInstance(true)->getOptionsIds($bundleProduct), $bundleProduct
        );

        $bundledItems = array();
        foreach($selectionCollection as $option)
        {
            $bundledItems[$option->product_id] = $option->selection_id;
        }

        return $bundledItems;
    }

    /**
     * @param Mage_Catalog_Model_Product $bundleProduct
     *
     * @return array
     */
    public function getSelectionToProductIds($bundleId) {
        return array_flip($this->getProductSelectionIds($bundleId));
    }

    /**
     * Get the session
     *
     * @return Mage_Core_Model_Abstract
     */
    protected function getSession()
    {
        return Mage::getSingleton('checkout/session');
    }

    /**
     * Get the cart
     *
     * @return Mage_Core_Model_Abstract
     */
    protected function getCart()
    {
        return Mage::getSingleton('checkout/cart');
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

    /**
     * We need to make sure the proper super attributes are selected in the buy request
     */
    protected function formatConfigurableAttributesInBuyRequest($product)
    {
        $currentAttributes = $this->_getRequest()->getParam('super_attribute');

        foreach($this->_getRequest()->getParam('bundle_option') as $optionIterator => $bundleOption) {
            $realProduct = $this->resolveBundleSelectionId($product->getEntityId(), $bundleOption);
            if(isset($currentAttributes[$optionIterator])) {
                $currentAttributes[$realProduct] = $currentAttributes[$optionIterator];

                // Do not unset option 1 if it happens to be product 1
                if($optionIterator != $realProduct) {
                    unset($currentAttributes[$optionIterator]);
                }
            }
        }

        $this->_getRequest()->setParam('super_attribute', $currentAttributes);
    }

    public function addToCart($product, $response)
    {
        /**
         * Needed to make sure the proper super attributes are selected when adding a bundle to the cart
         */
        $this->formatConfigurableAttributesInBuyRequest($product);


        try {
            if (isset($params['qty'])) {
                $filter = new Zend_Filter_LocalizedToNormalized(
                    array('locale' => Mage::app()->getLocale()->getLocaleCode())
                );
                $params['qty'] = $filter->filter($params['qty']);
            }

            $related = $this->_getRequest()->getParam('related_product') != '' ? $this->_getRequest()->getParam('related_product') : array();

            /**
             * Check product availability
             */
            if (!$product) {
                return false;
            }

            /**
             * Add to cart
             */
            $this->getCart()->addProduct($product, $this->_getRequest()->getParams());

            /**
             * Add related products
             */
            if (!empty($related)) {
                $this->getCart()->addProductsByIds(explode(',', $related));
            }

            $this->getCart()->save();

            $this->getSession()->setCartWasUpdated(true);

            /**
             * @todo remove wishlist observer processAddToCart
             */
            Mage::dispatchEvent('checkout_cart_add_product_complete',
                array('product' => $product, 'request' => $this->_getRequest(), 'response' => $response)
            );

            if (!$this->getSession()->getNoCartRedirect(true)) {
                if (!$this->getCart()->getQuote()->getHasError()) {
                    $message = $this->__('%s was added to your shopping cart.', Mage::helper('core')->escapeHtml($product->getName()));
                    $this->getSession()->addSuccess($message);
                }
                return true;
            }
        } catch (Mage_Core_Exception $e) {
            if ($this->getSession()->getUseNotice(true)) {
                $this->getSession()->addNotice(Mage::helper('core')->escapeHtml($e->getMessage()));
            } else {
                $messages = array_unique(explode("\n", $e->getMessage()));
                foreach ($messages as $message) {
                    $this->getSession()->addError(Mage::helper('core')->escapeHtml($message));
                }
            }

            return false;
        } catch (Exception $e) {
            $this->getSession()->addException($e, $this->__('Cannot add the item to shopping cart.'));
            Mage::logException($e);
            return true;
        }
    }
}