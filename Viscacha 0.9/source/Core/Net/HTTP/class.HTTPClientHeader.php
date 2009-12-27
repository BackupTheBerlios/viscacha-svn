<?php
abstract class AbstractHTTPHeader {

	private $headers;

	public function __construct() {
		$this->headers = array();
	}

	public function getHeader($header_name) {
		$header_name = $this->formatHeaderName($header_name);
		if (isset($this->headers[$header_name])) {
			return $this->headers[$header_name];
		}
		else {
			return null;
		}
	}

	public function setHeader($header_name, $value) {
		if ($value != '') {
			$header_name = $this->formatHeaderName($header_name);
			$this->headers[$header_name] = $value;
		}
	}

	public function reset() {
		$this->headers = array();
	}

	public function serializeHeaders() {
		$str = '';
		foreach ( $this->headers as $name=>$value) {
			$str .= "{$name}: {$value}" . HTTPClient::CRLF;
		}
		return $str;
	}

	private function formatHeaderName($header_name) {
		$formatted = str_replace('-', ' ', strtolower($header_name));
		$formatted = ucwords( $formatted );
		$formatted = str_replace(' ', '-', $formatted);
		return $formatted;
	}

}
?>