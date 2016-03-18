<?php
class IWD_OrderManager_Model_Order_Estimate extends IWD_OrderManager_Model_Order_Edit
{
    protected function estimateEditItem($order_item, $item)
    {
        $logger = Mage::getSingleton('iwd_ordermanager/logger');

        // product_options
        if (isset($item['product_options']) && !empty($item['product_options'])) {
            $product_options = $item['product_options'];
            Mage::getModel('iwd_ordermanager/order_edit')->addToLogProductOptions($order_item, $order_item->getData('product_options'), $product_options);
        }

        // description
        if (isset($item['description'])) {
            $logger->addOrderItemEdit($order_item, 'Description', $order_item->getDescription(), $item['description']);
        }

        // qty ordered
        if (isset($item['qty'])) {
            $logger->addOrderItemEdit($order_item, 'Qty', number_format($order_item->getQtyOrdered() - $order_item->getQtyRefunded(), 2), number_format($item['qty'], 2));
        }

        // tax amount
        if (isset($item['tax_amount'])) {
            $logger->addOrderItemEdit($order_item, 'Tax amount', number_format($order_item->getBaseTaxAmount(), 2), $item['tax_amount']);
        }

        // tax percent
        if (isset($item['tax_percent'])) {
            $logger->addOrderItemEdit($order_item, 'Tax percent', number_format($order_item->getTaxPercent(), 2), number_format($item['tax_percent'], 2));
        }

        // price
        if (isset($item['price'])) {
            $logger->addOrderItemEdit($order_item, 'Price (excl. tax)', number_format($order_item->getBasePrice(), 2), $item['price'], 'currency');
        }

        // discount amount
        if (isset($item['discount_amount'])) {
            $logger->addOrderItemEdit($order_item, 'Discount amount', number_format($order_item->getBaseDiscountAmount(), 2), $item['discount_amount']);
        }

        // discount percent
        if (isset($item['discount_percent'])) {
            $logger->addOrderItemEdit($order_item, 'Discount percent', number_format($order_item->getDiscountPercent(), 2), number_format($item['discount_percent'], 2));
        }
    }

    protected function estimateDeleteItem($order_item)
    {
        $refund = ($order_item->getQtyInvoiced() != 0);
        Mage::getSingleton('iwd_ordermanager/logger')->addOrderItemRemove($order_item, $refund);
    }

    protected function estimateAddNewItems($items)
    {
        $_items = array();

        foreach ($items as $id => $item) {
            if (isset($item['quote_item'])) {
                if (isset($item['remove']) && $item['remove'] == 1) {
                    continue;
                }

                $quote_item = Mage::getModel('sales/quote_item')->load($item['quote_item']);
                if (!$quote_item->getId()) {
                    continue;
                }

                Mage::getSingleton('iwd_ordermanager/logger')->addOrderItemAdd($quote_item);
            } else {
                $_items[$id] = $item;
            }
        }

        return $_items;
    }

    public function estimateEditItems($order_id, $items)
    {
        $order = Mage::getModel('sales/order')->load($order_id);

        /* check status */
        if (!$this->checkOrderStatusForUpdate($order)) {
            Mage::getSingleton('adminhtml/session')->addError("Sorry... You can't edit order with current status. Check configuration: IWD >> Order Manager >> Edit Order");
            return;
        }

        $logger = Mage::getSingleton('iwd_ordermanager/logger');

        $base_grand_total = 0;
        $qty_ordered = 0;
        $weight = 0;
        $base_subtotal = 0;
        $discount = 0;

        /* edit items */
        foreach ($items as $id => $item) {
            $order_item = $order->getItemById($id);

            if(isset($item['price'])){
                $item['custom_price'] = $item['price'];
                $item['original_custom_price'] = $item['price'];
            }

            /* add new */
            if (isset($item['quote_item'])) {
                if (isset($item['remove']) && $item['remove'] == 1) {
                    continue;
                }

                $quote_item = Mage::getModel('sales/quote_item')->load($item['quote_item']);
                if (!$quote_item->getId()) {
                    continue;
                }

                $weight += $quote_item->getWeight() * $item['qty'];
                $logger->addOrderItemAdd($quote_item);
            } else {
                /**** remove item ****/
                if (isset($item['remove']) && $item['remove'] == 1) {
                    if ($order_item->getProductType() == Mage_Catalog_Model_Product_Type::TYPE_BUNDLE) {
                        foreach ($order_item->getChildrenItems() as $c_item) {
                            $this->estimateDeleteItem($c_item);
                        }
                    }
                    $this->estimateDeleteItem($order_item);
                    continue;
                }

                /**** check data ****/
                if (!$this->checkItemData($item)) {
                    Mage::getSingleton('adminhtml/session')->addError(Mage::helper('iwd_ordermanager')->__("Enter the correct data for product with sku [{$order_item->getSku()}]"));
                    continue;
                }

                $this->estimateEditItem($order_item, $item);

                if($order_item->getParentItemId() == null){
                    $weight += $order_item->getWeight() * $item['qty'];
                }
            }

            //if($order_item->getParentItemId() == null){
                $base_grand_total += $item['row_total'];
                $qty_ordered += $item['qty'];
                $base_subtotal += $item['subtotal'];
            //}

            $discount += $item['discount_amount'];
        }

        $this->estimateOrderTotals($order_id, $qty_ordered, $weight, $base_grand_total, $base_subtotal);
    }

    protected function estimateOrderTotals($order_id, $qty_ordered, $weight, $base_grand_total, $base_subtotal)
    {
        $order = Mage::getModel('sales/order')->load($order_id);

        $base_currency_code = $order->getBaseCurrencyCode();
        $order_currency_code = $order->getOrderCurrencyCode();

        if(!$order->getIsVirtual()) {
            $iwd_shipping = Mage::getModel('iwd_ordermanager/shipping');
            $request = $iwd_shipping->prepareShippingRequest($order);
            $request
                ->setPackageValue($base_subtotal)
                ->setPackageValueWithDiscount($base_grand_total)
                ->setPackageWeight($weight)
                ->setPackageQty($qty_ordered);

            $shipping_amount = $iwd_shipping->estimateShippingAmount($order, $request, true);

            if (!empty($shipping_amount)) {
                $base_grand_total += $shipping_amount;
            } else {
                //TODO: show form with available shipping methods and change method
                $base_grand_total += $order->getBaseShippingInclTax();
            }
        }

        $grand_total = Mage::helper('directory')->currencyConvert($base_grand_total, $base_currency_code, $order_currency_code);

        $totals = array(
            'grand_total' => $grand_total,
            'base_grand_total' => $base_grand_total,
        );

        Mage::getSingleton('iwd_ordermanager/logger')->addNewTotalsToLog($totals);
    }
}