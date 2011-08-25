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
 * A HTTP client class - HTTPClient
 *
 * Supports:
 *  GET, HEAD and POST methods,
 *  Http cookies,
 *  multipart/form-data AND application/x-www-form-urlencoded,
 *  Chunked Transfer-Encoding,
 *  HTTP 1.0 and 1.1 protocols,
 *  Keep-Alive Connections,
 *  Proxy,
 *  Basic WWW-Authentification and Proxy-Authentification
 *
 * Examples:
 * <code>
 * // Example 1: Start a GET request (Get search results from twitter)
 * $http_client = new HTTPClient(HTTPClient::V10); // Use http protocoll version 1.0
 * $http_client->setServer('http://search.twitter.com');
 * $status = $http_client->get('/search.json?q=viscacha'); // viscacha is the keyword to search for
 * if ($status == 200) { // Error Code 200 means OK
 *   echo $http_client->getResponseBody(); // We should use a JSON parser now
 * }
 * else {
 *   echo 'Error: Status code ' . $status . ' and error message ' . $http_client->getError();
 * }
 * $http_client->disconnect();
 * </code>
 *
 * <code>
 * // Example 2: Start a POST request using a proxy (login somewhere)
 * $form_data = array(
 *   'username' => 'Guest', // Form field 'username' with value 'Guest'
 *   'password' => '12345', // Form field 'password' with value 'Guest'
 *   'submit' => 'Submit' // Could be the submit buttpnwith name 'submit' and value 'Submit'
 * );
 * $http_client = new HTTPClient();
 * $http_client->setServer('yourdomain.com', 8080); // Connect to server yourdomain.com on port 8080
 * $http_client->useProxy('ns.crs.org.ni', 3128); // Using the proxy ns.crs.org.ni on port 3128
 * // Try to send the post request to login.php using the data from above. We allow to follow
 * // redirects (requested page sends us to another page) and we send a referer (last parameter).
 * $status = $http_client->post('/login.php', $form_data, true, 'http://www.yourdomain.com/');
 * if ($status == 200) {
 *   // Here we should check whether login was successful (e.q. with preg_match)
 *   echo $http_client->getResponseBody();
 * }
 * else {
 *   echo 'Error: Status code ' . $status . ' and error message ' . $http_client->getError();
 * }
 * $http_client->disconnect();
 * </code>
 *
 * <code>
 * // Example 3: Start a multipart POST request (Upload a file somewhere)
 * $form_data = array(
 *   'title' => 'Upload test', // Form field 'title'
 *   'description' => 'Just a short file to test the upload', // Form field 'description'
 * );
 * $files = array(
 *   array(
 *     'name' => 'file', // name of the form field (with type=file)
 *     'content-type' => 'text/plain', // Mime type (see MimeType class for automatic detection)
 *     'filename' => '/path/to/file.txt', // Path to the file
 *     // Optional: Data to upload (or file will be read in BINARY mode)
 *     'data' => "This is the data to upload..."
 *   )
 * );
 * $http_client = new HTTPClient();
 * $http_client->setServer('filehost.com');
 * $status = $http_client->multipartPost('/upload.jsp', $form_data, $files);
 * unset($http_client); // This calls $http_client->disconnect(); automatically
 * // Attention: Status 200 does not confirm a successful upload, server returned just a page...
 * echo ($status == 200) ? 'Sent file...', 'Error occured';
 * </code>
 *
 * @package		Core
 * @subpackage	Net
 * @author		GuinuX <guinux@cosmoplazza.com>
 * @author		Matthias Mohr
 * @since 		1.0
 */
class HTTPClient {

	const CRLF = "\r\n";

	const V10 = '1.0';
	const V11 = '1.1';

	const POST = 'POST';
	const GET = 'GET';
	const HEAD = 'HEAD';

	private $socket;
	private $proxy_host;
	private $proxy_port;
	private $proxy_login;
	private $proxy_pwd;
	private $use_proxy;
	private $auth_login;
	private $auth_pwd;
	private $response;
	private $request;
	private $keep_alive;
	private $current_redirect_depth;
	private $max_redirect_depth;
	private $redirect_codes;
	private $host;
	private $port;
	private $errstr;
	private $timeout;

	public $http_version;
	public $user_agent;
	public $connected;
	public $uri;

