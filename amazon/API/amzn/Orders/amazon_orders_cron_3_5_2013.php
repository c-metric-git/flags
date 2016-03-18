#!/usr/bin/php
<?php
echo "dasdasdsad";
echo $BASEDIR = $_SERVER['DOCUMENT_ROOT'];
	exit;
				
	//error_reporting(E_ALL);
	$system_path = 'system';
	if (realpath($system_path) !== FALSE)
	{
		$system_path = realpath($system_path).'/';
	}
	$system_path = rtrim($system_path, '/').'/';
	define('BASEPATH', str_replace("\\", "/", $system_path));

	include_once("../../../application/config/database.php");
	/** your database credentials **/
	define('AMAZON_DB_NAME', $db['default']['database']);
	define('AMAZON_DB_HOST', $db['default']['hostname']);
	define('AMAZON_DB_USERNAME', $db['default']['username']);
	define('AMAZON_DB_PASSWORD', $db['default']['password']);
	
	$BASEDIR = $_SERVER['DOCUMENT_ROOT'].'/'.FOLDER_NAME.'';
	$userid = $_REQUEST['userid'];
	if($userid==""){
		die("UserID not found..");
	}else{
		define('userid', $userid );
	}
	
		
	//$BASEDIR = 'c:/dev/src/php/amzordermails/amzordermails';


		/** your amazon mws credentials **/
		define('AMAZON_MWS_MERCHANT_ID', 'A1TBXT81I6OFLY');
		define('AMAZON_MWS_MARKETPLACE_ID', 'ATVPDKIKX0DER');
		define('AMAZON_MWS_ACCESS_KEY', 'AKIAJ27HFTJITXJPSKHA');
		define('AMAZON_MWS_SECRET_KEY', 'oOVhQMDZ5kt2UEBmCLT9d3D4YtVn6cLMvNOduavK');
		
		define('AMAZON_CBA_MARKETPLACE_ID', 'AZ4B0ZS3LGLX');
		
		/** your CSV file location **/
		define('AMAZON_CSV_UNSHIPPED_ORDERS_URI', $BASEDIR.'/amazon-unshipped-orders.csv');
		
		/** your e-mail template file locations **/
		define('AMAZON_MAIL_TEMPLATE_DAY_8_BEFORE', $BASEDIR.'/amazon-order-mail-template-8-days-before.htm');
		define('AMAZON_MAIL_TEMPLATE_DAY_16_BEFORE', $BASEDIR.'/amazon-order-mail-template-16-days-before.htm');
		define('AMAZON_MAIL_TEMPLATE_DAY_24_BEFORE', $BASEDIR.'/amazon-order-mail-template-24-days-before.htm');

		define('AMAZON_MAIL_SUPPLIER_ADDRESS_BCC', 'virendra_chunara@tops-int.com'); // all supplier mails will be sent to this email as well
		define('AMAZON_MAIL_SUPPLIER_ADDRESS_REPLYTO', 'virendra_chunara@tops-int.com'); // all supplier mails will be sent with this reply-to address
		define('AMAZON_MAIL_SUPPLIER_SEND_MAIL_TO', ''); // all supplier mails will be sent to this email (!!! DEBUG !!!)
		
		/** your e-mail configuration **/
		define('AMAZON_MAIL_FROM', 'virendra_chunara@tops-int.com');
		define('AMAZON_MAIL_BCC', '');
		define('AMAZON_MAIL_SUBJECT', 'Your recent order from Nationwide Surgical on Amazon.com');
		
		/** (!!!) debug setting (!!!) **/
		//define('AMAZON_SEND_MAIL_TO', 'amazon-mail@lifehousemedical.com'); // set this value to a valid e-mail address if you would like to send all e-mails to this address
		define('AMAZON_SEND_MAIL_TO', ''); // set this value to a valid e-mail address if you would like to send all e-mails to this address
		define('AMAZON_DEBUG', 1); // set this value to 0 if no verbose logging statements should be written, only errors and exceptions are logged, set to 1 otherwise
	
		
		
		
		// Sears Cridencials
		define('Sears_Email', "Web@waresangears.com");
		define('Sears_Password', "test12345");



set_include_path(get_include_path() . PATH_SEPARATOR . $BASEDIR . 'amazon.mws');



$RUBY_SCRIPT = $BASEDIR . '/generateExcelFiles.rb';
$RUBY = 'E:/xampp/Ruby/bin/ruby';
/*$SUPPLIERS = array(
    'ovation' => array(
            'template' => $BASEDIR."/amazon-templates/amazon-order-mail-template-ovation.htm",
            'subject' => 'Ovation - New Drop Ship Orders (Please Review Instructions)',
            'address' => 'bhaines@ovationmed.com',
            'attachment' => $BASEDIR."/amazon-templates/Ovation-{{datestamp}}.xlsx",
            'order-file' => $BASEDIR."/amazon-templates/ovation.csv",
            ),
    'patterson' => array(
            'template' => $BASEDIR."/amazon-templates/amazon-order-mail-template-patterson.htm",
            'subject' => 'Patterson - New Drop Ship Orders (Please Review Instructions)',
            'address' => array('customersupport@patterson-medical.com', 'roger.shackelford@pattersonmedical.com'),
            'attachment' => $BASEDIR."/amazon-templates/Patterson-{{datestamp}}.xlsx",
            'order-file' => $BASEDIR."/amazon-templates/patterson.csv",
            ),
    'isg' => array(
            'template' => $BASEDIR."/amazon-templates/amazon-order-mail-template-isg.htm",
            'subject' => 'ISG - New Drop Ship Orders (Please Review Instructions)',
            'address' => 'emailorders.isg@invacare.com',
            'attachment' => $BASEDIR."/amazon-templates/ISG-{{datestamp}}.xlsx",
            'order-file' => $BASEDIR."/amazon-templates/isg.csv",
            ),
    'medline' => array(
            'template' => $BASEDIR."/amazon-templates/amazon-order-mail-template-medline.htm",
            'subject' => 'MEDLINE - New Drop Ship Orders (Please Review Instructions)',
            'address' => 'service@medline.com',
            'attachment' => $BASEDIR."/amazon-templates/Medline-{{datestamp}}.xlsx",
            'order-file' => $BASEDIR."/amazon-templates/medline.csv",
            ),
    'indemed' => array(
            'template' => $BASEDIR."/amazon-templates/amazon-order-mail-template-indemed.htm",
            'subject' => 'INDEMED - New Drop Ship Orders (Please Review Instructions)',
            'address' => 'imcs@indemed.com',
            'attachment' => $BASEDIR."/amazon-templates/Indemed-{{datestamp}}.xlsx",
            'order-file' => $BASEDIR."/amazon-templates/indemed.csv",
            ),
    'owner' => array(
            'template' => $BASEDIR."/amazon-templates/amazon-order-mail-template-owner.htm",
            'subject' => 'Amazon Orders',
            'address' => 'sean@examed.org',
            'attachment' => array(
                $BASEDIR."/amazon-templates/Ovation-{{datestamp}}.xlsx",
                $BASEDIR."/amazon-templates/Patterson-{{datestamp}}.xlsx",
                $BASEDIR."/amazon-templates/ISG-{{datestamp}}.xlsx",
                $BASEDIR."/amazon-templates/Medline-{{datestamp}}.xlsx",
                $BASEDIR."/amazon-templates/Indemed-{{datestamp}}.xlsx",
                $BASEDIR."/amazon-templates/NBS-{{datestamp}}.xlsx",
                $BASEDIR."/amazon-templates/AMZ-{{datestamp}}.xlsx"),
            'order-file' => $BASEDIR."/amazon-templates/all.csv",
            ),
    );*/


