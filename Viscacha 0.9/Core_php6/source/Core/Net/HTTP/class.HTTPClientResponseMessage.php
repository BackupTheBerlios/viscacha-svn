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

/**
 * A HTTP client class - HTTPClientResponseMessage
 *
 * @package		Core
 * @subpackage	Net
 * @author		GuinuX <guinux@cosmoplazza.com>
 * @author		Matthias Mohr
 * @since 		1.0
 */
class HTTPClientResponseMessage extends HTTPClientResponseHeader {

	protected $body;
	protected $cookies;

	public function __construct() {
		$this->cookies = new HTTPClientCookie();
		$this->body = '';
		parent::__construct();
	}

	public function getStatus() {
		$status = $this->getHeader('Status');
		if ($status != null) {
			return (int) $status;
		}
		else {
			return -1;
		}
	}

	public function getProtocolVersion() {
		if ($this->getHeader('Protocol-Version') != null) {
			return $this->getHeader('Protocol-Version');
		}
		else {
			return HTTPClient::V10;
		}
	}

	public function getContentType() {
		return $this->getHeader('Content-Type');
	}

	public function getBody() {
		return $this->body;
	}

	public function setBody($body) {
		$this->body = $body;
	}

	public function getCookies() {
		return $this->cookies;
	}

	public function reset() {
		$this->body = '';
		parent::reset();
	}

	public function parseCookies($host) {
		$headers = count($this->cookiesHeaders);
		for ($i = 0; $i < $headers; $i++) {
			$this->cookies->parse($this->cookiesHeaders[$i], $host);
		}
	}
}
?>