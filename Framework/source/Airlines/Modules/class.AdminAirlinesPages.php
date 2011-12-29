<?php
/**
 * This is the admin control panel.
 *
 * @package		Cms
 * @subpackage	Modules
 * @author		Matthias Mohr
 * @since 		1.0
 */
class AdminAirlinesPages extends AdminModuleObject {

	public function __construct() {
		$this->version = '1.0.0';
		$this->module = 'Admin CP: Airlines';
		parent::__construct();
	}

	public function __destruct() {
		parent::__destruct();
	}

	public function main() {
		$this->header();
		echo "TEST";
		$this->footer();
	}

}
?>
