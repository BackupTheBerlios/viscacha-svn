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
			URI::build('airlines/admin/default/') => '�bersicht',
			'Kategorien' => array(
				URI::build('airlines/admin/categories/') => '�bersicht',
				URI::build('airlines/admin/cfields/') => 'Felder'
			)
		);
	}

}
?>