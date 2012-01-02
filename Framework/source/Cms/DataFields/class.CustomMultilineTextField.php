<?php
/**
 * Simple multiline text field implementation for custom fields.
 *
 * @package		Cms
 * @subpackage	DataFields
 * @author		Matthias Mohr
 * @since 		1.0
 */

class CustomMultilineTextField extends CustomTextField {

	protected function getMaxPossibleLength() {
		return 16777215;
	}
	public function getTypeName() {
		return 'Text, mehrzeilig';
	}
	public function getClassPath() {
		return 'Cms.DataFields.CustomMultilineTextField';
	}
	public function getDbDataType() {
		return 'MEDIUMTEXT';
	}
	public function getInputCode() {
		return $this->getCodeImpl('/Cms/bits/textfield/ml_input');
	}

}
?>