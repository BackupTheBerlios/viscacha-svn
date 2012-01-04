<?php
Core::loadInterface('Cms.DataFields.Positions.CustomDataPosition');

/**
 * Base class for custom data field data storage and view positions.
 *
 * @package		Cms
 * @subpackage	DataFields
 * @author		Matthias Mohr
 * @since 		1.0
 */

class CustomData {

	private $position;
	private $fields;
	private $template;

	public function __construct(CustomDataPosition $position) {
		$this->position = $position;
		$this->fields = null;
		$this->template = null;
	}

	public function getOutputTemplate() {
		if (empty($this->template)) {
			return '/Cms/bits/positions/output';
		}
		return $this->template;
	}

	public function setOutputTemplate($path = null) {
		$this->template = $path;
	}

	public function load($pkValue) {
		return $this->loadByField($this->position->getPrimaryKey(), $pkValue);
	}

	public function loadByField($field, $value) {
		$this->cacheFields();
		$sql = compact("field", "value");
		$sql['table'] = $this->position->getDbTable();
		$db = Core::_(DB);
		$db->query("SELECT * FROM <p><table:noquote> WHERE <field:noquote> = <value> LIMIT 1", $sql);
		if ($db->numRows() == 1 && $row = $db->fetchAssoc()) {
			foreach ($this->fields as $name => $field) {
				if (isset($row[$name])) {
					$field->setData($row[$name]);
				}
			}
			return true;
		}
		else {
			return false;
		}
	}

	public function remove($pkValue) {
		$data = array(
			'id' => $pkValue,
			'table' => $this->position->getDbTable(),
			'pk' => $this->position->getPrimaryKey()
		);
		return Core::_(DB)->query("DELETE FROM <p><table:noquote> WHERE <pk:noquote> = <id:int>", $data);
	}

	public function edit($pkValue) {
		$sql = array();
		$data = array(
			'id' => $pkValue,
			'table' => $this->position->getDbTable(),
			'pk' => $this->position->getPrimaryKey()
		);
		foreach ($this->fields as $field) {
			if ($field->getDbDataType() != null) {
				$name = $field->getFieldName();
				$sql[] = "{$name} = <{$name}>";
				$data[$name] = $field->getDataForDb();
			}
		}
		$sql = implode(', ', $sql);
		$db = Core::_(DB);
		if ($pkValue > 0) {
			return $db->query("UPDATE <p><table:noquote> SET {$sql} WHERE <pk:noquote> = <id:int>", $data);
		}
		else {
			return $db->query("INSERT INTO <p><table:noquote> SET {$sql}", $data);
		}
	}

	public function add() {
		if ($this->edit(0)) {
			$id = Core::_(DB)->insertId();
			return iif($id > 0, $id, 0);
		}
		else {
			return 0;
		}
	}

	public function setFields($fields) {
		foreach($fields as $field) {
			$this->setField($field);
		}
	}

	public function setField($field) {
		$this->fields[$field->getFieldName()] = $field;
	}

	public function getField($internal) {
		$this->cacheFields();
		if (isset($this->fields[$internal])) {
			return $this->fields[$internal];
		}
		return null;
	}

	public function getFields($internal = null) {
		$this->cacheFields();
		if (is_array($internal)) {
			return array_intersect_key($this->fields, array_fill_keys($internal, null));
		}
		return $this->fields;
	}

	public function getFieldsExcept($internal) {
		$this->cacheFields();
		if (is_array($internal)) {
			return array_diff_key($this->fields, array_fill_keys($internal, null));
		}
		return $this->fields;
	}

	public function outputField($internal, $label = true) {
		return $this->output($this->getField($internal), $label);
	}

	public function outputFields($internal = null, $label = true) {
		return $this->outputMultiple($this->getFields($internal), $label);
	}

	public function outputFieldsExcept($internal, $label = true) {
		return $this->outputMultiple($this->getFieldsExcept($internal), $label);
	}
	
	protected function outputMultiple(array $fields, $label) {
		$html = '';
		foreach ($fields as $field) {
			$html .= $this->output($field, $label) . NL;
		}
		return $html;
	}

	protected function output(CustomDataField $field, $label) {
		if ($field == null) {
			return '';
		}
		else {
			$code = $field->getOutputCode(); // Do this before we process our template
			$tpl = Core::_(TPL);
			$tpl->assign('field', $field);
			$tpl->assign('output', $code);
			$tpl->assign('label', $label);
			return $tpl->parse($this->getOutputTemplate());
		}
	}

	// Lazy loading...
	protected function cacheFields() {
		if (!is_array($this->fields)) {
			$cache = Core::getObject('Core.Cache.CacheServer')->load('fields');
			$this->fields = $cache->getFields($this->position->getClassPath());
		}
	}

}
?>