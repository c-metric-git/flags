<?php
/**
* @package TeamDesk
* @version v1.0
*
* @author		ForeSoft Corporation
* @copyright	Copyright (c) ForeSoft Corporation 2012
*/

ini_set('max_execution_time', 0);
class TDAPI
{
    public $domain;
    public $appId;
    public $options = array("trace" => true);
    
    public function __construct()
    {        
        $this->domain   = TD_DOMAIN;
        $this->appId    = TD_API_KEY;
        $this->email    = TD_EMAIL;
        $this->password = TD_PASSWORD;        
        
        if(is_int($this->domain)) 
        { 
            $this->options = $this->appId; $this->appId = $this->domain; $this->domain = "www.teamdesk.net";
        }
        
        if(substr_compare($this->domain, "http://", 0) && substr_compare($this->domain, "https://", 0))
        {
            $url = "https://$this->domain/secure/api/$this->appId/service.asmx";
        }
        else
        {
            $url = $this->domain; $this->options = $this->appId;
        }
        $r = array("features" => SOAP_SINGLE_ELEMENT_ARRAYS /*"location" => $url, "uri" => self::api_ns*/);
        if($this->options != null) $r += $this->options;
        $wsdl = $url . "?wsdl";
        $this->soap = new SoapClient($wsdl, $r);
    }
	
    public function __destruct()
    {
        $this->soap = null;
    }
	
    protected function doCall($method, array $parameters = array())
    {
        $r = $this->soap->__soapCall((string)$method, array($parameters));
        $f = $method . "Result";
        if(isset($r->$f))
            return $r->$f;
    }

    /**
    * Performs authorization
    *
    * Should be first method to call
    *
    * @return	stdClass	User and session information
    *
    * @param	string		$this->email		User email
    * @param	string		$this->password	User password
    *
    *
    * Example:
    *
    * <code>
    * $api = new TeamDesk\API(12345);
    * $u = $api->Login("address\@server.com", "password");
    * echo "Hello " . $u->FirstName;
    * </code>
    *
    * @category API Call
    *
    */
    public function Login()
    {
        $h = array();
        $r = $this->soap->__soapCall("Login", array(array(
                "email" => (string)$this->email,
                "password" => (string)$this->password
                )), null, null, $h);
        $this->soap->__setSoapHeaders(self::patchHeaders($h));
        return $r->LoginResult;
    }

    /**
    * Logs the user off by clearing session headers
    *
    * Example:
    * <code>
    * $api->Login("address@server.com", "password");
    * // do some stuff
    * $api->Logout();
    * </code>
    *
    * @category Utility
    *
    */
    public function Logout()
    {
        $this->soap->__setSoapHeaders(array());
    }

    /**
    * Retrieves current user's information
    *
    * @return	stdClass			User and session information
    *
    * Example:
    * <code>
    * // Display user's first and last name
    * $info = $api->GetUserInfo();
    * echo $info->FirstName . " " . $info->LastName
    * </code>
    *
    * @category API Call
    *
    */
    public function GetUserInfo()
    {
        return $this->doCall("GetUserInfo");
    }

    /**
    * Retrieves application's and tables' basic information 
    *
    * @return	stdClass	Application and tables' information
    *
    * <code>
    * // Display first table's name from the app
    * $info = $api->DescribeApp();
    * echo $info->Tabs[0]->RecordName
    * </code>
    *
    * @category API Call
    *
    */
    public function DescribeApp()
    {
        $o = $this->doCall("DescribeApp");
        $o->Tabs = $o->Tabs->TabInfo;
        return $o;
    }

    /**
    * Retrieves metadata (table and column properties) for specified table
    *
    * Example:
    *
    * <code>
    * // Display whether new records are allowed
    * $info = $api->DescribeTable("Table");
    * echo $info->AllowAdd
    * </code>
    *
    *
    * @return	stdClass		Table's metadata
    *
    * @param	string	$table	Table name to retrieve the information for.
    *
    * @category API Call
    */
    public function DescribeTable($table)
    {
        $o = $this->doCall("DescribeTable", array(
                "table" => (string)$table
                ));
        $o->Columns = $o->Columns->ColumnInfo;
        $o->Views = $o->Views->ViewInfo;
        $o->Forms = $o->Forms->FormInfo;
        return $o;
    }

