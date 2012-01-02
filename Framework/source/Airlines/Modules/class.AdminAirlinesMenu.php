<?php
/**
 * Menu for airlines
 *
 * @package		Airlines
 * @subpackage	Modules
 * @author		Matthias Mohr
 * @since 		1.0
 */
class AdminAirlinesMenu extends AdminMenuObject {
	public function getHeader($class) {
		return 'Bewertungen';
	}

	public function getMenu($class) {
		return array(
			URI::build('airlines/admin/evals') => '�bersicht',
			URI::build('airlines/admin/efields') => 'Felder',
			URI::build('airlines/admin/airports') => 'Flugh�fen',
			'Kategorien' => array(
				URI::build('airlines/admin/categories') => '�bersicht',
				URI::build('airlines/admin/cfields') => 'Felder'
			)
		);
	}

}
?>