<?php
/**
 * Position for custom profile fields.
 *
 * @package		Cms
 * @subpackage	DataFields
 * @author		Matthias Mohr
 * @since 		1.0
 */

class PersonalProfileFields extends BaseDataPosition {

	public function getDbTable() {
		return 'user';
	}
	public function getName() {
		return 'Persönliche Daten';
	}
	public function getClassPath() {
		return 'Cms.DataFields.Positions.PersonalProfileFields';
	}

}
?>