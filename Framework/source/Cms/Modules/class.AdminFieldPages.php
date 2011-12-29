<?php
/**
 * This is the admin control panel.
 *
 * @package		Cms
 * @subpackage	Modules
 * @author		Matthias Mohr
 * @since 		1.0
 */
abstract class AdminFieldPages extends AdminModuleObject {

	protected abstract function getPositions();
	protected abstract function getFields();

	public function main() {
		$this->breadcrumb->resetUrl();
		$this->header();
		$this->overview();
		$this->footer();
	}

	public function add() {
		$this->breadcrumb->add('Hinzufügen');
		$this->header();
		$this->footer();
	}

	public function remove() {
		$this->breadcrumb->add('Löschen');
		$this->header();
		$id = Request::get(1, VAR_INT);

		$db = Core::_(DB);
		$db->query("SELECT * FROM <p>fields WHERE id = <id:int>", compact("id"));
		if ($db->numRows() == 1) {
			$field = CustomDataField::constructObject($db->fetchAssoc());
			if ($field->isImplemented()) {
				$this->error('Das Feld kann nicht gelöscht werden, da es durch Nutzung gesperrt wurde.');
			}
			else if (Request::get(2) == 'yes') {
				if ($field->remove()) {
					$this->ok("Das Feld wurde erfolgreich gelöscht.");
				}
				else {
					$this->error("Das Feld konnte leider nicht gelöscht werden.");
				}
				$this->overview();
			}
			else {
				$this->yesNo(
					"Möchten Sie das gewählte Feld inkl. aller verknüpften Daten wirklich löschen? Eine vorherige Datensicherung wird dringend empfohlen!",
					URI::build('airlines/admin/cfields/remove/'.$id.'/yes'),
					URI::build('airlines/admin/cfields')
				);
			}
		}
		else {
			$this->error('Das Feld wurde nicht gefunden.');
		}
		$this->footer();
	}

	public function edit() {
		$this->breadcrumb->add('Bearbeiten');
		$this->header();
		$this->footer();
	}

	protected function overview() {
		$db = Core::_(DB);
		$pos = $this->getPositions();
		$db->query("SELECT * FROM <p>fields WHERE position IN(<pos:string[]>) ORDER BY position, priority", compact("pos"));
		$fields = array();
		while ($row = $db->fetchAssoc()) {
			$fields[] = CustomDataField::constructObject($row);
		}
		$this->tpl->assign("data", $fields);
		$this->tpl->output("/cms/admin/fields");
	}

}
?>