require_once 'amazon.mws/Amazon.php';

class AmazonDatabase
{
    private $_link;
    
    public function init()
    {
        $this->_link = mysql_connect(AMAZON_DB_HOST, AMAZON_DB_USERNAME, AMAZON_DB_PASSWORD);
        mysql_select_db(AMAZON_DB_NAME);
        if (!$this->_link) {
            throw new Exception('No database');
        }
		
		
		
        $sql = 'CREATE TABLE IF NOT EXISTS amazon_order_items (
            order_id varchar(19),
			FkUser_id int(15),
            order_item_id varchar(14),
			purchase_date datetime,
            sku varchar(90),
            title varchar(250),
            ship_to_name varchar(90),
            ship_service_level varchar(45),
            buyer_email varchar(90),
			order_total_amount varchar(100) NOT NULL,
			type varchar(80) NOT NULL,
			ship_address_1 varchar(90) NOT NULL,
			ship_address_2 varchar(90) NOT NULL,
			ship_city varchar(90) NOT NULL,
			ship_state varchar(90) NOT NULL,
			ship_postal_code varchar(90) NOT NULL,
            primary key(order_id, order_item_id))';
        mysql_query($sql);
    }
    
    public function dispose()
    {
        if ($this->_link) {
            mysql_close($this->_link);
        }
    }
    
    public function getOrdersShippedBefore($nrOfDays)
    {
        $this->init();
        $date_after = strftime("'%Y-%m-%dT%H:%M:%S'", mktime(0, 0, 0, date("m"), date("d")-$nrOfDays, date("Y")));
        $date_before = strftime("'%Y-%m-%dT%H:%M:%S'", mktime(23, 59, 59, date("m"), date("d")-$nrOfDays, date("Y")));
        $sql = 'SELECT * FROM order_items WHERE purchase_date >= ' . $date_after . ' AND purchase_date <= ' . $date_before;
        //App::debug($sql);
        $result = mysql_query($sql, $this->_link);
        $orders = array();
        if (!$result) {
            throw new Exception('Unable to read shipped orders from database');
        } else {
            while ( ($row = mysql_fetch_assoc($result)) !== false) {
                $order = array();
                foreach($row as $name => $value) {
                    $order[$name] = $value;
                }
                $orders[] = $order;
            }
        }
        $this->dispose();
        return $orders;
    }

    public function insertShippedOrders($orders, $orderItems)
    {
        if (!$orderItems) {
            return;
        }
        $this->init();
        $orderMap = App::toMap($orders, 'orderId');
        $orderItemsGroups = App::splitArray($orderItems, 20);
        foreach($orderItemsGroups as $orderItemsGroup) {
            $values = '';
            $orderItemsMap = App::toMapArray($orderItemsGroup, 'orderId');
            foreach($orderItemsMap as $amazonOrderId => $orderItems) {
                $order = $orderMap[$amazonOrderId];
		$order->purchaseDate = str_ireplace('Z', '', $order->purchaseDate);
                foreach($order as $name => $value) {
                    $order->$name = codeClean($value);
                }
                foreach($orderItems as $orderItem) {
                    foreach($orderItem as $name => $value) {
                        $orderItem->$name = codeClean($value);
                    }
                    if ($values) {
                        $values .= ',';
                    }
                    $values .= "('{$orderItem->orderId}','".userid."','{$orderItem->orderItemId}', '{$order->purchaseDate}', '{$orderItem->sku}', '{$orderItem->title}', '{$order->shipToName}', '{$order->shipmentServiceLevelCategory }', '{$order->buyerEmail}')";
                }
            }
            $sql = 'INSERT IGNORE INTO order_items VALUES ' . $values; // purchase date in utc
            //App::debug($sql);
            if (!mysql_query($sql)) {
                throw new Exception('Unable to add shipped orders to database');
            }
        }
        $this->dispose();
    }
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
//////////////////////////////************************* Amazon Orders Start *******************************//////////////////////////

	public function insertUnShippedOrders($orders, $orderItems)
    {
        if (!$orderItems) {
            return;
        }
		
		
		
        $this->init();
        $orderMap = App::toMap($orders, 'orderId');
        $orderItemsGroups = App::splitArray($orderItems, 20);
        foreach($orderItemsGroups as $orderItemsGroup) {
            $values = '';
            $orderItemsMap = App::toMapArray($orderItemsGroup, 'orderId');
            foreach($orderItemsMap as $amazonOrderId => $orderItems) {
                $order = $orderMap[$amazonOrderId];
		$order->purchaseDate = str_ireplace('Z', '', $order->purchaseDate);
                foreach($order as $name => $value) {
                    $order->$name = codeClean($value);
                }
                foreach($orderItems as $orderItem) {
                    foreach($orderItem as $name => $value) {
                        $orderItem->$name = codeClean($value);
                    }
                    if ($values) {
                        $values .= ',';
                    }
					//print_r($order);exit;
					
                    $values .= "('{$orderItem->orderId}', '{$orderItem->orderItemId}', '".userid."', '{$order->orderTotalAmount}', 'amzn', '{$order->purchaseDate}', '{$orderItem->sku}', '{$orderItem->title}', '{$order->shipToName}', '{$order->shipmentServiceLevelCategory}', '{$order->buyerEmail}', '$orderItem->quantityOrdered', '{$order->shipToAddressLine1}', '{$order->shipToAddressLine2}', '{$order->shipToCity}', '{$order->shipToDistrict}', '{$order->shipToPostalCode}' )";
					
					
					
					// First Select main UnitQTY from product table with SKU and Order_flag = 0
					$select_UnitQTY = "SELECT ProductId,UnitQTY,SKU,amzn,ebay,sears,buy,xcart,spID_value FROM `amzn_products` WHERE SKU = '".$orderItem->sku."' AND Order_flag='0' AND Amazon_Cron_flag = '0' AND amzn='1'" ;
					$result_UnitQTY = mysql_query($select_UnitQTY) or die(mysql_error());
					if(mysql_num_rows($result_UnitQTY)>0){

						$UnitQTY = mysql_fetch_object($result_UnitQTY);
						//echo "<pre>";	print_r($UnitQTY->UnitQTY);
						
						
						
						// UPDATE Quantity in SITE DATABSE
						$update_Order_flag = "UPDATE `amzn_products` SET UnitQTY = (UnitQTY-".$orderItem->quantityOrdered."), Order_flag='1', Amazon_Cron_flag= '0' WHERE ProductId  = ".$UnitQTY->ProductId ;
						mysql_query($update_Order_flag);
						
						
						
						// Update Sears Quantity if sears product
						if($UnitQTY->sears == '1'){
							mysql_query("UPDATE amzn_products SET Sears_Cron_flag='1' WHERE ProductId='".$UnitQTY->ProductId."'");
						}
						if($UnitQTY->xcart== '1'){
							mysql_query("UPDATE amzn_products SET Xcart_Cron_flag='1' WHERE ProductId='".$UnitQTY->ProductId."'");
						}
						if($UnitQTY->ebay == '1'){
							//mysql_query("UPDATE amzn_products SET Ebay_Cron_flag='1' WHERE ProductId='".$UnitQTY->ProductId."'");
						}
						if($UnitQTY->buy == '1'){
							//mysql_query("UPDATE amzn_products SET Buy_Cron_flag='1' WHERE ProductId='".$UnitQTY->ProductId."'");
						}
					
					
					}
					
					
					
					
					
					
					echo "<b>OrderID: {$orderItem->orderId} <br><b>SKU: {$orderItem->sku} <br>Name: {$order->shipToName} <br> Buyer : {$order->buyerEmail}<br> PurchaseDate: {$order->purchaseDate}</b><br><br>";
                }
            }
			
		    $sql = "INSERT IGNORE INTO amazon_order_items VALUES " . $values."			
					ON DUPLICATE KEY UPDATE order_total_amount='{$order->orderTotalAmount}', type='amzn', purchase_date='{$order->purchaseDate}', sku='{$orderItem->sku}', title='{$orderItem->title}', ship_to_name='{$order->shipToName}', ship_service_level='{$order->shipmentServiceLevelCategory}', buyer_email='{$order->buyerEmail}' , Qty='{$orderItem->quantityOrdered}', ship_address_1='{$order->shipToAddressLine1}', ship_address_2='{$order->shipToAddressLine2}', ship_city='{$order->shipToCity}', ship_state='{$order->shipToDistrict}', ship_postal_code='{$order->shipToPostalCode}'"; // purchase date in utc
			//echo $sql;exit;
			
            //App::debug($sql);
            if (!mysql_query($sql)) {
				echo "Unable to add Unshipped orders to database => ".mysql_error();
				exit;
                throw new Exception('Unable to add Unshipped orders to database');
            }else{
				echo "Success..";
			}
        }
        $this->dispose();
    }

