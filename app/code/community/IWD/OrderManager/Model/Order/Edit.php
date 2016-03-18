<?php
class IWD_OrderManager_Model_Order_Edit extends Mage_Sales_Model_Order_Item
{
    const XML_PATH_SALES_STATUS_ORDER = 'iwd_ordermanager/edit/order_status';
    const XML_PATH_RETURN_TO_STOCK = 'iwd_ordermanager/edit/return_to_stock';
    const XML_PATH_RECALCULATE_SHIPPING = 'iwd_ordermanager/edit/recalculate_shipping';

    private $added_items = false;
    private $edit_items = array();
    protected $base_currency_code = "USD";
    protected $order_currency_code = "USD";
    protected $delta = 0.06;
    protected $remove_invoice = false;

    public function getOrderStatusesForUpdateIds()
    {
        return explode(',', Mage::getStoreConfig(self::XML_PATH_SALES_STATUS_ORDER));
    }

    public function getAllowReturnToStock()
    {
        return Mage::getStoreConfig(self::XML_PATH_RETURN_TO_STOCK);
    }

    public function isRecalculateShipping()
    {
        return Mage::getStoreConfig(self::XML_PATH_RECALCULATE_SHIPPING);
    }

    public function editItems($order_id, $items)
    {
        /* event */
        $order = Mage::getModel('sales/order')->load($order_id);
        $old_order = clone $order;
        Mage::dispatchEvent('iwd_ordermanager_sales_order_edit_before', array('order' => $order, 'order_items' => $order->getItemsCollection()));

        /* check status */
        if (!$this->checkOrderStatusForUpdate($order)) {
            Mage::getSingleton('adminhtml/session')->addError("Sorry... You can't edit order with current status. Check configuration: IWD >> Order Manager >> Edit Order");
            return 0;
        }

        $this->updateOrderItems($items, $order_id);

        $this->collectOrderTotals($order_id);

        $order = Mage::getModel("sales/order")->load($order_id);
        if($this->isRecalculateShipping() && $order->canShip()){
            Mage::getModel('iwd_ordermanager/shipping')->recollectShippingAmount($order_id);
        }

        $this->collectOrderTotals($order_id);

        $this->updateOrderPayment($order_id, $old_order);

        /* event */
        $order = Mage::getModel('sales/order')->load($order_id);
        Mage::dispatchEvent('iwd_ordermanager_sales_order_edit_after', array('order' => $order, 'order_items' => $order->getItemsCollection()));
        return 1;
    }

    public function execEditOrderItems($order_id, $params)
    {
        try {
            $notify = isset($params['notify']) ? $params['notify'] : null;
            $edited = $this->editItems($order_id, $params['items']);
            $result = array();

            if ($edited && $notify) {
                $message = isset($params['comment_text']) ? $params['comment_text'] : null;
                $email = isset($params['comment_email']) ? $params['comment_email'] : null;
                $result['notify'] = Mage::getModel('iwd_ordermanager/notify_notification')->sendNotifyEmail($order_id, $email, $message);
            }
            return $result;
        } catch (Exception $e) {
            Mage::log($e->getMessage(), null, 'iwd_order_manager.log');
            return false;
        }
    }

    public function checkOrderStatusForUpdate($order)
    {
        $order_status = $order->getStatus();
        $allow_order_statuses = $this->getOrderStatusesForUpdateIds();
        $wait_order_status = Mage::getStoreConfig(IWD_OrderManager_Model_Logger::CONFIG_XML_PATH_CONFIRM_STATUS_WAIT, Mage::app()->getStore());

        return in_array($order_status, $allow_order_statuses) || ($wait_order_status == $order_status);
    }

    public function updateOrderPayment($order_id, $old_order)
    {
        try {
            /* @var $new_order Mage_Sales_Model_Order */
            /* @var $old_order Mage_Sales_Model_Order */
            $new_order = Mage::getModel('sales/order')->load($order_id);

            $old_total = $old_order->getGrandTotal() - $old_order->getTotalRefunded();
            $new_total = $new_order->getGrandTotal() - $new_order->getTotalRefunded();

            if ($old_total != $new_total) {
                $this->updateInvoice($order_id);
                $this->reauthorizePayment($order_id, $old_order);
            }
        } catch (Exception $e) {
            return -1;
        }
        return 0;
    }

    public function updateInvoice($order_id)
    {
        try {
            /* @var $order Mage_Sales_Model_Order */
            $order = Mage::getModel('sales/order')->load($order_id);
            if ($order->getTotalPaid() > 0 || $this->remove_invoice == true) {
                /* @var $invoice IWD_OrderManager_Model_Invoice */
                $invoice = Mage::getModel('iwd_ordermanager/invoice');
                $invoice->updateInvoice($order);
            }
        } catch (Exception $e) {
            Mage::log($e->getMessage(), null, 'iwd_order_manager.log');
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            return false;
        }
        return true;
    }

