<?php
class IWD_OrderManager_Model_Mysql4_Archive extends Mage_Core_Model_Resource_Db_Abstract
{
    const ORDER      = IWD_OrderManager_Model_Archive::ORDER;
    const INVOICE    = IWD_OrderManager_Model_Archive::INVOICE;
    const SHIPMENT   = IWD_OrderManager_Model_Archive::SHIPMENT;
    const CREDITMEMO = IWD_OrderManager_Model_Archive::CREDITMEMO;

    protected $entities = array(
        self::ORDER      => 'sales/order',
        self::INVOICE    => 'sales/order_invoice',
        self::SHIPMENT   => 'sales/order_shipment',
        self::CREDITMEMO => 'sales/order_creditmemo',
    );

    protected $standard_tables = array(
        self::ORDER      => 'sales/order_grid',
        self::INVOICE    => 'sales/invoice_grid',
        self::SHIPMENT   => 'sales/shipment_grid',
        self::CREDITMEMO => 'sales/creditmemo_grid',
    );

    protected $archive_tables = array(
        self::ORDER      => 'iwd_ordermanager/archive_order',
        self::INVOICE    => 'iwd_ordermanager/archive_invoice',
        self::SHIPMENT   => 'iwd_ordermanager/archive_shipment',
        self::CREDITMEMO => 'iwd_ordermanager/archive_creditmemo'
    );

    protected function _construct()
    {
        $this->_setResource('iwd_ordermanager_archive');
    }

    public function getEntityModel($entity)
    {
        $entities = $this->entities;
        return isset($entities[$entity]) ? $entities[$entity] : false;
    }

    public function getArchiveTable($entity)
    {
        if (!isset($this->archive_tables[$entity])) {
            return false;
        }
        return $this->getTable($this->archive_tables[$entity]);
    }

    public function getStandardTable($entity)
    {
        if (!isset($this->standard_tables[$entity])) {
            return false;
        }
        return $this->getTable($this->standard_tables[$entity]);
    }


    public function setForeignKeyChecks($value)
    {
        $adapter = $this->_getWriteAdapter();
        $adapter->query("SET FOREIGN_KEY_CHECKS = {$value};");
        return $this;
    }

    public function getIdsInArchive($entity, $ids)
    {
        if (!is_array($ids)) {
            $ids = array($ids);
        }

        $select = $this->_getReadAdapter()->select()
            ->from($this->getArchiveTable($entity), 'entity_id')
            ->where('entity_id IN(?)', $ids);

        return $this->_getReadAdapter()->fetchCol($select);
    }

    public function addToArchiveFromStandard($entity, $field, $value)
    {
        $adapter = $this->_getWriteAdapter();
        $source_table = $this->getStandardTable($entity);
        $target_table = $this->getArchiveTable($entity);

        $insert_to_fields = array_intersect(
            array_keys($adapter->describeTable($target_table)),
            array_keys($adapter->describeTable($source_table))
        );

        $condition = $adapter->quoteIdentifier($field) . ' IN(?)';
        $select = $adapter->select()
            ->from($source_table, $insert_to_fields)
            ->where($condition, $value);

        $adapter->query($select->insertFromSelect($target_table, $insert_to_fields, true));
        return $this;
    }

    public function removeFromStandard($entity, $field, $value)
    {
        $adapter = $this->_getWriteAdapter();
        $source_table = $this->getStandardTable($entity);
        $target_table = $this->getArchiveTable($entity);
        $source_model = Mage::getResourceSingleton($this->getEntityModel($entity));

        if ($value instanceof Zend_Db_Expr) {
            $select = $adapter->select();
            $select->from($target_table, $source_model->getIdFieldName());
            $condition = $adapter->quoteInto($source_model->getIdFieldName() . ' IN(?)', new Zend_Db_Expr($select));
        } else {
            $fieldCondition = $adapter->quoteIdentifier($field) . ' IN(?)';
            $condition = $adapter->quoteInto($fieldCondition, $value);
        }

        $adapter->delete($source_table, $condition);
        return $this;
    }

