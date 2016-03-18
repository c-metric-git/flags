<?php
 /**
 * Magespot
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magespot License.
 * It is available through the world-wide-web at this URL:
 * http://magespot.com/license.html
 * If you need receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to contacts@magespot.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension to newer
 * versions in the future. If you wish to customize the extension for your
 * needs please refer to http://www.magespot.com/ for more information.
 *
 * @category   Magespot
 * @package    Magespot_Base
 * @copyright  Copyright (c) 2013 Magespot Crew (http://www.magespot.com/)
 * @author     Magespot Crew <contacts@magespot.com>
 * @license    http://magespot.com/license.html  Magespot License
 */
class Magespot_Base_Block_Extensions extends Mage_Adminhtml_Block_System_Config_Form_Fieldset
{
	protected $_dummyElement;
	protected $_fieldRenderer;
	protected $_values;

    public function render(Varien_Data_Form_Element_Abstract $element)
    {
		$html = $this->_getHeaderHtml($element);
		$modules = array_keys((array)Mage::getConfig()->getNode('modules')->children());
		sort($modules);
        
        foreach ($modules as $moduleName) {
        	if (strstr($moduleName, 'Magespot_') === false) {
        		continue;
        	}
			
			if ($moduleName == 'Magespot_Base'){
				continue;
			}
			
        	$html.= $this->_getFieldHtml($element, $moduleName);
        }
        $html .= $this->_getFooterHtml($element);

        return $html;
    }

    protected function _getFieldRenderer()
    {
    	if (empty($this->_fieldRenderer)) {
    		$this->_fieldRenderer = Mage::getBlockSingleton('adminhtml/system_config_form_field');
    	}
    	return $this->_fieldRenderer;
    }

	protected function _getFieldHtml($fieldset, $moduleCode)
    {
		$currentVer = Mage::getConfig()->getModuleConfig($moduleCode)->version;
		if (!$currentVer)
            return '';
		  
		$moduleName = (string)Mage::getConfig()->getNode('modules/' . $moduleCode . '/extension_name'); // in case we have no data in the RSS 
		
		$allExtensions = unserialize(Mage::app()->loadCache('msbase_extensions'));
            
        $status = '<a  target="_blank"><img src="'.$this->getSkinUrl('images/magespot/msbase/ok.gif').'" alt="'.$this->__('Installed').'" title="'.$this->__('Installed').'"/></a>';

		if ($allExtensions && isset($allExtensions[$moduleCode])){
            $ext = $allExtensions[$moduleCode];
            
            $url     = $ext['url'];
            $name    = $ext['name'];
            $lastVer = $ext['version'];

            $moduleName = '<a href="'.$url.'" target="_blank" title="'.$name.'">'.$name."</a>";

            if ($this->_convertVersion($currentVer) < $this->_convertVersion($lastVer)){
                $status = '<a href="'.$url.'" target="_blank"><img src="'.$this->getSkinUrl('images/magespot/msbase/update.gif').'" alt="'.$this->__('Update available').'" title="'.$this->__('Update available').'"/></a>';
            }
        }
        
        if (Mage::getStoreConfig('advanced/modules_disable_output/' . $moduleCode)) {
            $status = '<a  target="_blank"><img src="'.$this->getSkinUrl('images/magespot/msbase/bad.gif').'" alt="'.$this->__('Output disabled').'" title="'.$this->__('Output disabled').'"/></a>';
        }

        $moduleName = $status . ' ' . $moduleName;
	
		$field = $fieldset->addField($moduleCode, 'label', array(
            'name'  => 'dummy',
            'label' => $moduleName,
            'value' => $currentVer,
		))->setRenderer($this->_getFieldRenderer());
			
		return $field->toHtml();
    }
    
    protected function _convertVersion($v)
    {
		$digits = @explode(".", $v);
		$version = 0;
		if (is_array($digits)){
			foreach ($digits as $k=>$v){
				$version += ($v * pow(10, max(0, (3-$k))));
			}
			
		}
		return $version;
	}
}