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

	const BASEDIR = 'templates/';

	private $dir;
	private $ext;
	private $list;

	public function __construct() {
		$this->setDir(self::BASEDIR);
		$this->ext = '.html';
		$this->list = array();
	}

	public function load($file) {
		$file = $this->getFilePath($file);
		if (file_exists($file)) {
			return new TemplateBit($file);
		}
		else {
			Core::throwError("Template not found: '{$file}'");
			return null;
		}
	}

	public function prepend($file, $before = null) {
		$tpl = $this->load($file);
		if ($tpl !== null) {

		}
		return $tpl;
	}

	public function append($file, $after = null) {
		$tpl = $this->load($file);
		if ($tpl !== null) {

		}
		return $tpl;
	}

	public function setDir($dir) {
		$this->dir = FileSystem::realPath($dir).DIRECTORY_SEPARATOR;
	}

	public function getDir() {
		return $this->dir;
	}

	protected function getFilePath($file) {
		if (strpos($file, '/') === 0) {
			return FileSystem::realPath(self::BASEDIR).$file.$this->ext;
		}
		else {
			return $this->dir.$file.$this->ext;
		}
	}

}
?>
