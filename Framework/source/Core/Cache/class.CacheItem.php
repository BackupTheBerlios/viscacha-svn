<?php
Core::loadInterface('Core.Cache.CacheObject');
Core::loadClass('Core.FileSystem.File');

/**
 * Represents one CacheItem.
 *
 * @package		Core
 * @subpackage	Cache
 * @author		Matthias Mohr
 * @since 		1.0
 */
abstract class CacheItem implements CacheObject {

	protected $name;
	protected $file;
	protected $data;
	protected $max_age;

	public function __construct($filename, $cachedir = "data/cache/") {
		$this->name = $filename;
		$this->file = new File($cachedir.$this->name.'.cache');
		$this->data = null;
		$this->max_age = null;
	}

	public function export() {
		return $this->file->writeArray($this->data, true);
	}

	public function import() {
		if ($this->file->exists() == true) {
	        $this->data = $this->file->readArray();
	        return true;
	    }
	    else {
	        return false;
	    }
	}

	public function expired($max_age) {
		if ($max_age == null) {
			$max_age = $this->max_age;
		}
		if ($this->age() >= $max_age) {
			$this->delete();
			return true;
		}
		else {
			return false;
		}
	}

	public function age() {
		if ($this->file->exists() == true) {
			$age = time() - $this->file->time();
			return $age;
		}
		else {
			return -1;
		}
	}

	public function exists($max_age = null) {
		if ($max_age == null) {
			$max_age = $this->max_age;
		}
	    if ($this->file->exists() == true && $this->file->size() > 0) {
			if ($max_age != null) {
				return !($this->expired($max_age));
			}
	        return true;
	    }
	    else {
	        return false;
	    }
	}

	public function delete() {
	    if ($this->file->exists() == true) {
	    	return $this->file->delete();
	    }
	    else {
	    	return false;
	    }
	}

	public function rebuildable() {
		return true;
	}

	public function get($max_age = null) {
		if ($max_age == null) {
			$max_age = $this->max_age;
		}
		if ($this->data == null || ($max_age != null && $this->expired($max_age))) {
			if ($this->exists() == true) {
				$this->import();
			}
			else {
				$this->load();
				$this->export();
			}
		}
		return $this->data;
	}

	public function set($data) {
		$this->data = $data;
	}
}
?>