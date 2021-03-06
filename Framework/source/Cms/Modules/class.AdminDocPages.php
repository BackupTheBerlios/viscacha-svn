<?php
/**
 * This is the admin control panel.
 *
 * @package		Cms
 * @subpackage	Modules
 * @author		Matthias Mohr
 * @since 		1.0
 */
class AdminDocPages extends AdminModuleObject {

	public function __construct() {
		parent::__construct();
		$this->breadcrumb->add("Seiten", URI::build("cms/admin/documents"));
	}

	public function main(){
		$this->breadcrumb->resetUrl();
		$this->header();
		$this->docs();
		$this->footer();
	}

	public function delete() {
		$this->breadcrumb->add("L�schen");
		$this->header();
		$id = Request::get(1, VAR_INT);
		if (Request::get(2) == 'yes') {
			$db = Database::getObject();
			$db->query("DELETE FROM <p>page WHERE id = <id:int>", compact("id"));
			if ($db->affectedRows() == 1) {
				CmsPage::ok("Die gew�hlte Seite wurde gel�scht.");
			}
			else {
				CmsPage::error("Die gew�hlte Seite konnte leider nicht gel�scht werden.");
			}
			$this->docs();
		}
		else {
			CmsPage::yesNo(
				"M�chten Sie die gew�hlte Seite wirklich l�schen?",
				URI::build('cms/admin/documents/delete/'.$id.'/yes'),
				URI::build('cms/admin/documents')
			);
		}
		$this->footer();
	}

	public function write() {
		$db = Database::getObject();
		$id = Request::get(1, VAR_INT);
		$action = Request::get(2, VAR_URI);

		$options = array(
			'title' => array(
				Validator::MESSAGE => 'Der Name muss mindestens 2 und darf maximal 255 Zeichen lang sein.',
				Validator::MIN_LENGTH => 2,
				Validator::MAX_LENGTH => 255
			),
			'uri' => array(
				Validator::MULTIPLE => array(
					array(
						Validator::MESSAGE => 'Die URI enth�lt Zeichen die nicht erlaubt sind. Erlaubt sind: a-z, 0-9, _, -',
						Validator::REGEXP => '/^[\w\d\-]*$/i'
					),
					// Mindestl�nge braucht nicht gepr�ft werden, da es keine doppelten URIs geben
					// darf, unddie Startseite keine URI hat. Daher gibt es sowieso eine Meldung
					// (URI schon vorhanden), wenn die URI leer ist.
					array(
						Validator::MESSAGE => 'Die angegebene URI existiert bereits f�r eine andere Seite.',
						Validator::CLOSURE => function ($uri) use ($db, $id) {
							$db->query("SELECT uri FROM <p>page WHERE id != <id:int> AND uri = <uri>", compact("id", "uri"));
							return ($db->numRows() == 0);
						}
					)
				)
			),
			'content' => array(
				Validator::OPTIONAL => true
			)
		);

		$this->breadcrumb->add(iif($id > 0, "Bearbeiten", "Hinzuf�gen"));
		$this->scriptFiles[URI::build('client/scripts/wymeditor/jquery.wymeditor.js')] = 'text/javascript';
		$this->header();

		$data = array('id' => $id, 'title' => '', 'uri' => '', 'content' => '');
		if ($action == 'send') {
			extract(Validator::checkRequest($options));
			$data['id'] = $id;

			if (count($error) > 0) {
				CmsPage::error($error);
			}
			else {
				if ($id > 0) {
					$db->query("UPDATE <p>page SET title = <title>, uri = <uri>, content = <content> WHERE id = <id:int>", $data);
				}
				else {
					$db->query("INSERT INTO <p>page SET title = <title>, uri = <uri>, content = <content>", $data);
					$data['id'] = $db->insertId();
				}
				CmsPage::ok("Die Seite wurde erfolgreich gespeichert.");
			}
		}
		else {
			if ($id > 0) {
				$db->query("SELECT id, title, uri, content FROM <p>page WHERE id = <id:int>", compact("id"));
				if ($db->numRows() == 1) {
					$data = $db->fetchAssoc();
				}
			}
		}

		$tpl = Response::getObject()->appendTemplate('Cms/admin/docs_write');
		$tpl->assign('data', $data);
		$tpl->output();

		$this->footer();
	}

	protected function docs() {
		$db = Database::getObject();
		$db->query("SELECT id, title, uri FROM <p>page ORDER BY title");
		
		$tpl = Response::getObject()->appendTemplate("Cms/admin/docs");
		$tpl->assign("data", $db->fetchAll());
		$tpl->output();
	}

}
?>
