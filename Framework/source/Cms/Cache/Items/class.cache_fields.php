<?php
/**
 * Caches the custom fields.
 *
 * @package		Core
 * @subpackage	Cache
 * @author		Matthias Mohr
 * @since 		1.0
 */
class cache_fields extends CacheItem implements CacheObject {

	protected $fields;

	public function load() {
		$this->data = array();
		$db = Core::_(DB);
		$db->query("SELECT * FROM <p>fields ORDER BY priority");
		while($row = $db->fetchAssoc()) {
			$this->data[$row['position']][] = $row;
			$this->addField($row);
		}
	}

	public function import() {
		if (parent::import()) {
			foreach ($this->data as $fields) {
				foreach ($fields as $row) {
					$this->addField($row);
				}
			}
			return true;
		}
		return false;
	}

	protected function addField($row) {
		$field = CustomDataField::constructObject($row);
		if ($field != null) {
			$this->fields[$row['position']][$field->getFieldName()] = $field;
		}
	}

	public function getData($pos) {
		$this->get();
		if (isset($this->data[$pos])){
			return $this->data[$pos];
		}
		else {
			return array();
		}
	}

	public function getFields($pos) {
		$this->get();
		if (isset($this->fields[$pos])){
			return $this->fields[$pos];
		}
		else {
			return array();
		}
	}

}
?>