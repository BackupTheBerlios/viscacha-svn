<?php
/**
 * A calculated field. This should not be added to field management.
 * 
 * This is a base class for calculated values from database.
 * Should be only used programatically.
 *
 * @package		Cms
 * @subpackage	DataFields
 * @author		Matthias Mohr
 * @since 		1.0
 */

class UnknownDataField extends CustomField {

	public function getTypeName() {
		return 'Dynamische Daten';
	}
	public function getClassPath() {
		return 'Cms.DataFields.Types.CalculatedField';
	}
	public function getDbDataType() {
		return null;
	}
	public function getInputCode($data = null) {
		return ''; // It's calculated => no input
	}
	public function getOutputCode($data = null) {
		return $this->getDataCode('/Cms/bits/textfield/output', $data);
	}

}
?>