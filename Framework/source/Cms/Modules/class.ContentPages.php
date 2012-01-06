<?php
/**
 * This is the default and content pages package.
 *
 * @package		Cms
 * @subpackage	Modules
 * @author		Matthias Mohr
 * @since 		1.0
 */
class ContentPages extends CmsModuleObject {

	public function __construct() {
		$this->version = '1.0.0';
		$this->module = 'Custom content';
		parent::__construct();
	}

	public function __destruct() {
		parent::__destruct();
	}

	public function main(){
		$this->custom_pages();
	}

	private function parse($page) {
		// URI conversion, example: [@uri:/cms/contact]
		$page = preg_replace_callback('~\[@uri:([^]]+)\]~i', function($m) { return URI::build($m[1]); }, $page);
		return $page;
	}

	private function custom_pages() {
		$uri = Request::get(0, VAR_URI);
		$db = Core::_(DB);
		$db->query("SELECT title, content FROM <p>page WHERE uri = <uri>", compact("uri"));
		if ($db->numRows() != 1) {
			$this->notFoundError();
		}
		else {
			$data = $db->fetchAssoc();
			$this->breadcrumb->add($data['title']);
			$this->header();
			echo $this->parse($data['content']);
			$this->footer();
		}
	}

}
?>
