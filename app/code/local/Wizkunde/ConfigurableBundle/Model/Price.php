<?php

class Wizkunde_ConfigurableBundle_Model_Price extends Mage_Catalog_Model_Product_Type_Price{
    
    /**
     * Prefix of table names
     *
     * @var string
     */
    protected $_import_table = 'importprice_importprice_rows';
    
    /**
     * Attributes for import price 
     */
    protected $_import_fields = array('sku', 'max_height', 'max_width','price');
    
    
    /**
     * Check products has import prices    
     * @return array prices with attributes accociated
     */
    public function checkImportPrice($product_sku) { 
        
        if ($this->isRecordExist($product_sku)){
            return $this->getImportPrices($product_sku);
        }
        return array();        
    }
    
    /**
     * Get resource instance
     * Read connection
     */
    public function readResource() {
        return Mage::getSingleton('core/resource')->getConnection('core_read');
    }
    
    
    /**
     * Check products sku is exist to do next processing    
     * @return boolen
     */
    public function isRecordExist($sku) {       
        try {
            $entity_id = $this->readResource()->fetchRow(
                    "SELECT entity_id from " . $this->_import_table . " WHERE `sku` = '$sku'"                    
            );
            if ($entity_id)
                return $entity_id;
        } catch (Exception $e) {            
        }
        return false;
    }
    
    public function getImportPrices($sku){
        
        foreach($this->_import_fields as $field){
            $tem[] = "`$field`";
        }
        $fields = @implode(',', $tem);
        try {
            $entity_ids = $this->readResource()->fetchAll(
                    "SELECT  $fields from " . $this->_import_table . " WHERE `sku` = '$sku'"                    
            );
            if (is_array($entity_ids)){
                foreach ($entity_ids as $id => $row){
                    $entity_ids[$id]['price'] = $row['price'] / 100;
                }
                return $entity_ids;
            }
                
        } catch (Exception $e) {            
        }
        return array();
    }
}