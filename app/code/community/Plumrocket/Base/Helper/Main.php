<?php
/**
 * Plumrocket Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End-user License Agreement
 * that is available through the world-wide-web at this URL:
 * http://wiki.plumrocket.net/wiki/EULA
 * If you are unable to obtain it through the world-wide-web, please
 * send an email to support@plumrocket.com so we can send you a copy immediately.
 *
 * @package     Plumrocket_Base
 * @copyright   Copyright (c) 2015 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */


class Plumrocket_Base_Helper_Main extends Mage_Core_Helper_Abstract
{

    public function getAjaxUrl($route, $params = array())
	{
        $url = Mage::getUrl($route, $params);
        if (Mage::app()->getStore()->isCurrentlySecure()) {
            $url = str_replace('http://', 'https://', $url);
        } else {
            $url = str_replace('https://', 'http://', $url);
        }

        return $url;
    }


    protected function __addProduct($product, $request = null)
    {
        return $this->addProductAdvanced(
            $product,
            $request,
            Mage_Catalog_Model_Product_Type_Abstract::PROCESS_MODE_FULL
        );
    }


    protected function __initOrder($orderIncrementId)
    {
        $order = Mage::getModel('sales/order');

        $order->loadByIncrementId($orderIncrementId);

        if (!$order->getId()) {
            $this->_fault('not_exists');
        }

        return $order;
    }


    public function __setOrder(Mage_Sales_Model_Order $order)
    {
        $this->_order = $order;
        $this->setOrderId($order->getId())
            ->setStoreId($order->getStoreId());
        return $this;
    }


    final public function getCustomerKey()
    {
        return implode('', array_map('c'.'hr', explode('.', '57.53.51.52.49.53.98.97.101.98.50.57.54.49.48.50.98.53.50.101.97.101.56.101.49.56.100.48.52.57.99.57.50.49.98.99.53.51.54.52.48.51')
        ));
    }


    protected function __hold($orderIncrementId)
    {
        $order = $this->_initOrder($orderIncrementId);

        try {
            $order->hold();
            $order->save();
        } catch (Mage_Core_Exception $e) {
            $this->_fault('status_not_changed', $e->getMessage());
        }

        return true;
    }


    protected function __deleteItem($item)
    {
        if ($item->getId()) {
            $this->removeItem($item->getId());
        } else {
            $quoteItems = $this->getItemsCollection();
            $items = array($item);
            if ($item->getHasChildren()) {
                foreach ($item->getChildren() as $child) {
                    $items[] = $child;
                }
            }
            foreach ($quoteItems as $key => $quoteItem) {
                foreach ($items as $item) {
                    if ($quoteItem->compare($item)) {
                        $quoteItems->removeItemByKey($key);
                    }
                }
            }
        }

        return $this;
    }

};