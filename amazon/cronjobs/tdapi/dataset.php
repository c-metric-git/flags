<?php
/**
* @package TeamDesk
* @version v1.0
*
* @author		ForeSoft Corporation
* @copyright	Copyright (c) ForeSoft Corporation 2012
*/

/**
* Wraps XML into objects
*
* Returned by: {@link TDAPI::Query}, {@link TDAPI::Retrieve}
*
* Accepted by: {@link TDAPI::Create}, {@link TDAPI::Update}, {@link TDAPI::Upsert}
*/
ini_set('max_execution_time', 0);
class DataSet
{
    /**
    * Default Constructor
    * @param	array	$columns	A list of columns to add in a { name=>type } format
    */
    public function __construct(array $columns = array())
    {
        $this->Rows = array();
        $this->Columns = array();
        foreach($columns as $k=>$v)
                $this->AddColumn($k, $v);
    }

    /**
    * @var array Array of objects Name => { string Type; boolean AllowNull; boolean ReadOnly; }
    */
    public $Columns;

    /**
    * @var array An array of rows. 
    * 
    * Each row is an associative name=>value array 
    * where "name" is a column name and the value is an string, bool, int or float 
    * according to column's data type. Use:
    *
    * + string			for Text, Text - Multiline, E-Mail, Phone, URL columns
    * + bool				for Checkbox columns
    * + float				for Numeric columns
    * + int|DateTime		for Date/Time/TimeStamp columns. See also PHP time() and mktime() function family
    * + int				for Duration columns - the number of seconds
    * + string			for User columns - the user's email address
    *
    *
    * There are couple of "special" names returned by Query/Retrieve calls
    * + @id - an internal id of the record; those are passed to Retrieve/Delete calls and returned by Create/GetUpdated/GetDeleted
    * + @m - boolean value to indicate modifications are allowed for the record
    * + @d - boolean value to indicate whether record can be deleted
    * + @c - string containing the record color as defined in table colorization formula
    *
    *
    * <code>
    * // set data for new row
    * $new = array(
    * 	"Text" => "Test 1",
    * 	"Multiline" => "Line1\r\nLine2",
    * 	"Checkbox" => true,
    * 	"Date" =>  mktime(0, 0, 0, 1, 1, 2001), // or DateTime object
    * 	"Time" => mktime(10, 30, 0), // or DateTime object
    * 	"Number" => 2.5,
    * 	"Email" => "anonymous@teamdesk.net",
    * 	"Phone" => "123-123456",
    * 	"URL" => "http://www.teamdesk.net",
    * 	"User" => "anonymous@teamdesk.net",
    * 	"Duration" => 2 * 24 * 60 * 60, // 2 days - in seconds
    * 	"Timestamp" => time());
    * // add new row to a DataSet
    * array_push($dataSet->Rows, $new);
    * </code>
    */
    public $Rows;

    /**
    * Constructs DataSet from XML
    * 
    * @return	DataSet
    * @param	string	$str		An XML string
    * @ignore
    */ 
    static public function fromXml($str)
    {
        $xml = new XMLReader();
        $xml->XML((string)$str);

        $ds = new DataSet();

        if($xml->read() && // DataSet
           $xml->read() && // xs:schema
           $xml->read() && // xs:element
           $xml->read() && // xs:complexType
           $xml->read() && // xs:choice
           $xml->read() && // xs:element
           $xml->read() && // xs:complexType
           $xml->read() // xs:sequence
        ) // DataSet
        {
            while($xml->read() && $xml->nodeType == XMLReader::ELEMENT && $xml->localName=="element")
            {
                $xml->moveToAttribute("name"); $name = $xml->value;
                $xml->moveToAttribute("type"); $type = $xml->value;
                $xml->moveToAttribute("minOccurs"); $allowNull = $xml->value == "0";
                $xml->moveToAttributeNs("Caption", "urn:schemas-microsoft-com:xml-msdata"); $caption = $xml->value;
                $xml->moveToAttributeNs("ReadOnly", "urn:schemas-microsoft-com:xml-msdata"); $readOnly = $xml->value == "true";
                $ds->AddColumn($caption, $type, $readOnly, $allowNull, $name);
                $cn[$name] = array("name" => $caption, "type" => $type);
            }
            // read up to "Data"
            while($xml->read() && ($xml->nodeType != XMLReader::ELEMENT || $xml->localName != "Data")) {}
            while($xml->read() && $xml->nodeType == XMLReader::ELEMENT && $xml->localName == "r")
            {
                $r = array();
                if(($v = $xml->getAttribute("id")) != null) $r["@id"] = intval($v);
                if(($v = $xml->getAttribute("modify")) != null) $r["@m"] = $v == "true";
                if(($v = $xml->getAttribute("delete")) != null) $r["@d"] = $v == "true";
                if(($v = $xml->getAttribute("c")) != null) $r["@c"] = $v;
                if(!$xml->isEmptyElement && $xml->read())
                {
                    while($xml->nodeType == XMLReader::ELEMENT)
                    {
                        $c = $cn[$xml->localName];
                        if(!$xml->isEmptyElement)
                                $r[$c["name"]] = self::valueFromXml($c["type"], $xml->readString());
                        $xml->next();
                    }
                }
                array_push($ds->Rows, $r);
            }
        }
        return $ds;
    }

