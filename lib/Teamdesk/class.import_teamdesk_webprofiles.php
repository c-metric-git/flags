<?php 

                           

/**  

* @desc This class has functions to import the product, product attributes details from TeamDesk to Pinnacle. 

* @package  TeamDesk  

* @author Dinesh Nagdev

* @since 28-Jan-2013

*/ 

class TeamDeskWebprofiles {

    private $db;
    private $api;
    private $arrErrorLog;
    private $arrTDFields;   
    private $arrTDProdAttrFields;                           
    private $arrTDProductCategories;
    private $defaultTaxClassID;   
    private $productcount;
    private $dataset;      
    private $tdErrorLog; 
    private $update_rec;
    private $insert_rec;
    private $product_added_sku = array(); 
    private $blank_weights = array(); 
    private $image_not_found = array();
    private $product_counter = 0;
    private $csv_counter=0;
    private $fp;
    /* 

       CONSTRUCTOR

    */

    function __construct()

    {   

        $this->defaultTaxClassID  = 0;

    }    

   

   /**

    * @desc Function to connect to TeamDesk WebProfile table

    * @return boolean True on successful connection, False on unsuccessful connection

    * @since 28-Jan-2013

    * @author Dinesh Nagdev

    */
    private function connectToTeamdesk()
    {   
        /**
        * @desc code to create the teamdesk api object with the domain name and database id.
        */
        try {
            $td_api = new API(TD_DOMAIN,TD_DB_ID,array("trace" => true)); 
            /**
            * @desc code to check the user login and session
            */
            $td_api->login(TD_USERNAME, TD_PASSWORD);  
            $this->api = $td_api;
        }
        catch (Exception $e) {
            echo $this->tdErrorLog = '<br/>Caught exception: ' .$e->getMessage(). "<br/>";
            return $this->tdErrorLog;           
        }    
    }

