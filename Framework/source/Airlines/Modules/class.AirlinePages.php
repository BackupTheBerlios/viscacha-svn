<?php
/**
 * This are the airline pages.
 *
 * @package		Airlines
 * @subpackage	Modules
 * @author		Matthias Mohr
 * @since 		1.0
 */
class AirlinePages extends FieldDataPages {

	public function __construct() {
		parent::__construct(
			array('Airlines.DataFields.Positions.AirlinesCategoryPosition'),
			'airlines/airlines',
			array('name'),
			'Airlines'
		);
		$this->breadcrumb->add('Airlines', URI::build('airlines/airlines'));
	}
	
	public function main() {
		$page = Request::get(0, VAR_URI);
		if (preg_match('/^(\d+)-/', $page, $matches) > 0 && $matches[1] > 0) {
			$this->detail($matches[1]);
		}
		else {
			parent::main();
		}
	}
	
	public function write() {
		$this->notFoundError();
	}
	
	public function remove() {
		$this->notFoundError();
	}
	
	protected function getTemplateFile($file) {
		switch($file) {
			case '/Cms/fields/data_categories':
				return 'categories';
			case '/Cms/fields/data_categories_detail':
				return 'airline';
			default: 
				return $file;
		}
	}

}
?>
