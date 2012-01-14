<?php
/**
 * This are the airline pages.
 *
 * @package		Airlines
 * @subpackage	Modules
 * @author		Matthias Mohr
 * @since 		1.0
 */
class AirlinePages extends CmsModuleObject {

	private $airlinePage;
	private $flightPage;

	public function __construct() {
		parent::__construct('Airlines');
		$this->breadcrumb->add('Airlines', URI::build('airlines/airlines'));

		$this->airlinePage = new FieldDataPages(
			'Airlines.DataFields.Positions.AirlinesCategoryPosition',
			'airlines/airlines',
			array('name')
		);
		$this->flightPage = new FieldDataPages(
			'Airlines.DataFields.Positions.AirlinesFlightPosition',
			'airlines/airlines',
			array('title')
		);
	}

	public function main() {
		$page = Request::get(0, VAR_URI);
		if (preg_match('/^(\d+)-/', $page, $matches) > 0 && !empty($matches[1])) {
			$airlineData = new CustomData($this->airlinePage->getPosition());
			if ($airlineData->load($matches[1])) {
				$uri = AirlineTools::buildUri($airlineData);
				$this->flightPage->setBaseUri($uri);
				$this->breadcrumb->add($airlineData->getData('name'), $uri);
				$flight = Request::get(1, VAR_INT);
				if (is_id($flight)) {
					$this->flight($flight);
				}
				else {
					$this->airline($matches[1]);
				}
			}
			else {
				$this->header();
				$this->notFoundError();
				$this->footer();
			}
		}
		else {
			$this->header();
			$this->airlinePage->overview('/Airlines/categories');
			$this->footer();
		}
	}

	protected function airline($id) {
		$this->breadcrumb->resetUrl();
		$this->header();

		$this->airlinePage->detail($id, '/Airlines/airline');

		$filter = new CustomDataFilter($this->flightPage->getPosition());
		$filter->field('title');
//		$filter->condition('airline', $id);
		$filter->orderBy('date');
		$this->flightPage->overview('/Airlines/flights', $filter);

		$this->footer();
	}

	protected function flight($id) {
		$this->header();
		$this->flightPage->detail($id);
		$this->footer();
	}
	
	public function write() {
		$this->header();
		$this->flightPage->write();
		$this->footer();
	}

	public function search() {
		$this->header();
		CmsPage::notFoundError();
		$this->footer();
	}

	public function top() {
		$this->header();
		CmsPage::notFoundError();
		$this->footer();
	}

}
?>
