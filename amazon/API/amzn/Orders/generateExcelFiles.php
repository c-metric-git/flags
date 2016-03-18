<?php
$BASEDIR = $_SERVER['DOCUMENT_ROOT'].'/'.FOLDER_NAME.'';

# include parseCSV class.
require_once($_SERVER['DOCUMENT_ROOT'] .'/projects/inventory_api/parsecsv/parsecsv.lib.php');

$url = $BASEDIR.'/amazon-unshipped-orders.csv';
#url = 'http://tops-tech.com/amzn_api/amazon-unshipped-orders.csv'
$datestamp = strftime('%Y%m%d');
$original_csv = array();
//$BASEDIR='/home7/topstech/public_html/amzn_api';

$amz_array = array();
$ovation_array = array();
$patterson_array = array();
$isg_array = array();
$medline_array = array();
$indemed_array = array();
$nbs_array = array();

$all_orderids_array = array();
$ovation_orderids_array = array();
$patterson_orderids_array = array();
$isg_orderids_array = array();
$medline_orderids_array = array();
$indemed_orderids_array = array();

#puts "Fetching #{url}..."
#puts ''
$new_csv=array();
#puts file
#CSV.foreach(url, :headers => :first_row).each do |line|
#CSV.foreach(url) do |line|
$csv = new parseCSV();
# Parse '_books.csv' using automatic delimiter detection...
$csv->auto($url);

/*$i=0;
foreach ($csv->data as $key => $row){
	
	//foreach ($row as $value){
		
		$new_csv[$i] =  $csv->data;
		
	//}
$i+$i+1;	
}*/