	public function __construct($http_version = self::V11, $keep_alive = false, array $auth = array()) {
		$this->http_version = $http_version;
		$this->connected = false;
		$this->user_agent = 'Mozilla/5.0 (compatible; MSIE 8.0; Windows) Viscacha HTTPClient';
		$this->errstr = '';
		$this->keep_alive = $keep_alive;
		$this->timeout = 10;
		$this->proxy_host = '';
		$this->proxy_port = -1;
		$this->proxy_login = '';
		$this->proxy_pwd = '';
		$this->use_proxy = false;
		$this->response = new HTTPClientResponseMessage();
		$this->request = new HTTPClientRequestMessage();
		$this->setServer('');
		$this->setRedirectOptions(3);
		// Basic Authentification added by Mate Jovic, 2002-18-06, jovic@matoma.de
		if(count($auth) == 2){
			$this->auth_login = $auth[0];
			$this->auth_pwd	= $auth[1];
		}
		else {
			$this->auth_login = '';
			$this->auth_pwd	= '';
		}
	}

	public function  __destruct() {
		$this->disconnect();
	}

	public function getError() {
		return $this->errstr;
	}

	public function setTimeout($timeout) {
		$this->timeout = $timeout;
	}

	public function setServer($host, $port = 80) {
		$this->host = preg_replace('~^[\w\d]+://~i', '', $host); // Remove protocoll
		$this->port = $port;
	}

	// Status codes: Moved Permanently, Found, See other and Moved Temporary
	public function setRedirectOptions($max_depth, array $codes = array(301, 302, 303, 307)) {
		$this->max_redirect_depth = $max_depth;
		$this->current_redirect_depth = 0;
		$this->redirect_codes = $codes;
	}

	// TODO : Implement Proxy auth (Proxy auth not yet supported)
	public function useProxy($host, $port, $proxy_login = null, $proxy_pwd = null) {
		$this->http_version	= self::V10;
		$this->keep_alive = false;
		$this->proxy_host = $host;
		$this->proxy_port = $port;
		$this->proxy_login = $proxy_login;
		$this->proxy_pwd = $proxy_pwd;
		$this->use_proxy = true;
	}

	public function setRequestHeader($name, $value) {
		$this->request->setHeader($name, $value);
	}

	public function getResponseBody() {
		return $this->response->getBody();
	}

	public function getResponse() {
		return $this->response;
	}

	public function disconnect() {
		if ($this->socket && $this->connected) {
			fclose($this->socket);
			$this->connected = false;
		}
	}

	public function get($uri, $follow_redirects = true, $referer = null) {
		$this->current_redirect_depth = 0;
		return $this->doRequest(self::GET, __METHOD__, $uri, $follow_redirects, $referer);
	}

	public function head($uri) {
		return $this->doRequest(self::HEAD, __METHOD__, $uri);
	}

	public function multipartPost($uri, $form_fields, $form_files = null, $follow_redirects = true, $referer = null) {
		return $this->doRequest(
			self::POST,
			__METHOD__,
			$uri,
			$follow_redirects,
			$referer,
			compact($form_fields, $form_files)
		);
	}

	public function post($uri, $form_data, $follow_redirects = true, $referer = null) {
		return $this->doRequest(
			self::POST,
			__METHOD__,
			$uri,
			$follow_redirects,
			$referer,
			compact($form_data)
		);
	}

	public function postXML($uri, $xml_data, $follow_redirects = true, $referer = null) {
		return $this->doRequest(
			self::POST,
			__METHOD__,
			$uri,
			$follow_redirects,
			$referer,
			compact($xml_data)
		);
	}

