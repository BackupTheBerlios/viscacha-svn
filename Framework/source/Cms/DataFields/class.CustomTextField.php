<?php
/**
 * Simple Text field implementation for custom fields.
 *
 * @package		Cms
 * @author		Matthias Mohr
 * @since 		1.0
 */

class CustomTextField extends CustomDataField {

	protected function getMaxPossibleLength() {
		return 255;
	}
	public function getTypeName() {
		return 'Einzeiliges Textfeld';
	}
	public function getClassPath() {
		return 'Cms.DataFields.CustomTextField';
	}

	public function getDbDataType() {
		return 'VARCHAR('.$this->getMaxPossibleLength().')';
	}
	public function getInputCode() {
		return $this->getCodeImpl('/Cms/bits/textfield/input');
	}
	public function getOutputCode() {
		return $this->getCodeImpl('/Cms/bits/textfield/output');
	}
	public function getValidation() {
		return array(
			Validator::MESSAGE => 'Die angegebenen Daten im Feld "'.$this->getName().'" sind zu lang (max. '.$this->params['max_length'].' Zeichen).',
			Validator::MAX_LENGTH => $this->params['max_length'],
			Validator::OPTIONAL => $this->params['optional']
		);
	}

	public function getParamNames($add = false) {
		return array('max_length', 'optional');
	}
	public function getParamsCode($add = false) {
		return $this->getCodeImpl('/Cms/bits/textfield/params');
	}
	public function getValidationParams($add = false) {
		return array(
			'optional' => array(
				Validator::VAR_TYPE => VAR_INT
			),
			'max_length' => array(
				Validator::MESSAGE => 'Die "Maximale Länge" des Feldes darf nur zwischen 1 und '.$this->getMaxPossibleLength().' liegen.',
				Validator::VAR_TYPE => VAR_INT,
				Validator::MIN_VALUE => 1,
				Validator::MAX_VALUE => $this->getMaxPossibleLength()
			)
		);
	}

}
?>