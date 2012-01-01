<?php
Core::loadClass('Cms.CmsModuleObject');
/**
 * This is a general Admin module object. All admin modules should extend it.
 *
 * @package		Cms
 * @author		Matthias Mohr
 * @since 		1.0
 * @abstract
 */

abstract class AdminModuleObject extends CmsModuleObject {

	const MENU_FILE = './data/admincp.php';

	protected $menu;

	public function __construct($package = 'Cms') {
		parent::__construct($package);
		$this->scriptFiles[URI::build('client/scripts/admin.js')] = 'text/javascript';
		$this->cssFiles[URI::build('client/styles/admin.css')] = 'all';
		$this->loadMenu();
		$this->breadcrumb->add('Administrationsbereich', URI::build('cms/admin'));
	}

	public function __destruct() {
		parent::__destruct();
	}

	protected function header() {
		parent::header();
		$this->tpl->assign('menu', $this->menu);
		$this->tpl->output("/Cms/admin/header");
	}

	protected function footer() {
		$this->tpl->assign('menu', $this->menu);
		$this->tpl->output("/Cms/admin/footer");
		parent::footer();
	}

	public function route() {
		if (Me::get()->isAllowed('admin')) {
			parent::route();
		}
		else {
			parent::header();
			$this->error("Sie sind nicht berechtigt den Administrationebereich zu betreten.");
			parent::footer();
		}
	}

	protected function loadMenu() {
		require(self::MENU_FILE);
		$this->menu = $config;
		if (empty($this->menu['Pages'])) {
			Core::throwError("No pages in admin menu table found.", INTERNAL_ERROR);
		}
		foreach ($this->menu['Pages'] as $key => $menuClass) {
			$this->menu['Pages'][$key] = Core::constructObject($menuClass);
		}
	}

}

abstract class AdminMenuObject {
	public abstract function getHeader($class); // Header-Name
	public abstract function getMenu($class); // Link-Name-Pairs
}
?>