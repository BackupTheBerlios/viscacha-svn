<?php
Core::loadInterface('Core.DB.DbDriver');

/**
 * Abstract class database, that has to be extended by all database drivers.
 *
 * @package		Core
 * @subpackage	DB
 * @author		Matthias Mohr
 * @since 		1.0
 * @abstract
 */
abstract class Database implements DbDriver {

	private static $instance = NULL;

	public static function getObject() {
		if (self::$instance === NULL) {
			$driver = Config::get('db.driver');
			self::$instance = Core::constructObject("Core.DB.{$driver}");
			if (self::$instance == null || !(self::$instance instanceof DbDriver)) {
			   throw new Exception("Could not construct database driver of type {$driver}.");
			}
			self::$instance->connect(Config::get('db.username'), Config::get('db.password'), Config::get('db.host'), Config::get('db.port'), Config::get('db.socket'));
			self::$instance->selectDB(Config::get('db.database'), Config::get('db.prefix'));
		}
		return self::$instance;
	}

	private function __clone() {}

	/**
	 * Contains the prefix for the database tables.
	 * @var string
	 */
	public $pre;
	/**
	 * Contains the database driver name.
	 * @var string
	 */
	protected $system;
	/**
	 * Benchmark data.
	 * @var array
	 */
	protected $benchmark;
	/**
	 * The connection resource.
	 * @var resource
	 */
	protected $connection;
	/**
	 * Contains the host name.
	 * @var string
	 */
	protected $host;
	/**
	 * Contains the user name for the database.
	 * @var string
	 */
	protected $username;
	/**
	 * Contains the password for the database.
	 * @var string
	 */
	protected $password;
	/**
	 * Contains the database.
	 * @var string
	 */
	protected $database;
	/**
	 * Path to socket
	 * @var string
	 */
	protected $socket;
	/**
	 * Port number for the connection.
	 * @var int
	 */
	protected $port;
	/**
	 * Last result resource.
	 * @var resource
	 */
	protected $result;
	/**
	 * Object for debugging purposes.
	 * @var Debug
	 */
	protected $debug;
	/**
	 * Tells whether an transaction is in progress (true) or not (false).
	 * @var boolean
	 */
	protected $inProgress;
	/**
	 * Keyowrd NULL for database.
	 *
	 * @var string
	 */
	protected $null;

	protected $tempData;

	/**
	 * Constructs that Database class and sets some default values.
	 *
	 * Auto-commit is off at the beginning!<br />
	 * The log file for debugging will be saved in data/logs/database.log.
	 */
	public function __construct() {
		$this->system = null;
		$this->result = null;
		$this->benchmark = array(
			'time' => 0,
			'count' => 0,
			'queries' => array()
		);
		$this->connection = null;
		$this->host = 'localhost';
		$this->username = 'root';
		$this->password = '';
		$this->database = '';
		$this->socket = null;
		$this->port = 3306;
		$this->pre = null;
		$this->debug = new Debug('database.log');
		$this->inProgress = false;
		$this->null = 'NULL';
		$this->tempData = null;
	}

	/**
	 * Disconnect from the database and roll back open transactions.
	 */
	public function __destruct() {
		if ($this->inProgress == true) {
			try {
				$this->rollback();
			} catch (QueryException $e) {
				$this->debug->add($e);
				throw $e;
			}
		}
		$this->disconnect();
	}

	/**
	 * Returns an array with benchmark infotmation.
	 *
	 * Structure of the array:<br>
	 * time => time all queries took<br>
	 * count => number of queries that were executed<br>
	 * queries => array with all queries and the time they took (Keys: time and query)
	 *
	 * @return array Array with benchmark information
	 **/
	public function benchmark() {
		return $this->benchmark;
	}

	/**
	 * Returns the internal used Debug-object.
	 *
	 * @return Debug Object used for debugging
	 */
	public function getDebug() {
		return $this->debug;
	}

