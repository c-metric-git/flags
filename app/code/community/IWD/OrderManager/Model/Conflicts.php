<?php
class IWD_OrderManager_Model_Conflicts extends Mage_Core_Model_Abstract
{
    protected $rewrites_modules = array();

    public function getTypes()
    {
        return array(
            'model' => Mage::helper('iwd_ordermanager')->__('Model'),
            'block' => Mage::helper('iwd_ordermanager')->__('Block'),
            'helper' => Mage::helper('iwd_ordermanager')->__('Helper'),
        );
    }

    public function getRewritesClasses()
    {
        $this->rewrites_modules = array();

        foreach ($this->getTypes() as $type => $typeLabel) {
            $rewrites_modules = $this->_collectRewrites($type);
            foreach ($rewrites_modules as $base => $rewrites) {
                if (count($rewrites) > 1) {
                    foreach ($rewrites as $class) {
                        $pos = strpos($class, 'IWD_OrderManager');
                        if ($pos === 0) {
                            $this->rewrites_modules[$base] = $rewrites;
                            break;
                        }
                    }
                }
            }
        }
        return $this->rewrites_modules;
    }

    protected function _collectRewrites($type)
    {
        $rewrites_modules = array();
        $module_config_base = Mage::getModel('core/config_base');
        $module_config = Mage::getModel('core/config_base');

        $modules = Mage::getConfig()->getNode('modules')->children();
        foreach ($modules as $module_name => $module_settings) {
            if (!$module_settings->is('active')) {
                continue;
            }

            $config_file = Mage::getConfig()->getModuleDir('etc', $module_name) . DS . 'config.xml';
            $module_config_base->loadFile($config_file);

            $module_config->loadString('<config/>');
            $module_config->extend($module_config_base, true);

            $node_type = $module_config->getNode()->global->{$type . 's'};

            if (!$node_type) {
                continue;
            }

            $node_type_children = $node_type->children();

            foreach ($node_type_children as $node_name => $config) {
                $rewrites = $config->rewrite;
                if ($rewrites) {
                    foreach ($rewrites->children() as $class => $new_class) {
                        $base_class = $this->_getClassName($type, $node_name, $class);
                        if(!isset($rewrites_modules[$base_class]) || (isset($rewrites_modules[$base_class]) && !in_array($new_class, $rewrites_modules[$base_class]))){
                            if(strpos($new_class, 'IWD_All') === 0 || strpos($new_class, 'IWD_POS') === 0){
                                continue;
                            }
                            $rewrites_modules[$base_class][] = $new_class;
                        }
                    }
                }
            }
        }
        return $rewrites_modules;
    }

    protected function _getClassName($type, $group, $class)
    {
        $config = Mage::getConfig()->getNode()->global->{$type . 's'}->{$group};

        $class_name = (!empty($config)) ? $config->getClassName() : "";
        $class_name = (empty($class_name)) ? 'mage_' . $group . '_' . $type : $class_name;
        $class_name .= (!empty($class)) ? '_' . $class : $class_name;

        return uc_words($class_name);
    }
}