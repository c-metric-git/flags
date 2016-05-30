<?php

require_once 'Mage/Checkout/controllers/CartController.php';

class Evince_Ajaxaddtocart_Checkout_CartController extends Mage_Checkout_CartController {

    public function addAction() {

       $request_group_product_id=$this->getRequest()->getParam('product');
       if( Mage::getModel('catalog/product')->load($request_group_product_id)->getTypeID() == "grouped") {  
                $cart = Mage::getModel('checkout/cart')->getQuote();    
                $request_group_product_quantity=$this->getRequest()->getParam('qty');
                $group_max_quantity=$this->getRequest()->getParam('group_max_quantity');
                
                $cart_group_associated_item_qty=0;
                foreach ($cart->getAllVisibleItems() as $item){
                  
                   $cart_items_options=Mage::getModel('sales/quote_item_option')->getCollection()->addFieldToFilter('item_id',$item->getItemId())->addFieldToFilter('value','grouped');
                   foreach($cart_items_options as $cart_items_option )
                   {
                      if ($request_group_product_id == $cart_items_option->getProductId()) 
                      {
                          $cart_group_associated_item_qty=$item->getQty();
                          break;
                      }
                   }
                }

               $total_qty=$cart_group_associated_item_qty + $request_group_product_quantity ; 
               if($total_qty > $group_max_quantity)
               {

                    $_response = Mage::getModel('ajaxaddtocart/response');
                    $_response->setQtyerror(true);
                    $_response->setMessage($this->__('Item can not be add to shopping cart.'));
                    $_response->send();
                    return false; 
               }
         }      

        $cart = $this->_getCart();
        $params = $this->getRequest()->getParams();
        if(Mage::getModel('catalog/product')->load($request_group_product_id)->getTypeID() == 'bundle')
        {
            foreach($params['bundle_option'] as $key => $bundleoptoins)
            {
                if(is_array($bundleoptoins)){
                 $params['bundle_option'][$key]=explode(',',$bundleoptoins[0]);   
                } 
            }
        }
        try {
            if (isset($params['qty'])) {
                $filter = new Zend_Filter_LocalizedToNormalized(
                                array('locale' => Mage::app()->getLocale()->getLocaleCode())
                );
                $params['qty'] = $filter->filter($params['qty']);
            }

            $product = $this->_initProduct();
            $related = $this->getRequest()->getParam('related_product');

         
            if (!$product) {
                $this->_goBack();
                return;
            }

            $cart->addProduct($product, $params);
            if (!empty($related)) {
                $cart->addProductsByIds(explode(',', $related));
            }

            $cart->save();

            $this->_getSession()->setCartWasUpdated(true);

            $this->getLayout()->getUpdate()->addHandle('ajaxaddtocart');
            $this->loadLayout();

            Mage::dispatchEvent('checkout_cart_add_product_complete', array('product' => $product, 'request' => $this->getRequest(), 'response' => $this->getResponse())
            );

            if (!$this->_getSession()->getNoCartRedirect(true)) {
                if (!$cart->getQuote()->getHasError()) {
                    $message = $this->__('%s was added to your shopping cart.', Mage::helper('core')->escapeHtml($product->getName()));
                    $this->_getSession()->addSuccess($message);
                }
                $this->_goBack();
            }
        } catch (Mage_Core_Exception $e) {
            $_response = Mage::getModel('ajaxaddtocart/response');
            $_response->setError(true);

            $messages = array_unique(explode("\n", $e->getMessage()));
            $json_messages = array();
            foreach ($messages as $message) {
                $json_messages[] = Mage::helper('core')->escapeHtml($message);
            }

            $_response->setMessages($json_messages);

            $url = $this->_getSession()->getRedirectUrl(true);

            $_response->send();
        } catch (Exception $e) {
            $this->_getSession()->addException($e, $this->__('Item can not be add  to shopping cart.'));
            Mage::logException($e);

            $_response = Mage::getModel('ajaxaddtocart/response');
            $_response->setError(true);
            $_response->setMessage($this->__('Item can not be add  to shopping cart.'));
            $_response->send();
        }
    }

