<?php
/**
 * Custom Order Number Pro
 *
 * @category:    AdjustWare
 * @package:     AdjustWare_Ordernum
 * @version      5.1.5
 * @license:     d0NyTkcYcW64yuyl9Cf2M6q3gBilLUVMAwQSumkwPP
 * @copyright:   Copyright (c) 2015 AITOC, Inc. (http://www.aitoc.com)
 */
/**
 *
 * @copyright  Copyright (c) 2011 AITOC, Inc.
 * @package    AdjustWare_Ordernum
 * @author lyskovets
 */
class AdjustWare_Ordernum_Model_Backend_Config extends Mage_Adminhtml_Model_Config
{
    /** @return array */
    public function getGroupsCollection($scope)
    {
        $detector = $this->_getScopeDetector($scope);
        $groups = $this->getSection('ordernum')->descend('groups')->asArray();
        foreach($groups as $name => $group)
        {
            if(isset($group[$detector]) && $group[$detector] == '0' )
            {
                unset ($groups[$name]);
            }
        }
        return $groups;
    }
    /** @return array */
    public function getFieldsCollection($scope, $group)
    {
        $detector = $this->_getScopeDetector($scope);
        $groups = $this->getGroupsCollection($scope);
        $fields = $groups[$group]['fields'];
        foreach($fields as $name => $field)
        {
            if(isset($field[$detector]) && $field[$detector] == '0' )
            {
                unset ($fields[$name]);
            }
        }
        return $fields;
    }

    public function getField($scope, $group, $fieldName)
    {
        $collection = $this->getFieldsCollection($scope, $group);
        foreach ($collection as $name => $field)
        {
            if($name == $fieldName)
            {
                return $field;
            }
        }
    }

    private function _getScopeDetector($scope)
    {
        switch ($scope)
        {
            case('default'):
                return 'show_in_default' ;
            case('websites'):
                return 'show_in_website';
            case('stores'):
                return 'show_in_store';
            default:
                Mage::throwException('Scope is Illegal');
        }
    }
    
}
?>