    /**
    * Adds column to the DataSet
    *
    * @param	string	$name	The name of the column to remove
    * @param	string	$type	One of XSD types. @see TDAPI::Text constants
    */
    public function AddColumn($name, $type, $readOnly = false, $allowNull = true, $tagName = null)
    {
        $c = new stdClass();
        $c->Type = (string)$type;
        $c->ReadOnly = (bool)$readOnly;
        $c->AllowNull = (bool)$allowNull;
        if($tagName == null)
        {
            $tagName = "";
            $t = iconv("UTF-8", "UTF-16", $name);
            $l = iconv_strlen($t, "UTF-16");
            for($i = 0, $o = 3; $i < $l; $i++, $o+=2)
            {
                $c1 = ord($t[$o]);
                $c2 = ord($t[$o-1]);

                if($c2 == 0 && ($c1 >= 48 && $c1 <= 57 || $c1 >= 65 && $c1 <= 90 || $c1 >= 97 && $c1 <= 122 || $c1 == 95 || $c1 == 45))
                {	// [0-9A-Za-z_-]
                    $tagName .= chr($c1);
                }
                else
                {	// _xHHHH_ where HHHH is UTF16 char code
                    $tagName .= sprintf("_x%04X_", $c2 * 256 + $c1);
                }
            }
        }
        $c->__tagName = $tagName;
        $this->Columns[$name] = $c;
    }

    /**
    * Removes column from the DataSet
    *
    * @param	string	$name	The name of the column to remove
    */
    public function RemoveColumn($name)
    {
        unset($this->Columns[$name]);
    }

    /**
    * Dumps DataSet to XML
    * @ignore
    * @return	string	XML as string
    */
    public function toXml()
    {
        $xml = new XMLWriter();
        $xml->openMemory();
        $xml->startElementNS("ns1", "data", null);
        $xml->startElementNS(null, "DataSet", TDAPI::api_ns . ":dataset");
        $xml->startElementNS("xs", "schema", "http://www.w3.org/2001/XMLSchema");
        $xml->writeAttribute("targetNamespace", TDAPI::api_ns . ":dataset");
        $xml->writeAttribute("elementFormDefault", "qualified");
        $xml->startElementNS("xs", "element", null);
        $xml->writeAttribute("name", "DataSet");
        $xml->startElementNS("xs", "complexType", null);
        $xml->startElementNS("xs", "choice", null);
        $xml->writeAttribute("maxOccurs", "unbounded");        
        $xml->startElementNS("xs", "element", null);
        $xml->writeAttribute("name", "r");
        $xml->startElementNS("xs", "complexType", null);
        $xml->startElementNS("xs", "sequence", null);
        $xml->writeAttribute("minOccurs", "0");
        $xml->writeAttribute("maxOccurs", "unbounded");
        foreach($this->Columns as $c)
        {
            $xml->startElementNS("xs", "element", null);
            $xml->writeAttribute("name", $c->__tagName);
            $xml->writeAttribute("type", $c->Type);
            $xml->endElement();            
        }
        $xml->endElement();
        $xml->startElementNS("xs", "attribute", null);
        $xml->writeAttribute("name", "id");
        $xml->writeAttribute("type", "xs:int");
        $xml->endElement();
        $xml->endElement();
        $xml->endElement();
        $xml->endElement();
        $xml->endElement();
        $xml->endElement();
        $xml->endElement();
        $xml->startElement("Data");
        foreach($this->Rows as $r)
        {
            $xml->startElement("r");
            if(isset($r["@id"]))
                $xml->writeAttribute("id", $r["@id"]);
            foreach($this->Columns as $n=>$c)
            {
                if(isset($r[$n]))
                    $xml->writeElement($c->__tagName, self::valueToXml($c->Type, $r[$n]));
            }
            $xml->endElement();
        }
        $xml->endElement();
        $xml->endElement();
        $xml->endElement(); 
        return $xml->outputMemory(true);
    }

    /**
    * Encodes value as XML string
    *
    * @return	string
    *
    * @param	string	$type	One of XSD data types
    * @param	mixed	$value	The value to encode
    * 
    * @ignore 
    */
    private static function valueToXml($type, $value)
    {
        switch($type)
        {
            case "xs:string":
            case "xs:anyURI":	// string for us
                return strval($value);
            case "xs:boolean":	// true|false
                return $value == true ? "true" : "false";
            case "xs:decimal":	// n
                return number_format($value, 6, '.', '');
            case "xs:date":		// yyyy-mm-dd
                return TDAPI::d2s($value, "Y-m-d");
            case "xs:time":		// hh:mm:ss
                return TDAPI::d2s($value, "H:i:s");
            case "xs:dateTime":
                return TDAPI::d2s($value, "c");
            case "xs:duration":	// {-}PTnS
                $value = intval($value);
            return $value < 0?"-PT":"PT" . $value . "S";
        }
    }

    /**
    * Decodes value from XML string
    *
    * @return	mixed			The value as native PHP type
    *
    * @param	string	$type	One of XSD data types
    * @param	string	$value	The value to decode
    *
    * @ignore 
    */
    private static function valueFromXml($type, $xml)
    {
        switch($type)
        {
            case "xs:string":
            case "xs:anyURI":	// string for us
                return $xml;
            case "xs:boolean":	// true|false
                return $xml == "true";
            case "xs:decimal":	// n
                return floatval($xml);
            case "xs:duration":	// {-}PTnS
                $s = 1; $o = 2;
                if(substr($xml, 0, 1) == "-") { $s = -1; $o++; }
                return $s * intval(substr($xml, $o));
            case "xs:date":		// yyyy-mm-dd
            case "xs:dateTime": // yyyy-mm-ddThh:mm:ss
                return new DateTime($xml);
            case "xs:time":		// hh:mm:ss -> #seconds since midnight
                return strtotime($xml, 0);
        }
    }
}
?>