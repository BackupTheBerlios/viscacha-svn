<?php
/**
 * Advanced HTTP Client Class
 *
 * Copyright (C) 2002 - 2003 by GuinuX
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
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * For any suggestions or bug report please contact me: guinux@cosmoplazza.com
 *
 * @package		Core
 * @subpackage	Net
 * @author		GuinuX <guinux@cosmoplazza.com>
 * @author		Matthias Mohr
 * @copyright	Copyright (C) 2002 - 2003 by GuinuX
 * @version		1.1 (Released: 06-20-2002, Last Modified: 06-10-2003)
 * @license		http://www.gnu.org/licenses/lgpl-2.1.txt GNU Lesser General Public License
 */

/**
 * A HTTP client class - HTTPClientHeader
 *
 * @package		Core
 * @subpackage	Net
 * @author		GuinuX <guinux@cosmoplazza.com>
 * @author		Matthias Mohr
 * @since 		1.0
 * @abstract
 */
abstract class HTTPClientHeader {

	protected $headers;

	public function __construct() {
		$this->headers = array();
	}

	public function getHeader($header_name) {
		$header_name = $this->formatHeaderName($header_name);
		if (isset($this->headers[$header_name])) {
			return $this->headers[$header_name];
		}
		else {
			return null;
		}
	}

	public function setHeader($header_name, $value) {
		if ($value != '') {
			$header_name = $this->formatHeaderName($header_name);
			$this->headers[$header_name] = $value;
		}
	}

	public function reset() {
		$this->headers = array();
	}

	public function serializeHeaders() {
		$str = '';
		foreach ($this->headers as $name => $value) {
			$str .= "{$name}: {$value}" . HTTPClient::CRLF;
		}
		return $str;
	}

	private function formatHeaderName($header_name) {
		$formatted = str_replace('-', ' ', strtolower($header_name));
		$formatted = ucwords($formatted);
		$formatted = str_replace(' ', '-', $formatted);
		return $formatted;
	}

}
?>