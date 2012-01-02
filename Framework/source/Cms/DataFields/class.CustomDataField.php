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
	protected $internal;
	protected $name;
	protected $description;
	protected $priority;
	protected $position;
	protected $implemented;
	protected $permissions;

	protected $data;
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
					$this->position = Core::constructObject($value);
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
			return 'field' . $this->id;
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
	public function setPosition(CustomDataPosition $pos) {
		$this->position = $pos;
	}
	public function isImplemented() {
		return !empty($this->implemented);
	}

	public static function ensurePermissionsValid($permissions) {
		$data = array();
		foreach(CustomDataField::getRights() as $right) {
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
		$this->hasPermission('read', $user);
	}
	public function canWrite(User $user = null) {
		$this->hasPermission('write', $user);
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

	public abstract function getTypeName();
	public abstract function getClassPath(); // Example: Cms.DataFields.CustomDataField

	public function getData() {
		return $this->data;
	}
	public function setData($data) {
		$this->data = $data;
	}
	public abstract function getDbDataType(); // Example: INT(10)
	public abstract function getInputCode();
	public abstract function getOutputCode();
	public abstract function getValidation();

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
		return $this->getCodeImpl('/Cms/bits/no_params');
	}
	public function getValidationParams($add = false) {
		return array();
	}

	protected function getCodeImpl($file) {
		$tpl = Core::_(TPL);
		$tpl->assign('field', $this->getFieldName());
		$tpl->assign('data', Sanitize::saveHTML($this->data));
		$tpl->assign('params', Sanitize::saveHTML($this->getParamsData()));
		return $tpl->parse($file);
	}

	public function create() {
		$db = Core::_(DB);
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

			// Spalte erstellen in Daten-Tabelle
			$alter = array(
				'table' => $this->position->getDbTable(),
				'field' => $this->getFieldName(),
				'datatype' => $this->getDbDataType()
			);
			$db->query("ALTER TABLE <p><table:noquote> ADD <field:noquote> <datatype:noquote> NULL DEFAULT NULL", $alter);

			$db->commit();
			$this->invalidateCache();
			return true;
		} catch(QueryException $e) {
			$db->rollback();
			return false;
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
			$db->query("ALTER TABLE <p><table:noquote> DROP <field:noquote>", $alter); // Default value?

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