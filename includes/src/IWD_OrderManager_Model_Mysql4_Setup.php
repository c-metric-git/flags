<?php
class IWD_OrderManager_Model_Mysql4_Setup extends Mage_Core_Model_Resource_Setup
{
    protected $_callAfterApplyAllUpdates = true;

    protected $_ddlColumnTypes = array(
        Varien_Db_Ddl_Table::TYPE_BOOLEAN   => 'bool',
        Varien_Db_Ddl_Table::TYPE_SMALLINT  => 'smallint',
        Varien_Db_Ddl_Table::TYPE_INTEGER   => 'int',
        Varien_Db_Ddl_Table::TYPE_BIGINT    => 'bigint',
        Varien_Db_Ddl_Table::TYPE_FLOAT     => 'float',
        Varien_Db_Ddl_Table::TYPE_DECIMAL   => 'decimal',
        Varien_Db_Ddl_Table::TYPE_NUMERIC   => 'decimal',
        Varien_Db_Ddl_Table::TYPE_DATE      => 'date',
        Varien_Db_Ddl_Table::TYPE_TIMESTAMP => 'timestamp',
        'datetime'                          => 'datetime',
        'text'                              => 'text',
        Varien_Db_Ddl_Table::TYPE_BLOB      => 'blob',
        Varien_Db_Ddl_Table::TYPE_VARBINARY => 'blob'
    );

    const DEFAULT_TEXT_SIZE     = 1024;
    const MAX_TEXT_SIZE         = 2147483648;
    const MAX_VARBINARY_SIZE    = 2147483648;

    const TIMESTAMP_INIT_UPDATE = 'TIMESTAMP_INIT_UPDATE';
    const TIMESTAMP_INIT        = 'TIMESTAMP_INIT';
    const TIMESTAMP_UPDATE      = 'TIMESTAMP_UPDATE';

    protected $_readAdapter;
    protected $_writeAdapter;

    protected $tables = array(
        'sales/order_grid'      => 'iwd_ordermanager/archive_order',
        'sales/invoice_grid'    => 'iwd_ordermanager/archive_invoice',
        'sales/creditmemo_grid' => 'iwd_ordermanager/archive_creditmemo',
        'sales/shipment_grid'   => 'iwd_ordermanager/archive_shipment'
    );

    protected function _getReadAdapter()
    {
        if ($this->_readAdapter === null) {
            $this->_readAdapter = Mage::getSingleton('core/resource')->getConnection('iwd_ordermanager_read');
        }
        return $this->_readAdapter;
    }

    protected function _getWriteAdapter()
    {
        if ($this->_writeAdapter === null) {
            $this->_writeAdapter = Mage::getSingleton('core/resource')->getConnection('iwd_ordermanager_write');
        }
        return $this->_writeAdapter;
    }

    public function afterApplyAllUpdates()
    {
        try{
            $this->syncTables();
        } catch (Exception $e){

        }
        return $this;
    }

    protected function syncTables()
    {
        foreach ($this->tables as $origin => $archive) {
            $this->_syncTable($this->getTable($origin), $this->getTable($archive));
        }
        return $this;
    }

    protected function _syncTable($origin, $archive)
    {
        if ($this->tableExists($origin) && $this->tableExists($archive) ) {
            $this->_syncColumns($origin, $archive);
            $this->_syncColumnsPosition($origin, $archive);
        }

        return $this;
    }

    protected function _syncColumns($origin, $archive)
    {
        $adapter = $this->getConnection();

        $origin_fields = $adapter->describeTable($origin);
        $archive_fields = $adapter->describeTable($archive);
        foreach ($origin_fields as $field => $definition) {
            if (isset($archive_fields[$field])) {
                if ($this->_isColumnDifference($archive_fields[$field], $definition)) {
                    $this->modifyColumnByDdl($archive, $field, $definition);
                }
            } else {
                $columnInfo = $this->getColumnCreateByDescribe($definition);
                $this->addColumn($archive, $field, $columnInfo);
                $archive_fields[$field] = $definition;
            }
        }
    }

    public function addColumn($tableName, $columnName, $definition, $schemaName = null)
    {
        $adapter = $this->getConnection();

        if ($adapter->tableColumnExists($tableName, $columnName, $schemaName)) {
            return true;
        }

        $primaryKey = '';
        if (is_array($definition)) {
            $definition = array_change_key_case($definition, CASE_UPPER);
            if (empty($definition['COMMENT'])) {
                throw new Zend_Db_Exception("Impossible to create a column without comment.");
            }
            if (!empty($definition['PRIMARY'])) {
                $primaryKey = sprintf(', ADD PRIMARY KEY (%s)', $adapter->quoteIdentifier($columnName));
            }
            $definition = $this->_getColumnDefinition($definition);
        }

        $sql = sprintf('ALTER TABLE %s ADD COLUMN %s %s %s',
            $adapter->quoteIdentifier($this->_getTableName($tableName, $schemaName)),
            $adapter->quoteIdentifier($columnName),
            $definition,
            $primaryKey
        );

        $result = $adapter->raw_query($sql);

        $adapter->resetDdlCache($tableName, $schemaName);

        return $result;
    }

