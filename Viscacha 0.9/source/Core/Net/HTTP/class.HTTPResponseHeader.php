<?php
class HTTPResponseHeader extends HTTPHeader {

	private $cookiesHeaders;

	public function __construct() {
		$this->cookiesHeaders = array();
		parent::__construct();
	}

	public function deserializeHeaders($flat_headers) {
		$tmp_headers = Strings::toArray($flat_headers);
		if (preg_match("~HTTP/(\d\.\d)\s+(\d+).*~i", $tmp_headers[0], $matches) > 0) {
			$this->setHeader('Protocol-Version', $matches[1]);
			$this->setHeader('Status', $matches[2]);
		}
		array_shift($tmp_headers);
		foreach($tmp_headers as $value) {
			$pos = strpos($value, ':');
			if ($pos !== false) {
				$key = substr($value, 0, $pos);
				$value = trim(substr($value, $pos+1));
				if (strtolower($key) == 'set-cookie') {
					$this->cookiesHeaders[] = $value;
				}
				else {
					$this->setHeader($key, $value);
				}
			}
		}
	}

	public function reset() {
		$this->cookiesHeaders = array();
		parent::reset();
	}

}
?>