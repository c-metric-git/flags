<?php
class Teamdesk_Importmodule_IndexController extends Mage_Adminhtml_Controller_Action
{  
    public function indexAction()
    {
        $this->loadLayout();
        
        if ($this->getRequest()->isPost()) {
            $import_type = $_POST['import_type'];
            require_once('lib/Teamdesk/engine_config.php'); 
            require_once('lib/Teamdesk/engine_teamdesk_api.php');   
            set_time_limit(0);
            ini_set("memory_limit","512M");
            ini_set("display_errors",1);   
            switch($import_type) {
                case 'SS_categories' : 
				require_once('lib/Teamdesk/class.import_teamdesk_category.php'); 
                $objTDCategory = new TeamDeskCategory();  
                $strReturn = $objTDCategory->importTeamDeskCategories(); 
				    $strMessage .= $strReturn;     
                    try {
                        $this->openToImport("var/import/category/", "categories.csv");  
                        $i = 0;
                        while($csvData = $this->readCsvData()) { 
                            if ($i == 0) {
                                $this->addHeaderData($csvData);
                            } else {
                                $cat_success[] = $this->addContentData($csvData, 'add');
                            }
							$i++;
                        }
                        $this->closeFile('send');
                    } catch (Exception $e) {
                        $this->showError($e->getMessage());
                    }  
					$errormessage ='';
                    $errorcount=0;
                    $successmessage='';
                    $successcount=0;
                    if(count($cat_success) > 0) {
                        foreach($cat_success as $key => $cerror) {
                            if($cerror['error'] != '') {
                                $errormessage .= $this->__('<br />'.$cerror['error']);
                                $errorcount++;
                            }    
                            if($cerror['message'] != '') {
                                $successmessage .= $this->__('<br />'.$cerror['message']);
                                $successcount++;
                            }
                        }
                        if($errormessage !='') { 
                            Mage::getSingleton('core/session')->addError($errormessage); 
                        }    
                    }     
                    $message = $this->__('Categories have been imported successfully.');
                    $message .= $this->__('<br />Total categories successfully imported: '.$successcount);
                    $message .= $this->__('<br />Total categories errors: '.$errorcount."<br />");
                    Mage::getSingleton('core/session')->addSuccess($message);
					
					/**
                    * @desc code to sync the categories of TD and magento
                    */
                    /*if($_SESSION['team_cats'] == '') {
                        $_SESSION['team_cats'] = $this->team_cats;
                    }*/                                        
                    /*echo 'teamdesk result....<pre>';
                    print_r($temdesk_ss_cats);
                    exit;*/
                    $temdesk_ss_cats= array();  
                    if(count($this->team_cats) > 0) {                  
                        foreach($this->team_cats as $team_cats_result)
                        {                        
                            $temdesk_ss_cats [] = $team_cats_result['path'].'/'.$team_cats_result['name'].'/';                    
                        }
                        $ss_cats= array();
					    $rootCategoryId = 2;
					    $storeId = 1;
					    
                        $categories = Mage::getModel('catalog/category')
						    ->getCollection()
						    ->setStoreId($storeId)						
						    ->addAttributeToFilter('path', array('like' => "1/{$rootCategoryId}/%"))
						    ->addAttributeToSelect('*');
				
                        foreach($categories as $categorie)
                        {
						        $result = '';
						        $pathIds = $categorie->getPathIds();												
						        foreach ($pathIds as $cat)
						        {
							         $category = Mage::getModel('catalog/category')->load($cat);
    						         $result .= $category->getName().'/';
						        }
						        $res = explode("Root Catalog/",$result);
						        $ss_cats[]	= $res[1];
						        $catid = $categorie->getId();
						        $ss_cat_detail[$res[1]] = $catid;
					        
                        }
				        /**
                        * @desc code to check the magento categories in teamdesk array.
                        */
                        $res_compare_teamdesk_with_ss= array();					
				        foreach($ss_cats as $ss_cats_result)
				        {						
					        if(!in_array($ss_cats_result, $temdesk_ss_cats))
					        {						
						        if($ss_cats_result=="Default Category/Gift Card/")
						        {
							        
						        }
						        else
						        {
							        $res_compare_teamdesk_with_ss[] = $ss_cats_result;
						        }
					        }
					    }
				        /**
                        * @desc code to delete the categories from magento which are not there in TD
                        */
                        if($res_compare_teamdesk_with_ss && count($res_compare_teamdesk_with_ss)>0)
				        {
					       $message_compare_teamdesk_with_ss .= "Below categories are not available in teamdesk but available in SS is/are going to be delete..<br>";
					        foreach($ss_cat_detail as $key=>$val)
							        {
								        if(in_array($key,$res_compare_teamdesk_with_ss))
								        {	
									        //echo $key." ".$val."<br>";
								           try{
											        Mage::getModel("catalog/category")->load($val)->delete();
											        $message_compare_teamdesk_with_ss .= "Category id $val delted successfully..";
											        
									        }catch(Exception $e)
									        { __error($e);
									        
									        }	
								        }
							        }											
				        }
				        else
				        {
					        $message_compare_teamdesk_with_ss = "No more categories to delete from magento. All SS categories are synced to TD.";										
				        }
				        Mage::getSingleton('core/session')->addSuccess($message_compare_teamdesk_with_ss);
				        $res_compare_ss_with_teamdesk= array();		
                        /**
                        * @desc code to check the Teamdesk categories into magento array. 			
                        */
				        foreach($temdesk_ss_cats as $temdesk_ss_cats_result)
				        {						
					        if(!in_array($temdesk_ss_cats_result,$ss_cats))
					        {						
						        $res_compare_ss_with_teamdesk[] = $temdesk_ss_cats_result;
					        }
					        
				        }
				        if($res_compare_ss_with_teamdesk && count($res_compare_ss_with_teamdesk)>0)
				        {
							$message_compare_ss_category_teamdesk = "Below categories are not available in SS but available in teamdesk..<br>";
							foreach($res_compare_ss_with_teamdesk as $key=>$val)
							{						
								$message_compare_ss_category_teamdesk .= $val.'<br>';					
							}					        
				        }
				        else
				        {
					        $message_compare_ss_category_teamdesk ="No more categories to remove from TD. All SS categories are synced to magento.";					
				        }
                        Mage::getSingleton('core/session')->addSuccess($message_compare_ss_category_teamdesk);
                    }   
                    break;                 
                 case 'FL_categories' :
				  require_once('lib/Teamdesk/class.FL_import_teamdesk_category.php'); 
                    $objTDCategory = new FLTeamDeskCategory();  
                    $strReturn = $objTDCategory->importTeamDeskCategories();    
                    $strMessage .= $strReturn;     
                    try {
                        $this->openToImport("var/import/category/", "FL_categories.csv");  
                        $i = 0;
                        while($csvData = $this->readCsvData()) { 
                            if ($i == 0) {
                                $this->addHeaderData($csvData);
                            } else {      
                                $cat_success[] = $this->addContentData($csvData, 'add');
                            }
                            if($i % 50 == 0) {
                                echo "<br />50 categories processed"; 
                            }
                            $i++;
                        }
                        $this->closeFile('send');
                    } catch (Exception $e) {
                        $this->showError($e->getMessage());
                    }
					$errormessage ='';
                    $errorcount=0;
                    $successmessage='';
                    $successcount=0;
                    if(count($cat_success) > 0) {
                        foreach($cat_success as $key => $cerror) {
                            if($cerror['error'] != '') {
                                $errormessage .= $this->__('<br />'.$cerror['error']);
                                $errorcount++;
                            }    
                            if($cerror['message'] != '') {
                                $successmessage .= $this->__('<br />'.$cerror['message']);
                                $successcount++;
                            }
                        }
                        if($errormessage !='') { 
                            Mage::getSingleton('core/session')->addError($errormessage); 
                        }    
                    } 
                    $message = $this->__('Categories have been imported successfully.');
                    $message .= $this->__('<br />Total categories successfully imported: '.$successcount);
                    $message .= $this->__('<br />Total categories errors: '.$errorcount."<br />");  
                    Mage::getSingleton('core/session')->addSuccess($message);        
					/**
                    * @desc code to sync the categories of TD and magento
                    */
                    /*if(($_SESSION['team_cats'] == '') || count($_SESSION['team_cats']==0)) {
                        $_SESSION['team_cats'] = $this->team_cats;
                    }*/                                        
                    $temdesk_fl_cats= array(); 
                    if(count($this->team_cats) > 0) {                    
                        //echo '<pre>';
                        // print_r($_SESSION['team_cats']);
                        //exit;
                        foreach($this->team_cats as $team_cats_result)
                        {                        
                            $temdesk_fl_cats [] = $team_cats_result['path'].'/'.$team_cats_result['name'].'/';                    
                        } 
                        //echo 'teamdesk result....<pre>';
                        //print_r($temdesk_fl_cats);
                        //exit;               
                        $fl_cats= array();
					    $rootCategoryId = 146;
					    $storeId = 2;
					    
					    $categories = Mage::getModel('catalog/category')
						    ->getCollection()
						    ->setStoreId($storeId)						
						    ->addAttributeToFilter('path', array('like' => "1/{$rootCategoryId}/%"))
						    ->addAttributeToSelect('*');
						    
				        foreach($categories as $categorie)
                        {
						        $result = '';
						        $pathIds = $categorie->getPathIds();												
						        foreach ($pathIds as $cat)
						        {
							         $category = Mage::getModel('catalog/category')->load($cat);
    						         $result .= $category->getName().'/';							 
							         
						        }
						        //echo "<pre>";
						        //print_r($result);
	    				        //exit;
						        $res = explode("Root Catalog/",$result);
						        $fl_cats[]	= $res[1];
						        $catid = $categorie->getId();
						        $fl_cat_detail[$res[1]] = $catid;					
                        }
                        /**
                        * @desc code to check the magento categories in teamdesk array.
                        */
				        $res_compare_teamdesk_with_fl= array();					
				        foreach($fl_cats as $fl_cats_result)
				        {						
					        if(!in_array($fl_cats_result, $temdesk_fl_cats))
					        {						
						        if($fl_cats_result=="Flagsrus/Gift Card/")
						        {
							        
						        }
						        else
						        {
							        $res_compare_teamdesk_with_fl[] = $fl_cats_result;
						        }
					        }
					    }
                        /**
                        * @desc code to delete the categories from magento which are not there in TD
                        */
				        if($res_compare_teamdesk_with_fl && count($res_compare_teamdesk_with_fl)>0)
				        {
						$message_compare_teamdesk_with_fl .= "Below categories are not available in teamdesk but available in FF is/are going to be delete..<br>";
					        foreach($fl_cat_detail as $key=>$val)
							        {
								        if(in_array($key,$res_compare_teamdesk_with_fl))
								        {	
									        //echo $key." ".$val."<br>";
								           try{
											        Mage::getModel("catalog/category")->load($val)->delete();
											        $message_compare_teamdesk_with_fl .= "Category id $val deleted successfully..";
											        
									        }catch(Exception $e)
									        { __error($e);
									        
									        }	
								        }
							        }											
				        }
				        else
				        {
					        $message_compare_teamdesk_with_fl = "No more categories to delete from magento. All FL categories are synced to TD.";										
				        }
				        Mage::getSingleton('core/session')->addSuccess($message_compare_teamdesk_with_fl);
				        /**
                        * @desc code to check the Teamdesk categories into magento array.             
                        */
                        $res_compare_fl_with_teamdesk= array();					
				        foreach($temdesk_fl_cats as $temdesk_fl_cats_result)
				        {						
					        if(!in_array($temdesk_fl_cats_result,$fl_cats))
					        {						
						        $res_compare_fl_with_teamdesk[] = $temdesk_fl_cats_result;
					        }
					        
				        }
				        if($res_compare_fl_with_teamdesk)
				        {
					        $message_compare_fl_category_teamdesk = "Below categories are not available in Flagsrus but available in teamdesk..<br>";
							foreach($res_compare_fl_with_teamdesk as $key=>$val)
								{						
									$message_compare_fl_category_teamdesk .= $val.'<br>';					
								}				
				        }
				        else
				        {
					        $message_compare_fl_category_teamdesk ="No more categories to remove from TD. All Flagsrus categories are synced to magento..";					
				        }
				        Mage::getSingleton('core/session')->addSuccess($message_compare_fl_category_teamdesk);
                    }       
                    break;
					/*ps start*/
				case 'FP_categories' :   
				   //echo "here";exit;
                    require_once('lib/Teamdesk/class.FP_import_teamdesk_category.php'); 
                    $objTDCategory = new FPTeamDeskCategory();  
                    $strReturn = $objTDCategory->importTeamDeskCategories();     
                    $strMessage .= $strReturn;         
                    
					try {
                        $this->openToImport("var/import/category/", "FP_categories.csv");  
                        $i = 0;
                        while($csvData = $this->readCsvData()) { 
                            if ($i == 0) {
                                $this->addHeaderData($csvData);
                            } else {
                                $cat_success[] = $this->addContentData($csvData, 'add');
                            }
                            $i++;
                        }
                        $this->closeFile('send');
                    } catch (Exception $e) {
                        $this->showError($e->getMessage());
                    }
					$errormessage ='';
                    $errorcount=0;
                    $successmessage='';
                    $successcount=0;
                    if(count($cat_success) > 0) {
                        foreach($cat_success as $key => $cerror) {
                            if($cerror['error'] != '') {
                                $errormessage .= $this->__('<br />'.$cerror['error']);
                                $errorcount++;
                            }    
                            if($cerror['message'] != '') {
                                $successmessage .= $this->__('<br />'.$cerror['message']);
                                $successcount++;
                            }
                        }
                        if($errormessage !='') { 
                            Mage::getSingleton('core/session')->addError($errormessage); 
                        }    
                    }     
                    $message = $this->__('Categories have been imported successfully.');
                    $message .= $this->__('<br />Total categories successfully imported: '.$successcount);
                    $message .= $this->__('<br />Total categories errors: '.$errorcount."<br />");
                    Mage::getSingleton('core/session')->addSuccess($message);
                    $fl_cats= array();
					$rootCategoryId = 7648;
					$storeId = 3;
					/*if(($_SESSION['team_cats'] == '') || count($_SESSION['team_cats']==0)) {
                        $_SESSION['team_cats'] = $this->team_cats;
                    }*/                                        
                    $temdesk_fp_cats= array();                    
                    //echo '<pre>';
                //    print_r($_SESSION['team_cats']);
                    //exit;
                    foreach($this->team_cats as $team_cats_result)
                    {                        
                        $temdesk_fp_cats [] = $team_cats_result['path'].'/'.$team_cats_result['name'].'/';                    
                    } 
                    //echo 'teamdesk result....<pre>';
                    //print_r($temdesk_fp_cats);
                    //exit; 
					$categories = Mage::getModel('catalog/category')
						->getCollection()
						->setStoreId($storeId)						
						->addAttributeToFilter('path', array('like' => "1/{$rootCategoryId}/%"))
						->addAttributeToSelect('*');
						
				foreach($categories as $categorie)
                {
						$result = '';
						$pathIds = $categorie->getPathIds();												
						foreach ($pathIds as $cat)
						{
							 $category = Mage::getModel('catalog/category')->load($cat);
    						 $result .= $category->getName().'/';							 
							 
						}
						//echo "<pre>";
						//print_r($result);
					//	exit;
						$res = explode("Root Catalog/",$result);
						$fp_cats[]	= $res[1];
						$catid = $categorie->getId();
						$fp_cat_detail[$res[1]] = $catid;
						//exit;
					
                }
				$res_compare_teamdesk_with_fp= array();					
				foreach($fp_cats as $fp_cats_result)
				{						
					if(!in_array($fp_cats_result, $temdesk_fp_cats))
					{						
						if($fp_cats_result=="Facepaint/Gift Card/")
						{							

						}
						else
						{
							$res_compare_teamdesk_with_fp[] = $fp_cats_result;
						}
					}
					
					
				}
				
				if($res_compare_teamdesk_with_fp)
				{
					$message_compare_teamdesk_with_fp .= "Below categories are not available in teamdesk but available in Facepaint is/are going to be delete..<br>";
					foreach($fp_cat_detail as $key=>$val)
							{
								if(in_array($key,$res_compare_teamdesk_with_fp))
								{	
									//echo $key." ".$val."<br>";
								   try{
											Mage::getModel("catalog/category")->load($val)->delete();
											$message_compare_teamdesk_with_fp .= "Category id $val deleted successfully..";
											
									}catch(Exception $e)
									{ __error($e);
									
									}	
								}
								
							}											
				}
				else
				{
					$message_compare_teamdesk_with_ss = "No more categories to delete from magento. All FP categories are synced to TD.";																
				}
				Mage::getSingleton('core/session')->addSuccess($message_compare_teamdesk_with_fp);
				$res_compare_fp_with_teamdesk= array();					
				foreach($temdesk_fp_cats as $temdesk_fp_cats_result)
				{						
					if(!in_array($temdesk_fp_cats_result,$fp_cats))
					{						
						$res_compare_fp_with_teamdesk[] = $temdesk_fp_cats_result;
					}
					
				}
				if($res_compare_fp_with_teamdesk)
				{
					$message_compare_fp_category_teamdesk = "Below categories are not available in FP but available in teamdesk..<br>";
					foreach($res_compare_fp_with_teamdesk as $key=>$val)
					{						
						$message_compare_fp_category_teamdesk .= $val.'<br>';					
					}					
				}
				else
				{
					$message_compare_teamdesk_with_fp = "No more categories to remove from TD. All Flagsrus categories are synced to magento..";					
				}
				Mage::getSingleton('core/session')->addSuccess($message_compare_fp_category_teamdesk);				
			    break;   

				/* ps end*/	      
                 case 'SS_products' :    
                    require_once('lib/Teamdesk/class.import_teamdesk_webprofiles.php'); 
                    $objTDProducts = new TeamDeskWebprofiles(); 
                    $strReturn = $objTDProducts->importTeamdeskProduct();     
                    $strMessage = "Product Import Status";
                    $strMessage .= $strReturn;              
                    $succ_message ='';      
                    $product_csv_counter = $_SESSION['product_csv_counter']!=''?$_SESSION['product_csv_counter']:0; 
                    for($i=0;$i<=$product_csv_counter;$i++) {
                        $succ_message['error'] .= "<br /><br />Processing File => SS_Products$i.csv<br />";
                        $succ_message['success'] .= "<br /><br />Processing File => SS_Products$i.csv<br />";
                        $tmp_succ_message = $this->AddProduct("var/import/products/SS_Products$i.csv");
                        if($tmp_succ_message['success']!='') {
                             $succ_message['success'] .= $tmp_succ_message['success'];
                        } 
                        if($tmp_succ_message['error']!='') {
                             $succ_message['error'] .= $tmp_succ_message['error'];
                        }   
                    }              
                    unset($_SESSION['product_csv_counter']);
                    if($succ_message['success']!='') { 
                        Mage::getSingleton('core/session')->addSuccess($succ_message['success']);
                    }
                    if($succ_message['error']) {
                       Mage::getSingleton('core/session')->addError($succ_message['error']); 
                    }   
                    /* @var $indexCollection Mage_Index_Model_Resource_Process_Collection */
                    //$indexCollection = Mage::getModel('index/process')->getCollection();
                    //foreach ($indexCollection as $index) {
                        /* @var $index Mage_Index_Model_Process */
                      //  $index->reindexAll();
                    //}
                    break;  
                case 'FL_products' :
                     /**
                     * @desc code to clear the session for importing FL products
                     */
					 $clearsession = $_POST['fl_product_session'];
					 foreach($clearsession as $key=> $clearsession_result)
					 {
						 unset($_SESSION["$clearsession_result"]);					
					 }   
					ini_set("display_errors",1);
                    require_once('lib/Teamdesk/class.FL_import_teamdesk_webprofiles.php'); 
                    $objTDProducts = new FLTeamDeskWebprofiles(); 
                    $strReturn = $objTDProducts->importTeamdeskProduct();       
                    $strMessage = "Product Import Status"; 
                    if($strReturn !='') {
                        $strMessage .= $strReturn; 
                    } 
                    $succ_message ='';       
                    $product_csv_counter = $_SESSION['product_csv_counter']!=''?$_SESSION['product_csv_counter']:0;   
                    $counter_loop = 0; 
                    for($i=0;$i<=$product_csv_counter;$i++) {                   
                        $succ_message['error'] .= "<br /><br />Processing File => FL_Products$i.csv<br />";   
                        $succ_message['success'] .= "<br /><br />Processing File => FL_Products$i.csv<br />";    
                        $tmp_succ_message = $this->AddProduct("var/import/products/FL_Products$i.csv");
                        echo '<pre>';
                        print_R($tmp_succ_message);
                        if($tmp_succ_message['success']!='') {
                             $succ_message['success'] .= $tmp_succ_message['success'];
                        } 
                        if($tmp_succ_message['error']!='') {
                             $succ_message['error'] .= $tmp_succ_message['error'];
                        } 
                    }
                    //unset($_SESSION['product_csv_counter']);
                    if($succ_message['success']!='') { 
                        Mage::getSingleton('core/session')->addSuccess($succ_message['success']);
                    }
                    if($succ_message['error']) {
                       Mage::getSingleton('core/session')->addError($succ_message['error']); 
                    }   
                    /* @var $indexCollection Mage_Index_Model_Resource_Process_Collection */
                    //$indexCollection = Mage::getModel('index/process')->getCollection();
                   // foreach ($indexCollection as $index) { 
                        /* @var $index Mage_Index_Model_Process */
                     //   $index->reindexAll();
                    //}        
                    break;
					case 'FP_products' :
					 /**
                     * @desc code to clear the session for importing FL products
                     */
					 $clearsession = $_POST['fp_product_session'];
					 foreach($clearsession as $key=> $clearsession_result)
					 {    
						 unset($_SESSION["$clearsession_result"]);					
					 }   
					ini_set("display_errors",1);
                    /*require_once('lib/Teamdesk/class.FP_import_teamdesk_webprofiles.php'); 
                    $objTDProducts = new FPTeamDeskWebprofiles(); 
                    $strReturn = $objTDProducts->importTeamdeskProduct();    
                    $strMessage = "Product Import Status";                   
                    if($strReturn !='') {
                        $strMessage .= $strReturn; 
                    }     
                    $succ_message ='';        
                    $product_csv_counter = $_SESSION['product_csv_counter']!=''?$_SESSION['product_csv_counter']:0;  
                    $counter_loop = 0;
                    for($i=0;$i<=$product_csv_counter;$i++) {                   
                        $succ_message['error'] .= "<br /><br />Processing File => FP_Products$i.csv<br />";   
                        $succ_message['success'] .= "<br /><br />Processing File => FP_Products$i.csv<br />";    
                        $tmp_succ_message = $this->AddProduct("var/import/products/FP_Products$i.csv");        
                        echo '<pre>';
                        print_R($tmp_succ_message);
                        if($tmp_succ_message['success']!='') {
                             $succ_message['success'] .= $tmp_succ_message['success'];
                        } 
                        if($tmp_succ_message['error']!='') {
                             $succ_message['error'] .= $tmp_succ_message['error'];
                        }            
                    }*/                                                          
                    //unset($_SESSION['product_csv_counter']);
                    /**
                    * @desc code to update the bundle products data 
                    */
                    $tmp_succ_message = $this->UpdateBundleProductData("var/import/products/FP_BundleProducts.csv"); 
                    if(count($tmp_succ_message) > 0) {
                        foreach($tmp_succ_message as $key=> $temp_error ) {
                            $succ_message['error'] .= "<br />Error in Bundle Product $key => ".$temp_error;
                        }    
                    } 
                    if($succ_message['success']!='') { 
                        Mage::getSingleton('core/session')->addSuccess($succ_message['success']);
                    }
                    if($succ_message['error']) {
                       Mage::getSingleton('core/session')->addError($succ_message['error']); 
                    }  
                    /* @var $indexCollection Mage_Index_Model_Resource_Process_Collection */
                    //$indexCollection = Mage::getModel('index/process')->getCollection();
                   // foreach ($indexCollection as $index) { 
                        /* @var $index Mage_Index_Model_Process */
                     //   $index->reindexAll();
                    //}        
                    break;
            }
            $this->_initLayoutMessages('core/session');
            $this->_setActiveMenu('import_menu')->renderLayout();  
        } else {
            $this->loadLayout()->_setActiveMenu('importmodule'); 
            $this->_setActiveMenu('import_menu')->renderLayout();  
        }
    }