    /**
    * Array-based version of {@link DescribeTable}
    *
    * Retrieves metadata (table and column properties) for specified tables 
    *
    * <code>
    * // Display whether new records are allowed
    * $info = $api->DescribeTables(array("Table1", "Table2", "Table3"));
    * echo $info[0]->AllowAdd
    * </code>
    *
    * @return	stdClass		Tables' metadata
    *
    * @param	array	$tables	Array of table names to retrieve the information for.
    *
    * @category API Call
    *
    */
    public function DescribeTables(array $tables)
    {
        $o = $this->doCall("DescribeTables", array(
                "tables" => $tables
                ));
        foreach($o->DescribeTableResult as $x)
        {
            $x->Columns = $x->Columns->ColumnInfo;
            $x->Views = $x->Views->ViewInfo;
            $x->Forms = $x->Forms->FormInfo;
        }
        return $o;
    }

    /**
    * Executes a query against the specified table and returns data that matches the specified criteria.
    *
    * <pre>SELECT [ TOP n ] column-names | * FROM table [ WHERE condition ] [ ORDER BY column {ASC|DESC}, ... ]</pre>
    *
    * <code>
    * // selects recently modified record
    * $ds = $api->Query("SELECT TOP 1 * FROM [Table] ORDER BY [Date Modified] DESC");
    * // selects name from own leads
    * $ds = $api->Query("SELECT [Name], [Address] FROM [Lead] WHERE [Record Owner] = User()");
    * // calculates count of active records in the table
    * $ds = $api->Query("SELECT Count([Id]) as [Count] FROM [Table] WHERE [Active]")
    * </code>
    *
    * @return	DataSet
    *
    * @param	string	$query		The query to execute.
    *
    * @category API Call
    *
    * @see Retrieve, Create, Update, Upsert, Delete
    */
    public function Query($query)
    {
        $o = $this->doCall("Query", array(
                "query" => (string)$query
                ));
        return DataSet::fromXml($o->any);
    }

    /**	
    * Retrieves one or more records based on the specified record IDs
    *
    * <code>
    * // retrieve the record
    * $ds = $api->Retrieve("Lead", array("Name"), array(1));
    * // update the name
    * $ds->Rows[0]->Name .= "X";
    * // update the record
    * $api->Update("Lead", $ds);
    * </code>
    *
    * @return	DataSet
    *
    * @param	string $table		The table to retrieve records from
    * @param	array $columns		An array of column names to retrieve
    * @param	array $ids			An array of integer ids to retrieve
    *
    * @category API Call
    *
    * @see Query, Create, Update, Upsert, Delete
    */
    public function Retrieve($table, array $columns, array $ids)
    {
        $o = $this->doCall("Retrieve", array(
                "table" => (string)$table,
                "columns" => $columns,
                "ids" => $ids
                ));
        return DataSet::fromXml($o->any);
    }

    /**	
    * Creates one or more records
    *
    * Example:
    * <code>
    * // retrieve the schema
    * $ds = $api->GetSchema("Lead");
    * // insert a row
    * $ds->Rows[0] = array("Name" => "New Lead");
    * // create the record
    * $api->Create("Lead", $ds);
    * </code>
    *
    * @param	string		$table		The table to create records in
    * @param	DataSet		$data		A data set of records to create
    *
    * @return	array					An array of internal ids for all records created
    *
    * @see GetSchema, Query, Retrieve, Update, Upsert, Delete
    *
    * @category API Call
    */
    public function Create($table, DataSet $data)
    {
        $o = $this->doCall("Create", array(
                "table" => (string)$table,
                "data" => new SoapVar($data->toXml(), XSD_ANYXML, null, null, "data", self::api_ns)
                ));
        return isset($o->int) ? $o->int : array();
    }

    /**	
    * Updates one or more records based on the specified record IDs
    *
    * @param	string			$table		The table to update records in
    * @param	DataSet $data		A data set of records to update
    *
    * @category API Call
    */
    public function Update($table, DataSet $data)
    {     
        $this->doCall("Update", array(
                "table" => (string)$table,
                "data" => new SoapVar($data->toXml(), XSD_ANYXML, null, null, "data", self::api_ns)
                ));
    }

    /**	
    * Creates or updates one or more records based on the specified record IDs
    *
    * @return	array				An array of internal ids for all records processed
    *
    * @param	string	$table		The table to update or create records in
    * @param	DataSet $data		A data set of records to process
    *
    * @category API Call
    */
    public function Upsert($table, DataSet $data)
    {
        $o = $this->doCall("Upsert", array(
                "table" => (string)$table,
                "data" => new SoapVar($data->toXml(), XSD_ANYXML, null, null, "data", self::api_ns)
        ));
        return isset($o->int) ? $o->int : array();
    }