	/**
	 * Fetch all result rows as a multidimensional associative array.
	 *
	 * Each first level element contains an associative array that corresponds to the fetched row.
	 * The keys of the first level array can be set to the values of a field from the received result rows.
	 * If no key is specified, the array will be an enumerated array, where each column
	 * is stored in an array offset starting from 0 (zero).
	 *
	 * If the result set parameter is not specified, the last result will be used.
	 * After the result is returned, the resource will be cleared using freeResults().
	 *
	 * @param resource Result set
	 * @param string Field to use for the keys or null to get an enumerated array
	 * @param string Single field to use for as values
	 * @return array All results in a multimensional associative array
	 **/
	public function fetchAll($result = null, $key = null, $value = null) {
		$cache = array();
		while($row = $this->fetchAssoc($result)){
			if ($key !== null || $value != null) {
				if ($key !== null && !isset($row[$key])) {
					Core::throwError("Key assigned in fetchAll() was not found in result set. The keys will be enumerated.", INTERNAL_NOTICE);
				}
				else if ($value !== null && !isset($row[$value])) {
					Core::throwError("Key assigned in fetchAll() was not found in result set.");
				}
				if ($key !== null && $value === null) {
					$cache[$row[$key]] = $row;
				}
				else if ($key === null && $value !== null) {
					$cache[] = $row[$value];
				}
				else {
					$cache[$row[$key]] = $row[$value];
				}
			}
			else {
				$cache[] = $row;
			}
		}
		$this->freeResults($result);
		return $cache;
	}

	/**
	 * Fetches the first result row and returns the first field in this row.
	 *
	 * A result set (resource) already returned by a function call or a query (string), that will be
	 * executed, can be specified. If nothing is specified, the last result set will be used.
	 * If no result set is available a warning will be thrown and null will be returned.
	 *
	 * If the result set parameter is not specified, the last result will be used.
	 *
	 * @param mixed Result set (resource) or query (string) or null
	 * @return mixed First field in first row or null
	 **/
	public function fetchOne($result = null) {
		if ($result == null) {
			$result = $this->result;
		}
		if(is_string($result) == true) {
			$result = $this->rawQuery($result);
		}
		if ($this->isResultSet($result) == false) {
			Core::throwError('No valid result set specified.');
		}
		if ($this->numRows($result) > 0) {
			$this->resetResult($result);
			$first = $this->fetchNum($result);
			return $first[0];
		}
		else {
			return null;
		}
	}

	/**
	 * Returns the prefix of the tables.
	 *
	 * @return string Prefix
	 **/
	public function getPrefix(){
		return $this->pre;
	}

	/**
	 * Returns true if the current result set is a valid result set.
	 *
	 * @return boolean
	 */
	public function hasResultSet() {
		return $this->isResultSet($this->result);
	}

	/**
	 * Returns an array containing all fields of the specified table.
	 *
	 * If no database is specified, the database will be read from the configuration file.
	 *
	 * @param string Table
	 * @param string Database or null
	 * @return array Array containing all fields of a table
	 **/
	public function listFields($table, $database = null) {
		$result = $this->query("SHOW COLUMNS FROM `{$table}`", __LINE__, __FILE__);
		$columns = array();
		while ($row = $this->fetch_num($result)) {
			$columns[] = $row[0];
		}
		return $columns;
	}

	/**
	 * Returns an array containing all tables of the specified database.
	 *
	 * If no database is specified, the database will be read from the configuration file.
	 *
	 * @param string Database or null
	 * @return array Array containing all tables of a database
	 **/
	public function listTables($database = null) {
		if ($database == null) {
			$database = $this->database;
		}
		$result = $this->query("SHOW TABLES FROM `{$database}`", __LINE__, __FILE__);
		$tables = array();
		while ($row = $this->fetch_num($result)) {
			$tables[] = $row[0];
		}
		return $tables;
	}

	protected function parseFloat($var) {
		$var = str_replace(',', '.', strval(floatval($var)));
		return "'".$this->escapeString($var)."'";
	}


	protected function parseInt($var) {
		if (!is_string($var) && !is_numeric($var)) {
			Core::throwError('Database::parseInt() can only convert strings and numerical data to integers.');
		}
		if (is_string($var)) {
			$var = trim($var);
		}
		return intval($var);
	}

	protected function parseString($var) {
		return "'".$this->escapeString($var)."'";
	}

	protected function replaceQueryPlaceholder($matches) {
		$key = $matches[1];
		if (!isset($this->tempData[$key])) {
			return $matches[0];
		}
		else {
			if (empty($matches[3])) {
				$type = 'string';
			}
			else {
				$type = strtolower($matches[3]);
			}
			$data = $this->tempData[$key];
			if ($data === null) {
				return $this->null;
			}
			else {
				switch($type) {
					case 'raw':
						return $data;
					break;
					case 'noquote':
						return $this->escapeString($data);
					break;
					case 'float':
						return $this->parseFloat($data);
					break;
					case 'int':
						return $this->parseInt($data);
					break;
					case 'string[]':
						$data = array_map(array($this, 'parseString'), $data);
						return implode(',', $data);
					break;
					case 'int[]':
						$data = array_map(array($this, 'parseInt'), $data);
						return implode(',', $data);
					break;
					default:
						return $this->parseString($data);
					break;
				}
			}
		}
	}

