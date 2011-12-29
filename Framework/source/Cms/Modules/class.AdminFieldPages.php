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
	protected abstract function getFieldTypes();

	public function main() {
		$this->breadcrumb->resetUrl();
		$this->header();
		$this->overview();
		$this->footer();
	}

	public function _fieldcode() {
		$type = Request::get('type');
		if (!empty($type) && Core::classExists($type)) {
			$type = Core::constructObject($type);
			echo $type->getParamsCode(true);
		}
	}

	public function add() {
		$isSent = Request::get(1, VAR_URI) == 'send';
		$this->breadcrumb->add('Hinzufügen');
		$this->header();
		$_positions = $this->getPositions();
		$positions = Core::constructObjectArray($_positions);
		$_fieldTypes = $this->getFieldTypes();
		$fieldTypes = Core::constructObjectArray($_fieldTypes);
		$data = array(
			'name' => '',
			'description' => '',
			'priority' => 0,
			'position' => reset($_positions),
			'type' => reset($_fieldTypes),
		);
		$error = array();
		if ($isSent) {
			// Base options for every field
			$options = array(
				'name' => array(
					Validator::MESSAGE => 'Der Name muss mindestens 2 und darf maximal 100 Zeichen lang sein.',
					Validator::MIN_LENGTH => 2,
					Validator::MAX_LENGTH => 100
				),
				'description' => array(
					Validator::OPTIONAL => true
				),
				'priority' => array(
					Validator::OPTIONAL => true,
					Validator::VAR_TYPE => VAR_INT
				),
				'position' => array(
					Validator::LIST_CS => $_positions
				),
				'type' => array(
					Validator::LIST_CS => $_fieldTypes
				)
			);
			// get additional options for the specified field
			$type = Request::get('type');
			if (isset($fieldTypes[$type])) {
				$options = array_merge($options, $fieldTypes[$type]->validateParams(true));
			}

			extract(Validator::checkRequest($options));
			if (count($error) == 0) {
				$field = $fieldTypes[$data['type']];
				$field->injectData($data);
				if ($field->create()) {
					$this->ok("Das Feld wurde erfolgreich angelegt.");
				}
				else {
					$error[] = 'Das Feld konnt leider nicht angelegt werden.';
				}
			}
			if (count($error) > 0) {
				$this->error($error);
			}
		}
		if (!$isSent || count($error) > 0) {
			$this->tpl->assign('positions', $positions);
			$this->tpl->assign('types', $fieldTypes);
			$this->tpl->assign('data', $data);
			$this->tpl->output("/cms/admin/fields_add");
		}
		$this->footer();
	}

	public function remove() {
		$id = Request::get(1, VAR_INT);
		$this->breadcrumb->add('Löschen');
		$this->header();
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