    /**	
    * Deletes one or more records based on the specified record IDs
    *
    * @param	string	$table		The table to delete records from
    * @param	array	$ids		An array of record ids to delete
    *
    * @category API Call
    */
    public function Delete($table, array $ids)
    {
        $this->doCall("Delete", array(
                "table" => (string)$table,
                "ids" => $ids
                ));
    }

    /**	
    * Retrieves identifiers of the records updated within certain time range
    *
    * @return	array						Identifiers of updated records
    *
    * @param	string			$table		The table to check records in
    * @param	int|DateTime	$startTime	A start of time range in GMT timezone
    * @param	int|DateTime	$endTime	An end of time range in GMT timezone
    *
    * @category API Call
    */
    public function GetUpdated($table, $startTime = 0, $endTime = 3155760000)
    {
        $o = $this->doCall("GetUpdated", array(
                        "table" => (string)$table,
                        "startTime" => self::d2s($startTime, "c"),
                        "endTime" => self::d2s($endTime, "c")
                        ));
        return isset($o->int) ? $o->int : array();
    }

    /**	
    * Retrieves identifiers of the records deleted within certain time range
    *
    * @return	array						Identifiers of deleted records
    *
    * @param	string			$table		The table to check records in
    * @param	int|DateTime	$startTime	A start of time range in GMT timezone
    * @param	int|DateTime	$endTime	An end of time range in GMT timezone
    *
    * @category API Call
    */
    public function GetDeleted($table, $startTime = 0, $endTime = 3155760000)
    {
        $o = $this->doCall("GetDeleted", array(
                        "table" => (string)$table,
                        "startTime" => self::d2s($startTime, "c"),
                        "endTime" => self::d2s($endTime, "c")
                        ));
        return isset($o->int) ? $o->int : array();
    }

    /**
    * Retrieves an attachment information from the record
    * 
    * @param	string	$table		A table to retrieve attachment from
    * @param	string	$column		A name of the column
    * @param	int		$id			A record identifier
    * @param	string	$revisions	A number of last revisions to retrieve. If omited, last revision is returned.
    *
    * @category API Call
    */
    public function GetAttachmentInfo($table, $column, $id, $revisions = 1)
    {
        $o = $this->doCall("GetAttachmentInfo", array(
                        "table" => (string)$table,
                        "column" => (string)$column,
                        "id" => (int)$id,
                        "revisions" => (int)$revisions
                        ));
        return isset($o->AttachmentInfo) ? $o->AttachmentInfo : array();
    }

    /**
    * Retrieves an attachment from the record
    *
    * Returns attachment information as in GetAttachmentInfo() plus file data
    * 
    * @param	string	$table		A table to retrieve attachment from
    * @param	string	$column		A name of the column
    * @param	int		$id			A record identifier
    * @param	string	$revision	Optional revision, if omited, latest revision is retrieved
    *
    * @category API Call
    */
    public function GetAttachment($table, $column, $id, $revision = 0)
    {
        $o = $this->doCall("GetAttachment", array(
                        "table" => (string)$table,
                        "column" => (string)$column,
                        "id" => (int)$id,
                        "revision" => (int)$revision
                        ));
        $o->Data = base64_decode($o->Data);
        return $o;
    }

    /**
    * Adds an attachment to the record
    * 
    * @param	string	$table		A table to update
    * @param	string	$column		A name of the column to update
    * @param	int		$id			A record identifier to update
    * @param	string	$fileName	A name of the file
    * @param	string	$mimeType	A MIME type of the file
    * @param	string	$data		File content as returned from file_get_contents()
    *
    * @category API Call
    */
    public function SetAttachment($table, $column, $id, $fileName, $mimeType, $data)
    {
        $this->doCall("SetAttachment", array(
                "table" => (string)$table,
                "column" => (string)$column,
                "id" => (int)$id,
                "fileName" => (string)$fileName,
                "mimeType" => (string)$mimeType,
                "data" => base64_encode((string)$data)
                ));
    }

    /**
    * Retrieves empty DataSet, containing schema information. 
    * Can be used to obtain properly created DataSet to insert new record into.
    *
    * @return	DataSet				Empty data set with schema
    *
    * @param	string	$table		The name of the table
    * @param	array	$columns	List of column names to retrieve. If omited, retrieves all columns
    *
    * @category Utility
    */
    public function GetSchema($table, array $columns = null)
    {
        return count($columns) == 0 ?
                $this->Query("SELECT TOP 1 * FROM " . self::QName($table) . " WHERE false") :
                $this->Retrieve($table, $columns, array(0));
    }