	/**
	 * Prepares a query and secures/escapes the data.
	 *
	 * The first parameter is a query with placeholders.
	 * The second parameter is an array with the data for the query as an array.
	 * The keys of the array are the placeholders (in the query between the chars < and >).
	 * Placeholders can have only alphanumerical chars plus "_", "-" and ".".
	 * After the placeholder you can specify the type of the placeholder separated by ":".
	 * If you do not specify the type then string will be assumed to be the correct one. You do not need to quote the placeholders.
	 * The values of the array is the data for the placeholder. NULL in php is NULL in the database.<br />
	 * Reserved placeholders: p, prefix for prefix of tables.<br />
	 * Possible types: <br />
	 * - raw: no quotes, no escaping <br />
	 * - string /: quotes, escaping<br />
	 * - string[]: every element -> seperated by a comma, qupotes, escaping<br />
	 * - int: no quotes, conversion to integer<br />
	 * - int[]: every element -> seperated by a comma, no quotes, conversion to integer<br />
	 * - float: quotes, conversion to float
	 *
	 * @param string Single query
	 * @param array Data for the query
	 * @return string Prepared query
	 */
	public function prepareQuery($query, $data = array()) {
		$query = str_ireplace(array('<p>', '<prefix>'), $this->pre, $query);
		if (count($data) > 0) {
			$this->tempData = $data;
			$query = preg_replace_callback('~<([a-zA-Z0-9_\-\.]+)(:([a-z]+(\[\])?))?>~', array($this, 'replaceQueryPlaceholder'), $query);
			$this->tempData = null;
		}
		return $query;
	}

	/**
	 * Performs a secure query on the database.
	 *
	 * On failure a QueryExcpetion will be thrown.
	 * For information about the parameters see the method prepareQuery().
	 *
	 * @throws QueryException
	 * @see DbDriver::prepareQuery()
	 * @param string Single query
	 * @param array Data for the query
	 * @return mixed Result set for a select statement, a boolean for other statements (true on success, false on failure).
	 **/
	public function query($query, $data = array()) {
		return $this->rawQuery($this->prepareQuery($query, $data));
	}

	/**
	 * Performs one or more raw queries on the database.
	 *
	 * Executes one or multiple queries which are concatenated by a semicolon and a linebreak.
	 * You gat back an array with all the results in the order of the queries.
	 *
	 * @param string Queries
	 **/
	public function multiQuery($query) {
		$results = array();
		$lines = str_replace("\r", "\n", $query);
		$lines = explode("\n", $lines);
		$lines = array_map("trim", $lines);
		$query = '';
		foreach ($lines as $line) {
			$comment = substr($line, 0, 2);
			if ($comment == '--' || $comment == '//' || strlen($line) <= 10) {
				continue;
			}
			$query .= $line."\n";
		}
		$lines = explode(";\n", $query);
		foreach ($lines as $key => $line) {
			if (strlen($line) > 10) {
				unset($result);
				$result = $this->rawQuery($line);
				$results[$key] = array();
				if ($this->isResultSet($result) && $this->numRows($result) > 0) {
					while ($row = $this->fetchAssoc($result)) {
						$results[$key][] = $row;
					}
				}
			}
		}
		return $s;
	}

	/**
	 * Sets the prefix of the tables.
	 *
	 * @param string Prefix
	 **/
	public function setPrefix($prefix){
		$this->pre = $prefix;
	}

	/**
	 * Handles an Query Error: A QueryExcpetion will be thrown.
	 *
	 * @see QueryException
	 * @throws QueryException
	 * @param string Last Query
	 **/
	protected function queryError($query) {
		$e = new QueryException($this->error(), $this->errno());
		$e->setQuery($query);
		$e->setLine(__LINE__);
		$e->setFile(__FILE__);

		// Try to get better results for line and file.
		if (function_exists('debug_backtrace') == true) {
			$backtraceInfo = debug_backtrace();
			// 0 is class.mysql.php, 1 is the calling code...
			if (isset($backtraceInfo[1]) == true) {
				$e->setLine($backtraceInfo[1]['line']);
				$e->setFile($backtraceInfo[1]['file']);
			}
		}

		$this->debug->add($e);

		throw $e;
	}

	/**
	 * Returns a string containing which database system (database abstraction layer) is used.
	 *
	 * @return string Database System
	 **/
	public function system() {
		return $this->system;
	}

}
?>
