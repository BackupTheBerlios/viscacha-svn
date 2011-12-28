<?php
/**
 * Class for debugging purposes, e.g. benchmarks and logging misc. data.
 *
 * @package		Core
 * @subpackage 	System
 * @author		Matthias Mohr
 * @author		Moritz Baumann
 * @since		1.0
 */
class Debug {

	/**
	 * File to save the log-data to.
	 * @var File
	 */
	private $file;
	/**
	 * Log data for saving to the file.
	 * @var array
	 */
	private $logs;
	/**
	 * Finished benchmarks.
	 * @var array
	 */
	private $benchmarks;
	/**
	 * Temporary array for benchmarks.
	 * @var array
	 */
	private $temp;

	/**
	 * Constructs a new Debug-Object.
	 *
	 * The first parameter has to be the file name with extension, but without directory.
	 * Standard value is null, then the filename will be "internal.log".
	 * The second parameter has to be a valid and existing directory with trailing slash.
	 * Standard value is also null, then the directory will be "data/logs/" (make sure that it is writable).
	 * If the specified file doesn't exist, it will created.
	 * If it is not possible to create a file nn Exception will be thrown.
	 *
	 * @param string File for saving the logdata or null (filename is internal.log then)
	 * @param string Directory for saving the logfile or null (directory is data/logs/ then)
	 * @throws Exception
	 */
	public function __construct($file = null, $dir = null) {
		if ($dir == null || is_dir($dir) == false) {
			$dir = 'data/logs/';
		}
		if ($file == null) {
			$file = 'internal.log';
		}
		$this->file = new File($dir.basename($file));
		if ($this->file->create() == false) {
			throw new Exception('Could not create log file in method Debug::__construct().');
		}
		if ($this->file->readable() == false || $this->file->writable() == false) {
			$writable = new CHMOD(666);
			$this->file->setChmod($writable);
		}
		$this->logs = array();
		$this->benchmarks = array();
		$this->temp = array();
	}

	/**
	 * Write logs and benchmarks to file.
	 *
	 * This method simply calls the function saveFile().
	 */
	public function __destruct() {
		Core::destruct();
		$this->saveFile();
	}

	/**
	 * Text to add to the logfile.
	 *
	 * Specify a text without line breaks and/or carriage returns.
	 * Line breaks and carriage returns will be converted to a tab.
	 * Before this all tabs will be converted to 4 white spaces.
	 *
	 * @param string Things to write to the logfile
	 */
	public function add($text){
		$text = $this->makeLine($text);
		$text = '['.gmdate('r').'] '.$text;
		$this->logs[] = $text;
	}

	/**
	 * Finished Benchmarks and log texts will be added to the logfile.
	 *
	 * Each benchmark is one row in the file.
	 * After calling this function successfully, the finished benchmarks array and the log data array are empty.
	 * It is not recommended to use this function directly.
	 * If an error occurs while writing the file a warning will be thrown.
	 */
	public function saveFile() {
		$benchmarks = array();
		//add new line if file is not empty
		$text = iif(($this->file->size() != 0 && $this->file->size() != false), "\r\n");
		$benchmarks = $this->getBenchmarkStringArray();
		$text .= implode("\r\n", array_merge($this->logs, $benchmarks));
		if ($this->file->write($text, true) === false) {
			Core::throwError('Could not write log file in method Debug::saveFile().');
		}
		else {
			$this->clear();
		}
	}

	/**
	 * Log file will be returned.
	 *
	 * The current log file will be returned as an array.
	 * Each element of the array corresponds to a line in the file.
	 * The entries that are currently not in the file, but added to the array in this class,
	 * will be added to the end of the array.
	 *
	 * @return array Content of log file plus current log array
	 */
	public function getLogs() {
		$array = array();
		$benchmarks = array();
		$array = $this->file->read(FILE_LINES_TRIM);
		if (is_null($array)) {
			$array = array();
		}
		$benchmarks = $this->getBenchmarkStringArray();
		return array_merge($array, $benchmarks, $this->logs);
	}

	/**
	 * Log file will be cleared.
	 *
	 * After using this method the file still exists, but the file is empty.
	 * The content of the log file will be returned as an array.
	 * Each element of the array corresponds to a line in the file.
	 *
	 * If an error occurs null will be returned.
	 *
	 * @return array Content of log file
	 */
	public function clearFile() {
		$array = array();
		$array = $this->file->read(FILE_LINES_TRIM);
		if (is_null($array)) {
			$array = array();
		}
		if ($this->file->truncate() == false) {
			return null;
		}
		else {
			return $array;
		}
	}

	/**
	 * Benchmarks and log data will be cleared only from current runtime arrays.
	 */
	public function clear() {
		$this->logs = array();
		$this->benchmarks = array();
	}

	/**
	 * Start a benchmark with the specified name.
	 *
	 * If benchmark already exists it will be added to the time of the existent benchmark.
	 *
	 * @param string Name of benchmark
	 */
	public function startClock($name) {
		$this->temp[$name] = microtime(true);
	}

	/**
	 * Stops a benchmark with the specified name and returns result.
	 *
	 * If name is invalid or the benchmark is already stopped -1.0 will be returned.
	 * If benchmark already exists it will be added to the time of the existent benchmark and the sum will be returned.
	 *
	 * @param string Name of benchmark
	 * @return float Benchmark result
	 */
	public function stopClock($name) {
		$now = microtime(true);
		if (!array_key_exists($name, $this->temp)) {
			return (float) -1.0;
		}
		$diff = $now - $this->temp[$name];
		$this->benchmarks[$name] = array_key_exists($name, $this->benchmarks) ? ($this->benchmarks[$name] + $diff) : $diff;
		return (float) $this->benchmarks[$name];
	}

	/**
	 * Gets the benchmark result of the specified name or the complete array.
	 *
	 * When the specified name does not exist, -1.0 will be returned.
	 * If benchmark is currently in progress the Benchmark will be stopped and the result will be returned.
	 * If the whole array is requested, only those Benchmarks which are already stopped will be returned.
	 *
	 * @param string Name of benchmark or null (for array)
	 * @return float Benchmark result
	 */
	public function getBenchmarks($name = null) {
		if (array_key_exists($name, $this->temp) == true) {
			$this->stopClock($name);
		}
		if (is_null($name)) {
			return $this->benchmarks;
		}
		elseif (array_key_exists($name, $this->benchmarks) == true) {
			return $this->benchmarks[$name];
		}
		else {
			return (float) -1.0;
		}
	}

	/**
	 * Makes a "ready-to-save" numeric String array out of the associative Benchmark array
	 *
	 * @return array Benchmarks for Output
	 */
	private function getBenchmarkStringArray() {
		$benchmarks = array();
		foreach ($this->benchmarks as $name => $value) {
			$benchmarks[] = '['.gmdate('r').'] '.$this->makeLine($name).': '.$value;
		}
		return $benchmarks;
	}

	private function makeLine($text) {
		$text = preg_replace("#[\r\n\t]+#", '  ', $text);
		return trim($text);
	}
}
?>