//////////////////////////////************************* Amazon Orders End *******************************//////////////////////////    
	
	
	
//////////////////////////////************************* Sears Orders Start *******************************//////////////////////////

	public function insertUnShippedOrders_Sears($orders, $orderItems)
    {
        if (!$orders) {
            return;
        }

        $this->init();
		 $values = '';
		 $sears = 'sears';
		 $sears_SKU = '';
		 $saers_qunt = 0;
		 
				foreach($orders as $order) {
					if (!isset($order['po-number'])) {
						echo 'No order items for po number :';
						continue;
					}
            		
					if(isset($order['po-line'][0])){
						$i=0;
						$sears_SKU = '';
						 $saers_qunt = 0;
							foreach($order['po-line'] as $orderItem) {
							//print_r($orderItem);
							//echo $orderItem['po-line-header']['item-id'];
							$values .= "('{$order['po-number']}','".userid."', '{$order['customer-order-confirmation-number']}', 
										'{$order['po-date']},'{$order['po-time']}','{$orderItem['po-line-header']['item-id']}', '".mysql_real_escape_string($orderItem['po-line-header']['item-name'])."', '".mysql_real_escape_string($order['shipping-detail']['ship-to-name'])."', '{$order['shipping-detail']['shipping-method']}','{$order['customer-email']}','{$order[po-line][po-line-header]['selling-price-each']}','sears','{$order['shipping-detail']['address']}','{$order['shipping-detail']['city']}','{$order['shipping-detail']['state']}','{$order['shipping-detail']['zipcode']}','{$order['shipping-detail']['phone']}','{$order[po-line][po-line-detail]['po-line-status']}','{$order[channel]}'),";
								
								
								
								
								$sears_SKU = $orderItem['po-line-header']['item-id'];
								$saers_qunt += $orderItem['po-line-detail']['quantity'];
								
								echo "<br><b>OrderID: {$order['po-number']} <br>Name: ".mysql_real_escape_string($order['shipping-detail']['ship-to-name'])." <br> Buyer : {$order['customer-email']}<br> PurchaseDate: {$order['po-date']} {$order['po-time']}</b><br>";
							}
						}else{
							//echo "<pre>"; print_r($order);
							$values .= "('{$order['po-number']}','".userid."','{$order['customer-order-confirmation-number']}', '{$order['po-date']}','{$order['po-time']}', '{$order['po-line']['po-line-header']['item-id']}', '".mysql_real_escape_string($order['po-line']['po-line-header']['item-name'])."', '".mysql_real_escape_string($order['shipping-detail']['ship-to-name'])."', '{$order['shipping-detail']['shipping-method']}', '{$order['customer-email']}','{$order[po-line][po-line-header]['selling-price-each']}','sears','{$order['shipping-detail']['address']}','{$order['shipping-detail']['city']}','{$order['shipping-detail']['state']}','{$order['shipping-detail']['zipcode']}','{$order['shipping-detail']['phone']}','{$order[po-line] [po-line-detail]['po-line-status']}','{$order[channel]}'),";
							
							
								$sears_SKU = $orderItem['po-line-header']['item-id'];
								$saers_qunt += $orderItem['po-line-detail']['quantity'];
							
							
							echo "<br><b>OrderID: {$order['po-number']} <br>Name: {$order['shipping-detail']['ship-to-name']} <br> Buyer : {$order['customer-email']}<br> PurchaseDate: {$order['po-date']} {$order['po-time']}</b><br><br>";
						}
						
						
							# if Find Sears SKU						
							if($sears_SKU){
								
								// First Select main UnitQTY from product table with SKU and Order_flag = 0
								$select_UnitQTY = "SELECT UnitQTY,SKU,ebay,sears,buy,xcart,spID_value FROM `amzn_products` WHERE SKU = '".$sears_SKU."' AND Order_flag = '0' AND sears='1'" ;
								$result_UnitQTY = mysql_query($select_UnitQTY) or die(mysql_error());
								if(mysql_num_rows($result_UnitQTY)>0){
			
									$UnitQTY = mysql_fetch_object($result_UnitQTY);
									//echo "<pre>";	print_r($UnitQTY->UnitQTY);
									
									
									
									// UPDATE Quantity in SITE DATABSE
									$update_Order_flag = "UPDATE `amzn_products` SET UnitQTY = (UnitQTY-".$saers_qunt."), Order_flag= '1' WHERE ProductId  = (	SELECT ProductId FROM (SELECT * FROM  `amzn_products`) AS something WHERE SKU = '".$sears_SKU."' AND Order_flag = '0' AND sears='1'	) " ;
									mysql_query($update_Order_flag);
									
									
									
									// Update Sears Quantity if sears product
									if($UnitQTY->amzn == '1'){
										$qntforupdate = ($UnitQTY->UnitQTY - $saers_qunt);
										// check if qnt qrater than 0
										if($qntforupdate >= 0){
											//$this->update_amzn_quantity($UnitQTY->SKU, $qntforupdate);
										}
									}
								
								
								}
								
							}
						
						
			}
				/*echo "<br>";
				echo substr($values, 0, -1);
				echo "<br>";*/
	           // $sql = 'INSERT INTO sears_order_items VALUES ' . substr($values, 0, -1); // purchase date in utc
		       $sql = "INSERT IGNORE INTO sears_order_items VALUES " . $values." ON DUPLICATE KEY UPDATE type='{$sears}',purchase_date='{$order['po-date']}', sku='{$order['po-line']['po-line-header']['item-id']}', title='{$order['po-line']['po-line-header']['item-name']}',ship_to_name='{$order['shipping-detail']['ship-to-name']}', ship_service_level='{$order['shipping-detail']['shipping-method']}', buyer_email='{$order['customer-email']}'"; // purchase date in utc
					
					
            //App::debug($sql);
            if (!mysql_query($sql)) {
				echo mysql_error();
				echo "Unable to add Unshipped orders to database";
                //throw new Exception(mysql_error());
            }else{
				echo "Success..";
			}
        
        $this->dispose();
    }
	