    protected function reauthorizePayment($order_id, $old_order)
    {
        /* @var $payment IWD_OrderManager_Model_Payment_Payment */
        $payment = Mage::getModel('iwd_ordermanager/payment_payment');

        if ($payment->reauthorizePayment($order_id, $old_order) === -1) {
            $order = Mage::getModel('sales/order')->load($order_id);
            Mage::dispatchEvent('iwd_ordermanager_sales_order_reauthorize_payment_fail', array('order' => $order, 'payment' => $payment));
            throw new Exception('IWD Order Manager re-authorization failed');
        }
    }

    protected function checkItemData($item)
    {
        $keys = array('price', 'price_incl_tax', 'qty',
            'subtotal', 'subtotal_incl_tax',
            'tax_amount', 'tax_percent',
            'discount_amount', 'discount_percent', 'row_total'
        );

        foreach ($keys as $key) {
            if (isset($item[$key]) && !is_numeric($item[$key])) {
                return false;
            }
        }

        return true;
    }

    protected function updateQty($order_item, $new_qty)
    {
        if($order_item->getProductType() == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE){
            $collection = Mage::getModel('sales/order_item')->getCollection()
                ->addFieldToFilter('parent_item_id', $order_item->getItemId());
            if($collection->getSize() > 0){
                $simple_order_item = $collection->getFirstItem();
                $this->updateQtyProduct($simple_order_item, $new_qty);
            }
        }
        $this->updateQtyProduct($order_item, $new_qty);
    }

    protected function updateQtyProduct($order_item, $new_qty)
    {
        /*
         *  $new_qty is a NOT fact qty for customer NOW !!!
         *  it is the order item ORDERED QTY !!!
         */
        // '-' qty
        if ($order_item->getQtyOrdered() > $new_qty) {
            $this->reduceProduct($order_item, $new_qty);
        } // '+' qty
        else {
            $this->increaseProduct($order_item, $new_qty);
        }

        $order_item->setQtyOrdered($new_qty);
        $order_item->setRowWeight($order_item->getWeight() * $new_qty - $order_item->getQtyRefunded());
        $order_item->save();

        return $new_qty;
    }

    protected function reduceProduct($order_item, $new_qty)
    {
        $refund = $order_item->getQtyOrdered() - $new_qty - $order_item->getQtyRefunded();

        if ($refund > 0) {
            if ($this->getAllowReturnToStock()) {
                Mage::getSingleton('cataloginventory/stock')->backItemQty($order_item->getProductId(), $refund);
            }
        }
    }

    protected function increaseProduct($order_item, $new_qty)
    {
        $product_id = $order_item->getProductId();

        if ($product_id) {
            $stock_item = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product_id);

            if (Mage::helper('cataloginventory')->isQty($stock_item->getTypeId())) {
                if ($order_item->getStoreId()) {
                    $stock_item->setStoreId($order_item->getStoreId());
                }

                $qty = $new_qty - ($order_item->getQtyOrdered());
                $qty = $qty < 0 ? 0 : $qty;

                if ($stock_item->checkQty($qty)) {
                    $stock_item->subtractQty($qty)->save();
                }
            }
        } else {
            Mage::throwException(Mage::helper('iwd_ordermanager')->__('Cannot specify product identifier for the order item.'));
        }

