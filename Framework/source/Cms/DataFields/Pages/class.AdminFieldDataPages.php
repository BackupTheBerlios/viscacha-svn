<?php
/**
 * This is the admin control panel.
 *
 * @package		Cms
 * @subpackage	Modules
 * @author		Matthias Mohr
 * @since 		1.0
 */
class AdminFieldDataPages extends AdminModuleObject {

	protected $page;

	public function  __construct($position, $baseUri, array $mainFields, $package) {
		parent::__construct($package);
		$this->page = new FieldDataPages($position, $baseUri, $mainFields);
	}

	public function main() {
		$this->breadcrumb->resetUrl();
		$this->header();
		$this->page->overview($this->getTemplateFile('/Cms/admin/data_categories'), Config::get('pagination.admin'));
		$this->footer();
	}
	
	protected function getTemplateFile($file) {
		return $file;
	}

	public function write() {
		$id = Request::get(1, VAR_INT);
		$this->breadcrumb->add(iif($id > 0, "Bearbeiten", "Hinzufügen"));
		$this->header();
		$this->page->write(false, $this->getTemplateFile('/Cms/admin/data_categories_write'));
		$this->footer();
	}

	public function remove() {
		$this->breadcrumb->add('Löschen');
		$this->header();
		$this->page->remove();
		$this->footer();
	}

}
?>