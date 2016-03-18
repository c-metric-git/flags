<?php
$installer = $this;

$installer->startSetup();

Mage::getConfig()->reinit();
/* @var $installer Mage_Eav_Model_Entity_Setup */
$ultility = Mage::getModel('solrsearch/ultility');

$availableCores = $ultility->getAvailableCores();

if ( isset($availableCores) && !empty($availableCores) )
{
    foreach ($availableCores as $solrcore => $info)
    {
        if (isset($info['stores']) && strlen(trim($info['stores'], ',')) > 0)
        {
            try{
                $logTableName = $ultility->getLogTable();

                $indexedTableName = Mage::getResourceModel('solrsearch/solr')->getIndexTableName($logTableName, $solrcore);

                if (Mage::getResourceModel('solrsearch/solr')->isIndexTableNameExist($indexedTableName))
                {
                    $installer->getConnection()->addColumn($indexedTableName, 'changed', "TINYINT( 3 ) UNSIGNED NOT NULL DEFAULT '0'");
                }
            }
            catch (Exception $e)
            {
                $this->ultility->writeLog($e->getMessage(), 0, '', '', true);
            }
        }
    }
}

$installer->endSetup();