        $this->added_items = true;
    }


    protected function currencyConvert($price)
    {
        if ($this->base_currency_code === $this->order_currency_code){
            return $price;
        }
        return Mage::helper('directory')->currencyConvert($price, $this->base_currency_code, $this->order_currency_code);
    }

    protected function updateOrderItems($items, $orderId)
    {
        $order = Mage::getModel('sales/order')->load($orderId);

        $this->base_currency_code = $order->getBaseCurrencyCode();
        $this->order_currency_code = $order->getOrderCurrencyCode();
        $this->refund_qty = array();
        $this->edit_items = array();
        $this->added_items = false;

        foreach ($items as $id => $item) {
            $order_item = $order->getItemById($id);

            // remove item
            if (isset($item['remove']) && $item['remove'] == 1) {
                $this->removeOrderItem($order_item);
                continue;
            }

            // add new item
            if (isset($item['quote_item'])) {
                $order_item = $this->addNewOrderItem($item['quote_item'], $order);
            }

            // edit item
            $this->editOrderItem($order_item, $item);
        }
    }

    protected function editOrderItem($order_item, $item)
    {
        $logger = Mage::getSingleton('iwd_ordermanager/logger');

        // old order item values
        $old_row_total = $order_item->getRowTotal();
        $old_base_row_total = $order_item->getBaseRowTotal();
        $old_tax_amount = $order_item->getTaxAmount();
        $old_base_tax_amount = $order_item->getBaseTaxAmount();
        $old_hidden_tax_amount = $order_item->getHiddenTaxAmount();
        $old_base_hidden_tax_amount = $order_item->getBaseHiddenTaxAmount();
        $old_discount_amount = $order_item->getDiscountAmount();
        $old_base_discount_amount = $order_item->getBaseDiscountAmount();

        // description
        if (isset($item['description'])) {
            $logger->addOrderItemEdit($order_item, 'Description', $order_item->getDescription(), $item['description']);
            $order_item->setDescription($item['description']);
        }

        if (!$this->checkItemData($item)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('iwd_ordermanager')->__("Enter the correct data for product with sku [{$order_item->getSku()}]"));
            return;
        }

        $qty = $order_item->getQtyOrdered();

        // qty ordered
        $old_qty_ordered = $order_item->getQtyOrdered() - $order_item->getQtyRefunded();
        $fact_qty = isset($item['fact_qty']) ? $item['fact_qty'] : $old_qty_ordered;

        $this->updateQty($order_item, $fact_qty);
        $logger->addOrderItemEdit($order_item, 'Qty', number_format($old_qty_ordered, 2), number_format($fact_qty, 2));

        $this->updateAmounts($order_item, $item);

        // product options
        $this->updateProductOptions($order_item, $item);

        // support_date
        $this->updateSupportDate($order_item, $item);

        $order_item->save();
        $this->updateOrderTaxItemTable($order_item);

        $new_row_total = $this->getOrderItemRowTotal($order_item);

        if (abs($old_row_total - $new_row_total) >= $this->delta) {
            $this->edit_items[$order_item->getId()] = array(
                'row_total'              => $old_row_total - $order_item->getRowTotal(),
                'base_row_total'         => $old_base_row_total - $order_item->getBaseRowTotal(),
                'tax_refunded'           => $old_tax_amount - $order_item->getTaxAmount(),
                'base_tax_amount'        => $old_base_tax_amount - $order_item->getBaseTaxAmount(),
                'hidden_tax_amount'      => $old_hidden_tax_amount - $order_item->getHiddenTaxAmount(),
                'base_hidden_tax_amount' => $old_base_hidden_tax_amount - $order_item->getBaseHiddenTaxAmount(),
                'discount_amount'        => $old_discount_amount - $order_item->getDiscountAmount(),
                'base_discount_amount'   => $old_base_discount_amount - $order_item->getBaseDiscountAmount()
            );
        }
    }

    protected function getOrderItemRowTotal($item)
    {
        return $item->getRowTotal() + $item->getTaxAmount() + $item->getHiddenTaxAmount() + $item->getWeeeTaxAppliedRowAmount() - $item->getDiscountAmount();
    }

    protected function editDownloadItem($order_item, $item)
    {
        $new = $item['product_options'];
        $old = $order_item->getData('product_options');

        $old = is_string($old) ? unserialize($old) : $old;
        $new = is_string($new) ? unserialize($new) : $new;

        if($order_item->getProductType() == Mage_Downloadable_Model_Product_Type::TYPE_DOWNLOADABLE) {
            $old_links = isset($old["links"]) ? $old["links"] : array();
            $new_links = isset($new["links"]) ? $new["links"] : array();

            $added = array_diff($new_links, $old_links);
            foreach($added as $link_id){
                $this->addDownloadableLink($order_item, $link_id);
            }

            $removed = array_diff($old_links, $new_links);
            foreach($removed as $link_id){
                $this->removeDownloadableLink($order_item->getId(), $link_id);
            }
        }
    }

    protected function addDownloadableLink($order_item, $link_id)
    {
        $order_item_id = $order_item->getId();
        $link_purchased_id = $this->getLinkPurchasedIdForOrderItem($order_item_id);

        $linkPurchasedItem = Mage::getModel('downloadable/link_purchased_item')
            ->setPurchasedId($link_purchased_id)
            ->setOrderItemId($order_item_id);

        Mage::helper('core')->copyFieldset(
            'downloadable_sales_copy_link',
            'to_purchased',
            $link_id,
            $linkPurchasedItem
        );

        $linkHash = strtr(base64_encode(microtime() . $link_purchased_id . $order_item_id . $order_item->getProductId()), '+/=', '-_,');

        $link = Mage::getModel('downloadable/link')
            ->getCollection()
            ->addTitleToResult()
            ->addFieldToFilter('main_table.link_id',array('eq'=>$link_id))
            ->getFirstItem();

        $numberOfDownloads = $link->getNumberOfDownloads() * $order_item->getQtyOrdered();
        $linkPurchasedItem->setLinkHash($linkHash)
            ->setNumberOfDownloadsBought($numberOfDownloads)
            ->setStatus(Mage_Downloadable_Model_Link_Purchased_Item::LINK_STATUS_PENDING)
            ->setCreatedAt($order_item->getCreatedAt())
            ->setUpdatedAt($order_item->getUpdatedAt())
            ->setProductId($order_item->getProductId())
            ->setLinkId($link->getLinkId())
            ->setIsShareable($link->getIsShareable())
            ->setLinkUrl($link->getLinkUrl())
            ->setLinkFile($link->getLinkFile())
            ->setLinkType($link->getLinkType())
            ->setLinkTitle($link->getDefaultTitle())
            ->save();
    }

    protected function getLinkPurchasedIdForOrderItem($order_item_id){
        $collection = Mage::getModel('downloadable/link_purchased')->getCollection()
            ->addFieldToFilter('order_item_id', $order_item_id);

        if($collection->getSize() > 0){
            return $collection->getFirstItem()->getId();
        }

        return 0;
    }

    protected function removeDownloadableLink($order_item_id, $link_id)
    {
        try {
            $collection = Mage::getModel('downloadable/link_purchased_item')->getCollection()
                ->addFieldToFilter('order_item_id', $order_item_id)
                ->addFieldToFilter('link_id', $link_id);

            foreach ($collection as $link_purchased_item) {
                $link_purchased_item->delete();
            }
        } catch(Exception $e) {

        }
    }

    protected function updateProductOptions($order_item, $item)
    {
        if (isset($item['product_options']) && !empty($item['product_options'])) {

            // edit download product
            $this->editDownloadItem($order_item, $item);

            $product_options = $item['product_options'];
            $this->addToLogProductOptions($order_item, $order_item->getData('product_options'), $product_options);
            $order_item->setData('product_options', $product_options);

            $old_sku = $order_item->getSku();
            $options = unserialize($product_options);
            if (isset($options['simple_sku']) && !empty($options['simple_sku'])) {
                $order_item->setSku($options['simple_sku']);
            }
            if (isset($options['simple_name']) && !empty($options['simple_name'])) {
                $order_item->setName($options['simple_name']);
            }
            $new_sku = $order_item->getSku();

            //update inventory
            if ($old_sku != $new_sku) {
                // prepare qty
                $qty = $order_item->getQtyOrdered() - $order_item->getQtyRefunded() - $order_item->getQtyCanceled();
                $qty = $qty < 0 ? 0 : $qty;

                // prepare id
                $_catalog = Mage::getModel('catalog/product');
                $old_product_id = $_catalog->getIdBySku($old_sku);
                $new_product_id = $_catalog->getIdBySku($new_sku);

                // update product id for simple product
                try {
                    if ($order_item->getProductType() == 'configurable') {
                        $simple_order_item = Mage::getModel('sales/order_item')->getCollection()->addFieldToFilter('parent_item_id', $order_item->getItemId());
                        if ($simple_order_item->getSize() > 0) {
                            $simple_order_item->getFirstItem()
                                ->setProductId($old_product_id)
                                ->setSku($new_sku)
                                ->setName($order_item->getName())
                                ->save();
                        }
                    } else if ($order_item->getProductType() == 'simple') {
                        $order_item->setProductId($old_product_id);
                    }
                } catch(Exception $e) { }

                // push to inventory
                Mage::getSingleton('cataloginventory/stock')->backItemQty($old_product_id, $qty);

                // pull from inventory
                $stock_item = Mage::getModel('cataloginventory/stock_item')->loadByProduct($new_product_id);
                if (Mage::helper('cataloginventory')->isQty($stock_item->getTypeId())) {
                    if ($order_item->getStoreId()) {
                        $stock_item->setStoreId($order_item->getStoreId());
                    }
                    if ($stock_item->checkQty($qty)) {
                        $stock_item->subtractQty($qty)->save();
                    }
                }
            }

            $order_item->save();
        }

        return $order_item;
    }

    protected function addToLogProductOptions($order_item, $old, $new)
    {
        $logger = Mage::getSingleton('iwd_ordermanager/logger');
        $old = unserialize($old);
        $new = unserialize($new);

        /* attributes */
        $old_attributes_array = isset($old["attributes_info"]) ? $old["attributes_info"] : array();
        $new_attributes = isset($new["attributes_info"]) ? $new["attributes_info"] : array();
        $old_attributes = array();
        foreach($old_attributes_array as $attribute) {
            if(isset($attribute["label"]) && isset($attribute["value"])){
                $old_attributes[$attribute["label"]] = $attribute["value"];
            }
        }
        foreach($new_attributes as $attribute) {
            if(isset($attribute["label"]) && isset($attribute["value"])){
                $attribute_id = $attribute["label"];
                if(isset($old_attributes[$attribute_id])){
                    $old_attribute = $old_attributes[$attribute_id];
                } else {
                    $old_attribute = "";
                }
                unset($old_attributes[$attribute_id]);

                $logger->addOrderItemEdit($order_item, $attribute["label"], $old_attribute, $attribute['value']);
            }
        }
        foreach($old_attributes as $attribute) {
            if(isset($attribute["label"]) && isset($attribute["value"])){
                $logger->addOrderItemEdit($order_item, $attribute["label"], $attribute["value"], "");
            }
        }

        /* options */
        $old_options_array = isset($old["options"]) ? $old["options"] : array();
        $new_options = isset($new["options"]) ? $new["options"] : array();
        $old_options = array();
        foreach($old_options_array as $option) {
            if(isset($option["option_id"])){
                $old_options[$option["option_id"]] = $option;
            }
        }
        foreach($new_options as $option) {
            if(isset($option["option_id"]) && isset($option["label"]) && isset($option["print_value"])){
                $option_id = $option["option_id"];
                $label = $option['label'];
                if(isset($old_options[$option_id])){
                    $old_option = $old_options[$option_id]['print_value'];
                } else {
                    $old_option = "";
                }

                unset($old_options[$option_id]);
                $logger->addOrderItemEdit($order_item, $label, $old_option, $option['print_value']);
            }
        }
        foreach($old_options as $option) {
            if(isset($option["option_id"]) && isset($option["label"]) && isset($option["print_value"])){
                $logger->addOrderItemEdit($order_item, $option["label"], $option['print_value'], "");
            }
        }

        /* links */
        $old_links = isset($old["links"]) ? $old["links"] : array();
        $new_links = isset($new["links"]) ? $new["links"] : array();
        $added = array_diff($new_links, $old_links);
        foreach($added as $link){
            $title = $this->getDownloadTitle($link, $order_item->getId());
            $logger->addOrderItemEdit($order_item, 'Added link', $title, null);
        }
        $removed = array_diff($old_links, $new_links);
        foreach($removed as $link){
            $title = $this->getDownloadTitle($link, $order_item->getId());
            $logger->addOrderItemEdit($order_item, 'Removed link', $title, null);
        }

        /* name/sku */
        $new_name =  isset($new["simple_name"]) ?  $new["simple_name"] : "";
        $old_name =  isset($old["simple_name"]) ?  $old["simple_name"] : "";
        $new_sku =  isset($new["simple_sku"]) ?  " (" .$new["simple_sku"]. ")" : "";
        $old_sku =  isset($old["simple_sku"]) ?  " (" .$old["simple_sku"]. ")" : "";
        $logger->addOrderItemEdit($order_item, 'Product', $new_name . $new_sku, $old_name . $old_sku);
    }

    protected function getDownloadTitle($link_id, $order_item_id)
    {
        $collection = Mage::getModel('downloadable/link_purchased_item')->getCollection()
            ->addFieldToFilter('link_id', $link_id)
            ->addFieldToFilter('order_item_id', $order_item_id);

        if($collection->getSize() > 0){
            return $collection->getFirstItem()->getLinkTitle();
        }

        try {
            $link = Mage::getModel('downloadable/link')
                ->getCollection()
                ->addTitleToResult()
                ->addFieldToFilter('main_table.link_id',array('eq'=>$link_id));
            return $link->getFirstItem()->getTitle();
        }catch(Exception $e){}

        return "Link ID #" . $link_id;
    }

    protected function updateSupportDate($order_item, $item)
    {
        if (isset($item['support_date'])) {
            if (Mage::getConfig()->getModuleConfig('IWD_Downloadable')->is('active', 'true')) {
                Mage::helper('iwd_ordermanager/downloadable')->updateSupportPeriod($order_item->getId(), $item['support_date']);
            }
        }
    }

    protected function updateAmounts($order_item, $item)
    {
        $logger = Mage::getSingleton('iwd_ordermanager/logger');

        // tax amount
        if (isset($item['tax_amount'])) {
            $logger->addOrderItemEdit($order_item, 'Tax amount', number_format($order_item->getBaseTaxAmount(), 2), number_format($item['tax_amount'], 2));
            $tax_amount = $this->currencyConvert($item['tax_amount']);
            $order_item->setBaseTaxAmount($item['tax_amount'])->setTaxAmount($tax_amount);
        }

        // hidden tax amount
        if (isset($item['hidden_tax_amount'])) {
            $hidden_tax_amount = $this->currencyConvert($item['hidden_tax_amount']);
            $order_item->setBaseHiddenTaxAmount($item['hidden_tax_amount'])->setHiddenTaxAmount($hidden_tax_amount);
        }

        // tax percent
        if (isset($item['tax_percent'])) {
            $logger->addOrderItemEdit($order_item, 'Tax percent', number_format($order_item->getTaxPercent(), 2), number_format($item['tax_percent'], 2));
            $order_item->setTaxPercent($item['tax_percent']);
        }

        // price
        if (isset($item['price'])) {
            $logger->addOrderItemEdit($order_item, 'Price (excl. tax)', number_format($order_item->getBasePrice(), 2), number_format($item['price'], 2));
            $price = $this->currencyConvert($item['price']);
            $order_item->setBasePrice($item['price'])->setPrice($price);
        }

        // price include tax
        if (isset($item['price_incl_tax'])) {
            $price_incl_tax = $this->currencyConvert($item['price_incl_tax']);
            $order_item->setBasePriceInclTax($item['price_incl_tax'])->setPriceInclTax($price_incl_tax);
        }

        // discount amount
        if (isset($item['discount_amount'])) {
            $logger->addOrderItemEdit($order_item, 'Discount amount', number_format($order_item->getBaseDiscountAmount(), 2), number_format($item['discount_amount'], 2));
            $discount_amount = $this->currencyConvert($item['discount_amount']);
            $order_item->setBaseDiscountAmount($item['discount_amount'])->setDiscountAmount($discount_amount);
        }

        // discount percent
        if (isset($item['discount_percent'])) {
            $logger->addOrderItemEdit($order_item, 'Discount percent', number_format($order_item->getDiscountPercent(), 2), number_format($item['discount_percent'], 2));
            $order_item->setDiscountPercent($item['discount_percent']);
        }

        // subtotal (row total)
        if (isset($item['subtotal'])) {
            $subtotal = $this->currencyConvert($item['subtotal']);
            $order_item->setBaseRowTotal($item['subtotal'])->setRowTotal($subtotal);
        }

        // subtotal include tax
        if (isset($item['subtotal_incl_tax'])) {
            $subtotal_incl_tax = $this->currencyConvert($item['subtotal_incl_tax']);
            $order_item->setBaseRowTotalInclTax($item['subtotal_incl_tax'])->setRowTotalInclTax($subtotal_incl_tax);
        }

        $order_item->save();

        return $order_item;
    }

    protected function addNewOrderItem($quote_item_id, $order)
    {
        $quote_item = Mage::getModel('sales/quote_item')->load($quote_item_id);
        if (!$quote_item->getId()) {
            return null;
        }

        $quote = Mage::getModel('sales/quote')->loadByIdWithoutStore($quote_item->getQuoteId());
        $quote_item->setQuote($quote);

        $order_item = $this->addItemToOrder($order, $quote_item);
        $order_item->save();

        $this->addChildrenItems($quote_item_id, $quote, $order_item, $order);

        Mage::getSingleton('iwd_ordermanager/logger')->addOrderItemAdd($order_item);

        return $order_item;
    }

    public function addItemToOrder($order, $quote_item)
    {
        try {
            $optionCollection = Mage::getModel('sales/quote_item_option')->getCollection()
                ->addItemFilter(array($quote_item->getId()));
            $quote_item->setOptions($optionCollection->getOptionsByItem($quote_item));

            if ($simpleOption = $quote_item->getProduct()->getCustomOption('simple_product')) {
                $simple_product = Mage::getModel('catalog/product')->load($simpleOption->getProductId());
                $simpleOption->setProduct($simple_product);
            }

            $order_item = Mage::getModel('sales/convert_quote')->itemToOrderItem($quote_item);
            $order_item->setOrderId($order->getId());

            if ($quote_item->getParentItemId()) {
                $order_item->setParentItem($order->getItemByQuoteItemId($quote_item->getParentItemId()));
            }

            if (Mage::getModel("tax/config")->priceIncludesTax()) {
                $order_item->setOriginalPrice($order_item->getPriceInclTax());
                $order_item->setBaseOriginalPrice($order_item->getBasePriceInclTax());
            } else {
                $order_item->setOriginalPrice($order_item->getPrice());
                $order_item->setBaseOriginalPrice($order_item->getBasePrice());
            }

            $order_item->save($order->getId());

            //from inventory
            $stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($order_item->getProductId());
            if ($stockItem->checkQty($order_item->getQtyOrdered()) || Mage::app()->getStore()->isAdmin()) {
                $stockItem->subtractQty($order_item->getQtyOrdered());
                $stockItem->save();
                $this->added_items = true;
            }

            $this->addOrderTaxItemTable($order_item);
        } catch (Exception $e) {
            Mage::log($e->getMessage(), null, 'iwd_order_manager.log');
            return null;
        }
        return $order_item;
    }

    protected function addChildrenItems($quote_item_id, $quote, $order_item, $order)
    {
        $id = $order_item->getId();
        $qty = $order_item->getQtyOrdered();

        // children
        $quote_children_items = Mage::getModel("sales/quote_item")
            ->getCollection()->setQuote($quote)
            ->addFieldToFilter("parent_item_id", $quote_item_id);

        foreach ($quote_children_items as $quote_children_item) {
            $quote_children_item->setQuote($quote);
            $order_item = $this->addItemToOrder($order, $quote_children_item);
            $order_item_qty = $order_item->getQtyOrdered() * $qty;
            $order_item->setQtyOrdered($order_item_qty)
                ->setParentItemId($id)
                ->save();
        }
    }

    protected function removeOrderItem($order_item)
    {
        $product_type = $order_item->getProductType();

        /* return to stock */
        $this->reduceProduct($order_item, 0);

        /* delete children items */
        if ($product_type == Mage_Catalog_Model_Product_Type::TYPE_BUNDLE || $product_type == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE) {
            $children_items = $order_item->getChildrenItems();
            foreach ($children_items as $children_item) {
                $this->deleteItem($children_item);
            }
        }

        /* delete download items */
        if($product_type == Mage_Downloadable_Model_Product_Type::TYPE_DOWNLOADABLE){
            $collection = Mage::getModel('downloadable/link_purchased_item')->getCollection()->addFieldToFilter('order_item_id', $order_item->getId());
            foreach($collection as $item){
                $item->delete();
            }
        }

        /* delete shipping items */
        $shipment_items = Mage::getModel('sales/order_shipment_item')->getCollection()
            ->addFieldToFilter('order_item_id', $order_item->getItemId());
        foreach($shipment_items as $shipment_item){
            $shipment = Mage::getModel('sales/order_shipment')->load($shipment_item->getParentId());
            $qty = $shipment->getTotalQty() - $shipment_item->getQty();
            $shipment->setTotalQty($qty)->save();
            $shipment_item->delete();
        }


        /* delete creditmemo items */
        $creditmemo_items = Mage::getModel('sales/order_creditmemo_item')->getCollection()
            ->addFieldToFilter('order_item_id', $order_item->getItemId());
        foreach($creditmemo_items as $creditmemo_item){
            $creditmemo_item->delete();
        }


        $this->deleteItem($order_item);
        $this->addToLogAboutDeleteOrderItem($order_item);
    }

    protected function deleteItem($order_item){
        if($this->remove_invoice == false) {
            /* @var $invoice IWD_OrderManager_Model_Invoice */
            $invoice = Mage::getModel('iwd_ordermanager/invoice');
            $this->remove_invoice = $invoice->cancelInvoices($order_item->getOrder());
        }

        $order_item->delete();
    }

    protected function addToLogAboutDeleteOrderItem($order_item)
    {
        $is_refunded = ($order_item->getQtyInvoiced() != 0);
        Mage::getSingleton('iwd_ordermanager/logger')->addOrderItemRemove($order_item, $is_refunded);
    }

    public function addOrderTaxItemTable($order_item)
    {
        $result = Mage::getModel('tax/sales_order_tax')->getCollection()
            ->addFieldToFilter('order_id', $order_item->getOrderId());

        if (count($result) > 0) {
            $data = array(
                'item_id' => $order_item->getId(),
                'tax_id' => $result->getFirstItem()->getTaxId(),
                'tax_percent' => $order_item->getTaxPercent()
            );
            Mage::getModel('tax/sales_order_tax_item')->setData($data)->save();
        }
    }

    protected function updateOrderTaxItemTable($order_item)
    {
        /* @var $order_item Mage_Sales_Model_Order_Item */
        $new_tax_percent = $order_item->getTaxPercent();

        $tax_collection = Mage::getModel('tax/sales_order_tax_item')->getCollection()
            ->addFieldToFilter('item_id', $order_item->getId());

        //add new
        if ($tax_collection->getSize() == 0) {
            $result = Mage::getModel('tax/sales_order_tax')->getCollection()
                ->addFieldToFilter('order_id', $order_item->getOrderId());
            if (count($result) > 0) {
                $data = array(
                    'item_id' => $order_item->getId(),
                    'tax_id' => $result->getFirstItem()->getTaxId(),
                    'tax_percent' => $new_tax_percent
                );
                Mage::getModel('tax/sales_order_tax_item')->setData($data)->save();
            }else {
                //todo:
            }
        }

        //update
        foreach ($tax_collection as $tax) {
            $tax->setTaxPercent($new_tax_percent)->save();
        }
    }

    public function updateOrderTaxTable($order_id)
    {
        $order_items = Mage::getModel('sales/order')->load($order_id)->getItemsCollection();
        $taxes = array();

        foreach ($order_items as $item) {
            $id = $item->getTaxPercent();
            if (!isset($taxes[$id])) {
                $taxes[$id] = array('amount' => 0, 'base_amount' => 0);
            }

            $taxes[$id]['amount'] += $item->getTaxAmount();
            $taxes[$id]['base_amount'] += $item->getBaseTaxAmount();
        }

        $source = Mage::getModel('sales/order')->load($order_id);
        $rates = Mage::getModel('sales/order_tax')->getCollection()->loadByOrder($source);

        foreach ($rates as $rate) {
            $id = $rate->getTaxPercent();
            if (isset($taxes[$id])) {
                $rate->setAmount($taxes[$id]['amount'])
                    ->setBaseAmount($taxes[$id]['base_amount'])
                    ->save();
                unset($taxes[$id]);
            }
        }
    }

    public function collectOrderTotals($order_id)
    {
        /* @var $order Mage_Sales_Model_Order */
        $order = Mage::getModel('sales/order')->load($order_id);

        $total_qty_ordered = 0;
        $weight = 0;
        $total_item_count = 0;
        $base_tax_amount = 0;
        $base_hidden_tax_amount = 0;
        $base_discount_amount = 0;
        $base_total_weee_discount = 0;
        $base_subtotal = 0;
        $base_subtotal_incl_tax = 0;

        /* @var $order_item Mage_Sales_Model_Order_Item */
        foreach ($order->getItemsCollection() as $order_item) {
            $base_discount_amount += $order_item->getBaseDiscountAmount();

            //bundle part
            if ($order_item->getParentItem()) {
                continue;
            }

            $base_tax_amount += $order_item->getBaseTaxAmount();
            $base_hidden_tax_amount += $order_item->getBaseHiddenTaxAmount();

            $total_qty_ordered += $order_item->getQtyOrdered();
            $total_item_count++;
            $weight += $order_item->getRowWeight();
            $base_subtotal += $order_item->getBaseRowTotal(); /* RowTotal for item is a subtotal */
            $base_subtotal_incl_tax += $order_item->getBaseRowTotalInclTax();
            $base_total_weee_discount += $order_item->getBaseDiscountAppliedForWeeeTax();
        }

        //$base_subtotal_incl_tax = $base_subtotal + $base_hidden_tax_amount + $base_total_weee_discount + $base_tax_amount;

        /** convert currency **/
        $base_currency_code = $order->getBaseCurrencyCode();
        $order_currency_code = $order->getOrderCurrencyCode();

        /* @var $directory Mage_Directory_Helper_Data */
        $directory = Mage::helper('directory');
        if ($base_currency_code === $order_currency_code) {
            $discount_amount = $base_discount_amount;
            $tax_amount = $base_tax_amount;
            $hidden_tax_amount = $base_hidden_tax_amount;
            $subtotal = $base_subtotal;
            $subtotal_incl_tax = $base_subtotal_incl_tax;
        } else {
            $discount_amount = $directory->currencyConvert($base_discount_amount, $base_currency_code, $order_currency_code);
            $tax_amount = $directory->currencyConvert($base_tax_amount, $base_currency_code, $order_currency_code);
            $hidden_tax_amount = $directory->currencyConvert($base_hidden_tax_amount, $base_currency_code, $order_currency_code);
            $subtotal = $directory->currencyConvert($base_subtotal, $base_currency_code, $order_currency_code);
            $subtotal_incl_tax = $directory->currencyConvert($base_subtotal_incl_tax, $base_currency_code, $order_currency_code);
        }

        $order->setTotalQtyOrdered($total_qty_ordered)
            ->setWeight($weight);

        $order->setSubtotal($subtotal)->setBaseSubtotal($base_subtotal)
            ->setSubtotalInclTax($subtotal_incl_tax)->setBaseSubtotalInclTax($base_subtotal_incl_tax)
            ->setTaxAmount($tax_amount)->setBaseTaxAmount($base_tax_amount)
            ->setHiddenTaxAmount($hidden_tax_amount)->setBaseHiddenTaxAmount($base_hidden_tax_amount)
            ->setDiscountAmount('-' . $discount_amount)->setBaseDiscountAmount('-' . $base_discount_amount)
            ->setTotalItemCount($total_item_count)
            ->save();


        $order->save();

        $this->calculateGrandTotal($order);

        $this->updateOrderTaxTable($order_id);
    }

    /* @var $order Mage_Sales_Model_Order */
    public function calculateGrandTotal($order)
    {
        // shipping tax
        $tax = $order->getTaxAmount() + $order->getShippingTaxAmount();
        $base_tax = $order->getBaseTaxAmount() + $order->getBaseShippingTaxAmount();

        $order->setTaxAmount($tax)->setBaseTaxAmount($base_tax)->save();

        // Order GrandTotal include tax
        $grand_total = $order->getSubtotal() + $order->getTaxAmount() + $order->getShippingAmount() - abs($order->getDiscountAmount());
        $base_grand_total = $order->getBaseSubtotal() + $order->getBaseTaxAmount() + $order->getBaseShippingAmount() - abs($order->getBaseDiscountAmount());

        $order->setGrandTotal($grand_total)
            ->setBaseGrandTotal($base_grand_total)
            ->save();

        $this->addCustomPriceToOrderGrandTotal($order);
    }

    protected function addCustomPriceToOrderGrandTotal($order)
    {
        //TODO: add custom logic if you need add custom price to order
        return;

        /*
        $additional_total = 0.0;        // add custom amount
        $additional_base_total = 0.0;   // add custom base amount

        $grand_total = $order->getGrandTotal();
        $base_grand_total = $order->getBaseGrandTotal();
        $order->setGrandTotal($grand_total + $additional_total)
            ->setBaseGrandTotal($base_grand_total + $additional_base_total)
            ->save();
        */
    }
}