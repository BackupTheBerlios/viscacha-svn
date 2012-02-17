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

	public function addData(CustomData $fieldData) {
		$this->data[] = $fieldData;
	}

	public function getFields() {
		return $this->fields;
	}
	
	public function hasData() {
		return count($this->data) > 0;
	}

	public function getData() {
		return $this->data;
	}

}
?>