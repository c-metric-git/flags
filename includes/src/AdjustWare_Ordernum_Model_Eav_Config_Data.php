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
class AdjustWare_Ordernum_Model_Eav_Config_Data extends Mage_Eav_Model_Entity_Store
{

    public function getNumber($storeId , $group)
    {
        $entityTypeId = Mage::getModel('eav/entity_type')
            ->loadByCode($group)
            ->getEntityTypeId();
        $eavRow = $this->loadByEntityStore($entityTypeId, $storeId);
        $lastId = $eavRow->getIncrementLastId();
        $prefix = $eavRow->getIncrementPrefix();
        if (strpos($lastId, $prefix)===0)
        {
            $number = (int)substr($lastId, strlen($prefix));
        } 
        else
        {
            $number = (int)$lastId;
        }
        return $number;
    }
}