<?php
/**
 * Simple select box implementation for custom fields.
 *
 * @package		Cms
 * @subpackage	DataFields
 * @author		Matthias Mohr
 * @since 		1.0
 */

class CustomSelectBox extends CustomDataField {

	const MAX_KEY_LENGTH = 50;

	public function getTypeName() {
		return 'Auswahlliste';
	}
	public function getClassPath() {
		return 'Cms.DataFields.CustomSelectBox';
	}
	public function getDbDataType() {
		return 'VARCHAR('.self::MAX_KEY_LENGTH.')';
	}
	public function getInputCode() {
		Core::_(TPL)->assign('options', Sanitize::saveHTML(self::getOptionList($this->params['options'])));
		return $this->getCodeImpl('/Cms/bits/selectbox/input');
	}
	public function getOutputCode() {
		$options = self::getOptionList($this->params['options']);
		$data = $this->getData();
		$value = '';
		if (isset($options[$data]) && $options[$data] !== null) {
			$value = $options[$data];
		}

		$tpl = Core::_(TPL);
		$tpl->assign('value', Sanitize::saveHTML($value));
		$tpl->assign('options', Sanitize::saveHTML($options));
		return $this->getCodeImpl('/Cms/bits/checkbox/output');
	}

	public function getValidation() {
		return array(
			Validator::MESSAGE => 'Die angegebenen Daten im Feld "'.$this->getName().'" sind ungültig.',
			Validator::VAR_TYPE => VAR_INT,
			Validator::LIST_CS => array_keys(self::getOptionList($this->params['options']))
		);
	}

	public function getParamNames($add = false) {
		return array('options');
	}
	public function getParamsCode($add = false) {
		return $this->getCodeImpl('/Cms/bits/selectbox/params');
	}
	public function getValidationParams($add = false) {
		return array(
			'options' => array(
				Validator::MESSAGE => 'Die Optionen sind ungültig, bitte Einschränkungen beachten.',
				Validator::CALLBACK => 'CustomSelectBox::validateOptionParam'
			)
		);
	}

	public static function validateOptionParam($value) {
		$list = self::getOptionList($value);
		if (count($list) < 2) {
			return false;
		}
		foreach ($list as $key => $value) {
			if (strlen($key) > MAX_KEY_LENGTH) {
				return false;
			}
		}
		return true;
	}

	protected static function getOptionList($value) {
		$lines = preg_split('/(\r\n|\n|\r)+/', $value);
		$options = array();
		foreach ($lines as $line) {
			if (!empty($line)) {
				list($key, $value) = explode('=', $line, 2);
				if (empty($value)) {
					$value = $key;
				}
				$options[$key] = $value;
			}
		}
		return $options;
	}

}
?>