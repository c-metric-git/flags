<?php

class MWSOrderItem
{
	public $ASIN;
	public $SKU;
	public $OrderId;
	public $OrderItemId;
	public $Title;
	public $QuantityOrdered;
	public $QuantityShipped;
	public $ItemPriceAmount;
	public $ItemPriceCurrency;
	public $ShippingPriceAmount;
	public $ShippingPriceCurrency;
	public $ItemTaxAmount;
	public $ItemTaxCurrency;
	public $ShippingTaxAmount;
	public $ShippingTaxCurrency;
	public $GiftWrapAmount;
	public $GiftWrapCurrency;
	public $GiftWrapTaxAmount;
	public $GiftWrapTaxCurrency;
	public $ShippingDiscountAmount;
	public $ShippingDiscountCurrency;
	public $PromotionDiscountAmount;
	public $PromotionDiscountCurrency;
	public $PromotionIds;
	public $CODFeeAmount;
	public $CODFeeCurrency;
	public $CODFeeDiscountAmount;
	public $CODFeeDiscountCurrency;
	public $GiftMessageText;
	public $GiftWrapLevel;
	
	public function __construct()
	{
		
	}
	
	public function getCSVHeaders()
	{
		return array( "ASIN", "SKU", "OrderId", "OrderItemId", "Title", "QuantityOrdered", "QuantityShipped", "ItemPriceAmount", "ItemPriceCurrency", "ShippingPriceAmount", "ShippingPriceCurrency", "ItemTaxAmount", "ItemTaxCurrency",
					  "ShippingTaxAmount", "ShippingTaxCurrency", "GiftWrapAmount", "GiftWrapCurrency", "GiftWrapTaxAmount", "GiftWrapTaxCurrency", "ShippingDiscountAmount", "ShippingDiscountCurrency", "PromotionDiscountAmount", "PromotionDiscountCurrency", 
					  "PromotionIds", "CODFeeAmount", "CODFeeCurrency", "CODFeeDiscountAmount", "CODFeeDiscountCurrency", "GiftMessageText", "GiftWrapLevel" );
	}
		
	public function getShortCSVHeaders()
	{
		return array( "Title", "ItemPriceAmount", "ItemPriceCurrency", "ShippingPriceAmount", "ShippingPriceCurrency" );
	}
		
	public function getCSVRow()
	{
		return array( $this->ASIN, $this->SKU, $this->OrderId, $this->OrderItemId, $this->Title, $this->QuantityOrdered, $this->QuantityShipped, $this->ItemPriceAmount, $this->ItemPriceCurrency, $this->ShippingPriceAmount, $this->ShippingPriceCurrency,
			$this->ItemTaxAmount, $this->ItemTaxCurrency, $this->ShippingTaxAmount, $this->ShippingTaxCurrency, $this->GiftWrapAmount, $this->GiftWrapCurrency, $this->GiftWrapTaxAmount, $this->GiftWrapTaxCurrency, $this->ShippingDiscountAmount,
			$this->ShippingDiscountCurrency, $this->PromotionDiscountAmount, $this->PromotionDiscountCurrency, $this->PromotionIds, $this->CODFeeAmount, $this->CODFeeCurrency, $this->CODFeeDiscountAmount,
			$this->CODFeeDiscountCurrency, $this->GiftMessageText, $this->GiftWrapLevel); 
	}
	
	public function getShortCSVRow()
	{
		return array($this->Title, $this->ItemPriceAmount, $this->ItemPriceCurrency, $this->ShippingPriceAmount, $this->ShippingPriceCurrency);
	}
}
