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
		$this->version = '1.0.0';
		$this->module = 'Admin CP: Flights';
		parent::__construct(
			array('Airlines.DataFields.Positions.AirlinesFlightPosition'),
			'airlines/admin/evals',
			array('name', 'active'),
			'Airlines'
		);
		$this->breadcrumb->add('Bewertungen', URI::build('airlines/admin/evals'));
	}

	public function activate() {
		$this->header();
		$db = Core::_(DB);
		$db->query("SELECT * FROM <p><table:noquote>", array('table' => $this->dbTable));
		$this->tpl->assign("data", $db->fetchAll());
		$this->tpl->assign('baseUri', $this->baseUri);
		$this->tpl->output("/Cms/admin/data_categories");
		$this->footer();
	}

}
?>
