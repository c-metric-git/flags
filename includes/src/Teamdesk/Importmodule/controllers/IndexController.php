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
            ini_set("memory_limit","1024M");
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
					        ///echo "Below categories are not available in teamdesk but available in Stripedsocks so need to be delete..<pre>";
					        //print_r($res_compare_teamdesk_with_ss);
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
					        $message_compare_ss_category_teamdesk = "Below categories are not available in Stripedsocks but available in teamdesk..<br>";
					        $message_compare_ss_category_teamdesk .= $res_compare_ss_with_teamdesk;
					        //print_r($res_compare_ss_with_teamdesk);				
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
					        $message_compare_fl_category_teamdesk .= $res_compare_fl_with_teamdesk;
					        //print_r($res_compare_ss_with_teamdesk);				
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
					if(($_SESSION['team_cats'] == '') || count($_SESSION['team_cats']==0)) {
						$_SESSION['team_cats'] = $this->team_cats;
					}										
					$temdesk_fp_cats= array();					
					//echo '<pre>';
				//	print_r($_SESSION['team_cats']);
					//exit;
					foreach($_SESSION['team_cats'] as $team_cats_result)
					{						
						$temdesk_fp_cats [] = $team_cats_result['path'].'/'.$team_cats_result['name'].'/';					
					} 
					//echo 'teamdesk result....<pre>';
					//print_r($temdesk_fp_cats);
					//exit;              
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
					$rootCategoryId = 155;
					$storeId = 3;
					
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
					///echo "Below categories are not available in teamdesk but available in Facepaint so need to be delete..<pre>";
					//print_r($res_compare_teamdesk_with_fp);
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
					$message_compare_teamdesk_with_fp = "Matching not found for result of compare teamdesk with FP categories..";										
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
					$message_compare_fp_category_teamdesk = "Below categories are not available in Facepaint but available in teamdesk..<br>";
					$message_compare_fp_category_teamdesk .= $res_compare_fp_with_teamdesk;									
				}
				else
				{
					$message_compare_fp_category_teamdesk ="Matching not found for result of compare FP with teamdesk categories..";					
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
}