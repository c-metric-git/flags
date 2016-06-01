<?php

class Potato_Core_Block_System_Config_Form_Fieldset_Extensions extends Mage_Adminhtml_Block_System_Config_Form_Fieldset
{
    protected $_feedData = array();

    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $this->_prepareElement($element);
        return parent::render($element);
    }

    protected function _prepareElement(Varien_Data_Form_Element_Abstract $element)
    {
        $modules = $this->_getInstalledModules();
        $table = new Potato_Core_Block_System_Config_Form_Fieldset_Element_Table(array());
        foreach ($modules as $moduleInfo) {
            $table->addExtension($moduleInfo);
        }
        $element->addElement($table);
        return $this;
    }

    protected function _getInstalledModules()
    {
        $modules = array_keys((array)Mage::getConfig()->getNode('modules')->children());
        sort($modules);
        $_result = array();
        foreach ($modules as $moduleName) {
            if (strstr($moduleName, 'Potato_') === false) {
                continue;
            }
            if ($moduleName == 'Potato_Core') {
                continue;
            }
            $_result[$moduleName] = array(
                'module_name'   => strtolower($moduleName),
                'platform'      => strtoupper(Mage::getConfig()->getNode("modules/$moduleName/platform")),
                'version'       => (string)Mage::getConfig()->getNode("modules/$moduleName/version"),
                'extension_url' => $this->_getExtensionInfo($moduleName, 'extension_url'),
                'title'         => $this->_getExtensionInfo($moduleName, 'title'),
                'last_version'  => $this->_getExtensionInfo($moduleName, 'last_version'),
                'readme_url'    => $this->_getExtensionInfo($moduleName, 'readme_url'),
                'review_url'    => $this->_getExtensionInfo($moduleName, 'review_url'),
            );
        }
        return $_result;
    }

    protected function _getExtensionInfo($moduleName, $attributeName)
    {
        if (empty($this->_feedData)) {
            $this->_feedData = Mage::getModel('po_core/source_feed')->getFeed();
        }
        $_result = $this->_getDefaultValue($moduleName, $attributeName);
        if (array_key_exists($moduleName, $this->_feedData) &&
            array_key_exists($attributeName, $this->_feedData[$moduleName])
        ) {
            $_result = $this->_feedData[$moduleName][$attributeName];
        }
        return $_result;
    }

    protected function _getDefaultValue($moduleName, $attributeName)
    {
        switch($attributeName) {
            case 'title':
                return $moduleName;
            case 'last_version':
                return 0;
            case 'extension_url':
            case 'readme_url':
            case 'review_url':
                return null;
        }
        return null;
    }
}