    public function getImportUrl($action, $type) {    
        return $this->getUrl('*/*/'.$action, array('type' => $type));
    }

    public function showError($text, $id = '') {
        echo '<li style="background-color:#FDD; " id="'.$id.'">';
        echo '<img src="'.Mage::getDesign()->getSkinUrl('images/error_msg_icon.gif').'" class="v-middle"/>';
        echo $text;
        echo "</li>";
    }

    public function showWarning($text, $id = '') {
        echo '<li id="'.$id.'" style="background-color:#FFD;">';
        echo '<img src="'.Mage::getDesign()->getSkinUrl('images/fam_bullet_error.gif').'" class="v-middle" style="margin-right:5px"/>';
        echo $text;
        echo '</li>';
    }

    public function showNote($text, $id = '', $style = '') {
        echo '<li id="'.$id.'" style="'.$style.'">';
        echo '<img src="'.Mage::getDesign()->getSkinUrl('images/note_msg_icon.gif').'" class="v-middle" style="margin-right:5px"/>';
        echo $text;
        echo '</li>';
    }

    public function showSuccess($text, $id = '', $style = '') {
        echo '<li id="'.$id.'" style="background-color:#DDF; '.$style.'">';
        echo '<img src="'.Mage::getDesign()->getSkinUrl('images/fam_bullet_success.gif').'" class="v-middle" style="margin-right:5px"/>';
        echo $text;
        echo '</li>';
    }

