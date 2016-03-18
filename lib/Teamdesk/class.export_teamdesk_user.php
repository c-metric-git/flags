<?php 
/**  
* @desc This class has functions to export the user details from Pinnacle to TeamDesk. 
* @package  TeamDesk  
* @author Dinesh Nagdev
* @since 06-Feb-2013                       
*/ 
class TeamDeskUser {
	private $db;    
    private $connTDUser;
    private $arrTDFields;
    private $arrTDShipFields;
    private $arrErrorLog;
    private $dataset;  
    private $shippingdataset;
    private $tdErrorLog;  
    private $tdSendToPinnacleID;
    private $tdRecordHistoryID;
    private $tdSendToPinnacleID_shipAdd;
    private $tdRecordHistoryID_shipAdd;
    private $totalUserAdded;
    private $totalUserUpdated;
    private $totalUserUpdateFromTD;
    private $arrTDShipUpdateData;     
    private $arrTDUserUpdateData;     
    public $userID;   
	/* 
       CONSTRUCTOR
	*/
	function __construct(&$db){
		$this->db = $db;
        $this->userID = 0;
        $this->totalUserAdded = 0;
        $this->totalUserUpdated = 0;
        $this->totalUserUpdateFromTD = 0; 

        $this->connectToTeamDesk(); 
	}    
    /**
    * @desc Function to connect to TeamDesk Users table
    * @return array
    * @since 06-Feb-2013
    * @author Dinesh Nagdev
    */
    private function connectToTeamDesk()
    {
        /**
        * @desc code to create the teamdesk api object with the domain name and database id.
        */
        try {
            $td_api = new API(TD_DOMAIN,TD_DB_ID,array("trace" => true)); 
            /**
            * @desc code to check the user login and session
            */
            $td_api->login(TD_USERNAME, TD_PASSWORD);  
            $this->api = $td_api;   
        }

        catch (Exception $e) {
            $this->tdErrorLog = '<br/>Caught exception: ' .$e->getMessage(). "<br/>";
            return $this->tdErrorLog;              
        }            
    } 
    /**
    * @desc Function to export Pinnacle users into Teamdesk Contacts table
    * @since 06-Feb-2013
    * @author Dinesh Nagdev
    */
    public function exportUserToTeamDesk($export_new_user_only='',$order_cust_id=0)
    {
        $totalUsersAdded = 0;
        $totalUsersUpdated = 0;
        $arrUsers = array();
        $tmpcustomerID = 0;
        $user_type =''; 
        $this->connectToTeamDesk();
        if($export_new_user_only=='') {
            $export_new_user_only='Yes';
        }    
        if ($this->api !='') {     
            /**
            * @desc code to get the schema of webprofile  from teamdesk
            */
            $this->dataset = ''; 
            $this->datasetcreate = ''; 
            /**
            * @desc code to get the schema of Contact  from teamdesk
            */
            $this->dataset = $this->api->GetSchema("Contact",array('First Name','Last Name','login','E-mail Address'));          
            $this->datasetcreate = $this->api->GetSchema("Contact",array('Customer ID','pinnacleId','First Name','Last Name','login','E-mail Address')); 
            /**
            * @desc get list of all the new users registered with the site (pinnacle)   kk
            */ 
            // code added by lav butala that remove the quickbase fields and code 
            if($export_new_user_only == 'Yes') {
                   $this->db->query("SELECT *   
                            FROM customer_entity  
                            WHERE is_active = '1' AND teamdesk_id=0 ORDER BY entity_id ASC"); /*AND quickbase_id=0*/ 
            }
            else if($order_cust_id > '0') {
                   $this->db->query("SELECT *   
                            FROM customer_entity  
                            WHERE is_active = '1' AND entity_id=".$order_cust_id." ORDER BY entity_id ASC"); /*AND quickbase_id=0*/ 
            }
            else {
                $this->db->query("SELECT *   
                            FROM customer_entity  
                            ORDER BY uid ASC");  
            }                
            $arrUsers = $this->db->getRecords(); 
            foreach ($arrUsers as $key => $userData) {
                $this->datasetcreate->Rows = array();
                $this->dataset->Rows = array();
                // initialize
                $arrUserDetails = array(); 
                   /**
                   * @desc code to get the user name  
                   */
                   $userSQL = "SELECT *       
                                FROM customer_entity_varchar      
                                WHERE entity_id = '".addslashes($userData['entity_id'])."' AND (attribute_id=5 OR attribute_id=7) "; 
                    $this->db->query($userSQL);
                    while($this->db->moveNext()) {  
                          if($this->db->col['attribute_id'] == '5') {
                               $userData['fname'] = $this->db->col['value']; 
                          }
                          else {
                               $userData['lname'] = $this->db->col['value'];  
                          }   
                    }   
                /*****************end of code added for billing & shipping address *************>
                /**
                * @desc  New user, so add the information
                */
                // && $userData['quickbase_id']==0
                if ($userData['teamdesk_id'] == 0) {                                         
                    /**
                    * @desc  get the maximum customer id from the contacts table of Teamdesk
                    */
                    //$customerID    = $this->getTDMaxContactID();   
                    //$tmpcustomerID = $customerID +1;
                    $customerID = $this->getCustomerID($userData['entity_id']);
                    $arrUserDetails = array(
                                        'Customer ID' => $customerID,  
                                        'pinnacleId' => $userData['entity_id'],
                                        'First Name' => ucfirst($userData['fname']),
                                        'Last Name' => ucfirst($userData['lname']),
                                        'login' => $userData['email'],
                                        'E-mail Address' => $userData['email']
                                     );
                    array_push($this->datasetcreate->Rows, $arrUserDetails);    
                    /**
                    * @desc  uncheck the pinnacleId field, add record history in teamdesk for the processed products
                    */
                    try {
                         $id = $this->api->Create("Contact", $this->datasetcreate); // returns ids of the new record
                         $id = $id[0];
                          /**
                        * @desc  error in export
                        */
                        if ($id <= 0) {                   
                            $this->arrErrorLog[] = array("userName" => ucwords($userData['fname']." ".$userData['lname']),
                                                         "Error_Msg" => "Error adding record into teamdesk");
                        } 
                        else {
                            $this->totalUserAdded++;
                            /**
                            * @desc  update the teamdesk id in pinnacle cart
                            */
                            $this->db->reset();
                            $this->db->assignStr("teamdesk_id", $id);   
                            $this->db->assignStr("td_customer_id", $customerID);    
                            $this->db->update("customer_entity", "WHERE entity_id='".addslashes($userData['entity_id'])."'");
                        }
                   }     
                   catch (Exception $e) {
                        $this->tdErrorLog = '<br/>Caught exception: ' .$e->getMessage(). "<br/>";
                        $strReturn .= $this->tdErrorLog;           
                   } 
                }           
                else {
                        /**
                        * @desc  check that the sendToPinnacle flag is not set to true
                        */
                        $teamdeskid =  $userData['teamdesk_id']; 
                        $customerID =  $userData['td_customer_id']; 
                        $this->dataset->Rows[0]["@id"] = $teamdeskid; 
                        $this->dataset->Rows[0]['First Name'] = ucfirst($userData['fname']);
                        $this->dataset->Rows[0]['Last Name'] = ucfirst($userData['lname']);
                        $this->dataset->Rows[0]['login'] = $userData['email'];
                        $this->dataset->Rows[0]['E-mail Address'] = $userData['email'];
                        try {
                                $update_rec = $this->api->Upsert("Contact", $this->dataset);  
                         }                                    
                         catch (Exception $e) {
                                $this->tdErrorLog = '<br/>Caught exception: ' .$e->getMessage(). "<br/>";
                                $strReturn .= $this->tdErrorLog;           
                         }   
                        $this->totalUserUpdated++; 
                }
            }
            /**
            * @desc  update the user information from Teamdesk to Pinnacle 
            */
            //$this->updateRecordsFromTD();
            if ($order_cust_id > 0) {
               return $customerID;   
            }
            else {
                return $strReturn;                
            }            
        }
        else {
            return $this->tdErrorLog;     
        }
    }
    /**                                                                        

    * @desc Function to export the user's shipping address 

    * @param integer $userID user id    

    * @param integer $tdUserID customer ID from Teamdesk 

    * @since 08-Feb-2013

    * @author Dinesh Nagdev

    */

    function exportUserShippingAddress($userID=0, $tdUserID=0)

    {  

        /**

        * @desc code to get the schema of webprofile  from teamdesk

        */

        $this->shippingdatasetcreate->Rows = array(); 

        $this->shippingdataset->Rows = array();

        

        if ($userID > 0) {

            /**

            @desc get list of all shipping address of the user 

            */     

            $this->db->query("SELECT *

                            FROM ".DB_PREFIX."users_shipping  WHERE uid = '".$userID."' ORDER BY uid ASC");   

            $arrShipAddress = $this->db->getRecords();   

            foreach ($arrShipAddress as $key => $userShip) {   

                

                /**

                @desc get the country name and state name */    

                $countryName = $this->getCountryNameByID($userShip['country']);

                            

                /**

                * @desc  if country id is 1, country is USA, get the state else use the province value

                */

                if ($userShip['country'] == 1) { 

                    $stateName = $this->getStateNameByID($userShip['state']);

                }

                else {

                    $stateName = $userShip['province'];

                }   

                /**

                * @desc If teamdesk id greater than zero, update record                

                */

                if ($userShip['teamdesk_id'] > 0) { 

                        

                        $teamdeskid =  $userShip['teamdesk_id'];

                        $this->shippingdataset->Rows[0]["@id"] = $teamdeskid; 

                        $this->shippingdataset->Rows[0][$this->arrTDShipFields['uid']] = $tdUserID;

                        $this->shippingdataset->Rows[0][$this->arrTDShipFields['is_primary']] = $userShip['is_primary'];

                        $this->shippingdataset->Rows[0][$this->arrTDShipFields['name']] = ucwords($userShip['name'].' '.$userShip['lname']);

                        $this->shippingdataset->Rows[0][$this->arrTDShipFields['company']] = ucfirst($userShip['company']);

                        $this->shippingdataset->Rows[0][$this->arrTDShipFields['address1']] = ucfirst($userShip['address1']);

                        $this->shippingdataset->Rows[0][$this->arrTDShipFields['address2']] = ucfirst($userShip['address2']);

                        $this->shippingdataset->Rows[0][$this->arrTDShipFields['city']] = ucfirst($userShip['city']);

                        $this->shippingdataset->Rows[0][$this->arrTDShipFields['state']] = ucfirst($stateName);

                        $this->shippingdataset->Rows[0][$this->arrTDShipFields['province']] = $userShip['province'];

                        $this->shippingdataset->Rows[0][$this->arrTDShipFields['zip'] ] = $userShip['zip'];

                        $this->shippingdataset->Rows[0][$this->arrTDShipFields['country']] = ucfirst($countryName);

                        try {

                            $update_rec = $this->api->upsert("Shipping Address", $this->shippingdataset);  

                        }

                        catch (Exception $e) {

                            $this->tdErrorLog = '<br/>Caught exception: ' .$e->getMessage(). "<br/>";

                            $strReturn .= $this->tdErrorLog;           

                        }    

                } 

                else {

                   /**

                   * @desc  add record 

                   */

                   $arrRecords = array(                               

                                    $this->arrTDShipFields['usid'] => $userShip['usid'],

                                    $this->arrTDShipFields['uid'] => $tdUserID,

                                    $this->arrTDShipFields['is_primary'] => $userShip['is_primary'],   

                                    $this->arrTDShipFields['name'] => ucfirst($userShip['name']),   

                                    $this->arrTDShipFields['company'] => ucfirst($userShip['company']),

                                    $this->arrTDShipFields['address1'] => ucfirst($userShip['address1']),

                                    $this->arrTDShipFields['address2'] => ucfirst($userShip['address2']),

                                    $this->arrTDShipFields['city'] => ucfirst($userShip['city']),

                                    $this->arrTDShipFields['state'] => ucfirst($stateName),

                                    $this->arrTDShipFields['province'] => ucfirst($userShip['province']),

                                    $this->arrTDShipFields['zip'] => $userShip['zip'],

                                    $this->arrTDShipFields['country'] => ucfirst($countryName)

                                 );

                    array_push($this->shippingdatasetcreate->Rows, $arrRecords);     

                    try {

                         $shipping_address_id = $this->api->Create("Shipping Address", $this->shippingdatasetcreate); // returns ids of the new record

                         $shipping_address_id = $shipping_address_id[0];

                         /**

                        * @desc  error in export

                        */

                        if ($shipping_address_id <= 0) {                   

                        $this->arrErrorLog[] = array("userName"  => ucwords($userData['name']),

                                                     "Error_Msg" => "Shipping Address Import Error");

                        } else {                    

                            /**

                            * @desc  update the teamdesk record id in the teamdesk_id in the shipping address table of pinnacle

                            */

                            $this->db->reset();

                            $this->db->assignStr("teamdesk_id", $shipping_address_id);          

                            $this->db->update(DB_PREFIX."users_shipping", "WHERE usid='".addslashes($userShip['usid'])."'");

                        }                    

                   }     

                   catch (Exception $e) {

                        $this->tdErrorLog = '<br/>Caught exception: ' .$e->getMessage(). "<br/>";

                        $strReturn .= $this->tdErrorLog;           

                   } 

                }           

            } // end of foreach

        } // end of user id

    }      

    

    

    /**                                                                        

    * @desc Function to get the max customer id from Contacts table of Teamdesk.

    * @return integer $customerID, the maximum customer id value from Teamdesk

    * @since 06-Feb-2013

    * @author Dinesh Nagdev

    */

    function getTDMaxContactID()

    {

        $customerID = 0;

        

        //$arrQueries = "WHERE [Record ID#] > '0'"; 

        /**

        * @desc  id of the internal record id, required for updating the record in Teamdesk   

        */

        $strColumns = "[".$this->arrTDFields['record_id']."]";    

        $arrResults = $this->api->Query("SELECT MAX(".$strColumns.") FROM [Contact] ".$arrQueries." ORDER BY ".$strColumns);  

        if (isset($arrResults->Rows)) {

            $customerID = $arrResults->Rows[0]['Customer ID (max)'];

        }    

        return $customerID;

    }

    /**                                                                        

    * @desc Function to get the max customer id from Contacts table of Teamdesk.

    * @return integer $customerID, the maximum customer id value from Teamdesk

    * @since 06-Feb-2013

    * @author Dinesh Nagdev

    */

    function getTeamdeskid($quickbase_id)

    {              

        if($quickbase_id != '') {

            $arrQueries = "WHERE [Customer ID] = '".$quickbase_id."'"; 

            /**

            * @desc  id of the internal record id, required for updating the record in Teamdesk   

            */

            $strColumns = "[".$this->arrTDFields['customer_id']."]";    

            $arrResults = $this->api->Query("SELECT ".$strColumns." FROM [Contact] ".$arrQueries." ORDER BY ".$strColumns); 

            if (isset($arrResults->Rows)) {

                $teamdeskid = $arrResults->Rows[0]['@id'];

            }    

            return $teamdeskid;

        }    

    }

    function getShippingTeamdeskid($quickbase_id)

    {

        if($quickbase_id !='') {

            $arrQueries = "WHERE [Related Contact] = '".$quickbase_id."'"; 

            /**

            * @desc  id of the internal record id, required for updating the record in Teamdesk   

            */

            $strColumns = "[Related Contact]";    

            $arrResults = $this->api->Query("SELECT ".$strColumns." FROM [Shipping Address] ".$arrQueries." ORDER BY ".$strColumns); 

            if (isset($arrResults->Rows)) {

                $teamdeskid = $arrResults->Rows[0]['@id'];

            }    

            return $teamdeskid;

        }    

    }

    function getCustomerID($userID)
    {
        $customerID= '';
        $cust_id  = $userID;  
        $customerID = "SS".date("Y")."-".$cust_id;
        return $customerID;
    }

    /**                                                                        

    * @desc Function to get the country name based on the country id

    * @param integer $countryID, country ID for which the country name must be retrieved

    * @return string $countryName country name

    * @since 06-Feb-2013

    * @author Dinesh Nagdev

    */

    function getCountryNameByID($countryID=0)

    {

        $countryName = '';

        $this->db->query("SELECT name FROM ".DB_PREFIX."countries WHERE coid='".addslashes($countryID)."'");

        if ($this->db->moveNext()) { 

            $countryName  = $this->db->col['name'];  

        }

        return $countryName;

    }

    

    /**                                                                        

    * @desc Function to get the country id based on the country name

    * @param $countryName for which the country id must be retrieved

    * @return string $countryId

    * @since 11-Feb-2013

    * @author Dinesh Nagdev

    */

    function getCountryIDByName($countryName='')

    {

        $countryID = 0;

        $this->db->query("SELECT coid FROM ".DB_PREFIX."countries WHERE name='".addslashes($countryName)."'");

        if ($this->db->moveNext()) { 

            $countryID  = $this->db->col['coid'];  

        }

        return $countryID;

    }

    

    

    /**                                                                        

    * @desc Function to get the state name based on the state id

    * @param integer $stateID, state ID for which the state name must be retrieved

    * @return string $stateName state name 

    * @since 06-Feb-2013

    * @author Dinesh Nagdev

    */

    function getStateNameByID($stateID=0)

    {

        $stateName = '';         

        $this->db->query("SELECT short_name FROM ".DB_PREFIX."states WHERE stid='".addslashes($stateID)."' ");

        if ($this->db->moveNext()) { 

            $stateName   = $this->db->col['short_name'];  

        }

        

        return $stateName;

    }

    /**                                                                        

    * @desc Function to get the state id based on the state name

    * @param $stateShortName for which the state id must be retrieved

    * @return string $stateId

    * @since 11-Feb-2013

    * @author Dinesh Nagdev

    */

    function getStateByShortName($stateShortName='') 

    {

        $this->db->query("SELECT stid, name FROM ".DB_PREFIX."states WHERE short_name ='".addslashes($stateShortName)."'");

        if ($this->db->moveNext()) {

            return $this->db->col;

        } 

        return false;  

    }

    

    /**                                                                        

    * @desc Function to get the user type based on the user id

    * @param integer $UserID for which the user type must be retrieved

    * @return string $UserType 

    * @since 8-Feb-2013

    * @author Dinesh Nagdev

    */

    function getUserTypeByID($UserId=0)

    {

        $UserType = '';         

        $this->db->query("SELECT user_type FROM ".DB_PREFIX."users WHERE uid='".addslashes($UserId)."' ");

        if ($this->db->moveNext()) { 

            $UserType   = $this->db->col['user_type'];  

        }

        

        return $UserType;

    }  

    

    /**                                                                        

    * @desc Function to delete the user's shipping address from Teamdesk

    * @param integer $tdRecordID, teamdesk record id of the shipping address to be deleted

    * @return boolean true on successful deletion, false otherwise

    * @since 12-Mar-2013

    * @author Dinesh Nagdev

    */

    function deleteUserShipAddressInTD($tdRecordID=0)

    {

       $boolResult = false; 

       if ($tdRecordID > 0) {

          $boolResult = $this->connTDShipAddress->delete_record($tdRecordID);

       } 

       return $boolResult;

    }

    

    /**                                                                        

    * @desc Function to save the import log 

    * @param integer $totalUsersAdded total new users added

    * @param integer $totalUsersUpdated total users updated

    * @since 06-Feb-2013

    * @author Dinesh Nagdev

    */

    function saveImportLog($totalUsersAdded=0, $totalUsersUpdated=0, $totalUserUpdateFromTD=0)

    {                                                                                               

        global $settings;

        $strComment = '';

        $strRec  = 'Total Users added in Teamdesk:- <b>'.$totalUsersAdded.'</b> <br/> <br/> ';    

        $strRec  .= 'Total Users updated in Teamdesk:- <b>'.$totalUsersUpdated.'</b> <br/> <br/> ';        

        $strRec  .= 'Total Users updated from TeamDesk:- <b>'.$totalUserUpdateFromTD.'</b> <br/> <br/> '; 

        

        if (is_array($this->arrErrorLog)) {

           $strComment = '<br>'; 

           $strComment .= '<table cellpadding="0" cellspacing="1" width="95%" bgcolor="#cccccc">';  

           $strComment .= '<tr>

                                <td height="20" width="30%" style="padding-left:5px"><b>User</b></td>

                                <td height="20"width="70%" style="padding-left:5px" align="left"><b>Error Message</b></td>

                            </tr>';  

            

           foreach($this->arrErrorLog as $error) { 

              $strComment .= '<tr>

                               <td width="30%" height="20" style="background-color:#FFFFFF;padding-left:5px">'.$error["userName"].'</td>'.

                             ' <td width="70%" height="20" style="background-color:#FFFFFF;padding-left:5px">'.$error["Error_Msg"].'</td>'.

                             '</tr>';  

           }

           

           $strComment .= '</table>';        

        }

        

        $this->db->reset();              

        $this->db->assignStr("user_id", $_SESSION['admin_auth_id']);

        $this->db->assignStr("records_added", $totalUsersAdded);

        $this->db->assignStr("records_updated", $totalUsersUpdated);

        $this->db->assignStr("export_type", "User");

        $this->db->assignStr("comment", $strComment);

        $this->db->assignStr("log_date", date('Y-m-d h:i:s',time()));  

        $new_log_id = $this->db->insert(DB_PREFIX."tblteamdesk_export_log");  

        

        $subject = stripslashes($settings["CompanyName"].": "."User Export Status On ".date('Y-m-d h:i:s',time()));

        //send_mail(TD_UPDATE_MAIL_ID, $subject, $strRec.$strComment.$this->tdErrorLog,"",'html');

        

        return $strRec;

    }

    

    /**                                                                        

    * @desc Function to get the updated user information from TeamDesk to Pinnacle    

    * @since 11-Feb-2013

    * @author Dinesh Nagdev

    */

    function updateRecordsFromTD()

    {   

        $totalUsersUpdateFromTD = 0;

        

        /**

        * @desc  get all the records with sendToPinnacle marked as checked

        */

        $arrQueries = "WHERE [SendToPinnacle]";

          

        $arrResults = $this->api->Query("SELECT * FROM [Contact] ".$arrQueries);

                        

        if (isset($arrResults->Rows)) {

            

            /**

            * @desc  loop through each user and update the details in pinnacle and retreive the shipping address

            */

            foreach ($arrResults->Rows as $key => $tdUserDetail) { 

            

                $this->dataset->Rows = array();

                $userID =  $tdUserDetail['pinnacleId'];

                $tdRecordID = $tdUserDetail['Record ID#'];

                

                /**

                * @desc  update the user information

                */

                if ($tdUserDetail['Bill To Country'] == 'USA' || $tdUserDetail['Bill To Country'] == 'United States') {

                    $arrState = $this->getStateByShortName($tdUserDetail['Bill To State']);

                    $stateID  = $arrState['stid'];

                    $province = $arrState['name'];

                    $country  = $this->getCountryIDByName($tdUserDetail['Bill To Country']);

                }

                else {  

                    $province = $tdUserDetail['Bill To State'];

                    $stateID  = 0;

                    $country  = $this->getCountryIDByName($tdUserDetail['Bill To Country']);

                }

                unset($arrState);

                 

                $this->db->reset();              

                $this->db->assignStr("newsletters", $tdUserDetail['newsletters?']);

                $this->db->assignStr("updates", $tdUserDetail['productUpdates?']);

                $this->db->assignStr("fname", $tdUserDetail['First Name']);

                $this->db->assignStr("lname", $tdUserDetail['Last Name']); 

                $this->db->assignStr("company", $tdUserDetail['Company 2']);

                $this->db->assignStr("address1", $tdUserDetail['Bill To Address 1']);

                $this->db->assignStr("address2", $tdUserDetail['Bill To Address 2']);

                $this->db->assignStr("city", $tdUserDetail['Bill To City']);

                $this->db->assignStr("state", $stateID);

                $this->db->assignStr("province", $province);

                $this->db->assignStr("zip", $tdUserDetail['Bill To Zip']);

                $this->db->assignStr("country", $country);

                $this->db->assignStr("email", $tdUserDetail['E-mail Address']);

                $this->db->assignStr("email_mode", $tdUserDetail['email_mode']);

                $this->db->assignStr("phone", $tdUserDetail['Phone']); 

                $this->db->assignStr("last_update", date('Y-m-d h:i:s',time())); 

                $this->db->update(DB_PREFIX."users","WHERE uid = '".$tdUserDetail['pinnacleId']."' "); 

            

                $this->totalUserUpdateFromTD++;

                

                /**

                * @desc get all shipping address information from Teamdesk

                */

                $arrQueries = "WHERE [Related Contact] = ".$tdUserDetail['Customer ID'];                           

                

                $arrShipAddResults = $this->api->Query("SELECT * FROM [Shipping Address] ".$arrQueries);  

                

                foreach ($arrShipAddResults->Rows as $key => $tdUserShipAddDetail) {  

                    $this->shippingdataset->Rows = array();

                    $strRecMsg = '';   

                    $tdShipRecordID = 0; 

                    $pinnacleShipAddressID = 0;

                    $newShipAddID = 0;

                    

                    /**

                    * @desc  insert/ update the shipping address information           

                    */

                    $tdShipRecordID =  (int)$tdUserShipAddDetail['Record ID#'];

                    $pinnacleShipAddressID = (int)$tdUserShipAddDetail['pinnacleId']; 

                    

                   /**

                   * @desc  update the user information

                   */

                    if ($tdUserShipAddDetail['country'] == 'USA' || $tdUserShipAddDetail['country'] == 'United States') {

                        $arrState = $this->getStateByShortName($tdUserShipAddDetail['state']);

                        $stateID  = $arrState['stid'];

                        $province = $arrState['name'];

                        $country  = $this->getCountryIDByName($tdUserShipAddDetail['country']);

                    }

                    else {  

                        $province = $tdUserShipAddDetail['state'];

                        $stateID  = 0;

                        $country  = $this->getCountryIDByName($tdUserShipAddDetail['country']);

                    }

                    unset($arrState); 

                    

                    $this->db->reset();              

                    $this->db->assignStr("is_primary", $tdUserShipAddDetail['is_primary']);

                    $this->db->assignStr("name", $tdUserShipAddDetail['name']);

                    $this->db->assignStr("company", $tdUserShipAddDetail['company']);

                    $this->db->assignStr("address1", $tdUserShipAddDetail['address1']); 

                    $this->db->assignStr("address2", $tdUserShipAddDetail['address2']);

                    $this->db->assignStr("city", $tdUserShipAddDetail['city']);

                    $this->db->assignStr("state", $stateID);

                    $this->db->assignStr("province", $province);

                    $this->db->assignStr("zip", $tdUserShipAddDetail['zip']);

                    $this->db->assignStr("country", $country);   

                    $this->db->assignStr("last_td_updated_date", date('Y-m-d h:i:s',time()));    

                    

                    if ($pinnacleShipAddressID == 0) {   

                        $newShipAddID = $this->db->insert(DB_PREFIX."users_shipping");

                        

                        $strRecMsg = TD_RECORD_HISTORY_ADDED;

                    }

                    else {

                        $this->db->update(DB_PREFIX."users_shipping","WHERE usid = '".$tdUserShipAddDetail['pinnacleId']."' "); 

                        $strRecMsg = TD_RECORD_HISTORY_UPDATED;

                        $totalShipAddressUpdateFromTD++;     

                    } 

                    

                    $this->shippingdataset->Rows[0]["@id"] = $tdUserShipAddDetail['@id'];

                    $this->shippingdataset->Rows[0]["sendToPinnacle"] = "0";

                    $this->shippingdataset->Rows[0]["RecordHistory"] = date("Y-m-d H:i:s").' - '.$strRecMsg;

                    

                    $update_rec = $this->api->Upsert("Shipping Address", $this->shippingdataset);  

                }  

                /**

                * @desc  uncheck the sendToPinnacle and save record history in user   

                */

                $this->dataset->Rows[0]["@id"] = $tdUserDetail['@id']; 

                $this->dataset->Rows[0]["RecordHistory"] = date("Y-m-d H:i:s").' - '.TD_RECORD_HISTORY_UPDATED;

                $this->dataset->Rows[0]["SendToPinnacle"] = "0";

                $update_rec = $this->api->Upsert("Contact", $this->dataset);  

                

            } // end of foreach user    

        }     

        

    } // end of updateRecordsFromTD function     

    

}





?>                                           

