<?php
/**************************************************************************************************
* Class: Advanced HTTP Client
***************************************************************************************************
* Version 		: 1.1
* Released		: 06-20-2002
* Last Modified : 06-10-2003
* Author		: GuinuX <guinux@cosmoplazza.com>
*
***************************************************************************************************
* Changes
***************************************************************************************************
* 2003-06-10 : GuinuX
*   - Fixed a bug with multiple gets and basic auth
*   - Added support for Basic proxy Authentification
* 2003-05-25: By Michael Mauch <michael.mauch@gmx.de>
*	- Fixed two occurences of the former "status" member which is now deprecated
* 2002-09-23: GuinuX
*	- Fixed a bug to the post method with some HTTP servers
*	- Thanx to l0rd jenci <lord_jenci@bigfoot.com> for reporting this bug.
* 2002-09-07: Dirk Fokken <fokken@cross-consulting.com>
*   - Deleted trailing characters at the end of the file, right after the php closing tag, in order
*	  to fix a bug with binary requests.
* 2002-20-06: GuinuX, Major changes
*	- Turned to a more OOP style => added class http_header, http_response_header,
*		http_request_message, http_response_message.
*		The members : status, body, response_headers, cookies, _request_headers of the http class
*		are Deprecated.
* 2002-19-06: GuinuX, fixed some bugs in the http::_get_response() method
* 2002-18-06: By Mate Jovic <jovic@matoma.de>
*	- Added support for Basic Authentification
*  		usage: $http_client = new http( HTTP_V11, false, Array('user','pass') );
*
***************************************************************************************************
* Description:
***************************************************************************************************
*	A HTTP client class
* 	Supports :
*			- GET, HEAD and POST methods
*			- Http cookies
*			- multipart/form-data AND application/x-www-form-urlencoded
*			- Chunked Transfer-Encoding
*			- HTTP 1.0 and 1.1 protocols
*			- Keep-Alive Connections
*			- Proxy
*			- Basic WWW-Authentification and Proxy-Authentification
*
***************************************************************************************************
* TODO :
***************************************************************************************************
*			- Read trailing headers for Chunked Transfer-Encoding
***************************************************************************************************
* usage
***************************************************************************************************
* See example scripts.
*
***************************************************************************************************
* License
***************************************************************************************************
* GNU Lesser General Public License (LGPL)
* http://www.opensource.org/licenses/lgpl-license.html
*
* For any suggestions or bug report please contact me : guinux@cosmoplazza.com
***************************************************************************************************/

define( 'HTTP_STATUS_CONTINUE', 				100 );
define( 'HTTP_STATUS_SWITCHING_PROTOCOLS', 		101 );
define( 'HTTP_STATUS_OK', 						200 );
define( 'HTTP_STATUS_CREATED', 					201 );
define( 'HTTP_STATUS_ACCEPTED', 				202 );
define( 'HTTP_STATUS_NON_AUTHORITATIVE', 		203 );
define( 'HTTP_STATUS_NO_CONTENT', 				204 );
define( 'HTTP_STATUS_RESET_CONTENT', 			205 );
define( 'HTTP_STATUS_PARTIAL_CONTENT', 			206 );
define( 'HTTP_STATUS_MULTIPLE_CHOICES', 		300 );
define( 'HTTP_STATUS_MOVED_PERMANENTLY', 		301 );
define( 'HTTP_STATUS_FOUND', 					302 );
define( 'HTTP_STATUS_SEE_OTHER', 				303 );
define( 'HTTP_STATUS_NOT_MODIFIED', 			304 );
define( 'HTTP_STATUS_USE_PROXY', 				305 );
define( 'HTTP_STATUS_TEMPORARY_REDIRECT', 		307 );
define( 'HTTP_STATUS_BAD_REQUEST', 				400 );
define( 'HTTP_STATUS_UNAUTHORIZED', 			401 );
define( 'HTTP_STATUS_FORBIDDEN', 				403 );
define( 'HTTP_STATUS_NOT_FOUND', 				404 );
define( 'HTTP_STATUS_METHOD_NOT_ALLOWED', 		405 );
define( 'HTTP_STATUS_NOT_ACCEPTABLE', 			406 );
define( 'HTTP_STATUS_PROXY_AUTH_REQUIRED', 		407 );
define( 'HTTP_STATUS_REQUEST_TIMEOUT', 			408 );
define( 'HTTP_STATUS_CONFLICT', 				409 );
define( 'HTTP_STATUS_GONE', 					410 );
define( 'HTTP_STATUS_REQUEST_TOO_LARGE',		413 );
define( 'HTTP_STATUS_URI_TOO_LONG', 			414 );
define( 'HTTP_STATUS_SERVER_ERROR', 			500 );
define( 'HTTP_STATUS_NOT_IMPLEMENTED',			501 );
define( 'HTTP_STATUS_BAD_GATEWAY',				502 );
define( 'HTTP_STATUS_SERVICE_UNAVAILABLE',		503 );
define( 'HTTP_STATUS_VERSION_NOT_SUPPORTED',	505 );