    public function showProgress($text, $id = '', $style = '') {
        echo '<li id="'.$id.'" style="background-color:#DDF; '.$style.'">';
        echo '<img id="'.$id.'_img" src="'.Mage::getDesign()->getSkinUrl('images/ajax-loader.gif').'" class="v-middle" style="margin-right:5px"/>';
        echo '<span id="'.$id.'_text">'.$text.'<span>';
        echo '</li>';
    }
    protected $_resource;
    public function openToImport($path, $file) {
        $baseDir = Mage::getBaseDir();
        $this->_resource = new Varien_Io_File();
        $filepath = $this->_resource->getCleanPath($baseDir . DS . trim($path, DS));
        $realPath = realpath($filepath);

        if ($realPath === false) {
            $message = $this->__('The destination folder "%s" does not exist.', $path);
            Mage::throwException($message);
        }
        elseif (!is_dir($realPath)) {
            $message = $this->__('Destination folder "%s" is not a directory.', $realPath);
            Mage::throwException($message);
        }
        else {
            $filepath = rtrim($realPath, DS);
        }
        try {
            $this->_resource->open(array('path' => $filepath));
            $this->_resource->streamOpen($file, 'r+');
        } catch (Exception $e) {
            $message = $this->__('An error occurred while opening file: "%s".', $e->getMessage());
            Mage::throwException($message);
        }
    }

