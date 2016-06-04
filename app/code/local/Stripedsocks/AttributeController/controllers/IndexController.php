<?php
class Stripedsocks_AttributeController_IndexController extends Mage_Adminhtml_Controller_Action
{  
    public function indexAction()
    {
        $this->loadLayout();
        if ($this->getRequest()->isPost()) {
			/*echo "<pre>";
			print_r($_POST);
			exit;*/
			if($_POST['insert']){$action="insert";}
			if($_POST['delete']){$action="delete";}
			if($_POST['update']){$action="update";}
			if($_POST['create_attributesets']){$action="create_attributesets";}
			switch($action) {
                case 'insert':
					$this->InsertAttributeOptions($_POST['insert']);
				    break;                 
                case 'delete':
					$this->DeleteAttributeOptions($_POST['delete']);
				    break;
				case 'create_attributesets':
					$this->CreateAttributeSets($_POST['attributesets']);
				    break;
				case 'create_attribute':
					$this->CreateAttribute($_POST['attribute']);
				    break;		
            }
            $this->_initLayoutMessages('core/session');
            $this->_setActiveMenu('import_menu')->renderLayout();  
        } else {
            $this->loadLayout()->_setActiveMenu('importmodule'); 
            $this->_setActiveMenu('import_menu')->renderLayout();  
        }
    }
	public function InsertAttributeOptions($data) {
			$myFile = Mage::getBaseDir('var').DS."import".DS."csv".DS.$data.".csv"; // File in which attribute option values are there.
			$f = fopen($myFile, "r");
			$myValue = array();
			
			$attribute_model = Mage::getModel('eav/entity_attribute');
			$attribute_code  = $attribute_model->getIdByCode('catalog_product',$data);
			$attribute       = $attribute_model->load($attribute_code);
			$attribute_options_model= Mage::getModel('eav/entity_attribute_source_table') ;
			$attribute_table        = $attribute_options_model->setAttribute($attribute);
			$options                = $attribute_options_model->getAllOptions(true);
			
			 $s=0;
			 $arr_label = array();
			 $arr_value = array();
			 foreach($options as $opt)
			 {
				if($opt['label']!="")
				{
					$arr_label[$opt['value']] = $opt['label'];
				}
				$s++;
			 }
			// echo "<pre>";
			// print_r($attribute_options_model);
			// exit;

			while($line = fgetcsv($f, filesize($myFile),","))
			{
				array_push($myValue,$line);
			}
			/* echo "<pre>";
			 print_r($myValue);
			 exit;*/
			$attribute_added = array();
			$attribute_added_update= array();
			for($i=0;$i<count($myValue);$i++)
			{
				if($myValue[$i])
			 	{
					$value['option'] = array($myValue[$i]);
					foreach($arr_label as $k=>$v)
					{
						if($v==$value['option'][0][0])
						{
							$op_id = $k;
						}
					}
					//echo "<br>".$op_id;	
					if(in_array($value['option'][0][0],$arr_label))
					{
						///echo "here";exit;

						if($value['option'][0][1] > 0 && !in_array($value['option'][0][0],$attribute_added_updated) && $value['option'][0][0]!='')
						{
							$option_updated[$op_id][0] = $value['option'][0][0];
							$option_updated[$op_id][3] = $value['option'][0][0];
							$order_updated[$op_id] = 	$value['option'][0][1];
							
							$result_updated = array('value' => $option_updated,'order' =>$order_updated );
							$attribute_added_updated[] = $option_updated[$op_id][0]; 
							
							$attribute->setData('option',$result_updated);
							/*echo "<pre>";
							print_r($attribute);
							exit;*/
							$attribute->save();	
							$message .= $value['option'][0][0].' Successfully Updated!.<br>';
						}
					}
					else
					{
						if($value['option'][0][1] > 0 && !in_array($value['option'][0][0],$attribute_added) && $value['option'][0][0]!='')
						{
							$option['option'][0] = $value['option'][0][0];
							$option['option'][3] = $value['option'][0][0];
							$order['option'] = 	$value['option'][0][1];
							$result = array('value' => $option,'order' =>$order );
							$attribute_added[] = $option['option'][0]; 
							$attribute->setData('option',$result); 
							/*echo "<pre>";
							print_r($attribute);
							exit;*/							
							$attribute->save();
							$message .= $value['option'][0][0].' Successfully Saved!.<br>';
							$session = Mage::getSingleton('adminhtml/session');
							Mage::app()->cleanCache(array(Mage_Core_Model_Translate::CACHE_TAG));
						    $session->setAttributeData(false);
						}
					}
			 	}
			}
		Mage::getSingleton('core/session')->addSuccess($message);	
	}
	public function DeleteAttributeOptions($data) {
			
			$attribute_code = $data;
			$attribute = Mage::getModel('eav/config')->getAttribute('catalog_product', $attribute_code);
			$options = $attribute->getSource()->getAllOptions();
			$optionsDelete = array();
			foreach($options as $option) {
				if ($option['value'] != "") {
					$optionsDelete['delete'][$option['value']] = true;
					$optionsDelete['value'][$option['value']] = true;
				}
			}
			$installer = new Mage_Eav_Model_Entity_Setup('core_setup');
			$installer->addAttributeOption($optionsDelete);
			$session = Mage::getSingleton('adminhtml/session');
			$session->addSuccess(Mage::helper('catalog')->__($attribute_code."options's deleted successfully!"));
			return true;
	}
	public function CreateAttributeSets($data) {

			$name = $data;
			$entityTypeId = Mage::getModel('catalog/product')->getResource()->getTypeId();
			$cloneSetId = 4; // Default Attribute set
			//make sure an attribute set with the same name doesn't already exist
			$model =Mage::getModel('eav/entity_attribute_set')
					->getCollection()
					->setEntityTypeFilter($entityTypeId)
					->addFieldToFilter('attribute_set_name',$name)
					->getFirstItem();
			//print_r($model);		
			//echo "adf";
			//exit;		
			if(!is_object($model)){
				$model = Mage::getModel('eav/entity_attribute_set');
			}
			else
			{
				echo "Error: ".Mage::helper('catalog')->__($name." Already Exist!. Try another name which is not exist!.");
			}
			if(!is_numeric($model->getAttributeSetId())){
				$new = true;
			}
			$model->setEntityTypeId($entityTypeId);
									
			$model->setAttributeSetName($name);
			$model->validate();
			$model->save();						
			//if the set is new use the Magento Default Attributeset as a skeleton template for the default attributes						
			if($new){
				$model->initFromSkeleton($cloneSetId)->save();
				$session = Mage::getSingleton('adminhtml/session');
				$session->addSuccess(Mage::helper('catalog')->__($name." Created successfully!"));
			}
			return true;
	}
	public function getCrudUrl($action, $type) {    
        return $this->getUrl('*/*/'.$action, array('type' => $type));
    }
}