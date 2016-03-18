<?php
class IWD_OrderManager_Adminhtml_Sales_OrderrController extends IWD_OrderManager_Controller_Abstract
{
    /* edit: edit form */
    public function editOrderedItemsFormAction()
    {
        $result = array('status' => 1);

        try {
            $order_id = $this->getRequest()->getPost('order_id');
            $ordered = Mage::getModel('sales/order')->load($order_id)->getItemsCollection();


            $result['form'] = $this->getLayout()
                ->createBlock('iwd_ordermanager/adminhtml_sales_order_items_form')
                ->setTemplate('iwd/ordermanager/items/form.phtml')
                ->setData('ordered', $ordered)
                ->setData('order_id', $order_id)
                ->toHtml();

        } catch (Exception $e) {
            Mage::log($e->getMessage(), null, 'iwd_order_manager.log');
            $result = array('status' => 0, 'error' => $e->getMessage());
        }

        $this->getResponse()->setHeader('Content-type', 'application/json', true);
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    /* edit: edit ordered items */
    public function editOrderedItemsAction()
    {
        $result = array('status' => 1);

        Mage::dispatchEvent('iwd_ordermanager_update_order_before', array('order_id'=>$this->getRequest()->getParam('order_id', 0)));

        try {
            $params = $this->getRequest()->getParams();
            Mage::getModel('iwd_ordermanager/order_items')->updateOrderItems($params);
        } catch (Exception $e) {
            Mage::log($e->getMessage(), null, 'iwd_order_manager.log');
            $result = array('status' => 0, 'error' => $e->getMessage());
        }

        Mage::dispatchEvent('iwd_ordermanager_update_order_after', array('order_id'=>$this->getRequest()->getParam('order_id', 0)));

        $this->getResponse()->setHeader('Content-type', 'application/json', true);
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    /* add: search items form */
    public function addOrderedItemsFormAction()
    {
        try {
            $order_id = $this->getRequest()->getPost('order_id');
            $order = Mage::getModel('sales/order')->load($order_id);

            $this->_setQuoteSession($order_id);

            $result['form'] = $this->getLayout()
                    ->createBlock('adminhtml/sales_order_create_search_grid')
                    ->setData('order', $order)
                    ->toHtml() . '<div id="order-billing_method"></div><div id="order-shipping_method"></div>';

            $result['configure_form'] = $this->getLayout()
                ->createBlock('adminhtml/catalog_product_composite_configure')
                ->toHtml();

            $result['url_configure_js'] = Mage::helper('core/js')->getJsUrl('mage/adminhtml/product/composite/configure.js');

            $result['status'] = 1;
        } catch (Exception $e) {
            Mage::log($e->getMessage(), null, 'iwd_order_manager.log');
            $result = array('status' => 0, 'error' => $e->getMessage());
        }

        $this->getResponse()->setHeader('Content-type', 'application/json', true);
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    /* add: add new items */
    public function addOrderedItemsAction()
    {
        try {
            $options = $this->getRequest()->getParam('options');
            $options = Mage::helper('core')->jsonDecode($options);

            $items = $this->getRequest()->getParam('items');
            $items = Mage::helper('core')->jsonDecode($items);
            $order_id = $this->getRequest()->getParam('order_id');
            $selected_items = $this->_parseProductsConfig($items, $options);

            $quote_items = Mage::getModel('iwd_ordermanager/order_converter')->createNewQuoteItems($order_id, $selected_items);

            $result['form'] = $this->getLayout()
                ->createBlock('iwd_ordermanager/adminhtml_sales_order_items_form')
                ->setTemplate('iwd/ordermanager/items/new_items.phtml')
                ->setData('items', $quote_items)
                ->setData('order_id', $order_id)
                ->toHtml();

            $result['status'] = 1;
        } catch (Exception $e) {
            Mage::log($e->getMessage(), null, 'iwd_order_manager.log');
            $result = array('status' => 0, 'error_message' => $e->getMessage());
        }
        $this->getResponse()->setHeader('Content-type', 'application/json', true);
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    /* edit order items options */
    public function editOrderedItemsOptionsAction()
    {
        try {
            $params = $this->getRequest()->getParams();
            $item_id = $this->getRequest()->getParam('item_id');

            $order_item = Mage::getModel('iwd_ordermanager/order_converter')->createNewOrderItem($item_id, $params);

            $result['price'] = $order_item->getData('base_price');
            $result['name'] = $order_item->getData('name');
            $result['sku'] = $order_item->getData('sku');
            $result['product_options'] = $order_item->getData('product_options');
            $result['options_html'] = $this->getLayout()
                ->createBlock('iwd_ordermanager/adminhtml_sales_order_items_form')
                ->setTemplate('iwd/ordermanager/items/options.phtml')
                ->setData('new_order_item', $order_item)
                ->toHtml();

            $result['status'] = 1;
        } catch (Exception $e) {
            Mage::log($e->getMessage(), null, 'iwd_order_manager.log');
            $result = array('status' => 0, 'error_message' => $e->getMessage());
        }
        $this->getResponse()->setHeader('Content-type', 'application/json', true);
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    /* add: Loading page block (for pagination in search form) */
    public function loadBlockAction()
    {
        $request = $this->getRequest();

        $asJson = $request->getParam('json');
        $block = $request->getParam('block');

        $update = $this->getLayout()->getUpdate();
        if ($asJson) {
            $update->addHandle('adminhtml_sales_order_create_load_block_json');
        } else {
            $update->addHandle('adminhtml_sales_order_create_load_block_plain');
        }

        if ($block) {
            $blocks = explode(',', $block);
            if ($asJson && !in_array('message', $blocks)) {
                $blocks[] = 'message';
            }

            foreach ($blocks as $block) {
                $update->addHandle('adminhtml_sales_order_create_load_block_' . $block);
            }
        }
        $this->loadLayoutUpdates()->generateLayoutXml()->generateLayoutBlocks();
        $result = $this->getLayout()->getBlock('content')->toHtml();
        if ($request->getParam('as_js_varname')) {
            Mage::getSingleton('adminhtml/session')->setUpdateResult($result);
            $this->_redirect('*/*/showUpdateResult');
        } else {
            $this->getResponse()->setBody($result);
        }
    }


    public function configureProductAction()
    {
        $result = array('status' => 1);

        $configureResult = new Varien_Object();
        try {
            $order_item_id = (int)$this->getRequest()->getParam('id');
            if (!$order_item_id) {
                Mage::throwException($this->__('Order item id is not received.'));
            }

            $order_item = Mage::getModel('sales/order_item')->load($order_item_id);
            if (!$order_item->getId()) {
                Mage::throwException($this->__('Order item is not loaded.'));
            }
            $order = Mage::getModel('sales/order_item')->load($order_item->getOrderId());

            $buy_request = $order_item->getBuyRequest();

            $configureResult->setBuyRequest($buy_request);
            $configureResult->setCurrentStoreId($order_item->getStoreId());
            $configureResult->setProductId($order_item->getProductId());
            $configureResult->setCurrentCustomerId($order->getCustomerId());
            $configureResult->setOk(true);
        } catch (Exception $e) {
            $result['status'] = 0;
            $result['error'] = $e->getMessage();
            $configureResult->setError(true);
            $configureResult->setMessage($e->getMessage());
        }

        // Render page
        /* @var $helper IWD_OrderManager_Helper_Composite */
        $helper = Mage::helper('iwd_ordermanager/composite');
        Mage::helper('catalog/product')->setSkipSaleableCheck(true);
        $helper->renderConfigureResult($this, $configureResult);
        return $this;
    }


    private function _parseProductConfig($options, $product_id, $type)
    {
        $options_array = array();
        if (isset($options[$product_id])) {
            foreach ($options[$product_id] as $option) {
                $result = preg_match("/$type\[(\d*)\].*/", $option["name"], $found);
                if ($result == 1 && isset($found[1])) {
                    $i = $found[1];
                    if (!isset($options_array[$i])) {
                        $options_array[$i] = $option["value"];
                    } else {
                        if (is_array($options_array[$i]))
                            $options_array[$i][] = $option["value"];
                        else
                            $options_array[$i] = array($options_array[$i], $option["value"]);
                    }
                }
            }
        }
        return $options_array;
    }

    private function _parseProductsConfig($items, $options)
    {
        $selected_items = array();

        foreach ($items as $id => $val) {
            $options_array = array(
                'qty' => $val['qty'],
                'product' => $id,
            );

            $bundle_option = $this->_parseProductConfig($options, $id, 'bundle_option');
            if (!empty($bundle_option)) {
                $options_array['bundle_option'] = $bundle_option;
            }

            $bundle_option_qty = $this->_parseProductConfig($options, $id, 'bundle_option_qty');
            if (!empty($bundle_option_qty)) {
                $options_array['bundle_option_qty'] = $bundle_option_qty;
            }

            $super_group = $this->_parseProductConfig($options, $id, 'super_group');
            if (!empty($super_group)) {
                $options_array['super_group'] = $super_group;
            }

            $super_attribute = $this->_parseProductConfig($options, $id, 'super_attribute');
            if (!empty($super_attribute)) {
                $options_array['super_attribute'] = $super_attribute;
            }

            $links = $this->_parseProductConfig($options, $id, 'links');
            if (!empty($links)) {
                $options_array['links'] = $links;
            }

            $_options = $this->_parseProductConfig($options, $id, 'options');
            if (!empty($_options)) {
                $options_array['options'] = $_options;
            }

            $selected_items[$id] = $options_array;
        }

        return $selected_items;
    }

    public function _setQuoteSession($order_id)
    {
        $order = Mage::getModel('sales/order')->load($order_id);

        $quoteSession = Mage::getSingleton('adminhtml/session_quote');

        if (!$order->getReordered()) {
            $quoteSession->setOrderId($order->getId());
        } else {
            $quoteSession->setReordered($order->getId());
        }
        $quoteSession->setCurrencyId($order->getOrderCurrencyCode());
        if ($order->getCustomerId()) {
            $quoteSession->setCustomerId($order->getCustomerId());
        } else {
            $quoteSession->setCustomerId(false);
        }

        $quoteSession->setStoreId($order->getStoreId());
    }

    protected function _isAllowed()
    {
        return true;
    }
}