//////////////////////////////************************* Sears Orders End *******************************//////////////////////////	
	
	
	
	
	
	
	
    public function maintain()
    {
        $this->init();
        $date_before = strftime("'%Y-%m-%dT%H:%M:%S'", mktime(0, 0, 0, date("m"), date("d")-60, date("Y")));
        $sql = 'DELETE FROM order_items WHERE purchase_date <= ' . $date_before;
        mysql_query($sql);
	$this->dispose();
    }
    
	
	
	// Create by VIRU FOR Update (Sears.com Quantity - Amazon.com Quantity)
	public function update_sears_quantity($SKU,$new_qnt)
	{
		// SAMPLE: https://seller.marketplace.sears.com/SellerPortal/api/inventory/1x1/fbm-lmp/v1?email={emailaddres}&password={password}&itemId={itemid}&locationId={locationid}&onHand={onhand}&pickUpNowEligible={YES or NO}&inventoryTimestamp={YYYY-MM-DD HH:MM:SS}&lowInvThreshold={lowinvthreshold #}			
						
						
				# FIRST FIND LOCATION ID FROM ITEM ID		
				$curlheader[] = "Content-Type:application/html"; 
				curl_setopt($ch, CURLOPT_URL, "https://seller.marketplace.sears.com/SellerPortal/api/inventory/v2?itemIds=".$SKU."&email=".Email."&password=".Password);//put url
				//curl_setopt($ch, CURLOPT_PUT, true);
				//curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
				curl_setopt($ch, CURLOPT_FAILONERROR,1);
				curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
				
				//curl_setopt($ch, CURLOPT_INFILE, $putData);
				//curl_setopt($ch, CURLOPT_INFILESIZE, strlen($putString));
				//curl_setopt($ch, CURLOPT_HTTPHEADER, $curlheader);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				$result_location = curl_exec($ch);
				//fclose($putData);
				curl_close($ch);
	
				$array_location_id = $this->xmlstr_to_array($result_location); 	
				
				if($array_location_id['item']['locations']['location']['@attributes']['location-id']){								
						
						$lcoationID = $array_location_id['item']['locations']['location']['@attributes']['location-id'];
						
						# IF FIND LOCATION ID THAN UPDATE Quantity
						$feed="";
					$ch = curl_init();		
						//$putString = file_get_contents(base_url().'API/sears/lmp-item.xml');
						$feed .= '<store-inventory xmlns="http://seller.marketplace.sears.com/catalog/v6" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://seller.marketplace.sears.com/catalog/v6 ../../../../../rest/inventory/import/v6/store-inventory.xsd">
							<item item-id="'.$SKU.'">
								<locations>
									<location location-id="'.$lcoationID.'">
										<quantity>'.$new_qnt.'</quantity>
										<low-inventory-threshold>0</low-inventory-threshold>
										<pick-up-now-eligible>false</pick-up-now-eligible>
									</location>
								</locations>
							</item>
						</store-inventory>';
						
				//echo $feed;exit;		
				
						$putData1 = tempnam('\temp','FOO_Sears');//loading into local temp file to send quickly
						$putData = fopen($putData1,'rw+');
						fwrite($putData, $feed);
						//rewind($putData);
						fseek($putData, 0);
						//print_r(file_get_contents($putData));
						$curlheader[] = "Content-Type:application/xml"; 
						curl_setopt($ch, CURLOPT_URL, "https://seller.marketplace.sears.com/SellerPortal/api/inventory/fbm-lmp/v6?email=".Email."&password=".Password);//put url
						curl_setopt($ch, CURLOPT_PUT, true);
						curl_setopt($ch, CURLOPT_INFILE, $putData);
						curl_setopt($ch, CURLOPT_INFILESIZE, strlen($feed));
						curl_setopt($ch, CURLOPT_HTTPHEADER, $curlheader);
						curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
						$result = curl_exec($ch);
						fclose($putData);
						curl_close($ch);
						
						//print $result;exit;
						
						/*$xml = simplexml_load_string($result);
						$json = json_encode($xml);
						$array = json_decode($json,TRUE);*/
						//$array = $this->xmlstr_to_array($result); 
						//print_r($array);
				}
						
						
						
						
	}
	
	
	
	
	
	// Create by VIRU FOR Update (Amazon.com Quantity - sears.com Quantity)
	public function update_amazon_quantity($SKU,$new_qnt)
	{
				//$response = file_get_contents("http://".$_SERVER['HTTP_HOST'].''.'/'.FOLDER_NAME.'/'."index.php/admin/inventory/amzn/submitfeed/update_qnt/".$SKU."/".$new_qnt);
				if(exec("php-cli  http://".$_SERVER['HTTP_HOST'].''.'/'.FOLDER_NAME.'/'."index.php/admin/inventory/amzn/submitfeed/update_qnt/".$SKU."/".$new_qnt)) echo "true"; else "false";
				//App::debug($summary);
				//echo $response;
	}
	
	
	
	
		 /**
		 * convert xml string to php array - useful to get a serializable value
		 *
		 * @param string $xmlstr 
		 * @return array
		 * @author Adrien aka Gaarf
		 */
		public function xmlstr_to_array($xmlstr) {
		  $doc = new DOMDocument();
		  $doc->loadXML($xmlstr);
		  return $this->domnode_to_array($doc->documentElement);
		}
		public function domnode_to_array($node) {
		  $output = array();
		  switch ($node->nodeType) {
		   case XML_CDATA_SECTION_NODE:
		   case XML_TEXT_NODE:
			$output = trim($node->textContent);
		   break;
		   case XML_ELEMENT_NODE:
			for ($i=0, $m=$node->childNodes->length; $i<$m; $i++) { 
			 $child = $node->childNodes->item($i);
			 $v = $this->domnode_to_array($child);
			 if(isset($child->tagName)) {
			   $t = $child->tagName;
			   if(!isset($output[$t])) {
				$output[$t] = array();
			   }
			   $output[$t][] = $v;
			 }
			 elseif($v) {
			  $output = (string) $v;
			 }
			}
			if(is_array($output)) {
			 if($node->attributes->length) {
			  $a = array();
			  foreach($node->attributes as $attrName => $attrNode) {
			   $a[$attrName] = (string) $attrNode->value;
			  }
			  $output['@attributes'] = $a;
			 }
			 foreach ($output as $t => $v) {
			  if(is_array($v) && count($v)==1 && $t!='@attributes') {
			   $output[$t] = $v[0];
			  }
			 }
			}
		   break;
		  }
		  return $output;
		}

	
	
	
	
	
	
	
		
}