    /**
    * Encodes brackets in the name and adds brackets around the name
    *
    * @return	string			Encoded name
    *
    * @param	string			$name	Name to encode
    *
    * @category Utility
    *
    * @see QString, QDate, QTime, QDateTime
    */
    public static function QName($name)
    {
        return '[' . str_replace(']', '\\]', str_replace('\\', '\\\\', (string)$name)) . ']';
    }

    /**
    * Encodes quotes and backslashes in the string and adds quotes around the string
    *
    * @return	string			Encoded string
    *
    * @param	string	$value	String to encode
    *
    * @category Utility
    *
    * @see QName, QDate, QTime, QDateTime
    */
    public static function QString($value)
    {
        return '"' . str_replace('\n', '\\n', str_replace('"', '\\"', str_replace('\\', '\\\\', (string)$value))) . '"';
    }

    /**
    * Formats date constant
    *
    * @return	string			Encoded date constant
    *
    * @param	int|DateTime	$value	Value to encode
    *
    * @category Utility
    *
    * @see QName, QString, QDateTime, QTime
    */
    public static function QDate($value)
    {
        return self::d2s($value, "#Y-m-d#");
    }

    /**
    * Formats time constant
    *
    * @return	string			Encoded time constant
    *
    * @param	int|DateTime	$value	Value to encode
    *
    * @category Utility
    *
    * @see QName, QString, QDate, QDateTime
    */
    public static function QTime($value)
    {
        return self::d2s($value, "#H:i:s#");
    }

    /**
    * Formats timestamp constant
    *
    * @return	string			Encoded timestamp constant
    *
    * @param	int|DateTime	$value	Value to encode, in GMT timezone
    *
    * @category Utility
    *
    * @see QName, QString, QDate, QTime
    */
    public static function QDateTime($value)
    {
        return self::d2s($value, "#Y-m-d H:i:s#");
    }

    /**
    * Patches header's data namespaces
    * 
    * @ignore 
    */
    protected static function patchHeaders($hh)
    {
        $h = array();
        foreach($hh as $hn=>$hv)
        {
            $v = new stdClass;
            foreach($hv as $vn=>$vv)
                    $v->$vn = new SoapVar((string)$vv, XSD_STRING, null, null, $vn, self::api_ns);
            array_push($h, new SoapHeader(self::api_ns, $hn, $v));
        }
        return $h;
    }

    /**
    * @ignore Formats date/time
    *
    * @return	string						Formatted date/time
    *
    * @param	int|DateTime	$value		Value to format
    * @param	string			$format		Format specifier
    */
    public static function d2s($value, $format)
    {
        return is_object($value) ? $value->format($format) : date($format, $value);
    }

    /**
    * @ignore Dumps last SOAP request
    * @category Debugging
    */
    public function dumpRequest()
    {
        echo "<pre>" . htmlspecialchars($this->soap->__getLastRequest()) . "</pre>";
    }

    /**
    * @ignore Dumps last SOAP response
    * @category Debugging
    */
    public function dumpResponse()
    {
        echo "<pre>" . htmlspecialchars($this->soap->__getLastResponse()) . "</pre>";
    }

    /** 
    * @var SoapClient	A reference to SoapClient
    */
    protected $soap;

    /** 
    * @ignore TeamDesk's SOAP API namespace
    */
    const api_ns = "urn:soap.teamdesk.net";

    /** @ignore XSD type for Text columns */
    const Text = "xs:string";
    /** @ignore XSD type for Multi-Line Text columns */
    const Multiline = "xs:string";
    /** @ignore XSD type for Boolean/Checkbox columns */
    const Boolean = "xs:boolean";
    /** @ignore XSD type for Date columns */
    const Date = "xs:date";
    /** @ignore XSD type for Time columns */
    const Time = "xs:time";
    /** @ignore XSD type for Number columns */
    const Number = "xs:decimal";
    /** @ignore XSD type for User columns */
    const User = "xs:string";
    /** @ignore XSD type for Autonumber columns */
    const Autonumber = "xs:string";
    /** @ignore XSD type for Timestamp columns */
    const Timestamp = "xs:dateTime";
    /** @ignore XSD type for Duration columns */
    const Duration = "xs:duration";
    /** @ignore XSD type for Phone columns */
    const Phone = "xs:string";
    /** @ignore XSD type for Email columns */
    const Email = "xs:string";
    /** @ignore XSD type for URL columns */
    const URL = "xs:anyURI";
    /** @ignore XSD type for Attachment columns [read only] */
    const Attachment = "xs:string";
}
?>
