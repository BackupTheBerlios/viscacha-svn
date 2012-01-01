<?php
/**
 * Simple Text field implementation for custom fields.
 *
 * @package		Cms
 * @author		Matthias Mohr
 * @since 		1.0
 */

class CustomCheckBox extends CustomDataField {

	public function getTypeName() {
		return 'Checkbox';
	}
	public function getClassPath() {
		return 'Cms.DataFields.CustomCheckBox';
	}

	public function getDataType() {
		return VAR_INT;
	}
	public function getDbDataType() {
		return 'BOOLEAN';
	}
	public function getInputCode() {
		return $this->getCodeImpl('bits/checkbox/input');
	}
	public function getOutputCode() {
		return $this->getCodeImpl('bits/checkbox/output');
	}
	public function getValidation() {
		return array(
			Validator::MESSAGE => 'Die angegebenen Daten im Feld "'.$this->getName().'" sind ungültig.',
			Validator::VAR_TYPE => VAR_INT,
			Validator::OPTIONAL => true,
			Validator::EQUALS => 1
		);
	}
}
?>