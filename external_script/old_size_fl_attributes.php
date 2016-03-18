<?php 
define('MAGENTO', realpath(dirname(__FILE__)));
require_once '../app/Mage.php';
Mage::app();

$myFile = "csv/size_fl_attributes.csv"; // File in which attribute option values are there.
 $f = fopen($myFile, "r");
 
 $myValue = array();

 while($line = fgets($f, filesize($myFile))){

 array_push($myValue,$line);
 }
//echo "<pre>";
//print_r($myValue);
$attribute_added = array();
for($i=0;$i<count($myValue);$i++)
 {
 $attribute_model = Mage::getModel('eav/entity_attribute');
 $attribute_code  = $attribute_model->getIdByCode('catalog_product', 'fl_size');
 $attribute       = $attribute_model->load($attribute_code);
 
if(!attributeValueExists('fl_size', $myValue[$i]))
 {
	
$value['option'] = array($myValue[$i]);

$parts = explode(",",$value['option'][0]); 
//echo "<pre>";
//print_r($parts);


$value['option'][0] =  $parts[0];

$order['option'] = $parts[1];
//echo "<pre>";
//print_r($order);
//exit;
if($order['option'] > 0 && !in_array($parts[0],$attribute_added) && $parts[0]!='')
{
 $result = array('value' => $value,'order' => $order);
 echo "<pre>";
 print_r($result);
 $attribute_added[] = $parts[0];
 $res = $attribute->setData('option',$result);
 $attribute->save();
 echo "<pre>";
 print_r($res);
 exit;
}
//echo "<pre>";
 //print_r($result);
//exit;

 echo "successfully done!<br>";
 }
 }
 
function attributeValueExists($arg_attribute, $arg_value)
 {
 $attribute_model        = Mage::getModel('eav/entity_attribute');
 $attribute_options_model= Mage::getModel('eav/entity_attribute_source_table') ;
 
$attribute_code         = $attribute_model->getIdByCode('catalog_product', $arg_attribute);
 $attribute              = $attribute_model->load($attribute_code);
 
$attribute_table        = $attribute_options_model->setAttribute($attribute);
 $options                = $attribute_options_model->getAllOptions(false);
 
foreach($options as $option)
 {
 if ($option['label'] == $arg_value)
 {
 return $option['value'];
 }
 }
 return false;
 }
 ?>