	private function doRequest($method, $function, $uri, $follow_redirects = false, $referer = null, array $params = array()) {
		extract($params);

		$this->uri = $uri;

		if (($this->keep_alive && !$this->connected) || !$this->keep_alive) {
			if (!$this->connect()) {
				// TODO : This message shouldn't be here directly
				$this->errstr = 'Could not connect to ' . $this->host;
				return -1;
			}
		}

		if ($this->use_proxy) {
			$this->request->setHeader('Host', $this->host . ':' . $this->port);
			$this->request->setHeader(
				'Proxy-Connection',
				($this->keep_alive ? 'Keep-Alive' : 'Close')
			);
			if (!empty($this->proxy_login)) {
				$this->request->setHeader(
					'Proxy-Authorization',
					"Basic " . base64_encode($this->proxy_login . ':' . $this->proxy_pwd)
				);
			}
			$uri = 'http://' . $this->host . ':' . $this->port . $uri;
		}
		else {
			$this->request->setHeader('Host', $this->host);
			$this->request->setHeader('Connection', ($this->keep_alive ? 'Keep-Alive' : 'Close'));
			if ($method != 'HEAD') {
				$this->request->setHeader('Pragma', 'no-cache');
				$this->request->setHeader('Cache-Control', 'no-cache');
			}
		}

		if (!empty($this->auth_login)) {
			$this->request->setHeader(
				'Authorization',
				"Basic " . base64_encode($this->auth_login . ":" . $this->auth_pwd)
			);
		}

		if ($function == 'multipartPost') {
			$boundary = uniqid('------------------');
			$body = $this->mergeMultipartFormData($boundary, $form_fields, $form_files);
			$body .= self::CRLF;
			$this->request->setBody($body);

			$this->request->setHeader('Content-Type', 'multipart/form-data; boundary=' . $boundary);
		}
		elseif ($function == 'post') {
			$body = substr($this->mergeFormData($form_data), 1);
			$body .= self::CRLF . self::CRLF;
			$this->request->setBody($body);

			$this->request->setHeader('Content-Type', 'application/x-www-form-urlencoded');
		}
		elseif ($function == 'postXML') {
			$body = $xml_data . self::CRLF . self::CRLF;
			$this->request->setBody($body);

			$this->request->setHeader('Content-Type', 'text/xml; charset=utf-8');
		}
		else {
			$body = null;
		}

		if ($method == self::POST && $body !== null) {
			$this->request->setHeader('Content-Length', strlen($body));
		}
		if ($method != self::HEAD && !empty($referer)) {
			$this->request->setHeader('Referer', $referer);
		}
		$this->request->setHeader('User-Agent', $this->user_agent);
		$this->request->setHeader('Accept', '*/*');
		$this->request->setHeader(
			'Cookie',
			$this->response->getCookies()->get(
				$this->host,
				$this->currentDirectory($uri)
			)
		);

		$cmd = "{$method} {$uri} HTTP/{$this->http_version}" . self::CRLF;
		$cmd .= $this->request->serializeHeaders() . self::CRLF;

		// ToDo : Remove the settype and do it in another way
		settype($cmd, 'binary');
		fwrite($this->socket, $cmd);
		if ($method == self::POST && $body !== null) {
			usleep(10);
			// ToDo : Remove the settype and do it in another way
			settype($body, 'binary');
			fwrite($this->socket, $body);
		}

		$readResp = ($method != self::HEAD);
		$this->readResponse($readResp);

		if ($this->socket && !$this->keep_alive) {
			$this->disconnect();
		}

		$connHeader = $this->response->getHeader('Connection');
		if ($connHeader != null && $this->keep_alive && strtolower($connHeader) == 'close') {
			$this->keep_alive = false;
			$this->disconnect();
		}

		$status = $this->response->getStatus();

		if ($follow_redirects && $this->max_redirect_depth > $this->current_redirect_depth) {
			$location = $this->response->getHeader('Location');
			if (in_array($status, $this->redirect_codes) && $location != null) {
				$this->redirect($location, $follow_redirects, $referer);
			}
		}

		if ($this->response->getStatus() == 305) { // Use proxy
			$location = $this->parseLocation($this->response->getHeader('Location'));
			$this->disconnect();
			$this->useProxy($location['host'], $location['port']);
			$params['uri'] = $this->uri;
			$params['follow_redirects'] = false;
			$this->doRequest($method, $function, $uri, $follow_redirects, $referer, $params);
		}

		return $this->response->getStatus();
	}

	private function connect() {
		if ($this->host == '') {
			// ToDo : Replace with Exception or something...
			trigger_error('Class HTTP::connect() : host property not set !', E_ERROR);
		}
		if (!$this->use_proxy) {
			$this->socket = fsockopen(
				Networking::encodeIDNA($this->host),
				$this->port,
				$errno,
				$errstr,
				$this->timeout
			);
		}
		else {
			$this->socket = fsockopen(
				Networking::encodeIDNA($this->proxy_host),
				$this->proxy_port,
				$errno,
				$errstr,
				$this->timeout
			);
		}
		$this->errstr  = $errstr;
		$this->connected = ($this->socket == true);
		return $this->connected;
	}


