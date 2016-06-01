<?php 

                           

/**  

* @desc This class has functions to import the product, product attributes details from TeamDesk to Pinnacle. 

* @package  TeamDesk  

* @author Dinesh Nagdev

* @since 28-Jan-2013

*/ 

class FLTeamDeskWebprofiles {

    private $db;
    private $api;
    private $arrErrorLog;
    private $arrTDFields;   
    private $arrTDProdAttrFields;                           
    private $arrTDProductCategories;
    private $arrFilterSeason; 
    private $arrTDDesignFamilyContents;
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
    private $arrThemeSubtheme;
    private $arrOccassionSubtheme;
    private $arrHolidaySubtheme;
    private $arrSeasonSubtheme;
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
            /**
            * @desc code to unset the session variables.
            */
            /*unset($_SESSION['arrFilterSeason']); 
            unset($_SESSION['arrTDDesignFamilyContents']); 
            unset($_SESSION['arrTDProductFamilyContents']); 
            unset($_SESSION['arrSoloProducts']); 
            unset($_SESSION['arrParentTDProducts']); 
            unset($_SESSION['arrTDProductCategories']); 
            unset($_SESSION['arrTDProductsAttributes']);  
            unset($_SESSION['arrProductConfigurableAttributes']);  
            unset($_SESSION['arrThemeSubtheme']);
            unset($_SESSION['arrSeasonSubtheme']);
            unset($_SESSION['arrHolidaySubtheme']);
            unset($_SESSION['arrOccassionSubtheme']);*/
            /**
            * @desc code to get the filter theme sub themes.  
            */
            if($_SESSION['arrThemeSubtheme'] !='' && isset($_SESSION['arrThemeSubtheme'])) {
                $this->arrThemeSubtheme = $_SESSION['arrThemeSubtheme'];
            }
            else {
                $this->arrThemeSubtheme = $this->getTDThemeSubTheme(); 
                $_SESSION['arrThemeSubtheme'] = $this->arrThemeSubtheme; 
            }  
            /**
            * @desc code to get the filter season sub themes.  
            */
            if($_SESSION['arrSeasonSubtheme'] !='' && isset($_SESSION['arrSeasonSubtheme'])) {
                $this->arrSeasonSubtheme = $_SESSION['arrSeasonSubtheme'];
            }
            else {
                $this->arrSeasonSubtheme = $this->getTDSeasonSubTheme(); 
                $_SESSION['arrSeasonSubtheme'] = $this->arrSeasonSubtheme; 
            }
            /**
            * @desc code to get the filter holiday sub themes.  
            */
            if($_SESSION['arrHolidaySubtheme'] !='' && isset($_SESSION['arrHolidaySubtheme'])) {
                $this->arrHolidaySubtheme = $_SESSION['arrHolidaySubtheme'];
            }
            else {
                $this->arrHolidaySubtheme = $this->getTDHolidaySubTheme(); 
                $_SESSION['arrHolidaySubtheme'] = $this->arrHolidaySubtheme; 
            }
            /**
            * @desc code to get the filter occassion sub themes.  
            */
            if($_SESSION['arrOccassionSubtheme'] !='' && isset($_SESSION['arrOccassionSubtheme'])) {
                $this->arrOccassionSubtheme = $_SESSION['arrOccassionSubtheme'];
            }
            else {
                $this->arrOccassionSubtheme = $this->getTDOccassionSubTheme(); 
                $_SESSION['arrOccassionSubtheme'] = $this->arrOccassionSubtheme; 
            }
            /**
            * @desc code to get the filter seasons.  
            */
            if($_SESSION['arrFilterSeason'] !='' && isset($_SESSION['arrFilterSeason'])) {
                $this->arrFilterSeason = $_SESSION['arrFilterSeason'];
            }
            else {
                $this->arrFilterSeason = $this->getTDFilterSeason(); 
                $_SESSION['arrFilterSeason'] = $this->arrFilterSeason; 
            }
            /**
            * @desc code to get the design families contents.  
            */
            if($_SESSION['arrTDDesignFamilyContents'] !='' && isset($_SESSION['arrTDDesignFamilyContents'])) {
                $this->arrTDDesignFamilyContents = $_SESSION['arrTDDesignFamilyContents'];
            }
            else {
                $this->arrTDDesignFamilyContents = $this->getTDDesignFamilyContents();   
                $_SESSION['arrTDDesignFamilyContents'] = $this->arrTDDesignFamilyContents; 
            }
            /**
            * @desc code to get the product families contents.  
            */
            if($_SESSION['arrTDProductFamilyContents'] !='' && isset($_SESSION['arrTDProductFamilyContents'])) {
                $this->arrTDProductFamilyContents = $_SESSION['arrTDProductFamilyContents'];
            }
            else {
                $this->arrTDProductFamilyContents = $this->getTDProductFamilyContents();   
                $_SESSION['arrTDProductFamilyContents'] = $this->arrTDProductFamilyContents; 
            } 
            /**
            * @desc code to get the solo products.  
            */
            if($_SESSION['arrSoloProducts'] !='' && isset($_SESSION['arrSoloProducts'])) {   
                $arrSoloTDProducts = $_SESSION['arrSoloProducts'];
            }
            else {
                $arrSoloTDProducts = $this->getTDProducts('Solo');
                $_SESSION['arrSoloProducts'] = $arrSoloTDProducts; 
            }   
            /**
            * @desc get all the product categories
            */
            if($_SESSION['arrTDProductCategories'] !='' && isset($_SESSION['arrTDProductCategories'])) {
                $this->arrTDProductCategories = $_SESSION['arrTDProductCategories'];
            }
            else { 
                $this->arrTDProductCategories = $this->getALLTDProductCategory();  
                $_SESSION['arrTDProductCategories'] = $this->arrTDProductCategories; 
            }    
            /**
            * @desc get all attributes 
            */
            if($_SESSION['arrTDProductsAttributes'] !='' && isset($_SESSION['arrTDProductsAttributes'])) {
                $arrTDProductsAttributes = $_SESSION['arrTDProductsAttributes'];
            }
            else {
                $arrTDProductsAttributes = $this->getTDProductsAttributes();   
                $_SESSION['arrTDProductsAttributes'] = $arrTDProductsAttributes; 
            }
            if($_SESSION['arrProductConfigurableAttributes'] !='' && isset($_SESSION['arrProductConfigurableAttributes'])) {
                $this->arrProductConfigurableAttributes = $_SESSION['arrProductConfigurableAttributes'];
            }
            else {
                $_SESSION['arrProductConfigurableAttributes'] = $this->arrProductConfigurableAttributes; 
            }
            if($_SESSION['arrParentTDProducts'] !='' && isset($_SESSION['arrParentTDProducts'])) {
                $arrParentTDProducts = $_SESSION['arrParentTDProducts'];
            }
            else {
                $arrParentTDProducts = $this->getTDProducts('Parent');
                $_SESSION['arrParentTDProducts'] = $arrParentTDProducts; 
            }  
            if($_SESSION['FLarrBundleTDProducts'] !='' && isset($_SESSION['FLarrBundleTDProducts'])) {
                $arrBundleTDProducts = $_SESSION['FLarrBundleTDProducts'];
            }
            else {
                $arrBundleTDProducts = $this->getTDProducts('Fixed');
                $_SESSION['FLarrBundleTDProducts'] = $arrBundleTDProducts; 
            } 
            echo "<br />count of design family contents = ".count($_SESSION['arrTDDesignFamilyContents']);
            echo "<br />count of prodct family content = ".count($_SESSION['arrTDProductFamilyContents']);
            echo "<br />count of season = ".count($_SESSION['arrFilterSeason']);
            echo "<br />count of solo = ".count($_SESSION['arrSoloProducts']);
            echo "<br />count of categories =".count($_SESSION['arrTDProductCategories']);
            echo "<br />count of attributes = ".count($_SESSION['arrTDProductsAttributes']);
            echo "<br />count of config attributes = ".count($_SESSION['arrProductConfigurableAttributes']);  
            echo "<br />count of parent = ".count($_SESSION['arrParentTDProducts']);   
            echo "<br />count of theme subthemes = ".count($_SESSION['arrThemeSubtheme']);
            echo "<br />count of season subthemes = ".count($_SESSION['arrSeasonSubtheme']);
            echo "<br />count of occassion subthemes = ".count($_SESSION['arrOccassionSubtheme']);
            echo "<br />count of holiday subthemes = ".count($_SESSION['arrHolidaySubtheme']);
            echo "<br />count of bundled products = ".count($_SESSION['FLarrBundleTDProducts']);  
            /*echo '<pre>';
            print_R($_SESSION['arrThemeSubtheme']);
            print_R($_SESSION['arrSeasonSubtheme']);
            print_R($_SESSION['arrOccassionSubtheme']);
            print_R($_SESSION['arrHolidaySubtheme']); */
            /**                                                                                 
            * @desc code to create the simple products csv file for uploading in magento
            */
            //$cp = copy("var/import/products/FL_products.csv","var/import/products/FL_old_products.csv"); 
            
