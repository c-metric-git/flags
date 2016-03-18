<?php
set_time_limit(0);
ini_set('memory_limit', '1024M');
include_once "../app/Mage.php";
Mage::init();
$app = Mage::app('default');


function readCSV($csvFile){
 $file_handle = fopen($csvFile, 'r');
 while (!feof($file_handle) ) {
  $line_of_text[] = fgetcsv($file_handle, 1024);
 }
 fclose($file_handle);
 return $line_of_text;
}

// Set path to CSV file
$csvFile = 'csv/customer.csv';
//calling the function
$csv = readCSV($csvFile);
$arr = array();

if(!empty($csv)){    
	$fp1 = fopen('customer.csv', 'w');
	foreach ($csv as $fields) {
		if($fields[10]=="")
		{
			$fields[10]="test";
		}

		if($fields[13]=="")
		{
			$fields[13]="test";
		}
		if($fields[25]=="")
		{
			$fields[25]="New York";
		}
		if($fields[27]=="")
		{
			$fields[27]="US";
		}
		if($fields[29]=="")
		{
			$fields[29]="test";
		}
		if($fields[30]=="")
		{
			$fields[30]="test";
		}
		if($fields[32]=="")
		{
			$fields[32]="123";
		}
		if($fields[35]=="")
		{
			$fields[35]="New York";
		}
		if($fields[37]=="")
		{
			$fields[37]="123";
		}
   echo "<pre>";
    //print_r($fields);
	//fputcsv($fp1, $fields);
}
fclose($fp1);

  echo "successfully done";
}else{
   echo 'Csv is empty'; 
    
}

?>
