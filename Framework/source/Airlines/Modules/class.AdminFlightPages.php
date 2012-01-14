<?php
/**
 * This is the admin control panel.
 *
 * @package		Airlines
 * @subpackage	Modules
 * @author		Matthias Mohr
 * @since 		1.0
 */
class AdminFlightPages extends AdminFieldDataPages {

	public function __construct() {
		parent::__construct(
			'Airlines.DataFields.Positions.AirlinesFlightPosition',
			'airlines/admin/evals',
			array('title', 'published'),
			'Airlines'
		);
		$this->breadcrumb->add('Bewertungen', URI::build('airlines/admin/evals'));
	}

}
?>
