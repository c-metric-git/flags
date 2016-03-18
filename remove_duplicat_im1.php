<?php 
include('app/Mage.php');
//Mage::App('default');
Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
error_reporting(E_ALL | E_STRICT);
Mage::setIsDeveloperMode(true);
set_time_limit(0);
ini_set('memory_limit','1024M');
ini_set('display_errors', 1);
ob_implicit_flush (1);

$mediaApi = Mage::getModel("catalog/product_attribute_media_api");
$_products = Mage::getModel('catalog/product')->getCollection();
$i =0;
$total = count($_products);
$count = 0;
foreach($_products as $_prod)
{
$_product = Mage::getModel('catalog/product')->load($_prod->getId());
$_md5_values = array();

//protected base image
$base_image = $_product->getImage();
if($base_image != 'no_selection')
{
$filepath = Mage::getBaseDir('media') .'/catalog/product' . $base_image ;
if(file_exists($filepath))
$_md5_values[] = md5(file_get_contents($filepath));
}

$i ++;
echo "\r\n processing product $i of $total ";

// Loop through product images
$_images = $_product->getMediaGalleryImages();
if($_images){
foreach($_images as $_image){
//protected base image
if($_image->getFile() == $base_image)
continue;

$filepath = Mage::getBaseDir('media') .'/catalog/product' . $_image->getFile() ;
if(file_exists($filepath))
$md5 = md5(file_get_contents($filepath));
else
continue;

if(in_array($md5, $_md5_values))
{
$mediaApi->remove($_product->getId(), $_image->getFile());
echo "\r\n removed duplicate image from ".$_product->getSku();
$count++;
} else {
$_md5_values[] = $md5;
}

}
}

} 