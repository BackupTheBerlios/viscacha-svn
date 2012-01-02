<?php
/**
 * Simple check box implementation for custom fields.
 *
 * @package		Cms
 * @subpackage	DataFields
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

	public function getDbDataType() {
		return 'BOOLEAN';
	}
	public function getInputCode() {
		return $this->getCodeImpl('/Cms/bits/checkbox/input');
	}
	public function getOutputCode() {
		return $this->getCodeImpl('/Cms/bits/checkbox/output');
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
		return $this->getCodeImpl('/Cms/bits/checkbox/params');
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