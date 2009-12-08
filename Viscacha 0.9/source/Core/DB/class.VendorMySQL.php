<?php
/**
 * Viscacha - Flexible Website Management Solution
 *
 * Copyright (C) 2004 - 2010 by Viscacha.org
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @package		Core
 * @subpackage	DB
 * @author		Matthias Mohr
 * @copyright	Copyright (c) 2004-2010, Viscacha.org
 * @license		http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License
 */

Core::loadClass('Core.DB.Database');

/**
 * Abstract class database, that has to be extended by all database drivers.
 *
 * {@inheritdoc}
 *
 * @package		Core
 * @subpackage	DB
 * @author		Matthias Mohr
 * @since 		1.0
 * @abstract
 */
abstract class VendorMySQL extends Database {

	/**
	 * Constructs the Database class and sets some default values.
	 *
	 * {@inheritdoc}
	 */
	public function __construct() {
		parent::__construct();
		$this->vendor = 'MySQL';
		$this->port = 3306;
		$this->null = 'NULL';
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

	/**
	 * Tells the server to begin a new transaction.
	 *
	 * Note: For most databases the transaction will be started by turning auto-commit off.
	 *
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 **/
	public function begin() {
		$this->inProgress = true;
		return $this->rawQuery("BEGIN");
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
	 * Returns the create statement for creating the specified table.
	 *
	 * After executing this method the MySQL option "SQL_QUOTE_SHOW_CREATE" is set to 1.
	 *
	 * @param string Name of the table
	 * @return string Create statement or null
	 */
	public function createStatement($table) {
		$this->query('SET SQL_QUOTE_SHOW_CREATE = 1');

    	$result = $this->query("SHOW CREATE TABLE `{$table}`", __LINE__, __FILE__);
    	$show_results = $this->fetchNum($result);

    	return $show_results[1];
	}

    /**
     * Returns insert statements for the specified table.
     *
     * If offset is = -1: All rows will be returned at once and $limit parameter won't be used.
     * Is offset is >= 0: Returns the number of rows specified with $limit starting at the row
	 * specified in $offset.
     *
     * @param string Table name
     * @param int Offset to begin with
     * @param int Limit that is used per call
     * @return string Insert statements
     */
    public function getData($table, $offset = -1, $limit = 1000) {
	    $table_data = $this->new_line.$this->commentdel.' Data: '.$table.
			($offset != -1 ? ' {'.$offset.', '.($offset+$limit).'}' : '').
			"\n";
     	// Datensaetze vorhanden?
     	$result = $this->rawQuery(
			'SELECT * FROM '.chr(96).$table.chr(96).
				($offset >= 0 ? " LIMIT {$offset},{$limit}" : ''),
			__LINE__,
			__FILE__
		);
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
                $table_value .= "'".$this->escapeString($select_result[$table_field])."'";
	        }
	        // Aktuelle Werte
	        $table_data .= 'INSERT INTO '.chr(96).$table.chr(96).' ('.$table_structure.') '.
						   'VALUES ('.$table_value.');'."\n";
			unset($table_structure, $table_value);
  	    }
		return trim($table_data);
    }

	/**
	 * Returns the create statement for creating the specified table.
	 *
	 * Set the second parameter to true to add "DROP TABLE IF EXISTS" statements before the
	 * create statement.
	 *
	 * After executing this method the MySQL option "SQL_QUOTE_SHOW_CREATE" is set to 1.
	 *
	 * @param string Name of the table
	 * @param boolean Add drop statements (default: false, no drop statements)
	 * @return string Create statement or null
	 */
    public function getStructure($table, $drop = false) {
    	// Activate Quotes in sql names
    	$this->rawQuery('SET SQL_QUOTE_SHOW_CREATE = 1');

    	$table_data = '';
        if ($drop == true) {
	        $table_data .= 'DROP TABLE IF EXISTS '.chr(96).$table.chr(96).';' ."\n";
	    }
	    $result = $this->rawQuery('SHOW CREATE TABLE '.chr(96).$table.chr(96));
	    $show_results = $this->fetchNum($result);
	    if (!$this->isResultSet($show_results)) {
		    return false;
	    }
		else {
			$table_data .= String::replaceLineBreak($show_results[1], "\n").';';
		    return trim($table_data);
		}
    }

	/**
	 * Returns an array containing all fields of the specified table.
	 *
	 * If no database is specified, the database will be read from the configuration file.
	 *
	 * @param string Table
	 * @param string Database or null
	 * @return array Array containing all fields of a table
	 */
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
	 */
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

	/**
	 * Performs one or more raw queries on the database.
	 *
	 * Executes one or multiple queries which are concatenated by a semicolon and a linebreak.
	 * You get back an array with all the results in the order of the queries.
	 * Comments are stripped.
	 *
	 * @param string Queries
	 */
	public function multiQuery($query) {
		$results = array();
		$lines = String::toTrimmedArray($query);
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
	 * Rolls back current transaction.
	 *
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	public function rollback() {
		$this->inProgress = false;
		return $this->rawQuery("ROLLBACK");
	}

	/**
	 * Executes a query for UTF-8 support.
	 *
	 * Executed query: <code>SET NAMES 'UTF8'</code>
	 *
	 * @see http://dev.mysql.com/doc/refman/5.0/en/charset-connection.html
	 */
	protected function setUTF8() {
		// MySQL should	return UTF-8
		$this->rawQuery("SET NAMES 'UTF8'");
	}

}
?>