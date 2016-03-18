<?php 
require_once '../app/Mage.php';
Mage::app();
$products = Mage::getModel('catalog/product')->getCollection();
    $products->addStoreFilter(2)
	->setPageSize(50)
	->addAttributeToFilter('type_id','simple')
	->addAttributeToFilter(
    'status',
    array('eq' => Mage_Catalog_Model_Product_Status::STATUS_ENABLED))
	->addAttributeToSelect('*');
 	Mage::getSingleton('cataloginventory/stock')
    ->addInStockFilterToCollection($products);
echo "<pre>";
print_r($products->getSize());
exit;
//Mage::getModel('catalog/product')->getCollection();
//print_r(get_class_methods(Mage::getResourceModel('cataloginventory/stock_item_collection')));
?>