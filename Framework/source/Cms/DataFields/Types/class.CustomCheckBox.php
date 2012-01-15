<?php
/**
 * Simple check box implementation for custom fields.
 *
 * @package		Cms
 * @subpackage	DataFields
 * @author		Matthias Mohr
 * @since 		1.0
 */

class CustomCheckBox extends CustomField {

	public function getTypeName() {
		return 'Checkbox';
	}
	public function getClassPath() {
		return 'Cms.DataFields.Types.CustomCheckBox';
	}

	public function getDbDataType() {
		return 'BOOLEAN';
	}
	public function getDefaultData() {
		return 0;
	}
	public function getInputCode($data = null) {
		return $this->getDataCode('/Cms/bits/checkbox/input', $data);
	}
	public function getOutputCode($data = null) {
		return $this->getDataCode('/Cms/bits/checkbox/output', $data);
	}
	public function getValidation() {
		return array(
			Validator::MESSAGE => 'Die angegebenen Daten im Feld "'.$this->getName().'" sind ungültig.',
			Validator::VAR_TYPE => VAR_INT,
			Validator::OPTIONAL => true,
			Validator::EQUALS => 1
		);
	}

	public function getParamNames($add = false) {
		return array('yes', 'no');
	}
	public function getParamsCode($add = false) {
		return $this->getCodeImpl('/Cms/bits/checkbox/params', compact("add"));
	}
	public function getValidationParams($add = false) {
		return array(
			'yes' => array(
				Validator::MESSAGE => '"Ausgabe für selektierte Box" ist leer.',
				Validator::MIN_LENGTH => 1
			),
			'no' => array(
				Validator::MESSAGE => '"Ausgabe für nicht selektierte Box" ist leer.',
				Validator::MIN_LENGTH => 1
			)
		);
	}

}
?>