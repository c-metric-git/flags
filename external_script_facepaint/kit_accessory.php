<?php 
define('MAGENTO', realpath(dirname(__FILE__)));
require_once '../app/Mage.php';
Mage::app();
set_time_limit(0);

$myFile = "csv/kit_accessory.csv"; // File in which attribute option values are there.
$f = fopen($myFile, "r");
 
 $myValue = array();

 while($line = fgetcsv($f, filesize($myFile),",")){

 array_push($myValue,$line);
 }
//echo "<pre>";
//print_r($myValue);
//exit;
$attribute_added = array();
for($i=0;$i<count($myValue);$i++)
 {
 $attribute_model = Mage::getModel('eav/entity_attribute');
 $attribute_code  = $attribute_model->getIdByCode('catalog_product', 'kit_accessory');
 $attribute       = $attribute_model->load($attribute_code);
 
if(!attributeValueExists('kit_accessory', $myValue[$i]))
 {
	
$value['option'] = array($myValue[$i]);
if(!in_array($value['option'][0][0],$attribute_added) && $value['option'][0][0]!='')
{
  $option['option'][0] = $value['option'][0][0];
  $option['option'][3] = $value['option'][0][0];
  $order['option'] = 	$value['option'][0][1];
 $result = array('value' => $option,'order' =>$order );
 //echo "<pre>";
// print_r($value);
 //print_r($result);
 $attribute_added[] = $option['option'][0]; 
 $attribute->setData('option',$result); 
 $attribute->save();
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