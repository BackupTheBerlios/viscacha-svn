<?php
Core::loadClass('Core.ExceptionData');

/**
 * Exception for errors in Queries to the database.
 *
 * @package		Core
 * @subpackage	DB
 * @author		Matthias Mohr
 * @since 		1.0
 */
class QueryException extends ExceptionData {

	/**
	 * Containts the query.
	 * @var string
	 */
	protected $query;

	/**
	 * Constructs the QueryException.
	 *
	 * @param string Database error message
	 * @param int Database error number (default: 0)
	 */
	public function __construct($message, $code = 0) {
		parent::__construct($message, $code);
	}


	/**
	 * Returns a detailed error message.
	 *
	 * The error message contains:
	 * 1. Error Number (only if greater than zero)
	 * 2. Error Message
	 * 3. File Name
	 * 4. Line Number of the file
	 * 5. Executed SQL Query (optional)
	 *
	 * @return string All error details as string
	 */
	public function __toString() {
		$error = '';
		if ($this->code > 0) {
			$error .= "#{$this->code}: ";
		}
		$error .= $this->getMessage();
		$error .= " [File: {$this->file} #{$this->line}]";
		if (empty($this->query) == false) {
			$query = str_replace(array("\r\n", "\n", "\r"), "\t", $this->query);
			$error .= "\r\nQuery: {$query}";
		}

		return $error;
	}

	/**
	 * Overrides the line number.
	 *
	 * @param int Line number
	 */
	public function setLine($line) {
		$this->line = $line;
	}

	/**
	 * Overrides the file name.
	 *
	 * @param int File name
	 */
	public function setFile($file) {
		$this->file = $file;
	}

	/**
	 * Sets the query.
	 *
	 * @param string Query
	 */
	public function setQuery($query) {
		$this->query = $query;
	}

	/**
	 * Returns the query.
	 *
	 * @return string Query
	 */
	public function getQuery() {
		return $this->query();
	}

	/**
	 * Returns an array with additional information about the excpetion.
	 *
	 * The array contains one element. The key is "Query" and contains the query which causes the exception.
	 *
	 * @return array Data with keys as labels and values as data.
	 */
	public function getData() {
		return array('Query' => $this->query);
	}
}
?>
