<?php
/**
 * Simple text implementation for custom fields.
 *
 * @package		Cms
 * @subpackage	DataFields
 * @author		Matthias Mohr
 * @since 		1.0
 */

class CustomText extends CustomField {

	public function getTypeName() {
		return 'Beschreibung';
	}
	public function getClassPath() {
		return 'Cms.DataFields.CustomText';
	}
	public function getDbDataType() {
		return null;
	}
	public function getInputCode($data = null) {
		return $this->getDataCode('/Cms/bits/text/plain', $data);
	}
	public function getOutputCode($data = null) {
		return $this->getInputCode($data);
	}
	public function noLabel() {
		return true;
	}

}
?>