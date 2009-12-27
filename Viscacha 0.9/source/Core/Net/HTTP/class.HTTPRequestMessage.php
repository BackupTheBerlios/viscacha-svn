<?php
class HTTPRequestMessage extends HTTPHeader {

	private $body;

	public function __construct() {
		$this->body = '';
		parent::__construct();
	}

	public function reset() {
		$this->body = '';
		parent::reset();
	}
}
?>