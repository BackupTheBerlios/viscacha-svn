<?php
/**
 * PHP Template class.
 *
 * @package		Core
 * @subpackage	Util
 * @author		Matthias Mohr
 * @since 		1.0
 */

class TemplateBit {

	private $file;
	private $time;
	private $vars;

	public function __construct($file) {
		$this->file = $file;
		$this->time = 0;
		$this->vars = array();
	}

    public function assignMultiple(array $vars) {
    	$this->vars = array_merge($this->vars, $vars);
    }

    public function assign($key, $value, $sanitize = true) {
		if ($sanitize) {
			$value = Sanitize::saveHTML($value);
		}
		$this->vars[$key] = $value;
    }

    public function output() {
    	echo $this->parse();
    }

	public function parse() {
		$__debug = Core::getObject('Core.System.Debug');
		$__debug->startClock($this->file);

		extract($this->vars, EXTR_SKIP);

		ob_start();
		include($this->file);
		$contents = ob_get_contents();
		ob_end_clean();

		$this->time = $__debug->stopClock($this->file);

		return $contents;
	}

	public function getTime() {
		return $this->time;
	}

}
?>