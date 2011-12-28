<?php
/**
 * Class to manage IPv4 data.
 *
 * @package		Core
 * @subpackage	Net
 * @author		Matthias Mohr
 * @since		1.0
 */
class IPv4 {

	private $ip;

	public function __construct() {
		$this->ip = $this->parseIP();
		if ($this->ip == null) {
			throw new IPException();
		}
	}

	public function __toString() {
		return $this->ip;
	}

	public function getIP($parts = 4) {
		if ($parts <= 3) {
			$arr = explode('.', $this->ip);
			$str = '';
			for ($i = 0; $i < $parts; $i++) {
				$str .= $arr[$i].'.';
			}
			return $str;
		}
		else {
			return $this->ip;
		}
	}

	public function compare($ip, $parts = 3) {
		if (IPv4::check($ip)) {
			return false;
		}
		else {
			$other = explode('.', $ip);
			$own = explode('.', $this->ip);
			for($i = 0; $i < $parts; $i++) {
				if ($other[$i] != $own[$i]) {
					return false;
				}
			}
			return true;
		}
	}

    /**
     * Validate the syntax of the given IPv4 adress.
     *
     * This function splits the IP adress in 4 pieces
     * (separated by &quot;.&quot;) and checks for each piece
     * if it's an integer value between 0 and 255.
     * If all 4 parameters pass this test, the function
     * returns true.
     *
     * If the second parameter is true, the function checks also whether the
     * given ip adress is a public ip address or a private.
     * If the ip adress is private then, false will be returned.
     *
     * <b>License:</b><br>
     * Permission is hereby granted, free of charge, to any person obtaining
     * a copy of this software and associated documentation files (the
     * "Software"), to deal in the Software without restriction, including
     * without limitation the rights to use, copy, modify, merge, publish,
     * distribute, sublicense, and/or sell copies of the Software, and to
     * permit persons to whom the Software is furnished to do so, subject to
     * the following conditions:
     *
     * The above copyright notice and this permission notice shall be included
     * in all copies or substantial portions of the Software.
     *
     * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS
     * OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
     * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL
     * THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
     * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
     * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER
     * DEALINGS IN THE SOFTWARE.
     *
     * @author Martin Jansen <mj@php.net>
     * @author Guido Haeger <gh-lists@ecora.de>
     * @param string IPv4 adress
     * @param boolean Check private ip adress
     * @return boolean true if syntax is valid, otherwise false
     * @copyright 2002-2006 Martin Jansen
     **/
    public static function check($ip, $check_private = false) {
        $oct = explode('.', $ip);
        if (count($oct) != 4) {
            return false;
        }
        for ($i = 0; $i < 4; $i++) {
            if (!is_numeric($oct[$i])) {
                return false;
            }
            if ($oct[$i] < 0 || $oct[$i] > 255) {
                return false;
            }
        }
        if ($check_private == true) {
        	return self::checkPrivate($ip);
        }
        else {
        	return true;
        }
    }

    public static function checkPrivate($ip) {
	   	$private_ips = array("/^0\..+$/", "/^127\.0\.0\..+$/", "/^192\.168\..+$/", "/^172\.16\..+$/", "/^10..+$/", "/^224..+$/", "/^240..+$/");
		foreach ($private_ips as $pip) {
			if (preg_match($pip, $ip)) {
				return false;
			}
		}
		return true;
    }


	private function parseIP() {
		$ips = array();
		$indices = array('REMOTE_ADDR', 'HTTP_X_FORWARDED_FOR', 'HTTP_CLIENT_IP');
		foreach ($indices as $index) {
			$tip = @getenv($index);
			if(!empty($tip)) {
				$ips[] = $tip;
			}
			if(!empty($_SERVER[$index])) {
				$ips[] = $_SERVER[$index];
			}
		}
		$ips = array_unique($ips);
		foreach ($ips as $key => $ip) {
			if (IPv4::check($ip)) {
				if (IPv4::checkPrivate($ip)) {
					return $ip;
				}
			}
			else {
				unset($ips[$key]);
			}
		}
		// Try a private one
		if (count($ips) > 0) {
			return end($ips);
		}
		else { // No ip found
			return null;
		}
	}
}
?>