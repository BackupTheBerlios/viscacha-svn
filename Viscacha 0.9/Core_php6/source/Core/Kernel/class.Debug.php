<?php
/**
 * Viscacha - Flexible Website Management Solution
 *
 * Copyright (C) 2004 - 2010 by Viscacha.org
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA
 *
 * @package		Core
 * @subpackage	Kernel
 * @author		Matthias Mohr
 * @copyright	Copyright (c) 2004-2010, Viscacha.org
 * @license		http://www.gnu.org/licenses/lgpl-2.1.txt GNU Lesser General Public License
 */

/**
 * Class for debugging purposes, e.g. benchmarks and logging misc. data.
 *
 * @package		Core
 * @subpackage	Kernel
 * @author		Matthias Mohr
 * @author		Moritz Baumann
 * @since 		1.0
 * @todo		Rework class
 */
class Debug {

	/**
	 * File to save the log data to.
	 * @var File
	 */
	private $file;
	/**
	 * Log data to save to the file.
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
	 * The first parameter has to be the file name with extension, but without directory. The second
	 * parameter has to be a valid and existing directory path with trainling directory separator
	 * (make sure the directory is writable). Standard value for this paramteer is null. If the
	 * directory is null or invalid  "data/logs/" will be used. If the specified file doesn't exist
	 * it will created. If it is not possible to create a file a NonFatalException will be thrown.
	 *
	 * @param string File for saving the logdata
	 * @param string Valid Directory for saving the logfile or null (directory will be "data/logs/")
	 * @throws NonFatalException
	 */
	public function __construct($file, $dir = null) {
		if ($dir === null || is_dir($dir) === false) {
			$dir = 'data/logs/';
		}
		$this->file = new File($dir.basename($file));
		if ($this->file->create() === false) {
			throw new NonFatalException('Could not create log file "'.$this->file->relPath().'".');
		}
		if ($this->file->readable() === false || $this->file->writable() === false) {
			$this->file->setPermissions(666);
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
		FileSystem::resetWorkingDir();
		$this->saveFile();
	}

	/**
	 * Text to add to the logfile.
	 *
	 * Line breaks, carriage returns and tabs will be converted to a single space char.
	 *
	 * @param string Text to write to the logfile
	 */
	public function addText($text) {
		$text = str_replace("\t", " ", $text);
		$text = Strings::replaceLineBreaks($text, " ");
		$this->logs[] = '['.gmdate('r').'] '.$text;
	}

	/**
	 * Finished Benchmarks and log texts will be added to the logfile.
	 *
	 * Each benchmark is one row in the file. After calling this function successfully, the finished
	 * benchmarks array and the log data array are empty. It is not recommended to use this function
	 * directly. If an error occurs while writing the file a NonFatalException will be thrown.
	 *
	 * @throws NonFatalException
	 */
	public function saveFile() {
		$benchmarks = array();
		//add new line if file is not empty
		$text = $this->file->size() != 0 ? "\r\n" : '';
		$benchmarks = $this->getBenchmarkStringArray();
		$text .= implode("\r\n", array_merge($this->logs, $benchmarks));
		if ($this->file->write($text, true) === false) {
			throw NonFatalException('Could not write to log file.');
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
		$benchmarks = array();
		$array = $this->file->read(FILE_LINES_TRIM);
		if ($array == null) {
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
		$array = $this->file->read(FILE_LINES_TRIM);
		if ($array == null) {
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
	 * If name is invalid or the benchmark is already stopped -1.0 will be returned. If benchmark
	 * already exists it will be added to the time of the existent benchmark and the sum will be
	 * returned.
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
		$this->benchmarks[$name] = array_key_exists(
			$name,
			($this->benchmarks ? ($this->benchmarks[$name] + $diff) : $diff)
		);
		return (float) $this->benchmarks[$name];
	}

	/**
	 * Gets the benchmark result of the specified name or the complete array.
	 *
	 * When the specified name does not exist, -1.0 will be returned. If benchmark is currently in
	 * progress the Benchmark will be stopped and the result will be returned If the whole array is
	 * requested, only those Benchmarks which are already stopped will be returned.
	 *
	 * @param string Name of benchmark or null (for array)
	 * @return float Benchmark result
	 */
	public function getBenchmarks($name = null) {
		if (array_key_exists($name, $this->temp) == true) {
			$this->stopClock($name);
		}
		if ($name == null) {
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
	 * Makes a "ready-to-save" numeric String array out of the associative Benchmark array.
	 *
	 * @return array Benchmarks for Output
	 */
	private function getBenchmarkStringArray() {
		$benchmarks = array();
		foreach ($this->benchmarks as $name => $value) {
			$benchmarks[] = '['.gmdate('r').'] '.$name.': '.$value;
		}
		return $benchmarks;
	}
}
?>