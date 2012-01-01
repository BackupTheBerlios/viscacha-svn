<?php
/**
 * Simple Text field implementation for custom fields.
 *
 * @package		Cms
 * @author		Matthias Mohr
 * @since 		1.0
 */

class CustomMultilineTextField extends CustomTextField {

	protected function getMaxPossibleLength() {
		return 16777215;
	}
	public function getTypeName() {
		return 'Mehrzeiliges Textfeld';
	}
	public function getClassPath() {
		return 'Cms.DataFields.CustomMultilineTextField';
	}
	public function getDbDataType() {
		return 'MEDIUMTEXT';
	}
	public function getInputCode() {
		return $this->getCodeImpl('bits/textfield/ml_input');
	}

}
?>