class HTTPClient {

	const CRLF = "\r\n";
	const V10 = '1.0';
	const V11 = '1.1';

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

	public $host;
	public $port;
	public $http_version;
	public $user_agent;
	public $errstr;
	public $connected;
	public $uri;

	public function __construct($http_version = self::V10, $keep_alive = false, $auth = false) {
		$this->http_version = $http_version;
		$this->connected = false;
		$this->user_agent = 'Mozilla/5.0 (compatible; MSIE 8.0; Windows) Viscacha HTTPClient';
		$this->host = '';
		$this->port = 80;
		$this->errstr = '';
		$this->keep_alive = $keep_alive;
		$this->proxy_host = '';
		$this->proxy_port = -1;
		$this->proxy_login = '';
		$this->proxy_pwd = '';
		$this->use_proxy = false;
		$this->response = new HTTPResponseMessage();
		$this->request = new HTTPRequestMessage();

		// Basic Authentification added by Mate Jovic, 2002-18-06, jovic@matoma.de
		if(is_array($auth) && count($auth) == 2){
			$this->auth_login = $auth[0];
			$this->auth_pwd	= $auth[1];
		}
		else {
			$this->auth_login = '';
			$this->auth_pwd	= '';
		}
	}

	// Todo: Implement Proxi auth (Proxy auth not yet supported)
	public function useProxy($host, $port, $proxy_login = null, $proxy_pwd = null) {
		$this->http_version	= self::V10;
		$this->keep_alive	= false;
		$this->proxy_host	= $host;
		$this->proxy_port	= $port;
		$this->proxy_login	= $proxy_login;
		$this->proxy_pwd	= $proxy_pwd;
		$this->use_proxy	= true;
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

	public function head($uri) {
		$this->uri = $uri;

		if (($this->keep_alive && !$this->connected) || !$this->keep_alive) {
			if (!$this->connect()) {
				// Todo: This message shouldn't be here directly
				$this->errstr = 'Could not connect to ' . $this->host;
				return -1;
			}
		}
		$http_cookie = $this->response->cookies->get($this->host, $this->currentDirectory($uri));

		if ($this->use_proxy) {
			$this->request->setHeader( 'Host', $this->host . ':' . $this->port );
			$this->request->setHeader( 'Proxy-Connection', ($this->keep_alive?'Keep-Alive':'Close') );
			if ( $this->proxy_login != '' ) $this->request->setHeader( 'Proxy-Authorization', "Basic " . base64_encode( $this->proxy_login . ":" . $this->proxy_pwd ) );
			$uri = 'http://' . $this->host . ':' . $this->port . $uri;
		}
		else {
			$this->request->setHeader( 'Host', $this->host );
			$this->request->setHeader( 'Connection', ($this->keep_alive?'Keep-Alive':'Close') );
		}

		if ( $this->auth_login != '' ) $this->request->setHeader( 'Authorization', "Basic " . base64_encode( $this->auth_login . ":" . $this->auth_pwd ) );
		$this->request->setHeader( 'User-Agent', $this->user_agent );
		$this->request->setHeader( 'Accept', '*/*' );
		$this->request->setHeader( 'Cookie', $http_cookie );

		$cmd =	"HEAD $uri HTTP/" . $this->http_version . self::CRLF .
				$this->request->serializeHeaders() .
				self::CRLF;
		fwrite( $this->socket, $cmd );

		$this->readResponse( false );

		if ($this->socket && !$this->keep_alive) $this->disconnect();
		if ( $this->response->getHeader( 'Connection' ) != null ) {
			if ( $this->keep_alive && strtolower( $this->response->getHeader( 'Connection' ) ) == 'close' ) {
				$this->keep_alive = false;
				$this->disconnect();
			}
		}

		if ( $this->response->getStatus() == HTTP_STATUS_USE_PROXY ) {
			$location = $this->parseLocation( $this->response->getHeader( 'Location' ) );
			$this->disconnect();
			$this->useProxy( $location['host'], $location['port'] );
			$this->head( $this->uri );
		}

		return $this->response->getHeader( 'Status' );
	}


	public function get( $uri, $follow_redirects = true, $referer = '' ) {
		$this->uri = $uri;

		if ( ($this->keep_alive && !$this->connected) || !$this->keep_alive ) {
			if ( !$this->connect() ) {
				$this->errstr = 'Could not connect to ' . $this->host;
				return -1;
			}
		}

		if ($this->use_proxy) {
			$this->request->setHeader( 'Host', $this->host . ':' . $this->port );
			$this->request->setHeader( 'Proxy-Connection', ($this->keep_alive?'Keep-Alive':'Close') );
			if ( $this->proxy_login != '' ) {
				$this->request->setHeader( 'Proxy-Authorization', "Basic " . base64_encode( $this->proxy_login . ":" . $this->proxy_pwd ) );
			}
			$uri = 'http://' . $this->host . ':' . $this->port . $uri;
		}
		else {
			$this->request->setHeader( 'Host', $this->host );
			$this->request->setHeader( 'Connection', ($this->keep_alive?'Keep-Alive':'Close') );
			$this->request->setHeader( 'Pragma', 'no-cache' );
			$this->request->setHeader( 'Cache-Control', 'no-cache' );
		}

		if ( $this->auth_login != '' ) {
			$this->request->setHeader( 'Authorization', "Basic " . base64_encode( $this->auth_login . ":" . $this->auth_pwd ) );
		}
		$http_cookie = $this->response->cookies->get( $this->host, $this->currentDirectory( $uri ) );
		$this->request->setHeader( 'User-Agent', $this->user_agent );
		$this->request->setHeader( 'Accept', '*/*' );
		$this->request->setHeader( 'Referer', $referer );
		$this->request->setHeader( 'Cookie', $http_cookie );

		$cmd = "GET $uri HTTP/" . $this->http_version . self::CRLF . $this->request->serializeHeaders() . self::CRLF;
		fwrite( $this->socket, $cmd );

		$this->readResponse();

		if ($this->socket && !$this->keep_alive) $this->disconnect();
		if (  $this->response->getHeader( 'Connection' ) != null ) {
			if ( $this->keep_alive && strtolower( $this->response->getHeader( 'Connection' ) ) == 'close' ) {
				$this->keep_alive = false;
				$this->disconnect();
			}
		}
		if ( $follow_redirects && ($this->response->getStatus() == HTTP_STATUS_MOVED_PERMANENTLY || $this->response->getStatus() == HTTP_STATUS_FOUND || $this->response->getStatus() == HTTP_STATUS_SEE_OTHER ) ) {
			if ( $this->response->getHeader( 'Location' ) != null  ) {
				$this->redirect( $this->response->getHeader( 'Location' ) );
			}
		}

		if ( $this->response->getStatus() == HTTP_STATUS_USE_PROXY ) {
			$location = $this->parseLocation( $this->response->getHeader( 'Location' ) );
			$this->disconnect();
			$this->useProxy( $location['host'], $location['port'] );
			$this->get( $this->uri, $referer );
		}

		return $this->response->getStatus();
	}



	public function multipartPost( $uri, &$form_fields, $form_files = null, $follow_redirects = true, $referer = '' ) {
		$this->uri = $uri;

		if ( ($this->keep_alive && !$this->connected) || !$this->keep_alive ) {
			if ( !$this->connect() ) {
				$this->errstr = 'Could not connect to ' . $this->host;
				return -1;
			}
		}
		$boundary = uniqid('------------------');
		$http_cookie = $this->response->cookies->get( $this->host, $this->currentDirectory( $uri ) );
		$body = $this->mergeMultipartFormData( $boundary, $form_fields, $form_files );
		$this->request->body =  $body . self::CRLF;
		$content_length = strlen( $body );


		if ($this->use_proxy) {
			$this->request->setHeader( 'Host', $this->host . ':' . $this->port );
			$this->request->setHeader( 'Proxy-Connection', ($this->keep_alive?'Keep-Alive':'Close') );
			if ( $this->proxy_login != '' ) $this->request->setHeader( 'Proxy-Authorization', "Basic " . base64_encode( $this->proxy_login . ":" . $this->proxy_pwd ) );
			$uri = 'http://' . $this->host . ':' . $this->port . $uri;
		} else {
			$this->request->setHeader( 'Host', $this->host );
			$this->request->setHeader( 'Connection', ($this->keep_alive?'Keep-Alive':'Close') );
			$this->request->setHeader( 'Pragma', 'no-cache' );
			$this->request->setHeader( 'Cache-Control', 'no-cache' );
		}

		if ( $this->auth_login != '' ) $this->request->setHeader( 'Authorization', "Basic " . base64_encode( $this->auth_login . ":" . $this->auth_pwd ) );
		$this->request->setHeader( 'Accept', '*/*' );
		$this->request->setHeader( 'Content-Type', 'multipart/form-data; boundary=' . $boundary );
		$this->request->setHeader( 'User-Agent', $this->user_agent );
		$this->request->setHeader( 'Content-Length', $content_length );
		$this->request->setHeader( 'Cookie', $http_cookie );
		$this->request->setHeader( 'Referer', $referer );

		$req_header	= "POST $uri HTTP/" . $this->http_version . self::CRLF . $this->request->serializeHeaders() . self::CRLF;

		fwrite( $this->socket, $req_header );
		usleep(10);
		fwrite( $this->socket, $this->request->body );

		$this->readResponse();

		if ($this->socket && !$this->keep_alive) $this->disconnect();
		if ( $this->response->getHeader( 'Connection' ) != null ) {
			if ( $this->keep_alive && strtolower( $this->response->getHeader( 'Connection' ) ) == 'close' ) {
				$this->keep_alive = false;
				$this->disconnect();
			}
		}

		if ( $follow_redirects && ($this->response->getStatus() == HTTP_STATUS_MOVED_PERMANENTLY || $this->response->getStatus() == HTTP_STATUS_FOUND || $this->response->getStatus() == HTTP_STATUS_SEE_OTHER ) ) {
			if ( $this->response->getHeader( 'Location') != null ) {
				$this->redirect( $this->response->getHeader( 'Location') );
			}
		}

		if ( $this->response->getStatus() == HTTP_STATUS_USE_PROXY ) {
			$location = $this->parseLocation( $this->response->getHeader( 'Location') );
			$this->disconnect();
			$this->useProxy( $location['host'], $location['port'] );
			$this->multipartPost( $this->uri, $form_fields, $form_files, $referer );
		}

		return $this->response->getStatus();
	}



	public function post( $uri, &$form_data, $follow_redirects = true, $referer = '' ) {
		$this->uri = $uri;

		if ( ($this->keep_alive && !$this->connected) || !$this->keep_alive ) {
			if ( !$this->connect() ) {
				$this->errstr = 'Could not connect to ' . $this->host;
				return -1;
			}
		}
		$http_cookie = $this->response->cookies->get( $this->host, $this->currentDirectory( $uri ) );
		$body = substr( $this->mergeFormData( $form_data ), 1 );
		$this->request->body =  $body . self::CRLF . self::CRLF;
		$content_length = strlen( $body );

		if ($this->use_proxy) {
			$this->request->setHeader( 'Host', $this->host . ':' . $this->port );
			$this->request->setHeader( 'Proxy-Connection', ($this->keep_alive?'Keep-Alive':'Close') );
			if ( $this->proxy_login != '' ) $this->request->setHeader( 'Proxy-Authorization', "Basic " . base64_encode( $this->proxy_login . ":" . $this->proxy_pwd ) );
			$uri = 'http://' . $this->host . ':' . $this->port . $uri;
		} else {
			$this->request->setHeader( 'Host', $this->host );
			$this->request->setHeader( 'Connection', ($this->keep_alive?'Keep-Alive':'Close') );
			$this->request->setHeader( 'Pragma', 'no-cache' );
			$this->request->setHeader( 'Cache-Control', 'no-cache' );
		}

		if ( $this->auth_login != '' ) $this->request->setHeader( 'Authorization', "Basic " . base64_encode( $this->auth_login . ":" . $this->auth_pwd ) );
		$this->request->setHeader( 'Accept', '*/*' );
		$this->request->setHeader( 'Content-Type', 'application/x-www-form-urlencoded' );
		$this->request->setHeader( 'User-Agent', $this->user_agent );
		$this->request->setHeader( 'Content-Length', $content_length );
		$this->request->setHeader( 'Cookie', $http_cookie );
		$this->request->setHeader( 'Referer', $referer );

		$req_header	= "POST $uri HTTP/" . $this->http_version . self::CRLF .
					$this->request->serializeHeaders() .
					self::CRLF;

		fwrite( $this->socket, $req_header );
		usleep( 10 );
		fwrite( $this->socket, $this->request->body );

		$this->readResponse();

		if ($this->socket && !$this->keep_alive) $this->disconnect();
		if ( $this->response->getHeader( 'Connection' ) != null ) {
			if ( $this->keep_alive && strtolower( $this->response->getHeader( 'Connection' ) ) == 'close' ) {
				$this->keep_alive = false;
				$this->disconnect();
			}
		}

		if ( $follow_redirects && ($this->response->getStatus() == HTTP_STATUS_MOVED_PERMANENTLY || $this->response->getStatus() == HTTP_STATUS_FOUND || $this->response->getStatus() == HTTP_STATUS_SEE_OTHER ) ) {
			if ( $this->response->getHeader( 'Location' ) != null ) {
				$this->redirect( $this->response->getHeader( 'Location' ) );
			}
		}

		if ( $this->response->getStatus() == HTTP_STATUS_USE_PROXY ) {
			$location = $this->parseLocation( $this->response->getHeader( 'Location' ) );
			$this->disconnect();
			$this->useProxy( $location['host'], $location['port'] );
			$this->post( $this->uri, $form_data, $referer );
		}

		return $this->response->getStatus();
	}



	public function postXML( $uri, $xml_data, $follow_redirects = true, $referer = '' ) {
		$this->uri = $uri;

		if ( ($this->keep_alive && !$this->connected) || !$this->keep_alive ) {
			if ( !$this->connect() ) {
				$this->errstr = 'Could not connect to ' . $this->host;
				return -1;
			}
		}
		$http_cookie = $this->response->cookies->get( $this->host, $this->currentDirectory( $uri ) );
		$body = $xml_data;
		$this->request->body =  $body . self::CRLF . self::CRLF;
		$content_length = strlen( $body );

		if ($this->use_proxy) {
			$this->request->setHeader( 'Host', $this->host . ':' . $this->port );
			$this->request->setHeader( 'Proxy-Connection', ($this->keep_alive?'Keep-Alive':'Close') );
			if ( $this->proxy_login != '' ) $this->request->setHeader( 'Proxy-Authorization', "Basic " . base64_encode( $this->proxy_login . ":" . $this->proxy_pwd ) );
			$uri = 'http://' . $this->host . ':' . $this->port . $uri;
		} else {
			$this->request->setHeader( 'Host', $this->host );
			$this->request->setHeader( 'Connection', ($this->keep_alive?'Keep-Alive':'Close') );
			$this->request->setHeader( 'Pragma', 'no-cache' );
			$this->request->setHeader( 'Cache-Control', 'no-cache' );
		}

		if ( $this->auth_login != '' ) $this->request->setHeader( 'Authorization', "Basic " . base64_encode( $this->auth_login . ":" . $this->auth_pwd ) );
		$this->request->setHeader( 'Accept', '*/*' );
		$this->request->setHeader( 'Content-Type', 'text/xml; charset=utf-8' );
		$this->request->setHeader( 'User-Agent', $this->user_agent );
		$this->request->setHeader( 'Content-Length', $content_length );
		$this->request->setHeader( 'Cookie', $http_cookie );
		$this->request->setHeader( 'Referer', $referer );

		$req_header	= "POST $uri HTTP/" . $this->http_version . self::CRLF .
					$this->request->serializeHeaders() .
					self::CRLF;

		fwrite( $this->socket, $req_header );
		usleep( 10 );
		fwrite( $this->socket, $this->request->body );

		$this->readResponse();

		if ($this->socket && !$this->keep_alive) $this->disconnect();
		if ( $this->response->getHeader( 'Connection' ) != null ) {
			if ( $this->keep_alive && strtolower( $this->response->getHeader( 'Connection' ) ) == 'close' ) {
				$this->keep_alive = false;
				$this->disconnect();
			}
		}

		if ( $follow_redirects && ($this->response->getStatus() == HTTP_STATUS_MOVED_PERMANENTLY || $this->response->getStatus() == HTTP_STATUS_FOUND || $this->response->getStatus() == HTTP_STATUS_SEE_OTHER ) ) {
			if ( $this->response->getHeader( 'Location' ) != null ) {
				$this->redirect( $this->response->getHeader( 'Location' ) );
			}
		}

		if ( $this->response->getStatus() == HTTP_STATUS_USE_PROXY ) {
			$location = $this->parseLocation( $this->response->getHeader( 'Location' ) );
			$this->disconnect();
			$this->useProxy( $location['host'], $location['port'] );
			$this->post( $this->uri, $form_data, $referer );
		}

		return $this->response->getStatus();
	}


	public function disconnect() {
		if ($this->socket && $this->connected) {
			 fclose($this->socket);
			$this->connected = false;
		 }
	}

	private function connect( ) {
		if ( $this->host == '' ) user_error( 'Class HTTP->_connect() : host property not set !' , E_ERROR );
		if (!$this->use_proxy)
			$this->socket = fsockopen( $this->host, $this->port, $errno, $errstr, 10 );
		else
			$this->socket = fsockopen( $this->proxy_host, $this->proxy_port, $errno, $errstr, 10 );
		$this->errstr  = $errstr;
		$this->connected = ($this->socket == true);
		return $this->connected;
	}


	private function mergeMultipartFormData( $boundary, &$form_fields, &$form_files ) {
		$boundary = '--' . $boundary;
		$multipart_body = '';
		foreach ( $form_fields as $name => $data) {
			$multipart_body .= $boundary . self::CRLF;
			$multipart_body .= 'Content-Disposition: form-data; name="' . $name . '"' . self::CRLF;
			$multipart_body .=  self::CRLF;
			$multipart_body .= $data . self::CRLF;
		}
		if ( isset($form_files) ) {
			foreach ( $form_files as $data) {
				$multipart_body .= $boundary . self::CRLF;
				$multipart_body .= 'Content-Disposition: form-data; name="' . $data['name'] . '"; filename="' . $data['filename'] . '"' . self::CRLF;
				if ($data['content-type']!='')
					$multipart_body .= 'Content-Type: ' . $data['content-type'] . self::CRLF;
				else
					$multipart_body .= 'Content-Type: application/octet-stream' . self::CRLF;
				$multipart_body .=  self::CRLF;
				$multipart_body .= $data['data'] . self::CRLF;
			}
		}
		$multipart_body .= $boundary . '--' . self::CRLF;
		return $multipart_body;
	}


	private function mergeFormData( &$param_array,  $param_name = '' ) {
		$params = '';
		$format = ($param_name !=''?'&'.$param_name.'[%s]=%s':'&%s=%s');
		foreach ( $param_array as $key=>$value ) {
			if ( !is_array( $value ) )
				$params .= sprintf( $format, $key, urlencode( $value ) );
			else
				$params .= $this->mergeFormData( $param_array[$key],  $key );
		}
		return $params;
	}

	private function currentDirectory( $uri ) {
		$tmp = split( '/', $uri );
		array_pop($tmp);
		$current_dir = implode( '/', $tmp ) . '/';
		return ($current_dir!=''?$current_dir:'/');
	}


	private function readResponse( $get_body = true ) {
		$this->response->reset();
		$this->request->reset();
		$header = '';
		$body = '';
		$continue	= true;

		while ($continue) {
			$header = '';

			// Read the Response Headers
			while ( (($line = fgets( $this->socket, 4096 )) != self::CRLF || $header == '') && !feof( $this->socket ) ) {
				if ($line != self::CRLF) $header .= $line;
			}
			$this->response->deserializeHeaders( $header );
			$this->response->parseCookies( $this->host );

			$continue = ($this->response->getStatus() == HTTP_STATUS_CONTINUE);
			if ($continue) fwrite( $this->socket, self::CRLF );
		}

		if ( !$get_body ) return;

		// Read the Response Body
		if ( strtolower( $this->response->getHeader( 'Transfer-Encoding' ) ) != 'chunked' && !$this->keep_alive ) {
			while ( !feof( $this->socket ) ) {
				$body .= fread( $this->socket, 4096 );
			}
		} else {
			if ( $this->response->getHeader( 'Content-Length' ) != null ) {
				$content_length = (integer)$this->response->getHeader( 'Content-Length' );
				$body = fread( $this->socket, $content_length );
			} else {
				if ( $this->response->getHeader( 'Transfer-Encoding' ) != null ) {
					if ( strtolower( $this->response->getHeader( 'Transfer-Encoding' ) ) == 'chunked' ) {
						$chunk_size = (integer)hexdec(fgets( $this->socket, 4096 ) );
						while($chunk_size > 0) {
							$body .= fread( $this->socket, $chunk_size );
							fread( $this->socket, strlen(self::CRLF) );
							$chunk_size = (integer)hexdec(fgets( $this->socket, 4096 ) );
						}
						// TODO : Read trailing http headers
					}
				}
			}
		}
		$this->response->body = $body;
	}


	private function parseLocation( $redirect_uri ) {
		$parsed_url 	= parse_url( $redirect_uri );
		$scheme 		= (isset($parsed_url['scheme'])?$parsed_url['scheme']:'');
		$port			= (isset($parsed_url['port'])?$parsed_url['port']:$this->port);
		$host 			= (isset($parsed_url['host'])?$parsed_url['host']:$this->host);
		$request_file 	= (isset($parsed_url['path'])?$parsed_url['path']:'');
		$query_string 	= (isset($parsed_url['query'])?$parsed_url['query']:'');
		if ( substr( $request_file, 0, 1 ) != '/' )
			$request_file = $this->currentDirectory( $this->uri ) . $request_file;

		return array(	'scheme' => $scheme,
						'port' => $port,
						'host' => $host,
						'request_file' => $request_file,
						'query_string' => $query_string
		);

	}


	private function redirect( $uri ) {
		$location = $this->parseLocation( $uri );
		if ( $location['host'] != $this->host || $location['port'] != $this->port ) {
			$this->host = $location['host'];
			$this->port = $location['port'];
			if ( !$this->use_proxy) $this->disconnect();
		}
		usleep( 100 );
		$this->get( $location['request_file'] . '?' . $location['query_string'] );
	}

}
?>