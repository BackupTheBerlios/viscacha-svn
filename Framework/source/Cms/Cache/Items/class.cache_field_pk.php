<?php
/**
 * Caches the custom fields.
 *
 * @package		Core
 * @subpackage	Cache
 * @author		Matthias Mohr
 * @since 		1.0
 */
class cache_field_pk extends CacheItem {

	public function __construct($filename, $cachedir = "data/cache/") {
		parent::__construct($filename, $cachedir);
	}

	public function rebuildable() {
		return false;
	}
	
	public function load() {
		$this->data = array();
	}
	
	/**
	 * Tries to resolve the PK of a table.
	 */
	public function getPk($table) {
		$this->get();
		if (!empty($this->data[$table])) {
			return $this->data[$table];
		}
		else {
			$pk = null;
			$db = Database::getObject();

			// 1. Try to get PK info from column information
			$db->query('SHOW COLUMNS FROM <p><table:noquote> WHERE `Key` = "PRI" ', compact("table"));
			if ($db->numRows() > 0) {
				$pk = $db->fetchOne();
			}

			// 2. Try to get auto_increment info from column information
			if ($pk == null) {
				$db->query('SHOW COLUMNS FROM <p><table:noquote> WHERE `Extra` = "auto_increment"', compact("table"));
				if ($db->numRows() > 0) {
					$pk = $db->fetchOne();
				}
			}

			// 3. Use the first column as PK (important e.q. for views)
			if ($pk == null) {
				$db->query('SHOW COLUMNS FROM <p><table:noquote>', compact("table"));
				if ($db->numRows() > 0) {
					$pk = $db->fetchOne();
				}
			}
			
			// Save and return
			$this->data[$table] = $pk;
			$this->export();
			return $pk;
		}
	
	}

}
?>