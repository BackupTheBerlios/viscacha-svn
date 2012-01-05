<?php
/**
 * Simple text implementation for custom fields.
 *
 * @package		Cms
 * @subpackage	DataFields
 * @author		Matthias Mohr
 * @since 		1.0
 */

class CustomText extends CustomDataField {

	public function getTypeName() {
		return 'Beschreibung';
	}
	public function getClassPath() {
		return 'Cms.DataFields.CustomText';
	}
	public function getDbDataType() {
		return null;
	}
	public function getInputCode() {
		return $this->getCodeImpl('/Cms/bits/text/plain');
	}
	public function getOutputCode() {
		return $this->getOutputCode();
	}
	public function noLabel() {
		return true;
	}

}
?>