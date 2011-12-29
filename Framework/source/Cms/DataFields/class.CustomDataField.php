<?php
/**
 * Base implementation for custom fields.
 *
 * @package		Cms
 * @author		Matthias Mohr
 * @since 		1.0
 */

abstract class CustomDataField {

	protected $id;
	protected $name;
	protected $description;
	protected $priority;
	protected $position;

	protected $data;
	protected $params;

	public function __construct() {
	}

	public function getId() {
		return $this->id;
	}
	public function getFieldName() {
		return 'field' . $this->id;
	}
	public function getName() {
		return $this->name;
	}
	public function setName($name) {
		$this->name = $name;
	}
	public function getDescription() {
		return $this->description;
	}
	public function setDescription($description) {
		$this->description = $description;
	}
	public function getPriority() {
		return $this->priority;
	}
	public function setPriority($priority) {
		$this->priority = $priority;
	}
	public function getPosition() {
		return $this->position;
	}
	public function setPosition(CustomDataPosition $pos) {
		$this->position = $pos;
	}

	public abstract function getTypeName();
	public abstract function getClassPath(); // Example: Cms.DataFields.CustomDataField

	public function getData() {
		return $this->data;
	}
	public abstract function getDataType(); // Example: VAR_INT
	public abstract function getDbDataType(); // Example: INT(10)
	public abstract function getInputCode();
	public abstract function getOutputCode();
	public abstract function validate();

	public abstract function getParamNames();
	public function getParamsData() {
		return $this->params;
	}
	public abstract function getParamsCode();
	public abstract function validateParams();

	protected function getCodeImpl($file) {
		$tpl = Core::_(TPL);
		$tpl->assign('field', $this->getFieldName());
		$tpl->assign('data', Sanitize::saveHTML($this->data));
		$tpl->assign('params', Sanitize::saveHTML($this->params));
		return $tpl->output($file);
	}

	public function create() {
		$db = Core::_(DB);
		try {
			$db->begin();
			// Einfügen in Felder-Tabelle
			$insert = array(
				'name' => $this->name,
				'desc' => $this->description,
				'type' => $this->getClassPath(),
				'pos' => $this->position->getClassPath(),
				'prio' => $this->priority,
				'params' => serialize($this->params)
			);
			$db->query("
				INSERT INTO <p>fields (name, description, type, position, priority, params)
				VALUES (<name>, <desc>, <type>, <pos>, <prio:int>, <params>)", $insert);
			// Save generated id to have it for the field name
			$this->id = $db->insertId();

			// Spalte erstellen in Daten-Tabelle
			$alter = array(
				'table' => $this->position->getDbTable(),
				'field' => $this->getFieldName(),
				'datatype' => $this->getDbDataType()
			);
			$db->query("ALTER TABLE <p><table> ADD <field> <datatype> NULL DEFAULT NULL", $alter);

			$db->commit();
		} catch(QueryException $e) {
			$db->rollback();
		}
	}

	public function update() {
		$db = Core::_(DB);
		// Aktualisierung in Daten-Tabelle wird nicht erlaubt
		// Aktualisiere in Felder-Tabelle
		$update = array(
			'name' => $this->name,
			'desc' => $this->description,
			'prio' => $this->priority,
			'params' => serialize($this->params),
			'id' => $this->id
		);
		$db->query("UPDATE <p>fields SET name = <name>, description = <desc>, priority = <prio:int>, params = <params> WHERE id = <id:int>", $update);
	}

	public function remove() {
		$db = Core::_(DB);
		try {
			$db->begin();
			// Löschen in Felder-Tabelle
			$db->query("DELETE FROM <p>fields WHERE id = <id:int>", array('id' => $this->id));

			// Spalte löschen aus Daten-Tabelle
			$alter = array(
				'table' => $this->position->getDbTable(),
				'field' => $this->getFieldName()
			);
			$db->query("ALTER TABLE <p><table> DROP <field>", $alter); // Default value?

			$db->commit();
		} catch(QueryException $e) {
			$db->rollback();
		}
	}

	public function save() {
		$db = Core::_(DB);
		// Speichern in Daten-Tabelle (einzelnd unsinnig????)
	}

}
?>