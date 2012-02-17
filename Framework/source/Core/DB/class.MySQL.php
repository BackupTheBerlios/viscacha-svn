<?php
Core::loadInterface('Core.DB.DbDriver');
Core::loadClass('Core.DB.Database');

/**
 * Database driver for MySQL.
 *
 * This dirver is primary for older MySQL installations, mysql extension is used.
 *
 * @package		Core
 * @subpackage	DB
 * @author		Matthias Mohr
 * @since 		1.0
 */
class MySQL extends Database implements DbDriver {

	/**
	 * Initializes the class and the variables.
	 *
	 * No connection will be established. Use the methods connect() and selectDB() to open a connection.
	 */
	public function __construct() {
		parent::__construct();
		$this->system = 'MySQL';
	}

	/**
	 * Destructs the class. The method disconnect is called.
	 **/
	public function __destruct() {
		parent::__destruct();
	}

	/**
	 * Returns the number of rows affected by the last INSERT, UPDATE, or DELETE query.
	 *
	 * If the last query was invalid, this function will return -1.
	 *
	 * @return int Affected rows by the last query
	 **/
	public function affectedRows() {
		$id = mysqli_affected_rows($this->connection);
		return iif(is_numeric($id), $id, -1);
	}

	/**
	 * Attempts to open a connection to the MySQL Server.
	 *
	 * The host can be either a host name or an IP address. Passing the null value or the
	 * string "localhost" to this parameter, the local host is assumed. If successful,
	 * the the function will return an object representing the connection to the database,
	 * or null on failure. The port and socket parameters are used in conjunction with the
	 * host parameter to further control how to connect to the database server. The port
	 * parameter specifies the port number to attempt to connect to the MySQL server on,
	 * while the socket parameter specifies the socket or named pipe that should be used.
	 * If all values are null, the data from the configuration file will be read and used.
	 * If one value is not null, the data specified will be used. Default values are in this
	 * case:<br>
	 * Host: localhost<br>
	 * Username: root<br>
	 * Password: <empty> (An warning will occur)<br>
	 * Port: 3306<br>
	 * Socket: null<br>
	 *
	 * @param string Host
	 * @param string Username
	 * @param string Password
	 * @param int Port
	 * @param string Socket or null
	 **/
	public function connect($username = null, $password = null, $host = null, $port = null, $socket = null) {
		if (false == ($username == null && $password == null && $host == null && $port == null && $socket == null)) {
			$this->username = empty($username) ? 'root' : $username;
			$this->password = $password;
			if (empty($this->password) == true) {
				Core::throwError("Password for SQL-Connection is empty. It is highly recommended to set a password for your Database.");
			}
			$this->host = empty($host) ? 'localhost' : $host;
			$this->port = empty($port) ? '3306' : $port;
			$this->socket = $socket;
		}

		$host = $this->host.iif(is_id($port), ":{$this->port}").iif(!empty($this->socket), ":{$this->socket}");
		$this->connection = mysqli_connect($host, $this->username, $this->password);

		if ($this->hasConnection() == false) {
			Core::throwError('Could not connect to database! Pleasy try again later or check the database settings!', INTERNAL_ERROR);
		}
	}

	/**
	 * Closes a previously opened database connection.
	 *
	 * If no connection is open TRUE will be returned.
	 *
	 * @return boolean true on success, false on failure
	 **/
	public function disconnect() {
		if ($this->hasConnection() == false) {
			return true;
		}
		return mysqli_close($this->connection);
	}

	/**
	 * Returns an internal error message for the last error.
	 *
	 * @return string Error message
	 **/
	public function error() {
		if ($this->hasConnection()) {
			return mysqli_error($this->connection);
		}
		else {
			return mysqli_connect_error();
		}
	}

	/**
	 * Returns an internal error number for the last error.
	 *
	 * @return int Error number
	 **/
	public function errno() {
		if ($this->hasConnection()) {
			return mysqli_errno($this->connection);
		}
		else {
			return mysqli_connect_errno();
		}
	}

	/**
	 * Escapes special characters in a string for use in a SQL statement.
	 *
	 * You can either specify an string to be escaped or an array.
	 * Each array element will be escaped recursively.
	 *
	 * @param mixed String or array to escape
	 * @return mixed Escaped string or array
	 **/
	public function escapeString($data) {
		if (is_array($data) == true) {
			foreach($data as $key => $value){
				$data[$key] = $this->escapeString($value);
			}
		}
		else {
			$data = mysqli_real_escape_string($this->connection, $data);
		}
		return $data;
	}