class AllOrders
{
    private $_db;

   /* public static function shipCbaOrders()
    {
        $instance = new AllOrders();
        $instance->_init();
        
        try {
            $orders = $instance->_getUnshippedOrders(AMAZON_CBA_MARKETPLACE_ID);
            if ($orders && $orders['orders']) {
                $cbaOrders = array();
                foreach($orders['orders'] as $order) {
                    if (str_ireplace(' ', '', strtolower($order->salesChannel)) == 'amazoncheckout') {
                        $cbaOrders[] = $order;
                    }
                }
                if ($cbaOrders) {
                    App::debug($cbaOrders);
                    $instance->_ship($cbaOrders);
                }
            }
        } catch(exception $ex) {
            $instance->_dispose();
            throw $ex;
        }
        
        $instance->_dispose();
    }
 	*/
 
/************************************************ Amazon Module ***********************************************************/
 
 	// Get Amazon Orders
    public static function saveCsv()
    {
        $instance = new AllOrders();
        $instance->_init();
        
        try {
            $orders = $instance->_getUnshippedOrders(AMAZON_MWS_MARKETPLACE_ID);
			if(count($orders['orders'])>0){
				//echo "<pre>";print_r($orders);exit;
				//$instance->_saveToCsv($orders['orders'], $orders['orderitems'], AMAZON_CSV_UNSHIPPED_ORDERS_URI);
				$instance->_db->insertUnShippedOrders($orders['orders'], $orders['orderitems']);
				//$instance->_db->update_amazon_quantity($SKU='MYTEST_1Z-500ABR-FLAT',$new_qnt='1');
				//$instance->_shipSupplierOrders();
			}else{
				echo "No Amazon Orders Found.";
			}
        } catch(exception $ex) {
            $instance->_dispose();
            throw $ex;
        }
        
        $instance->_dispose();
   }
   
   
   	// Amazon Unshipped Orders Get
    private function _getUnshippedOrders($marketplaceId)
    {
		
        $sec = new AmazonMWSSec(AMAZON_MWS_MERCHANT_ID, $marketplaceId, AMAZON_MWS_ACCESS_KEY, AMAZON_MWS_SECRET_KEY, 'COM');
        $mws = AmazonMWS::createOrders($sec);
        
        $sometime  = mktime(0, 0, 0, 1, 1, 2012);
        $sometimeStr = strftime("%Y-%m-%dT%H:%M:%S", $sometime);
        
        $lastUpdatedAfter = $sometimeStr;
        $lastUpdatedBefore = false;
        $orderStatus = array('Unshipped', 'PartiallyShipped');
        
        $orders = $mws->listOrders($lastUpdatedAfter, $lastUpdatedBefore, $orderStatus);
		//echo "<pre>";print_r($orders);exit;
        $orderItems = false;
        if ($orders) {
            $orderIds = App::toArrayFromObjectMembers($orders, 'orderId');
            $orderItems = $mws->listOrderItems($orderIds);
        }

        return array('orders' => $orders, 'orderitems' => $orderItems);
    }

/************************************************ End Amazon Module ***********************************************************/





/************************************************ Sears Module ***********************************************************/
   
