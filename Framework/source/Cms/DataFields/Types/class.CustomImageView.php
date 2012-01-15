<?php
/**
 * Simple image view implementation for custom fields.
 *
 * @package		Cms
 * @subpackage	DataFields
 * @author		Matthias Mohr
 * @since 		1.0
 */

class CustomImageView extends CustomTextField {

	public function getTypeName() {
		return 'Bild';
	}
	public function getClassPath() {
		return 'Cms.DataFields.Types.CustomImageView';
	}

	public function getOutputCode($data = null) {
		return $this->getDataCode('/Cms/bits/image/output', $data);
	}
	public function getValidation() {
		return array(
			Validator::MESSAGE => 'Die angegebenen Daten im Feld "'.$this->getName().'" sind zu kurz/lang (min. 1, max. '.$this->getMaxPossibleLength().' Zeichen).',
			Validator::MIN_LENGTH => 1,
			Validator::MAX_LENGTH => $this->getMaxPossibleLength(),
			Validator::OPTIONAL => $this->params['optional']
		);
	}

	public function getParamNames($add = false) {
		return array('alt', 'optional');
	}
	public function getParamsCode($add = false) {
		return $this->getCodeImpl('/Cms/bits/image/params', compact("add"));
	}
	public function getValidationParams($add = false) {
		return array(
			'optional' => array(
				Validator::VAR_TYPE => VAR_INT
			),
			'alt' => array()
		);
	}

}
?>