            $attribute_set='';   
            $product_type='';  
            $site_images_path = "/";  
            $type_of_product='';
            /**
            * @desc code to create the solo products csv file for uploading in magento
            */
            if (isset($arrSoloTDProducts) && is_array($arrSoloTDProducts) && count($arrSoloTDProducts) > 0) {
                $pr_type = 'Solo';
                $this->fp = fopen("var/import/products/FL_Products".$this->csv_counter.".csv","w+"); 
                $this->add_header_into_csv();   
                $this->add_data_into_csv($arrSoloTDProducts,$arrTDProductsAttributes,$pr_type);  
            }  // End of isset prduct loop
            /**
            * @desc get all the parent products - products which are not be added as options        
            */
            $insert_attribute=0;
            if (isset($arrTDProductsAttributes) && is_array($arrTDProductsAttributes) && count($arrTDProductsAttributes) > 0 && $insert_attribute==1) {
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
                              if(!in_array($tdProductAtt['PinnacleAttributeSKU'],$this->product_added_sku) && ($tdProductAtt["Product - Weight"]>0)) {
                                  $this->product_added_sku[] = $tdProductAtt['PinnacleAttributeSKU'];    
                                  for($i=0;$i<$categories_counter;$i++) { 
                                      $csv_row = array();
                                      if($i == 0) { 
                                            $filename='';
                                            $csv_row[] = $tdProductAtt['PinnacleAttributeSKU']; //sku
                                            $csv_row[] = "";  //_store
                                            $csv_row[] = $tdProductAtt['Web Option Label']=='Size'?'FL Size':$tdProductAtt['Web Option Label']; //_attribute_set 
                                            $csv_row[] = "simple"; //_type 
                                            $csv_row[] = $this->arrTDProductCategories[$lowerpinnacleSKU][$i]['category_path'];  //_category
                                            $csv_row[] = "Flagsrus";  //_root_category
                                            $csv_row[] = "flagsrus";  //_product_websites
                                            $csv_row[] = $tdProductAtt['priority'];  //priority
                                            $csv_row[] = "";  //url_key   
                                            
                                            $csv_row[] = ""; //fl_new_products 
                                            $csv_row[] = ""; //fl_filter_section
                                            $csv_row[] = ""; //fl_filter_type    
                                            $csv_row[] = ""; //feature                               
                                            $csv_row[] = ""; //fl_filter_size    
                                            $csv_row[] = ""; //multi seasons 
                                            $csv_row[] = ""; //season sub themes  
                                            $csv_row[] = ""; //holiday
                                            $csv_row[] = ""; //holiday sub themes  
                                            $csv_row[] = ""; //occassion
                                            $csv_row[] = ""; //occassion sub themes  
                                            $csv_row[] = ""; //multi themes 
                                            $csv_row[] = ""; //Theme sub themes  
                                            $csv_row[] = ""; //multibrand  
                                            
                                            $csv_row[] = $tdProductAtt['Web Option Label']=="Accessory"?$tdProductAtt["Attribute"]:""; //Accessory 
                                            $csv_row[] = $tdProductAtt['Web Option Label']=="Design"?$tdProductAtt["Attribute"]:""; //Design  
                                            $csv_row[] = $tdProductAtt['Web Option Label']=="Size"?$tdProductAtt["Attribute"]:"";  //FL size
                                            $csv_row[] = $tdProductAtt['Web Option Label']=="Option"?$tdProductAtt["Attribute"]:""; //Option 
                                            $csv_row[] = $tdProductAtt['Web Option Label']=="Finish"?$tdProductAtt["Attribute"]:""; //Finish 
                                            $csv_row[] = $tdProductAtt['Web Option Label']=="Letter"?$tdProductAtt["Attribute"]:""; //Letter  
                                            
                                            $csv_row[] = ""; //custom_layout_update
                                            $csv_row[] = ""; //custom design  //ultimo/default
                                            $csv_row[] = "1 column"; //page layout two_columns_left
                                            $csv_row[] = "Disabled (do not show)"; //estimated_delivery_enable : Inherited  
                                            $csv_row[] = "Disabled (do not show)"; //estimated_shipping_enable :Inherited 
                                            $csv_row[] = $tdProductAtt['Product - Description']!=''?$tdProductAtt['Product - Description']:$tdProductAtt['Web Profile - Description']; //description
                                            $image_path = $tdProductAtt['imgLocationCustom'];//str_replace("http://myclownantics.com/","/home/myclown/public_html/",$tdProductAtt['imgLocationCustom']);
                                            $image_arr = @getimagesize($image_path);   
                                            $related_image_path = "http://myclownantics.com/admin/CA_resize_500_500/".strtolower($tdProductAtt['Related Product']).".jpg"; 
                                            $related_image_arr = @getimagesize($related_image_path); 
                                            if(is_array($image_arr) && $tdProductAtt['imgLocationCustom']!='') {
                                                $filename = basename($tdProductAtt['imgLocationCustom']);
                                                if(!file_exists("media/import/".$filename)) {
                                                    copy($image_path,"media/import/".$filename);
                                                }    
                                            }
                                            else if(is_array($related_image_arr) && $tdProductAtt['imgLocationCustom']!='') {
                                                $filename = basename($tdProductAtt['imgLocationCustom']);
                                                if(!file_exists("media/import/".$filename)) {
                                                    copy($related_image_path,"media/import/".$filename);
                                                }    
                                            } 
                                            else {
                                                $image_not_found[$tdProductAtt['PinnacleAttributeSKU']] =  $image_path;
                                            }   
                                            $csv_row[] = $site_images_path.$filename;  //image
                                            $csv_row[] = $site_images_path.$filename;  //small image      
                                            $csv_row[] = $site_images_path.$filename;  //thumbnail      
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
                                            $csv_row[] = $tdProductAtt['Product - Description']!=''?$tdProductAtt['Product - Description']:$tdProductAtt['Web Profile - Description'];   //short_description
                                            $csv_row[] = $tdProductAtt['is_active']=="Yes"?1:2;  //status
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
                                            $csv_row[] = ""; //$tdProductAtt["PinnacleAttributeSKU"];  //_super_products_sku
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
                                            $csv_row[] = "";  //priority
                                            $csv_row[] = ""; //url_key
                                            
                                            $csv_row[] = ""; //fl_filter_section
                                            $csv_row[] = ""; //fl_filter_type 
                                            $csv_row[] = ""; //feature                                  
                                            $csv_row[] = ""; //fl_filter_size  
                                            $csv_row[] = ""; //multi seasons 
                                            $csv_row[] = ""; //holiday 
                                            $csv_row[] = ""; //occassion 
                                            $csv_row[] = ""; //multi themes 
                                            $csv_row[] = ""; //multi sub themes  
                                            $csv_row[] = ""; //multi brand  
                                            $csv_row[] = ""; //new_only_and_show_all
                                            
                                            $csv_row[] = ""; //accessory 
                                            $csv_row[] = ""; //design 
                                            $csv_row[] = "";  //fl_size
                                            $csv_row[] = ""; //option 
                                            $csv_row[] = ""; //finish 
                                            $csv_row[] = ""; //letter 
                                            
                                            $csv_row[] = ""; //custom_layout_update
                                            $csv_row[] = ""; //custom design
                                            $csv_row[] = ""; //page layout
                                            $csv_row[] = ""; //estimated_delivery_enable   
                                            $csv_row[] = ""; //estimated_shipping_enable   
                                            $csv_row[] = ""; //description
                                            $csv_row[] = "";  //image
                                            $csv_row[] = "";  //small image      
                                            $csv_row[] = "";  //thumbnail  
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
                                            $csv_row[] = ""; //$tdProduct["PinnacleAttributeSKU"];  //_super_products_sku
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
                           if($this->product_counter % 2500 == 0) {
                                 $this->csv_counter++;
                                 fclose($this->fp);
                                 $this->fp = fopen("var/import/products/FL_Products".$this->csv_counter.".csv","w+");
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
            fclose($this->fp); 
            /**
            * @desc code to create the attributes configurable products csv file for uploading in magento
            */
            if (isset($arrBundleTDProducts) && is_array($arrBundleTDProducts) && count($arrBundleTDProducts) > 0) {   
                $this->fp = fopen("var/import/products/FL_BundleProducts".$this->bundle_csv_counter.".csv","w+"); 
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
             mail("dhiraj@clownantics.com","Products Details not found on FL site ",$mail_message,$headers);   
    } 
    function add_data_into_csv($arrSoloTDProducts,$arrTDProductsAttributes,$pr_type='Solo',$type_of_product='')
    {
        $site_images_path = "/"; 
        $holiday_arr = $this->arrFilterSeason['holiday'];
        $occassion_arr = $this->arrFilterSeason['occassion'];
        $seasons_arr = $this->arrFilterSeason['seasons']; 
        $counter=0;
        foreach ($arrSoloTDProducts as $tdProduct) { 
                       $theme_arr ='';  
                       $subtheme_arr='';  
                       $nonsubtheme_arr='';        
                       $type='';
                       $size = '';
                       $brand='';
                       $feature_arr='';
                       $filter_section='';
                       $product_type = '';
                       $show_all = '';
                       $categories_counter = 0;
                       $attributes_counter = 0;
                       $theme_counter = 0;
                       $subtheme_counter = 0; 
                       $nonsubtheme_counter = 0;  
                       $holiday_counter = 0; 
                       $occassion_counter = 0; 
                       $season_counter = 0; 
                       $feature_counter=0;
                       
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
                       $pr_season_arr='';
                       if(!in_array($tdProduct['PinnacleSKU'],$this->product_added_sku)  && ($tdProduct["Product - Weight"]>0)) { 
                            $this->product_added_sku[] = $tdProduct['PinnacleSKU']; 
                            if ($tdProduct['Product - Seasons']!=''){    
                                $pr_season_arr = explode(",",$tdProduct['Product - Seasons']); 
                            }
                            $pr_holiday_list = array();
                            $pr_occassion_list = array();
                            $pr_season_list = array();
                            foreach($pr_season_arr as $pr_season) {
                                 if(in_array(trim($pr_season),$holiday_arr)) {
                                      $pr_holiday_list[] = trim($pr_season); 
                                 }
                                 elseif(in_array(trim($pr_season),$occassion_arr)) {     
                                    $pr_occassion_list[] = trim($pr_season); 
                                 } 
                                 else {
                                     $pr_season_list[] = trim($pr_season);  
                                 }  
                            }  
                            if ($tdProduct['Product - Themes']!=''){    
                                $theme_arr = explode(",",$tdProduct['Product - Themes']);  
                            }
                            if ($tdProduct['Product - SubThemes']!=''){    
                                $subtheme_arr = explode(",",$tdProduct['Product - SubThemes']);  
                            }
                            if ($tdProduct['Product - Themes Full Name']!=''){    
                                $nonsubtheme_arr = explode(",",$tdProduct['Product - Themes Full Name']);  
                            }
                            if ($tdProduct['FLFilterSectionCalced']!=''){    
                                $filter_section = $tdProduct['FLFilterSectionCalced'];  
                            }
                            if ($tdProduct['Product - VENDOR - DisplayLabelEnteredFL']!=''){    
                                $brand = $tdProduct['Product - VENDOR - DisplayLabelEnteredFL'];  
                            }
                            if ($tdProduct['Product - Type - DisplayLabelEnteredFL']!=''){
                                $type = $tdProduct['Product - Type - DisplayLabelEnteredFL']; 
                            }
                            if ($tdProduct['Product - Filter - Size - DisplayLabelEnteredFL']!=''){    
                                $size = $tdProduct['Product - Filter - Size - DisplayLabelEnteredFL'];   
                            } 
                            if ($tdProduct['flagFeaturesSearchLabel']!=''){ 
                                $feature_arr = explode(",",$tdProduct['flagFeaturesSearchLabel']); 
                            } 
                            $web_option_label='';    
                            $concat_web=''; 
                            if($pr_type == 'Parent') {
                                 if($tdProduct['kitType'] == 'Fixed') {
                                     //$attribute_set='Filter New Products/Filter Section/Filter Type/Feature/Filter Size/Multi Seasons/Season Subthemes/Holiday/Holiday Subthemes/Occassion/Occassion Subthemes/Multi Themes/Theme Subthemes/Multi Brand';  
                                     $product_type = "bundle";  
                                 }
                                 else {   
                                    $product_type = "configurable";
                                    if($this->arrProductConfigurableAttributes[$lowerpinnacleSKU][0]['Web Option Label'] == 'Size') {
                                        $web_option_label = "FL Size";
                                    }
                                    else {
                                        $web_option_label = $this->arrProductConfigurableAttributes[$lowerpinnacleSKU][0]['Web Option Label']; 
                                    }    
                                    //$concat_web = $web_option_label!=''?"/".$web_option_label:"";
                                    //$attribute_set = "Filter New Products/Filter Section/Filter Type/Feature/Filter Size/Multi Seasons/Season Subthemes/Holiday/Holiday Subthemes/Occassion/Occassion Subthemes/Multi Themes/Theme Subthemes/Multi Brand".$concat_web; 
                                 }    
                            }
                            else {
                                if($arrTDProductsAttributes[$lowerpinnacleSKU][0]['Web Option Label'] == 'Size') {
                                        $web_option_label = "FL Size";
                                }
                                else {
                                    $web_option_label = $arrTDProductsAttributes[$lowerpinnacleSKU][0]['Web Option Label']; 
                                } 
                                //$concat_web = $web_option_label!=''?"/".$web_option_label:"";
                                //$attribute_set='Filter New Products/Filter Section/Filter Type/Feature/Filter Size/Multi Seasons/Season Subthemes/Holiday/Holiday Subthemes/Occassion/Occassion Subthemes/Multi Themes/Theme Subthemes/Multi Brand'.$concat_web;
                                $product_type = "simple";
                            }
                            if($attribute_set=='') {    
                                //$attribute_set='Filter New Products/Filter Section/Filter Type/Feature/Filter Size/Multi Seasons/Season Subthemes/Holiday/Holiday Subthemes/Occassion/Occassion Subthemes/Multi Themes/Theme Subthemes/Multi Brand/Accessory/Design/Finish/FL Size/Letter/Option';
                                $attribute_set = 'Accessory/Design/Finish/FL Size/Letter/Option';
                            }                                                                                                 
                            if($product_type=='') {    
                                $product_type = "simple"; 
                            }  
                            $related_design_family = '';
                            $related_product_family = '';
                            $design_counter = 0;
                            $related_counter =0;
                            $related_products='';
                            if ($tdProduct['Related Design Family']!=''){    
                                  $related_design_family = $tdProduct['Related Design Family'];
                                  if(count($this->arrTDDesignFamilyContents[$related_design_family]) > 0) {
                                        //$design_counter = count($this->arrTDDesignFamilyContents[$related_design_family]);
                                        foreach($this->arrTDDesignFamilyContents[$related_design_family] as $key => $rel_products) {  
                                            $related_products[] =  $rel_products['related_web_profile'];
                                        }    
                                  }    
                            }
                            if ($tdProduct['Related Product Family']!=''){    
                                $related_product_family = $tdProduct['Related Product Family']; 
                                if(count($this->arrTDProductFamilyContents[$related_product_family]) > 0) {
                                    //$related_counter = count($this->arrTDProductFamilyContents[$related_product_family]);
                                    foreach($this->arrTDProductFamilyContents[$related_product_family] as $key => $rel_products) {
                                        $related_products[] =  $rel_products['related_web_profile'];
                                    }
                                }  
                            }                             
                            $related_counter = count($related_counter);
                            $root_category = "Flagsrus"; 
                            $categories_counter = count($this->arrTDProductCategories[$lowerpinnacleSKU]);
                            $attributes_counter = count($this->arrProductConfigurableAttributes[$lowerpinnacleSKU]);
                            $theme_counter = count($theme_arr);
                            $subtheme_counter = count($subtheme_arr); 
                            $nonsubtheme_counter = count($nonsubtheme_arr);
                            $holiday_counter = count($pr_holiday_list); 
                            $occassion_counter = count($pr_occassion_list); 
                            $season_counter = count($pr_season_list); 
                            $feature_counter = count($feature_arr);
                            $show_all_counter = 2;
                            if($categories_counter > $attributes_counter){
                                $largest_counter = $categories_counter;
                            }
                            else {
                                $largest_counter = $attributes_counter; 
                            }
                            if($theme_counter > $largest_counter){
                                $largest_counter = $theme_counter;
                            }
                            if($subtheme_counter > $largest_counter){
                                $largest_counter = $subtheme_counter;
                            }
                            if($nonsubtheme_counter > $largest_counter){
                                $largest_counter = $nonsubtheme_counter;
                            }
                            if($holiday_counter > $largest_counter){
                                $largest_counter = $holiday_counter;
                            }
                            if($occassion_counter > $largest_counter){
                                $largest_counter = $occassion_counter;
                            }
                            if($season_counter > $largest_counter){
                                $largest_counter = $season_counter;
                            }
                            if($feature_counter > $largest_counter) {
                                $largest_counter = $feature_counter;
                            }    
                            if($design_counter > $largest_counter) {
                                $largest_counter = $design_counter;
                            }
                            if($related_counter > $largest_counter) {
                                $largest_counter = $related_counter;
                            }  
                            if($show_all_counter > $largest_counter) {
                                $largest_counter = $show_all_counter;
                            } 
                            if($largest_counter==0) {
                                $largest_counter=1;
                            }
                            $season_subtheme_arr='';
                            $holiday_subtheme_arr='';
                            $occassion_subtheme_arr='';
                            if(count($subtheme_arr) > 0) {
                                foreach($subtheme_arr as $subtheme_value) {
                                    if(in_array(trim($subtheme_value),$this->arrSeasonSubtheme)) {
                                         $season_subtheme_arr[] = trim($subtheme_value);  
                                    }
                                    if(in_array(trim($subtheme_value),$this->arrHolidaySubtheme)) {
                                         $holiday_subtheme_arr[] = trim($subtheme_value);  
                                    }
                                    if(in_array(trim($subtheme_value),$this->arrOccassionSubtheme)) {
                                         $occassion_subtheme_arr[] = trim($subtheme_value);  
                                    }    
                                }    
                            }    
                            /*if($tdProduct['PinnacleSKU'] == 'CA46125') {
                               echo '<pre>';
                               print_R($tdProduct);
                               echo "<br />holiday";
                               print_R($pr_holiday_list);
                               echo "<br />occassion";
                               print_R($pr_occassion_list);
                               echo "<br />season";
                               print_R($pr_season_list);
                               echo "<pre>";
                               print_R($season_subtheme_arr);
                               print_R($holiday_subtheme_arr);
                               print_R($occassion_subtheme_arr);
                               exit;
                            }*/  
                            $subtheme_added = array();
                            for($i=0;$i<$largest_counter;$i++) {
                                    $csv_row = array(); 
                                        $nonsubtheme='';
                                        $nonsubtheme_split='';
                                        $nonsubtheme_split = explode(": ",$nonsubtheme_arr[$i]);
                                        $nonsubtheme = $nonsubtheme_split[0]!=''&&$nonsubtheme_split[1]!=''?trim($nonsubtheme_split[0]).": ".trim($nonsubtheme_split[1]):"";
                                        if(!in_array($nonsubtheme,$subtheme_added)) {
                                            $subtheme_added[] = $nonsubtheme;
                                        }
                                        else {
                                           $nonsubtheme='';
                                        } 
                                        if($i == 0) {
                                            $filename='';
                                            $csv_row[] = $tdProduct['PinnacleSKU']; //sku
                                            $csv_row[] = "";  //_store
                                            $csv_row[] = $attribute_set; //_attribute_set 
                                            $csv_row[] = $product_type; //_type 
                                            $csv_row[] = $this->arrTDProductCategories[$lowerpinnacleSKU][$i]['category_path'];  //_category
                                            $csv_row[] = $root_category;  //_root_category
                                            $csv_row[] = "flagsrus";  //_product_websites
                                            $csv_row[] = $tdProduct['Priority_Cached'];  //priority
                                            $csv_row[] = str_replace("http://www.flagsrus.org/","",$tdProduct['url']); //url_key
                                            
                                            $csv_row[] = 'Show All'; //new_only_and_show_all
                                            $csv_row[] = $filter_section; //fl_filter_section
                                            $csv_row[] = $type; //fl_filter_type   
                                            $csv_row[] = $feature_arr[$i]; //feature                                
                                            $csv_row[] = $size; //fl_filter_size 
                                            $csv_row[] = trim($pr_season_list[$i]); //multi seasons 
                                            $csv_row[] = $season_subtheme_arr[$i]!=''?$season_subtheme_arr[$i]:""; //season_subthemes  
                                            $csv_row[] = trim($pr_holiday_list[$i]); //holiday   
                                            $csv_row[] = $holiday_subtheme_arr[$i]!=''?$holiday_subtheme_arr[$i]:""; //holiday_subthemes  
                                            $csv_row[] = trim($pr_occassion_list[$i]); //occassion  
                                            $csv_row[] = $occassion_subtheme_arr[$i]!=''?$occassion_subtheme_arr[$i]:""; //occassion_subthemes   
                                            $csv_row[] = trim($theme_arr[$i]); //multi themes 
                                            $csv_row[] = in_array(trim($nonsubtheme),$this->arrThemeSubtheme)?trim($nonsubtheme):""; //themes_subthemes  
                                            $csv_row[] = $brand; //multi brand  
                                            $accessory_value='';
                                            $design_value='';
                                            $size_value='';
                                            $option_value='';
                                            $finish_value='';
                                            $letter_value='';
                                            if(count($arrTDProductsAttributes[$lowerpinnacleSKU])>0) {
                                                foreach($arrTDProductsAttributes[$lowerpinnacleSKU] as $key => $attribute_details) {
                                                     if($attribute_details['Web Option Label']=="Accessory") {
                                                         $accessory_value = $attribute_details["Attribute"];  
                                                      }
                                                      elseif($attribute_details['Web Option Label']=="Design") {
                                                         $design_value = $attribute_details["Attribute"];  
                                                      }
                                                      elseif($attribute_details['Web Option Label']=="Size") {
                                                         $size_value = $attribute_details["Attribute"];  
                                                      }
                                                      elseif($attribute_details['Web Option Label']=="Option") {
                                                         $option_value = $attribute_details["Attribute"];  
                                                      }
                                                      elseif($attribute_details['Web Option Label']=="Finish") {
                                                         $finish_value = $attribute_details["Attribute"];  
                                                      }
                                                      elseif($attribute_details['Web Option Label']=="Letter") {
                                                          $letter_value = $attribute_details["Attribute"];  
                                                      }  
                                                }
                                            }               
                                            $csv_row[] = $accessory_value; //Accessory 
                                            $csv_row[] = $design_value; //Design  
                                            $csv_row[] = $size_value;  //FL size
                                            $csv_row[] = $option_value; //Option 
                                            $csv_row[] = $finish_value; //Finish 
                                            $csv_row[] = $letter_value; //Letter  
                                            
                                            $csv_row[] = $tdProduct['kitType']== 'Fixed'?"1":"0"; //is_fixed_kit 
                                            $csv_row[] = $tdProduct['iconLabel']; //iconlabel 
                                            $csv_row[] = $tdProduct['Product - Next Date Due To Arrive']>date("Y-m-d")?$tdProduct['Product - Next Date Due To Arrive']:""; //date_of_arrival
                                            $csv_row[] = $tdProduct['Product - QTY On Current POs']; //qty_on_current_po
                                            $csv_row[] = $tdProduct['kitType']=='Fixed'?'1':'1'; //price_type   
                                            $csv_row[] = $tdProduct['kitType']=='Fixed'?'As Low as':''; //price_view 
                                            $csv_row[] = $tdProduct['flagShowInMonogramFilter?']=="1"?'yes':'no'; //display_product_in_filter
                                            $csv_row[] = ""; //custom_layout_update
                                            $csv_row[] = ""; //custom_design   ultimo/default
                                            $csv_row[] = "1 column";  //page_layout
                                            $csv_row[] = "Disabled (do not show)"; //estimated_delivery_enable Inherited  
                                            $csv_row[] = "Disabled (do not show)"; //estimated_shipping_enable  Inherited  
                                            $csv_row[] = $tdProduct['Description']; //description
                                            /****
                                            * @desc code for copying the images
                                            */
                                            $image_path = $tdProduct['imgLocationCustom'];//str_replace("http://myclownantics.com/","/home/myclown/public_html/",$tdProduct['imgLocationCustom']);
                                            $image_arr = @getimagesize($image_path);  
                                            $related_image_path = "http://myclownantics.com/admin/CA_resize_500_500/".strtolower($tdProduct['Related Product']).".jpg"; 
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
                                            $csv_row[] = ""; //ormd
                                            $csv_row[] = $tdProduct['PriceCalced'];   //price 
                                            $csv_row[] = $tdProduct['DiscountPriceCalced']>0?$tdProduct['DiscountPriceCalced']:"";   //special price   
                                            $csv_row[] = $tdProduct['Description'];   //short_description
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
                                            $csv_row[] = $tdProduct['Image Alt Text 1']; //_media_lable
                                            $csv_row[] = "1"; //_media_position
                                            $csv_row[] = "0"; //_media_is_disabled
                                            $csv_row[] = $tdProduct['kitType']!='Fixed'?$this->arrProductConfigurableAttributes[$lowerpinnacleSKU][$i]['Product - FL Solo PinnacleSKU']:""; //$tdProduct["PinnacleAttributeSKU"];  //_super_products_sku
                                            if(strtolower($this->arrProductConfigurableAttributes[$lowerpinnacleSKU][$i]['Web Option Label'])=='size') {
                                                $web_option = 'fl_size';
                                            }
                                            elseif(strtolower($this->arrProductConfigurableAttributes[$lowerpinnacleSKU][$i]['Web Option Label'])=='option') {
                                                $web_option = 'fl_option';
                                            }  
                                            else {
                                                 $web_option = strtolower($this->arrProductConfigurableAttributes[$lowerpinnacleSKU][$i]['Web Option Label']);   
                                            }  
                                            $csv_row[] = $tdProduct['kitType']!='Fixed'?$web_option:"";   //_super_attribute_code
                                            $csv_row[] = $tdProduct['kitType']!='Fixed'?$this->arrProductConfigurableAttributes[$lowerpinnacleSKU][$i]['Attribute']:""; //$tdProduct["Attribute"]; //_super_attribute_option
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
                                            $csv_row[] = $tdProduct['kitType']!='Fixed'?$price:"";//_super_attribute_price_corr
                                            $csv_row[] = $tdProduct['kitType']=='Fixed'?$this->arrProductConfigurableAttributes[$lowerpinnacleSKU][$i]['Product - FL Solo PinnacleSKU']:"";//bundle_sku  
                                            $csv_row[] = $tdProduct['kitType']=='Fixed'?$web_option:"";//bundle_option_title   
                                            $csv_row[] = $tdProduct['kitType']=='Fixed'?$this->arrProductConfigurableAttributes[$lowerpinnacleSKU][$i]['Attribute']:"";//bundle_attribute 
                                            $csv_row[] = $tdProduct['kitType']=='Fixed'?$attributePrice:"";//bundle_price  
                                            $csv_row[] = $tdProduct['kitType']=='Fixed'?$this->arrProductConfigurableAttributes[$lowerpinnacleSKU][$i]['priority']:"";//bundle_position   
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
                                                
                                                $csv_row[] = $tdProduct['isNewProduct?']=='Yes' && $i==1?'New Only':""; //new_only_and_show_all  
                                                $csv_row[] = ""; //fl_filter_section 
                                                $csv_row[] = ""; //fl_filter_type
                                                $csv_row[] = trim($feature_arr[$i]); //feature 
                                                $csv_row[] = ""; //fl_filter_size 
                                                $csv_row[] = trim($pr_season_list[$i]); //multi seasons 
                                                $csv_row[] = $season_subtheme_arr[$i]!=''?$season_subtheme_arr[$i]:""; //season_subthemes  
                                                $csv_row[] = trim($pr_holiday_list[$i]); //holiday   
                                                $csv_row[] = $holiday_subtheme_arr[$i]!=''?$holiday_subtheme_arr[$i]:""; //holiday_subthemes  
                                                $csv_row[] = trim($pr_occassion_list[$i]); //occassion  
                                                $csv_row[] = $occassion_subtheme_arr[$i]!=''?$occassion_subtheme_arr[$i]:""; //occassion_subthemes   
                                                $csv_row[] = trim($theme_arr[$i]); //multi themes 
                                                $csv_row[] = in_array(trim($nonsubtheme),$this->arrThemeSubtheme)?trim($nonsubtheme):""; //themes_subthemes  
                                            
                                                $csv_row[] = ""; //multi brand  
                                                
                                                $csv_row[] = ""; //accessory
                                                $csv_row[] = ""; //design  
                                                $csv_row[] = "";  //fl_size
                                                $csv_row[] = ""; //option 
                                                $csv_row[] = ""; //finish 
                                                $csv_row[] = ""; //letter 
                                            
                                                $csv_row[] = ""; //is_fixed_kit  
                                                $csv_row[] = ""; //iconlabel
                                                $csv_row[] = ""; //date_of_arrival
                                                $csv_row[] = ""; //qty_on_current_po  
                                                $csv_row[] = ""; //price_type   
                                                $csv_row[] = ""; //price_view
                                                $csv_row[] = ""; //display_product_in_filter    
                                                $csv_row[] = ""; //custom_layout_update
                                                $csv_row[] = ""; //custom design
                                                $csv_row[] = ""; //page layout
                                                $csv_row[] = ""; //estimated_delivery_enable   
                                                $csv_row[] = ""; //estimated_shipping_enable   
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
                                                $csv_row[] = $tdProduct['kitType']!='Fixed'?$this->arrProductConfigurableAttributes[$lowerpinnacleSKU][$i]['Product - FL Solo PinnacleSKU']:""; //$tdProduct["PinnacleAttributeSKU"];  //_super_products_sku
                                                if(strtolower($this->arrProductConfigurableAttributes[$lowerpinnacleSKU][$i]['Web Option Label'])=='size') {
                                                    $web_option = 'fl_size';
                                                }
                                                elseif(strtolower($this->arrProductConfigurableAttributes[$lowerpinnacleSKU][$i]['Web Option Label'])=='option') {
                                                    $web_option = 'fl_option';
                                                }  
                                                else {
                                                     $web_option = strtolower($this->arrProductConfigurableAttributes[$lowerpinnacleSKU][$i]['Web Option Label']);   
                                                }  
                                                $csv_row[] = $tdProduct['kitType']!='Fixed'?$web_option:"";   //_super_attribute_code         
                                                $csv_row[] = $tdProduct['kitType']!='Fixed'?$this->arrProductConfigurableAttributes[$lowerpinnacleSKU][$i]['Attribute']:""; //$tdProduct["Attribute"]; //_super_attribute_option
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
                                                $csv_row[] = $tdProduct['kitType']!='Fixed'?$price:""; //_super_attribute_price_corr
                                                $csv_row[] = $tdProduct['kitType']=='Fixed'?$this->arrProductConfigurableAttributes[$lowerpinnacleSKU][$i]['Product - FL Solo PinnacleSKU']:"";//bundle_sku  
                                                $csv_row[] = $tdProduct['kitType']=='Fixed'?$web_option:"";//bundle_option_title   
                                                $csv_row[] = $tdProduct['kitType']=='Fixed'?$this->arrProductConfigurableAttributes[$lowerpinnacleSKU][$i]['Attribute']:"";//bundle_attribute 
                                                $csv_row[] = $tdProduct['kitType']=='Fixed'?$attributePrice:"";//bundle_price  
                                                $csv_row[] = $tdProduct['kitType']=='Fixed'?$this->arrProductConfigurableAttributes[$lowerpinnacleSKU][$i]['priority']:"";//bundle_position   

                                        }        
                                        fputcsv($this->fp,$csv_row);
                                    } // End of for largest counter loop
                            } // End of if not in product added array  
                            if($this->product_counter % 700 == 0 && $type_of_product!='bundle') {
                                 $this->csv_counter++;  
                                 fclose($this->fp);
                                 $this->fp = fopen("var/import/products/FL_Products".$this->csv_counter.".csv","w+");
                                 $this->add_header_into_csv();   
                            } 
                            if($this->bundle_product_counter % 500 == 0 && $type_of_product=='bundle') {
                                 $this->bundle_csv_counter++;  
                                 fclose($this->fp);
                                 $this->fp = fopen("var/import/products/FL_BundleProducts".$this->bundle_csv_counter.".csv","w+");  
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
            if($type=='Fixed') {
                $type = " AND ([kitType]='".$type."')"; 
            }   
            elseif($type == 'Parent') {
                $type = " AND Nz([kitType],'')<>'Fixed' AND ([Type Attribute]='".$type."' OR [Type Attribute]='Monogram')";  
            }
            else {    
                $type = "  AND Nz([kitType],'')<>'Fixed' AND [Type Attribute]='".$type."'";
            }    
        }
        $arrQueries = "WHERE [SendToPinnacle] $type AND [is_visible]";
        /**
        * @desc  create string of columns to be retreived from the query  
        */       
        $strColumns = "[flagShowInMonogramFilter?], [isNewProduct?], [Product - VENDOR - DisplayLabelEnteredFL],[Product - Type - DisplayLabelEnteredFL],[Product - Filter - Size - DisplayLabelEnteredFL],[Product - Seasons], [Product - Themes], [Product - SubThemes],[Product - Themes Full Name],[PinnacleSKU],[Description],[Display Name],[PriceCalced],[DiscountPriceCalced],[overview],[is_visible],[Product - Weight],[Quantity Available],[imgLocationCustom],[Related Product],[meta_description],[meta_keywords],[meta_title],[kitType],[Related Design Family],[Related Product Family],[Priority_Cached],[FLFilterSectionCalced],[url],[flagFeaturesSearchLabel],[Image Alt Text 1],[Product - Next Date Due To Arrive],[Product - QTY On Current POs],[iconLabel]";   
        try
        {        
            $arrResults = $this->api->Query("SELECT TOP 800 ".$strColumns." FROM [FL Web Profile] ".$arrQueries." ORDER BY [PinnacleSKU]"); 
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
                        $arrResults = $this->api->Query("SELECT TOP 800 ".$strColumns." FROM [FL Web Profile] ".$arrQueries." ORDER BY [PinnacleSKU]");
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
        //$strColumns = "[Web Option Label],[Attribute],[priority],[PriceCalced],[DiscountPriceCalced],[Product - Quantity Available],[Product - Weight],[is_modifier],[is_active],[PinnacleAttributeSKU],[Related Web Profile],[Product - FL Solo PinnacleSKU],[Product - Solo Profile Display Name],[Product - Solo Profile Description],[Web Profile - Display Name],[Web Profile - Description],[imgLocationCustom],[Web Profile - isNewProduct?],[Web Profile - meta_description],[Web Profile - meta_title]";  
        $strColumns = "[Web Option Label],[Attribute],[priority],[PriceCalced],[DiscountPriceCalced],[Product - Quantity Available],[PinnacleAttributeSKU],[Related Web Profile],[Product - FL Solo PinnacleSKU],[imgLocationCustom]";  
       try
       {
            $arrResults = $this->api->Query("SELECT TOP 3000 ".$strColumns." FROM [FL Attribute] ".$arrQueries ." ORDER BY [PinnacleAttributeSKU] ASC"); 
            if (isset($arrResults->Rows)) {    
                foreach ($arrResults->Rows as $tdProductAtt) {    
                     $last_record = $tdProductAtt['PinnacleAttributeSKU'];
                     $productSKU = (string) $tdProductAtt['Product - FL Solo PinnacleSKU'];
                     $productSKU = strtolower($productSKU);
                     $arrProductAttributes[$productSKU][] = $tdProductAtt; 
                     
                     $configproductSKU = (string) $tdProductAtt['Related Web Profile'];
                     $configproductSKU = strtolower($configproductSKU);
                     $this->arrProductConfigurableAttributes[$configproductSKU][] = $tdProductAtt;  
                }
                
                $resultcount = 0;
                while($resultcount == 0) {
                    $arrQueries = "WHERE [sendToPinnacle] AND [is_active]='Yes' AND [PinnacleAttributeSKU] > '".$last_record."'";
                    /**
                    * @desc  create string of columns to be retreived from the query - Related Category, is_primary, Related Web Profile, Priority  
                    */                      
                    try {  
                        $arrResults = $this->api->Query("SELECT TOP 3000 ".$strColumns." FROM [FL Attribute] ".$arrQueries." ORDER BY [PinnacleAttributeSKU] ASC ");
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
                              $last_record = $tdProductAtt['PinnacleAttributeSKU'];
                              $productSKU = (string) $tdProductAtt['Product - FL Solo PinnacleSKU'];
                              $productSKU = strtolower($productSKU);
                              $arrProductAttributes[$productSKU][] = $tdProductAtt;  
                              
                              $configproductSKU = (string) $tdProductAtt['Related Web Profile'];
                              $configproductSKU = strtolower($configproductSKU);
                              $this->arrProductConfigurableAttributes[$configproductSKU][] = $tdProductAtt;
                        }
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
                  
                  $product_header_row[] = "fl_new_products";
                  $product_header_row[] = "fl_filter_section";
                  $product_header_row[] = "fl_filter_type"; 
                  $product_header_row[] = "feature";
                  $product_header_row[] = "fl_filter_size";
                  $product_header_row[] = "multi_seasons"; 
                  $product_header_row[] = "season_subtheme";    
                  $product_header_row[] = "holiday"; 
                  $product_header_row[] = "holiday_subtheme";
                  $product_header_row[] = "occassion";  
                  $product_header_row[] = "occassion_subthemes";
                  $product_header_row[] = "multi_themes";
                  $product_header_row[] = "theme_subthemes";  
                  $product_header_row[] = "multi_brand"; 
                  
                  $product_header_row[] = "accessory";
                  $product_header_row[] = "design"; 
                  $product_header_row[] = "fl_size";
                  $product_header_row[] = "fl_option";
                  $product_header_row[] = "finish";
                  $product_header_row[] = "letter";
                  
                  $product_header_row[] = "is_fixed_kit";
                  $product_header_row[] = "iconlabel";
                  $product_header_row[] = "date_of_arrival"; 
                  $product_header_row[] = "qty_on_current_po";
                  $product_header_row[] = "price_type";  
                  $product_header_row[] = "price_view"; 
                  $product_header_row[] = "display_product_in_filter";  
                  
                  $product_header_row[] = "custom_layout_update";
                  $product_header_row[] = "custom_design"; 
                  $product_header_row[] = "page_layout";   
                  $product_header_row[] = "estimated_delivery_enable";
                  $product_header_row[] = "estimated_shipping_enable"; 
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
                  $product_header_row[] = "bundle_position"; 
                  fputcsv($this->fp,$product_header_row);
           }
    }    
    /**
    * @desc code to get filter seasons from TD
    */
    private function getTDFilterSeason()
    {
        $arrFilterSeason = array();
        try
        {    
            /** 
            * @desc  create the teamdesk query, to fetch all the promo codes
            */
            $arrQueries = "";
            $strColumns = "[Name], [isOccasion?], [FLFilterSortOrder],[IsHoliday?]";
            $sqlQuery = "SELECT ".$strColumns." FROM [Season] ".$arrQueries; 
            /** 
            * @desc  create string of columns to be retreived from the query
            */
            $arrResults = $this->api->Query($sqlQuery); 
             if (isset($arrResults)) {   
                foreach ($arrResults->Rows as $tdFilter) {  
                    if($tdFilter['isOccasion?'] == 1) {
                        $arrFilterSeason['occassion'][] = $tdFilter['Name'];
                    }
                    elseif($tdFilter['IsHoliday?'] == 1) {
                        $arrFilterSeason['holiday'][] = $tdFilter['Name'];
                    }
                    else {     
                        $arrFilterSeason['seasons'][] = $tdFilter['Name'];
                    }    
                }                
            }
        } 
        catch (Exception $e) {
             echo $this->tdErrorLog = '<br/>Error Fetching Filter Season from TD.Caught exception: ' .$e->getMessage(). "<br/>";
             $strReturn .= $this->tdErrorLog;  
             die;  
             return  $strReturn;         
        }
        return  $arrFilterSeason;
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
        $arrQueries = "WHERE [FL Category - isBetaOrLive?]='Live'"; 
        /**
        * @desc  create string of columns to be retreived from the query - Related Category, is_primary, Related Web Profile, Priority  
        */                      
        $strColumns = "[Category - Label],[Related Web Profile],[Record ID#]";  //,[Priority],[meta_title],[meta_description] 
        try {             
            $arrResults = $this->api->Query("SELECT TOP 3000 ".$strColumns." FROM [FL Web Entry] ".$arrQueries." ORDER BY [Record ID#]");
            $total_result_rows = $total_result_rows + count($arrResults->Rows);
        } 
        catch (Exception $e) {
             echo $this->tdErrorLog = '<br/>Error Fetching Web entries from TD. Caught exception: ' .$e->getMessage(). "<br/>";
             $strReturn .= $this->tdErrorLog;    
             die;
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
            $arrQueries = "WHERE [FL Category - isBetaOrLive?]='Live' AND [Record ID#] > '".$last_record."'";
            /**
            * @desc  create string of columns to be retreived from the query - Related Category, is_primary, Related Web Profile, Priority  
            */                      
            $strColumns = "[Category - Label],[Related Web Profile],[Record ID#]"; //,[Priority],[meta_title],[meta_description] 
            try {  
                $arrResults = $this->api->Query("SELECT TOP 3000 ".$strColumns." FROM [FL Web Entry] ".$arrQueries." ORDER BY [Record ID#]");
                $total_result_rows = $total_result_rows + count($arrResults->Rows);  
            }
            catch (Exception $e) {
                 echo $this->tdErrorLog = '<br/>Error Fetching Web entries from TD.Caught exception: ' .$e->getMessage(). "<br/>";
                 $strReturn .= $this->tdErrorLog;    
                 die;       
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
            $strColumns = "[PinnacleSKU],[Quantity Available],[is_visible],[Product - Next Date Due To Arrive],[Product - QTY On Current POs],[Priority_Cached]"; 
            $arrTDProducts = $this->api->Query("SELECT TOP 1500 ".$strColumns." FROM [FL Web Profile] ".$arrQueries." ORDER BY [PinnacleSKU]");  
            if (isset($arrTDProducts->Rows)) {
                foreach ($arrTDProducts->Rows as $productDetails) {
                          $arrProducts[] = $productDetails;
                          $last_record = $productDetails['PinnacleSKU'];
                }        
                $resultcount = 0;
                while($resultcount == 0) {
                      $arrQueries = "WHERE [sendToPinnacle] AND [PinnacleSKU] > '".$last_record."'";   //AND [Product - Weight]>0 AND [is_visible]
                      try {  
                            $arrTDProducts = $this->api->Query("SELECT TOP 1500 ".$strColumns." FROM [FL Web Profile] ".$arrQueries." ORDER BY [PinnacleSKU]");
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
                            $db->assignStr("value", $TDProduct['Priority_Cached']!=''?$TDProduct['Priority_Cached']:0);  
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
    * @return array $arrDesignFamily array of all the product families
    * @since 01-Feb-2013
    * @author Dinesh Nagdev
    */
    private function getTDDesignFamilyContents()
    {
        $arrTDDesignFamilyContents = array();
        /** 
        * @desc  create the teamdesk query, to fetch all the product families
        */
        $arrQueries = "WHERE ([Record ID#] > '0') AND [Related Design Family] > '0' AND [isDesignOrAccessory?]='Design'"; 
        /** 
        * @desc  create string of columns to be retreived from the query  - Related Web Profile, 
        * Related Prod Family, Product Family Name
        */
        $strColumns = "[Related Web Profile],[Related Design Family],[Design Family - Name]";  
        try {
            $arrResults = $this->api->Query("SELECT ".$strColumns." FROM [FL Product Accessory] ".$arrQueries);  
            if (isset($arrResults->Rows)) {  
                foreach ($arrResults->Rows as $tdDesignFamilyContent) {  
                     $TDFamilyID = (int) $tdDesignFamilyContent['Related Design Family'];
                     $arrTDDesignFamilyContents[$TDFamilyID][] = array (
                                                                'related_web_profile' => (string) $tdDesignFamilyContent['Related Web Profile'],
                                                                );
                }
            }
            return $arrTDDesignFamilyContents;      
        }
        catch (Exception $e) {
            $this->tdErrorLog = '<br/>Error in fetching product design family contents form FL Product Accessory : Caught exception: ' .$e->getMessage(). "<br/>";
            echo $this->tdErrorLog;
            die;         
        }     
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
        $arrQueries = "WHERE ([Record ID#] > '0') AND [Related Product Family] > '0' AND [isDesignOrAccessory?]='Accessory'"; 
        /** 
        * @desc  create string of columns to be retreived from the query  - Related Web Profile, 
        * Related Prod Family, Product Family Name
        */
        $strColumns = "[Related Web Profile],[Related Product Family],[Product Family - Name]";  
        try {
            $arrResults = $this->api->Query("SELECT ".$strColumns." FROM [FL Product Accessory] ".$arrQueries);  
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
            $this->tdErrorLog = '<br/>Error in fetching product family contents from FL Product Accessory : Caught exception: ' .$e->getMessage(). "<br/>";
            echo $this->tdErrorLog;
            die;         
        }         
    }
    
    private function getTDOccassionSubTheme()
    {
        $arrOccassionSubTheme = array();
        /** 
        * @desc  create the teamdesk query, to fetch all the promo codes
        */
        $arrQueries = "WHERE [Season - isHoliday?]=false and [# of Product Season Themes]>0 and not IsNull([FL Theme - Sub Theme]) and [Season - isOccasion?]=true and [FL Theme - isSeasonal?]=true and [is_visible]=true";  
        $strColumns = "[Season - Name],[FL Theme - Sub Theme]";
        $sqlQuery = "SELECT ".$strColumns." FROM [Product Season] ".$arrQueries; 
        try{
            /** 
            * @desc  create string of columns to be retreived from the query
            */
            $arrResults = $this->api->Query($sqlQuery);     
             if (isset($arrResults)) {   
                foreach ($arrResults->Rows as $tdFilter) {    
                    $subTheme = trim($tdFilter['FL Theme - Sub Theme']);
                    if(!in_array($subTheme,$arrOccassionSubTheme)) {   
                        $arrOccassionSubTheme[] = $subTheme;
                    }    
                }                
            }
            return  $arrOccassionSubTheme;
        }      
        catch (Exception $e) {
            $this->tdErrorLog = '<br/>Error in sub themes from FL Product Subtheme : Caught exception: ' .$e->getMessage(). "<br/>";
            echo $this->tdErrorLog;
            die;         
        } 
    }
    private function getTDHolidaySubTheme()
    {
        $arrHolidaySubTheme = array();
        /** 
        * @desc  create the teamdesk query, to fetch all the promo codes
        */
        $arrQueries = "WHERE [Season - isHoliday?]=true and [# of Product Season Themes]>0 and not IsNull([FL Theme - Sub Theme]) and [Season - isOccasion?]=false and [FL Theme - isSeasonal?]=true and [is_visible]=true";  
        $strColumns = "[Season - Name],[FL Theme - Sub Theme]";
        $sqlQuery = "SELECT ".$strColumns." FROM [Product Season] ".$arrQueries; 
        try{
            /** 
            * @desc  create string of columns to be retreived from the query
            */
            $arrResults = $this->api->Query($sqlQuery);     
             if (isset($arrResults)) {   
                foreach ($arrResults->Rows as $tdFilter) {    
                    $subTheme = trim($tdFilter['FL Theme - Sub Theme']);
                    if(!in_array($subTheme,$arrHolidaySubTheme)) {   
                        $arrHolidaySubTheme[] = $subTheme;
                    }    
                }                
            }
            return  $arrHolidaySubTheme;
        }      
        catch (Exception $e) {
            $this->tdErrorLog = '<br/>Error in sub themes from FL Holiday Subtheme : Caught exception: ' .$e->getMessage(). "<br/>";
            echo $this->tdErrorLog;
            die;         
        } 
    }
    private function getTDSeasonSubTheme()
    {
        $arrSeasonSubTheme = array();
        /** 
        * @desc  create the teamdesk query, to fetch all the promo codes
        */
        $arrQueries = "WHERE [Season - isHoliday?]=false and [# of Product Season Themes]>0 and not IsNull([FL Theme - Sub Theme]) and [Season - isOccasion?]=false and [FL Theme - isSeasonal?]=true and [is_visible]=true";  
        $strColumns = "[Season - Name],[FL Theme - Sub Theme]";
        $sqlQuery = "SELECT ".$strColumns." FROM [Product Season] ".$arrQueries; 
        try{
            /** 
            * @desc  create string of columns to be retreived from the query
            */
            $arrResults = $this->api->Query($sqlQuery);     
             if (isset($arrResults)) {   
                foreach ($arrResults->Rows as $tdFilter) {    
                    $subTheme = trim($tdFilter['FL Theme - Sub Theme']);
                    if(!in_array($subTheme,$arrSeasonSubTheme)) {   
                        $arrSeasonSubTheme[] = $subTheme;
                    }    
                }                
            }
            return  $arrSeasonSubTheme;
        }      
        catch (Exception $e) {
            $this->tdErrorLog = '<br/>Error fetching sub themes from FL Season Subtheme : Caught exception: ' .$e->getMessage(). "<br/>";
            echo $this->tdErrorLog;
            die;         
        } 
    }
    private function getTDThemeSubTheme()
    {
        $arrThemeSubTheme = array();
        /** 
        * @desc  create the teamdesk query, to fetch all the promo codes
        */
        $arrQueries = "WHERE [is_active]=true and [Level]=2 and [isSeasonal?]=false";  
        $strColumns = "[ThemeSubTheme]";
        $sqlQuery = "SELECT ".$strColumns." FROM [FL Theme] ".$arrQueries; 
        try{
            /** 
            * @desc  create string of columns to be retreived from the query
            */
            $arrResults = $this->api->Query($sqlQuery);     
             if (isset($arrResults)) {   
                foreach ($arrResults->Rows as $tdFilter) { 
                    $subTheme = trim($tdFilter['ThemeSubTheme']);
                    if(!in_array($subTheme,$arrThemeSubTheme)) {   
                         $arrThemeSubTheme[] = $subTheme;
                    }    
                }                
            }
            return  $arrThemeSubTheme;
        }      
        catch (Exception $e) {
            $this->tdErrorLog = '<br/>Error fetching sub themes from FL Theme Subtheme : Caught exception: ' .$e->getMessage(). "<br/>";
            echo $this->tdErrorLog;
            die;         
        } 
    }
    /**
    * @desc function to get channel advisor products and inventory from teamdesk
    */
    public function getCATDProductsInventory()
    {
        $arrTDProductInventory = array();
        $this->connectToTeamDesk();
        if($this->api !='' )      
        { 
            /** 
            * @desc  create the teamdesk query, to fetch all the promo codes
            */
            $arrQueries = 'WHERE [Product - is_visible]=true and [Product - isFlagsrusProduct?]=true and Ask(Contains([sku], Parameter([sku], Text))) and Ask(Contains([Related Amazon entry], Parameter([Related Amazon entry], Text))) and [isVisibleOnAmazon?]=true and [TypeJet]<>"Parent"';  
            $strColumns = "[sku],[quantity],[item-price]";
            $sqlQuery = "SELECT ".$strColumns." FROM [Amazon Entry] ".$arrQueries; 
            try{
                /** 
                * @desc  create string of columns to be retreived from the query
                */
                $arrResults = $this->api->Query($sqlQuery);     
                 if (isset($arrResults)) {   
                    return $arrResults->Rows;               
                }
            }      
            catch (Exception $e) {
                $this->tdErrorLog = '<br/>Error fetching Channel Advisor products from Amazon Entry table : Caught exception: ' .$e->getMessage(). "<br/>";
                echo $this->tdErrorLog;
                die;         
            } 
        }     
    }
}

?>                                           

