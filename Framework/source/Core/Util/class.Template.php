<?php
/**
 * PHP Template class.
 *
 * @package		Core
 * @subpackage	Util
 * @author		Matthias Mohr
 * @since 		1.0
 */

class Template {

	private $dir;
	private $ext;
	private $benchmark;
	private $vars;
	private $sent;

	public function __construct($package) {
		$this->setDir('templates/' . $package . '/');
		$this->ext = '.html';
		$this->benchmark = array(
			'all' => 0,
			'ok' => 0,
			'error' => 0,
			'time' => 0,
			'detail' => array()
		);
		$this->vars = array();
		$this->sent = array();
	}

    public function assignMultiple($vars) {
    	$this->vars = array_merge($this->vars, $vars);
    }

    public function assign($key, $value) {
		$this->vars[$key] = $value;
    }

    public function exists($file) {
		if (file_exists($this->dir.$file.$this->ext)) {
			return true;
		}
		else {
			return false;
		}
    }

    public function output($file) {
    	echo $this->parse($file);
    }

	public function parse($__file) {
		$this->benchmark['all']++;
		$__file = $this->dir.$__file.$this->ext;

		if (!file_exists($__file)) {
		    $this->benchmark['error']++;
		    $this->benchmark['detail'][] = array('time' => 0, 'file' => $__file);
		    $debug = Core::getObject('Core.System.Debug');
		    $debug->add('Template not found: '.$__file);
			return false;
		}
		else {
			$debug = Core::getObject('Core.System.Debug');
			$debug->startClock($__file);
			$this->benchmark['ok']++;

			extract($this->vars, EXTR_SKIP);

			ob_start();
			include($__file);
			$__contents = ob_get_contents();
			ob_end_clean();

			$this->vars = array();

			$this->sent[] = $__file;

	    	$__time = $debug->stopClock($__file);
	    	$this->benchmark['time'] += $__time;
	    	$this->benchmark['detail'][] = array('time' => $__time, 'file' => $__file);

	        return $__contents;
		}
	}

	public function setDir($dir) {
		$this->dir = FileSystem::realPath($dir).DIRECTORY_SEPARATOR;
	}

	public function getDir() {
		return $this->dir;
	}

	public function alreadyParsed($file) {
		$file = $this->dir.$file.$this->ext;
		if(in_array($file, $this->sent)) {
			return true;
		}
		else {
			return false;
		}
	}

}
?>