   // Get Sears Orders
    public static function sears_orders()
    {	
		echo "<pre>";
		$instance = new AllOrders();
		$instance->_init();
        try {
            $orders = $instance->_getUnshippedOrders_Sears('Closed');
			print_r($orders);exit;
			//status={New or Open or Closed}
			$instance->_db->insertUnShippedOrders_Sears($orders['orders'], $orders['orderitems']);

            $instance->_shipSupplierOrders();
        } catch(exception $ex) {
            $instance->_dispose();
            throw $ex;
        }
        
        $instance->_dispose();
   }
   
   
    private function _getUnshippedOrders_Sears($Status)
    { 	
		$instance = new AllOrders();
		$instance->_init();
	
		//echo "https://seller.marketplace.sears.com/SellerPortal/api/oms/purchaseorder/v3?email=".Email."&password=".Password."&status=".$Status;exit;
		$ch = curl_init();
		$curlheader[] = "Content-Type:application/xml"; 
		curl_setopt($ch, CURLOPT_URL, "https://seller.marketplace.sears.com/SellerPortal/api/oms/purchaseorder/v4?email=".Sears_Email."&password=".Sears_Password."&status=".$Status);//put url
		curl_setopt($ch, CURLOPT_PUT, true);
		/*curl_setopt($ch, CURLOPT_INFILE, $putData);
		curl_setopt($ch, CURLOPT_INFILESIZE, strlen($feed));*/
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
		curl_setopt($ch, CURLOPT_HTTPHEADER, $curlheader);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$result = curl_exec($ch);
		curl_close($ch);
		
		
		$response = $instance->_db->xmlstr_to_array($result);
		//echo "<pre>";print_r($response);exit;
		if(!isset($response['error-detail'])){
		    return array('orders' => $response['purchase-order']);
		}else{
			echo $response['error-detail'];exit;
		}
    }




/************************************************ End Sears Module ***********************************************************/



    /*public static function sendMails()
    {
        $instance = new AllOrders();
        $instance->_init();
        
        try {
            $orders= $instance->_getShippedOrders(AMAZON_MWS_MARKETPLACE_ID);
            $instance->_db->insertShippedOrders($orders['orders'], $orders['orderitems']);
            $instance->_db->maintain();
            
            $stat = array(
                'eight_days_before' => 0,
                'sixteen_days_before' => 0,
                'twentyfour_days_before' => 0,
                );
            
            $orders = $instance->_db->getOrdersShippedBefore(8); 
            $instance->_sendMails($orders, AMAZON_MAIL_TEMPLATE_DAY_8_BEFORE);
            $stat['eight_days_before'] = count($orders);
            
            $orders = $instance->_db->getOrdersShippedBefore(16);
            $instance->_sendMails($orders, AMAZON_MAIL_TEMPLATE_DAY_16_BEFORE);
            $stat['sixteen_days_before'] = count($orders);
            
            $orders = $instance->_db->getOrdersShippedBefore(24);
            $instance->_sendMails($orders, AMAZON_MAIL_TEMPLATE_DAY_24_BEFORE);
            $stat['twentyfour_days_before'] = count($orders);

            $instance->_sendMail('sean@examed.org', 'sean@examed.org', 'Amazon Order Feedback Mails', print_r($stat, true));
            
        } catch(exception $ex) {
            $instance->_dispose();
            throw $ex;
        }
        
        $instance->_dispose();
    }
	*/



    
    /*private function _saveToCsv($orders, $orderItems, $uri)
    {
        if (!$orders) {
            return;
        }
        if (!$orderItems) {
            App::warning('No order items for orders: ', $orders);
            return;
        }
        if (!$uri) {
            throw new Exception('No unshipped orders csv');
        }
        $orderMap = App::toMap($orders, 'orderId');
        $orderItemMap = App::toMapArray($orderItems, 'orderId');
        
        $fp = fopen($uri, 'w');
        if (!$fp) {
            throw new Exception('Unable to open CSV file location');
        }
        
        $header = array(
            'recipient-name',
            'ship-address-1',
            'ship-address-2',
            'ship-city',
            'ship-state',
            'ship-postal-code',
            'sku',
            'product-name',
            'quantity-to-ship',
            'ship-service-level',
            'order-id',
            );
        
        fputcsv($fp, $header);
        
        foreach($orderMap as $orderId => $order) {
            if (!isset($orderItemMap[$orderId])) {
                App::warning('No order items for order id: ', $orderId);
                continue;
            }
            $orderItems = $orderItemMap[$orderId];
            foreach($orderItems as $orderItem) {
                if ($orderItem->quantityOrdered - $orderItem->quantityShipped < 1) { // partially shipped orders
                    continue;
                }
                $row = array();
                $row[] = $order->shipToName;
                $row[] = $order->shipToAddressLine1;
                $row[] = $order->shipToAddressLine2 . $order->shipToAddressLine3;
                $row[] = $order->shipToCity;
                $row[] = $order->shipToStateOrRegion;
                $row[] = $order->shipToPostalCode;
                $row[] = $orderItem->sku;
                $row[] = $orderItem->title;
                $row[] = $orderItem->quantityOrdered - $orderItem->quantityShipped;
                $row[] = $order->shipmentServiceLevelCategory;
                $row[] = "'".str_ireplace('-', '', $orderId);
                
                fputcsv($fp, $row);
            }
        }
        
        fflush($fp);
        fclose($fp);
    }*/

   /* private function _ship($orders)
    {
        $sec = new AmazonMWSSec(AMAZON_MWS_MERCHANT_ID, AMAZON_CBA_MARKETPLACE_ID, AMAZON_MWS_ACCESS_KEY, AMAZON_MWS_SECRET_KEY, 'COM');
        $mws = AmazonMWS::createFeeds($sec);
        
        $shipments = array();
        foreach($orders as $order) {
            if (is_string($order)) {
		    if (count($order) >= 10) {
			    $orderId = $order;
		    } else {
			    continue;
		    }
	    } else {
		    $orderId = $order->orderId;
	    }
	    $shippingCarrier = 'FedEx';
	    $amazonShipment = new AmazonShipment($orderId, $shippingCarrier);
            $shipments[] = $amazonShipment;
        }
        
        $summary = $mws->updateShipments($shipments);
        App::debug($summary);
        if ($summary->totalErrors) {
            throw new Exception('Unable to update ' . $summary->totalErrors . ' shipments');
        }
    }*/
    
	
    
