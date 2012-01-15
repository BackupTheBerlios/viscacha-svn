<?php
/**
 * This is the default and content pages package.
 *
 * @package		Cms
 * @subpackage	Modules
 * @author		Matthias Mohr
 * @since 		1.0
 */
class BackendPages extends CmsModuleObject {

	public function __construct() {
		parent::__construct();
	}

	public function main(){
		Response::getObject()->sendStatusCode(404);
	}

	public function suggest() {
		$data = array();
		$id = Request::get(1, VAR_INT);
		$q = Request::get('q');
		$q = SystemEnvironment::fromUtf8($q);
		$db = Database::getObject();
		$db->query("SELECT * FROM <p>fields WHERE id = <id:int>", compact("id"));
		if ($db->numRows() == 1) {
			$field = CustomField::constructObject($db->fetchAssoc());
			if ($field instanceof CustomAutoCompleteTextField) {
				$data = $field->getList($q);
			}
		}
		Response::getObject()->sendHeader('Content-Type: text/plain; charset='.Config::get('intl.charset'));
		echo implode("\n", $data);
	}

}
?>
