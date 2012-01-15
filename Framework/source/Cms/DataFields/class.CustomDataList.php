<?php
/**
 * Custom data list with iteration support.
 *
 * @package		Cms
 * @subpackage	DataFields
 * @author		Matthias Mohr
 * @since 		1.0
 */

class CustomDataList {

	private $position;
	private $fields;
	private $data;

	public function __construct(CustomDataPosition &$position) {
		$this->position = $position;
		$this->data = array();
		$this->fields = array();
	}

	public function setFields(array $fields) {
		$this->fields = $this->position->getFields($fields);
	}

	public function addData(array $data, $fromDb = true) {
		$obj = new CustomData($this->position);
		$obj->set($data, $fromDb, $this->fields);
		$this->data[] = $obj;
	}

	public function getFields() {
		return $this->fields;
	}

	public function getData() {
		return $this->data;
	}

}
?>