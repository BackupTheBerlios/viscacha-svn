<?php
/**
 * This is the admin control panel.
 *
 * @package		Cms
 * @subpackage	Modules
 * @author		Matthias Mohr
 * @since 		1.0
 */
abstract class AdminFieldDataPages extends AdminModuleObject {

	protected $positions;
	protected $baseUri;
	protected $dbTable;
	protected $dbPk;

	public function  __construct(array $positions, $baseUri, $package) {
		parent::__construct($package);
		$this->positions = Core::constructObjectArray($positions);
		// Check that every position has the same table and primary key or this won't work very well
		$table = null;
		$pk = null;
		foreach ($this->positions as $p) {
			if ($table != null && strcasecmp($table, $p->getDbTable()) != 0) {
				Core::throwError('Position "'.$p->getName().'" has a different database table.', INTERNAL_ERROR);
			}
			if ($pk != null && strcasecmp($pk, $p->getPrimaryKey()) != 0) {
				Core::throwError('Position "'.$p->getName().'" has a different primary key.', INTERNAL_ERROR);
			}
			$table = $p->getDbTable();
			$pk = $p->getPrimaryKey();
		}
		$this->dbTable = $table;
		$this->dbPk = $pk;
		$this->baseUri = $baseUri;
	}

	public function main() {
		$this->breadcrumb->resetUrl();
		$this->header();
		$this->overview();
		$this->footer();
	}

	public function write() {
		$id = Request::get(1, VAR_INT);
		$isSent = (Request::get(2, VAR_URI) == 'send');

		$this->breadcrumb->add(iif($id > 0, "Bearbeiten", "Hinzufügen"));
		$this->header();

		$data = new CustomData(reset($this->positions));
		if ($id > 0 && !$data->load($id)) {
			$this->error('Der gewählte Datensatz wurde leider nicht gefunden.');
		}
		else {
			$fields = $data->getFields();

			if ($isSent) {
				$options = array();
				foreach ($fields as $field) {
					$options[$field->getFieldName()] = $field->getValidation();
				}

				$result = Validator::checkRequest($options);

				foreach ($fields as $field) {
					$name = $field->getFieldName();
					if (isset($result['data'][$name])) {
						$field->setData($result['data'][$name]);
					}
				}

				if (count($result['error']) > 0) {
					$this->error($result['error']);
				}
				else {
					$data->setFields($fields);
					$success = false;
					if ($id > 0) {
						$success = $data->edit($id);
					}
					else {
						$id = $data->add();
						if ($id > 0) {
							$success = true;
						}
						else {
							$id = 0;
							$success = false;
						}
					}
					if ($success) {
						$this->ok("Der Datensatz wurde erfolgreich gespeichert.");
					}
					else {
						$this->error("Der Datensatz konnt leider nicht gespeichert werden.");
					}
				}
			}

			$html = array();
			foreach ($fields as $field) {
				$html[] = array(
					'field' => Sanitize::saveHTML($field->getFieldName()),
					'name' => Sanitize::saveHTML($field->getName()),
					'description' => Sanitize::saveHTML($field->getDescription()),
					'code' => $field->getInputCode()
				);
			}
			$this->tpl->assign('fields', $html);
			$this->tpl->assign('id', $id);
			$this->tpl->assign('baseUri', $this->baseUri);
			$this->tpl->output('/Cms/admin/data_categories_write');
		}

		$this->footer();
	}

	public function remove() {
		$id = Request::get(1, VAR_INT);
		$this->breadcrumb->add('Löschen');
		$this->header();

		$data = new CustomData(reset($this->positions));
		if ($data->load($id)) {
			if (Request::get(2) == 'yes') {
				if ($data->remove($id)) {
					$this->ok("Der Datensatz wurde erfolgreich gelöscht.");
				}
				else {
					$this->error("Der Datensatz konnte leider nicht gelöscht werden.");
				}
				$this->overview();
			}
			else {
				$this->yesNo(
					"Möchten Sie den gewählten Datensatz inkl. aller evtl. verknüpften Daten wirklich löschen?",
					URI::build($this->baseUri.'/remove/'.$id.'/yes'),
					URI::build($this->baseUri)
				);
			}
		}
		else {
			$this->error('Der Datensatz wurde nicht gefunden.');
		}
		$this->footer();
	}

	protected function overview() {
		$db = Core::_(DB);
		$db->query("SELECT * FROM <p><table:noquote>", array('table' => $this->dbTable));
		$this->tpl->assign("data", $db->fetchAll());
		$this->tpl->assign('baseUri', $this->baseUri);
		$this->tpl->output("/Cms/admin/data_categories");
	}

}
?>