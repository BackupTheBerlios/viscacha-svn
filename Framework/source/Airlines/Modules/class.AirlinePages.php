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
				$uri = AirlineTools::buildUri($airlineData->getId(), $airlineData->getData('name'), true);
				$this->flightPage->setBaseUri($uri);
				$this->breadcrumb->add($airlineData->getData('name'), URI::build($uri));
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

		// Airline details
		$this->airlinePage->detail($id, '/Airlines/airline');

		// Airline average ratings
		$avgFields = CustomRating::getAverageFields(
			$this->flightPage->getPosition(),
			array('airline' => $id, 'published' => 1)
		);
		$tpl = Response::getObject()->appendTemplate('/Airlines/airline_avg');
		$tpl->assign('data', $avgFields, false);
		$tpl->output();

		// Evaluated flights
		$filter = new CustomDataFilter($this->flightPage->getPosition());
		$filter->field('title');
		$filter->fieldCalculation('rating', '(vdr_rating+bord_rating+service_rating)/3');
		$filter->condition('airline', $id);
		$filter->condition('published', 1);
		$filter->orderBy('date');
		$this->flightPage->overview('/Airlines/flights', Config::get('pagination.evaluations'), $filter);

		$this->footer();
	}

	protected function flight($id) {
		$this->breadcrumb->add('Flug-Bewertung');
		$this->header();
		$this->flightPage->detail($id);
		$this->footer();
	}
	
	public function write() {
		$this->breadcrumb->add('Bewertung verfassen');
		$this->header();
		$this->flightPage->write();
		$this->footer();
	}

	public function search() {
		$this->breadcrumb->add('Suche');
		$this->header();
		CmsPage::notFoundError();
		$this->footer();
	}

	public function top() {
		$this->breadcrumb->add('Rating');
		$this->header();

		$filter = new CustomDataFilter($this->flightPage->getPosition());
		$filter->fieldForeign('categories', 'name', 'airlineName');
		$filter->fieldForeign('categories', 'id', 'airlineId');
		$filter->fieldCalculation('rating', '(AVG(vdr_rating)+AVG(bord_rating)+AVG(service_rating))/3');
		$filter->join('categories', 'id', 'airline');
		$filter->condition('published', 1);
		$filter->orderBy('rating', false);
		$filter->groupBy('airline');
		$this->flightPage->overview('/Airlines/top', 0, $filter);

		$this->footer();
	}

}
?>
