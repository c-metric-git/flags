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
                    break;   
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
    public function addContentData($data, $opt = 'send') {
        $this->_count++;

        $contentData = array('count' => $this->_count);
        foreach($data as $key => $val) {
            if (isset($this->_csvHeader[$key])) $contentData[$this->_csvHeader[$key]] = $val;
        }
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