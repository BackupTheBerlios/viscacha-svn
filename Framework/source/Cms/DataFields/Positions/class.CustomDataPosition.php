<?php
/**
 * Class for custom data field data storage and view positions.
 *
 * @package		Cms
 * @subpackage	DataFields
 * @author		Matthias Mohr
 * @since 		1.0
 */

abstract class CustomDataPosition {

	private $fields;

	public abstract function getDbTable(); // Database table name for the data, without prefix
	public abstract function getPrimaryKey(); // Primary key column name of the specified db table
	public abstract function getName();
	public abstract function getClassPath(); // Example: Cms.DataFields.CustomDataPosition

	public function __construct() {}

	protected function loadFields() {
		if (!is_array($this->fields)) {
			$cache = Core::getObject('Core.Cache.CacheServer')->load('fields');
			$this->fields = $cache->getFields($this->getClassPath());
		}
	}

	public function getField($internal) {
		$this->loadFields();
		if (isset($this->fields[$internal])) {
			return $this->fields[$internal];
		}
		return null;
	}

	public function getFields($internal = null) {
		$this->loadFields();
		if (is_array($internal)) {
			return array_intersect_key($this->fields, array_fill_keys($internal, null));
		}
		return $this->fields;
	}

	public function getFieldsExcept($internal) {
		$this->loadFields();
		if (is_array($internal)) {
			return array_diff_key($this->fields, array_fill_keys($internal, null));
		}
		return $this->fields;
	}

}
?>
