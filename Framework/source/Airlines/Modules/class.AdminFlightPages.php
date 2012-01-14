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
		parent::__construct(
			'Airlines.DataFields.Positions.AirlinesFlightPosition',
			'airlines/admin/evals',
			array('title'),
			'Airlines'
		);
		$this->breadcrumb->add('Bewertungen', URI::build('airlines/admin/evals'));
	}

	public function activate() {
		$this->header();

		$filter = new CustomDataFilter($this->page->getPosition());
		$filter->field('title');
		$filter->condition('published', 0);
		$filter->orderBy('date');
		$this->page->overview($this->getTemplateFile('/Cms/admin/data_categories'), $filter);

		$this->footer();
	}

}
?>
