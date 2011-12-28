<?php
/**
 * Interface for all database driver classes.
 *
 * Note: All data should be in UTF-8. Keep this in mind!
 *
 * @package		Core
 * @subpackage	DB
 * @author		Matthias Mohr
 * @since 		1.0
 */
interface DbDriver {

	/**
	 * Returns the number of rows affected by the last INSERT, UPDATE, or DELETE query.
	 *
	 * If the last query was invalid, this function will return -1.
	 *
	 * @return int Affected rows by the last query
	 **/
	public function affectedRows();

	/**
	 * Returns an array with benchmark infotmation.
	 *
	 * Structure of the array:<br>
	 * time => time alle queries took<br>
	 * count => number of queries that were executed<br>
	 * queries => array with all queries and the time they took (Keys: time and query)
	 *
	 * @return array Array with benchmark information
	 **/
	public function benchmark();


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
	 * Host: localhost,<br>
	 * Username: root<br>
	 * Password: <empty> (An warning will occur)<br>
	 * Port: 3306<br>
	 * Socket: null<br>
	 *
	 * @param string Host
	 * @param string Username
	 * @param string Passwort
	 * @param int Port
	 * @param string Socket or null
	 **/
	public function connect($username = null, $password = null, $host = null, $port = null, $socket = null);

	/**
	 * Closes a previously opened database connection.
	 *
	 * If no connection is open TRUE will be returned.
	 *
	 * @return boolean true on success, false on failure
	 **/
	public function disconnect();

	/**
	 * Returns an internal error message for the last error.
	 *
	 * @return string Error message
	 **/
	public function error();

	/**
	 * Returns an internal error number for the last error.
	 *
	 * @return int Error number
	 **/
	public function errno();

	/**
	 * Escapes special characters in a string for use in a SQL statement.
	 *
	 * You can either specify an string to be escaped or an array.
	 * Each array element will be escaped recursively.
	 *
	 * @param mixed String or array to escape
	 * @return mixed Escaped string or array
	 **/
	public function escapeString($data);


	/**
	 * Fetch all result rows as a multidimensional associative array.
	 *
	 * Each first level element contains an associative array that corresponds to the fetched row.
	 * The keys of the first level array can be set to the values of a field from the received result rows.
	 * If no key is specified, the array will be an enumerated array, where each column
	 * is stored in an array offset starting from 0 (zero).
	 *
	 * @param resource Result set
	 * @param string Field to use for the keys or null to get an enumerated array
	 * @return Array (see description) or null
	 **/
	public function fetchAll($result = null, $key = null);


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
	public function fetchOne($result = null);

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
	 * @param mixed $result
	 * @return Associative array or null
	 **/
	public function fetchAssoc($result = null);

	/**
	 * Get a result row as an enumerated array.
	 *
	 * Fetches one row of data from the result set represented by result and returns it as an enumerated array,
	 * where each column is stored in an array offset starting from 0 (zero). Each subsequent call to the
	 * function will return the next row within the result set, or null if there are no more rows.
	 *
	 * @param resource Result set
	 * @return array Enumerated array or null
	 **/
	public function fetchNum($result = null);

	/**
	 * Returns the current row of a result set as an object.
	 *
	 * Returns the current row result set as an object where the attributes of the object represent the names
	 * of the fields found within the result set. If no more rows exist in the current result set, null is returned.
	 * If two or more columns of the result have the same field names, the last column will take precedence.
	 *
	 * @param resource Result set
	 * @return object Object or null
	 **/
	public function fetchObject($result = null);

	/**
	 * Frees the memory associated with a result.
	 *
	 * @param resource Result set
	 **/
	public function freeResults($result = null);

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
	public function getData($table, $offset = -1, $limit = 1000);

	/**
	 * Returns the create statement for creating the specified table.
	 *
	 * Turning the first parameter to true, "DROP TABLE IF EXISTS" statements will be added before the create statement.
	 *
	 * @param string Name of the table
	 * @param boolean Add drop statements (default: false, no drop statements)
	 * @return string Create statement or false
	 */
	public function getStructure($table, $drop = false);

	/**
	 * Returns the ID used in the last INSERT query.
	 *
	 * null is returned on failure or when there was no last INSERT query.
	 *
	 * @return int ID or null
	 **/
	public function insertID();

	/**
	 * Returns true if the specified parameter is a valid result set.
	 *
	 * @return boolean
	 */
	public function isResultSet($result);

	/**
	 * Returns true if the current result set is a valid result set.
	 *
	 * @return boolean
	 */
	public function hasResultSet();

	/**
	 * Returns true if connection has been established successfully.
	 *
	 * @return boolean
	 */
	public function hasConnection();

	/**
	 * Returns an array containing all fields of the specified table.
	 *
	 * If no database is specified, the database will be read from the configuration file.
	 *
	 * @param string Table
	 * @param string Database or null
	 * @return array Array containing all fields of a table
	 **/
	public function listFields($table, $database = null);

	/**
	 * Returns an array containing all tables of the specified database.
	 *
	 * If no database is specified, the database will be read from the configuration file.
	 *
	 * @param string Database or null
	 * @return array Array containing all tables of a database
	 **/
	public function listTables($database = null);

	/**
	 * Performs one or more raw queries on the database.
	 *
	 * Executes one or multiple queries which are concatenated by a semicolon and a linebreak.
	 * You gat back an array with all the results in the order of the queries.
	 *
	 * @param string Queries
	 **/
	public function multiQuery($query);

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
	public function numRows($result = null);

	/**
	 * Returns the prefix of the tables.
	 *
	 * @return string Prefix
	 **/
	public function getPrefix();

	/**
	 * Sets the prefix of the tables.
	 *
	 * @param string Prefix
	 **/
	public function setPrefix($prefix);

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
	public function prepareQuery($query, $data = array());

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
	public function query($query, $data = array());

	/**
	 * Performs a raw query on the database.
	 *
	 * On failure a QueryExcpetion will be thrown.
	 *
	 * @throws QueryException
	 * @param string Single raw query
	 * @return mixed Result set for a select statement, a boolean for other statements (true on success, false on failure).
	 **/
	public function rawQuery($query);

	/**
	 * Adjusts the result pointer to the first row in the result set.
	 *
	 * @param resource Result set
	 * @return boolean Returns true on success, false on failure
	 **/
	public function resetResult($result = null);

	/**
	 * Selects the default database to be used when performing queries against the database connection.
	 *
	 * @param string Database name
	 * @param string Prefix for tables
	 * @return boolean true on success, false on failure
	 **/
	public function selectDB($database, $prefix = '');

	/**
	 * Returns a string containing which database system (database abstraction layer) is used.
	 *
	 * @return string Database System
	 **/
	public function system();

	/**
	 * Returns a string representing the version of the database server.
	 *
	 * @return string Version
	 **/
	public function version();

	/**
	 * Tells the server to begin a new transaction.
	 *
	 * Note: For most databases the transaction will be started by turning auto-commit off.
	 *
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 **/
	public function begin();

	/**
	 * Commits the current transaction.
	 *
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 **/
	public function commit();

	/**
	 * Rolls back current transaction.
	 *
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 **/
	public function rollback();

	/**
	 * Turns on or off auto-commiting database modifications.
	 *
	 * If there are open queries that are not already committed a commit will be started.
	 *
	 * @param boolean TRUE turns auto-commit on, FALSE disables it.
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 **/
	public function autocommit($status);

}
?>
