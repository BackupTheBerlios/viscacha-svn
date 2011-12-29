<?php
Core::loadInterface('Cms.DataFields.Positions.CustomDataPosition');

/**
 * Position for custom profile fields.
 *
 * @package		Cms
 * @author		Matthias Mohr
 * @since 		1.0
 */

class AirlinesFlightPosition implements CustomDataPosition {

	public function getDbTable() {
		return 'evaluations';
	}
	public function getName() {
		return 'Bewertungen';
	}
	public function getClassPath() {
		return 'Airlines.DataFields.Positions.AirlinesFlightPosition';
	}

}
?>