    /*private function _getShippedOrders($marketplaceId)
    {
        $sec = new AmazonMWSSec(AMAZON_MWS_MERCHANT_ID, $marketplaceId, AMAZON_MWS_ACCESS_KEY, AMAZON_MWS_SECRET_KEY, 'COM');
        $mws = AmazonMWS::createOrders($sec);
        
        $twoDaysAgo  = mktime(date("h"), date("i"), 0, date("m")  , date("d")-2, date("Y"));
        $twoDaysAgoStr = strftime("%Y-%m-%dT%H:%M:%S-0500", $twoDaysAgo);
        
        $lastUpdatedAfter = $twoDaysAgoStr;
        $lastUpdatedBefore = false;
        $orderStatus = 'Shipped';
        
        $orders = $mws->listOrders($lastUpdatedAfter, $lastUpdatedBefore, $orderStatus);

        $orderItems = false;
        if ($orders) {
            $orderIds = App::toArrayFromObjectMembers($orders, 'orderId');
            $orderItems = $mws->listOrderItems($orderIds);
        }
        
        return array('orders' => $orders, 'orderitems' => $orderItems);
    }*/
    
    /*private function _shipSupplierOrders()
    {
        global $SUPPLIERS;
        global $RUBY_SCRIPT;
		global $RUBY;
        
		//echo $RUBY;exit;
		
        $suppliersWithErrors = array();
        $dateStamp = strftime('%Y%m%d');
        foreach($SUPPLIERS as $name => $data) {
            if (!is_array($data['attachment'])) {
                $SUPPLIERS[$name]['attachment'] = str_ireplace('{{datestamp}}', $dateStamp, $data['attachment']);
            } else {
                for ($i = 0; $i < count($data['attachment']); $i++) {
                    $SUPPLIERS[$name]['attachment'][$i] = str_ireplace('{{datestamp}}', $dateStamp, $data['attachment'][$i]);
                }
            }
        }
        
        if (exec($RUBY . ' ' . $RUBY_SCRIPT) === false) {
            throw new Exception('Unable to execute ruby script');
        }
        
        foreach($SUPPLIERS as $name => $data) {
            try {
                if (!is_array($data['attachment'])) {
                    if (!file_exists($data['attachment'])) {
                        throw new Exception('No attachment for supplier ' . $name);
                    }
                }
                if ($data['order-file']) {
                    if (!file_exists($data['order-file'])) {
                        throw new Exception('No order-file for supplier ' . $name);
                    }
                }
            } catch(exception $ex) {
                $suppliersWithErrors[] = $name;
                App::ex($ex, '_sendSupplierMail');
            }
        }
        
        // mark as shipped
        $orderIds = array();
        foreach($SUPPLIERS as $name => $data) {
            try {
                if (array_search($name, $suppliersWithErrors) !== false) {
                    App::error('Unable to mark orders for supplier ', $name, ' as shipped');
                    continue;
                }
                if (!$data['order-file']) {
                    continue;
                }
                $orderFile = $data['order-file'];
                
                $orders = trim(file_get_contents($orderFile));
                
                if ($name == 'owner') {
                    $orderIds = array_merge($orderIds, explode(',', $orders));
                }
                
                $SUPPLIERS[$name]['order-ids'] = $orders;
            } catch(exception $ex) {
                App::ex($ex, '_sendSupplierMail');
            }
        }
        
        
        $bcc = AMAZON_MAIL_SUPPLIER_ADDRESS_BCC;
        $replyTo = AMAZON_MAIL_SUPPLIER_ADDRESS_REPLYTO;
        
        foreach($SUPPLIERS as $name => $data) {
            try {
                if (array_search($name, $suppliersWithErrors) !== false) {
                    App::error('Unable to send mail for supplier ', $name);
                    continue;
                }
                if ($name != 'owner' && !$SUPPLIERS[$name]['order-ids']) {
                    continue;
                }
                if (!is_array($data['attachment'])) {
                    $data['attachment'] = array($data['attachment']);
                }
                $mailTo = $data['address'];
                
                $attachments = array();
                foreach($data['attachment'] as $attachment) {
                    $attachmentPath = $attachment;
                    if (!file_exists($attachmentPath)) {
                        continue;
                    }
                    $attachmentName = substr($attachmentPath, strrpos($attachmentPath, '/')+1);
                    $attachments[] = array('path' => $attachmentPath, 'name' => $attachmentName);
                }
                
                $debugMailTo = AMAZON_MAIL_SUPPLIER_SEND_MAIL_TO;
                $mailTo = (!empty($debugMailTo) ? $debugMailTo : $mailTo);
                $bcc = (!empty($debugMailTo) ? false : $bcc);
                
                $from = $replyTo;
                
                if (!is_array($mailTo)) {
                    $mailTo = array($mailTo);
                }
                
                $content = file_get_contents($data['template']);
                foreach($mailTo as $to) {
                    $this->_sendMail($from, $to, $data['subject'], $content, $bcc, false, $attachments);
                }
            } catch(exception $ex) {
                $suppliersWithErrors[] = $name;
                App::ex($ex, '_sendSupplierMail');
            }
        }
        
        foreach($SUPPLIERS as $name => $data) {
            try {
                $attachments = $data['attachment'];
                if (!is_array($attachments)) {
                    $attachments = array($attachments);
                }
                foreach($attachments as $attachment) {
                    if (file_exists($attachment)) {
                        unlink($attachment);
                    }
                }
                if (file_exists($data['order-file'])) {
                    unlink($data['order-file']);
                }
            } catch(exception $ex) {
                App::ex($ex, '_sendSupplierMail');
            }
        }
        
        App::debug($orderIds);
        if (true) {
            foreach($orderIds as &$orderId) {
                $orderId = substr($orderId, 0, 3) . '-' . substr($orderId, 3, 7) . '-' . substr($orderId, 10);
            }
            if ($orderIds) {
                $this->_ship($orderIds);
            }
        }
    }*/
    
