<?php

/**

 * @class DB

 */

class DB{

	public $host;

	public $user_name;

	public $password;

	public $db_name;

	

	public $cacheDir = 'content/cache/tmp/';



	public $link_id;

	public $result;

	public $col;

	public $query;

	public $fields;



	/**

	 * Creates a new database connection

	 *

	 * @param string $_host

	 * @param string $_user

	 * @param string $_password

	 * @param string $_db_name

	 */

	public function init($_host, $_user, $_password, $_db_name)

	{

		//file_put_contents("content/cache/log/mysql.sql", "NEW SESSION\n=====================================\n\n", FILE_APPEND);

		$this->host = $_host;

		$this->user_name = $_user;

		$this->password = $_password;

		$this->db_name = $_db_name;

		$this->fields = array();

		

		$this->link_id = @mysql_connect($_host, $_user, $_password)

		or $this->error("Can not connect to database. Please check database access information.");

		

		if (!@mysql_select_db($_db_name, $this->link_id))

		{

			$this->error("Can not select database. Please check database access information.");

		}

		else

		{

			if (function_exists('mysql_set_charset'))

			{

				mysql_set_charset('utf8', $this->link_id);

			}

			else

			{

				mysql_query("SET NAMES 'utf8'", $this->link_id);

			}

		}

	}

	

	/**

	 * Show database error

	 *

	 * @param string $msg

	 */

	public function error($msg)

	{

		echo '<div style="border:2px solid black;font-family:tahoma;font-size:16px;padding:20px;text-align:center;color:yellow;background-color:#CC0000;">'.htmlspecialchars($msg).'</div>';

		$db_err = "error";
        if($this->link_id) {$db_err = mysql_error($this->link_id);}
        $msg = date("c")."\n".$this->query."\n\n".$db_err."\n\n";

		if (defined("DEVMODE"))

		{

			echo '<div style="border:2px solid black;font-family:tahoma;font-size:16px;padding:20px;color:yellow;background-color:#CC0000;">'.nl2br($msg).'</div>';

		}

		$s = BASE_PATH."var/log/mysql-failures.txt";
             
		if (is_file($s))

		{
            
			@file_put_contents($s, $msg, FILE_APPEND);

		}

		else

		{
              
			@file_put_contents($s, $msg);

		}

		die();	

	}

	

	/**

	 * Assign value

	 *

	 * @param string $field

	 * @param mixed $value

	 */

	public function assign($field, $value)

	{

		$this->fields[$field] = ($value)==""?("'".$value."'"):$value;

	}



	/**

	 * Assigns string value

	 *

	 * @param string $field

	 * @param mixed $value

	 */

	public function assignStr($field, $value)

	{

		$this->fields[$field] = "'".$this->escape($value)."'";

	}

	

	/**

	 * Escapes mysql string

	 *

	 * @param string $str

	 * @return string

	 */

	public function escape($str)

	{

		return @mysql_real_escape_string($str);

	}

	

	/**

	 * Resets fields for insert/update

	 */

	public function reset()

	{

		$this->fields = array();

	}

	

	/**

	 * Insert new value into table

	 *

	 * @param string $table

	 *

	 * @return int

	 */

	public function insert($table)

	{

		$f = "";

		$v = "";

		reset($this->fields);

		foreach($this->fields as $field=>$value){

			$f.= ($f!=""?", ":"").$field;

			$v.= ($v!=""?", ":"").$value;

		}

		$sql = "INSERT INTO ".$table." (".$f.") VALUES (".$v.")";

		$this->query($sql);

		return $this->insertId();

	}



	/**

	 * Updates database record

	 *

	 * @param $table

	 * @param $where

	 */

	public function update($table, $where)

	{

      	//$call_stack = debug_backtrace(false);

		//$position = $call_stack[0];

		//$m = 'At '.$position['file'].' at line: '.$position['line'];

		//file_put_contents("content/cache/log/mysql.sql", $m."\n".$table."\n\n", FILE_APPEND);



		$f = "";

		reset($this->fields);

		foreach ($this->fields as $field=>$value)

		{

			$f.= ($f!=""?", ":"").$field." = ".$value;

		}

		$sql = "UPDATE ".$table." SET ".$f." ".$where;

		$this->query($sql);

	}

	

	/**

	 * Execute mysql query

	 *

	 * @param string $_query

	 *

	 * @return mixed

	 */

