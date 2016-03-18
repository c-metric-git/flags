<?php
static $config = NULL;
static $log = NULL;

// Error Handler
function error_handler_for_export($errno, $errstr, $errfile, $errline) {
	global $config;
	global $log;
	
	switch ($errno) {
		case E_NOTICE:
		case E_USER_NOTICE:
			$errors = "Notice";
			break;
		case E_WARNING:
		case E_USER_WARNING:
			$errors = "Warning";
			break;
		case E_ERROR:
		case E_USER_ERROR:
			$errors = "Fatal Error";
			break;
		default:
			$errors = "Unknown";
			break;
	}
		
	if (($errors=='Warning') || ($errors=='Unknown')) {
		return true;
	}

	if ($config->get('config_error_display')) {
		echo '<b>' . $errors . '</b>: ' . $errstr . ' in <b>' . $errfile . '</b> on line <b>' . $errline . '</b>';
	}
	
	if ($config->get('config_error_log')) {
		$log->write('PHP ' . $errors . ':  ' . $errstr . ' in ' . $errfile . ' on line ' . $errline);
	}

	return true;
}


function fatal_error_shutdown_handler_for_export()
{
	$last_error = error_get_last();
	if ($last_error['type'] === E_ERROR) {
		// fatal error
		error_handler_for_export(E_ERROR, $last_error['message'], $last_error['file'], $last_error['line']);
	}
}

