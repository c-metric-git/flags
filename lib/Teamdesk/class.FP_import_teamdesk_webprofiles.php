<?php 

                           

/**  

* @desc This class has functions to import the product, product attributes details from TeamDesk to Pinnacle. 

* @package  TeamDesk  

* @author Dinesh Nagdev

* @since 28-Jan-2013

*/ 

class FPTeamDeskWebprofiles {

    private $db;
    private $api;
    private $arrErrorLog;
    private $arrTDFields;   
    private $arrTDProdAttrFields;                           
    private $arrTDProductCategories;
    private $arrTDProductFamilyContents; 
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
    private $bundle_product_counter = 0;   
    private $csv_counter=0;
    private $bundle_csv_counter=0;  
    private $fp;
    private $arrProductConfigurableAttributes;
    private $arrColor;
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
            echo $this->tdErrorLog = '<br/>Error connecting to TD. Caught exception: ' .$e->getMessage(). "<br/>";
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
        set_time_limit(0);
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
            //unset($_SESSION["arrTDProductCategories"]);  
            //unset($_SESSION["arrParentTDProducts"]);  
            /**
            * @desc code to get the product families contents.  
            */
            if($_SESSION['FParrTDProductFamilyContents'] !='' && isset($_SESSION['FParrTDProductFamilyContents'])) {
                $this->arrTDProductFamilyContents = $_SESSION['FParrTDProductFamilyContents'];
            }
            else {
                $this->arrTDProductFamilyContents = $this->getTDProductFamilyContents();   
                $_SESSION['FParrTDProductFamilyContents'] = $this->arrTDProductFamilyContents; 
            } 
            /**
            * @desc code to get the solo products.  
            */
            if($_SESSION['FParrSoloProducts'] !='' && isset($_SESSION['FParrSoloProducts'])) {   
                $arrSoloTDProducts = $_SESSION['FParrSoloProducts'];
            }
            else {
                $arrSoloTDProducts = $this->getTDProducts('Solo');
                $_SESSION['FParrSoloProducts'] = $arrSoloTDProducts; 
            }  
            /**
            * @desc get all the product categories
            */
            if($_SESSION['FParrTDProductCategories'] !='' && isset($_SESSION['FParrTDProductCategories'])) {
                $this->arrTDProductCategories = $_SESSION['FParrTDProductCategories'];
            }
            else { 
                $this->arrTDProductCategories = $this->getALLTDProductCategory();  
                $_SESSION['FParrTDProductCategories'] = $this->arrTDProductCategories; 
            }  
            /**
            * @desc get all attributes 
            */
            if($_SESSION['FParrTDProductsAttributes'] !='' && isset($_SESSION['FParrTDProductsAttributes'])) {
                $arrTDProductsAttributes = $_SESSION['FParrTDProductsAttributes'];
            }
            else {
                $arrTDProductsAttributes = $this->getTDProductsAttributes();   
                $_SESSION['FParrTDProductsAttributes'] = $arrTDProductsAttributes; 
            }
            if($_SESSION['FParrProductConfigurableAttributes'] !='' && isset($_SESSION['FParrProductConfigurableAttributes'])) {
                $this->arrProductConfigurableAttributes = $_SESSION['FParrProductConfigurableAttributes'];
            }
            else {
                $_SESSION['FParrProductConfigurableAttributes'] = $this->arrProductConfigurableAttributes; 
            }
            if($_SESSION['FParrParentTDProducts'] !='' && isset($_SESSION['FParrParentTDProducts'])) {
                $arrParentTDProducts = $_SESSION['FParrParentTDProducts'];
            }
            else {
                $arrParentTDProducts = $this->getTDProducts('Parent');
                $_SESSION['FParrParentTDProducts'] = $arrParentTDProducts; 
            }
            if($_SESSION['FParrBundleTDProducts'] !='' && isset($_SESSION['FParrBundleTDProducts'])) {
                $arrBundleTDProducts = $_SESSION['FParrBundleTDProducts'];
            }
            else {
                $arrBundleTDProducts = $this->getTDProducts('Build Your Own');
                $_SESSION['FParrBundleTDProducts'] = $arrBundleTDProducts; 
            }   
            echo "<br />count of prodct family content = ".count($_SESSION['FParrTDProductFamilyContents']);
            echo "<br />count of solo = ".count($_SESSION['FParrSoloProducts']);
            echo "<br />count of categories =".count($_SESSION['FParrTDProductCategories']);
            echo "<br />count of attributes = ".count($_SESSION['FParrTDProductsAttributes']);
            echo "<br />count of config attributes = ".count($_SESSION['FParrProductConfigurableAttributes']);  
            echo "<br />count of parent = ".count($_SESSION['FParrParentTDProducts']);   
            echo "<br />count of bundled products = ".count($_SESSION['FParrBundleTDProducts']);   
            /*  
            * @desc code to create the simple products csv file for uploading in magento
            */
            //$cp = copy("var/import/products/FP_products.csv","var/import/products/FP_old_products.csv"); 
            
