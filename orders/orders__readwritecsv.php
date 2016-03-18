<?php
set_time_limit(0);
ini_set('memory_limit', '1024M');
include_once "../app/Mage.php";
Mage::init();
$app = Mage::app('default');
$websiteId = '2';
$storeId ='2';

$arr = array();
$resource	= Mage::getSingleton('core/resource');
$conn 		= $resource->getConnection('externaldb_read');
$orders 	= $conn->query('SELECT * FROM orders limit 0,1');

$fp1 = fopen('orders.csv', 'w');
$headerDisplayed = false;
$i=0;
foreach($orders as $fields)
{	
	//echo "Orders table";
	//echo "<pre>";
//print_r($fields);
	//exit;	
	$arr[$i]['order_id'] = $fields['td_order_number'];	
	if($fields['uid'])
	{
		$users 	= $conn->query('SELECT * FROM users where uid='.$fields[uid].'');
		foreach($users as $users_res)
		{
		//echo "users table";
		//echo "<pre>";
		//print_r($users_res);
		//exit;
		$arr[$i]['email'] = $users_res['email'];
		$arr[$i]['firstname'] = $users_res['fname'];
		$arr[$i]['lastname'] = $users_res['lname'];
		$arr[$i]['prefix'] = "";		
		$arr[$i]['middlename'] = "";		
		$arr[$i]['suffix'] ="";
		$arr[$i]['taxvat'] = "";		
		$collection = Mage::getModel("customer/customer");
		$collection->setWebsiteId($websiteId);        
        $collection->LoadByEmail($users_res['email']);
		
		//echo "<pre>";
		//print_r($collection);
		$arr[$i]['customer_id'] = $collection['entity_id']!=''?$collection['entity_id']:0;	   // have to map with magento
		$arr[$i]['shipping_telephone'] = $users_res['phone'];		
		$arr[$i]['customer_id1'] = $collection['entity_id']!=''?$collection['entity_id']:0;	   // have to map with magento
		$arr[$i]['billing_prefix'] = "";
		$arr[$i]['billing_firstname'] = $users_res['fname'];
		$arr[$i]['billing_middlename'] = "";
		$arr[$i]['billing_lastname'] = $users_res['lname'];
		$arr[$i]['billing_suffix'] = "";
		$arr[$i]['billing_street_full'] = $users_res['address1'];
		$arr[$i]['billing_city'] = $users_res['city'];
		$arr[$i]['billing_region'] = $users_res['state1'];
		$arr[$i]['billing_country'] = $users_res['countryid'];
		$arr[$i]['billing_postcode'] = $users_res['zip'];
		$arr[$i]['billing_company'] = $users_res['company'];
		$arr[$i]['billing_fax'] ="";
		$arr[$i]['billing_telephone'] = $users_res['phone'];			
   	     		
		}
	}  
	
	$arr[$i]['created_at'] = $fields['placed_date'];
	$arr[$i]['updated_at'] = $fields['status_date'];	
	$arr[$i]['invoice_created_at'] = "";
	$arr[$i]['shipment_created_at'] =$fields['ship_date'];
	$arr[$i]['creditmemo_created_at'] = "";
	$arr[$i]['tax_amount']= $fields['tax_amount'];		
    $arr[$i]['base_tax_amount']= $fields['tax_amount'];
    $arr[$i]['discount_amount']= $fields['discount_amount'];
    $arr[$i]['base_discount_amount']= $fields['discount_amount'];
    $arr[$i]['shipping_tax_amount']= $fields['shipping_tax_amount'];
    $arr[$i]['base_shipping_tax_amount']= $fields['shipping_tax_amount'];
    $arr[$i]['base_to_global_rate']= 1;
    $arr[$i]['base_to_order_rate']= 1;
    $arr[$i]['store_to_base_rate']= 1;
    $arr[$i]['store_to_order_rate']= 1;
    $arr[$i]['subtotal_incl_tax']= $fields['subtotal_amount'];
    $arr[$i]['base_subtotal_incl_tax']= $fields['subtotal_amount'];
    $arr[$i]['coupon_code']=""; 
    $arr[$i]['shipping_incl_tax']= $fields['shipping_tax_amount'];
    $arr[$i]['base_shipping_incl_tax']= $fields['shipping_tax_amount'];
    $arr[$i]['shipping_method']= $fields['shipping_cm_name'];
    $arr[$i]['shipping_amount']= $fields['shipping_amount'];
    $arr[$i]['subtotal']= $fields['subtotal_amount'];
    $arr[$i]['base_subtotal']= $fields['subtotal_amount'];
    $arr[$i]['grand_total']= $fields['total_amount'];
    $arr[$i]['base_grand_total']= $fields['total_amount'];
    $arr[$i]['base_shipping_amount']= $fields['shipping_amount'];
    $arr[$i]['adjustment_positive']= 0;
    $arr[$i]['adjustment_negative']= 0;
    $arr[$i]['refunded_shipping_amount']= 0;
    $arr[$i]['base_refunded_shipping_amount']= 0;
    $arr[$i]['refunded_subtotal']= 0;
    $arr[$i]['base_refunded_subtotal']= 0;
    $arr[$i]['refunded_tax_amount']= 0;
    $arr[$i]['base_refunded_tax_amount']= 0;
    $arr[$i]['refunded_discount_amount']= 0;
    $arr[$i]['base_refunded_discount_amount']= 0;
    $arr[$i]['store_id']= 2;
	if($fields['status']=="Completed")
	{ 	$arr[$i]['order_status']="complete";
		$arr[$i]['order_state']="complete";
	}
	else if($fields['status']=="Process")
	{ 	$arr[$i]['order_status']="processing";
		$arr[$i]['order_state']="processing";
	}	
	else
	{
	}
    $arr[$i]['hold_before_state']= "";
    $arr[$i]['hold_before_status']= "";
    $arr[$i]['store_currency_code'] ="USD";
    $arr[$i]['base_currency_code'] = "USD";
    $arr[$i]['order_currency_code'] = "USD";
    $arr[$i]['total_paid'] = $fields['total_amount'];
    $arr[$i]['base_total_paid'] = $fields['total_amount'];	
    $arr[$i]['is_virtual'] = 0;
    $arr[$i]['remote_ip'] = "";
    $arr[$i]['total_refunded'] ="";
    $arr[$i]['base_total_refunded'] ="";
    $arr[$i]['total_canceled'] = "";
    $arr[$i]['total_invoiced'] = "";
	$arr[$i]['shipping_prefix'] = "";
	$name = $fields['shipping_name'];
	$fullname = explode(" ",$name);
	//echo "<pre>";
	//print_r($fullname);	
	//exit;		
	$arr[$i]['shipping_firstname'] = $fullname['0'];
	$arr[$i]['shipping_middlename'] = "";
	$arr[$i]['shipping_lastname'] = $fullname['1'];
	$arr[$i]['shipping_suffix'] = "";
	$arr[$i]['shipping_street_full'] = $fields['shipping_address1'];
	$arr[$i]['shipping_city'] = $fields['shipping_city'];
	$arr[$i]['shipping_region'] = $fields['shipping_state1'];
	$arr[$i]['shipping_country'] = $fields['shipping_countryid'];
	$arr[$i]['shipping_postcode'] = $fields['shipping_zip'];	
	$arr[$i]['shipping_company'] = $fields['shipping_company'];
	$arr[$i]['shipping_fax'] ="";
	if($fields['payment_method_name']=="PayPal Express Checkout")
	{			
	$arr[$i]['payment_method'] = "paypaluk_express"; //paypaluk_express
	}
	else if($fields['payment_method_name']=="PayPal Payflow Pro")
	{
		$arr[$i]['payment_method'] = "verisign";//"verisign";
	}
	else
	{
	}
	
		if($fields['oid'])
		{
			$total_qnt='';
			$row_count = 0;
			$rows = array();
			
			$orders_content 	= $conn->query('SELECT * FROM orders_content where oid='.$fields[oid].'');
			foreach($orders_content as $res)
			{
				$row_count++;
				if($row_count>1)
				{	//echo "<pre>";
					//echo $row_count;
					$arr[$i]['order_id'] = '';
					$arr[$i]['email'] ='';
					$arr[$i]['firstname'] ='';
					$arr[$i]['lastname'] ='';
					$arr[$i]['prefix'] = '';		
					$arr[$i]['middlename'] ='';		
					$arr[$i]['suffix'] ='';
					$arr[$i]['taxvat'] ='';					
					$arr[$i]['created_at'] ='';
					$arr[$i]['updated_at'] ='';
					$arr[$i]['invoice_created_at'] ='';
					$arr[$i]['shipment_created_at'] ='';
					$arr[$i]['creditmemo_created_at'] = '';
					$arr[$i]['tax_amount']= '';		
				    $arr[$i]['base_tax_amount']= '';
				    $arr[$i]['discount_amount']= '';
				    $arr[$i]['base_discount_amount']='';
				    $arr[$i]['shipping_tax_amount']= '';
				    $arr[$i]['base_shipping_tax_amount']= '';
				    $arr[$i]['base_to_global_rate']= '';
				    $arr[$i]['base_to_order_rate']= '';
				    $arr[$i]['store_to_base_rate']= '';
				    $arr[$i]['store_to_order_rate']= '';
					$arr[$i]['subtotal_incl_tax']='';
				    $arr[$i]['base_subtotal_incl_tax']='';
					$arr[$i]['coupon_code']='';
					$arr[$i]['shipping_incl_tax']= '';
				    $arr[$i]['base_shipping_incl_tax']= '';
				    $arr[$i]['shipping_method']= '';
				    $arr[$i]['shipping_amount']= '';
				    $arr[$i]['subtotal']= '';
				    $arr[$i]['base_subtotal']= '';
				    $arr[$i]['grand_total']= '';
				    $arr[$i]['base_grand_total']= '';
				    $arr[$i]['base_shipping_amount']= '';
				    $arr[$i]['adjustment_positive']= '';
				    $arr[$i]['adjustment_negative']= '';
 					$arr[$i]['refunded_shipping_amount']= '';
				    $arr[$i]['base_refunded_shipping_amount']= '';
				    $arr[$i]['refunded_subtotal']= '';
				    $arr[$i]['base_refunded_subtotal']= '';
				    $arr[$i]['refunded_tax_amount']= '';
				    $arr[$i]['base_refunded_tax_amount']= '';
				    $arr[$i]['refunded_discount_amount']= '';
					$arr[$i]['base_refunded_discount_amount']= '';
				    $arr[$i]['store_id']= '';
					$arr[$i]['order_status']='';
					$arr[$i]['order_state']='';
				    $arr[$i]['hold_before_state']= '';
				    $arr[$i]['hold_before_status']= '';
				    $arr[$i]['store_currency_code'] ='';
				    $arr[$i]['base_currency_code'] = '';
				    $arr[$i]['order_currency_code'] = '';
				    $arr[$i]['total_paid'] = '';
				    $arr[$i]['base_total_paid'] = '';	
				    $arr[$i]['is_virtual'] = '';
				    $arr[$i]['remote_ip'] = '';
				    $arr[$i]['total_refunded'] ='';
				    $arr[$i]['base_total_refunded'] ='';
				    $arr[$i]['total_canceled'] = '';
				    $arr[$i]['total_invoiced'] = '';
					$arr[$i]['customer_id'] = '';									
					$arr[$i]['billing_prefix'] = '';
					$arr[$i]['billing_firstname'] = '';
					$arr[$i]['billing_middlename'] = '';
					$arr[$i]['billing_lastname'] ='';
					$arr[$i]['billing_suffix'] = '';
					$arr[$i]['billing_street_full'] = '';
					$arr[$i]['billing_city'] = '';
					$arr[$i]['billing_region'] = '';
					$arr[$i]['billing_country'] = '';
					$arr[$i]['billing_postcode'] = '';
					$arr[$i]['billing_telephone'] ='';
					$arr[$i]['billing_company'] = '';					
					$arr[$i]['billing_fax'] ='';					
					$arr[$i]['customer_id1'] = '';
					$arr[$i]['shipping_prefix'] = '';
					$arr[$i]['shipping_firstname'] ='';
					$arr[$i]['shipping_middlename'] = '';
					$arr[$i]['shipping_lastname'] = '';
					$arr[$i]['shipping_suffix'] = '';
					$arr[$i]['shipping_street_full'] = '';
					$arr[$i]['shipping_city'] = '';
					$arr[$i]['shipping_region'] = '';
					$arr[$i]['shipping_country'] = '';
					$arr[$i]['shipping_postcode'] ='';	
					$arr[$i]['shipping_telephone'] = '';		
					$arr[$i]['shipping_company'] = '';
					$arr[$i]['shipping_fax'] ='';
					$arr[$i]['payment_method'] ='';
					
					$arr[$i]['product_sku'] = $res['product_sub_id']!=''?$res['product_id']."/".$res['product_sub_id']:$res['product_id'];
					$arr[$i]['product_name'] = $res['title'];
					$arr[$i]['qty_ordered'] = $res['quantity'];
					$arr[$i]['qty_invoiced'] = $res['quantity'];
					$arr[$i]['qty_shipped'] = $res['quantity'];		
					$arr[$i]['qty_refunded'] = 0;
					$arr[$i]['qty_canceled'] = 0;
					$arr[$i]['product_type'] = 'simple';
					$arr[$i]['original_price'] = $res['price'];		
					$arr[$i]['base_original_price'] = $res['price'];
					$arr[$i]['row_total'] = ($res['price']*$res['quantity']);
					$arr[$i]['base_row_total'] = ($res['price']*$res['quantity']);
					$arr[$i]['row_weight'] = $res['weight'];
					$arr[$i]['price_incl_tax'] = $res['price'];
					$arr[$i]['base_price_incl_tax'] = $res['price'];
					$arr[$i]['product_tax_amount'] = $res['tax_amount'];		
					$arr[$i]['product_base_tax_amount'] = $res['tax_amount'];
					$arr[$i]['product_tax_percent'] = "";//$res['quantity'];
					$arr[$i]['product_discount'] = $res['discount_amount'];
					$arr[$i]['product_base_discount'] = $res['discount_amount'];
					$arr[$i]['product_discount_percent'] ="" ;//$res['quantity'];				
					$arr[$i]['is_child'] = 'no';
					$arr[$i]['product_option'] = "";//$res['quantity'];		
					$total_qnt  +=$res['quantity'];				
				}
				else
				{
				$origin_i = $i;
				$arr[$i]['product_sku'] = $res['product_sub_id']!=''?$res['product_id']."/".$res['product_sub_id']:$res['product_id'];
				$arr[$i]['product_name'] = $res['title'];
				$arr[$i]['qty_ordered'] = $res['quantity'];
				$arr[$i]['qty_invoiced'] = $res['quantity'];
				$arr[$i]['qty_shipped'] = $res['quantity'];		
				$arr[$i]['qty_refunded'] = 0;
				$arr[$i]['qty_canceled'] = 0;
				$arr[$i]['product_type'] = 'simple';
				$arr[$i]['original_price'] = $res['price'];		
				$arr[$i]['base_original_price'] = $res['price'];
				$arr[$i]['row_total'] = ($res['price']*$res['quantity']);
				$arr[$i]['base_row_total'] = ($res['price']*$res['quantity']);
				$arr[$i]['row_weight'] = $res['weight'];
				$arr[$i]['price_incl_tax'] = $res['price'];
				$arr[$i]['base_price_incl_tax'] = $res['price'];
				$arr[$i]['product_tax_amount'] = $res['tax_amount'];		
				$arr[$i]['product_base_tax_amount'] = $res['tax_amount'];
				$arr[$i]['product_tax_percent'] = "";//$res['quantity'];
				$arr[$i]['product_discount'] = $res['discount_amount'];
				$arr[$i]['product_base_discount'] = $res['discount_amount'];
				$arr[$i]['product_discount_percent'] ="" ;//$res['quantity'];				
				$arr[$i]['is_child'] = 'no';
				$arr[$i]['product_option'] = "";//$res['quantity'];		
				$total_qnt  +=$res['quantity'];	
				}
				$i++;
				//echo "<pre>";
				//print_r($res);
				}			
			$arr[$origin_i]['total_qty_ordered'] = $total_qnt;
			//echo count($row_count);
		}
		/*echo "<pre>";
		print_r($arr);
		exit;*/
		
		$header_arr = array('order_id','email','firstname','lastname','prefix','middlename','suffix','taxvat','created_at','updated_at','invoice_created_at','shipment_created_at','creditmemo_created_at','tax_amount','base_tax_amount','discount_amount','base_discount_amount','shipping_tax_amount','base_shipping_tax_amount','base_to_global_rate','base_to_order_rate','store_to_base_rate','store_to_order_rate','subtotal_incl_tax','base_subtotal_incl_tax','coupon_code','shipping_incl_tax','base_shipping_incl_tax','shipping_method','shipping_amount','subtotal','base_subtotal','grand_total','base_grand_total','base_shipping_amount','adjustment_positive','adjustment_negative','refunded_shipping_amount','base_refunded_shipping_amount','refunded_subtotal','base_refunded_subtotal','refunded_tax_amount','base_refunded_tax_amount','refunded_discount_amount','base_refunded_discount_amount','store_id','order_status','order_state','hold_before_state','hold_before_status','store_currency_code','base_currency_code','order_currency_code','total_paid','base_total_paid','is_virtual','total_qty_ordered','remote_ip','total_refunded','base_total_refunded','total_canceled','total_invoiced','customer_id','billing_prefix','billing_firstname','billing_middlename','billing_lastname','billing_suffix','billing_street_full','billing_city','billing_region','billing_country','billing_postcode','billing_telephone','billing_company','billing_fax','customer_id','shipping_prefix','shipping_firstname','shipping_middlename','shipping_lastname','shipping_suffix','shipping_street_full','shipping_city','shipping_region','shipping_country','shipping_postcode','shipping_telephone','shipping_company','shipping_fax','payment_method','product_sku','product_name','qty_ordered','qty_invoiced','qty_shipped','qty_refunded','qty_canceled','product_type','original_price','base_original_price','row_total','base_row_total','row_weight','price_incl_tax','base_price_incl_tax','product_tax_amount','product_base_tax_amount','product_tax_percent','product_discount','product_base_discount','product_discount_percent','is_child','product_option');
		if(!$headerDisplayed)
		{	
			fputcsv($fp1,$header_arr);		
			$headerDisplayed = true;
		}	
	
	fputcsv($fp1, $arr[$i]);	
	
	//$i++;
}
/*echo "<pre>";
    print_r($arr);
	print_R($header_arr);*/
	
$arr_keys = '';	
foreach($arr as $key=> $value) {
	foreach($header_arr as $head_key) {
		if(in_array($head_key,$arr_keys[$key]) && $head_key=='customer_id') {
			$csv_arr[$key]['customer_id1'] = $value['customer_id1']!=''?$value['customer_id1']:'';
		}
		else {
			$csv_arr[$key][$head_key] = $value[$head_key];
		}
		$arr_keys[$key][] = $head_key;
	}
	fputcsv($fp1,$csv_arr[$key]);
}
	//echo "final:";
//	echo "<pre>";
 //   print_r($csv_arr);
	//print_R($header_arr);
//exit;
echo "successfully done!";
fclose($fp1);
?>