class ModelSaleCustomerBulk extends Model {	
	public function getTotalCustomersByEmail($email) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "customer WHERE email = '" . $this->db->escape($email) . "'");
		
		return $query->row['total'];
	}
	
	public function getCustomersZone($country,$zone) {
		$zoneinfo=array();
		$query = $this->db->query("SELECT country_id FROM " . DB_PREFIX . "country  WHERE LCASE(name) = '" . strtolower($country) . "'");		
		if(isset($query->row['country_id']))$zoneinfo['country'] = $query->row['country_id'];
		
		$query = $this->db->query("SELECT zone_id FROM " . DB_PREFIX . "zone WHERE LCASE(name) = '" . strtolower($zone) . "'");		
		if(isset($query->row['zone_id']))$zoneinfo['zone'] = $query->row['zone_id'];
		
		if(isset($zoneinfo['country']) && isset($zoneinfo['zone'])){
			return $zoneinfo;
		}else{
			return false;
		}
	}
	
	public function getCustomerGroups() {		
		$CustomerGroups = array();
		
		$version_int = (int)str_replace(".", "", substr(VERSION, 0, 5));
		if($version_int < 153){
			$sql = "SELECT `customer_group_id`, `name` FROM `".DB_PREFIX."customer_group`;";
		}else{
			$languageId = (int)$this->config->get('config_language_id');
			$sql = "SELECT `customer_group_id`, `name` FROM `".DB_PREFIX."customer_group_description` WHERE `language_id`=$languageId;";
		}	
		
		$result = $this->db->query( $sql );
		if ($result->rows) {
			foreach ($result->rows as $row) {
				$CustomerGroups[strtoupper($row['name'])] = $row['customer_group_id'];
			}
		}

		return $CustomerGroups;
	}
	
	public function upload($filename) {
		global $config;
		global $log;
		$config = $this->config;
		$log = $this->log;
		set_error_handler('error_handler_for_export',E_ALL);
		register_shutdown_function('fatal_error_shutdown_handler_for_export');
		
		ini_set("memory_limit","512M");
		ini_set("max_execution_time",180);
		//set_time_limit( 60 );
		chdir( DIR_SYSTEM . 'PHPExcel' );
		require_once( 'Classes/PHPExcel.php' );
		chdir( DIR_APPLICATION );
		$inputFileType = PHPExcel_IOFactory::identify($filename);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);
		$reader = $objReader->load($filename);
		$ok = $this->validateUpload( $reader );
		if (!$ok) {
			return FALSE;
		}
		$this->addcustomers( $reader);
		return $ok;
	}
	
	public function addcustomers( &$reader ){
		$this->load->model('sale/customer');
		$version_int = (int)str_replace(".", "", substr(VERSION, 0, 5));
		$customergroups = $this->getCustomerGroups();
		$data = $reader->getSheet(0);
		$isFirstRow = TRUE;
		$i = 0;
		$k = $data->getHighestRow();
		for ($i=0; $i<$k; $i+=1) {
			if ($isFirstRow) {
				$isFirstRow = FALSE;
				continue;
			}
			$customer = array();
			$customer['firstname'] =  $this->getCell($data,$i,1);
			$customer['lastname'] = $this->getCell($data,$i,2);
			$customer['email'] =  trim($this->getCell($data,$i,3,''));
			if ($customer['email']=="" || $this->getTotalCustomersByEmail($customer['email'])) continue;
			if ((strlen(utf8_decode($customer['email'])) > 96) || !preg_match('/^[^\@]+@.*\.[a-z]{2,6}$/i', $customer['email'])) continue;
			$customer['telephone'] = $this->getCell($data,$i,4);
			$customer['fax'] =$this->getCell($data,$i,5);
			$customer['newsletter'] = ((strtoupper($this->getCell($data,$i,7))=="YES") || (strtoupper($this->getCell($data,$i,7))=="ENABLED")) ? 1 : 0;
			$customer['customer_group_id'] = isset($customergroups[strtoupper($this->getCell($data,$i,8))]) ? $customergroups[strtoupper($this->getCell($data,$i,8))] : $this->config->get('config_customer_group_id');
			$customer['password'] =trim($this->getCell($data,$i,6));
			$customer['status'] = ((strtoupper($this->getCell($data,$i,9))=="YES") || (strtoupper($this->getCell($data,$i,9))=="ENABLED")) ? 1 : 0;
			$customer['zoneinfo'] = $this->getCustomersZone($this->getCell($data,$i,17), $this->getCell($data,$i,18));
			if($customer['zoneinfo']){
				$customer['address'][1]['default'] =1;
				$customer['address'][1]['firstname'] =$this->getCell($data,$i,10);
				$customer['address'][1]['lastname'] =$this->getCell($data,$i,11);
				$customer['address'][1]['company'] =$this->getCell($data,$i,12);
				$customer['address'][1]['address_1'] =$this->getCell($data,$i,13);
				$customer['address'][1]['address_2'] =$this->getCell($data,$i,14);
				$customer['address'][1]['city'] =$this->getCell($data,$i,15);
				$customer['address'][1]['postcode'] =$this->getCell($data,$i,16);
				$customer['address'][1]['country_id'] =$customer['zoneinfo']['country'];
				$customer['address'][1]['zone_id'] =$customer['zoneinfo']['zone'];
				if($version_int >= 153){
					$customer['address'][1]['company_id'] =$this->getCell($data,$i,19);
					$customer['address'][1]['tax_id'] =$this->getCell($data,$i,20);
				}
			}
			$this->model_sale_customer->addCustomer($customer);
		}
	}
	
	function populateWorksheet( &$worksheet, &$database, $customersid )
	{	
		$version_int = (int)str_replace(".", "", substr(VERSION, 0, 5));
		$i = 0;
		$j = 0;
		$worksheet->writeString( $i, $j++, 'First Name' );
		$worksheet->writeString( $i, $j++, 'Last Name' );
		$worksheet->writeString( $i, $j++, 'E-Mail' );
		$worksheet->writeString( $i, $j++, 'Telephone' );
		$worksheet->writeString( $i, $j++, 'Fax' );
		$worksheet->writeString( $i, $j++, 'Password' );
		$worksheet->writeString( $i, $j++, 'Newsletter' );
		$worksheet->writeString( $i, $j++, 'Customer Group' );
		$worksheet->writeString( $i, $j++, 'Status' );
		//$worksheet->writeString( $i, $j++, 'Approved' );
		$worksheet->writeString( $i, $j++, 'Address First Name' );
		$worksheet->writeString( $i, $j++, 'Address Last Name' );
		$worksheet->writeString( $i, $j++, 'Company' );
		$worksheet->writeString( $i, $j++, 'Address 1' );
		$worksheet->writeString( $i, $j++, 'Address 2' );
		$worksheet->writeString( $i, $j++, 'City' );
		$worksheet->writeString( $i, $j++, 'Postcode' );
		$worksheet->writeString( $i, $j++, 'Country' );
		$worksheet->writeString( $i, $j++, 'Region/State' );
		if($version_int >= 153){
			$worksheet->writeString( $i, $j++, 'Company ID' );
			$worksheet->writeString( $i, $j++, 'Tax ID' );
		}
		// The actual options data
		
		$i += 1;
		$j = 0;
		$version_int = (int)str_replace(".", "", substr(VERSION, 0, 5));
		if($version_int < 153){
			$cg = DB_PREFIX."customer_group cg WHERE c.customer_group_id=cg.customer_group_id";
			$aid = "";
		}else{
			$languageId = (int)$this->config->get('config_language_id');
			$cg = DB_PREFIX."customer_group_description cg WHERE language_id='" .$languageId. "' AND c.customer_group_id=cg.customer_group_id";
			$aid = ",a.company_id,a.tax_id";
		}
		$query  = "SELECT c.*,(SELECT cg.name FROM ".$cg.") AS groupname,a.firstname AS afirstname,a.lastname AS alastname,a.company,a.address_1,a.address_2,a.city,a.postcode".$aid.",(SELECT cu.name FROM ".DB_PREFIX."country cu WHERE a.country_id=cu.country_id) AS countryname,(SELECT z.name FROM ".DB_PREFIX."zone z WHERE a.zone_id=z.zone_id) AS zonename FROM ".DB_PREFIX."customer c LEFT OUTER JOIN ".DB_PREFIX."address a ON c.address_id=a.address_id WHERE c.customer_id IN ( ".$customersid." )";
		$result = $database->query( $query );
		foreach ($result->rows as $row) {			
			$worksheet->writeString( $i, $j++, $row['firstname']);
			$worksheet->writeString( $i, $j++, $row['lastname']);
			$worksheet->writeString( $i, $j++, $row['email']);
			$worksheet->writeString( $i, $j++, $row['telephone']);
			$worksheet->writeString( $i, $j++, $row['fax']);
			$worksheet->writeString( $i, $j++, '');
			$worksheet->writeString( $i, $j++, ($row['newsletter']) ? "Enabled" : "Disabled");
			$worksheet->writeString( $i, $j++, $row['groupname']);
			$worksheet->writeString( $i, $j++, ($row['status']) ? "Enabled" : "Disabled");
			//$worksheet->writeString( $i, $j++, ($row['approved']) ? "Yes" : "No");
			$worksheet->writeString( $i, $j++, $row['afirstname']);
			$worksheet->writeString( $i, $j++, $row['alastname']);
			$worksheet->writeString( $i, $j++, $row['company']);
			$worksheet->writeString( $i, $j++, $row['address_1']);
			$worksheet->writeString( $i, $j++, $row['address_2']);
			$worksheet->writeString( $i, $j++, $row['city']);
			$worksheet->writeString( $i, $j++, $row['postcode']);
			$worksheet->writeString( $i, $j++, $row['countryname']);
			$worksheet->writeString( $i, $j++, $row['zonename']);
			if($version_int >= 153){
				$worksheet->writeString( $i, $j++, $row['company_id']);
				$worksheet->writeString( $i, $j++, $row['tax_id']);
			}
			$i += 1;
			$j = 0;
		}
	}

	public function export($selectid) {
		global $config;
		global $log;
		$config = $this->config;
		$log = $this->log;
		set_error_handler('error_handler_for_export',E_ALL);
		register_shutdown_function('fatal_error_shutdown_handler_for_export');
		$database =& $this->db;

		// We use the package from http://pear.php.net/package/Spreadsheet_Excel_Writer/
		chdir( DIR_SYSTEM . 'pear' );
		require_once "Spreadsheet/Excel/Writer.php";
		chdir( DIR_APPLICATION );
		
		// Creating a workbook
		$workbook = new Spreadsheet_Excel_Writer();
		$workbook->setTempDir(DIR_CACHE);
		$workbook->setVersion(8); // Use Excel97/2000 BIFF8 Format
		
		// sending HTTP headers
		$workbook->send('download_customers.xls');
		
		// Creating the categories worksheet
		$worksheet =& $workbook->addWorksheet('sheet');
		$worksheet->setInputEncoding ( 'UTF-8' );
		$this->populateWorksheet( $worksheet, $database, $selectid); 
		//$worksheet->freezePanes(array(1, 1, 1, 1));

		$workbook->close();
		
		// Clear the spreadsheet caches
		$this->clearSpreadsheetCache();
		exit;
	}	
	
	public function getCell(&$worksheet,$row,$col,$default_val='') {
		$col -= 1; // we use 1-based, PHPExcel uses 0-based column index
		$row += 1; // we use 0-based, PHPExcel used 1-based row index
		return ($worksheet->cellExistsByColumnAndRow($col,$row)) ? $worksheet->getCellByColumnAndRow($col,$row)->getValue() : $default_val;
	}
	
	protected function clearSpreadsheetCache() {
		$files = glob(DIR_CACHE . 'Spreadsheet_Excel_Writer' . '*');
		
		if ($files) {
			foreach ($files as $file) {
				if (file_exists($file)) {
					@unlink($file);
					clearstatcache();
				}
			}
		}
	}	
	
	public function validateUpload( &$reader )
	{
		if (!$this->validateProducts( $reader )) {
			error_log(date('Y-m-d H:i:s - ', time())."\n",3,DIR_LOGS."error.txt");
			return FALSE;
		}
		return TRUE;
	}
	
	public function validateProducts( &$reader )
	{
		$expectedProductHeading = array
		( "First Name", "Last Name", "E-Mail", "Telephone", "Fax", "Password", "Newsletter", "Customer Group", "Status", "Approved", "Address First Name", "Address Last Name", "Company", "Address 1", "Address 2", "City", "Postcode", "Country", "Region/State");
		$data =& $reader->getSheet(0);
		return true;//$this->validateHeading( $data, $expectedProductHeading );
	}
	
	public function validateHeading( &$data, &$expected ) {
		$heading = array();
		$k = PHPExcel_Cell::columnIndexFromString( $data->getHighestColumn() );
		if ($k != count($expected)) {
			return FALSE;
		}
		$i = 0;
		for ($j=1; $j <= $k; $j+=1) {
			$heading[] = $this->getCell($data,$i,$j);
		}
		$valid = TRUE;
		for ($i=0; $i < count($expected); $i+=1) {
			if (!isset($heading[$i])) {
				$valid = FALSE;
				break;
			}
			if (strtolower($heading[$i]) != strtolower($expected[$i])) {
				$valid = FALSE;
				break;
			}
		}
		return $valid;
	}
}
?>