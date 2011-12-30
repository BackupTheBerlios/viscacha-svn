<?php
/**
 * This is the admin control panel.
 *
 * @package		Airlines
 * @subpackage	Modules
 * @author		Matthias Mohr
 * @since 		1.0
 */
class AdminFlightPages extends AdminModuleObject {

	public function __construct() {
		$this->version = '1.0.0';
		$this->module = 'Admin CP: Flights';
		parent::__construct('Airlines');
	}

	public function __destruct() {
		parent::__destruct();
	}

	public function main() {
		$this->header();
		echo "Not implemented yet, sorry! :-(";
		$this->footer();
	}

}
?>
