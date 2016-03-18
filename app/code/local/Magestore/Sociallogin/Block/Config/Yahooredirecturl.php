<?php
class Magestore_Sociallogin_Block_Config_Yahooredirecturl
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
        $redirectUrl = str_replace('https://','http://',Mage::app()->getStore($storeId)->getUrl(''));	
        $domain = parse_url($redirectUrl);
        $referer = isset($domain['host']) ? $domain['scheme'].'://'.$domain['host'] : $redirectUrl;
        $html  = "<input readonly id='sociallogin_yahoologin_redirecturl' class='input-text' value='".$referer."'>";        
        return $html;
    }
        
}