            $attribute_set='';   
            $product_type='';  
            $site_images_path = "/"; 
            $type_of_product='';  
           /**
            * @desc code to create the solo products csv file for uploading in magento
            */
            if (isset($arrSoloTDProducts) && is_array($arrSoloTDProducts) && count($arrSoloTDProducts) > 0) {
                $pr_type = 'Solo';
                $this->fp = fopen("var/import/products/FP_Products".$this->csv_counter.".csv","w+"); 
                $this->add_header_into_csv();   
                $this->add_data_into_csv($arrSoloTDProducts,$arrTDProductsAttributes,$pr_type);  
            }  // End of isset prduct loop
            /**
            * @desc code to create the attributes configurable products csv file for uploading in magento
            */
            if (isset($arrParentTDProducts) && is_array($arrParentTDProducts) && count($arrParentTDProducts) > 0) {
                $pr_type = 'Parent';
                $this->add_data_into_csv($arrParentTDProducts,$arrTDProductsAttributes,$pr_type);   
            }  // End of isset prduct loop
            fclose($this->fp); 
            /**
            * @desc code to create the attributes configurable products csv file for uploading in magento
            */
            if (isset($arrBundleTDProducts) && is_array($arrBundleTDProducts) && count($arrBundleTDProducts) > 0) {   
                $this->fp = fopen("var/import/products/FP_BundleProducts".$this->bundle_csv_counter.".csv","w+"); 
                $pr_type = 'Parent';
                $type_of_product = 'bundle'; 
                $this->add_header_into_csv();   
                $this->add_data_into_csv($arrBundleTDProducts,$arrTDProductsAttributes,$pr_type,$type_of_product);   
            }  // End of isset prduct loop
            fclose($this->fp); 
            $this->send_missing_product_images_mail();
            echo "<br />total products count =".$this->product_counter;
            echo "<br />total csv counter =".$this->csv_counter;
            $_SESSION['product_csv_counter'] = $this->csv_counter; 
            echo "<br />Bundle product total csv counter =".$this->bundle_csv_counter;  
            $_SESSION['bundle_product_csv_counter'] = $this->bundle_csv_counter; 
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
             mail("dhiraj@clownantics.com","Products Details not found on FP site ",$mail_message,$headers);   
    } 
    function add_data_into_csv($arrSoloTDProducts,$arrTDProductsAttributes,$pr_type='Solo',$type_of_product='')
    {
        $site_images_path = "/"; 
        	 
        $counter=0;
        foreach ($arrSoloTDProducts as $tdProduct) { 
                       $color_arr = '';	
                       $brand='';
                       $size='';
                       $type='';
                       $character='';				   					   					   
					   
					   
                       $categories_counter = 0;
                       $attributes_counter = 0;                       
					   
                       if($type_of_product=='bundle') {
                            $this->bundle_product_counter++;           
                       }
                       else {
                           $this->product_counter++; 
                       }        
                       $lowerpinnacleSKU = strtolower($tdProduct['PinnacleSKU']);
                       if(($tdProduct["Product - Weight"]<= 0) || ($tdProduct["Product - Weight"]==''))  
                       {
                          $blank_weights[$lowerpinnacleSKU] = $tdProduct["Product - Weight"];
                       }
                       
                       if(!in_array($tdProduct['PinnacleSKU'],$this->product_added_sku)  && ($tdProduct["Product - Weight"]>0)) { 
                            $this->product_added_sku[] = $tdProduct['PinnacleSKU']; 
                            
							if ($tdProduct['Product - FP_Colors']!=''){    
                                $color_arr = explode("),",$tdProduct['Product - FP_Colors']);  
                            }
							if ($tdProduct['Brand']!=''){    
                                $brand = $tdProduct['Brand'];  
                            }
                            if ($tdProduct['DisplayLabelEnteredFPCharacterCalced']!=''){
                                $character = $tdProduct['DisplayLabelEnteredFPCharacterCalced']; 
                            }
							if ($tdProduct['FilterSizeDisplayLabelEnteredFPCalced']!=''){
                                $size = $tdProduct['FilterSizeDisplayLabelEnteredFPCalced']; 
                            }
                            if ($tdProduct['TypeDisplayLabelEnteredFPCalced']!=''){
                                $type = $tdProduct['TypeDisplayLabelEnteredFPCalced']; 
                            }
                            $web_option_label='';    
                            $concat_web=''; 
                            if($pr_type == 'Parent') {
                                 if($tdProduct['kitType'] == 'Fixed') {                                 
                                     $product_type = "grouped";  
                                 }
								 elseif($tdProduct['kitType'] == 'Build Your Own') {                                 
                                     $product_type = "bundle";  
                                 }
                                 else {   
                                    $product_type = "configurable";
                                 }    
                            }
                            else {
                                $product_type = "simple";
                            }
                            if($attribute_set=='') {    
                                $attribute_set = 'Brand & Size/Brush/FP Color/FP Design/Face Paint Kit Accessory/Kit Accessory/FP Size/Skin Color/Stencil Kit Accessory/FP Style/Title/Type';
                            }                                                                                                 
                            if($product_type=='') {    
                                $product_type = "simple"; 
                            }  
                            $related_product_family = '';
                            $related_counter =0;
                            $related_products='';
                            if ($tdProduct['Related Product Family']!=''){    
                                $related_product_family = $tdProduct['Related Product Family']; 
                                if(count($this->arrTDProductFamilyContents[$related_product_family]) > 0) {
                                    foreach($this->arrTDProductFamilyContents[$related_product_family] as $key => $rel_products) {
                                        $related_products[] =  $rel_products['related_web_profile'];
                                    }
                                }  
                            }                             
                            $related_counter = count($related_products);
                            $root_category = "Facepaint"; 
                            $categories_counter = count($this->arrTDProductCategories[$lowerpinnacleSKU]);
                            $attributes_counter = count($this->arrProductConfigurableAttributes[$lowerpinnacleSKU]);
                            if($categories_counter > $attributes_counter){
                                $largest_counter = $categories_counter;
                            }
                            else {
                                $largest_counter = $attributes_counter; 
                            }                                                       
                            if($related_counter > $largest_counter) {
                                $largest_counter = $related_counter;
                            } 
							$color_type='';
                            $fp_colors='';
                            foreach($color_arr as $f_color) {
                                $f_color = str_replace("(","",$f_color);
                                $f_color = str_replace(")","",$f_color);
                                $f_color = trim($f_color);
                                if(($f_color == 'Regular') || ($f_color == 'Metallic') || ($f_color == 'Neon')) {
                                    $color_type = $f_color;
                                }
                                else {
                                    if($f_color!='zz None') {
                                        $fp_colors[] = $f_color;
                                    }    
                                }    
                            }    
                            $fp_color_counter = count($fp_colors);
                            if($fp_color_counter > $largest_counter) {
                                $largest_counter = $fp_color_counter;
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
                                            $csv_row[] = "facepaint";  //_product_websites
                                            $csv_row[] = $tdProduct['Priority'];  //priority
                                            $csv_row[] = str_replace("http://www.facepaint.com/","",$tdProduct['url']); //url_key
                                            
                                                                                    
                                            $csv_row[] = $brand; //fp_filter_brand
											$csv_row[] = $size; //fp_filter_size
											$csv_row[] = $type; //fp_filter_types
											$csv_row[] = $character; //fp_filter_character
											$csv_row[] = $color_type; //fp_filter_color_types
											$csv_row[] = $fp_colors[$i]; //fp_filter_color											  
                                            
                                            $brand_and_size_value='';
                                            $brush_value='';
                                            $face_paint_kit_accessory_value='';
                                            $kit_accessory_value='';
                                            $fp_color_value='';
                                            $fp_design_value='';
                                            $fp_size_value='';
											$fp_style_value='';
											$stencil_kit_accessory_value='';
											$skin_color_value='';
											$title_value='';
											$type_value='';											
											
										    if(count($arrTDProductsAttributes[$lowerpinnacleSKU])>0) {
                                                foreach($arrTDProductsAttributes[$lowerpinnacleSKU] as $key => $attribute_details) {
                                                     if($attribute_details['Web Option Label']=="Brand & Size") {
                                                         $brand_and_size_value = $attribute_details["Attribute"];  
                                                      }
                                                      elseif($attribute_details['Web Option Label']=="Brush") {
                                                         $brush_value = $attribute_details["Attribute"];  
                                                      }
                                                      elseif($attribute_details['Web Option Label']=="Face Paint Kit Accessory") {
                                                         $face_paint_kit_accessory_value = $attribute_details["Attribute"];  
                                                      }
                                                      elseif($attribute_details['Web Option Label']=="Kit Accessory") {
                                                         $kit_accessory_value = $attribute_details["Attribute"];  
                                                      }
                                                      elseif($attribute_details['Web Option Label']=="Color") {
                                                         $fp_color_value = $attribute_details["Attribute"];  
                                                      }
                                                      elseif($attribute_details['Web Option Label']=="Design") {
                                                         $fp_design_value = $attribute_details["Attribute"];  
                                                      }
                                                      elseif($attribute_details['Web Option Label']=="Size") {
                                                          $fp_size_value = $attribute_details["Attribute"];  
                                                      }
													  elseif($attribute_details['Web Option Label']=="Style") {
                                                          $fp_style_value = $attribute_details["Attribute"];  
                                                      }
													  elseif($attribute_details['Web Option Label']=="Stencil Kit Accessory") {
                                                          $stencil_kit_accessory_value = $attribute_details["Attribute"];  
                                                      }
													  elseif($attribute_details['Web Option Label']=="Skin Color") {
                                                          $skin_color_value = $attribute_details["Attribute"];  
                                                      }
													  elseif($attribute_details['Web Option Label']=="Title") {
                                                          $title_value = $attribute_details["Attribute"];  
                                                      }
													  elseif($attribute_details['Web Option Label']=="Type") {
                                                          $type_value = $attribute_details["Attribute"];  
                                                      }
													    
                                                }
                                            }               
                                            $csv_row[] = $brand_and_size_value; //Brand & Size 
                                            $csv_row[] = $brush_value; //Brush  
                                            $csv_row[] = $face_paint_kit_accessory_value;  //Fac Paint Kit Accessory
                                            $csv_row[] = $kit_accessory_value;  //Kit Accessory        
                                            $csv_row[] = $fp_color_value; //Fp Color 
                                            $csv_row[] = $fp_design_value; //Fp Design 
                                            $csv_row[] = $fp_size_value; //Fp Size
											$csv_row[] = $fp_style_value; //Fp Style
											$csv_row[] = $stencil_kit_accessory_value; //Stencil Kit Accessory
											$csv_row[] = $skin_color_value; //Skin Color
											$csv_row[] = $title_value; //Title
											$csv_row[] = $type_value; //Type
											
											$csv_row[] = $tdProduct['flagDropShip']==1?"Yes":"No";
											$csv_row[] = $tdProduct['quantityPerPack']!=''?$tdProduct['quantityPerPack']:"";
											$csv_row[] = $tdProduct['Product - Type - Google Category']!=''?$tdProduct['Product - Type - Google Category']:"";
											$csv_row[] = $tdProduct['Product - Google Color']!=''?$tdProduct['Product - Google Color']:"";
											$csv_row[] = $tdProduct['Product - Google Gender']!=''?$tdProduct['Product - Google Gender']:"";
											$csv_row[] = $tdProduct['Product - Google Age']!=''?$tdProduct['Product - Google Age']:"";
											$csv_row[] = $tdProduct['Product - Google Size']!=''?$tdProduct['Product - Google Size']:"";										                                         $csv_row[] = $tdProduct['Product - Type - SearchBarLabel']!=''?$tdProduct['Product - Type - SearchBarLabel']:"";
                                            $csv_row[] = $tdProduct['iconLabel']!=''?$tdProduct['iconLabel']:""; //iconlabel 
                                            $csv_row[] = $tdProduct['Product - Next Date Due To Arrive']>date("Y-m-d")?$tdProduct['Product - Next Date Due To Arrive']:""; //date_of_arrival                             
                                            $csv_row[] = $tdProduct['Product - QTY On Current POs']; //qy_on_current_po 
                                            $csv_row[] = $tdProduct['requiredProductSKUForKit']; //requiredskuforkit   
                                            $csv_row[] = $tdProduct['numOfOptionsForKit']; //num_of_options_for_kit 
                                            $csv_row[] = $tdProduct['kitType']=='Build Your Own'?'1':'1'; //price_type 
                                            $csv_row[] = $tdProduct['kitType']=='Build Your Own'?'As Low as':''; //price_view 
                                            $csv_row[] = $tdProduct['kitType']== 'Fixed'?"1":"0"; //is_fixed_kit
                                            $csv_row[] = ""; //custom_layout_update
                                            $csv_row[] = ""; //custom_design   ultimo/default
                                            $csv_row[] = "1 column";  //page_layout
                                            $csv_row[] = $tdProduct['ProductDescription1']; //description
                                            /****
                                            * @desc code for copying the images
                                            */
                                            $image_path = $tdProduct['imgLocationCustom'];//str_replace("http://myclownantics.com/","/home/myclown/public_html/",$tdProduct['imgLocationCustom']);
                                            $image_arr = @getimagesize($image_path);  
                                            $related_image_path = "http://myclownantics.com/admin/CA_resize_500_500/".strtolower($tdProduct['Related Product']).".jpg";//"/home/myclown/public_html/admin/CA_resize_500_500/".strtolower($tdProduct['Related Product']).".jpg"; 
                                            $related_image_arr = @getimagesize($related_image_path); 
                                            if(is_array($image_arr) && $tdProduct['imgLocationCustom']!='') {
                                                $filename = basename($tdProduct['imgLocationCustom']);
                                                if(!file_exists("media/import/".$filename)) {
                                                    copy($image_path,"media/import/".$filename);
                                                }    
                                            } 
                                            else if(is_array($related_image_arr) && $tdProduct['imgLocationCustom']!='') {
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
                                            $csv_row[] = $tdProduct['Image Alt Text 1']; //image_label
                                            $csv_row[] = $site_images_path.$filename;  //small image 
                                            $csv_row[] = $tdProduct['Image Alt Text 1']; //small_image_label     
                                            $csv_row[] = $site_images_path.$filename;  //thumbnail  
                                            $csv_row[] = $tdProduct['meta_description']; //meta_description
                                            $csv_row[] = $tdProduct['meta_keywords']; //meta_keyword
                                            $csv_row[] = $tdProduct['meta_title'];  //meta_title
                                            $csv_row[] = "Use config"; //msrp_display_actual_price_type
                                            $csv_row[] = "No"; //msrp_enabled
                                            $csv_row[] = $tdProduct['Display Name'];  //name
                                            $csv_row[] = $tdProduct['isNewProduct?']=='Yes'?date("Y-m-d"):'';  //News from date
                                            $csv_row[] = "Product Info Column"; //options_container 
                                            $csv_row[] = $tdProduct['Product - flagORMD']==1?"Yes":"No"; //ormd
                                            $csv_row[] = $tdProduct['PriceCalced'];   //price 
                                            $csv_row[] = $tdProduct['DiscountPriceCalced']>0?$tdProduct['DiscountPriceCalced']:"";   //special price   
                                            $csv_row[] = $tdProduct['ProductDescription1'];   //short_description
                                            $csv_row[] = $tdProduct['is_visible']=="Yes"?1:2;  //status
                                            $csv_row[] = "2";  //tax_class_id
                                            $csv_row[] = ""; //url_path
                                            $csv_row[] = "4";//$product_type=='configurable'?"2":"4"; //visibility
                                            $csv_row[] = $tdProduct["Product - Weight"];  //weight
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
                                            $csv_row[] = $related_products[$i];//$this->arrTDProductFamilyContents[$related_product_family][$i]['related_web_profile']; //_links_related_sku
                                            $csv_row[] = ""; //_links_related_position
                                            $csv_row[] = ""; //_links_crosssell_sku
                                            $csv_row[] = ""; //_links_crosssell_position
                                            $csv_row[] = "";//$this->arrTDDesignFamilyContents[$related_design_family][$i]['related_web_profile']; //_links_upsell_sku
                                            $csv_row[] = "";  //_links_upsell_position
                                            $csv_row[] = $tdProduct['kitType']=='Fixed'?$this->arrProductConfigurableAttributes[$lowerpinnacleSKU][$i]['Product - FP Solo PinnacleSKU']:"";  //_associated_sku
                                            $csv_row[] = $tdProduct['kitType']=='Fixed'?1:""; //_associated_default_qty
                                            $csv_row[] = $tdProduct['kitType']=='Fixed'?$this->arrProductConfigurableAttributes[$lowerpinnacleSKU][$i]['priority']:""; //_associated_position
                                            $csv_row[] = ""; //_tier_price_website
                                            $csv_row[] = ""; //_tier_price_customer_group
                                            $csv_row[] = ""; //_tier_price_qty
                                            $csv_row[] = ""; //_tier_price_price
                                            $csv_row[] = "";  //_group_price_website
                                            $csv_row[] = "";  //_group_price_customer_group
                                            $csv_row[] = ""; //_group_price_price
                                            $csv_row[] = "88"; //_media_attribute_id
                                            $csv_row[] = $site_images_path.$filename; //_media_image
                                            $csv_row[] = $tdProduct['Image Alt Text 1']; //_media_lable
                                            $csv_row[] = "1"; //_media_position
                                            $csv_row[] = "0"; //_media_is_disabled
                                            $csv_row[] = $tdProduct['kitType']!='Fixed' && $tdProduct['kitType']!='Build Your Own'?$this->arrProductConfigurableAttributes[$lowerpinnacleSKU][$i]['Product - FP Solo PinnacleSKU']:""; //$tdProduct["PinnacleAttributeSKU"];  //_super_products_sku
                                            if(strtolower($this->arrProductConfigurableAttributes[$lowerpinnacleSKU][$i]['Web Option Label'])=='size') {
                                                $web_option = 'fp_size';
                                            }
                                            elseif(strtolower($this->arrProductConfigurableAttributes[$lowerpinnacleSKU][$i]['Web Option Label'])=='brand & size') {
                                                $web_option = 'brand_and_size';
                                            }
                                            elseif(strtolower($this->arrProductConfigurableAttributes[$lowerpinnacleSKU][$i]['Web Option Label'])=='skin color') {
                                                $web_option = 'skin_color';
                                            }
                                            elseif(strtolower($this->arrProductConfigurableAttributes[$lowerpinnacleSKU][$i]['Web Option Label'])=='color') {
                                                $web_option = 'fp_color';
                                            }
                                            elseif(strtolower($this->arrProductConfigurableAttributes[$lowerpinnacleSKU][$i]['Web Option Label'])=='design') {
                                                $web_option = 'fp_design';
                                            }
                                            elseif(strtolower($this->arrProductConfigurableAttributes[$lowerpinnacleSKU][$i]['Web Option Label'])=='style') {
                                                $web_option = 'fp_style';
                                            }  
                                            else {
                                                 $web_option = strtolower($this->arrProductConfigurableAttributes[$lowerpinnacleSKU][$i]['Web Option Label']);   
                                            }  
                                            $csv_row[] = $tdProduct['kitType']!='Fixed'&& $tdProduct['kitType']!='Build Your Own'?$web_option:"";   //_super_attribute_code
                                            $csv_row[] = $tdProduct['kitType']!='Fixed'&& $tdProduct['kitType']!='Build Your Own'?$this->arrProductConfigurableAttributes[$lowerpinnacleSKU][$i]['Attribute']:""; //$tdProduct["Attribute"]; //_super_attribute_option
                                            $price = 0;    
                                            $tmpProductPrice = 0;
                                            $attributePrice = 0; 
                                            if ($tdProduct['DiscountPriceCalced'] > 0) {
                                                    $tmpProductPrice = $tdProduct['DiscountPriceCalced'];
                                            }
                                            else {
                                                    $tmpProductPrice = $tdProduct['PriceCalced'];
                                            }
                                            if ($this->arrProductConfigurableAttributes[$lowerpinnacleSKU][$i]['DiscountPriceCalced'] > 0) {
                                                $attributePrice =  $this->arrProductConfigurableAttributes[$lowerpinnacleSKU][$i]['DiscountPriceCalced'];
                                            }
                                            else {
                                                $attributePrice =  $this->arrProductConfigurableAttributes[$lowerpinnacleSKU][$i]['PriceCalced'];
                                            }       
                                            if ($attributePrice != 0 && $attributePrice != '') {
                                                 if ($tmpProductPrice > $attributePrice) {
                                                     $price  = $tmpProductPrice - $attributePrice;    
                                                 } 
                                                 elseif ($tmpProductPrice < $attributePrice) {
                                                     $price  = $attributePrice - $tmpProductPrice; 
                                                 } 
                                            }  
                                            $csv_row[] = $tdProduct['kitType']!='Fixed'&& $tdProduct['kitType']!='Build Your Own'?$price:"";//_super_attribute_price_corr
                                            $csv_row[] = $tdProduct['kitType']=='Build Your Own'?$this->arrProductConfigurableAttributes[$lowerpinnacleSKU][$i]['Product - FP Solo PinnacleSKU']:"";//bundle_sku  
                                            $csv_row[] = $tdProduct['kitType']=='Build Your Own'?$web_option:"";//bundle_option_title   
                                            $csv_row[] = $tdProduct['kitType']=='Build Your Own'?$this->arrProductConfigurableAttributes[$lowerpinnacleSKU][$i]['Attribute']:"";//bundle_attribute 
                                            $csv_row[] = $tdProduct['kitType']=='Build Your Own'?$this->arrProductConfigurableAttributes[$lowerpinnacleSKU][$i]['BYOAttributeUpcharge']:"";//bundle_price    
                                        }
                                        else {
                                                $csv_row[] = ""; //sku
                                                $csv_row[] = "";  //_store
                                                $csv_row[] = ""; //_attribute_set 
                                                $csv_row[] = ""; //_type 
                                                $csv_row[] = $this->arrTDProductCategories[$lowerpinnacleSKU][$i]['category_path'];  //_category
                                                $csv_row[] = "";  //_root_category
                                                $csv_row[] = "";  //_product_websites
                                                $csv_row[] = "";  // priority
                                                $csv_row[] = ""; //url_key
                                                
												$csv_row[] = ""; //fp_filter_brand
												$csv_row[] =""; //fp_filter_size
												$csv_row[] = ""; //fp_filter_types
												$csv_row[] = ""; //fp_filter_character
												$csv_row[] = ""; //fp_filter_color_types
												$csv_row[] = $fp_colors[$i]; //fp_filter_color
												               
                                                $csv_row[] = ""; //brand_and_size
                                                $csv_row[] = ""; //brush  
                                                $csv_row[] = "";  //face_paint_kit_accessory
                                                $csv_row[] = "";  //kit_accessory  
                                                $csv_row[] = ""; //fp_color 
                                                $csv_row[] = ""; //fp_design 
                                                $csv_row[] = ""; //fp_size
												$csv_row[] = ""; //fp_style
												$csv_row[] = ""; //stencil_kit_accessory
												$csv_row[] = ""; //skin_color
												$csv_row[] = ""; //title
												$csv_row[] = ""; //type
												
												$csv_row[] = ""; //flagDropShip
												$csv_row[] = ""; //quantityPerPack
												$csv_row[] = ""; //Product - Type - Google Category
												$csv_row[] = ""; //Product - Google Color
												$csv_row[] = ""; //Product - Google Gender
												$csv_row[] = ""; //Product - Google Age
												$csv_row[] = ""; //Product - Google Size
                                                $csv_row[] = ""; //fp_searchbarlabel
                                            
                                                $csv_row[] = ""; //iconlabel
                                                $csv_row[] = ""; //date_of_arrival
                                                $csv_row[] = ""; //qy_on_current_po  
                                                $csv_row[] = ""; //requiredskuforkit 
                                                $csv_row[] = ""; //num_of_options_for_kit 
                                                $csv_row[] = ""; //price_type  
                                                $csv_row[] = ""; //price_view 
                                                $csv_row[] = ""; //is_fixed_kit 
                                                $csv_row[] = ""; //custom_layout_update
                                                $csv_row[] = ""; //custom design
                                                $csv_row[] = ""; //page layout
                                                $csv_row[] = ""; //description
                                                $csv_row[] = "";  //image
                                                $csv_row[] = "";  //image_label  
                                                $csv_row[] = "";  //small image  
                                                $csv_row[] = "";  //small_image_label     
                                                $csv_row[] = "";  //thumbnail  
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
                                                $csv_row[] = "";  //status
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
                                                $csv_row[] = $related_products[$i];//$this->arrTDProductFamilyContents[$related_product_family][$i]['related_web_profile']; //_links_related_sku  
                                                $csv_row[] = ""; //_links_related_position
                                                $csv_row[] = ""; //_links_crosssell_sku
                                                $csv_row[] = ""; //_links_crosssell_position
                                                $csv_row[] = "";//$this->arrTDDesignFamilyContents[$related_design_family][$i]['related_web_profile']; //_links_upsell_sku
                                                $csv_row[] = "";  //_links_upsell_position
                                                $csv_row[] = $tdProduct['kitType']=='Fixed'?$this->arrProductConfigurableAttributes[$lowerpinnacleSKU][$i]['Product - FP Solo PinnacleSKU']:"";  //_associated_sku
                                                $csv_row[] = $tdProduct['kitType']=='Fixed'?1:""; //_associated_default_qty
                                                $csv_row[] = $tdProduct['kitType']=='Fixed'?$this->arrProductConfigurableAttributes[$lowerpinnacleSKU][$i]['priority']:""; //_associated_position
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
                                                $csv_row[] = $tdProduct['kitType']!='Fixed'&& $tdProduct['kitType']!='Build Your Own'?$this->arrProductConfigurableAttributes[$lowerpinnacleSKU][$i]['Product - FP Solo PinnacleSKU']:""; //$tdProduct["PinnacleAttributeSKU"];  //_super_products_sku
                                                if(strtolower($this->arrProductConfigurableAttributes[$lowerpinnacleSKU][$i]['Web Option Label'])=='size') {
                                                    $web_option = 'fp_size';
                                                }
                                                elseif(strtolower($this->arrProductConfigurableAttributes[$lowerpinnacleSKU][$i]['Web Option Label'])=='brand & size') {
                                                    $web_option = 'brand_and_size';
                                                }
                                                elseif(strtolower($this->arrProductConfigurableAttributes[$lowerpinnacleSKU][$i]['Web Option Label'])=='skin color') {
                                                    $web_option = 'skin_color';
                                                }
                                                elseif(strtolower($this->arrProductConfigurableAttributes[$lowerpinnacleSKU][$i]['Web Option Label'])=='color') {
                                                    $web_option = 'fp_color';
                                                }
                                                elseif(strtolower($this->arrProductConfigurableAttributes[$lowerpinnacleSKU][$i]['Web Option Label'])=='design') {
                                                    $web_option = 'fp_design';
                                                }
                                                elseif(strtolower($this->arrProductConfigurableAttributes[$lowerpinnacleSKU][$i]['Web Option Label'])=='style') {
                                                    $web_option = 'fp_style';
                                                } 
                                                else {
                                                     $web_option = strtolower($this->arrProductConfigurableAttributes[$lowerpinnacleSKU][$i]['Web Option Label']);   
                                                }  
                                                $csv_row[] = $tdProduct['kitType']!='Fixed'&& $tdProduct['kitType']!='Build Your Own'?$web_option:"";   //_super_attribute_code         
                                                $csv_row[] = $tdProduct['kitType']!='Fixed'&& $tdProduct['kitType']!='Build Your Own'?$this->arrProductConfigurableAttributes[$lowerpinnacleSKU][$i]['Attribute']:""; //$tdProduct["Attribute"]; //_super_attribute_option
                                                $price = 0;    
                                                $tmpProductPrice = 0;
                                                $attributePrice = 0; 
                                                if ($tdProduct['DiscountPriceCalced'] > 0) {
                                                        $tmpProductPrice = $tdProduct['DiscountPriceCalced'];
                                                }
                                                else {
                                                        $tmpProductPrice = $tdProduct['PriceCalced'];
                                                }
                                                if ($this->arrProductConfigurableAttributes[$lowerpinnacleSKU][$i]['DiscountPriceCalced'] > 0) {
                                                    $attributePrice =  $this->arrProductConfigurableAttributes[$lowerpinnacleSKU][$i]['DiscountPriceCalced'];
                                                }
                                                else {
                                                    $attributePrice =  $this->arrProductConfigurableAttributes[$lowerpinnacleSKU][$i]['PriceCalced'];
                                                }       
                                                if ($attributePrice != 0 && $attributePrice != '') {
                                                     if ($tmpProductPrice > $attributePrice) {
                                                         $price  = $tmpProductPrice - $attributePrice;    
                                                     } 
                                                     elseif ($tmpProductPrice < $attributePrice) {
                                                         $price  = $attributePrice - $tmpProductPrice; 
                                                     } 
                                                }  
                                                $csv_row[] = $tdProduct['kitType']!='Fixed'&& $tdProduct['kitType']!='Build Your Own'?$price:"";
                                                $csv_row[] = $tdProduct['kitType']=='Build Your Own'?$this->arrProductConfigurableAttributes[$lowerpinnacleSKU][$i]['Product - FP Solo PinnacleSKU']:"";//bundle_sku  
                                                $csv_row[] = $tdProduct['kitType']=='Build Your Own'?$web_option:"";//bundle_option_title   
                                                $csv_row[] = $tdProduct['kitType']=='Build Your Own'?$this->arrProductConfigurableAttributes[$lowerpinnacleSKU][$i]['Attribute']:"";//bundle_attribute 
                                                $csv_row[] = $tdProduct['kitType']=='Build Your Own'?$this->arrProductConfigurableAttributes[$lowerpinnacleSKU][$i]['BYOAttributeUpcharge']:"";//bundle_price    
                                        }        
                                        fputcsv($this->fp,$csv_row);
                                    } // End of for largest counter loop
                            } // End of if not in product added array  
                            if($this->product_counter % 800 == 0 && $type_of_product!='bundle') {
                                 $this->csv_counter++;  
                                 fclose($this->fp);
                                 $this->fp = fopen("var/import/products/FP_Products".$this->csv_counter.".csv","w+");
                                 $this->add_header_into_csv();   
                            } 
                            if($this->bundle_product_counter % 800 == 0 && $type_of_product=='bundle') {
                                 $this->bundle_csv_counter++;  
                                 fclose($this->fp);
                                 $this->fp = fopen("var/import/products/FP_BundleProducts".$this->bundle_csv_counter.".csv","w+");  
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
            if($type=='Build Your Own') {
                $type = " AND ([kitType]='".$type."')"; 
            }   
            else if($type == 'Parent') {
                $type = " AND Nz([kitType],'')<>'Build Your Own' AND ([Type Attribute]='".$type."')";  
            }
            else {    
                $type = " AND Nz([kitType],'')<>'Build Your Own' AND [Type Attribute]='".$type."'";
            }    
        }
        $arrQueries = "WHERE [is_visible] AND [SendToPinnacle] $type ";
        /**
        * @desc  create string of columns to be retreived from the query  
        */       
       $strColumns = "[is_visible],[is_home],[Quantity Available],[Product - Weight],[free_shipping],[PriceCalced],[Priority],[PinnacleSKU],[Display Name],[meta_keywords],[overview],[ProductDescription1],[isBuildYourOwnKit],[numOfOptionsForKit],[requiredProductSKUForKit],[Image Alt Text 1],[Product - Next Date Due To Arrive],[isNewProduct?],[Product - flagORMD],[Product - # of Order Line Items 365 Days],[flagDropShip],[Product - isBulkyProduct?],[vendorItemNumberCalced],[kitType],[Record ID#],[Product - UPC Code],[Product - flagOutOfStock],[Product - VENDOR - Company Display Name],[Product - QTY On Current POs],[DiscountPriceCalced],[Related Product],[Related Product Family],[url],[quantityPerPack],[meta_title],[meta_description],[addShippingDays],[Product - Type - Gender],[Product - Type - Age],[Product - Type - Default Size],[Product - Type - Google Category],[Product - Google Color],[Product - Type - SearchBarLabel],[Brand],[DisplayLabelEnteredFPCharacterCalced],[Product - FP_Colors],[FilterSizeDisplayLabelEnteredFPCalced],[TypeDisplayLabelEnteredFPCalced],[iconlabel],[imgLocationCustom],[numOfOptionsForKit],[requiredProductSKUForKit]";	      

        try
        {           
            $arrResults = $this->api->Query("SELECT TOP 700 ".$strColumns." FROM [FP Web Profile] ".$arrQueries." ORDER BY [PinnacleSKU]");     
            if (isset($arrResults->Rows)) { 
                 foreach ($arrResults->Rows as $productDetails) {
                    $last_record = $productDetails['PinnacleSKU'];
                    $arrProducts[] = $productDetails;
                 }  
                 $resultcount = 0;
                 while($resultcount == 0) {
                    $arrQueries = "WHERE [is_visible] AND [SendToPinnacle] AND [PinnacleSKU] > '".$last_record."' $type";
                    /**
                    * @desc  create string of columns to be retreived from the query - Related Category, is_primary, Related Web Profile, Priority  
                    */                      
                    try {  
                        $arrResults = $this->api->Query("SELECT TOP 700 ".$strColumns." FROM [FP Web Profile] ".$arrQueries." ORDER BY [PinnacleSKU]");
                    }
                    catch (Exception $e) {
                         echo $this->tdErrorLog = "<br/>Error Fetching Profiles from TD for type $type. Caught exception: " .$e->getMessage(). "<br/>";
                         $strReturn .= $this->tdErrorLog;
                         die;    
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
             echo $this->tdErrorLog = "<br/>Error Fetching Profiles from TD for type $type. Caught exception: " .$e->getMessage(). "<br/>";
             die;
             $strReturn .= $this->tdErrorLog; 
             return  $strReturn;           
       }
    }
    /**
    * @desc Function to get all the products attributes from teamdesk 
    * @return array $arrResults array of all the products  attributes
    * @since 29-    Jan-2013
    * @author Dinesh Nagdev
    */ 
    private function getTDProductsAttributes()    
    {
        $arrProductsAttributes = array();
        $arrQueries = "WHERE [sendToPinnacle] AND [is_active]='Yes'";
        /**
        * @desc  create string of columns to be retreived from the query  
        */
        $strColumns = "[Web Option Label],[Attribute],[priority],[PriceCalced],[DiscountPriceCalced],[Product - Quantity Available],[PinnacleAttributeSku],[Related Web Profile],[Product - FP Solo PinnacleSKU],[BYOAttributeUpcharge]";  
       try
       {
           
            $arrResults = $this->api->Query("SELECT TOP 3000 ".$strColumns." FROM [FP Attribute] ".$arrQueries ." ORDER BY [PinnacleAttributeSKU] ASC"); 
            if (isset($arrResults->Rows)) {    
                foreach ($arrResults->Rows as $tdProductAtt) { 
                     $last_record = $tdProductAtt['PinnacleAttributeSku'];
                     $productSKU = (string) $tdProductAtt['Product - FP Solo PinnacleSKU'];
                     $productSKU = strtolower($productSKU);
                     $arrProductAttributes[$productSKU][] = $tdProductAtt; 
                     
                     $configproductSKU = (string) $tdProductAtt['Related Web Profile'];
                     $configproductSKU = strtolower($configproductSKU);
                     $this->arrProductConfigurableAttributes[$configproductSKU][] = $tdProductAtt;  
                }
                $resultcount = 0;
                while($resultcount == 0) {
                    $arrQueries = "WHERE [sendToPinnacle] AND [is_active]='Yes' AND [PinnacleAttributeSku] > '".$last_record."'";
                    /**
                    * @desc  create string of columns to be retreived from the query - Related Category, is_primary, Related Web Profile, Priority  
                    */                      
                    try {  
                        $arrResults = $this->api->Query("SELECT TOP 3000 ".$strColumns." FROM [FP Attribute] ".$arrQueries." ORDER BY [PinnacleAttributeSku] ASC ");
                    }
                    catch (Exception $e) {
                         echo $this->tdErrorLog = '<br/>Error Fetching Attributes from TD. Caught exception: ' .$e->getMessage(). "<br/>";
                         $strReturn .= $this->tdErrorLog;  
                         die;  
                    }
                    /**
                    * @desc code to check the records and assign the same into the array
                    */
                    if (isset($arrResults->Rows)) {                                
                        /**
                        * @desc  loop through each category to get the pinnacle id from the categories table of teamdesk    
                        */
                        foreach ($arrResults->Rows as $tdProductAtt) {    
                              $last_record = $tdProductAtt['PinnacleAttributeSku'];
                              $productSKU = (string) $tdProductAtt['Product - FP Solo PinnacleSKU'];
                              $productSKU = strtolower($productSKU);
                              $arrProductAttributes[$productSKU][] = $tdProductAtt;  
                              
                              $configproductSKU = (string) $tdProductAtt['Related Web Profile'];
                              $configproductSKU = strtolower($configproductSKU);
                              $this->arrProductConfigurableAttributes[$configproductSKU][] = $tdProductAtt;
                        }
                    }
                    else {
                        $arrResults->Rows = array();
                    }    
                    if(count($arrResults->Rows) > 0)
                         $resultcount = 0;
                    else
                        $resultcount = 1;      
                }
                //$arrProductsAttributes = $arrResults->Rows;            
            } 
            return  $arrProductAttributes;
       }
       catch (Exception $e) {
             echo $this->tdErrorLog = '<br/>Error Fetching Attributes from TD. Caught exception: ' .$e->getMessage(). "<br/>";
             die;
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
                  $product_header_row[] = "priority";  
                  $product_header_row[] = "url_key"; 
                  
                  $product_header_row[] = "fp_filter_brands";
				  $product_header_row[] = "fp_filter_size";                  
                  $product_header_row[] = "fp_filter_types"; 
                  $product_header_row[] = "fp_filter_character";
                  $product_header_row[] = "fp_filter_color_types";
                  $product_header_row[] = "fp_filter_colors";                  
                  
                  $product_header_row[] = "brand_and_size";
                  $product_header_row[] = "brush"; 
                  $product_header_row[] = "face_paint_kit_accessory";
				  $product_header_row[] = "kit_accessory";
                  $product_header_row[] = "fp_color";
                  $product_header_row[] = "fp_design";
                  $product_header_row[] = "fp_size";
				  $product_header_row[] = "fp_style";
				  $product_header_row[] = "stencil_kit_accessory";
				  $product_header_row[] = "skin_color";
				  $product_header_row[] = "title";
				  $product_header_row[] = "type";
				  
				  $product_header_row[] = "dropship";
                  $product_header_row[] = "quantityperpack";
				  $product_header_row[] = "rw_google_base_product_categ";
				  $product_header_row[] = "google_color";
				  $product_header_row[] = "google_gender";
				  $product_header_row[] = "google_age";
				  $product_header_row[] = "google_size";
                  $product_header_row[] = "fp_searchbarlabel"; 			  
				  $product_header_row[] = "iconlabel";   
                  $product_header_row[] = "date_of_arrival"; 
                  $product_header_row[] = "qty_on_current_po";
                  $product_header_row[] = "requiredskuforkit";
                  $product_header_row[] = "num_of_options_for_kit";
                  $product_header_row[] = "price_type";  
                  $product_header_row[] = "price_view"; 
                  $product_header_row[] = "is_fixed_kit"; 
                  
                  $product_header_row[] = "custom_layout_update";
                  $product_header_row[] = "custom_design"; 
                  $product_header_row[] = "page_layout";   
                  $product_header_row[] = "description"; 
                  $product_header_row[] = "image";
                  $product_header_row[] = "image_label";     
                  $product_header_row[] = "small_image"; 
                  $product_header_row[] = "small_image_label"; 
                  $product_header_row[] = "thumbnail"; 
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
                  $product_header_row[] = "status";
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
                  $product_header_row[] = "bundle_sku";
                  $product_header_row[] = "bundle_option_title"; 
                  $product_header_row[] = "bundle_attribute";
                  $product_header_row[] = "bundle_price";  
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
        $arrQueries = " WHERE [Category - is_visible]"; 
        /**
        * @desc  create string of columns to be retreived from the query - Related Category, is_primary, Related Web Profile, Priority  
        */                      
        $strColumns = "[Category - Label],[Related Web Profile],[Record ID#]";  //,[Priority],[meta_title],[meta_description] 
        try {             
            $arrResults = $this->api->Query("SELECT TOP 3000 ".$strColumns." FROM [FP Web Entry] ".$arrQueries." ORDER BY [Record ID#]");
            $total_result_rows = $total_result_rows + count($arrResults->Rows);
        } 
        catch (Exception $e) {
             echo $this->tdErrorLog = '<br/>Error Fetching Web entries from TD. Caught exception: ' .$e->getMessage(). "<br/>";
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
            $arrQueries = "WHERE [Record ID#] > '".$last_record."' AND  [Category - is_visible]";
            /**
            * @desc  create string of columns to be retreived from the query - Related Category, is_primary, Related Web Profile, Priority  
            */                      
            $strColumns = "[Category - Label],[Related Web Profile],[Record ID#]"; //,[Priority],[meta_title],[meta_description] 
            try {  
                $arrResults = $this->api->Query("SELECT TOP 3000 ".$strColumns." FROM [FP Web Entry] ".$arrQueries." ORDER BY [Record ID#]");
                $total_result_rows = $total_result_rows + count($arrResults->Rows);  
            }
            catch (Exception $e) {
                 echo $this->tdErrorLog = '<br/>Error Fetching Web entries from TD.Caught exception: ' .$e->getMessage(). "<br/>";
                 $strReturn .= $this->tdErrorLog;    
                 return  $strReturn;  
                 die;       
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
        echo "<br />Total Web Entries Rows =".$total_result_rows."<br />";
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
             /**
            * @desc  create the teamdesk query, to fetch all the categories
            */  
            // Get all products inventory for which send to pinnacle flag is true
            $arrQueries = "WHERE [sendToPinnacle]"; //AND [Product - Weight]>0 AND [is_visible]  
            /**
            * @desc  create string of columns to be retreived from the query  
            */       
            $strColumns = "[PinnacleSKU],[Quantity Available],[is_visible],[Product - Next Date Due To Arrive],[Product - QTY On Current POs],[Priority]"; 
            $arrTDProducts = $this->api->Query("SELECT TOP 1500 ".$strColumns." FROM [FP Web Profile] ".$arrQueries." ORDER BY [PinnacleSKU]");  
            if (isset($arrTDProducts->Rows)) {
                foreach ($arrTDProducts->Rows as $productDetails) {
                          $arrProducts[] = $productDetails;
                          $last_record = $productDetails['PinnacleSKU'];
                }        
                $resultcount = 0;
                while($resultcount == 0) {
                      $arrQueries = "WHERE [sendToPinnacle] AND [PinnacleSKU] > '".$last_record."'";    //AND [Product - Weight]>0 AND [is_visible] 
                      try {  
                            $arrTDProducts = $this->api->Query("SELECT TOP 1500 ".$strColumns." FROM [FP Web Profile] ".$arrQueries." ORDER BY [PinnacleSKU]");
                            if (isset($arrTDProducts->Rows)) {
                                foreach ($arrTDProducts->Rows as $productDetails) {
                                          $arrProducts[] = $productDetails;
                                          $last_record = $productDetails['PinnacleSKU']; 
                                }
                            }
                        }
                        catch (Exception $e) {
                             echo $this->tdErrorLog = '<br/>Error Fetching Profiles from TD.Caught exception: ' .$e->getMessage(). "<br/>";
                             $strReturn .= $this->tdErrorLog;
                             die;   
                             $resultcount = 1;   
                       }
                       if(count($arrTDProducts->Rows) > 0)
                            $resultcount = 0;
                       else
                            $resultcount = 1; 
                                
                }  
            }   
            if (count($arrProducts) > 0) {                    
                /**
                * @desc loop through each record to add new product and update existing products
                */
                $db->query("SELECT attribute_id from eav_attribute where attribute_code='qty_on_current_po'");   
                if($db->moveNext()) {
                    $qty_on_current_po_id = $db->col['attribute_id'];
                }
                $db->query("SELECT attribute_id from eav_attribute where attribute_code='priority'");   
                if($db->moveNext()) {
                    $priority_id = $db->col['attribute_id'];
                }
                $db->query("SELECT attribute_id from eav_attribute where attribute_code='date_of_arrival'");   
                if($db->moveNext()) {
                    $date_of_arrival_id = $db->col['attribute_id'];
                } 
                $db->query("SELECT attribute_id from eav_attribute where attribute_code='status'");   
                if($db->moveNext()) {
                    $status_id = $db->col['attribute_id'];
                }         
                foreach ($arrProducts as $TDProduct) {  
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
                            
                            $db->reset();  
                            $db->assignStr("value", $TDProduct['is_visible']=="Yes"?1:2);  
                            $db->update("catalog_product_entity_int", "WHERE entity_id='".addslashes($product_id)."' and attribute_id='".addslashes($status_id)."'");
                            
                            $db->reset();  
                            $db->assignStr("value", $TDProduct['Product - QTY On Current POs']!=''?$TDProduct['Product - QTY On Current POs']:0);  
                            $db->update("catalog_product_entity_varchar", "WHERE entity_id='".addslashes($product_id)."' and attribute_id='".addslashes($qty_on_current_po_id)."'");
                            $db->reset();  
                            $db->assignStr("value", $TDProduct['Priority']!=''?$TDProduct['Priority']:0);  
                            $db->update("catalog_product_entity_varchar", "WHERE entity_id='".addslashes($product_id)."' and attribute_id='".addslashes($priority_id)."'"); 
                            
                            $db->reset();  
                            $db->assignStr("value", $TDProduct['Product - Next Date Due To Arrive']!=''?$TDProduct['Product - Next Date Due To Arrive']:'0000-00-00');  
                            $db->update("catalog_product_entity_datetime", "WHERE entity_id='".addslashes($product_id)."' and attribute_id='".addslashes($date_of_arrival_id)."'"); 
                            $totalProductsUpdate++;
                        }        
                        $product_added[] = $productSKU;
                    }    
                }
            }
        }     
       $arrReturn["totalProductsUpdated"]  = $totalProductsUpdate;
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
                        $this->tdErrorLog = '<br/>Error Inserting data into TD.Caught exception: ' .$e->getMessage(). "<br/>";
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
    
    /**
    * @desc Function to get all the product accessories from the product_accessories table of Quickbase
    * @return array $arrProductFamily array of all the product families
    * @since 01-Feb-2013
    * @author Dinesh Nagdev
    */
    private function getTDProductFamilyContents()
    {
        $arrTDProductFamilyContents = array();
        /** 
        * @desc  create the teamdesk query, to fetch all the product families
        */
        $arrQueries = "WHERE ([Record ID#] > '0') AND [Related Product Family] > '0'"; 
        /** 
        * @desc  create string of columns to be retreived from the query  - Related Web Profile, 
        * Related Prod Family, Product Family Name
        */
        $strColumns = "[Related Web Profile],[Related Product Family],[Product Family - Name]";  
        try {
            $arrResults = $this->api->Query("SELECT ".$strColumns." FROM [FP Product Accessory] ".$arrQueries);  
            if (isset($arrResults->Rows)) {  
                foreach ($arrResults->Rows as $tdProductFamilyContent) {  
                     $TDFamilyID = (int) $tdProductFamilyContent['Related Product Family'];
                     $arrTDProductFamilyContents[$TDFamilyID][] = array (
                                                                'related_web_profile' => (string) $tdProductFamilyContent['Related Web Profile'],
                                                                );
                }
            }        
            return $arrTDProductFamilyContents;   
        }
        catch (Exception $e) {
            $this->tdErrorLog = '<br/>Error in fetching product family contents from FP Product Accessory : Caught exception: ' .$e->getMessage(). "<br/>";
            echo $this->tdErrorLog;
            die;         
        }         
    }
}

?>                                           

