<?php
/**
 * Simple date picker implementation for custom fields.
 *
 * @package		Cms
 * @subpackage	DataFields
 * @author		Matthias Mohr
 * @since 		1.0
 */

class CustomDatePicker extends CustomDataField {

	public function getTypeName() {
		return 'Datum';
	}
	public function getClassPath() {
		return 'Cms.DataFields.CustomDatePicker';
	}
	public function getDbDataType() {
		return 'DATE';
	}
	public function getInputCode() {
		return $this->getCodeImpl('/Cms/bits/date/input');
	}
	public function getOutputCode() {
		return $this->getCodeImpl('/Cms/bits/date/output');
	}
	public function getValidation() {
		return array(
			Validator::MESSAGE => 'Das angegebene Datum im Feld "'.$this->getName().'" ist ungültig.',
			Validator::CALLBACK => Validator::CB_DATE
		);
	}
	public function getDataForDb() {
		$data = $this->data;
		if ($data !== null) {
			$dt = DT::createFromFormat(Config::get('intl.date_format'), $data);
			if ($dt !== null) {
				$data = $dt->dbDate();
			}
			else {
				Core::throwError('Could not convert DateTime-data for database.');
			}
		}
		return $data;
	}
	public function setData($data) {
		if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $data) > 0) {
			// Data is probably coming from database
			$dt = DT::createFromFormat('Y-m-d', $data);
			if ($dt != null) {
				$data = $dt->date();
			}
		}
		$this->data = $data;
	}

}
?>