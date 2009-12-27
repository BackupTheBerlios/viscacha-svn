<?php
// Todo: Make compliant to RFC (see old HTTP? classes, ReadOnly etc.)
class HTTPCookie {

	private $cookies;

	public function __construct() {
		$this->cookies 	= array();
	}

	private function now() {
		return strtotime(gmdate("l, d-F-Y H:i:s", time()));
	}

	private function timestamp($date) {
		if ($date == '') {
			return $this->now() + 3600;
		}
		$time = strtotime($date);
		return ($time>0?$time:$this->now()+3600);
	}

	public function get($currentDomain, $currentPath) {
		$cookieStr = '';
		$now = $this->now();
		$newCookies = array();

		foreach($this->cookies as $cookieName => $cookieData) {
			if ($cookieData['expires'] > $now) {
				$newCookies[$cookieName] = $cookieData;

				$domain = preg_quote($cookieData['domain'], '~');
				$domainMatch = (bool) preg_match("~.*{$domain}$~i", $currentDomain);

				$path = preg_quote($cookieData['path'], '~');
				$pathMatch = (bool) preg_match("~^{$path}.*~i", $currentPath);

				if ($domainMatch == true && $pathMatch == true) {
					$cookieStr .= $cookieName.'='.$cookieData['value'].'; ';
				}
			}
		}

		$this->cookies = $newCookies;
		return $cookieStr;
	}

	public function set($name, $value, $domain, $path, $expires) {
		$this->cookies[$name] = array(
			'value' => $value,
			'domain' => $domain,
			'path' => $path,
			'expires' => $this->timestamp($expires)
		);
	}

	public function parse($cookie_str, $host) {
		$cookie_str = str_replace('; ', ';', $cookie_str).';';
		$data = explode(';', $cookie_str);
		$value_str = $data[0];

		$cookie_param = 'domain=';
		$start = strpos( $cookie_str, $cookie_param );
		if ($start > 0) {
			$domain = substr($cookie_str, $start + strlen($cookie_param));
			$domain = substr($domain, 0, strpos($domain, ';'));
		}
		else {
			$domain = $host;
		}

		$cookie_param = 'expires=';
		$start = strpos($cookie_str, $cookie_param);
		if ($start > 0) {
			$expires = substr($cookie_str, $start + strlen($cookie_param));
			$expires = substr($expires, 0, strpos($expires, ';'));
		}
		else {
			$expires = '';
		}

		$cookie_param = 'path=';
		$start = strpos($cookie_str, $cookie_param);
		if ($start > 0) {
			$path = substr($cookie_str, $start + strlen($cookie_param ));
			$path = substr($path, 0, strpos($path, ';'));
		}
		else {
			$path = '/';
		}

		$sep_pos = strpos($value_str, '=');
		if ($sep_pos !== false){
			$name = substr($value_str, 0, $sep_pos);
			$value = substr($value_str, $sep_pos+1);
			$this->set($name, $value, $domain, $path, $expires);
		}
	}

}
?>