	/**
	 * Fetch a result row as an associative array
	 *
	 * Returns an associative array that corresponds to the fetched row or null if there are no more rows.
	 * The function is used to return an associative array representing the next row in the result set for
	 * the result represented by the result parameter, where each key in the array represents the name of
	 * one of the result set's columns. If two or more columns of the result have the same field names,
	 * the last column will take precedence. To access the other column(s) of the same name, you either
	 * need to access the result with numeric indices by using fetchNum() or add alias names.
	 *
	 * If the result set parameter is not specified, the last result will be used.
	 *
	 * @param mixed $result
	 * @return array Result in an associative array
	 **/
	public function fetchAssoc($result = null) {
		if ($result == null) {
			$result = $this->result;
		}
		return mysqli_fetch_assoc($result);
	}

	/**
	 * Get a result row as an enumerated array.
	 *
	 * Fetches one row of data from the result set represented by result and returns it as an enumerated array,
	 * where each column is stored in an array offset starting from 0 (zero). Each subsequent call to the
	 * function will return the next row within the result set, or null if there are no more rows.
	 *
	 * If the result set parameter is not specified, the last result will be used.
	 *
	 * @param resource Result set
	 * @return array Enumerated array or null
	 **/
	public function fetchNum($result = null) {
		if ($result == null) {
			$result = $this->result;
		}
		return mysqli_fetch_row($result);
	}

	/**
	 * Returns the current row of a result set as an object.
	 *
	 * Returns the current row result set as an object where the attributes of the object represent the names
	 * of the fields found within the result set. If no more rows exist in the current result set, null is returned.
	 * If two or more columns of the result have the same field names, the last column will take precedence.
	 *
	 * If the result set parameter is not specified, the last result will be used.
	 *
	 * @param resource Result set
	 * @return object Object or null
	 **/
	public function fetchObject($result = null) {
		if ($result == null) {
			$result = $this->result;
		}
		return mysqli_fetch_object($result);
	}

	/**
	 * Frees the memory associated with a result.
	 *
	 * If the result set parameter is not specified, the last result will be used.
	 *
	 * @param resource Result set
	 **/
	public function freeResults($result = null) {
		if ($result == null) {
			$result = $this->result;
		}
		if ($this->isResultSet($result) == true) {
			mysqli_free_result($result);
		}
	}

	/**
	 * Returns the create statement for creating the specified table.
	 *
	 * Turning the first parameter to true, "DROP TABLE IF EXISTS" statements will be added before the create statement.
	 *
	 * After executing this method the MySQL option "SQL_QUOTE_SHOW_CREATE" is set to 1.
	 *
	 * @param string Name of the table
	 * @param boolean Add drop statements (default: false, no drop statements)
	 * @return string Create statement or false
	 */
    function getStructure($table, $drop = false) {
    	// Activate Quotes in sql names
    	$this->rawQuery('SET SQL_QUOTE_SHOW_CREATE = 1');

    	$table_data = '';
        if ($drop == true) {
	        $table_data .= 'DROP TABLE IF EXISTS '.chr(96).$table.chr(96).';' ."\n";
	    }
	    $result = $this->rawQuery('SHOW CREATE TABLE '.chr(96).$table.chr(96));
	    $show_results = $this->fetch_num($result);
	    if (!$this->isResultSet($show_results)) {
		    return false;
	    }
		else {
		    $table_data .= str_replace(array("\r\n", "\r", "\n"), "\n", $show_results[1]). ';' ."\n";
		    return trim($table_data);
		}
    }

    /**
     * Returns insert statements for the specified table.
     *
     * If offset is = -1: all rows will be returned at once and $limit parameter won't be used.<br />
     * Is offset is >= 0: all rows starting at row $offset until we reached the row ($offset + $limit).
     *
     * @param string $table Table name
     * @param int $offset Offset to begin at
     * @param int $limit Limit that is used per call
     * @return string Insert statements
     */
    function getData($table, $offset = -1, $limit = 1000) {
	    $table_data = $this->new_line. $this->commentdel.' Data: ' .$table . iif ($offset != -1, ' {'.$offset.', '.($offset+$limit).'}' ). "\n";
     	// Datensaetze vorhanden?
     	$result = $this->rawQuery('SELECT * FROM '.chr(96).$table.chr(96).iif($offset >= 0, " LIMIT {$offset},{$limit}"), __LINE__, __FILE__);
  	    while ($select_result = $this->fetchAssoc($result)) {
      		// Result-Keys
      		$select_result_keys = array_keys($select_result);
      		foreach ($select_result_keys as $table_field) {
	      		// Struktur & Werte der Tabelle
	      		if (isset($table_structure)) {
	          		$table_structure .= ', ';
	          		$table_value .= ', ';
	      		}
	      		else {
		            $table_structure = $table_value = '';
	            }
                $table_structure .= chr(96).$table_field.chr(96);
                $table_value .= "'".$this->escape_string($select_result[$table_field])."'";
	        }
	        // Aktuelle Werte
	        $table_data .= 'INSERT INTO '.chr(96).$table.chr(96).' (' .$table_structure. ') VALUES (' .$table_value. ');' ."\n";
			unset($table_structure, $table_value);
  	    }
		return trim($table_data);
    }

