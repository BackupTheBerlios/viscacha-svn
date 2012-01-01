<?php
/**
 * Simple Text field implementation for custom fields.
 *
 * @package		Cms
 * @author		Matthias Mohr
 * @since 		1.0
 */

class CustomImageView extends CustomTextField {

	public function getTypeName() {
		return 'Bild-Ansicht';
	}
	public function getClassPath() {
		return 'Cms.DataFields.CustomImageView';
	}

	public function getOutputCode() {
		return $this->getCodeImpl('bits/image/output');
	}
	public function getValidation() {
		return array(
			Validator::MESSAGE => 'Die angegebenen Daten im Feld "'.$this->getName().'" sind zu lang (max. 255 Zeichen).',
			Validator::MAX_LENGTH => 255,
			Validator::OPTIONAL => $this->params['optional']
		);
	}

	public function getParamNames($add = false) {
		return array('alt', 'optional');
	}
	public function getParamsCode($add = false) {
		return $this->getCodeImpl('bits/image/params');
	}
	public function getValidationParams($add = false) {
		return array(
			'optional' => array(
				Validator::VAR_TYPE => VAR_INT
			),
			'alt' => array(
				Validator::VAR_TYPE => VAR_HTML
			)
		);
	}

}
?>