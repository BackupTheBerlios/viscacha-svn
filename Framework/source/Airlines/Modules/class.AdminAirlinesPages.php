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
		$this->version = '1.0.0';
		$this->module = 'Admin CP: Airlines';
		parent::__construct(
			array('Airlines.DataFields.Positions.AirlinesCategoryPosition'),
			'airlines/admin/categories',
			'Airlines'
		);
		$this->breadcrumb->add('Bewertungen', URI::build('airlines/admin/flights/evals'));
		$this->breadcrumb->add('Kategorien', URI::build('airlines/admin/categories'));
	}

}
?>
