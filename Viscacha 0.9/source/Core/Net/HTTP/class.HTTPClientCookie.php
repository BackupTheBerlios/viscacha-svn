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
 * A HTTP client class - HTTPClientCookie
 *
 * @package		Core
 * @subpackage	Net
 * @author		GuinuX <guinux@cosmoplazza.com>
 * @author		Matthias Mohr
 * @since 		1.0
 */
class HTTPClientCookie {

	private $cookies;

	public function __construct() {
		$this->cookies 	= array();
	}

	private function now() {
		return strtotime(gmdate("l, d-F-Y H:i:s", time()));
	}

	private function timestamp($date) {
		if ($date == '') {
			return $this->now() + 3600;
		}
		$time = strtotime($date);
		return ($time>0?$time:$this->now()+3600);
	}

	public function get($currentDomain, $currentPath) {
		$cookieStr = '';
		$now = $this->now();
		$newCookies = array();

		foreach($this->cookies as $cookieName => $cookieData) {
			if ($cookieData['expires'] > $now) {
				$newCookies[$cookieName] = $cookieData;

				$domain = preg_quote($cookieData['domain'], '~');
				$domainMatch = (bool) preg_match("~.*{$domain}$~i", $currentDomain);

				$path = preg_quote($cookieData['path'], '~');
				$pathMatch = (bool) preg_match("~^{$path}.*~i", $currentPath);

				if ($domainMatch == true && $pathMatch == true) {
					$cookieStr .= $cookieName.'='.$cookieData['value'].'; ';
				}
			}
		}

		$this->cookies = $newCookies;
		return $cookieStr;
	}

	public function set($name, $value, $domain, $path, $expires) {
		$this->cookies[$name] = array(
			'value' => $value,
			'domain' => $domain,
			'path' => $path,
			'expires' => $this->timestamp($expires)
		);
	}

	public function parse($cookie_str, $host) {
		$cookie_str = str_replace('; ', ';', $cookie_str).';';
		$data = explode(';', $cookie_str);
		$value_str = $data[0];

		$cookie_param = 'domain=';
		$start = strpos($cookie_str, $cookie_param);
		if ($start > 0) {
			$domain = substr($cookie_str, $start + strlen($cookie_param));
			$domain = substr($domain, 0, strpos($domain, ';'));
		}
		else {
			$domain = $host;
		}

		$cookie_param = 'expires=';
		$start = strpos($cookie_str, $cookie_param);
		if ($start > 0) {
			$expires = substr($cookie_str, $start + strlen($cookie_param));
			$expires = substr($expires, 0, strpos($expires, ';'));
		}
		else {
			$expires = '';
		}

		$cookie_param = 'path=';
		$start = strpos($cookie_str, $cookie_param);
		if ($start > 0) {
			$path = substr($cookie_str, $start + strlen($cookie_param));
			$path = substr($path, 0, strpos($path, ';'));
		}
		else {
			$path = '/';
		}

		$sep_pos = strpos($value_str, '=');
		if ($sep_pos !== false){
			$name = substr($value_str, 0, $sep_pos);
			$value = substr($value_str, $sep_pos+1);
			$this->set($name, $value, $domain, $path, $expires);
		}
	}

}
?>