    public function updateItemOptionsAction() {
        $cart = $this->_getCart();
        $id = (int) $this->getRequest()->getParam('id');
        $params = $this->getRequest()->getParams();

        if (!isset($params['options'])) {
            $params['options'] = array();
        }
        try {
            if (isset($params['qty'])) {
                $filter = new Zend_Filter_LocalizedToNormalized(
                                array('locale' => Mage::app()->getLocale()->getLocaleCode())
                );
                $params['qty'] = $filter->filter($params['qty']);
            }

            $quoteItem = $cart->getQuote()->getItemById($id);
            if (!$quoteItem) {
                Mage::throwException($this->__('Quote item is not found.'));
            }

            $item = $cart->updateItem($id, new Varien_Object($params));
            if (is_string($item)) {
                Mage::throwException($item);
            }
            if ($item->getHasError()) {
                Mage::throwException($item->getMessage());
            }

            $related = $this->getRequest()->getParam('related_product');
            if (!empty($related)) {
                $cart->addProductsByIds(explode(',', $related));
            }

            $cart->save();

            $this->_getSession()->setCartWasUpdated(true);

            $this->getLayout()->getUpdate()->addHandle('ajaxaddtocart');
            $this->loadLayout();

            Mage::dispatchEvent('checkout_cart_update_item_complete', array('item' => $item, 'request' => $this->getRequest(), 'response' => $this->getResponse())
            );
            if (!$this->_getSession()->getNoCartRedirect(true)) {
                if (!$cart->getQuote()->getHasError()) {
                    $message = $this->__('%s was updated in your shopping cart.', Mage::helper('core')->htmlEscape($item->getProduct()->getName()));
                    $this->_getSession()->addSuccess($message);
                }
                $this->_goBack();
            }
        } catch (Mage_Core_Exception $e) {
            $_response = Mage::getModel('ajaxaddtocart/response');
            $_response->setError(true);

            $messages = array_unique(explode("\n", $e->getMessage()));
            $json_messages = array();
            foreach ($messages as $message) {
                $json_messages[] = Mage::helper('core')->escapeHtml($message);
            }

            $_response->setMessages($json_messages);

            $url = $this->_getSession()->getRedirectUrl(true);

            $_response->send();
        } catch (Exception $e) {
            $this->_getSession()->addException($e, $this->__('Cannot update the item.'));
            Mage::logException($e);

            $_response = Mage::getModel('ajaxaddtocart/response');
            $_response->setError(true);
            $_response->setMessage($this->__('Cannot update the item.'));
            $_response->send();
        }
    }

    public function deleteAction() {
        $id = (int) $this->getRequest()->getParam('id');
        if ($id) {
            try {
				$delitem = $this->_getCart()->getQuote()->getItemById($id);
				$productsku = $delitem->getSku();
                $this->_getCart()->removeItem($id)
                        ->save();
            } catch (Exception $e) {
                $_response = Mage::getModel('ajaxaddtocart/response');
                $_response->setError(true);
                $_response->setMessage($this->__('Cannot remove the item.'));
                $_response->send();

                Mage::logException($e);
            }
        }

       
        $_response = Mage::getModel('ajaxaddtocart/response');
    
        $_response->setMessage($this->__('Item was Successfully removed.'));
        $_response->setSku($productsku);
        
        $this->getLayout()->getUpdate()->addHandle('ajaxaddtocart');
        $this->loadLayout();

        $url = Mage::getSingleton('core/url')->parseUrl(Mage::app()->getRequest()->getServer('HTTP_REFERER'));
        $path = $url->getPath();
        if( ($this->_getCart()->getItemsCount() == 0 ) &&  ( strpos($path, 'checkout') !== false  ||  strpos($path, 'onestepcheckout') !== false ) )
        {
           $_response->setRedirectUrl(Mage::helper('checkout/cart')->getCartUrl());
        }
       

       
        $_response->addUpdatedBlocks($_response);

        $_response->send();
    }

}