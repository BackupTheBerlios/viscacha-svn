<?php
/**
 * This is the admin control panel.
 *
 * @package		Airlines
 * @subpackage	Modules
 * @author		Matthias Mohr
 * @since 		1.0
 */
class AdminAirlinesPages extends AdminFieldDataPages {

	public function __construct() {
		parent::__construct(
			array('Airlines.DataFields.Positions.AirlinesCategoryPosition'),
			'airlines/admin/categories',
			array('name', 'code'),
			'Airlines'
		);
		$this->breadcrumb->add('Bewertungen', URI::build('airlines/admin/evals'));
		$this->breadcrumb->add('Kategorien', URI::build('airlines/admin/categories'));
	}

}
?>