	public function query($_query)

	{

//		$call_stack = debug_backtrace(false);

//		$position = $call_stack[0];

//		$m = 'At '.$position['file'].' at line: '.$position['line'];

//		file_put_contents("content/cache/log/mysql-log.txt", $m."\n".$_query."\n\n", FILE_APPEND);



		$this->query = $_query;

		$this->result = @mysql_query($_query, $this->link_id)

			or $this->error("Can not execute database query. Please contact site administrator. ".(defined("DEV_MODE")&&DEV_MODE?"<br/>".htmlspecialchars($_query):""));

		return $this->result; 

	}

	

	/**

	 * Select all records or false

	 *

	 * @param $query

	 * @return mixed

	 */

	public function selectAll($query)

	{

		$this->query($query);

		return $this->getRecords();

	}



	/**

	 * Select all records / cached

	 *

	 * @param string $query

	 * @param int $cacheLifeTime in seconds

	 *

	 * @return mixed

	 */

	public function selectAllCached($query, $cacheLifeTime = 3600)

	{

		$hash = md5($query);

		$filename = $this->cacheDir.$hash.".cache";



		if (is_file($filename) && filemtime($filename) + $cacheLifeTime > time())

		{

			$f = @fopen($filename, "rb");

			if ($f)

			{

				flock($f, LOCK_SH);  

				$data = @unserialize(@fread($f, filesize($filename)));

				flock($f, LOCK_UN);

				@fclose($f);



				if ($data)

				{

					return $data;

				}

				else

				{

					return array();

				}

			}

		}



		$this->query($query);

		$data = $this->getRecords();



		$f = @fopen($filename, "wb");

		if ($f && @flock($f, LOCK_EX) && @fwrite($f, serialize($data)))

		{

			flock($f, LOCK_UN);

			@fclose($f);

		}

		else

		{

			if ($f)

			{

				flock($f, LOCK_UN);

				@fclose($f);

			}

			unlink($filename);

		}

		

		return $data;

	}





	/**

	 * Select only one record

	 *

	 * @param $query

	 * @param int $result_type

	 * @return array|bool

	 */

	public function selectOne($query, $result_type = MYSQL_BOTH)

	{

		$result = $this->query($query);



		if (($record = $this->moveNext($result, $result_type)) !== false)

		{

			return $record;

		}



		return false;

	}



	/**

	 * Returns all records from last select

	 *

	 * @param bool $result

	 *

	 * @return array

	 */

	public function getRecords($result = false)

	{

		$records = array();

        
        
		while (($row = @mysql_fetch_assoc($result ? $result : $this->result)) != false)

		{

			$records[] = $row;

		}



		return $records;

	}



	/**

	 * Fetches all all

	 *

	 * @param bool $result

	 *

	 * @return array

	 */

	public function getObjects($result = false)

	{

		$records = array();



		while (($row = @mysql_fetch_object($result ? $result : $this->result)) != false)

		{

			$records[current($row)] = $row;

		}



		return $records;

	}

	

	/**

	 * Returns tables status

	 *

	 * @return array

	 */

	public function getTablesStatus()

	{

		$this->query("SHOW TABLE STATUS FROM `".$this->db_name."`");

		if ($this->numRows() > 0)

		{

			$tables = array();

			while ($this->moveNext())

			{

				$tables[$this->col["Name"]] = $this->col;

			}

			return $tables;

		}

		return false;

	}



	/**

	 * Number of rows in last result

	 *

	 * @param bool $result

	 *

	 * @return int

	 */

	public function numRows($result = false)

	{

		return (int)@mysql_num_rows($result ? $result : $this->result);

	}



	public function numRowsAffected($linkId = false)

	{

		return (int)@ mysql_affected_rows($linkId ? $linkId : $this->link_id);

	}



	/**

	 * Return fetched array

	 *

	 * @param bool $result

	 * @param int $result_type

	 *

	 * @return array

	 */

	public function moveNext($result = false, $result_type = MYSQL_BOTH)

	{

		return $this->col = @mysql_fetch_array($result ? $result : $this->result, $result_type);

	}

	

	/**

	 * Shuts mysql connection down

	 */

	public function done()

	{

		@mysql_close($this->link_id);

	}

	

	/**

	 * Return return id

	 * @return int

	 */

	public function insertId()

	{

		return @mysql_insert_id($this->link_id);

	}

}

