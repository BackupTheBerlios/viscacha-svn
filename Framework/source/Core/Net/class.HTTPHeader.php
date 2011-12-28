<?php
/**
 * Manages the HTTP Headers and compression of a page.
 *
 * @package		Core
 * @subpackage	Net
 * @author		Matthias Mohr
 * @since 		1.0
 */
class HTTPHeader {

	/**
	 * Level of GZIP compression or -1 for no compression.
	 * @var int
	 */
	private $level;
	/**
	 * Tells which type of GZIP is used. IF GZIP is not used it is set to FALSE.
	 * @var mixed
	 */
	private $gz;
	/**
	 * Tells whether the ouput is already flushed or not.
	 * @var boolean
	 */
	private $flushed;

	/**
	 * Starts the output buffer and checks for the capabillity to GZIP the content.
	 *
	 * @param int Compression level (0-9) or -1 for no compression.
	 */
	public function __construct($level = -1) {
		$this->level = $level;
		$this->gz = false;
		$this->flushed = false;
		$this->checkGZIP();
		ob_start();
		ob_implicit_flush(0);
	}

	/**
	 * Flushes the output buffer if not already done.
	 */
	public function __destruct() {
		$this->flush();
	}

	/**
	 * This function is used to send raw HTTP headers.
	 *
	 * Variable headers with header() are not secure in php (HTTP response Splitting). viscacha_header() removes \r, \n and \0.
	 *
	 * @param string Header to send
	 * @see http://www.php.net/header
	 */
	public function rawHeader($header) {
		$header = str_replace("\n", '', $header);
		$header = str_replace("\r", '', $header);
		$header = str_replace("\0", '', $header);
		header($header);
		return true;
	}

	public function disableClientCache() {
		if (!empty($_SERVER['SERVER_SOFTWARE']) && strstr($_SERVER['SERVER_SOFTWARE'], 'Apache/2')) {
			header ('Cache-Control: no-cache, no-store, must-revalidate, pre-check=0, post-check=0');
		}
		else {
			header ('Cache-Control: private, no-store, must-revalidate, pre-check=0, post-check=0, max-age=0');
		}
		$now = gmdate('D, d M Y H:i:s').' GMT'; // rfc2616 - Section 14.21
		header ('Expires: '.$now);
		header ('Last-Modified: '.$now);
		header ('Pragma: no-cache');
	}

	/**
	 * A better alternative (RFC 2109 compatible) to the php setcookie() function.
	 *
	 * @param string Name of the cookie
	 * @param string Value of the cookie
	 * @param int Lifetime of the cookie
	 * @param bool Only allow HTTP usage?
	 * @param string Path where the cookie can be used
	 * @param string Domain which can read the cookie
	 * @param bool Secure mode?
	 * @return bool True or false whether the method has successfully run
	 */
	public function setCookie($name, $value = '', $maxage = 0, $HTTPOnly = false, $path = '/', $domain = '', $secure = false) {
		if (!empty($domain)) {
			// Fix the domain to accept domains with and without 'www.'.
			if (strtolower( substr($domain, 0, 4) ) == 'www.') {
				$domain = substr($domain, 4);
			}
			// Add the dot prefix to ensure compatibility with subdomains
			if (substr($domain, 0, 1) != '.') {
				$domain = '.'.$domain;
			}
			// Remove port information.
			$port = strpos($domain, ':');
			if ($port !== false) {
				$domain = substr($domain, 0, $port);
			}
		}
		$name = Config::get("http.cookie_prefix") . $name;
		$this->rawHeader('Set-Cookie: '.rawurlencode($name).'='.rawurlencode($value)
									.(empty($domain) ? '' : '; Domain='.$domain)
									.(empty($maxage) ? '' : '; Max-Age='.$maxage)
									.(empty($path) ? '' : '; Path='.$path)
									.(!$secure ? '' : '; Secure')
									.(!$HTTPOnly ? '' : '; HttpOnly'), false);
		return true;
	}

	/**
	 * Returns the value of the specified cookie.
	 *
	 * If there is no cookie with the specified name, null will be returned.
	 *
	 * @param string $name Name of the cookie
	 * @return mixed Content of the cookie
	 */
	public function getCookie($name) {
		$name = Config::get("http.cookie_prefix") . $name;
		if (isset($_COOKIE[$name])) {
			return $_COOKIE[$name];
		}
		else {
			return null;
		}
	}

	/**
	 * Flushs the output to the browser.
	 *
	 * If the variable flushed is set to true nothing will be done.
	 */
	public function flush(){
		if ($this->flushed == false) {
			$content = ob_get_contents();
			ob_end_clean();

			$this->flushed = true;

			// here can be done some rewriting

			if ($this->useGZIP()) {
				$this->rawHeader("Content-Encoding: {$this->gz}");
				echo "\x1f\x8b\x08\x00\x00\x00\x00\x00";
				$content = gzcompress($content, $this->level);
				$content = substr($content, 0, strlen($content) - 4);
				echo $content;
				echo pack('V', crc32($content));
				echo pack('V', strlen($content));
			}
			else{
				echo $content;
			}
		}
	}

	/**
	 * Returns whether GZIP is used or not.
	 *
	 * @return boolean	Returns TRUE is GZIP is used, FALSE instead.
	 */
	public function useGZIP() {
		return ($this->gz != false && $this->level >= 0 && $this->level <= 9);
	}

	/**
	 * Checks whether GZIP compression can be used to compress the page.
	 *
	 * @return mixed Returns FALSE on failure or the type of compression that can be used (x-gzip ot gzip)
	 */
	private function checkGZIP() {
		if (empty($_SERVER['HTTP_ACCEPT_ENCODING']) == false && headers_sent() == false && connection_aborted() == false && function_exists('gzcompress') == true) {
			if (strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'x-gzip') !== false) {
				$this->gz = 'x-gzip';
			}
			elseif (strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== false) {
				$this->gz = 'gzip';
			}
			else {
				$this->gz = false;
			}
		}
		else {
			$this->gz = false;
		}
		return $this->gz;
	}

}
?>
