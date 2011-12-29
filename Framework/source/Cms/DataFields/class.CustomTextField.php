<?php
/**
 * Simple Text field implementation for custom fields.
 *
 * @package		Cms
 * @author		Matthias Mohr
 * @since 		1.0
 */

class CustomTextField extends CustomDataField {

	public function getTypeName() {
		return 'Einzeiliges Textfeld';
	}
	public function getClassPath() {
		return 'Cms.DataFields.CustomTextField';
	}

	public function getDataType() {
		return VAR_HTML;
	}
	public function getDbDataType() {
		return 'VARCHAR(255)';
	}
	public function getInputCode() {
		return $this->getCodeImpl('bits/textfield_input');
	}
	public function getOutputCode() {
		return $this->getCodeImpl('bits/textfield_output');
	}
	public function validate() {
		return array(
			Validator::MESSAGE => 'Die angegebenen Daten im Feld "'.$this->getName().'" sind zu lang (max. 255 Zeichen).',
			Validator::MAX_LENGTH => $this->params['max_length'],
			Validator::OPTIONAL => $this->params['optional']
		);
	}

	public function getParamNames() {
		return array('max_length', 'optional');
	}
	public function getParamsCode() {
		return $this->getCodeImpl('bits/textfield_params');
	}
	public function validateParams() {
		return array(
			'optional' => array(
				Validator::VAR_TYPE => VAR_INT
			),
			'max_length' => array(
				Validator::MESSAGE => 'Die maximale Länge des Feldes darf nur zwischen 1 und 255 liegen.',
				Validator::VAR_TYPE => VAR_INT,
				Validator::MIN_VALUE => 1,
				Validator::MAX_VALUE => 255
			)
		);
	}

}
?>