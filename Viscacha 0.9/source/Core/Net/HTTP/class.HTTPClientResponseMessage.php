<?php
class HTTPResponseMessage extends HTTPResponseHeader {

	private $body;
	private $cookies;

	public function __construct() {
		$this->cookies = new HTTPCookie();
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