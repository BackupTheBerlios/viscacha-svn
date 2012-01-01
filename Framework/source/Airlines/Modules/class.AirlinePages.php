<?php
/**
 * This is the default and content pages package.
 *
 * @package		Cms
 * @subpackage	Modules
 * @author		Matthias Mohr
 * @since 		1.0
 */
class AirlinePages extends CmsModuleObject {

	public function __construct() {
		$this->version = '1.0.0';
		$this->module = 'Airlines';
		parent::__construct('Airlines');
		$this->breadcrumb->add('Airlines', URI::build('airlines/airlines/'));
	}

	public function __destruct() {
		parent::__destruct();
	}

	public function main(){
		$page = Request::get(0, VAR_URI);
		if (empty($page)) {
			$this->categories();
		}
		else if (preg_match('/^\d+-/', $page) > 0) {
			$this->airline();
		}
		else {
			parent::main();
		}
	}
	
	protected function categories() {
		$this->breadcrumb->resetUrl();
		$this->header();
		$db = Core::_(DB);
		$db->query("SELECT * FROM <p>categories ORDER BY name");
		$this->tpl->assign("data", $db->fetchAll());
		$this->tpl->output('categories');
		$this->footer();
	}

	protected function airline () {
		list($id,) = explode('-', Request::get(0, VAR_URI), 2);

		$airline = new CustomData(new AirlinesCategoryPosition());
		if ($airline->load($id)) {
			$name = $airline->getField('name');
			$this->breadcrumb->add($name->getData());
			$this->header();
			$this->tpl->assign('airline', $airline);
			$this->tpl->output('airline');
		}
		else {
			$this->header();
			$this->error('Die Airline wurde leider nicht gefunden.');
		}
		$this->footer();
	}

	public function evaluate() {
		$this->main();
	}

	public function search() {
		$this->main();
	}

	public function top() {
		$this->main();
	}

}
?>
