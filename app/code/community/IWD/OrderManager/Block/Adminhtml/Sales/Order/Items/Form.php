<?php
class IWD_OrderManager_Block_Adminhtml_Sales_Order_Items_Form extends Mage_Adminhtml_Block_Widget
{
    public function getSelectionAttributes($item)
    {
        if ($item instanceof Mage_Sales_Model_Order_Item) {
            $options = $item->getProductOptions();
        } else if ($item instanceof Mage_Sales_Model_Quote_Item) {
            $optionCollection = Mage::getModel('sales/quote_item_option')->getCollection()
                ->addItemFilter(array($item->getId()))->addFieldToFilter('code', 'bundle_selection_attributes');

            $options = unserialize($optionCollection->getFirstItem()->getValue());
            $item->getProductOptions($options);
            return $options;
        } else {
            $options = $item->getOrderItem()->getProductOptions();
        }

        if (isset($options['bundle_selection_attributes'])) {
            return unserialize($options['bundle_selection_attributes']);
        }
        return null;
    }

    public function isChildCalculated($item)
    {
        if ($item) {
            if ($parentItem = $item->getParentItem()) {
                if ($options = $parentItem->getProductOptions()) {
                    if (isset($options['product_calculations']) && $options['product_calculations'] == Mage_Catalog_Model_Product_Type_Abstract::CALCULATE_CHILD) {
                        return true;
                    } else {
                        return false;
                    }
                }
            } else {
                if ($options = $item->getProductOptions()) {
                    if (isset($options['product_calculations']) && $options['product_calculations'] == Mage_Catalog_Model_Product_Type_Abstract::CALCULATE_CHILD) {
                        return false;
                    } else {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    public function canShowPriceInfo($item)
    {
        if (($item->getParentItem() && $this->isChildCalculated($item))
            || (!$item->getParentItem() && !$this->isChildCalculated($item))
        ) {
            return true;
        }
        return false;
    }

    public function getOrderDataJson($order_id)
    {
        $order = Mage::getModel('sales/order')->load($order_id);
        $data = array();

        if (!is_null($order->getCustomerId())) {
            $data['customer_id'] = $order->getCustomerId();
        }
        if (!is_null($order->getStoreId())) {
            $data['store_id'] = $order->getStoreId();
            $currency = Mage::app()->getLocale()->currency($order->getStore()->getCurrentCurrencyCode());
            $symbol = $currency->getSymbol() ? $currency->getSymbol() : $currency->getShortName();
            $data['currency_symbol'] = $symbol;
            $data['payment_method'] = $order->getPayment()->getMethod();
        }
        return Mage::helper('core')->jsonEncode($data);
    }

    public function getLoadBlockUrl()
    {
        return $this->getUrl('*/*/loadBlock');
    }

    public function getCurrencyRowTotal($item)
    {
        return $this->getOrder()->formatBasePrice($this->getBaseRowTotal($item), $this->getRowTotal($item));
    }

    public function getBaseRowTotal($item)
    {
        return $item->getBaseRowTotal() + $item->getBaseTaxAmount() + $item->getBaseHiddenTaxAmount() + $item->getBaseWeeeTaxAppliedRowAmount() - $item->getBaseDiscountAmount();
    }

    public function getRowTotal($item)
    {
        return $item->getRowTotal() + $item->getTaxAmount() + $item->getHiddenTaxAmount() + $item->getWeeeTaxAppliedRowAmount() - $item->getDiscountAmount();
    }

    public function getOrder(){
        return Mage::getModel('sales/order')->load($this->order_id);
    }

    public function getStockObjectForOrderItem($item){
        if ($item->getProductType() == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE) {
            $child_order_item = Mage::getModel('sales/order_item')
                ->getCollection()->addFieldToFilter('parent_item_id', $item->getItemId())
                ->getFirstItem();
            if(!empty($child_order_item)){
                $product = Mage::getModel('catalog/product')->load($child_order_item->getProductId());
            } else {
                $product = $item->getProduct();
            }
        } else {
            $product = $item->getProduct();
        }

        return Mage::getModel('cataloginventory/stock_item')->loadByProduct($product);
    }

    public function hasOptions($item)
    {
        return Mage::getModel('catalog/product')->load($item->getProductId())->canConfigure();
    }


    public function getOrderOptions($item)
    {
        $result = array();
        if ($options = $item->getProductOptions()) {
            if (isset($options['options'])) {
                $result = array_merge($result, $options['options']);
            }
            if (isset($options['additional_options'])) {
                $result = array_merge($result, $options['additional_options']);
            }
            if (!empty($options['attributes_info'])) {
                $result = array_merge($options['attributes_info'], $result);
            }
        }
        return $result;
    }

    public function getCustomizedOptionValue($optionInfo)
    {
        // render customized option view
        $_default = $optionInfo['value'];
        if (isset($optionInfo['option_type'])) {
            try {
                $group = Mage::getModel('catalog/product_option')->groupFactory($optionInfo['option_type']);
                return $group->getCustomizedView($optionInfo);
            } catch (Exception $e) {
                return $_default;
            }
        }
        return $_default;
    }
}