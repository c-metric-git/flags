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
class AdjustWare_Ordernum_Model_Rewrite_EavEntityType extends Mage_Eav_Model_Entity_Type
{
    public function fetchNewIncrementId($storeId = null)
    {
        if (!$this->getIncrementModel()) {
            return false;
        }

        if (!$this->getIncrementPerStore() || ($storeId === null)) {
            /**
             * store_id null we can have for entity from removed store
             */
            $storeId = 0;
        }

        $this->_getResource()->beginTransaction();

        $entityStoreConfig = Mage::getModel('eav/entity_store') // Mage_Eav_Model_Entity_Store
            ->loadByEntityStore($this->getId(), $storeId);

        if (!$entityStoreConfig->getId()) {
            $entityStoreConfig
                ->setEntityTypeId($this->getId())
                ->setStoreId($storeId)
                ->setIncrementPrefix($storeId)
                ->save();
        }

        $incrementInstance = Mage::getModel($this->getIncrementModel())
            ->setPrefix($entityStoreConfig->getIncrementPrefix())
            ->setPadLength($this->getIncrementPadLength())
            ->setPadChar($this->getIncrementPadChar())
            ->setLastId($entityStoreConfig->getIncrementLastId())
            ->setEntityTypeId($entityStoreConfig->getEntityTypeId())
            ->setStoreId($entityStoreConfig->getStoreId());

        $incrementId = $incrementInstance->getNextId();
        $entityStoreConfig->setIncrementLastId($incrementId);
        #aitoc start changes
        $entityStoreConfig->setIncrementPrefix($incrementInstance->getPrefix());
        #aitoc end changes
        $entityStoreConfig->save();

        // Commit increment_last_id changes
        $this->_getResource()->commit();

        return $incrementId;
    }
}