	/**
	 * Returns the ID used in the last INSERT query.
	 *
	 * null is returned on failure or when there was no last INSERT query.
	 *
	 * @return int ID or null
	 **/
	public function insertID() {
		$id = mysqli_insert_id($this->connection);
		return is_id($id) ? $id : null;
	}

	/**
	 * Returns true if connection has been established successfully.
	 *
	 * @return boolean
	 */
	function hasConnection(){
		return is_object($this->connection);
	}

	/**
	 * Returns true if the specified parameter is a valid result set.
	 *
	 * @return boolean
	 */
	function isResultSet($result){
		if (!is_object($result)) {
	    	$result = $this->result;
	    }
		return is_object($result);
	}

	/**
	 * Gets the number of rows in a result.
	 *
	 * The use depends on whether you use buffered or unbuffered result sets.
	 * In case you use unbuffered resultsets this function will not correct the
	 * correct number of rows until all the rows in the result have been retrieved.
	 *
	 * @param resource Result set
	 * @return int Number of rows
	 **/
	public function numRows($result = null) {
		if ($result == null) {
	    	$result = $this->result;
	    }
	    return mysqli_num_rows($result);
	}

	/**
	 * Performs a raw query on the database.
	 *
	 * On failure a QueryExcpetion will be thrown.
	 *
	 * @throws QueryException
	 * @param string Single raw Query
	 * @return mixed Result set for a select statement, a boolean for other statements (true on success, false on failure)
	 **/
	public function rawQuery($query){
		$this->benchmark['count']++;

		$this->debug->startClock($query);
		$this->result = @mysqli_query($this->connection, $query);
		$time = $this->debug->stopClock($query);

		if ($this->result === false) {
			$this->queryError($query);
		}

		$this->benchmark['time'] += $time;
		$this->benchmark['queries'][] = array('query' => $query, 'time' => $time);

	    return $this->result;
	}

	/**
	 * Adjusts the result pointer to the first row in the result set.
	 *
	 * @param resource Result set
	 * @return boolean Returns true on success, false on failure
	 **/
	public function resetResult($result = null) {
		if ($result == null) {
			$result = $this->result;
		}
		return mysqli_data_seek($result, 0);
	}

	/**
	 * Selects the default database to be used when performing queries against the database connection.
	 *
	 * @param string Database name
	 * @param string Prefix for tables
	 * @return boolean true on success, false on failure
	 **/
	public function selectDB($database, $prefix = '') {
		$this->database = $database;
		$this->pre = $prefix;
		return mysqli_select_db($this->connection, $this->database);
	}

	/**
	 * Returns a string representing the version of the database server.
	 *
	 * @return string Version or an empty string on failure
	 **/
	public function version() {
		$version = mysqli_get_server_info($this->connection);
		return empty($version) ? '' : $version;
	}

	/**
	 * Tells the server to begin a new transaction.
	 *
	 * Note: For most databases the transaction will be started by turning auto-commit off.
	 *
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 **/
	public function begin() {
		$this->inProgress = true;
		return $this->rawQuery("START TRANSACTION");
	}

	/**
	 * Commits the current transaction.
	 *
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 **/
	public function commit() {
		$this->inProgress = false;
		return $this->rawQuery("COMMIT");
	}

	/**
	 * Rolls back current transaction.
	 *
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 **/
	public function rollback() {
		$this->inProgress = false;
		return $this->rawQuery("ROLLBACK");
	}

	/**
	 * Turns on or off auto-commiting database modifications.
	 *
	 * If there are open queries that are not already committed a commit will be started.
	 *
	 * @param boolean TRUE turns auto-commit on, FALSE disables it.
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 **/
	public function autocommit($status) {
		if ($status == true) {
			if ($this->inProgress == true) {
				$this->commit();
			}
			return $this->rawQuery("SET AUTOCOMMIT = 1");
		}
		else {
			return $this->rawQuery("SET AUTOCOMMIT = 0");
		}
	}

}
?>