	private function mergeMultipartFormData($boundary, &$form_fields, &$form_files) {
		$boundary = '--' . $boundary;
		$multipart_body = '';
		foreach ($form_fields as $name => $data) {
			$multipart_body .= $boundary . self::CRLF;
			$multipart_body .= 'Content-Disposition: form-data; name="' . $name . '"' . self::CRLF;
			$multipart_body .=  self::CRLF;
			$multipart_body .= $data . self::CRLF;
		}
		if (!empty($form_files)) {
			foreach ($form_files as $data) {
				$multipart_body .= $boundary . self::CRLF;
				$multipart_body .= 'Content-Disposition: form-data; name="' . $data['name'] . '"; filename="' . $data['filename'] . '"' . self::CRLF;
				if ($data['content-type']!='') {
					$multipart_body .= 'Content-Type: ' . $data['content-type'] . self::CRLF;
				}
				else {
					$multipart_body .= 'Content-Type: application/octet-stream' . self::CRLF;
				}
				if (!isset($data['data'])) {
					$fileObject = new File($data['filename']);
					$data['data'] = $fileObject->read(File::READ_STRING, File::BINARY);
				}
				$multipart_body .=  self::CRLF;
				$multipart_body .= $data['data'] . self::CRLF;
			}
		}
		$multipart_body .= $boundary . '--' . self::CRLF;
		return $multipart_body;
	}


	private function mergeFormData(&$param_array, $param_name = '') {
		$params = '';
		$format = ($param_name != '') ? '&'.$param_name.'[%s]=%s' : '&%s=%s';
		foreach ($param_array as $key => $value) {
			if (!is_array($value)) {
				$params .= sprintf($format, $key, urlencode($value));
			}
			else {
				$params .= $this->mergeFormData($param_array[$key], $key);
			}
		}
		return $params;
	}

	private function currentDirectory($uri) {
		$tmp = explode('/', $uri);
		array_pop($tmp);
		$current_dir = implode('/', $tmp) . '/';
		return ($current_dir!=''?$current_dir:'/');
	}


	private function readResponse($get_body = true) {
		$this->response->reset();
		$this->request->reset();
		$header = '';
		$body = '';
		$continue	= true;

		while ($continue) {
			$header = '';

			// Read the Response Headers
			while ((($line = fgets($this->socket, 4096)) != self::CRLF || $header == '') && !feof($this->socket)) {
				if ($line != self::CRLF) {
					$header .= $line;
				}
			}
			$this->response->deserializeHeaders($header);
			$this->response->parseCookies($this->host);

			$continue = ($this->response->getStatus() == 100);
			if ($continue) {
				fwrite($this->socket, self::CRLF);
			}
		}

		if (!$get_body) {
			return;
		}

		// Read the Response Body
		if (strtolower($this->response->getHeader('Transfer-Encoding')) != 'chunked' && !$this->keep_alive) {
			while (!feof($this->socket)) {
				$body .= fread($this->socket, 4096);
			}
		}
		else {
			if ($this->response->getHeader('Content-Length') != null) {
				$content_length = (int) $this->response->getHeader('Content-Length');
				$body = fread($this->socket, $content_length);
			}
			else {
				if ($this->response->getHeader('Transfer-Encoding') != null) {
					if (strtolower($this->response->getHeader('Transfer-Encoding')) == 'chunked') {
						$chunk_size = (int) hexdec(fgets($this->socket, 4096));
						while($chunk_size > 0) {
							$body .= fread($this->socket, $chunk_size);
							fread($this->socket, strlen(self::CRLF));
							$chunk_size = (int) hexdec(fgets($this->socket, 4096));
						}
						// TODO : Read trailing http headers
					}
				}
			}
		}
		$this->response->setBody($body);
	}


	private function parseLocation($redirect_uri) {
		$parsed_url = parse_url($redirect_uri);
		$scheme = (isset($parsed_url['scheme']) ? $parsed_url['scheme'] : '');
		$port = (isset($parsed_url['port']) ? $parsed_url['port'] : $this->port);
		$host = (isset($parsed_url['host']) ? $parsed_url['host'] : $this->host);
		$request_file = (isset($parsed_url['path']) ? $parsed_url['path'] : '');
		$query_string = (isset($parsed_url['query']) ? $parsed_url['query'] : '');
		if ($request_file[0] != '/') {
			$request_file = $this->currentDirectory($this->uri) . $request_file;
		}

		return array(
			'scheme' => $scheme,
			'port' => $port,
			'host' => $host,
			'request_file' => $request_file,
			'query_string' => $query_string
		);

	}


	private function redirect($uri, $follow_redirects = true, $referer = null) {
		$this->current_redirect_depth++;
		$location = $this->parseLocation($uri);
		if ($location['host'] != $this->host || $location['port'] != $this->port) {
			$this->host = $location['host'];
			$this->port = $location['port'];
			if (!$this->use_proxy) {
				$this->disconnect();
			}
		}
		usleep(100);
		$uri = $location['request_file'] . '?' . $location['query_string'];
		$this->doRequest(self::GET, 'get', $uri, $follow_redirects, $referer);
	}

}
?>