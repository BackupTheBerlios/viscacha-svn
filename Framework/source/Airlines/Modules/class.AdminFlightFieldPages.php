<?php
/**
 * This is the admin control panel.
 *
 * @package		Airlines
 * @subpackage	Modules
 * @author		Matthias Mohr
 * @since 		1.0
 */
class AdminFlightFieldPages extends AdminFieldPages {

	public function __construct() {
		$this->version = '1.0.0';
		$this->module = 'Admin CP: Flight-Fields';
		parent::__construct();
		$this->breadcrumb->add('Bewertungen', URI::build('airlines/admin/flights/evals'));
		$this->breadcrumb->add('Felder', URI::build('airlines/admin/efields'));
	}

	public function __destruct() {
		parent::__destruct();
	}

	protected function getPositions() {
		return array('Airlines.DataFields.Positions.AirlinesFlightPosition');
	}

	protected function getBaseURI() {
		return 'airlines/admin/efields';
	}

}
?>