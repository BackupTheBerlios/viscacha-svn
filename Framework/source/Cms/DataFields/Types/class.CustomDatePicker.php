<?php
/**
 * Simple date picker implementation for custom fields.
 *
 * @package		Cms
 * @subpackage	DataFields
 * @author		Matthias Mohr
 * @since 		1.0
 */

class CustomDatePicker extends CustomField {

	public function getTypeName() {
		return 'Datum';
	}
	public function getClassPath() {
		return 'Cms.DataFields.Types.CustomDatePicker';
	}
	public function getDbDataType() {
		return 'DATE';
	}
	public function getInputCode($data = null) {
		return $this->getDataCode('/Cms/bits/date/input', $data);
	}
	public function getOutputCode($data = null) {
		return $this->getDataCode('/Cms/bits/date/output', $data);
	}
	public function getValidation() {
		return array(
			Validator::MESSAGE => 'Das angegebene Datum im Feld "'.$this->getName().'" ist ungültig.',
			Validator::CALLBACK => Validator::CB_DATE
		);
	}
	public function formatDataForDb($data) {
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
	public function formatDataFromDb($data) {
		if (strlen($data) == 10 && preg_match('/^\d{4}-\d{2}-\d{2}$/', $data) > 0) {
			// Data is probably coming from database
			$dt = DT::createFromFormat('Y-m-d', $data);
			if ($dt != null) {
				$data = $dt->date();
			}
		}
		return $data;
	}

}
?>