<?php
Core::loadInterface('Cms.DataFields.Positions.CustomDataPosition');

/**
 * Position for custom profile fields.
 *
 * @package		Airlines
 * @subpackage	DataFields
 * @author		Matthias Mohr
 * @since 		1.0
 */

class AirlinesCategoryPosition implements CustomDataPosition {

	public function getDbTable() {
		return 'categories';
	}
	public function getName() {
		return 'Kategorien';
	}
	public function getClassPath() {
		return 'Airlines.DataFields.Positions.AirlinesCategoryPosition';
	}

}
?>