    protected function _getTableName($tableName, $schemaName = null)
    {
        return ($schemaName ? $schemaName . '.' : '') . $tableName;
    }

    public function modifyColumnByDdl($tableName, $columnName, $definition, $flushData = false, $schemaName = null)
    {
        $definition = array_change_key_case($definition, CASE_UPPER);
        $definition['COLUMN_TYPE'] = $this->_getColumnTypeByDdl($definition);
        if (array_key_exists('DEFAULT', $definition) && is_null($definition['DEFAULT'])) {
            unset($definition['DEFAULT']);
        }

        if (is_array($definition)) {
            $definition = $this->_getColumnDefinition($definition);
        }

        return $this->modifyColumn($tableName, $columnName, $definition, $flushData);
    }

    public function modifyColumn($tableName, $columnName, $definition, $showStatus = false)
    {
        $adapter = $this->getConnection();

        if (!$adapter->tableColumnExists($tableName, $columnName)) {
            throw new Exception(sprintf('Column "%s" does not exists on table "%s"', $columnName, $tableName));
        }

        $sql = sprintf('SET FOREIGN_KEY_CHECKS = 0; ALTER TABLE %s MODIFY COLUMN %s %s; SET FOREIGN_KEY_CHECKS = 0;',
            $adapter->quoteIdentifier($tableName),
            $adapter->quoteIdentifier($columnName),
            $definition);

        $result = $adapter->raw_query($sql);
        if ($showStatus) {
            $adapter->showTableStatus($tableName);
        }

        $adapter->resetDdlCache($tableName);
        return $result;
    }