    public function restoreFromArchive($entity, $field = '', $value = null)
    {
        $adapter = $this->_getWriteAdapter();
        $source_table = $this->getArchiveTable($entity);
        $target_table = $this->getStandardTable($entity);
        $source_model = Mage::getResourceSingleton($this->getEntityModel($entity));

        $insert_to_fields = array_intersect(
            array_keys($adapter->describeTable($target_table)),
            array_keys($adapter->describeTable($source_table))
        );

        $select_from_fields = $insert_to_fields;
        $updatedAtIndex = array_search('updated_at', $select_from_fields);
        if ($updatedAtIndex !== false) {
            unset($select_from_fields[$updatedAtIndex]);
            $select_from_fields['updated_at'] = new Zend_Db_Expr($adapter->quoteInto('?', $this->formatDate(true)));
        }

        $select = $adapter->select()->from($source_table, $select_from_fields);

        if (!empty($field)) {
            $select->where($adapter->quoteIdentifier($field) . ' IN(?)', $value);
        }

        $adapter->query($select->insertFromSelect($target_table, $insert_to_fields, true));
        if ($value instanceof Zend_Db_Expr) {
            $select->reset()->from($target_table, $source_model->getIdFieldName());
            $condition = $adapter->quoteInto($source_model->getIdFieldName() . ' IN(?)', new Zend_Db_Expr($select));
        } elseif (!empty($field)) {
            $condition = $adapter->quoteInto(
                $adapter->quoteIdentifier($field) . ' IN(?)', $value
            );
        } else {
            $condition = '';
        }

        $adapter->delete($source_table, $condition);
        return $this;
    }


    public function getOrderIdsForArchiveExpression()
    {
        $statuses = Mage::getModel('iwd_ordermanager/archive')->getArchiveOrderStatuses();
        $period = Mage::getModel('iwd_ordermanager/archive')->getArchiveAfterDays();

        $select = $this->_getOrderIdsForArchiveSelect($statuses, $period);

        return new Zend_Db_Expr($select);
    }

    protected function _getOrderIdsForArchiveSelect($statuses, $period)
    {
        $adapter = $this->_getReadAdapter();
        $table = $this->getStandardTable(IWD_OrderManager_Model_Archive::ORDER);
        $select = $adapter->select()->from($table, 'entity_id')->where('status IN(?)', $statuses);

        if ($period) {
            $archivePeriodExpr = $adapter->getDateSubSql($adapter->quote($this->formatDate(true)), (int)$period, Varien_Db_Adapter_Interface::INTERVAL_DAY);
            $select->where($archivePeriodExpr . ' >= created_at');
        }

        return $select;
    }

    public function getOrderIdsForArchive($order_ids = array(), $use_period = false)
    {
        $statuses = Mage::getModel('iwd_ordermanager/archive')->getArchiveOrderStatuses();
        $period = Mage::getModel('iwd_ordermanager/archive')->getArchiveAfterDays();
        $period = ($use_period ? $period : 0);

        if (empty($statuses)) {
            return array();
        }

        $select = $this->_getOrderIdsForArchiveSelect($statuses, $period);
        if (!empty($order_ids)) {
            $select->where('entity_id IN(?)', $order_ids);
        }

        return $this->_getReadAdapter()->fetchCol($select);
    }


    public function updateGridRecords($entity, $ids)
    {
        $gridColumns = array_keys($this->_getWriteAdapter()->describeTable($this->getArchiveTable($entity)));

        $columnsToSelect = array();

        $select = Mage::getResourceSingleton($this->getEntityModel($entity))
            ->getUpdateGridRecordsSelect($ids, $columnsToSelect, $gridColumns, true);

        $this->_getWriteAdapter()->query($select->insertFromSelect($this->getArchiveTable($entity), $columnsToSelect, true));

        return $this;
    }
}