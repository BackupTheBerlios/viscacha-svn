<?php
/**
 * Response class
 *
 * @package		Core
 * @subpackage	System
 * @author		Matthias Mohr
 * @since 		1.0
 */
final class Response extends HTTPHeader {

	private $tpl;

	private static $instance = null;

	public static function getObject() {
		if (self::$instance === null) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	private function __clone() {}

	protected function __construct() {
		parent::__construct(Config::get('http.gzip'));
		$this->tpl = new Template();
	}

	public function getTemplate($path) {
		return $this->tpl->load($path);
	}

	public function appendTemplate($path, $after = null) {
		return $this->tpl->append($path, $after);
	}

	public function prependTemplate($path, $before = null) {
		return $this->tpl->prepend($path, $before);
	}

}
?>