    /**
    * @desc Function to import teamdesk products from the Web Profile table of teamdesk 
    * into the Products table of Pinnacle database
    * @since 28-Jan-2013
    * @author Dinesh Nagdev
    */
    public function importTeamdeskProduct()
    {
        $arrSoloTDProducts = array();
        $totalProductsUpdated = 0;
        $totalProductsAdded = 0;
        $totalProductEvents = 0;   
        $totalAttributesModified = 0;   
        $this->productcount = 0; 
        $this->update_rec = 0; 
        if(TD_LIVE_FLAG == 0) {
           $this->pinnacleflag = 'betaPinnacleId'; 
        }
        else {
           $this->pinnacleflag = 'pinnacleId'; 
        }  
        /**
        * @desc  connect to TeamDesk web profiles table
        */
        $this->connectToTeamDesk();
        if($this->api !='' )
        {
            $arrSoloTDProducts = $this->getTDProducts('Solo');     
            /**
            * @desc get all the product categories
            */
            $this->arrTDProductCategories = $this->getALLTDProductCategory();  
            /**
            * @desc get all attributes 
            */
            $arrTDProductsAttributes = $this->getTDProductsAttributes();  
            
            $arrParentTDProducts = $this->getTDProducts('Parent');
            /*echo '<pre>';  
            print_R($arrSoloTDProducts);
            print_R($arrTDProductsAttributes);
            print_R($this->arrTDProductCategories);  */ 
            /**
            * @desc code to create the simple products csv file for uploading in magento
            */
            //$cp = copy("var/import/products/products.csv","var/import/products/old_products.csv"); 
            $attribute_set='';   
            $product_type='';  
            $site_images_path = "/";  
           /**
            * @desc code to create the solo products csv file for uploading in magento
            */
            if (isset($arrSoloTDProducts) && is_array($arrSoloTDProducts) && count($arrSoloTDProducts) > 0) {
                $pr_type = 'Solo';
                $this->fp = fopen("var/import/products/SS_Products".$this->csv_counter.".csv","w+"); 
                $this->add_header_into_csv();   
                $this->add_data_into_csv($arrSoloTDProducts,$arrTDProductsAttributes,$pr_type);  
            }  // End of isset prduct loop
             
            /**
            * @desc get all the parent products - products which are not be added as options        
            */
            if (isset($arrTDProductsAttributes) && is_array($arrTDProductsAttributes) && count($arrTDProductsAttributes) > 0) {
                       foreach ($arrTDProductsAttributes as $key => $tdProductAttribute) {
                          foreach($tdProductAttribute as $tdProductAtt) {
                              $this->product_counter++;
                              $lowerpinnacleSKU = strtolower($tdProductAtt['PinnacleAttributeSKU']);  
                              $categories_counter = count($this->arrTDProductCategories[$lowerpinnacleSKU]); 
                              if($categories_counter==0) {
                                   $categories_counter=1;
                              }
                              if(($tdProductAtt["Product - Weight"]<= 0) || ($tdProductAtt["Product - Weight"]==''))  
                              {
                                  $blank_weights[$lowerpinnacleSKU] = $tdProductAtt["Product - Weight"];
                              }   
                              if(!in_array($tdProductAtt['PinnacleAttributeSku'],$this->product_added_sku) && ($tdProductAtt["Product - Weight"]>0)) {
                                  $this->product_added_sku[] = $tdProductAtt['PinnacleAttributeSku'];    
                                  for($i=0;$i<$categories_counter;$i++) { 
                                      $csv_row = array();
                                      if($i == 0) { 
                                            $filename='';
                                            $csv_row[] = $tdProductAtt['PinnacleAttributeSku']; //sku
                                            $csv_row[] = "";  //_store
                                            $csv_row[] = $tdProductAtt['Web Option Label']; //_attribute_set 
                                            $csv_row[] = "simple"; //_type 
                                            $csv_row[] = $this->arrTDProductCategories[$lowerpinnacleSKU][$i]['category_path'];  //_category
                                            $csv_row[] = "Default Category";  //_root_category
                                            $csv_row[] = "stripedsocks";  //_product_websites
                                            $csv_row[] = ""; //bulky
                                            $csv_row[] = $tdProductAtt['Web Option Label']=="Color"?$tdProductAtt["Attribute"]:""; //color 
                                            $csv_row[] = ""; //multi color  
                                            $csv_row[] = ""; //filter type
                                            $csv_row[] = ""; //filter size    
                                            $csv_row[] = ""; //custom_layout_update
                                            $csv_row[] = ""; //custom design  //ultimo/default
                                            $csv_row[] = "1 column"; //page layout two_columns_left
                                            $csv_row[] = "Disabled (do not show)"; //estimated_delivery_enable : Inherited  
                                            $csv_row[] = "Disabled (do not show)"; //estimated_shipping_enable :Inherited 
                                            $csv_row[] = $tdProductAtt['Product - Solo Profile Description']!=''?$tdProductAtt['Product - Solo Profile Description']:$tdProductAtt['Web Profile - Solo Product Description']; //description
                                            /************desc code to copy the image *************/
                                            $image_path = str_replace("http://myclownantics.com/","/home/myclown/public_html/",$tdProductAtt['imgLocationCustom']);
                                            $related_image_path = "/home/myclown/public_html/admin/CA_resize_500_500/".strtolower($tdProductAtt['Related Product']).".jpg"; 
                                            if(file_exists($image_path) && $tdProductAtt['imgLocationCustom']!='') {
                                                $filename = basename($tdProductAtt['imgLocationCustom']);
                                                if(!file_exists("media/import/".$filename)) {
                                                    copy($image_path,"media/import/".$filename);
                                                }    
                                            }
                                            else if(file_exists($related_image_path) && $tdProductAtt['imgLocationCustom']!='') {
                                                $filename = basename($tdProductAtt['imgLocationCustom']);
                                                if(!file_exists("media/import/".$filename)) {
                                                    copy($related_image_path,"media/import/".$filename);
                                                }    
                                            } 
                                            else {
                                                $image_not_found[$tdProductAtt['PinnacleAttributeSku']] =  $image_path;
                                            }   
                                            /**************End of code added for image *********/
                                            $csv_row[] = $site_images_path.$filename;  //image
                                            $csv_row[] = $site_images_path.$filename;  //small image      
                                            $csv_row[] = $site_images_path.$filename;  //thumbnail      
                                            $csv_row[] = $tdProductAtt['Web Option Label']=="Length"?$tdProductAtt["Attribute"]:"";  //length
                                            $csv_row[] = $tdProductAtt['Web Profile - meta_description']; //meta_description
                                            $csv_row[] = ""; //meta_keyword
                                            $csv_row[] = $tdProductAtt['Web Profile - meta_title'];  //meta_title
                                            $csv_row[] = "Use config"; //msrp_display_actual_price_type
                                            $csv_row[] = "No"; //msrp_enabled
                                            $csv_row[] = $tdProductAtt['Product - Solo Profile Display Name']!=''?$tdProductAtt['Product - Solo Profile Display Name']:$tdProductAtt['Web Profile - Display Name'];  //name
                                            $csv_row[] = $tdProductAtt['Web Profile - isNewProduct?']=='Yes'?date("Y-m-d"):'';  //News from date
                                            $csv_row[] = "Product Info Column"; //options_container 
                                            $csv_row[] = ""; //ormd
                                            $csv_row[] = $tdProductAtt['PriceCalced'];   //price
                                            $csv_row[] = $tdProductAtt['DiscountPriceCalced']>0?$tdProductAtt['DiscountPriceCalced']:"";   //special price 
                                            $csv_row[] = $tdProductAtt['Product - Solo Profile Description']!=''?$tdProductAtt['Product - Solo Profile Description']:$tdProductAtt['Web Profile - Solo Product Description'];   //short_description
                                            $csv_row[] = $tdProductAtt['Web Option Label']=="Size"?$tdProductAtt["Attribute"]:""; //size 
                                            $csv_row[] = $tdProductAtt['is_active']=="Yes"?1:2;  //status
                                            $csv_row[] = $tdProductAtt['Web Option Label']=="Style"?$tdProductAtt["Attribute"]:""; //style 
                                            $csv_row[] = "2";  //tax_class_id
                                            $csv_row[] = ""; //url_path
                                            $csv_row[] = "1"; //visibility  //$tdProductAtt["SearchDisplay"]=="Yes"?"4":
                                            $csv_row[] = $tdProductAtt["Product - Weight"];  //weight
                                            $csv_row[] = $tdProductAtt["Product - Quantity Available"];  //qty
                                            $csv_row[] = "0"; //min_qty
                                            $csv_row[] = "1"; //use_config_min_qty
                                            $csv_row[] = "0"; //is_qty_decimal
                                            $csv_row[] = "0"; //backorders
                                            $csv_row[] = "1"; //use_config_backorders
                                            $csv_row[] = "1"; //min_sale_qty
                                            $csv_row[] = "1"; //use_config_min_sale_qty
                                            $csv_row[] = "0"; //max_sale_qty
                                            $csv_row[] = "1"; //use_config_max_sale_qty
                                            $csv_row[] = $tdProductAtt["Product - Quantity Available"]>0 ? "1" : "0";  //is_in_stock
                                            $csv_row[] = "";  //notify_stock_qty
                                            $csv_row[] = "1"; //use_config_notify_stock_qty
                                            $csv_row[] = "0"; //manage_stock
                                            $csv_row[] = "1"; //use_config_manage_stock
                                            $csv_row[] = "0"; //stock_status_changed_auto
                                            $csv_row[] = "1"; //use_config_qty_increments
                                            $csv_row[] = "0"; //qty_increments
                                            $csv_row[] = "1";  //use_config_enable_qty_inc
                                            $csv_row[] = "0";  //enable_qty_increments
                                            $csv_row[] = "0"; //is_decimal_divided
                                            $csv_row[] = ""; //_links_related_sku
                                            $csv_row[] = ""; //_links_related_position
                                            $csv_row[] = ""; //_links_crosssell_sku
                                            $csv_row[] = ""; //_links_crosssell_position
                                            $csv_row[] = ""; //_links_upsell_sku
                                            $csv_row[] = "";  //_links_upsell_position
                                            $csv_row[] = "";  //_associated_sku
                                            $csv_row[] = ""; //_associated_default_qty
                                            $csv_row[] = ""; //_associated_position
                                            $csv_row[] = ""; //_tier_price_website
                                            $csv_row[] = ""; //_tier_price_customer_group
                                            $csv_row[] = ""; //_tier_price_qty
                                            $csv_row[] = ""; //_tier_price_price
                                            $csv_row[] = "";  //_group_price_website
                                            $csv_row[] = "";  //_group_price_customer_group
                                            $csv_row[] = ""; //_group_price_price
                                            $csv_row[] = "88"; //_media_attribute_id
                                            $csv_row[] = $site_images_path.$filename; //_media_image
                                            $csv_row[] = ""; //_media_lable
                                            $csv_row[] = "1"; //_media_position
                                            $csv_row[] = "0"; //_media_is_disabled
                                            $csv_row[] = ""; //$tdProductAtt["PinnacleAttributeSku"];  //_super_products_sku
                                            $csv_row[] = "";  //_super_attribute_code
                                            $csv_row[] = ""; //$tdProductAtt["Attribute"]; //_super_attribute_option
                                            $csv_row[] = "";//$tdProductAtt["Product - Quantity Available"];  //_super_attribute_price_corr
                                      }
                                      else {
                                            $csv_row[] = ""; //sku
                                            $csv_row[] = "";  //_store
                                            $csv_row[] = ""; //_attribute_set 
                                            $csv_row[] = ""; //_type 
                                            $csv_row[] = $this->arrTDProductCategories[$lowerpinnacleSKU][$i]['category_path'];  //_category
                                            $csv_row[] = "";  //_root_category
                                            $csv_row[] = "";  //_product_websites
                                            $csv_row[] = ""; //bulky
                                            $csv_row[] = ""; //color 
                                            $csv_row[] = ""; //multi color
                                            $csv_row[] = ""; //filter type  
                                            $csv_row[] = ""; //filter size  
                                            $csv_row[] = ""; //custom_layout_update
                                            $csv_row[] = ""; //custom design
                                            $csv_row[] = ""; //page layout
                                            $csv_row[] = ""; //estimated_delivery_enable   
                                            $csv_row[] = ""; //estimated_shipping_enable   
                                            $csv_row[] = ""; //description
                                            $csv_row[] = "";  //image
                                            $csv_row[] = "";  //small image      
                                            $csv_row[] = "";  //thumbnail  
                                            $csv_row[] = "";  //length
                                            $csv_row[] = ""; //meta_description
                                            $csv_row[] = ""; //meta_keyword
                                            $csv_row[] = "";  //meta_title
                                            $csv_row[] = ""; //msrp_display_actual_price_type
                                            $csv_row[] = ""; //msrp_enabled
                                            $csv_row[] = "";  //name
                                            $csv_row[] = "";  //News from date  
                                            $csv_row[] = ""; //options_container 
                                            $csv_row[] = ""; //ormd
                                            $csv_row[] = "";   //price
                                            $csv_row[] = "";   //special price    
                                            $csv_row[] = "";   //short_description
                                            $csv_row[] = ""; //size 
                                            $csv_row[] = "";  //status
                                            $csv_row[] = ""; //style 
                                            $csv_row[] = "";  //tax_class_id
                                            $csv_row[] = ""; //url_path
                                            $csv_row[] = ""; //visibility
                                            $csv_row[] = "";  //weight
                                            $csv_row[] = "";  //qty
                                            $csv_row[] = ""; //min_qty
                                            $csv_row[] = ""; //use_config_min_qty
                                            $csv_row[] = ""; //is_qty_decimal
                                            $csv_row[] = ""; //backorders
                                            $csv_row[] = ""; //use_config_backorders
                                            $csv_row[] = ""; //min_sale_qty
                                            $csv_row[] = ""; //use_config_min_sale_qty
                                            $csv_row[] = ""; //max_sale_qty
                                            $csv_row[] = ""; //use_config_max_sale_qty
                                            $csv_row[] = "";  //is_in_stock
                                            $csv_row[] = "";  //notify_stock_qty
                                            $csv_row[] = ""; //use_config_notify_stock_qty
                                            $csv_row[] = ""; //manage_stock
                                            $csv_row[] = ""; //use_config_manage_stock
                                            $csv_row[] = ""; //stock_status_changed_auto
                                            $csv_row[] = ""; //use_config_qty_increments
                                            $csv_row[] = ""; //qty_increments
                                            $csv_row[] = "";  //use_config_enable_qty_inc
                                            $csv_row[] = "";  //enable_qty_increments
                                            $csv_row[] = ""; //is_decimal_divided
                                            $csv_row[] = ""; //_links_related_sku
                                            $csv_row[] = ""; //_links_related_position
                                            $csv_row[] = ""; //_links_crosssell_sku
                                            $csv_row[] = ""; //_links_crosssell_position
                                            $csv_row[] = ""; //_links_upsell_sku
                                            $csv_row[] = "";  //_links_upsell_position
                                            $csv_row[] = "";  //_associated_sku
                                            $csv_row[] = ""; //_associated_default_qty
                                            $csv_row[] = ""; //_associated_position
                                            $csv_row[] = ""; //_tier_price_website
                                            $csv_row[] = ""; //_tier_price_customer_group
                                            $csv_row[] = ""; //_tier_price_qty
                                            $csv_row[] = ""; //_tier_price_price
                                            $csv_row[] = "";  //_group_price_website
                                            $csv_row[] = "";  //_group_price_customer_group
                                            $csv_row[] = ""; //_group_price_price
                                            $csv_row[] = ""; //_media_attribute_id
                                            $csv_row[] = ""; //_media_image
                                            $csv_row[] = ""; //_media_lable
                                            $csv_row[] = ""; //_media_position
                                            $csv_row[] = ""; //_media_is_disabled
                                            $csv_row[] = ""; //$tdProduct["PinnacleAttributeSku"];  //_super_products_sku
                                            $csv_row[] = "";  //_super_attribute_code
                                            $csv_row[] = ""; //$tdProduct["Attribute"]; //_super_attribute_option
                                            $csv_row[] = "";//$tdProduct["Product - Quantity Available"];  //_super_attribute_price_corr
                                      } 
                                      fputcsv($this->fp,$csv_row);
                                   }// End of for counter loop   
                          }   
                            /*$productSKU = (string) $tdProductAtt['Related Web Profile'];
                            $productSKU = strtolower($productSKU);
                            $arrProductAttributes[$productSKU][] = $tdProductAtt;  */
                           if($this->product_counter % 1000 == 0) {
                                 $this->csv_counter++;
                                 fclose($this->fp);
                                 $this->fp = fopen("var/import/products/SS_Products".$this->csv_counter.".csv","w+");
                                 $this->add_header_into_csv();   
                           }  
                        }   
                      }    
            }
            /**
            * @desc code to create the attributes configurable products csv file for uploading in magento
            */
            if (isset($arrParentTDProducts) && is_array($arrParentTDProducts) && count($arrParentTDProducts) > 0) {
                $pr_type = 'Parent';
                $this->add_data_into_csv($arrParentTDProducts,$arrTDProductsAttributes,$pr_type);   
            }  // End of isset prduct loop
            $this->send_missing_product_images_mail();
            fclose($this->fp); 
            echo "<br />total products count =".$this->product_counter;
            echo "<br />total csv counter =".$this->csv_counter;
            $_SESSION['product_csv_counter'] = $this->csv_counter; 
             /**
             * @desc code to check the new products added in the csv file or not.  
             */
             /*$fp = fopen("var/import/products/old_products.csv","r");
             $line_counter = 0;
             if($fp) { 
                 while(! feof($fp)) {
                      $line = fgetcsv($fp);
                     if($line_counter != 0) {
                         if($line[0] !='') {
                            $product_already_in_db[] = $line[0];
                         }   
                     } 
                     $line_counter++;   
                 }
                 fclose($fp);
             }
             $fp = fopen("var/import/products/products.csv","r");
             $line_counter = 0;
             if($fp) { 
                 while(! feof($fp)) {
                      $line = fgetcsv($fp);
                     if($line_counter != 0) {
                         if($line[0] !='') {
                             if(!in_array($line[0],$product_already_in_db)) {
                                $new_product_array[] = $line[0];
                             }   
                         }   
                     } 
                     $line_counter++;   
                 }
                 fclose($fp);
             }
             foreach($new_product_array as $sku) {
                  if(!$this->isProductEventExist($sku)){
                        $sendToProductEvent[$totalProductEvents]['Related Product'] = $sku;
                        $sendToProductEvent[$totalProductEvents]['Label'] = 'Website Start Record';
                        $sendToProductEvent[$totalProductEvents]['Related Website'] = 'SS';
                        $sendToProductEvent[$totalProductEvents]['Start Date'] = new DateTime(date('Y-m-d'));
                        $sendToProductEvent[$totalProductEvents]['End Date'] = NULL;
                        $sendToProductEvent[$totalProductEvents]['isNewProduct?'] = 1;
                        $totalProductEvents++;
                    }
            }
            if($sendToProductEvent && sizeof($sendToProductEvent)>0){
                $TDTable = "Product Events";
                $schemaArray = array("Related Product", "Label", "Related Website", "Start Date", "End Date", "isNewProduct?");
                $record_inserted = $this->insertTDData($TDTable, $sendToProductEvent, $schemaArray);
            }*/   
            /*************End of code added by dhiraj for new products ***************/
           return $strReturn;
        }                         
        else {
             return $this->tdErrorLog;    
        }
    }

