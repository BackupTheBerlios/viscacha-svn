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
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA
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

Core::loadClass('Core.Net.HTTP.HTTPClientHeader');

/**
 * A HTTP client class - HTTPClientResponseHeader
 *
 * @package		Core
 * @subpackage	Net
 * @author		GuinuX <guinux@cosmoplazza.com>
 * @author		Matthias Mohr
 * @since 		1.0
 */
class HTTPClientResponseHeader extends HTTPClientHeader {

	protected $cookiesHeaders;

	public function __construct() {
		$this->cookiesHeaders = array();
		parent::__construct();
	}

	public function deserializeHeaders($flat_headers) {
		$tmp_headers = Strings::toArray($flat_headers);
		if (preg_match("~HTTP/(\d\.\d)\s+(\d+).*~i", $tmp_headers[0], $matches) > 0) {
			$this->setHeader('Protocol-Version', $matches[1]);
			$this->setHeader('Status', $matches[2]);
		}
		array_shift($tmp_headers);
		foreach($tmp_headers as $value) {
			$pos = strpos($value, ':');
			if ($pos !== false) {
				$key = substr($value, 0, $pos);
				$value = trim(substr($value, $pos+1));
				if (strtolower($key) == 'set-cookie') {
					$this->cookiesHeaders[] = $value;
				}
				else {
					$this->setHeader($key, $value);
				}
			}
		}
	}

	public function reset() {
		$this->cookiesHeaders = array();
		parent::reset();
	}

}
?>