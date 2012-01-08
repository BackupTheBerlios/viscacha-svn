<?php
/**
 * This is the admin control panel.
 *
 * @package		Airlines
 * @subpackage	Modules
 * @author		Matthias Mohr
 * @since 		1.0
 */
class AdminAirportPages extends AdminModuleObject {

	public function __construct() {
		parent::__construct('Airlines');
		$this->breadcrumb->add('Airports', URI::build('airlines/admin/airports'));
	}

	public function main() {
		$this->breadcrumb->resetUrl();
		$this->header();
		$this->show();
		$this->footer();
	}

	public function delete() {
		$this->breadcrumb->add("Löschen");
		$this->header();
		$id = Request::get(1, VAR_INT);
		if (Request::get(2) == 'yes') {
			try {
				Database::getObject()->query("DELETE FROM <p>airports WHERE id = <id:int>", compact("id"));
				$this->ok("Der gewählte Airport wurde gelöscht.");
			} catch (QueryException $e) {
				$this->error("Der gewählte Airport konnte leider nicht gelöscht werden. Möglicherweise referenzieren noch andere Daten auf diesen Airport.");
			}
			$this->show();
		}
		else {
			$this->yesNo(
				"Möchten Sie den ausgewählten Airport wirklich löschen?",
				URI::build('airlines/admin/airports/delete/'.$id.'/yes'),
				URI::build('airlines/admin/airports')
			);
		}
		$this->footer();
	}

	public function edit() {
		$id = Request::get(1, VAR_INT, 0);
		$action = Request::get(2, VAR_URI);

		$this->breadcrumb->add(iif($id > 0, "Bearbeiten", "Hinzufügen"));
		$this->header();

		$db = Database::getObject();
		$data = array('id' => $id, 'flughafen' => '', 'code' => '', 'land' => '', 'stadt' => '');
		if ($action == 'send') {
			$options = array(
				'flughafen' => array(
					Validator::MESSAGE => 'Der Name muss mindestens 2 und darf maximal 128 Zeichen lang sein.',
					Validator::MIN_LENGTH => 2,
					Validator::MAX_LENGTH => 128
				),
				'code' => array(
					Validator::MESSAGE => 'Der Code muss genau 3 Zeichen lang sein.',
					Validator::LENGTH => 3
				),
				'land' => array(
					Validator::MESSAGE => 'Der Name muss mindestens 2 und darf maximal 64 Zeichen lang sein.',
					Validator::MIN_LENGTH => 2,
					Validator::MAX_LENGTH => 64
				),
				'stadt' => array(
					Validator::MESSAGE => 'Der Name muss mindestens 2 und darf maximal 96 Zeichen lang sein.',
					Validator::MIN_LENGTH => 2,
					Validator::MAX_LENGTH => 96
				)
			);

			extract(Validator::checkRequest($options));
			$data['id'] = $id;

			if (count($error) > 0) {
				$this->error($error);
			}
			else {
				if ($id > 0) {
					$db->query("UPDATE <p>airports SET flughafen = <flughafen>, land = <land>, stadt = <stadt>, code = <code> WHERE id = <id:int>", $data);
				}
				else {
					$db->query("INSERT INTO <p>airports SET flughafen = <flughafen>, land = <land>, stadt = <stadt>, code = <code>", $data);
					$data['id'] = $db->insertId();
				}
				$this->ok("Der Airport wurde erfolgreich gespeichert.");
			}
		}
		else if ($id > 0) {
			$db->query("SELECT * FROM <p>airports WHERE id = <id:int>", compact("id"));
			if ($db->numRows() == 1) {
				$data = $db->fetchAssoc();
			}
		}

		$tpl = Response::getObject()->appendTemplate('Airlines/admin/airports_edit');
		$tpl->assign('data', $data);
		$tpl->output();

		$this->footer();
	}

	protected function show() {
		$db = Database::getObject();
		$tpl = Response::getObject()->appendTemplate("Airlines/admin/airports");
		
		$db->query("SELECT * FROM <p>airports ".iif(!empty($country), "WHERE land = <country>")." ORDER BY land, stadt, flughafen", compact("country"));
		$tpl->assign('data', $db->fetchAll());

		$db->query("SELECT DISTINCT land FROM <p>airports ORDER BY land");
		$tpl->assign('countries', $db->fetchAll(null, null, 'land'));

		$country = Request::get('country', VAR_NONE, 'Schweiz');
		$tpl->assign('country', $country);

		$tpl->output();
	}

}
?>