    function send_missing_product_images_mail() 
    {
            /**
            * @desc code to import the send email of not found images of products  
            */
             $headers  = 'MIME-Version: 1.0' . "\r\n";
             $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
             $not_found_images_counter = count($this->image_not_found);
             $mail_message = "<br />Total Product Images Not Found: ".$not_found_images_counter." <br /><br />Below images are not found: <br />";
             $mail_message .= "<br />SKU&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Image Location<br />";
             foreach($this->image_not_found as $key=> $image_pr) {
                 $mail_message .= $key."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$image_pr."<br />"; 
             } 
             $mail_message .= "<br />Total Product Weight Not Found: ".count($this->blank_weights)." <br /><br />Below weights are not found: <br />";
             $mail_message .= "<br />SKU&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Product Weight<br />";
             foreach($this->blank_weights as $key=> $weight_pr) {
                 $mail_message .= $key."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$weight_pr."<br />"; 
             } 
             mail("dhiraj@clownantics.com","Products Details not found on SS site ",$mail_message,$headers);  
    }    
    function add_data_into_csv($arrSoloTDProducts,$arrTDProductsAttributes,$pr_type='Solo')
    { 
        $site_images_path = "/";     
        foreach ($arrSoloTDProducts as $tdProduct) {  
					   $this->product_counter++;
                       $lowerpinnacleSKU = strtolower($tdProduct['PinnacleSKU']);
                       if(($tdProduct["weight_calced"]<= 0) || ($tdProduct["weight_calced"]==''))  
                       {
                          $this->blank_weights[$lowerpinnacleSKU] = $tdProduct["weight_calced"];
                       }
                       if(!in_array($tdProduct['PinnacleSKU'],$this->product_added_sku)  && ($tdProduct["weight_calced"]>0)) { 
                            $this->product_added_sku[] = $tdProduct['PinnacleSKU']; 
                            if ($tdProduct['Product - SS_Colors']!=''){    
                                $attribute_set='Multi Color';
                                $color_arr = str_replace("(","",$tdProduct['Product - SS_Colors']); 
                                $color_arr = str_replace(")","",$color_arr); 
                                $color_arr = explode(",",$color_arr);
                            }
                            if ($tdProduct['Product - Filter - Size - DisplayLabelEnteredCA']!=''){
                                if($attribute_set != '') {
                                  $attribute_set .='/';
                                }    
                                $attribute_set .='Filter Size';
                            }
                            if ($tdProduct['Product - Type - DisplayLabelEnteredCA']!=''){    
                                if($attribute_set != '') {
                                  $attribute_set .='/';
                                }
                                $attribute_set .='Filter Type';
                            }
                            if($pr_type == 'Parent') { 
                                 $attribute_set = $arrTDProductsAttributes[$lowerpinnacleSKU][0]['Web Option Label']; 
                                 $product_type = "configurable"; 
                            }
                            else {    
                                //$attribute_set='Default';
                                $product_type = "simple"; 
                            }
                            if($attribute_set=='') {    
                                $attribute_set='Default';
                            }
                            if($product_type=='') {    
                                $product_type = "simple"; 
                            }
                            $root_category = "Default Category"; 
                            $categories_counter = count($this->arrTDProductCategories[$lowerpinnacleSKU]);
                            $attributes_counter = count($arrTDProductsAttributes[$lowerpinnacleSKU]);
                            $color_counter = count($color_arr);
                            if($categories_counter > $attributes_counter){
                                $largest_counter = $categories_counter;
                            }
                            else {
                                $largest_counter = $attributes_counter; 
                            }
                            if($color_counter > $largest_counter){
                                $largest_counter = $color_counter;
                            }
                            if($largest_counter==0) {
                                $largest_counter=1;
                            }
                            for($i=0;$i<$largest_counter;$i++) {
                                    $csv_row = array();
                                        if($i == 0) {
                                            $filename='';
                                            $csv_row[] = $tdProduct['PinnacleSKU']; //sku
                                            $csv_row[] = "";  //_store
                                            $csv_row[] = $attribute_set; //_attribute_set 
                                            $csv_row[] = $product_type; //_type 
                                            $csv_row[] = $this->arrTDProductCategories[$lowerpinnacleSKU][$i]['category_path'];  //_category
                                            $csv_row[] = $root_category;  //_root_category
                                            $csv_row[] = "stripedsocks";  //_product_websites
                                            $csv_row[] = ""; //bulky
                                            $csv_row[] = ""; //color 
                                            $csv_row[] = trim($color_arr[$i]); //multicolor  
                                            $csv_row[] = $tdProduct['Product - Type - DisplayLabelEnteredCA']; //filter_type  
                                            $csv_row[] = $tdProduct['Product - Filter - Size - DisplayLabelEnteredCA']; //filter_size 
                                            $csv_row[] = ""; //custom_layout_update
                                            $csv_row[] = ""; //custom_design   ultimo/default
                                            $csv_row[] = "1 column";  //page_layout
                                            $csv_row[] = "Disabled (do not show)"; //estimated_delivery_enable Inherited  
                                            $csv_row[] = "Disabled (do not show)"; //estimated_shipping_enable  Inherited  
                                            $csv_row[] = $tdProduct['Solo Product Description']; //description
                                            /****
                                            * @desc code for copying the images
                                            */
                                            $image_path = str_replace("http://myclownantics.com/","/home/myclown/public_html/",$tdProduct['imgLocationCustom']);
                                            $related_image_path = "/home/myclown/public_html/admin/CA_resize_500_500/".strtolower($tdProduct['Related Product']).".jpg"; 
                                            if(file_exists($image_path) && $tdProduct['imgLocationCustom']!='') {
                                                $filename = basename($tdProduct['imgLocationCustom']);
                                                if(!file_exists("media/import/".$filename)) {
                                                    copy($image_path,"media/import/".$filename);
                                                }    
                                            } 
                                            else if(file_exists($related_image_path) && $tdProduct['imgLocationCustom']!='') {
                                                $filename = basename($tdProduct['imgLocationCustom']);
                                                if(!file_exists("media/import/".$filename)) {
                                                    copy($related_image_path,"media/import/".$filename);
                                                }   
                                            } 
                                            else {
                                                $this->image_not_found[$tdProduct['PinnacleSKU']] =  $image_path;
                                            }  
                                            /**************End of code for images *********************/                
                                            $csv_row[] = $site_images_path.$filename;  //image
                                            $csv_row[] = $site_images_path.$filename;  //small image      
                                            $csv_row[] = $site_images_path.$filename;  //thumbnail  
                                            $csv_row[] = "";  //length
                                            $csv_row[] = $tdProduct['meta_description']; //meta_description
                                            $csv_row[] = $tdProduct['meta_keywords']; //meta_keyword
                                            $csv_row[] = $tdProduct['meta_title'];  //meta_title
                                            $csv_row[] = "Use config"; //msrp_display_actual_price_type
                                            $csv_row[] = "No"; //msrp_enabled
                                            $csv_row[] = $tdProduct['Display Name'];  //name
                                            $csv_row[] = $tdProduct['isNewProduct?']=='Yes'?date("Y-m-d"):'';  //News from date
                                            $csv_row[] = "Product Info Column"; //options_container 
                                            $csv_row[] = ""; //ormd
                                            $csv_row[] = $tdProduct['PriceCalced'];   //price 
                                            $csv_row[] = $tdProduct['DiscountPriceCalced']>0?$tdProduct['DiscountPriceCalced']:"";   //special price   
                                            $csv_row[] = $tdProduct['Solo Product Description'];   //short_description
                                            $csv_row[] = ""; //size 
                                            $csv_row[] = $tdProduct['is_visible']=="Yes"?1:2;  //status
                                            $csv_row[] = ""; //style 
                                            $csv_row[] = "2";  //tax_class_id
                                            $csv_row[] = ""; //url_path
                                            $csv_row[] = "4";//$product_type=='configurable'?"2":"4"; //visibility
                                            $csv_row[] = $tdProduct["weight_calced"];  //weight
                                            $csv_row[] = $tdProduct["Quantity Available"];  //qty
                                            $csv_row[] = "0"; //min_qty
                                            $csv_row[] = "1"; //use_config_min_qty
                                            $csv_row[] = "0"; //is_qty_decimal
                                            $csv_row[] = "0"; //backorders
                                            $csv_row[] = "1"; //use_config_backorders
                                            $csv_row[] = "1"; //min_sale_qty
                                            $csv_row[] = "1"; //use_config_min_sale_qty
                                            $csv_row[] = "0"; //max_sale_qty
                                            $csv_row[] = "1"; //use_config_max_sale_qty
                                            $csv_row[] = $tdProduct["Quantity Available"]>0 ? "1" : "0";  //is_in_stock
                                            $csv_row[] = "";  //notify_stock_qty
                                            $csv_row[] = "1"; //use_config_notify_stock_qty
                                            $csv_row[] = "0"; //manage_stock
                                            $csv_row[] = "1"; //use_config_manage_stock
                                            $csv_row[] = "0"; //stock_status_changed_auto
                                            $csv_row[] = "1"; //use_config_qty_increments
                                            $csv_row[] = "0"; //qty_increments
                                            $csv_row[] = "1";  //use_config_enable_qty_inc
                                            $csv_row[] = "0";  //enable_qty_increments
                                            $csv_row[] = "0"; //is_decimal_divided
                                            $csv_row[] = ""; //_links_related_sku
                                            $csv_row[] = ""; //_links_related_position
                                            $csv_row[] = ""; //_links_crosssell_sku
                                            $csv_row[] = ""; //_links_crosssell_position
                                            $csv_row[] = ""; //_links_upsell_sku
                                            $csv_row[] = "";  //_links_upsell_position
                                            $csv_row[] = "";  //_associated_sku
                                            $csv_row[] = ""; //_associated_default_qty
                                            $csv_row[] = ""; //_associated_position
                                            $csv_row[] = ""; //_tier_price_website
                                            $csv_row[] = ""; //_tier_price_customer_group
                                            $csv_row[] = ""; //_tier_price_qty
                                            $csv_row[] = ""; //_tier_price_price
                                            $csv_row[] = "";  //_group_price_website
                                            $csv_row[] = "";  //_group_price_customer_group
                                            $csv_row[] = ""; //_group_price_price
                                            $csv_row[] = "88"; //_media_attribute_id
                                            $csv_row[] = $site_images_path.$filename; //_media_image
                                            $csv_row[] = ""; //_media_lable
                                            $csv_row[] = "1"; //_media_position
                                            $csv_row[] = "0"; //_media_is_disabled
                                            $csv_row[] = $arrTDProductsAttributes[$lowerpinnacleSKU][$i]['PinnacleAttributeSku'];; //$tdProduct["PinnacleAttributeSku"];  //_super_products_sku
                                            $csv_row[] = strtolower($arrTDProductsAttributes[$lowerpinnacleSKU][$i]['Web Option Label']);  //_super_attribute_code
                                            $csv_row[] = $arrTDProductsAttributes[$lowerpinnacleSKU][$i]['Attribute']; //$tdProduct["Attribute"]; //_super_attribute_option
                                            $price = 0;    
                                            $tmpProductPrice = 0;
                                            $attributePrice = 0; 
                                            if ($tdProduct['DiscountPriceCalced'] > 0) {
                                                    $tmpProductPrice = $tdProduct['DiscountPriceCalced'];
                                            }
                                            else {
                                                    $tmpProductPrice = $tdProduct['PriceCalced'];
                                            }
                                            if ($arrTDProductsAttributes[$lowerpinnacleSKU][$i]['DiscountPriceCalced'] > 0) {
                                                $attributePrice =  $arrTDProductsAttributes[$lowerpinnacleSKU][$i]['DiscountPriceCalced'];
                                            }
                                            else {
                                                $attributePrice =  $arrTDProductsAttributes[$lowerpinnacleSKU][$i]['PriceCalced'];
                                            }       
                                            if ($attributePrice != 0 && $attributePrice != '') {
                                                 if ($tmpProductPrice > $attributePrice) {
                                                     $price  = $tmpProductPrice - $attributePrice;    
                                                 } 
                                                 elseif ($tmpProductPrice < $attributePrice) {
                                                     $price  = $attributePrice - $tmpProductPrice; 
                                                 } 
                                            }  
                                            $csv_row[] = $price;//_super_attribute_price_corr
                                        }
                                        else {
                                                $csv_row[] = ""; //sku
                                                $csv_row[] = "";  //_store
                                                $csv_row[] = ""; //_attribute_set 
                                                $csv_row[] = ""; //_type 
                                                $csv_row[] = $this->arrTDProductCategories[$lowerpinnacleSKU][$i]['category_path'];  //_category
                                                $csv_row[] = "";  //_root_category
                                                $csv_row[] = "";  //_product_websites
                                                $csv_row[] = ""; //bulky
                                                $csv_row[] = ""; //color 
                                                $csv_row[] = trim($color_arr[$i]); //multicolor 
                                                $csv_row[] = ""; //filter_type 
                                                $csv_row[] = ""; //filter_size 
                                                $csv_row[] = ""; //custom_layout_update
                                                $csv_row[] = ""; //custom design
                                                $csv_row[] = ""; //page layout
                                                $csv_row[] = ""; //estimated_delivery_enable   
                                                $csv_row[] = ""; //estimated_shipping_enable   
                                                $csv_row[] = ""; //description
                                                $csv_row[] = "";  //image
                                                $csv_row[] = "";  //small image      
                                                $csv_row[] = "";  //thumbnail  
                                                $csv_row[] = "";  //length
                                                $csv_row[] = ""; //meta_description
                                                $csv_row[] = ""; //meta_keyword
                                                $csv_row[] = "";  //meta_title
                                                $csv_row[] = ""; //msrp_display_actual_price_type
                                                $csv_row[] = ""; //msrp_enabled
                                                $csv_row[] = "";  //name
                                                $csv_row[] = "";  //news from date 
                                                $csv_row[] = ""; //options_container 
                                                $csv_row[] = ""; //ormd
                                                $csv_row[] = "";   //price
                                                $csv_row[] = "";   //special price
                                                $csv_row[] = "";   //short_description
                                                $csv_row[] = ""; //size 
                                                $csv_row[] = "";  //status
                                                $csv_row[] = ""; //style 
                                                $csv_row[] = "";  //tax_class_id
                                                $csv_row[] = ""; //url_path
                                                $csv_row[] = ""; //visibility
                                                $csv_row[] = "";  //weight
                                                $csv_row[] = "";  //qty
                                                $csv_row[] = ""; //min_qty
                                                $csv_row[] = ""; //use_config_min_qty
                                                $csv_row[] = ""; //is_qty_decimal
                                                $csv_row[] = ""; //backorders
                                                $csv_row[] = ""; //use_config_backorders
                                                $csv_row[] = ""; //min_sale_qty
                                                $csv_row[] = ""; //use_config_min_sale_qty
                                                $csv_row[] = ""; //max_sale_qty
                                                $csv_row[] = ""; //use_config_max_sale_qty
                                                $csv_row[] = "";  //is_in_stock
                                                $csv_row[] = "";  //notify_stock_qty
                                                $csv_row[] = ""; //use_config_notify_stock_qty
                                                $csv_row[] = ""; //manage_stock
                                                $csv_row[] = ""; //use_config_manage_stock
                                                $csv_row[] = ""; //stock_status_changed_auto
                                                $csv_row[] = ""; //use_config_qty_increments
                                                $csv_row[] = ""; //qty_increments
                                                $csv_row[] = "";  //use_config_enable_qty_inc
                                                $csv_row[] = "";  //enable_qty_increments
                                                $csv_row[] = ""; //is_decimal_divided
                                                $csv_row[] = ""; //_links_related_sku
                                                $csv_row[] = ""; //_links_related_position
                                                $csv_row[] = ""; //_links_crosssell_sku
                                                $csv_row[] = ""; //_links_crosssell_position
                                                $csv_row[] = ""; //_links_upsell_sku
                                                $csv_row[] = "";  //_links_upsell_position
                                                $csv_row[] = "";  //_associated_sku
                                                $csv_row[] = ""; //_associated_default_qty
                                                $csv_row[] = ""; //_associated_position
                                                $csv_row[] = ""; //_tier_price_website
                                                $csv_row[] = ""; //_tier_price_customer_group
                                                $csv_row[] = ""; //_tier_price_qty
                                                $csv_row[] = ""; //_tier_price_price
                                                $csv_row[] = "";  //_group_price_website
                                                $csv_row[] = "";  //_group_price_customer_group
                                                $csv_row[] = ""; //_group_price_price
                                                $csv_row[] = ""; //_media_attribute_id
                                                $csv_row[] = ""; //_media_image
                                                $csv_row[] = ""; //_media_lable
                                                $csv_row[] = ""; //_media_position
                                                $csv_row[] = ""; //_media_is_disabled
                                                $csv_row[] = $arrTDProductsAttributes[$lowerpinnacleSKU][$i]['PinnacleAttributeSku'];; //$tdProduct["PinnacleAttributeSku"];  //_super_products_sku
                                                $csv_row[] = strtolower($arrTDProductsAttributes[$lowerpinnacleSKU][$i]['Web Option Label']);
                                                $csv_row[] = $arrTDProductsAttributes[$lowerpinnacleSKU][$i]['Attribute'];; //$tdProduct["Attribute"]; //_super_attribute_option
                                                $price = 0;    
                                                $tmpProductPrice = 0;
                                                $attributePrice = 0; 
                                                if ($tdProduct['DiscountPriceCalced'] > 0) {
                                                        $tmpProductPrice = $tdProduct['DiscountPriceCalced'];
                                                }
                                                else {
                                                        $tmpProductPrice = $tdProduct['PriceCalced'];
                                                }
                                                if ($arrTDProductsAttributes[$lowerpinnacleSKU][$i]['DiscountPriceCalced'] > 0) {
                                                    $attributePrice =  $arrTDProductsAttributes[$lowerpinnacleSKU][$i]['DiscountPriceCalced'];
                                                }
                                                else {
                                                    $attributePrice =  $arrTDProductsAttributes[$lowerpinnacleSKU][$i]['PriceCalced'];
                                                }       
                                                if ($attributePrice != 0 && $attributePrice != '') {
                                                     if ($tmpProductPrice > $attributePrice) {
                                                         $price  = $tmpProductPrice - $attributePrice;    
                                                     } 
                                                     elseif ($tmpProductPrice < $attributePrice) {
                                                         $price  = $attributePrice - $tmpProductPrice; 
                                                     } 
                                                }  
                                                $csv_row[] = $price; //_super_attribute_price_corr
                                        }        
                                        fputcsv($this->fp,$csv_row);
                                    } // End of for largest counter loop
                            } // End of if not in product added array  
                            if($this->product_counter % 1000 == 0) {
                                 $this->csv_counter++;  
                                 fclose($this->fp);
                                 $this->fp = fopen("var/import/products/SS_Products".$this->csv_counter.".csv","w+");
                                 $this->add_header_into_csv();   
                            }  
                   } // End of foreach product loop 
    }
    /**

    * @desc Function to get all the products from teamdesk 

    * @return array $arrProducts array of all the products

    * @since 29-Jan-2013

    * @author Dinesh Nagdev

    */
    private function getTDProducts($type='')
    {
        $arrProducts = array();
        /**
        * @desc  create the Teamdesk query, to fetch all the products that are marked as sendToPinnacle
        */ 
        if($type != '') {
            $type = " AND [Type]='".$type."'";
        }
        $arrQueries = "WHERE [is_visible] AND [SendToPinnacle] $type";
        /**
        * @desc  create string of columns to be retreived from the query  
        */       
        $strColumns = "[Product - SS_Colors],[Product - Filter - Size - DisplayLabelEnteredCA],[Product - Type - DisplayLabelEnteredCA],[PinnacleSKU],[Solo Product Description],[Product - imgLocation350],[Display Name],[PriceCalced],[DiscountPriceCalced],[overview],[is_visible],[weight_calced],[Quantity Available],[imgLocationCustom],[Related Product],[isNewProduct?],[meta_description],[meta_keywords],[meta_title]";   
        try
        {        
            $arrResults = $this->api->Query("SELECT TOP 500 ".$strColumns." FROM [SS Web Profile] ".$arrQueries." ORDER BY [PinnacleSKU]");    
            if (isset($arrResults->Rows)) { 
                 foreach ($arrResults->Rows as $productDetails) {
                    $last_record = $productDetails['PinnacleSKU'];
                    $arrProducts[] = $productDetails;
                 }  
                 $resultcount = 0;
                 while($resultcount == 0) {
                    $arrQueries = "WHERE [SendToPinnacle] AND [PinnacleSKU] > '".$last_record."' $type";
                    /**
                    * @desc  create string of columns to be retreived from the query - Related Category, is_primary, Related Web Profile, Priority  
                    */                      
                    try {  
                        $arrResults = $this->api->Query("SELECT TOP 500 ".$strColumns." FROM [SS Web Profile] ".$arrQueries." ORDER BY [PinnacleSKU]");
                    }
                    catch (Exception $e) {
                         echo $this->tdErrorLog = '<br/>Caught exception: ' .$e->getMessage(). "<br/>";
                         $strReturn .= $this->tdErrorLog;    
                    }    
                    /**
                    * @desc code to check the records and assign the same into the array
                    */
                    if (isset($arrResults->Rows)) {                                
                        /**
                        * @desc  loop through each category to get the pinnacle id from the categories table of teamdesk    
                        */
                        foreach ($arrResults->Rows as $productDetails) {
                            $last_record = $productDetails['PinnacleSKU'];
                            $arrProducts[] = $productDetails;
                        } 
                    }
                    if(count($arrResults->Rows) > 0)
                         $resultcount = 0;
                    else
                        $resultcount = 1;      
                }       
            }  
            echo "<br />Count of Web profiles ".count($arrProducts);
            return  $arrProducts;
       }
       catch (Exception $e) {
             echo $this->tdErrorLog = '<br/>Caught exception: ' .$e->getMessage(). "<br/>";
             die;
             $strReturn .= $this->tdErrorLog; 
             return  $strReturn;           
       }
    }
    /**
    * @desc Function to get all the products attributes from teamdesk 
    * @return array $arrResults array of all the products  attributes
    * @since 29-Jan-2013
    * @author Dinesh Nagdev
    */ 
    private function getTDProductsAttributes()    
    {
        $arrProductsAttributes = array();
        $arrQueries = "WHERE [sendToPinnacle] ";
        /**
        * @desc  create string of columns to be retreived from the query  
        */
        $strColumns = "[Web Option Label],[Attribute],[priority],[PriceCalced],[DiscountPriceCalced],[Product - Quantity Available],[Product - Weight],[is_modifier],[is_active],[PinnacleAttributeSku],[Related Web Profile],[Related Product],[Product - Solo Profile Display Name],[Product - Solo Profile Description],[Product - Solo Profile Overview],[Web Profile - overview],[Web Profile - Display Name],[Web Profile - Solo Product Description],[imgLocationCustom],[SearchDisplay],[Web Profile - isNewProduct?],[Web Profile - meta_description],[Web Profile - meta_title]";  
       try
       {
            $arrResults = $this->api->Query("SELECT ".$strColumns." FROM [SS Attribute] ".$arrQueries ." ORDER BY [priority] ASC");  
            if (isset($arrResults->Rows)) {    
                foreach ($arrResults->Rows as $tdProductAtt) {    
                     $productSKU = (string) $tdProductAtt['Related Web Profile'];
                     $productSKU = strtolower($productSKU);
                     $arrProductAttributes[$productSKU][] = $tdProductAtt;
                }
                //$arrProductsAttributes = $arrResults->Rows;            
            } 
            return  $arrProductAttributes;
       }
       catch (Exception $e) {
             echo $this->tdErrorLog = '<br/>Caught exception: ' .$e->getMessage(). "<br/>";
             $strReturn .= $this->tdErrorLog;    
             return  $strReturn;         
       }
    }
    private function add_header_into_csv() 
    {
             if($this->fp) {
                  $csv_line_break = "\n";
                  $product_header_row = array();
                  $product_header_row[] = "sku"; 
                  $product_header_row[] = "_store";
                  $product_header_row[] = "_attribute_set";
                  $product_header_row[] = "_type";
                  $product_header_row[] = "_category";
                  $product_header_row[] = "_root_category";
                  $product_header_row[] = "_product_websites";
                  $product_header_row[] = "bulky";
                  $product_header_row[] = "color";
                  $product_header_row[] = "multi_color";
                  $product_header_row[] = "filter_type";  
                  $product_header_row[] = "filter_size"; 
                  $product_header_row[] = "custom_layout_update";
                  $product_header_row[] = "custom_design"; 
                  $product_header_row[] = "page_layout";   
                  $product_header_row[] = "estimated_delivery_enable";
                  $product_header_row[] = "estimated_shipping_enable"; 
                  $product_header_row[] = "description"; 
                  $product_header_row[] = "image";
                  $product_header_row[] = "small_image"; 
                  $product_header_row[] = "thumbnail"; 
                  $product_header_row[] = "length";
                  $product_header_row[] = "meta_description";
                  $product_header_row[] = "meta_keyword";
                  $product_header_row[] = "meta_title";
                  $product_header_row[] = "msrp_display_actual_price_type";
                  $product_header_row[] = "msrp_enabled";
                  $product_header_row[] = "name";
                  $product_header_row[] = "news_from_date";
                  $product_header_row[] = "options_container";
                  $product_header_row[] = "ormd";
                  $product_header_row[] = "price";
                  $product_header_row[] = "special_price";
                  $product_header_row[] = "short_description";
                  $product_header_row[] = "size";
                  $product_header_row[] = "status";
                  $product_header_row[] = "style";
                  $product_header_row[] = "tax_class_id";
                  $product_header_row[] = "url_path";
                  $product_header_row[] = "visibility";
                  $product_header_row[] = "weight";
                  $product_header_row[] = "qty";
                  $product_header_row[] = "min_qty";
                  $product_header_row[] = "use_config_min_qty";
                  $product_header_row[] = "is_qty_decimal";
                  $product_header_row[] = "backorders";
                  $product_header_row[] = "use_config_backorders";
                  $product_header_row[] = "min_sale_qty";
                  $product_header_row[] = "use_config_min_sale_qty";
                  $product_header_row[] = "max_sale_qty";
                  $product_header_row[] = "use_config_max_sale_qty";
                  $product_header_row[] = "is_in_stock";
                  $product_header_row[] = "notify_stock_qty";
                  $product_header_row[] = "use_config_notify_stock_qty";
                  $product_header_row[] = "manage_stock";
                  $product_header_row[] = "use_config_manage_stock";
                  $product_header_row[] = "stock_status_changed_auto";
                  $product_header_row[] = "use_config_qty_increments";
                  $product_header_row[] = "qty_increments";
                  $product_header_row[] = "use_config_enable_qty_inc";
                  $product_header_row[] = "enable_qty_increments";
                  $product_header_row[] = "is_decimal_divided";
                  $product_header_row[] = "_links_related_sku";
                  $product_header_row[] = "_links_related_position";
                  $product_header_row[] = "_links_crosssell_sku";
                  $product_header_row[] = "_links_crosssell_position";
                  $product_header_row[] = "_links_upsell_sku";
                  $product_header_row[] = "_links_upsell_position";
                  $product_header_row[] = "_associated_sku";
                  $product_header_row[] = "_associated_default_qty";
                  $product_header_row[] = "_associated_position";
                  $product_header_row[] = "_tier_price_website";
                  $product_header_row[] = "_tier_price_customer_group";
                  $product_header_row[] = "_tier_price_qty";
                  $product_header_row[] = "_tier_price_price";
                  $product_header_row[] = "_group_price_website";
                  $product_header_row[] = "_group_price_customer_group";
                  $product_header_row[] = "_group_price_price";
                  $product_header_row[] = "_media_attribute_id";
                  $product_header_row[] = "_media_image";
                  $product_header_row[] = "_media_lable";
                  $product_header_row[] = "_media_position";
                  $product_header_row[] = "_media_is_disabled";
                  $product_header_row[] = "_super_products_sku";
                  $product_header_row[] = "_super_attribute_code";
                  $product_header_row[] = "_super_attribute_option";
                  $product_header_row[] = "_super_attribute_price_corr";
                  fputcsv($this->fp,$product_header_row);
           }
    }
    /**
    * @desc Function to get all the categories related to the product    
    * @return array $arrProductCategories array of all the categories
    * @since 28-Jan-2013 
    * @author Dinesh Nagdev
    */
    function getALLTDProductCategory() 
    {   
        $arrProductCategories = array();
        $total_result_rows = 0;
        /**
        * @desc  create string of columns to be retreived from the query - Related Category, is_primary, Related Web Profile, Priority  
        */                      
        $strColumns = "[Category - Label],[Related Web Profile],[Record ID#]";  //,[Priority],[meta_title],[meta_description] 
        try {             
            $arrResults = $this->api->Query("SELECT TOP 1000 ".$strColumns." FROM [SS Web Entry] ".$arrQueries." ORDER BY [Record ID#]");
            $total_result_rows = $total_result_rows + count($arrResults->Rows);
        } 
        catch (Exception $e) {
             echo $this->tdErrorLog = '<br/>Caught exception: ' .$e->getMessage(). "<br/>";
             $strReturn .= $this->tdErrorLog;    
             return  $strReturn;         
        }
        if (isset($arrResults->Rows)) {                                
                /**
                * @desc  loop through each category to get the pinnacle id from the categories table of teamdesk    
                */
                foreach ($arrResults->Rows as $productCategory) {
                    $productSKU = (string) $productCategory['Related Web Profile'];
                    $productSKU = strtolower($productSKU);
                    $category_path = str_replace(": ","/",$productCategory['Category - Label']); 
                    $arrProductCategories[$productSKU][] = array('category_path' => $category_path);
                } 
        }
        $last_record = $productCategory['Record ID#'];
        $resultcount = 0;
        while($resultcount == 0) {
            $arrQueries = "WHERE [Record ID#] > '".$last_record."'";
            /**
            * @desc  create string of columns to be retreived from the query - Related Category, is_primary, Related Web Profile, Priority  
            */                      
            $strColumns = "[Category - Label],[Related Web Profile],[Record ID#]"; //,[Priority],[meta_title],[meta_description] 
            try {  
                $arrResults = $this->api->Query("SELECT TOP 1000 ".$strColumns." FROM [SS Web Entry] ".$arrQueries." ORDER BY [Record ID#]");
                $total_result_rows = $total_result_rows + count($arrResults->Rows);  
            }
            catch (Exception $e) {
                 echo $this->tdErrorLog = '<br/>Caught exception: ' .$e->getMessage(). "<br/>";
                 $strReturn .= $this->tdErrorLog;    
                 return  $strReturn;         
            }    
            /**
            * @desc code to check the records and assign the same into the array
            */
            if (isset($arrResults->Rows)) {                                
                /**
                * @desc  loop through each category to get the pinnacle id from the categories table of teamdesk    
                */
                foreach ($arrResults->Rows as $productCategory) {
                    $productSKU = (string) $productCategory['Related Web Profile'];
                    $productSKU = strtolower($productSKU);
                    $category_path = str_replace(": ","/",$productCategory['Category - Label']); 
                    $arrProductCategories[$productSKU][] = array('category_path' => $category_path);
                }
                $last_record = $productCategory['Record ID#'];  
            }
            if(count($arrResults->Rows) > 0)
                 $resultcount = 0;
            else
                $resultcount = 1;      
        }       
        //echo "<br />Total Web Entries Rows =".$total_result_rows."<br />";
        return $arrProductCategories; 
    }
    
   
    /**                                                                        
    * @desc Function to update the inventory in pinnacle
    * @param 
    * @retuns array of products inventory updated
    * @since 19-Feb-2013 
    * @author Dinesh Nagdev
    */
    function updateProductInventory($db)
    {
        $arrReturn = array();         
        $totalProductsUpdate = 0;
        $totalAttributesUpdate = 0;
        /**
        * @desc connect to teamdesk tables
        */
        $this->connectToteamdesk();
        if ($this->api !='') {  
            // Update attributes
             /**
            * @desc  create the teamdesk query, to fetch all the categories
            */  
            $arrQueries = "WHERE [Product - Weight]>0";  
            $product_added = array();
            /**
            * @desc  create string of columns to be retreived from the query  
            */       
            $strColumns = "[PinnacleAttributeSku],[Product - Quantity Available]"; 
            $arrTDProductsAttributes = $this->api->Query("SELECT ".$strColumns." FROM [SS Attribute] ".$arrQueries);
            $i = 0;
            if (isset($arrTDProductsAttributes->Rows)) {  
                foreach ($arrTDProductsAttributes->Rows as $TDProductAttribute) {
                    $productSKU = $TDProductAttribute['PinnacleAttributeSku'];  
                    if(!in_array($productSKU,$product_added)) {
                       $db->query("SELECT entity_id from catalog_product_entity where sku='".$productSKU."'");
                       if($db->moveNext()) {
                           $product_id = $db->col['entity_id'];
                       } 
                       $db->reset();
                       $db->assignStr("qty", $TDProduct['Product - Quantity Available']);  
                       if ($TDProductAttribute['Product - Quantity Available'] <= 0){  
                             $is_in_stock ="0";
                       }    
                       else {
                             $is_in_stock="1"; 
                       } 
                       $db->assignStr("is_in_stock", $is_in_stock);  
                       if($product_id > 0) {
                            $db->update("cataloginventory_stock_item", "WHERE product_id = '".addslashes($productSKU)."'"); 
                            $totalAttributesUpdate++;
                       }
                       $product_added[] = $productSKU;
                    }   
                } 
            }
            // Get all products inventory for which send to pinnacle flag is true
            $arrQueries = "WHERE [sendToPinnacle] AND [weight_calced]>0 ";  
            /**
            * @desc  create string of columns to be retreived from the query  
            */       
            $strColumns = "[PinnacleSKU],[Quantity Available],[is_visible]"; 
            $arrTDProducts = $this->api->Query("SELECT ".$strColumns." FROM [SS Web Profile] ".$arrQueries);  
            if (isset($arrTDProducts->Rows) ) {                    
                /**
                * @desc loop through each record to add new product and update existing products
                */
                foreach ($arrTDProducts->Rows as $TDProduct) {  
                    $productSKU = $TDProduct['PinnacleSKU'];  
                    if(!in_array($productSKU,$product_added)) {   
                        $db->query("SELECT entity_id from catalog_product_entity where sku='".$productSKU."'");
                        if($db->moveNext()) {
                            $product_id = $db->col['entity_id'];
                        }
                        $db->reset();  
                        $db->assignStr("qty", $TDProduct['Quantity Available']);  
                        if ($TDProduct['Quantity Available'] <= 0){  
                             $is_in_stock ="0";
                        }    
                        else {
                             $is_in_stock="1"; 
                        } 
                        $db->assignStr("is_in_stock", $is_in_stock);    
                        if($product_id > 0) {
                            $db->update("cataloginventory_stock_item", "WHERE product_id='".addslashes($product_id)."'"); 
                            $totalProductsUpdate++;
                        }    
                        $product_added[] = $productSKU;
                    }    
                }
            }
        }     
       $arrReturn["totalProductsUpdated"]  = $totalProductsUpdate;
       $arrReturn["totalAttributesUpdate"] = $totalAttributesUpdate;
       return $arrReturn;
    } 
    
