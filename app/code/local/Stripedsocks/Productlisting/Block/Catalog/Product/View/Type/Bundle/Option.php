<?php
/**
 * Cart crosssell list
 *
 * @category   Mage
 * @package    Mage_Checkout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Stripedsocks_Productlisting_Block_Catalog_Product_View_Type_Bundle_Option extends Mage_Checkout_Block_Block_Catalog_Product_View_Type_Bundle_Option
{
    public function getSelectionTitlePrice($_selection, $includeContainer = true)
    { echo "tes";exit;
        $price = $this->getProduct()->getPriceModel()->getSelectionPreFinalPrice($this->getProduct(), $_selection, 1);
        $this->setFormatProduct($_selection);
        $priceTitle = $this->escapeHtml($_selection->getName());
        $priceTitle .= ' &nbsp; ' . ($includeContainer ? '<span class="price-notice">' : '')
            . '+' . $this->formatPriceString($price, $includeContainer)
            . ($includeContainer ? '</span>' : '');
        return 'test';
    }
}