    protected function _getColumnDefinition($options, $ddlType = null)
    {
        $adapter = $this->getConnection();

        // convert keys to uppercase
        $options    = array_change_key_case($options, CASE_UPPER);
        $cType      = null;
        $cUnsigned  = false;
        $cNullable  = true;
        $cDefault   = false;
        $cIdentity  = false;

        // detect and validate column type
        if ($ddlType === null) {
            $ddlType = $this->_getDdlType($options);
        }

        if (empty($ddlType) || !isset($this->_ddlColumnTypes[$ddlType])) {
            throw new Zend_Db_Exception('Invalid column definition data');
        }

        // column size
        $cType = $this->_ddlColumnTypes[$ddlType];
        switch ($ddlType) {
            case Varien_Db_Ddl_Table::TYPE_SMALLINT:
            case Varien_Db_Ddl_Table::TYPE_INTEGER:
            case Varien_Db_Ddl_Table::TYPE_BIGINT:
                if (!empty($options['UNSIGNED'])) {
                    $cUnsigned = true;
                }
                break;
            case Varien_Db_Ddl_Table::TYPE_DECIMAL:
            case Varien_Db_Ddl_Table::TYPE_NUMERIC:
                $precision  = 10;
                $scale      = 0;
                $match      = array();
                if (!empty($options['LENGTH']) && preg_match('#^\(?(\d+),(\d+)\)?$#', $options['LENGTH'], $match)) {
                    $precision  = $match[1];
                    $scale      = $match[2];
                } else {
                    if (isset($options['SCALE']) && is_numeric($options['SCALE'])) {
                        $scale = $options['SCALE'];
                    }
                    if (isset($options['PRECISION']) && is_numeric($options['PRECISION'])) {
                        $precision = $options['PRECISION'];
                    }
                }
                $cType .= sprintf('(%d,%d)', $precision, $scale);
                break;
            case 'text':
            case Varien_Db_Ddl_Table::TYPE_BLOB:
            case Varien_Db_Ddl_Table::TYPE_VARBINARY:
                if (empty($options['LENGTH'])) {
                    $length = self::DEFAULT_TEXT_SIZE;
                } else {
                    $length = $this->_parseTextSize($options['LENGTH']);
                }
                if ($length <= 255) {
                    $cType = $ddlType == 'text' ? 'varchar' : 'varbinary';
                    $cType = sprintf('%s(%d)', $cType, $length);
                } else if ($length > 255 && $length <= 65536) {
                    $cType = $ddlType == 'test' ? 'text' : 'blob';
                } else if ($length > 65536 && $length <= 16777216) {
                    $cType = $ddlType == 'test' ? 'mediumtext' : 'mediumblob';
                } else {
                    $cType = $ddlType == 'text' ? 'longtext' : 'longblob';
                }
                break;
        }

        if (array_key_exists('DEFAULT', $options)) {
            $cDefault = $options['DEFAULT'];
        }
        if (array_key_exists('NULLABLE', $options)) {
            $cNullable = (bool)$options['NULLABLE'];
        }
        if (!empty($options['IDENTITY']) || !empty($options['AUTO_INCREMENT'])) {
            $cIdentity = true;
        }

        /*  For cases when tables created from createTableByDdl()
         *  where default value can be quoted already.
         *  We need to avoid "double-quoting" here
         */
        if ( $cDefault !== null && strlen($cDefault)) {
            $cDefault = str_replace("'", '', $cDefault);
        }

        // prepare default value string
        if ($ddlType == Varien_Db_Ddl_Table::TYPE_TIMESTAMP) {
            if ($cDefault === null) {
                $cDefault = new Zend_Db_Expr('NULL');
            } elseif ($cDefault == self::TIMESTAMP_INIT) {
                $cDefault = new Zend_Db_Expr('CURRENT_TIMESTAMP');
            } else if ($cDefault == self::TIMESTAMP_UPDATE) {
                $cDefault = new Zend_Db_Expr('0 ON UPDATE CURRENT_TIMESTAMP');
            } else if ($cDefault == self::TIMESTAMP_INIT_UPDATE) {
                $cDefault = new Zend_Db_Expr('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
            } else if ($cNullable && !$cDefault) {
                $cDefault = new Zend_Db_Expr('NULL');
            } else {
                $cDefault = new Zend_Db_Expr('0');
            }
        } else if (is_null($cDefault) && $cNullable) {
            $cDefault = new Zend_Db_Expr('NULL');
        }

        if (empty($options['COMMENT'])) {
            $comment = '';
        } else {
            $comment = $options['COMMENT'];
        }

        //set column position
        $after = null;
        if (!empty($options['AFTER'])) {
            $after = $options['AFTER'];
        }

        return sprintf('%s%s%s%s%s COMMENT %s %s',
            $cType,
            $cUnsigned ? ' UNSIGNED' : '',
            $cNullable ? ' NULL' : ' NOT NULL',
            $cDefault !== false ? $adapter->quoteInto(' default ?', $cDefault) : '',
            $cIdentity ? ' auto_increment' : '',
            $adapter->quote($comment),
            $after ? 'AFTER ' . $adapter->quoteIdentifier($after) : ''
        );
    }

    protected function _parseTextSize($size)
    {
        $size = trim($size);
        $last = strtolower(substr($size, -1));

        switch ($last) {
            case 'k':
                $size = intval($size) * 1024;
                break;
            case 'm':
                $size = intval($size) * 1024 * 1024;
                break;
            case 'g':
                $size = intval($size) * 1024 * 1024 * 1024;
                break;
        }

        if (empty($size)) {
            return self::DEFAULT_TEXT_SIZE;
        }
        if ($size >= self::MAX_TEXT_SIZE) {
            return self::MAX_TEXT_SIZE;
        }

        return intval($size);
    }

    protected function _getDdlType($options)
    {
        $ddlType = null;
        if (isset($options['TYPE'])) {
            $ddlType = $options['TYPE'];
        } elseif (isset($options['COLUMN_TYPE'])) {
            $ddlType = $options['COLUMN_TYPE'];
        }

        return $ddlType;
    }

    protected function _getColumnTypeByDdl($column)
    {
        switch ($column['DATA_TYPE']) {
            case 'bool':
                return Varien_Db_Ddl_Table::TYPE_BOOLEAN;
            case 'tinytext':
            case 'char':
            case 'varchar':
            case 'text':
            case 'mediumtext':
            case 'longtext':
                return 'text';
            case 'blob':
            case 'mediumblob':
            case 'longblob':
                return Varien_Db_Ddl_Table::TYPE_BLOB;
            case 'tinyint':
            case 'smallint':
                return Varien_Db_Ddl_Table::TYPE_SMALLINT;
            case 'mediumint':
            case 'int':
                return Varien_Db_Ddl_Table::TYPE_INTEGER;
            case 'bigint':
                return Varien_Db_Ddl_Table::TYPE_BIGINT;
            case 'datetime':
                return 'datetime';
            case 'timestamp':
                return Varien_Db_Ddl_Table::TYPE_TIMESTAMP;
            case 'date':
                return Varien_Db_Ddl_Table::TYPE_DATE;
            case 'float':
                return Varien_Db_Ddl_Table::TYPE_FLOAT;
            case 'decimal':
            case 'numeric':
                return Varien_Db_Ddl_Table::TYPE_DECIMAL;
        }
        return 'text';
    }

    protected function _syncColumnsPosition($origin, $archive)
    {
        $prev = false;
        $origin_fields = $this->_getTableDescriptions($origin);
        $archive_fields = $this->_getTableDescriptions($archive);

        foreach ($origin_fields as $field => $definition) {
            if ($prev === false) {
                reset($archive_fields);
                if (key($archive_fields) !== $field) {
                    $this->_updateColumnPosition($archive, $field, $prev, true);
                }
            } else {
                reset($archive_fields);
                $key = key($archive_fields);
                while ($key !== $field) {
                    if (next($archive_fields) === false){
                        $key = false;
                        break;
                    }
                    $key = key($archive_fields);
                }
                if ($key) {
                    $moved = prev($archive_fields) !== false;
                    if (($moved && $prev !== key($archive_fields)) || !$moved) {
                        $this->_updateColumnPosition($archive, $field, $prev, false);
                    }
                }
            }
            $prev = $field;
        }
    }


    protected function _isColumnDifference($sourceColumn, $targetColumn)
    {
        unset($sourceColumn['TABLE_NAME']);
        unset($targetColumn['TABLE_NAME']);

        return $sourceColumn !== $targetColumn;
    }

    protected function _getTableDescriptions($table)
    {
        $description = $this->getConnection()->describeTable($table);
        $result = array();
        foreach ($description as $column) {
            $name = $column['COLUMN_NAME'];
            $result[$name] = $name;
        }
        return $result;
    }

    public function _updateColumnPosition($table, $column, $after, $first)
    {
        try{
            if ($after && $first && !is_string($after)) {
                $after = false;
            } elseif (!$after && !$first) {
                return $this;
            }

            $columns = array();
            $adapter = $this->_getWriteAdapter();
            $description = $adapter->describeTable($table);
            foreach ($description as $desc) {
                $name = $desc['COLUMN_NAME'];
                $columns[$name] = $this->getColumnDefinitionFromDescribe($desc);
            }

            if (!isset($columns[$column])) {
                return null;
            } elseif ($after && !isset($columns[$after])) {
                return null;
            }

            $_table = $adapter->quoteIdentifier($table);
            $_column = $adapter->quoteIdentifier($column);

            if ($after) {
                $_after = $adapter->quoteIdentifier($after);
                $sql = sprintf('ALTER TABLE %s MODIFY COLUMN %s %s AFTER %s', $_table, $_column, $columns[$column], $_after);
            } else {
                $sql = sprintf('ALTER TABLE %s MODIFY COLUMN %s %s FIRST', $_table, $_column, $columns[$column]);
            }

            $adapter->query($sql);
        } catch (Exception $e){

        }

        return $this;
    }

    public function getColumnDefinitionFromDescribe($options, $ddlType = null)
    {
        $columnInfo = $this->getColumnCreateByDescribe($options);
        foreach ($columnInfo['options'] as $key => $value) {
            $columnInfo[$key] = $value;
        }
        return $this->_getColumnDefinition($columnInfo, $ddlType);
    }

    public function getColumnCreateByDescribe($columnData)
    {
        $adapter = $this->getConnection();

        $type = $this->_getColumnTypeByDdl($columnData);
        $options = array();

        if ($columnData['IDENTITY'] === true) {
            $options['identity'] = true;
        }
        if ($columnData['UNSIGNED'] === true) {
            $options['unsigned'] = true;
        }
        if ($columnData['NULLABLE'] === false
            && !($type == 'text' && strlen($columnData['DEFAULT']) != 0)
        ) {
            $options['nullable'] = false;
        }
        if ($columnData['PRIMARY'] === true) {
            $options['primary'] = true;
        }
        if (!is_null($columnData['DEFAULT'])
            && $type != 'text'
        ) {
            $options['default'] = $adapter->quote($columnData['DEFAULT']);
        }
        if (strlen($columnData['SCALE']) > 0) {
            $options['scale'] = $columnData['SCALE'];
        }
        if (strlen($columnData['PRECISION']) > 0) {
            $options['precision'] = $columnData['PRECISION'];
        }

        $comment = uc_words($columnData['COLUMN_NAME'], ' ');

        $result = array(
            'name'      => $columnData['COLUMN_NAME'],
            'type'      => $type,
            'length'    => $columnData['LENGTH'],
            'options'   => $options,
            'comment'   => $comment
        );

        return $result;
    }
}