    /**
    * @desc function to update in TD table by dinesh
    */
    public function insertTDData($TDTable, $results, $schemaArray){
        // connect to TD
        //$this->connectToTeamdesk();
        // get schema in dataset
        try{
            $this->dataset = $this->api->GetSchema($TDTable,$schemaArray);
            // store result data in dataset
            $counter = 0;
            foreach($results as $key => $resultArray){
                $array_keys = array_keys($resultArray);
                foreach($array_keys as $key => $array_key){
                    $this->dataset->Rows[$counter][$array_key] = $resultArray[$array_key];
                }
                // update in TD
                if($counter==499) {
                    try{
                        $records_inserted = $this->api->Create($TDTable, $this->dataset);
                        $this->insert_rec +=  count($records_inserted);
                        $this->dataset->Rows = array();
                        $counter = 0;
                    }
                    catch (Exception $e) {
                        $this->tdErrorLog = '<br/>Caught exception: ' .$e->getMessage(). "<br/>";
                        echo $this->tdErrorLog;
                        return $this->tdErrorLog;           
                    }
                }            
                $counter++;
            }
            // update in TD
            if(count($this->dataset->Rows)) {  
                $records_inserted = $this->api->Create($TDTable, $this->dataset);
                $this->insert_rec +=  count($records_inserted);
            }
            // return number of records updated
            return $this->insert_rec;
        }
        catch (Exception $e) {
            $this->tdErrorLog = '<br/>Caught exception: ' .$e->getMessage(). "<br/>";
            echo $this->tdErrorLog;
            return $this->insert_rec;           
        }
        // return number of records updated
        return $this->insert_rec;
    }
    
    /**
    * @desc function to check propduct event exists or not - by Lav Butala
    */
    public function isProductEventExist($sku){
        $arrResults = $this->api->Query('SELECT [Related Product] FROM [Product Events] WHERE [Label]="Website Start Record" AND [Related Website]="SS" AND [isNewProduct?]=true AND [Related Product]="'.$sku.'"');
        if (isset($arrResults->Rows) && sizeof($arrResults->Rows)>0) {
            return true;
        }
        return false;
    }  
     
}

?>                                           

