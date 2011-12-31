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
		$this->version = '1.0.0';
		$this->module = 'Admin CP: Documents';
		parent::__construct();
		$this->breadcrumb->add("Seiten", URI::build("cms/admin/documents"));
	}

	public function __destruct() {
		parent::__destruct();
	}

	public function main(){
		$this->breadcrumb->resetUrl();
		$this->header();
		$this->docs();
		$this->footer();
	}

	public function delete() {
		$this->breadcrumb->add("Löschen");
		$this->header();
		$id = Request::get(1, VAR_INT);
		if (Request::get(2) == 'yes') {
			$db = Core::_(DB);
			$db->query("DELETE FROM <p>page WHERE id = <id:int>", compact("id"));
			if ($db->affectedRows() == 1) {
				$this->ok("Die gewählte Seite wurde gelöscht.");
			}
			else {
				$this->error("Die gewählte Seite konnte leider nicht gelöscht werden.");
			}
			$this->docs();
		}
		else {
			$this->yesNo(
				"Möchten Sie die gewählte Seite wirklich löschen?",
				URI::build('cms/admin/documents/delete/'.$id.'/yes'),
				URI::build('cms/admin/documents')
			);
		}
		$this->footer();
	}

	public function write() {
		$db = Core::_(DB);
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
						Validator::MESSAGE => 'Die URI enthält Zeichen die nicht erlaubt sind. Erlaubt sind: a-z, 0-9, _, -',
						Validator::REGEXP => '/^[\w\d\-]*$/i'
					),
					array(
						// Mindestlänge wird geprüft, da es keine doppelten URIs geben darf, aber die Startseite keine URI hat.
						Validator::MESSAGE => 'Die URI muss mindestens 1 und darf maximal 100 Zeichen lang sein.',
						Validator::MAX_LENGTH => 100
					),
					array(
						Validator::MESSAGE => 'Die angegebene URI existiert bereits für eine andere Seite.',
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
		$this->enableClientFormValidation($options);

		$this->breadcrumb->add(iif($id > 0, "Bearbeiten", "Hinzufügen"));
		$this->scriptFiles[URI::build('client/scripts/wymeditor/jquery.wymeditor.js')] = 'text/javascript';
		$this->scriptFiles[URI::build('client/scripts/jquery/jquery.keyfilter.js')] = 'text/javascript';
		$this->header();

		$data = array('id' => $id, 'title' => '', 'uri' => '', 'content' => '');
		if ($action == 'send') {
			extract(Validator::checkRequest($options));
			$data['id'] = $id;

			if (count($error) > 0) {
				$this->error($error);
			}
			else {
				if ($id > 0) {
					$db->query("UPDATE <p>page SET title = <title>, uri = <uri>, content = <content> WHERE id = <id:int>", $data);
				}
				else {
					$db->query("INSERT INTO <p>page SET title = <title>, uri = <uri>, content = <content>", $data);
					$data['id'] = $db->insertId();
				}
				$this->ok("Die Seite wurde erfolgreich gespeichert.");
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

		$this->tpl->assign('data', Sanitize::saveHTML($data));
		$this->tpl->output('admin/docs_write');

		$this->footer();
	}

	protected function docs() {
		$db = Core::_(DB);
		$db->query("SELECT id, title, uri FROM <p>page ORDER BY title");
		$this->tpl->assign("data", $db->fetchAll());
		$this->tpl->output("admin/docs");
	}

}
?>