    public function readCsvData() {
        return $this->_resource->streamReadCsv();
    }

    public function closeFile($opt = '') {
        $this->_resource->streamClose();
    }

    protected $_csvHeader = array();
    public function addHeaderData($data) {
        foreach ($data as $val) {
            $this->_csvHeader[] = $this->getHeaderField($val);
        }
    }

    protected $_count = 0;
	public $team_cats= array();
    public function addContentData($data, $opt = 'send') {
        $this->_count++;

        $contentData = array('count' => $this->_count);
		
        foreach($data as $key => $val) {
            if (isset($this->_csvHeader[$key])) $contentData[$this->_csvHeader[$key]] = $val;
        }
		$this->team_cats[] = $contentData;
        $category_success = $this->AddCategory($contentData);
        return $category_success;
    }
    protected $_headerCodes;
    public function getHeaderField($header) {
        if (is_null($this->_headerCodes)) {
            $this->_headerCodes = array();
            $attributes = Mage::helper('mageworks_core/category')->getAttributes();
            foreach($attributes as $attribute) {
                $this->_headerCodes[$attribute['label']] = $attribute['field'];
            }
        }
        if (isset($this->_headerCodes[$header])) return $this->_headerCodes[$header];
        else {
            Mage::throwException($this->__('The header field %s is not a valid one.', $header));
        }
    }
   public function AddCategory($data) {                 
        if (count($data)>0) {  
            $count = $data['count'];
            $result['count'] = $count;
            $error = '';
            try {         
                $attributes = Mage::helper('mageworks_core/category')->getAttributes();      
                $helper = Mage::helper('importmodule/category');
                $helper->_pathids = array(1); 
                $category = Mage::getModel('catalog/category');
                $categoryPath = $data['path'];
                $categoryName = $data['name'];                                            
                $catid = $helper->getCategoryIdFromPath($categoryPath, $categoryName);  
                if (is_array($catid)) {   
                    $error = Mage::helper('mageworks_import')->__('Path provided is not a valid one. Category path: '.$categoryPath.', Category Name:'.$categoryName);
                } else {
                    if ($catid > 0) $category->load($catid);
                    foreach($attributes as $attribute) {
                        $fieldvalue = $data[$attribute['field']];
                        if (isset($attribute['function'])) {
                            $fieldvalue = $helper->$attribute['function']($fieldvalue);
                        }
                        if (isset($attribute['required'])) {
                            if (strtolower($attribute['required']) == 'yes') {
                                if (!$fieldvalue) {
                                    $error = Mage::helper('mageworks_import')->__('Please provide the value for "%s" Category path: '.$categoryPath.', Category Name:'.$categoryName, $attribute['field']);
                                    break;
                                }
                            }
                        }
                        if (isset($attribute['importfn'])) {
                            $category->$attribute['importfn']($attribute['field'], $fieldvalue);
                        } else {
                            $category->setData($attribute['field'], $fieldvalue);
                        }
                    }
                }

                if ($error) {
                    echo "<br />".$result['error'] = $error;
                } else {
                    $category->save();
                    $result['message'] = Mage::helper('mageworks_import')->__('Imported the category "%s" successfully.', $category->getName());
                }
            } catch (Exception $e) {
                $result['error'] = $e->getMessage();
            }
        } else { 
            $result['error'] = Mage::helper('mageworks_import')->__('Invalid request');
        }  
        return $result;
        //$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    } 
    public function AddProduct($data) 
    {
        if ($data) {
            $this->loadLayout(false);
            // common actions
            try {
                /** @var $import Mage_ImportExport_Model_Import */
                $import = Mage::getModel('importmodule/import');  
                $validationResult = $import->validateSource($data);       
                $result='';
                if(count($validationResult) > 0) {
                    foreach($validationResult as $validresult) {   
                        $result['error'] .= $validresult."<br />";
                    }    
                } 
                $importModel = Mage::getModel('importexport/import');
                $importModel->behaviour = 'replace';           
                $importModel->importSource();  
                $importModel->invalidateIndex(); 
                $result['success'] = 'Products Imported Successfully';  
            } catch (Exception $e) {
                $result['error']  .= ($this->__('Please fix errors and re-upload file '.$e->getMessage()));   
            }
            return $result; 
        } else {
            $result['error'] = ($this->__('Data is invalid or file is not uploaded'));
            $this->_redirect('*/*/index');
        }
    }
    public function UpdateBundleProductData($csvFile) {
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
                        $num_of_options = $bundle_details[0]['num_of_options_for_kit'];
                        $required_sku_for_kit = $bundle_details[0]['requiredskuforkit'];
                        $bundleProduct = Mage::getModel('catalog/product')->loadByAttribute('sku',$key);  
                        $main_product_id = $bundleProduct->entity_id;  
                        $bundleProduct->setIsSuperMode(true);
                        $bundleProduct->setData('_edit_mode', true);
                        $bundleProduct->setData('price_type', 1);   
                        $bundleProduct->setData('price', $bundle_details[0]['price']);   
                        //$bundleProduct->setPriceType(1); //price type (0 - dynamic, 1 - fixed)   
                        //$bundleProduct->setPrice($bundle_details[0]['price']);   
                         /*Mage::register('product', $bundleProduct);
                         $bundleProduct->save();
                         echo "done";
                         exit;    
                         */         
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
                                                       $bundleSelections[$opt_id][$s]['selection_price_type'] = '1'; 
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
                                                   $bundleSelections[$opt_id][$s]['selection_price_type'] = '1'; 
                                                   $bundleSelections[$opt_id][$s]['selection_qty'] = '1'; 
                                                   $bundleSelections[$opt_id][$s]['position'] = '0'; 
                                                   $bundleSelections[$opt_id][$s]['is_default'] = '1'; 
                                                   $bundleSelections[$opt_id][$s]['option_id'] =  $opt_id;  
                                                   $s++;                                                   
                                               } 
                                               if($option_array[$opt_id]->getData('default_title') =='Palettes') {
                                                   $bundle_selection_id = $pr_id;
                                                   $bundle_option_id = $opt_id;   
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
                                        for($i=0;$i<$num_of_options;$i++) {    
                                             $cnt = $i+1;
                                             $bundleOptions[$i]['title'] = $bundle_values['bundle_option_title']=='fp_color'?"Color $cnt":$bundle_values['bundle_option_title'];
                                             $bundleOptions[$i]['option_id'] ='';
                                             $bundleOptions[$i]['delete'] ='';
                                             $bundleOptions[$i]['type'] = 'select';
                                             $bundleOptions[$i]['required'] ='1';
                                             $bundleOptions[$i]['position'] =$cnt; 
                                        } 
                                            $bundle_product_id='';
                                            $bundle_product_id = Mage::getModel("catalog/product")->getIdBySku($bundle_values['bundle_sku']); 
                                            if($bundle_product_id!= '') {
                                                $bundleSelections[0][$k]['product_id'] = $bundle_product_id;
                                                $bundleSelections[0][$k]['delete'] = ''; 
                                                $bundleSelections[0][$k]['selection_price_value'] = $bundle_values['bundle_price']; 
                                                $bundleSelections[0][$k]['selection_price_type'] = '1'; 
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
                            //setting the bundle options and selection data
                            if(count($children)==0) {    
                                for($i=0;$i<$num_of_options;$i++) {
                                    $bundleUpdatedSelections[$i] = $bundleSelections[0];
                                }
                                if(count($bundleUpdatedSelections)>0) {
                                    if($required_sku_for_kit !='') {
                                        $bundleOptions[$num_of_options]['title'] = "Palettes";
                                        $bundleOptions[$num_of_options]['option_id'] ='';
                                        $bundleOptions[$num_of_options]['delete'] ='';
                                        $bundleOptions[$num_of_options]['type'] = 'select';
                                        $bundleOptions[$num_of_options]['required'] ='1';
                                        $bundleOptions[$num_of_options]['position'] =$cnt+1; 
                                                 
                                        $bundle_product_id = Mage::getModel("catalog/product")->getIdBySku($required_sku_for_kit); 
                                        $bundleUpdatedSelections[$num_of_options][0]['product_id'] = $bundle_product_id;
                                        $bundleUpdatedSelections[$num_of_options][0]['delete'] = ''; 
                                        $bundleUpdatedSelections[$num_of_options][0]['selection_price_value'] = "0"; 
                                        $bundleUpdatedSelections[$num_of_options][0]['selection_price_type'] = "1"; 
                                        $bundleUpdatedSelections[$num_of_options][0]['selection_qty'] = '1'; 
                                        $bundleUpdatedSelections[$num_of_options][0]['position'] = '0'; 
                                        $bundleUpdatedSelections[$num_of_options][0]['is_default'] = '1'; 
                                        $bundleProduct->setBundleSelectionsData($bundleUpdatedSelections);
                                    }    
                                }
                            }    
                            else {    
                                $bundle_product_id = Mage::getModel("catalog/product")->getIdBySku($required_sku_for_kit); 
                                $bundleSelections[$bundle_option_id][0]['product_id'] = $bundle_product_id;
                                $bundleSelections[$bundle_option_id][0]['delete'] = '';    
                                $bundleSelections[$bundle_option_id][0]['selection_price_value'] = "0"; 
                                $bundleSelections[$bundle_option_id][0]['selection_price_type'] = '1'; 
                                $bundleSelections[$bundle_option_id][0]['selection_qty'] = '1'; 
                                $bundleSelections[$bundle_option_id][0]['selection_id'] = $bundle_selection_id; 
                                $bundleSelections[$bundle_option_id][0]['position'] = '0'; 
                                $bundleSelections[$bundle_option_id][0]['is_default'] = '1'; 
                                $bundleSelections[$bundle_option_id][0]['option_id'] =  $bundle_option_id;  
                               
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
                            }    
                            if(count($bundleOptions)>0) {
                                $bundleProduct->setBundleOptionsData($bundleOptions);
                            }
                            //print_R($bundleselect_arr);
                            //print_R($error_sku_names);
                            //exit;  
                            Mage::register('product', $bundleProduct);
                            $bundleProduct->save();
                            //echo "done";
                            Mage::unregister('product', $bundleProduct); 
                            $bundleProduct->clearInstance();
  
                            //exit;
                               
                        }
                       // }       
                    }    
                    return $error_sku_names;
           }   
    }   
}