echo "<pre>";
	$i=0;
	
	$AMZ_FILE_PATH = 'amazon-templates/AMZ-'.$datestamp.'.csv';
	
	$fp = fopen($AMZ_FILE_PATH , 'w');
	
	$AMZ_TITLE = array('Name', 'Address1', 'Address2', 'City', 'State', 'ZIP', 'SKU', 'ProductName', 'Quantity', 'ShipService', 'PurchaseOrder');
	//foreach ($AMZ_TITLE as $title){
		fputcsv($fp,$AMZ_TITLE);
	//}
	
	//foreach ($csv->titles as $value){
		//$AMZ_TITLE[$i] =  $csv->titles;
		//fputcsv($fp,$AMZ_TITLE[$i]);
		//$i++;
	//}
	//print_r($AMZ_TITLE);exit;
	
	
	
	$i=0;
	
	foreach ($csv->data as $key => $row){
		//foreach ($row as $value){
			array_push($original_csv,$row);
			//$AMZ_TITLE[$i] =  $row;
		//}
		
		/*if($row['order-id']){ 
			echo $row['order-id'].' => ';
			$row['order-id'] = str_replace("'","",strval($row['order-id'])); 
			echo $row['order-id'].'\n';
		}*/
		
		fputcsv($fp, $row);

		// Insert Values into Arrays
		#$string = preg_replace("/[']/", '', $row['order-id']);
		$string = $row['order-id'];
		
		array_push($all_orderids_array,$string);
		#array_push($all_orderids_array,str_replace("'","",$string));
		
		if (strpos($row['sku'],'NMI')>0 && !strpos($row['product-name'],'Ovation') && !strpos($row['product-name'],'Adjustable Plantar Fasciitis') ){
			//echo strpos($row['sku'],'NMI');exit;
			array_push($nbs_array,$row);
			//$nbs_array[$i] = $row;
			continue;
		}
		//else
		if (strpos($row['product-name'],'Ovation')>0 || strpos($row['product-name'],'Adjustable Plantar Fasciitis')>0 ){
			array_push($ovation_array,$row);
			array_push($ovation_orderids_array,$string);
			
			/*$ovation_array[$i] = $row;
			$ovation_orderids_array[$i] = $string;*/
			continue;
		}
		//else
		if (strpos($row['sku'],'CTS')>0){
			array_push($patterson_array,$row);
			array_push($patterson_orderids_array,$string);
			
			/*$patterson_array[$i] = $row;
			$patterson_orderids_array[$i] = $string;*/
			continue;
		}
		//else
		if (strpos($row['sku'],'MD44')>0 || strpos($row['sku'],'MDLN')>0 || strpos($row['sku'],'MD88')>0 ){
			array_push($medline_array,$row);
			array_push($medline_orderids_array,$string);
			
			/*$medline_array = $row;
			$medline_orderids_array =  $string;*/
			continue;
		}
		//else
		if ( (substr_count($row['sku'],'-') == 0 && !strpos($row['product-name'],'Ovation')) || strpos($row['sku'],'IND')>0 || (strpos($row['sku'],'UHS')>0 && substr_count($row['sku'],'-') == 1) ){
			//echo strpos($row['product-name'],'Ovation');exit;
			array_push($indemed_array,$row);
			array_push($indemed_orderids_array,$string);
			
			/*$indemed_array[$i] = $row;
			$indemed_orderids_array[$i] = $string;*/
			continue;
		}
		//else
		if (substr_count($row['sku'],'-') > 1){
			array_push($isg_array,$row);
			array_push($isg_orderids_array,$string);
			
			/*$isg_array[$i] = $row;
			$isg_orderids_array[$i] = $string;*/
			continue;
		}
		array_push($nbs_array,$row);
		
		
		//$nbs_array[$i] = $row;
		
	$i++;	
	} # End Froreach;
	fclose($fp);
	chmod($AMZ_FILE_PATH, 0777);
	
	
	//print_r($AMZ_ROW);exit;
	//print_r($AMZ_TITLE);exit;
	
	/*echo "<br><br>all_orderids_array=> ";
	print_r($all_orderids_array);
	
	echo "<br><br>nbs_array=> ";
	print_r($nbs_array);
	
	echo "<br><br>ovation_array=> ";
	print_r($ovation_array);
	echo "<br><br>ovation_orderids_array=> ";	
	print_r($ovation_orderids_array);

	echo "<br><br>patterson_array=> ";	
	print_r($patterson_array);
	echo "<br><br>patterson_orderids_array=> ";
	print_r($patterson_orderids_array);
	
	echo "<br><br>medline_array=> ";
	print_r($medline_array);
	echo "<br><br>medline_orderids_array=> ";	
	print_r($medline_orderids_array);
	
	echo "<br><br>indemed_array=> ";	
	print_r($indemed_array);
	echo "<br><br>indemed_orderids_array=> ";	
	print_r($indemed_orderids_array);
	
	echo "<br><br>isg_array=> ";
	print_r($isg_array);
	echo "<br><br>isg_orderids_array=> ";	
	print_r($isg_orderids_array);*/
	
	
	// Create Ovation CSV
	//if(count($ovation_array)>0){	
		$Ovation_FILE_PATH = 'amazon-templates/Ovation-'.$datestamp.'.csv';
		$fp_Ovation = fopen($Ovation_FILE_PATH , 'w');
		
		$Ovation_TITLE = array('Name', 'Address1', 'Address2', 'City', 'State', 'ZIP', 'SKU', 'ProductName', 'Quantity', 'ShipService', 'PurchaseOrder');
	
			fputcsv($fp_Ovation,$Ovation_TITLE);
			
		$i=0;
		foreach ($ovation_array as $key => $row){
				fputcsv($fp_Ovation, $row);
		}
		fclose($fp_Ovation);
		chmod($Ovation_FILE_PATH, 0777);
	//}
	
	
	
	// Create NBS Array CSV
	//if(count($nbs_array)>0){	
		$NBS_FILE_PATH = 'amazon-templates/NBS-'.$datestamp.'.csv';
		$fp_NBS = fopen($NBS_FILE_PATH , 'w');
		
		$NBS_TITLE = array('Name', 'Address1', 'Address2', 'City', 'State', 'ZIP', 'SKU', 'ProductName', 'Quantity', 'ShipService', 'PurchaseOrder');
	
			fputcsv($fp_NBS,$NBS_TITLE);
			
		$i=0;
		foreach ($nbs_array as $key => $row){
				fputcsv($fp_NBS, $row);
		}
		fclose($fp_NBS);
		chmod($NBS_FILE_PATH, 0777);
	//}
	
	
	// Create Patterson Array CSV
	//if(count($patterson_array)>0){	
		$Patterson_FILE_PATH = 'amazon-templates/Patterson-'.$datestamp.'.csv';
		$fp_Patterson = fopen($Patterson_FILE_PATH , 'w');
		
		$Patterson_TITLE = array('Name', 'Address1', 'Address2', 'City', 'State', 'ZIP', 'SKU', 'ProductName', 'Quantity', 'ShipService', 'PurchaseOrder');
	
			fputcsv($fp_Patterson,$Patterson_TITLE);
			
		$i=0;
		foreach ($patterson_array as $key => $row){
				fputcsv($fp_Patterson, $row);
		}
		fclose($fp_Patterson);
		chmod($Patterson_FILE_PATH, 0777);
	//}
	
	// Create Medline Array CSV
	//if(count($medline_array)>0){	
		$Medline_FILE_PATH = 'amazon-templates/Medline-'.$datestamp.'.csv';
		$fp_Medline = fopen($Medline_FILE_PATH , 'w');
		
		$Medline_TITLE = array('Name', 'Address1', 'Address2', 'City', 'State', 'ZIP', 'SKU', 'ProductName', 'Quantity', 'ShipService', 'PurchaseOrder');
	
			fputcsv($fp_Medline,$Medline_TITLE);
			
		$i=0;
		foreach ($medline_array as $key => $row){
				fputcsv($fp_Medline, $row);
		}
		fclose($fp_Medline);
		chmod($Medline_FILE_PATH, 0777);
	//}
	
	
	// Create Indemed Array CSV
	//if(count($indemed_array)>0){	
		$Indemed_FILE_PATH = 'amazon-templates/Indemed-'.$datestamp.'.csv';
		$fp_Indemed = fopen($Indemed_FILE_PATH , 'w');
		
		$Indemed_TITLE = array('Name', 'Address1', 'Address2', 'City', 'State', 'ZIP', 'SKU', 'ProductName', 'Quantity', 'ShipService', 'PurchaseOrder');
	
			fputcsv($fp_Indemed,$Indemed_TITLE);
			
		$i=0;
		foreach ($indemed_array as $key => $row){
				fputcsv($fp_Indemed, $row);
		}
		fclose($fp_Indemed);
		chmod($Indemed_FILE_PATH, 0777);
	//}
	
	// Create NBS Array CSV
	//if(count($isg_array)>0){	
		$ISG_FILE_PATH = 'amazon-templates/ISG-'.$datestamp.'.csv';
		$fp_ISG = fopen($ISG_FILE_PATH , 'w');
		
		$ISG_TITLE = array('Name', 'Address1', 'Address2', 'City', 'State', 'ZIP', 'SKU', 'ProductName', 'Quantity', 'ShipService', 'PurchaseOrder');
	
			fputcsv($fp_ISG,$ISG_TITLE);
			
		$i=0;
		foreach ($isg_array as $key => $row){
				fputcsv($fp_ISG, $row);
		}
		fclose($fp_ISG);
		chmod($ISG_FILE_PATH, 0777);
	//}
	
	
	
	$order_ids=array();
	
	// NoW Create Orders CSV Files
	
	# all_orderids_array CSV
	$all_orderids_array_FILE_PATH = 'amazon-templates/all.csv';
	$fp_all_orderids_array = fopen($all_orderids_array_FILE_PATH , 'w');

		fputcsv($fp_all_orderids_array,$all_orderids_array);	
	
	fclose($fp_all_orderids_array);
	chmod($all_orderids_array_FILE_PATH, 0777);
	
	
	# ovation_orderids_array CSV
	$ovation_orderids_array_FILE_PATH = 'amazon-templates/ovation.csv';
	$fp_ovation_orderids_array = fopen($ovation_orderids_array_FILE_PATH , 'w');

		fputcsv($fp_ovation_orderids_array,$ovation_orderids_array);	
	
	fclose($fp_ovation_orderids_array);
	chmod($ovation_orderids_array_FILE_PATH, 0777);
	
	# patterson_orderids_array CSV
	$patterson_orderids_array_FILE_PATH = 'amazon-templates/patterson.csv';
	$fp_patterson_orderids_array = fopen($patterson_orderids_array_FILE_PATH , 'w');

		fputcsv($fp_patterson_orderids_array,$patterson_orderids_array);	
	
	fclose($fp_patterson_orderids_array);
	chmod($patterson_orderids_array_FILE_PATH, 0777);
	
	# isg_orderids_array CSV
	$isg_orderids_array_FILE_PATH = 'amazon-templates/isg.csv';
	$fp_isg_orderids_array = fopen($isg_orderids_array_FILE_PATH , 'w');

		fputcsv($fp_isg_orderids_array,$isg_orderids_array);	
	
	fclose($fp_isg_orderids_array);
	chmod($isg_orderids_array_FILE_PATH, 0777);
	
	
	# medline_orderids_array CSV
	$medline_orderids_array_FILE_PATH = 'amazon-templates/medline.csv';
	$fp_medline_orderids_array = fopen($medline_orderids_array_FILE_PATH , 'w');

		fputcsv($fp_medline_orderids_array,$medline_orderids_array);	
	
	fclose($fp_medline_orderids_array);
	chmod($medline_orderids_array_FILE_PATH, 0777);
	
	
	# indemed_orderids_array CSV
	$indemed_orderids_array_FILE_PATH = 'amazon-templates/indemed.csv';
	$fp_indemed_orderids_array = fopen($indemed_orderids_array_FILE_PATH , 'w');

		fputcsv($fp_indemed_orderids_array,$indemed_orderids_array);	
	
	fclose($fp_indemed_orderids_array);
	chmod($indemed_orderids_array_FILE_PATH, 0777);
	
	
	echo "<br>";
	echo "Original CSV:\t\t".count($original_csv)." record(s)<br>";
	echo "AMZ-".$datestamp.":\t\t".count($amz_array)." record(s)<br>";
	echo "Patterson-".$datestamp.":\t".count($patterson_array)." record(s)<br>";
	echo "Indemed-".$datestamp.":\t".count($indemed_array)." record(s)<br>";
	echo "ISG-".$datestamp.":\t\t".count($isg_array)." record(s)<br>";
	echo "NBS-".$datestamp.":\t\t".count($nbs_array)." record(s)<br>";
	echo "Medline-".$datestamp.":\t".count($medline_array)." record(s)<br>";
	echo "--------------------------------------<br>";
	echo "Total:\t\t\t".(count($patterson_array) + count($indemed_array) + count($isg_array) + count($nbs_array) + count($medline_array))." record(s)<br><br>";

	echo 'Excel files successfully generated. You may now close this window.<br>';

	
exit

?>