    /*private function _sendMails($orders, $templateUri)
    {
        if (!file_exists($templateUri)) {
            throw new Exception('No template file at ' . $templateUri);
        }
        if (!$orders) {
            return ;
        }
        $templateContent = file_get_contents($templateUri);
        $templateContent = iconv('UTF-8','UTF-8//IGNORE', $templateContent); 
        $orderMap = App::toMapArray($orders, 'order_id');
        $exceptions = array();
        foreach($orderMap as $orderId => $orders) {
            try {
                $reference = $orders[0];
                $mailContent = $this->_prepareMailContent($reference, $orders, $templateContent);
                if (!$mailContent) {
                    throw new Exception('No valid mail content for ' . $templateUri);
                }
                $subject = AMAZON_MAIL_SUBJECT;
                $bcc = trim(AMAZON_MAIL_BCC);
                $from = trim(AMAZON_MAIL_FROM);
                $this->_sendMail($from, $reference['buyer_email'], $subject, $mailContent, $bcc);
            } catch(exception $ex) {
                App::ex($ex, 'sendMails');
                $exceptions[] = $ex;
            }
        }
        if ($exceptions) {
            throw new Exception($exceptions[0]);
        }
    }*/
    
    private function _sendMail($from, $to, $subject, $mailContent, $bcc = false, $replyTo = false, $attachments = false)
    {
        if (empty($to)) {
            throw new Exception('No mail to');
        }
        if (empty($mailContent)) {
            throw new Exception('No mail content');
        }
        if (AMAZON_SEND_MAIL_TO) {
            $to = AMAZON_SEND_MAIL_TO;
        }
        $headers  = 'MIME-Version: 1.0' . PHP_EOL;
        $headers .= "From: " . $from . PHP_EOL;
        if ($replyTo) {
            $headers .= "Reply-To: " . $replyTo . PHP_EOL;
        }
        if ($bcc) {
            $headers .= "Bcc: " . $bcc . PHP_EOL;
        }
        $headers .= 'X-Mailer: PHP/' . phpversion() . PHP_EOL;
        if (!$attachments) {
            $headers .= 'Content-type: text/html; charset=iso-8859-1' . PHP_EOL;
        } else {
            $random_hash = md5(date('r', time()));

            $headers .= "Content-Type: multipart/mixed; boundary=\"PHP-mixed-".$random_hash."\"";
            
            $mailContent = "
--PHP-mixed-$random_hash
Content-type: text/html; charset=iso-8859-1'

$mailContent";

            foreach($attachments as $attachment) {
                $attachmentContent = chunk_split(base64_encode(file_get_contents($attachment['path'])));
                $attachmentName = $attachment['name'];
                
                $mailContent .= "
--PHP-mixed-$random_hash
Content-Type: application/xlsx; name=$attachmentName
Content-Transfer-Encoding: base64
Content-Disposition: attachment

$attachmentContent";
            }
            
            $mailContent .= "
--PHP-mixed-$random_hash--";

        }

        /*
        App::debug('sending mail ... ');
        App::debug('from: ', $from);
        App::debug('to: ', $to);
        App::debug('subject: ', $subject);
        App::debug('content -->>');
        App::debug($mailContent);
        App::debug('<<-- content');
         */
        //'file_put_contents('mail.htm', $mailContent);
        
        if (mail($to, $subject, $mailContent, $headers) === false) {
            throw new Exception('Unable to send mail to ' . $to);
        }
    }
    
    /*private function _prepareMailContent($order, $orderItems, $templateContent) // all types are from db, order is reference item for set of orders
    {
        if (!$templateContent) {
            throw new Exception('No template content');
        }
        
        $content = $templateContent;
        $content = str_ireplace('{{ORDER-ID}}', $order['order_id'], $content);
        $content = str_ireplace('{{NAME}}', $order['ship_to_name'], $content);
        
        $forEachBeginPos = stripos($content, '{{FOR-EACH-BEGIN}}');
        $forEachEndPos = stripos($content, '{{FOR-EACH-END}}');
        
        $forEachContent = false;
        if ($forEachBeginPos !== false && $forEachEndPos !== false) {
            $forEachContent = substr($content, $forEachBeginPos + strlen('{{FOR-EACH-BEGIN}}'), $forEachEndPos - ($forEachBeginPos + strlen('{{FOR-EACH-BEGIN}}')));
        }
        if (!$forEachContent) {
            App::warning('No valid for each content in template file');
        } else {
            $itemContent = '';
            foreach($orderItems as $orderItem) {
                $itemContent .= str_ireplace('{{TITLE}}', $orderItem['title'], $forEachContent);
            }
            $content = substr($content, 0, $forEachBeginPos) . $itemContent . substr($content, $forEachEndPos + strlen('{{FOR-EACH-END}}'));
        }
        return $content;
    }*/
    
    private function _init()
    {
        App::init(AMAZON_DEBUG);
        
        $this->_db = new AmazonDatabase();
    }
    
    private function _dispose()
    {
        $from = AMAZON_MAIL_SUPPLIER_ADDRESS_BCC;
        $to  = AMAZON_MAIL_SUPPLIER_ADDRESS_BCC;
        if (AMAZON_MAIL_SUPPLIER_SEND_MAIL_TO) {
            $from = $to = AMAZON_MAIL_SUPPLIER_SEND_MAIL_TO;
        }
        App::mailOnErorrs($from, $to, 'Amazon CRON Job Errors');
    }

}

/*if ($_SERVER['argc'] > 1) {
    if ($_SERVER['argc'] == 2 && $_SERVER['argv'][1] == 'send-mails') {
        AllOrders::sendMails();
    } else if ($_SERVER['argc'] == 2 && $_SERVER['argv'][1] == 'save-csv') {
        AllOrders::saveCsv();
    } else if ($_SERVER['argc'] == 2 && $_SERVER['argv'][1] == 'ship-cba-orders') {
        AllOrders::shipCbaOrders();
    }
}
*/
if ($_REQUEST['argc'] > 1) {
    if ($_REQUEST['argc'] == 2 && $_REQUEST['argv'] == 'amzon-orders') {
        		AllOrders::saveCsv();
    } else if ($_REQUEST['argc'] == 2 && $_REQUEST['argv'] == 'sears-orders') {
				AllOrders::sears_orders();
    } else if ($_REQUEST['argc'] == 2 && $_REQUEST['argv'] == 'xcart-orders') {
				AllOrders::xcart_orders();
    }
}


//Check if magic qoutes is on then stripslashes if needed
function codeClean($var)
{
	if (is_array($var)) {
		foreach($var as $key => $val) {
			$output[$key] = codeClean($val);
		}
	} else {
			$var = strip_tags(trim($var));
			if (function_exists("get_magic_quotes_gpc")) {
				$output = mysql_real_escape_string((get_magic_quotes_gpc())? stripslashes($var): $var);
			} else {
				$output = mysql_real_escape_string($var);
			}
	}
	if (!empty($output))
	return $output;
}