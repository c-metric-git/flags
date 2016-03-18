<?php

 /**                                                                        

    * @desc Function to get the country name based on the country id

    * @param integer $countryID, country ID for which the country name must be retrieved

    * @return string $countryName country name

    * @since 05-Feb-2013  

    * @author Dinesh Nagdev

    */

    function getCountryNameByID($db, $countryID=0, $type='')

    {

        $countryName = '';

        $db->query("SELECT name, iso_a2 FROM ".DB_PREFIX."countries WHERE coid='".addslashes($countryID)."'");

        if ($db->moveNext()) { 

           if ($type == 'country_code')

              $countryName  = $db->col['iso_a2'];  

           else    

              $countryName  = $db->col['name'];

        }

        return $countryName;

    }

    

    /**                                                                        

    * @desc Function to get the country id based on the country name

    * @param integer $countryName, type for which the country id must be retrieved

    * @return string $countryid

    * @since 05-Feb-2013  

    * @author Dinesh Nagdev

    */

    function getCountryIDByName($db, $countryName='', $type='')

    {

        $countryID = 0;

        

        if ($type == 'country_code')

            $db->query("SELECT coid FROM ".DB_PREFIX."countries WHERE iso_a2='".addslashes($countryName)."'");

        else 

            $db->query("SELECT coid FROM ".DB_PREFIX."countries WHERE name='".addslashes($countryName)."'"); 

                

        if ($db->moveNext()) { 

            $countryID  = $db->col['coid'];  

        }

        return $countryID;

    }

    

    

    /**                                                                        

    * @desc Function to get the state name based on the state id

    * @param integer $stateID, state ID for which the state name must be retrieved

    * @return string $stateName state name 

    * @since 05-Feb-2013  

    * @author Dinesh Nagdev

    */

    function getStateNameByID($db, $stateID=0)
    {
        $stateName = '';         
        $db->query("SELECT code FROM directory_country_region WHERE region_id='".addslashes($stateID)."' ");
        if ($db->moveNext()) { 
            $stateName   = $db->col['code'];  
        }
        return $stateName;
    }

    /**                                                                        

    * @desc Function to get the state id based on the state short name

    * @param integer $db, state $stateShortName for which the state Id must be retrieved

    * @return string $stateid state name 

    * @since 05-Feb-2013

    * @author Dinesh Nagdev

    */

    function getStateByShortName($db, $stateShortName='') 

    {

        $db->query("SELECT stid, name FROM ".DB_PREFIX."states WHERE short_name ='".addslashes($stateShortName)."'");

        if ($db->moveNext()) {

            return $db->col;

        } 

        return false;  

    }   

?>

