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
	private $isSent;

	/**
	 * Starts the output buffer and checks for the capabillity to GZIP the content.
	 *
	 * @param int Compression level (0-9) or -1 for no compression.
	 */
	protected function __construct($level = -1) {
		$this->level = $level;
		$this->gz = false;
		$this->isSent = false;
		$this->isGzipCompatible();
		ob_start();
		ob_implicit_flush(0);
	}

	/**
	 * Flushes the output buffer if not already done.
	 */
	public function __destruct() {
		$this->send();
	}

	/**
	 * Sends a http status code to the client.
	 *
	 * Aditional header data can be send depending on the code number given in the first parameter.
	 * Only some error codes support this and each error code has its own additional header data.
	 * Supported additional headers:
	 * - 301/302/307 => Location: Specify a new location (url)
	 * - 401 => WWW-Authenticate: Specify a page name
	 * - 503 => Retry-after: Specify the time the page is unavailable
	 *
	 * @param int $code Error Code Number
	 * @param mixed $additional Additional Header data (depends in error code number)
	 * @return boolean
	 */
	function sendStatusCode($code, $additional = null) {
		$status = array(
			100 => 'Continue',
			101 => 'Switching Protocols',
			200 => 'OK',
			201 => 'Created',
			202 => 'Accepted',
			203 => 'Non-Authoritative Information',
			204 => 'No Content',
			205 => 'Reset Content',
			206 => 'Partial Content',
			300 => 'Multiple Choices',
			301 => 'Moved Permanently',
			302 => 'Moved Temporarily', // Found
			303 => 'See Other',
			304 => 'Not Modified',
			305 => 'Use Proxy',
			307 => 'Temporary Redirect',
			400 => 'Bad Request',
			401 => 'Authorization Required', // Unauthorized
			402 => 'Payment Required',
			403 => 'Forbidden',
			404 => 'Not Found',
			405 => 'Method Not Allowed',
			406 => 'Not Acceptable',
			407 => 'Proxy Authentication Required',
			408 => 'Request Time-Out',
			409 => 'Conflict',
			410 => 'Gone',
			411 => 'Length Required',
			412 => 'Precondition Failed',
			413 => 'Request Entity Too Large',
			414 => 'Request-URI Too Large',
			415 => 'Unsupported Media Type',
			416 => 'Request Rang Not Satisfiable',
			417 => 'Expectation Failed',
			500 => 'Internal Server Error',
			501 => 'Not Implemented',
			502 => 'Bad Gateway',
			503 => 'Service Temporarily Unavailable', // Service Unavailable
			504 => 'Gateway Time-Out',
			505 => 'HTTP Version not supported'
		);

		if (isset($status[$code])) {

			// Send status code
			$this->sendHeader("HTTP/1.1 {$code} {$status[$code]}");
			$this->sendHeader("Status: {$code} {$status[$code]}");

			// Additional headers
			if ($additional != null) {
				switch ($code) {
					case '301':
					case '302':
					case '307':
						$this->sendHeader("Location: {$additional}");
					break;
					case '401':
						$this->sendHeader('WWW-Authenticate: Basic Realm="'.$additional.'"');
					break;
					case '503':
						$this->sendHeader("Retry-After: {$additional}");
					break;
				}
			}

			return true;
		}
		else {
			return false;
		}
	}

	/**
	 * This function is used to send raw HTTP headers.
	 *
	 * Variable headers with header() are not secure in php (HTTP response Splitting). viscacha_header() removes \r, \n and \0.
	 *
	 * @param string Header to send
	 * @see http://www.php.net/header
	 */
	public function sendHeader($header) {
		$header = str_replace("\n", '', $header);
		$header = str_replace("\r", '', $header);
		$header = str_replace("\0", '', $header);
		header($header);
		return true;
	}

	public function sendNoCacheHeader() {
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
	public function sendCookie($name, $value = '', $maxage = 0, $HTTPOnly = false, $path = '/', $domain = '', $secure = false) {
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
		$this->sendHeader('Set-Cookie: '.rawurlencode($name).'='.rawurlencode($value)
									.(empty($domain) ? '' : '; Domain='.$domain)
									.(empty($maxage) ? '' : '; Max-Age='.$maxage)
									.(empty($path) ? '' : '; Path='.$path)
									.(!$secure ? '' : '; Secure')
									.(!$HTTPOnly ? '' : '; HttpOnly'), false);
		return true;
	}

	/**
	 * Flushs the output to the browser.
	 *
	 * If the variable flushed is set to true nothing will be done.
	 */
	public function send(){
		if ($this->isSent == false) {
			$content = ob_get_contents();
			ob_end_clean();

			// Place for rewriting or similar...

			if ($this->isGzipEnabled()) {
				$this->sendHeader("Content-Encoding: {$this->gz}");
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

			$this->isSent = true;
		}
	}

	/**
	 * Returns whether GZIP is used or not.
	 *
	 * @return boolean	Returns TRUE is GZIP is used, FALSE instead.
	 */
	public function isGzipEnabled() {
		return ($this->gz !== false && $this->level >= 0 && $this->level <= 9);
	}

	/**
	 * Checks whether GZIP compression can be used to compress the page.
	 *
	 * @return mixed Returns FALSE on failure or the type of compression that can be used (x-gzip ot gzip)
	 */
	private function isGzipCompatible() {
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
