<?php
/**
* @desc Lav butala created this file of class to get products of amazon entry from teamdesk
*/

// TDAPI class have been included in config.php

class TDDataGeneral{
    
    // private variables
    public $api;
    public $arrErrorLog;
    public $productcount;
    public $dataset;
    public $arrTDFields; 
    public $tdErrorLog;
    public $update_rec;
    public $insert_rec;
    
    /**
    * @desc function to connect Teamdesk database by Lav Butala
    */
    public function Login(){
        // code added by Lav Buitala
        $td_api = new TDAPI();
        $td_api->Login();
        $this->api = $td_api;
        return $this->api;           
    }
    
    /**
    * @desc function to close connection to Teamdesk database by Lav Butala
    */
    public function Logout(){
        // code added by Lav Buitala
       $this->api->Logout();          
    }
    
    /**
    * @desc function to get all products from TD table by Lav Butala
    */
    public function getTDData($TDTable, $strColumns, $arrQueries,$orderBy=''){
        // connect to TD 
        //$this->connectToTeamdesk();
        // initialise TDProducts as array
        $tdProducts = array();
        // set $strColumns in TD syntax
        $strColumns_array = explode(",", $strColumns);
        foreach($strColumns_array as $strColumn){
            $columns[] = trim($strColumn);
        }
        $columns = "[".implode("], [", $columns)."]";
        // set $arrQueries
        if($arrQueries!=""){
            $arrQueries = "WHERE ".$arrQueries;
        }
        else{
            $arrQueries = "";
        }
        // create the teamdesk query, to fetch all the products
        $sqlQuery = "SELECT ".$columns." FROM [".$TDTable."] ".$arrQueries." ".$orderBy;
        // call TD query and retrive data        
        try{
            $arrResults = $this->api->Query($sqlQuery);     
             if (isset($arrResults)) {   
                foreach ($arrResults->Rows as $arrResult) {    
                    $tdProducts[] = $arrResult;
                }                
            }
        // return TDProducts array         
        return  $tdProducts;
       }
       catch (Exception $e) {
            $this->tdErrorLog = '<br/>Caught exception: ' .$e->getMessage(). "<br/>";
            echo $this->tdErrorLog;
            return $this->tdErrorLog;           
        }  
    }
    
    /**
    * @desc function to update in TD table by Lav Butala
    */
    public function updateTDData($TDTable, $results, $schemaArray){
        // connect to TD
        //$this->connectToTeamdesk();
        // get schema in dataset
        try{
            $this->dataset = $this->api->GetSchema($TDTable,$schemaArray);
            // store result data in dataset
            $counter = 0;
            foreach($results as $key => $resultArray){
                $array_keys = array_keys($resultArray);
                foreach($array_keys as $key => $array_key){
                    $this->dataset->Rows[$counter][$array_key] = $resultArray[$array_key];
                }
                // update in TD
                if($counter==499) {
                    try{
                        $records_updated = $this->api->Upsert($TDTable, $this->dataset);
                        $this->update_rec +=  count($records_updated);
                        $this->dataset->Rows = array();
                        $counter = 0;
                    }
                    catch (Exception $e) {
                        $this->tdErrorLog = '<br/>Caught exception: ' .$e->getMessage(). "<br/>";
                        echo $this->tdErrorLog;
                        return $this->tdErrorLog;           
                    }
                }            
                $counter++;
            }
            // update in TD
            if(count($this->dataset->Rows)) {  
                $records_updated = $this->api->Upsert($TDTable, $this->dataset);
                $this->update_rec +=  count($records_updated);
            }
            // return number of records updated
            return $this->update_rec;
        }
        catch (Exception $e) {
            $this->tdErrorLog = '<br/>Caught exception: ' .$e->getMessage(). "<br/>";
            echo $this->tdErrorLog;
            return $this->update_rec;           
        }
        // return number of records updated
        return $this->update_rec;
    }
    
     /**
    * @desc function to update in TD table by Lav Butala
    */
    public function insertTDData($TDTable, $results, $schemaArray){
        // connect to TD
        //$this->connectToTeamdesk();
        // get schema in dataset
        try{
            $this->dataset = $this->api->GetSchema($TDTable,$schemaArray);
            // store result data in dataset
            $counter = 0;
            foreach($results as $key => $resultArray){
                $array_keys = array_keys($resultArray);
                foreach($array_keys as $key => $array_key){
                    $this->dataset->Rows[$counter][$array_key] = $resultArray[$array_key];
                }
                // update in TD
                if($counter==499) {
                    try{
                        $records_inserted = $this->api->Create($TDTable, $this->dataset);
                        $this->insert_rec +=  count($records_inserted);
                        $this->dataset->Rows = array();
                        $counter = 0;
                    }
                    catch (Exception $e) {
                        $this->tdErrorLog = '<br/>Caught exception: ' .$e->getMessage(). "<br/>";
                        echo $this->tdErrorLog;
                        return $this->tdErrorLog;           
                    }
                }            
                $counter++;
            }
            // update in TD
            if(count($this->dataset->Rows)) {  
                $records_inserted = $this->api->Create($TDTable, $this->dataset);
                $this->insert_rec +=  count($records_inserted);
            }
            // return number of records updated
            return $this->insert_rec;
        }
        catch (Exception $e) {
            $this->tdErrorLog = '<br/>Caught exception: ' .$e->getMessage(). "<br/>";
            echo $this->tdErrorLog;
            return $this->insert_rec;           
        }
        // return number of records updated
        return $this->insert_rec;
    }
}
?>