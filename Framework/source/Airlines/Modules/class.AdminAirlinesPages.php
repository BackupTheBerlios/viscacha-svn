<?php
/**
 * This is the admin control panel.
 *
 * @package		Airlines
 * @subpackage	Modules
 * @author		Matthias Mohr
 * @since 		1.0
 */
class AdminAirlinesPages extends AdminModuleObject {

	public function __construct() {
		$this->version = '1.0.0';
		$this->module = 'Admin CP: Airlines';
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

	public function categories() {
		$this->header();
		$db = Core::_(DB);
		$db->query("SELECT * FROM <p>categories ORDER BY name");
		$this->tpl->assign("data", $db->fetchAll());
		$this->tpl->output("admin/categories");
		$this->footer();
	}

}
?>
