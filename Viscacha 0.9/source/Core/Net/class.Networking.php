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
 * @subpackage	Net
 * @author		Matthias Mohr
 * @copyright	Copyright (c) 2004-2010, Viscacha.org
 * @license		http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License
 */

/**
 * Several utilities for the Net classes and general Network stuff.
 *
 * @package		Core
 * @subpackage	Net
 * @author		Matthias Mohr
 * @copyright	Copyright (c) 2004-2010, Viscacha.org
 * @since 		1.0
 */
class Networking {

	/**
	 * Encode a given UTF-8 domain name to ASCII Compatible Encoding (ACE).
	 *
	 * Example: www.österreich.at => www.xn--sterreich-z7a.at
	 *
	 * @param string Domain name to encode in UTF-8
	 * @return string Encoded domain name in ACE
	 * @see Networking::encodeIDNA()
	 * @link http://en.wikipedia.org/wiki/Internationalized_domain_name
	 */
	public static function encodeIDNA($domain) {
		return idn_to_ascii($domain);
	}

	/**
	 * Decode a given ASCII Compatible Encoding (ACE) domain name in to UTF-8.
	 *
	 * Example: www.xn--sterreich-z7a.at => www.österreich.at
	 *
	 * @param string Domain name to encode in ACE
	 * @return string Decoded domain name in UTF-8
	 * @see Networking::encodeIDNA()
	 * @link http://en.wikipedia.org/wiki/Internationalized_domain_name
	 */
	public static function decodeIDNA($domain) {
		return idn_to_utf8($domain);
	}

	public static function checkMX($host) {
		if (empty($host)) {
			return false;
		}
		$host_idna = self::encodeIDNA($host);
		if (function_exists('checkdnsrr') == true) {
			return checkdnsrr($host_idna, 'MX');
		}
		elseif (function_exists('getmxrr') == true) {
			return getmxrr($host_idna, $mxhosts);
		}
		elseif (function_exists('exec') == true) {
			@exec("nslookup -querytype=MX {$host_idna}", $output);
			while(list($k, $line) = each($output)) {
				// Valid records begin with host name
				$quote_host = preg_quote($host, '~');
				$quote_host_idna = preg_quote($host_idna, '~');
				if(preg_match("~^({$quote_host}|{$quote_host_idna})~i", $line) > 0) {
					return true;
				}
			}
		}
		return false;
	}

}
?>