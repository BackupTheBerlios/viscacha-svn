<?php
Core::loadInterface('Cms.DataFields.Positions.CustomDataPosition');

/**
 * Position for custom profile fields.
 *
 * @package		Cms
 * @subpackage	DataFields
 * @author		Matthias Mohr
 * @since 		1.0
 */

class PersonalProfileFields implements CustomDataPosition {

	public function getDbTable() {
		return 'user';
	}
	public function getPrimaryKey() {
		return 'id';
	}
	public function getName() {
		return 'Persönliche Daten';
	}
	public function getClassPath() {
		return 'Cms.DataFields.Positions.PersonalProfileFields';
	}

}
?>