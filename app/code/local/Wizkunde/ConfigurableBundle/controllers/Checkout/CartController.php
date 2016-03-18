<?php

require_once("Mage/Checkout/controllers/CartController.php");
class Wizkunde_ConfigurableBundle_Checkout_CartController extends Mage_Checkout_CartController
{
    /**
     * If configurable grid is enabled, alter the adding of the products to the cart!
     *
     * @return Mage_Core_Controller_Varien_Action
     */
    public function addAction()
    {
        if (!$this->_validateFormKey()) {
            $this->_goBack();
            return;
        }

        $currentProduct = $this->_initProduct();

        /**
         * Check if we need to handle the grid product
         */
        if($this->isGridProduct($currentProduct) == false) {
            Mage::helper('configurablebundle/bundle')->addToCart($currentProduct, $this->getResponse());
        }

        // If its a configurable with a grid, add it to the cart
        if($currentProduct->isConfigurable() == true && Mage::helper('configurablegrid/grid')->gridIsEnabled($currentProduct) == true) {
            Mage::helper('configurablegrid/cart')->addGridConfigurableToCart($currentProduct, $this->getResponse());
        }

        // If its a bundle product with grid setup, add it to the cart
        if($currentProduct->getTypeId() == 'bundle' && Mage::helper('configurablegrid/grid')->bundleHasGridEnabled($currentProduct) == true) {
            Mage::helper('configurablegrid/cart')->addGridBundleToCart($currentProduct, $this->getResponse());
        }

        $cart   = $this->_getCart();
        if (!$this->_getSession()->getNoCartRedirect(true)) {
            if (!$cart->getQuote()->getHasError()) {
                $this->_getSession()->addSuccess($this->__('Selected products were added to your shopping cart.'));
            }
            $this->_goBack();
        }
    }

    /**
     * Test if we need to return the parent addAction instead of handle this here
     *
     * @param $product
     */
    protected function isGridProduct($product) {
        // If the module is disabled or its not a configurable or bundled product, just go to the parent
        if(Mage::helper('core')->isModuleEnabled('Wizkunde_ConfigurableGrid') == true) {
            if($product->isConfigurable() == true &&  Mage::helper('configurablegrid/grid')->gridIsEnabled($product) == true) {
                return true;
            } else if($product->getTypeId() == 'bundle' && Mage::helper('configurablegrid/grid')->bundleHasGridEnabled($product) == true) {
                return true;
            }
        }

        return false;
    }
}