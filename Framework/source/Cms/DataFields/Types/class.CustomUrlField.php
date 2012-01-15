<?php
/**
 * Simple url/link field implementation for custom fields.
 *
 * @package		Cms
 * @subpackage	DataFields
 * @author		Matthias Mohr
 * @since 		1.0
 */

class CustomUrlField extends CustomTextField {

	public function getTypeName() {
		return 'Link';
	}
	public function getClassPath() {
		return 'Cms.DataFields.Types.CustomUrlField';
	}

	public function getOutputCode($data = null) {
		return $this->getDataCode('/Cms/bits/url/output', $data);
	}
	public function getValidation() {
		return array(
			Validator::MULTIPLE => array(
				array(
					Validator::MESSAGE => 'Die angegebenen Daten im Feld "'.$this->getName().'" sind zu lang (max. '.$this->getMaxPossibleLength().' Zeichen).',
					Validator::MAX_LENGTH => $this->getMaxPossibleLength()
				),
				array(
					Validator::MESSAGE => 'Die Eingabe im Feld "'.$this->getName().'" ist keine gültige URL.',
					Validator::CALLBACK => Validator::CB_URL
				)
			),
			Validator::OPTIONAL => $this->params['optional']
		);
	}

	public function getParamNames($add = false) {
		return array('caption', 'target', 'optional');
	}
	public function getParamsCode($add = false) {
		return $this->getCodeImpl('/Cms/bits/url/params');
	}
	public function getValidationParams($add = false) {
		return array(
			'caption' => array(),
			'target' => array(
				Validator::MESSAGE => 'Das Zielfenster darf nur folgende Zeichen enthalten: a-z, 0-9, _, -',
				Validator::REGEXP => Validator::RE_URI,
				Validator::OPTIONAL => true
			),
			'optional' => array(
				Validator::VAR_TYPE => VAR_INT
			),
		);
	}

}
?>