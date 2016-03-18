<?php
class Magestore_Sociallogin_Block_Config_Instagramredirecturl
	extends Mage_Adminhtml_Block_System_Config_Form_Field
{    
     protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
		$storeId = $this->getRequest()->getParam('store');
                if(!$storeId){
			$stores=Mage::app()->getStores(false);
			foreach($stores as $store => $value){
				if($value->getStoreId()){
					$storeId = $value->getStoreId();
					Break;
				}
			}
		}
        $redirectUrl = Mage::app()->getStore($storeId)->getUrl('sociallogin/instagramlogin/login/',array('_secure'=>true));	
		$array=parse_url($redirectUrl);
		if(isset($array['query']) && $array['query'])
		$redirectUrl=str_replace('?'.$array['query'],'',$redirectUrl);
        $html  = "<input readonly id='sociallogin_instagramlogin_redirecturl' class='input-text' value='".$redirectUrl."'>";        
        return $html;
    }
        
}
