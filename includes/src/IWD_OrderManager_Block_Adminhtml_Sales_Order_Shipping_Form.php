<?php
class IWD_OrderManager_Block_Adminhtml_Sales_Order_Shipping_Form extends Mage_Adminhtml_Block_Widget
{
    protected $shipping_rates;

    public function __construct()
    {
        $this->shipping_rates = null;
        parent::__construct();
        $this->setTemplate('iwd/ordermanager/shipping/form.phtml');
    }

    public function getCustomMethodCode()
    {
        return IWD_OrderManager_Model_Shipping::CustomMethodCode;
    }

    public function displayPriceAttribute($code, $strong = false, $separator = '<br/>')
    {
        return $this->helper('adminhtml/sales')->displayPriceAttribute($this->getPriceDataObject(), $code, $strong, $separator);
    }

    public function displayPrices($basePrice, $price, $strong = false, $separator = '<br/>')
    {
        return $this->helper('adminhtml/sales')->displayPrices($this->getPriceDataObject(), $basePrice, $price, $strong, $separator);
    }

    public function displayShippingPriceInclTax($order)
    {
        $shipping = $order->getShippingInclTax();
        if ($shipping) {
            $baseShipping = $order->getBaseShippingInclTax();
        } else {
            $shipping = $order->getShippingAmount() + $order->getShippingTaxAmount();
            $baseShipping = $order->getBaseShippingAmount() + $order->getBaseShippingTaxAmount();
        }
        return $this->displayPrices($baseShipping, $shipping, false, ' ');
    }

    public function getAddress()
    {
        return $this->getOrder()->getShippingAddress();
    }

    public function getShippingRates()
    {
        $order = $this->getOrder();
        $this->shipping_rates = Mage::getModel("iwd_ordermanager/shipping")->getShippingRates($order);
        return $this->shipping_rates;
    }

    public function getActiveMethodRate()
    {
        if ($this->getCustomMethodCode() == $this->getOrder()->getShippingMethod()) {
            $rate = new Varien_Object();
            $rate->setCode($this->getCustomMethodCode());
            $rate->setPrice($this->getOrder()->getShippingAmount());
            $rate->setMethodTitle(Mage::helper('iwd_ordermanager')->__("Custom"));
            $rate->setMethodDescription($this->getOrder()->getShippingDescription());
            return $rate;
        }

        if (is_array($this->shipping_rates)) {
            foreach ($this->shipping_rates as $group) {
                foreach ($group as $code => $rate) {
                    if ($rate->getCode() == $this->getOrder()->getShippingMethod()) {
                        return $rate;
                    }
                }
            }
        }
        return false;
    }

    public function isMethodActive($code)
    {
        return $code === $this->getOrder()->getShippingMethod();
    }

    public function getCarrierName($carrierCode)
    {
        if ($name = Mage::getStoreConfig('carriers/' . $carrierCode . '/title', $this->getOrder()->getStoreId())) {
            return $name;
        }
        return $carrierCode;
    }

    public function getShippingPrice($price, $flag)
    {
        $store = Mage::getModel('core/store')->load($this->getOrder()->getStoreId());
        return Mage::helper('tax')->getShippingPrice($price, $flag, $this->getAddress(), null, $store);
    }

    public function formatPrice($price)
    {
        return Mage::getModel('core/store')
            ->load($this->getOrder()->getStoreId())
            ->convertPrice($price, true);
    }
}