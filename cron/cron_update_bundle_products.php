<?php
define('BASE_PATH', dirname(__FILE__).'/../'); 
set_time_limit(0);
ini_set("memory_limit","512M");
ini_set("display_errors",1);   
            
$mageFilename = BASE_PATH.'app/Mage.php';
echo 'got';exit;
require_once $mageFilename;
Mage::app();
for($i=1;$i<=2;$i++) {    
    $tmp_succ_message = FLUpdateBundleProductData("var/import/products/FL_BundleProducts$i.csv"); 
    if(count($tmp_succ_message) > 0) {
        foreach($tmp_succ_message as $key=> $temp_error ) {
            $succ_message['error'] .= "<br />Error in Bundle Product $key => ".$temp_error;
        }    
    }
}  
  function FLUpdateBundleProductData($csvFile) {
           if($csvFile!='') {
                   $file_handle = fopen($csvFile, 'r');
                   $i=0;
                   while (!feof($file_handle) ) {
                       if($i==0) {
                           $header_data[] = fgetcsv($file_handle, 0); 
                       } 
                       else {   
                            $data_arr[] = fgetcsv($file_handle, 0);
                       }  
                       $i++;   
                    }
                    fclose($file_handle);
                    foreach($data_arr as $data) {
                        $bundle_data_arr[] = array_combine($header_data[0], $data);
                    }
                    $i=0;
                    foreach($bundle_data_arr as $key => $arr) {    
                        if($arr['sku']!='') {
                            $i=0;
                            $sku = $arr['sku'];
                        }
                        if($arr!='') {
                            $bundle_arr[$sku][$i] = $arr;
                            $i++;
                        }    
                    }          
                    /**
                    * @desc code to update the bundle product options and data   
                    */
                    $k=0;
                    foreach($bundle_arr as $key => $bundle_details) {     
                        echo "<br />product sku ".$key;
                        //if($key =='F50420K' || $key=='F50420T' || $key=='F10699 SP' || $key=='F50420C' || $key=='F62099 SP' || $key=='M61200' || $key='M61202'  ) {
                        $num_of_options=0;
                        $required_sku_for_kit='';
                        $is_fix_kit = $bundle_details[0]['is_fixed_kit'];
                        $num_of_options = $bundle_details[0]['num_of_options_for_kit'];
                        $required_sku_for_kit = $bundle_details[0]['requiredskuforkit'];
                        $bundleProduct = Mage::getModel('catalog/product')->loadByAttribute('sku',$key);  
                        $main_product_id = $bundleProduct->entity_id;  
                        $bundleProduct->setIsSuperMode(true);
                        $bundleProduct->setData('_edit_mode', true);
                        //$bundleProduct->setData('price_type', 1);   
                        //$bundleProduct->setData('price', $bundle_details[0]['price']);   
                        //$bundleProduct->setPriceType(1); //price type (0 - dynamic, 1 - fixed)   
                        //$bundleProduct->setPrice($bundle_details[0]['price']); 
                        /**
                        * @desc code to check if children/options exists
                        */
                        $option_array = array();
                        $bundled_items = array();
                        $children = Mage::getResourceModel('bundle/selection')->getChildrenIds($main_product_id, false);
                        if(count($children)>0) {
                            /**
                            * @desc code to get the options of bundle product
                            */
                            $bundled_items = array(); 
                            $bundled_product_details = Mage::getModel('catalog/product')->load($main_product_id);          
                            $optionCollection = $bundled_product_details->getTypeInstance(true)->getOptionsCollection($bundled_product_details); 
                            $selectionCollection = $bundled_product_details->getTypeInstance(true)->getSelectionsCollection(
                                $bundled_product_details->getTypeInstance(true)->getOptionsIds($bundled_product_details), $bundled_product_details);    
                            /**
                            * @desc code to create the array of options and selections.
                            */ 
                            if(count($selectionCollection) >0) {
                                foreach($selectionCollection as $option) 
                                {
                                    $option_id = $option->getData('option_id');  
                                    foreach ($optionCollection as $option_val) {
                                        if ($option_val->getOptionId() == $option_id)
                                        {
                                           $option_array[$option_id] = $option_val; 
                                        }
                                    }        
                                    $selection_id = $option->getData('selection_id');   
                                    $bundled_items[$option_id][$selection_id] = $option->product_id;
                                }  
                            } 
                            foreach ($optionCollection as $option_val) {
                                if ($bundled_items[$option_val->getOptionId()]=='')
                                {
                                   $bundled_items[$option_val->getOptionId()] = array(); 
                                   $option_array[$option_val->getOptionId()] = $option_val; 
                                }
                            }    
                        }    
                        if($bundleProduct!='') {
                            $bundleOptions = array();
                            $bundleSelections = array();
                            $i=0;
                            $j=0;
                            $s=0;    
                            $k=0;    
                            $options_added_array = array(); 
                            $bundle_selection_id='';
                            $bundle_option_id='';  
                            $bundleUpdatedSelections = array();  
                            $bundleselect_arr=array(); 
                            foreach($bundle_details as $bundle_values) {
                                if($bundle_values['bundle_sku'] !='') { 
                                    if(count($bundled_items) > 0) {     
                                         $bundle_product_id='';
                                         $bundle_product_id = Mage::getModel("catalog/product")->getIdBySku($bundle_values['bundle_sku']);  
                                         if($bundle_product_id!= '') {
                                           foreach($bundled_items as $opt_id => $bundle_pr_selection) { 
                                              if(!in_array($opt_id,$options_added_array)) {
                                                 $options_added_array[] = $opt_id; 
                                                 $bundleOptions[$j]['title'] = $option_array[$opt_id]->getData('default_title');
                                                 $bundleOptions[$j]['option_id'] =$opt_id;
                                                 $bundleOptions[$j]['delete'] ='';
                                                 $bundleOptions[$j]['type'] = $option_array[$opt_id]->getData('type');
                                                 $bundleOptions[$j]['required'] =$option_array[$opt_id]->getData('required');
                                                 $bundleOptions[$j]['position'] =$option_array[$opt_id]->getData('position');
                                                 $j++;
                                              }
                                              $product_already_there="no";
                                              foreach($bundle_pr_selection as $pr_id =>$bundle_selection) {
                                                  if($bundle_selection == $bundle_product_id && $option_array[$opt_id]->getData('default_title') !='Palettes') {
                                                      $product_already_there = "yes";
                                                       $bundleSelections[$opt_id][$s]['product_id'] = $bundle_product_id;
                                                       $bundleSelections[$opt_id][$s]['delete'] = ''; 
                                                       $bundleSelections[$opt_id][$s]['selection_price_value'] = $bundle_values['bundle_price']; 
                                                       $bundleSelections[$opt_id][$s]['selection_price_type'] = '0'; 
                                                       $bundleSelections[$opt_id][$s]['selection_qty'] = '1'; 
                                                       $bundleSelections[$opt_id][$s]['selection_id'] = $pr_id; 
                                                       $bundleSelections[$opt_id][$s]['position'] = '0'; 
                                                       $bundleSelections[$opt_id][$s]['is_default'] = '1'; 
                                                       $bundleSelections[$opt_id][$s]['option_id'] =  $opt_id;
                                                       $s++;
                                                       break;
                                                  }   
                                              } 
                                              if($product_already_there=="no" && $option_array[$opt_id]->getData('default_title') !='Palettes') {
                                                   $bundleSelections[$opt_id][$s]['product_id'] = $bundle_product_id;
                                                   $bundleSelections[$opt_id][$s]['delete'] = ''; 
                                                   $bundleSelections[$opt_id][$s]['selection_price_value'] = $bundle_values['bundle_price']; 
                                                   $bundleSelections[$opt_id][$s]['selection_price_type'] = '0'; 
                                                   $bundleSelections[$opt_id][$s]['selection_qty'] = '1'; 
                                                   $bundleSelections[$opt_id][$s]['position'] = '0'; 
                                                   $bundleSelections[$opt_id][$s]['is_default'] = '1'; 
                                                   $bundleSelections[$opt_id][$s]['option_id'] =  $opt_id;  
                                                   $s++;                                                   
                                               } 
                                          } // End of foreach
                                        }//End of if product id not blank
                                        else {
                                                $error_sku_names[$key] = " SKU ".$bundle_values['bundle_sku']."  Attribute =>".$bundle_values['bundle_attribute'] ." Not Found"; 
                                        }
                                     } 
                                    else { 
                                        /**
                                        * @desc code to add new bundle product option and selections   
                                        */
                                        $j=0;
                                             $cnt = $i+1;
                                             $bundleOptions[0]['title'] = $bundle_values['bundle_option_title'];
                                             $bundleOptions[0]['option_id'] ='';
                                             $bundleOptions[0]['delete'] ='';
                                             $bundleOptions[0]['type'] = 'multi';
                                             $bundleOptions[0]['required'] ='1';
                                             $bundleOptions[0]['position'] =1; 
                                            $bundle_product_id='';
                                            $bundle_product_id = Mage::getModel("catalog/product")->getIdBySku($bundle_values['bundle_sku']); 
                                            if($bundle_product_id!= '') {
                                                $bundleSelections[0][$k]['product_id'] = $bundle_product_id;
                                                $bundleSelections[0][$k]['delete'] = ''; 
                                                $bundleSelections[0][$k]['selection_price_value'] = 0;//$bundle_values['bundle_price']; 
                                                $bundleSelections[0][$k]['selection_price_type'] = '0'; 
                                                $bundleSelections[0][$k]['selection_qty'] = '1'; 
                                                $bundleSelections[0][$k]['position'] = '0'; 
                                                $bundleSelections[0][$k]['is_default'] = '1'; 
                                                $k++;
                                            }
                                            else {
                                                $error_sku_names[$key] = " SKU ".$bundle_values['bundle_sku']."  Attribute=>".$bundle_values['bundle_attribute'] ." Not Found"; 
                                            }    
                                    }// End of else part    
                                }  // End of of if bundle product sku not blank 
                                else {
                                      $error_sku_names[$key] = " SKU ".$bundle_values['bundle_sku']."  Attribute =>".$bundle_values['bundle_attribute'] ." Not Found";
                                }  
                            }//End if foreach loop  
                            
                            $bundleProduct->setCanSaveCustomOptions(true);
                            $bundleProduct->setCanSaveBundleSelections(true);
                            $bundleProduct->setAffectBundleProductSelections(true);
                            if(count($bundleSelections)>0) {   
                                $counter=0;
                                foreach($bundleSelections as $key =>$bundle_select) {
                                    foreach($bundle_select as $bundle_val_arr) {
                                         $bundleselect_arr[$counter][] = $bundle_val_arr;
                                    }  
                                    $counter++;  
                                }    
                                $bundleProduct->setBundleSelectionsData($bundleselect_arr);
                            }
                            if(count($bundleOptions)>0) {
                                $bundleProduct->setBundleOptionsData($bundleOptions);
                            }
                            //print_R($bundleselect_arr);
                            //print_R($error_sku_names);
                            //exit;  
                            try {
                                Mage::register('product', $bundleProduct);
                                $bundleProduct->save();
                                $bundleProduct->setData('price_type',1);
                                $bundleProduct->getResource()->saveAttribute($bundleProduct,'price_type');   
                                $bundleProduct->clearInstance();
                                Mage::unregister('product', $bundleProduct); 
                            }
                            catch (Exception $e) {
                                echo $e->getMessage();
                            }    
                            echo "dodffddfne";exit; 
                        }
                    }    
                    return $error_sku_names;
           }   
    }   
?>
