<?php
/**
 * This is the admin control panel.
 *
 * @package		Cms
 * @subpackage	Modules
 * @author		Matthias Mohr
 * @since 		1.0
 */
class AdminAirlinesFieldPages extends AdminFieldPages {

	public function __construct() {
		$this->version = '1.0.0';
		$this->module = 'Admin CP: Airline-Fields';
		parent::__construct();
		$this->breadcrumb->add('Flug-Bewertungen');
		$this->breadcrumb->add('Kategorien-Felder', URI::build('airlines/admin/cfields'));
	}

	public function __destruct() {
		parent::__destruct();
	}

	protected function getPositions() {
		return array('Airlines.DataFields.Positions.AirlinesCategoryPosition');
	}
	protected function getFields() {
		return array('Cms.DataFields.CustomTextField');
	}

}
?>