<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at http://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   Reward Points + Referral program
 * @version   1.1.2
 * @build     928
 * @copyright Copyright (C) 2015 Mirasvit (http://mirasvit.com/)
 */


class Mirasvit_Rewards_Model_Observer_Output
{
    /**
     * @param Varien_Event_Observer $obj
     * @return $this
     */
    public function afterOutput($obj)
    {
        $block = $obj->getEvent()->getBlock();
        /** @var Varien_Object $transport */
        /** @noinspection PhpUndefinedMethodInspection */
        $transport = $obj->getEvent()->getTransport();
        if(empty($transport)) { //it does not work for magento 1.4 and older
            return $this;
        }
        $this->appendCartPointsBlock($block, $transport);
        //$this->appendToCatalogListing($block, $transport); //this can produce high server load
        $this->appendAccountPointsSummary($block, $transport);
        $this->appendToIdevOnestepcheckout($block, $transport);
        $this->appendToAppthaOnestepcheckout($block, $transport);
        $this->appendToIWDOnestepcheckout($block, $transport);
        $this->appendToFirecheckout($block, $transport);
        $this->appendToMagegiantCheckout($block, $transport);
        return $this;
    }

    protected $isBlockInserted = false;

    public function appendCartPointsBlock($block, $transport)
    {
        if (Mage::app()->getRequest()->getControllerName() != 'cart') {
            return $this;
        }
        if ($block->getBlockAlias() == 'coupon'
            // || $block->getBlockAlias() == 'shipping' //if we add to shipping block, we have bad CSS styles
            || $block->getBlockAlias() == 'crosssell') {
            $b = $block->getLayout()
                ->createBlock('rewards/checkout_cart_usepoints', 'usepoints')
                ->setTemplate('mst_rewards/checkout/cart/usepoints.phtml');

            $html = $transport->getHtml();
            $ourhtml = $b->toHtml();
            if (!$this->isBlockInserted) {
                $html = $ourhtml . $html;
                $this->isBlockInserted = true;
            }
            $transport->setHtml($html);
        }
        return $this;
    }

    public function appendAccountPointsSummary($block, $transport)
    {
        if ($block->getBlockAlias() == 'top' && $block->getChild('rewards_customer_account_dashboard_top')) {
            $html = $transport->getHtml();
            $ourhtml = $block->getChildHtml('rewards_customer_account_dashboard_top');

            if ($ourhtml && strpos($html, $ourhtml) === false) {
                $html = $ourhtml . $html;
            }
            $transport->setHtml($html);
        }

        return $this;
    }

    public function appendToCatalogListing($block, $transport)
    {
        if (!($block instanceof Mage_Catalog_Block_Product_List)) {
            return $this;
        }
        $productCollection = $block->getLoadedProductCollection();
        $html = $transport->getHtml();
        $html = $this->_addToProductListHtml($productCollection, $block, $html);
        $transport->setHtml($html);
        return $this;

    }

    protected function _addToProductListHtml($productCollection, $block, $html)
    {
        $isListMode = strpos($html, 'class="products-list" id="products-list">') !== false;

        foreach($productCollection as $product) {
            $b = $block->getLayout()
                ->createBlock('rewards/product_list_points', 'rewards_product_list_points')
                ->setTemplate('mst_rewards/product/list/points.phtml');
            $block->insert($b);
            $b->setProduct($product);
            $ourhtml = $b->toHtml();

            if(!$ourhtml) {
                continue;
            }
        }

        return $html;
    }



    public function appendToIdevOnestepcheckout($block, $transport)
    {
        if (!$block instanceof Idev_OneStepCheckout_Block_Checkout) {
            return $this;
        }
        $html = $transport->getHtml();

        if (strpos($html, '<div class="tool-tip" id="payment-tool-tip') !== false) {
            $block = Mage::app()->getLayout()->createBlock('rewards/checkout_cart_usepoints')
                ->setTemplate('mst_rewards/checkout/cart/usepoints_idev_onestepcheckout.phtml');
            $html = str_replace('<div class="tool-tip" id="payment-tool-tip"', $block->toHtml().'<div class="tool-tip" id="payment-tool-tip"', $html);
        }

        $transport->setHtml($html);
        return $this;
    }

    public function appendToMageStoreOnestepcheckout($block, $transport)
    {
        return false;
        if (!$block instanceof Magestore_Onestepcheckout_Block_Onestepcheckout) {
            return $this;
        }
        $html = $transport->getHtml();

        if (strpos($html, '<div class="tool-tip" id="payment-tool-tip') !== false) {
            $block = Mage::app()->getLayout()->createBlock('rewards/checkout_cart_usepoints')
                ->setTemplate('mst_rewards/checkout/cart/usepoints_idev_onestepcheckout.phtml');
            $html = str_replace('<div class="tool-tip" id="payment-tool-tip"', $block->toHtml().'<div class="tool-tip" id="payment-tool-tip"', $html);
        }

        $transport->setHtml($html);
        return $this;
    }

    public function appendToAppthaOnestepcheckout($block, $transport)
    {
        if (!$block instanceof Apptha_Onestepcheckout_Block_Onestepcheckout) {
            return $this;
        }
        $html = $transport->getHtml();

        $anchor = '<li id="column-3" class="firecheckout-section">';
        $pos = strpos($html, $anchor);
        if ($pos !== false) {
            $pos2 = $pos - 15;
            $anchor2 = "</ul>";
            $pos2 = strpos($html, $anchor2, $pos2);
            if ($pos2 !== false) {
                $block = Mage::app()->getLayout()->createBlock('rewards/checkout_cart_usepoints')
                    ->setTemplate('mst_rewards/checkout/cart/usepoints_apptha_onestepcheckout.phtml');
                $anchor3 = substr($html, $pos2, $pos-$pos2+strlen($anchor));
                $html = str_replace($anchor3, $block->toHtml().$anchor3, $html);
            }
        }

        $transport->setHtml($html);
        return $this;
    }

    public function appendToIWDOnestepcheckout($block, $transport){
        if (!$block instanceof IWD_Opc_Block_Wrapper) {
            return $this;
        }
        $block = Mage::app()->getLayout()->createBlock('rewards/checkout_cart_usepoints')
            ->setTemplate('mst_rewards/checkout/cart/usepoints_iwd_onestepcheckout.phtml');
        $html = $transport->getHtml();
        $anchor = '<div class="shipping-block">';
        $html = str_replace($anchor, $block->toHtml() . $anchor, $html);
        $transport->setHtml($html);
        return $this;
    }

    public function appendToFirecheckout($block, $transport){

        if (!$block instanceof TM_FireCheckout_Block_Checkout) {
            return $this;
        }

        $html = $transport->getHtml();

        $anchor = '<div class="col-2">';
        $pos = strpos($html, $anchor);
        if ($pos !== false) {
            $pos2 = $pos - 15;
            $anchor2 = "</div>";
            $pos2 = strpos($html, $anchor2, $pos2);
            if ($pos2 !== false) {
                $block = Mage::app()->getLayout()->createBlock('rewards/checkout_cart_usepoints')
                    ->setTemplate('mst_rewards/checkout/cart/usepoints_firecheckout.phtml');
                $anchor3 = substr($html, $pos2, $pos-$pos2+strlen($anchor));
                $html = str_replace($anchor3, $block->toHtml().$anchor3, $html);
            }
        }
        $transport->setHtml($html);
        return $this;
    }

    /**
     * @param Mage_Core_Block_Template $block
     * @param Varien_Object $transport
     * @return $this
     */
    private function appendToMagegiantCheckout($block, $transport)
    {
        if (!$block instanceof Magegiant_Onestepcheckout_Block_Onestep_Form_Review_Comments) {
            return $this;
        }

        $html = $transport->getHtml();

        $block = Mage::app()->getLayout()->createBlock('rewards/checkout_cart_usepoints')
            ->setTemplate('mst_rewards/checkout/cart/usepoints_magegiant.phtml');
        $anchor = '<div id="giant-onestepcheckout-order-review-comments-wrapper">';
        $html = str_replace($anchor, $block->toHtml() . $anchor, $html);
        $transport->setHtml($html);
        return $this;
    }

}