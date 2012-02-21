<?php

class CustomUserData {

	public function hasMultipleFields() {
		return true;
	}
	
	public function getTypeName() {
		return 'Benutzerangaben';
	}
	public function getClassPath() {
		return 'Airlines.DataFields.Types.CustomUserData';
	}

	public function getDbDataType() {
		return null;
	}
	public function getInputCode($data = null) {
		return $this->getDataCode('/Cms/bits/textfield/input', $data);
	}
	public function getOutputCode($data = null) {
		return $this->getDataCode('/Cms/bits/textfield/output', $data);
	}
	public function getValidation() {
		return array(
			Validator::MESSAGE => 'Die angegebenen Daten im Feld "'.$this->getName().'" sind zu kurz/lang (min. 1, max. '.$this->params['max_length'].' Zeichen).',
			Validator::MIN_LENGTH => 1,
			Validator::MAX_LENGTH => $this->params['max_length'],
			Validator::OPTIONAL => $this->params['optional']
		);
	}

	public function getParamNames($add = false) {
		return array('max_length', 'optional');
	}
	public function getParamsCode($add = false) {
		return $this->getCodeImpl('/Cms/bits/textfield/params', compact("add"));
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
