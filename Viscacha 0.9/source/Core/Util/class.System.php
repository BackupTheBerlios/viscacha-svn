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
 * @subpackage	Util
 * @author		Matthias Mohr
 * @copyright	Copyright (c) 2004-2010, Viscacha.org
 * @license		http://www.gnu.org/licenses/lgpl-2.1.txt GNU Lesser General Public License
 */

/**
 * Operating system specific functions.
 *
 * @package		Core
 * @subpackage	Util
 * @author		Matthias Mohr
 * @since 		1.0
 * @abstract
 */
abstract class System {

	const WINDOWS = 1;
	const MAC = 2;
	const UNIX = 3; // Linux, BSD, Unix etc.

	/**
	 * Returns the basic type/family of operating system.
	 *
	 * The default OS is UNIX (used for everything not Windows and not MAC). 
	 *
	 * @return int Constant: System::WINDOWS, System::MAC or System::UNIX (default)
	 */
	public static function getOS() {
		$os = strtoupper(substr(PHP_OS, 0, 3));
		if($os == 'MAC' || $os == 'DAR') {
			return System::MAC;
		}
		elseif ($os == 'WIN') {
			return System::WINDOWS;
		}
		elseif (isset($_SERVER['OS']) && stripos($_SERVER['OS'], 'Windows') !== false) {
			return System::WINDOWS;
		}
		elseif (function_exists('php_uname') && stripos(@php_uname(), 'Windows') !== false) {
			return System::WINDOWS;
		}
		else {
			return System::UNIX;
		}
	}

	/**
	 * Returns the maximum possible value for post/upload requests.
	 *
	 * If the paramater is true (default) the maximum upload size will be returned. This takes the
	 * max post size into account. If the parameter is false only the maximum post size is returned.
	 *
	 * @param boolean Whether to return max. upload size (default) or only max post size (false).
	 * @return int Maximum post/upload size in bytes.
	 */
	public static function getMaxPostSize($upload = true) {
		$keys = array('post_max_size' => 0);
		if ($upload == true)  {
			$keys['upload_max_filesize'] = 0;
		}

		foreach ($keys as $key => $bytes) {
			$val = trim(@ini_get($key));
			$last = strtolower(substr($val, -1));
			switch($last) {
				case 'g':
					$val *= 1024;
				case 'm':
					$val *= 1024;
				case 'k':
					$val *= 1024;
			}
			$keys[$key] = $val;
		}

		return min($keys);
	}

	/**
	 * Returns the average server load in the last minute.
	 *
	 * Note: This is currently not available on windows.
	 *
	 * @return Load average of the last minute
	 */
	public static function getLoad() {
		$serverload = -1;
		// Not implemented on Windows
		if(self::getOS() == self::WINDOWS) {
			return $serverload;
		}

		if (function_exists('sys_getloadavg')) {
			list($serverload) = sys_getloadavg();
		}

		$proc = new File('/proc/loadavg');
		if($serverload == -1 && $proc->exists() && $proc->readable()) {
			$load = $proc->read(File::READ_STRING, File::BINARY);
			if ($load !== false) {
				list($serverload) = explode(b" ", $load);
				$serverload = trim($serverload);
			}
		}

		if ($serverload == -1 && function_exists('exec')) {
			$load = @exec("uptime");
			if(preg_match("~load averages?:\s?([\d\.]+)~i", $load, $serverload)) {
				list(,$serverload) = $serverload;
			}
		}

		if (empty($serverload)) {
			$serverload = -1;
		}
		return $serverload;
	}

}
?>