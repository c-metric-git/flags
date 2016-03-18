<?php

/***************************************************************************

	@extension	: Bestseller Product.

	@copyright	: Copyright (c) 2015 Capacity Web Solutions.

	( http://www.capacitywebsolutions.com )

	@author		: Capacity Web Solutions Pvt. Ltd.

	@support	: magento@capacitywebsolutions.com	

***************************************************************************/



class CapacityWebSolutions_Bestseller_Block_Bestseller extends Mage_Catalog_Block_Product_Abstract // Mage_Core_Block_Template

{

	public function __construct()

	{

		$this->setEnabled((bool)Mage::getStoreConfigFlag("bestseller/general/enabled"));

  		$this->setHeader(Mage::getStoreConfig("bestseller/general/heading"));

		$this->setLimit((int)Mage::getStoreConfig("bestseller/general/number_of_items"));

		$this->setItemsPerRow((int)Mage::getStoreConfig("bestseller/general/number_of_items_per_row"));

		$this->setStoreId(Mage::app()->getStore()->getId());

		$this->setImageHeight((int)Mage::getStoreConfig("bestseller/general/thumbnail_height"));

		$this->setImageWidth((int)Mage::getStoreConfig("bestseller/general/thumbnail_width"));

		$this->setTimePeriod((int)Mage::getStoreConfig("bestseller/general/time_period"));

		$this->setAddToCart((bool)Mage::getStoreConfig('bestseller/general/add_to_cart'));

		$this->setActive((bool)Mage::getStoreConfigFlag("bestseller/general/active"));

		$this->setAddToCompare((bool)Mage::getStoreConfig("bestseller/general/add_to_compare"));

		$this->setProductsPrice((int)Mage::getStoreConfig('bestseller/general/products_price'));

		$this->setReview((int)Mage::getStoreConfig('bestseller/general/review'));

		$this->setOutofStock((int)Mage::getStoreConfig('bestseller/general/out_of_stoke'));

		$this->setChooseProducts((int)Mage::getStoreConfig('bestseller/general/choose_products'));

		$this->setSortOrder((int)Mage::getStoreConfig('bestseller/general/sort_order_both'));

		$this->setOrderStatus(Mage::getStoreConfig('bestseller/general/order_status'));

	}

	

	public function getBestsellerProduct() { 

		$o_status = $this->getOrderStatus();

		if($o_status != "all"){

			$order_statuses = explode(",",$o_status);

			$order_status =  "'" . implode("','", $order_statuses) . "'";

		}

		

		$productCount = $this->getLimit();

		$timePeriod = ($this->getTimePeriod()) ? $this->getTimePeriod() : 60;

		$storeId    = Mage::app()->getStore()->getId();

		$today = strtotime(date('Y-m-d H:i:s',Mage::getModel('core/date')->gmtTimestamp()));

		$last = $today - (60*60*24*$timePeriod);

		$from = date("Y-m-d H:i:s", $last);

		$to = date("Y-m-d H:i:s", $today);

		

		$products = Mage::getResourceModel('bestseller/product_collection')

			->addAttributeToSelect('*')	

			->addOrderedQty($from, $to)

			//->setStoreId($storeId)

			//->addStoreFilter($storeId)

			->setOrder('ordered_qty', 'desc')

			->setPageSize($productCount);

			Mage::getSingleton('catalog/product_status')

			->addVisibleFilterToCollection($products);  

			Mage::getSingleton('catalog/product_visibility')

			->addVisibleInCatalogFilterToCollection($products);       

			$products->getSelect()->where('order_items.store_id ='.$storeId);

		

		if($o_status != "all"){

			$products->getSelect()->where('order.status IN ('.$order_status.')');

		}
         
		return $products;

	}

	

	public function getProducts($productid) { 

    	$collection = Mage::getModel('catalog/product')->load($productid);

		if($collection->getVisibility()!=1){

			if(!$this->getOutofStock()){

				if($collection->isSalable()){

					return $collection;

				}

				else{

					return false;

				}

			}

			else{

				return $collection; 	

			}

		}else{

			return false;

		}

	}

	

	public function getIdsArr($element){

		return $element['product_id'];

	}

	

	public function getProductIds(){

		$model = Mage::getModel('bestseller/bestseller')->getCollection();

		$product_ids=array_map(array($this,"getIdsArr"), $model->getData());

		return $product_ids;

	}



}

