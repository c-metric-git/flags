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
 * @copyright  Copyright (c) 2011 AITOC, Inc. 
 * @package    AdjustWare_Ordernum
 * @author lyskovets
 */
class  AdjustWare_Ordernum_Helper_Path extends Mage_Core_Helper_Abstract
{
    private $_section = 'ordernum';

    public function  __construct()
    {
        if(empty ($this->_section))
        {
            Mage::throwException('Prefix not initialized');
        }
    }
    
    public function getSection()
    {
        return $this->_section;
    }

    public function compilePath($group, $field)
    {
        return $this->getSection().'/'.$group.'/'.$field;
    }
    /**
     *
     * @return array()
     */
    public function getValidPaths($scope)
    {
        $groups = Mage::getModel('adjordernum/backend_config')
            ->getGroupsCollection($scope);
        $pathsSet = array();
        $i = 0;
        foreach($groups as $groupName => $groupe)
        {
            foreach ($groupe['fields'] as $fieldName => $field )
            {
                $path = $this->getSection().'/'.$groupName.'/'.$fieldName;
                $pathsSet[$i] = $path;
                $i++;
            }
        }
        return $pathsSet;
    }

}