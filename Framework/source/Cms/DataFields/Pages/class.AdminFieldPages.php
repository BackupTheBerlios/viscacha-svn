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
	protected abstract function getBaseURI();

	protected function getFieldTypes() {
		return array(
			'Cms.DataFields.CustomTextField',
			'Cms.DataFields.CustomMultilineTextField',
			'Cms.DataFields.CustomCheckBox',
			'Cms.DataFields.CustomSelectBox',
			'Cms.DataFields.CustomImageView',
			'Cms.DataFields.CustomUrlField',
			'Cms.DataFields.CustomDatePicker',
			'Cms.DataFields.CustomRating',
			'Cms.DataFields.CustomHeader',
			'Cms.DataFields.CustomText',
			'Cms.DataFields.CustomSpacer',
		);
	}

	public function main() {
		$this->breadcrumb->resetUrl();
		$this->header();
		$this->overview();
		$this->footer();
	}

	public function _fieldcode() { // AJAX
		$type = Request::get('type');
		if (!empty($type) && Core::classExists($type)) {
			$type = Core::constructObject($type);
			echo $type->getParamsCode(true);
		}
	}

	protected function getValidator() {
		return array(
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
			'read' => array(
				Validator::OPTIONAL => true,
				Validator::VAR_TYPE => VAR_ARR_INT
			),
			'write' => array(
				Validator::OPTIONAL => true,
				Validator::VAR_TYPE => VAR_ARR_INT
			)
		);
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
			'internal' => '',
			'priority' => 0,
			'position' => reset($_positions),
			'type' => reset($_fieldTypes),
			'read' => array(),
			'write' => array()
		);
		foreach(CustomDataField::getRights() as $right) {
			foreach (array('read', 'write') as $type) {
				$data[$type][$right] = 1;
			}
		}

		$error = array();
		if ($isSent) {
			// Base options for every field
			$options = array_merge(
				$this->getValidator(),
				array(
					'internal' => array(
						Validator::OPTIONAL => true,
						Validator::MULTIPLE => array(
							array(
								Validator::MESSAGE => 'Der interne Name enthält Zeichen die nicht erlaubt sind. Erlaubt sind: a-z, 0-9, _, -',
								Validator::REGEXP => Validator::RE_URI,
								Validator::OPTIONAL => true
							),
							array(
								Validator::MESSAGE => 'Der interne Name darf maximal 32 Zeichen lang sein.',
								Validator::MAX_LENGTH => 32
							),
							// Check whether internal name exists for the table targeted
							array(
								Validator::MESSAGE => 'Der interne Name existiert bereits für eine anderes Feld der Tabelle.',
								Validator::CLOSURE => function ($internal) use (&$_positions) {
									if (!empty($internal)) {
										$db = Database::getObject();
										$db->query("SELECT id FROM <p>fields WHERE internal = <internal> AND position IN(<_positions:string[]>)", compact("internal", "_positions"));
										return ($db->numRows() == 0);
									}
									return true; // If nothing is specified we will generate a valid name
								}
							)
						)
					),
					'position' => array(
						Validator::MESSAGE => 'Der Anzeigeort ist ungültig.',
						Validator::LIST_CS => $_positions
					),
					'type' => array(
						Validator::MESSAGE => 'Der Feldtyp ist ungültig.',
						Validator::LIST_CS => $_fieldTypes
					)
				)
			);
			// get additional options for the specified field
			$type = Request::get('type');
			if (isset($fieldTypes[$type])) {
				$options = array_merge($options, $fieldTypes[$type]->getValidationParams(true));
			}

			extract(Validator::checkRequest($options));

			if (count($error) == 0) {
				$field = $fieldTypes[$data['type']];
				$this->injectDataToField($field, $data);
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
			$tpl = Response::getObject()->appendTemplate("/Cms/admin/fields_add");
			$tpl->assign('positions', $positions, false);
			$tpl->assign('types', $fieldTypes, false);
			$tpl->assign('data', $data, false);
			$tpl->assign('baseUri', $this->getBaseURI());
			$tpl->output();
		}
		$this->footer();
	}

	public function remove() {
		$id = Request::get(1, VAR_INT);
		$this->breadcrumb->add('Löschen');
		$this->header();
		$db = Database::getObject();
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
					URI::build($this->getBaseURI().'/remove/'.$id.'/yes'),
					URI::build($this->getBaseURI())
				);
			}
		}
		else {
			$this->error('Das Feld wurde nicht gefunden.');
		}
		$this->footer();
	}

	public function edit() {
		$id = Request::get(1, VAR_INT);
		$isSent = Request::get(2, VAR_URI) == 'send';
		$this->breadcrumb->add('Bearbeiten');
		$this->header();

		$db = Database::getObject();
		$db->query("SELECT * FROM <p>fields WHERE id = <id:int>", compact("id"));
		if ($db->numRows() == 0) {
			$this->error('Das Feld wurde leider nicht gefunden.');
			$this->overview();
		}
		else {
			$field = CustomDataField::constructObject($db->fetchAssoc());
			$_positions = $this->getPositions();
			$positions = Core::constructObjectArray($_positions);
			// Fill data array with the default (currently saved) data
			$permissions = $field->getPermissions();
			$data = array(
				'name' => $field->getName(),
				'description' => $field->getDescription(),
				'priority' => $field->getPriority(),
				'position' => $field->getPosition()->getClassPath(),
				'type' => $field->getClassPath(),
				'read' => $permissions['read'],
				'write' => $permissions['write']
			);
			foreach ($field->getParamsData() as $key => $value) {
				$data[$key] = $value;
			}

			$error = array();
			if ($isSent) {
				// Base options for every field
				$options = array_merge(
					$this->getValidator(),
					array(
						'position' => array(
							Validator::MESSAGE => 'Der Anzeigeort ist ungültig.',
							Validator::LIST_CS => $_positions
						)
					),
					$field->getValidationParams(false)
				);
				extract(Validator::checkRequest($options));
				if (count($error) == 0) {
					$this->injectDataToField($field, $data);
					if ($field->update()) {
						$this->ok("Das Feld wurde erfolgreich aktualisiert.");
					}
					else {
						$error[] = 'Das Feld konnt leider nicht aktualisiert werden.';
					}
				}
				if (count($error) > 0) {
					$this->error($error);
				}
			}

			$tpl = Response::getObject()->appendTemplate("/Cms/admin/fields_edit");
			$tpl->assign('field', $field, false);
			$tpl->assign('positions', $positions, false);
			$tpl->assign('data', $data);
			$tpl->assign('baseUri', $this->getBaseURI());
			$tpl->output();
		}
		$this->footer();
	}

	protected function overview() {
		foreach ($this->getPositions() as $p) {
			$cache = Core::getObject('Core.Cache.CacheServer')->load('fields');
			$tpl = Response::getObject()->appendTemplate("/Cms/admin/fields");
			$tpl->assign("data", $cache->getFields($p), false);
			$tpl->assign('baseUri', $this->getBaseURI());
			$tpl->output();
		}
	}

	private function injectDataToField($field, $data) {
		$data['permissions']['read'] = $data['read'];
		$data['permissions']['write'] = $data['write'];
		$field->injectData($data);
	}

}
?>