<?php
$installer = $this;

$installer->startSetup();

$table = $installer->getConnection()
    ->newTable($installer->getTable('solrsearch/index'))
    ->setOption('type', 'MyISAM')
    ->addColumn('index_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
    		'identity'  => true,
    		'unsigned'  => true,
    		'nullable'  => false,
    		'primary'   => true,
    ), 'Index Id')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
    		'unsigned'  => true,
    		'nullable'  => false,
    		'primary'   => true,
    		'default'   => '0',
    ), 'Store ID')
    ->addColumn('solr_core', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
    		'nullable'  => false,
    		'primary'   => true,
    ), 'Solr Core')
    ->addColumn('title', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
    ), 'Index title')
    ->addColumn('update_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
    		'default'   => 'CURRENT_TIMESTAMP',
    ), 'Update Time')
    ->addColumn('changed', Varien_Db_Ddl_Table::TYPE_TINYINT, null, array(
    		'unsigned'  => true,
    		'nullable'  => false,
    		'primary'   => false,
    		'default'   => '0',
    ), 'Item Changed')
    ->addIndex("IDX_SOLRBRIDGE_SEARCH_INDEX_INDEX_ID", array('index_id'))
    ->addIndex("IDX_SOLRBRIDGE_SEARCH_INDEX_STORE_ID", array('store_id'))
    ->addIndex("IDX_SOLRBRIDGE_SEARCH_INDEX_SOLR_CORE", array('solr_core'));
    
$installer->getConnection()->createTable($table);

$installer->endSetup();