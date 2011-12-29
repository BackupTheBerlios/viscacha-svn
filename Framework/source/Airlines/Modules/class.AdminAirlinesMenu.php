<?php
/**
 * Menu for airlines
 *
 * @package		Cms
 * @subpackage	Modules
 * @author		Matthias Mohr
 * @since 		1.0
 */
class AdminAirlinesMenu extends AdminMenuObject {
	public function getHeader($class) {
		return 'Flug-Bewertungen';
	}

	public function getMenu($class) {
		return array(
			URI::build('airlines/admin/default/') => 'Übersicht',
			'Kategorien' => array(
				URI::build('airlines/admin/categories/') => 'Übersicht',
				URI::build('airlines/admin/cfields/') => 'Felder'
			)
		);
	}

}
?>