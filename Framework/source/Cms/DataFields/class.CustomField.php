<?php
Core::loadInterface('Cms.DataFields.CustomFieldInfo');

/**
 * Base class for custom data field data storage and view positions.
 *
 * @package		Cms
 * @subpackage	DataFields
 * @author		Matthias Mohr
 * @since 		1.0
 */

abstract class CustomField implements CustomFieldInfo {

	protected $id;
	protected $internal;
	protected $name;
	protected $description;
	protected $priority;
	protected $position;
	protected $implemented;
	protected $permissions;
	protected $params;

	public static function constructObject($data) {
		$obj = Core::constructObject($data['type']);
		$obj->injectData($data);
		return $obj;
	}

	public static function getRights() {
		return array('admin', 'editor', 'registered');
	}

	public function injectData($data) {
		$params = $this->getParamNames();
		foreach ($data as $key => $value) {
			switch ($key) {
				case 'position':
					if (is_object($value)) {
						$this->position = &$value;
					}
					else {
						$this->position = Core::constructObject($value);
					}
					break;
				case 'type':
					break;
				case 'params':
				case 'permissions':
					if (empty($data[$key])) {
						$this->$key = array();
					}
					else if (is_array($data[$key])) {
						$this->$key = $data[$key];
					}
					else {
						$this->$key = unserialize($data[$key]);
					}
					break;
				default:
					if (in_array($key, $params)) {
						$this->params[$key] = $value;
					}
					else {
						$this->$key = $value;
					}
			}
		}
	}

	public function getId() {
		return $this->id;
	}
	public function getFieldName() {
		if (empty($this->internal)) {
			if (empty($this->id)) {
				return '';
			}
			else {
				return 'field' . $this->id;
			}
		}
		else {
			return $this->internal;
		}
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
	public function setPosition(CustomDataPosition &$pos) {
		$this->position = $pos;
	}
	public function isImplemented() {
		return !empty($this->implemented);
	}
	public function noLabel() {
		return false;
	}

	public static function ensurePermissionsValid($permissions) {
		$data = array();
		foreach(CustomField::getRights() as $right) {
			foreach (array('read', 'write') as $type) {
				if (empty($permissions[$type][$right])) {
					$data[$type][$right] = 0;
				}
				else {
					$data[$type][$right] = 1;
				}
			}
		}
		return $data;
	}
	public function getPermissions() {
		return self::ensurePermissionsValid($this->permissions);
	}
	public function canRead(User $user = null) {
		return $this->hasPermission('read', $user);
	}
	public function canWrite(User $user = null) {
		return $this->hasPermission('write', $user);
	}
	protected function hasPermission($type, $user = null) {
		if ($user === null) {
			$user = Me::get();
		}
		foreach (self::getRights() as $right) {
			if ($user->isAllowed($right) && !empty($this->permissions[$type][$right])) {
				return true;
			}
		}
		return false;
	}

	public function formatDataForDb($data) {
		return $data;
	}
	public function formatDataFromDb($data) {
		return $data;
	}
	public function getDefaultData() {
		return '';
	}
	public abstract function getInputCode($data = null);
	public abstract function getOutputCode($data = null);
	protected function getDataCode($tpl, $data = null, array $vars = array()) {
		if ($data === null) {
			$data = $this->getDefaultData();
		}
		$vars['data'] = $data;
		return $this->getCodeImpl($tpl, $vars);
	}
	public function getValidation() {
		return array();
	}

	public function getParamNames($add = false) {
		return array();
	}
	public function getParamsData($add = false) {
		if (!is_array($this->params)) {
			$this->params = array();
		}
		return array_merge(
			array_fill_keys($this->getParamNames(), null),
			$this->params
		);
	}
	public function getParamsCode($add = false) {
		return $this->getCodeImpl('/Cms/bits/no_params', compact("add"));
	}
	public function getValidationParams($add = false) {
		return array();
	}

	protected function getCodeImpl($file, $additionalVars = array()) {
		$tpl = Response::getObject()->getTemplate($file);
		$tpl->assign('fieldId', $this->getId());
		$tpl->assign('field', $this->getFieldName());
		$tpl->assign('title', $this->getName());
		$tpl->assign('description', $this->getDescription());
		$tpl->assign('params', $this->getParamsData());
		$tpl->assignMultiple($additionalVars);
		return $tpl->parse();
	}

	public function create() {
		$db = Database::getObject();
		try {
			$db->begin();
			// Einfügen in Felder-Tabelle
			$insert = array(
				'name' => $this->name,
				'desc' => $this->description,
				'internal' => $this->getFieldName(),
				'type' => $this->getClassPath(),
				'pos' => $this->position->getClassPath(),
				'prio' => $this->priority,
				'params' => serialize($this->getParamsData()),
				'permissions' => serialize($this->permissions)
			);
			$db->query("
				INSERT INTO <p>fields (internal, name, description, type, position, priority, params, permissions)
				VALUES (<internal>, <name>, <desc>, <type>, <pos>, <prio:int>, <params>, <permissions>)", $insert);
			// Save generated id to have it for the field name
			$this->id = $db->insertId();

			if (empty($insert['internal']))
			{
				$update = array(
					'internal' => $this->getFieldName(),
					'id' => $this->id
				);
				$db->query("UPDATE <p>fields SET internal = <internal> WHERE id = <id:int>", $update);
			}

			if ($this->getDbDataType() != null) {
				// Spalte erstellen in Daten-Tabelle
				$alter = array(
					'table' => $this->position->getDbTable(),
					'field' => $this->getFieldName(),
					'datatype' => $this->getDbDataType()
				);
				$db->query("ALTER TABLE <p><table:noquote> ADD <field:noquote> <datatype:noquote> NULL DEFAULT NULL", $alter);
			}

			$db->commit();
			$this->invalidateCache();
			return true;
		} catch(QueryException $e) {
			$db->rollback();
			return false;
		}
	}

	public function update() {
		$db = Database::getObject();
		// Aktualisierung in Daten-Tabelle wird nicht erlaubt
		// Aktualisiere in Felder-Tabelle
		$update = array(
			'name' => $this->name,
			'desc' => $this->description,
			'prio' => $this->priority,
			'params' => serialize($this->getParamsData()),
			'permissions' => serialize($this->permissions),
			'position' => $this->position->getClassPath(),
			'id' => $this->id
		);
		$result = $db->query("UPDATE <p>fields SET name = <name>, description = <desc>, priority = <prio:int>, params = <params>, position = <position>, permissions = <permissions> WHERE id = <id:int>", $update);
		$this->invalidateCache();
		return $result;
	}

	public function remove() {
		$db = Database::getObject();
		try {
			$db->begin();
			// Löschen in Felder-Tabelle
			$db->query("DELETE FROM <p>fields WHERE id = <id:int>", array('id' => $this->id));

			if ($this->getDbDataType() != null) {
				// Spalte löschen aus Daten-Tabelle
				$alter = array(
					'table' => $this->position->getDbTable(),
					'field' => $this->getFieldName()
				);
				$db->query("ALTER TABLE <p><table:noquote> DROP <field:noquote>", $alter); // Default value?
			}

			$db->commit();
			$this->invalidateCache();
			return true;
		} catch(QueryException $e) {
			$db->rollback();
			return false;
		}
	}

	protected function invalidateCache() {
		$server = Core::getObject('Core.Cache.CacheServer');
		if ($server != null) {
			$cache = $server->load('fields');
			if ($cache != null) {
				$cache->delete();
			}
		}
	}

}
?>