<?php
/**
 * Base implementation for custom data list filter.
 *
 * @package		Cms
 * @subpackage	DataFields
 * @author		Matthias Mohr
 * @since 		1.0
 */

class CustomDataFilter {

	private $fields;
	private $fieldsForeign;
	private $fieldsCalc;
	private $order;
	private $group;
	private $offset;
	private $num;
	private $conditions;
	private $position;
	private $join;

	public function  __construct(CustomDataPosition $position) {
		$this->fields = array();
		$this->fieldsForeign = array();
		$this->fieldsCalc = array();
		$this->offset = 0;
		$this->num = 0;
		$this->order = array();
		$this->group = array();
		$this->conditions = null;
		$this->position = $position;
		$this->join = array();
	}

	public function getPosition() {
		return $this->position;
	}

	public function field($fieldName) {
		$this->fields[] = $fieldName;
	}

	public function fieldForeign($table, $fieldName, $alias = null) {
		$this->fieldsForeign[$table.'.'.$fieldName] = $alias === null ? $fieldName : $alias;
	}

	public function fieldCalculation($fieldName, $calculation) {
		$this->fieldsCalc[$fieldName] = $calculation;
	}
	
	public function join($table, $primaryKey, $foreignKey) {
		$this->join[$table] = compact("primaryKey", "foreignKey");
	}
	
	public function orderBy($fieldName, $ascending = true) {
		$this->order[$fieldName] = $ascending;
	}
	
	public function groupBy($fieldName) {
		$this->group[] = $fieldName;
	}

	public function limit($num, $offset = 0) {
		$this->offset = $offset;
		$this->num = $num;
	}

	public function condition($fieldName, $value, $operator = CustomDataFilterCondition::OP_EQUAL) {
		if ($this->conditions === null) {
			$this->conditions = new CustomDataFilterConditionGroup(array());
		}
		$this->conditions->newCondition($fieldName, $value, $operator);
	}

	public function conditions(CustomDataFilterConditionGroup $conditions) {
		$this->conditions = $conditions;
	}

	public function getAmount() {
		$vars = array('table' => $this->position->getDbTable());

		$where = '';
		if ($this->conditions != null) {
			$where = 'WHERE ' . $this->buildWhere($this->conditions, $vars);
		}

		$group = '';
		if (count($this->group) > 0) {
			$group = 'GROUP BY ' . $this->buildGroup($vars);
		}

		$db = Database::getObject();
		$result = $db->query("SELECT COUNT(*) FROM <p><table:noquote> {$where} {$group}", $vars);
		return $db->fetchOne($result);
	}
	
	private function getForeignCalcFields() {
		return array_merge(array_keys($this->fieldsCalc), array_values($this->fieldsForeign));
	}

	public function retrieveList() {
		$list = new CustomDataList($this->position);
		$list->setFields($this->fields);
		$result = $this->execute();
		$db = Database::getObject();
		while($row = $db->fetchAssoc($result)) {
			$fieldData = new CustomData($this->position);
			$fieldData->set($row, true, $list->getFields());
			$fieldData->setCalculated($row, $this->getForeignCalcFields());
			$list->addData($fieldData);
		}
		return $list;
	}

	public function retrieveTo(CustomData $obj) {
		$result = $this->execute();
		$row = Database::getObject()->fetchAssoc($result);
		if ($row) {
			$obj->set($row, true);
			$obj->setCalculated($row, $this->getForeignCalcFields());
			return true;
		}
		else {
			return false;
		}
	}

	protected function execute() {
		$vars = array('table' => $this->position->getDbTable());

		$fields = $this->buildFields($vars);

		$where = '';
		if ($this->conditions != null) {
			$where = 'WHERE ' . $this->buildWhere($this->conditions, $vars);
		}
		
		$join = '';
		if (count($this->join) > 0) {
			$join = $this->buildJoins($vars);
		}

		$order = '';
		if (count($this->order) > 0) {
			$order = 'ORDER BY ' . $this->buildOrder($vars);
		}
		
		$group = '';
		if (count($this->group) > 0) {
			$group = 'GROUP BY ' . $this->buildGroup($vars);
		}

		$limit = $this->buildLimit($vars);

		return Database::getObject()->query("SELECT {$fields} FROM <p><table:noquote> {$join} {$where} {$group} {$order} {$limit}", $vars);
	}

	protected function buildFields(array &$vars) {
		$fields = array();
		if (count($this->fields) > 0) {
			$fields[] = $this->position->getPrimaryKey();
			foreach ($this->fields as $i => $field) {
				$fields[] = "<field{$i}:noquote>";
				$vars["field{$i}"] = $field;
			}
		}
		else {
			$fields[] = '<p><table:noquote>.*';
		}
		foreach ($this->fieldsCalc as $name => $expr) {
			$i = count($vars);
			$vars["cFieldExpr{$i}"] = $expr;
			$vars["cFieldName{$i}"] = $name;
			$fields[] = "<cFieldExpr{$i}:noquote> AS <cFieldName{$i}:noquote>";
		}
		foreach ($this->fieldsForeign as $name => $alias) {
			$i = count($vars);
			$vars["fField{$i}"] = $name;
			$vars["fFieldAlias{$i}"] = $alias;
			$field = "<p><fField{$i}:noquote> AS <fFieldAlias{$i}:noquote>";
			$fields[] = $field;
		}
		return implode(', ', $fields);
	}

	protected function buildJoins(array &$vars) {
		$joins = array();
		foreach ($this->join as $table => $keys) {
			$i = count($vars);
			$vars["join{$i}"] = $table;
			$vars["pk{$i}"] = $keys['primaryKey'];;
			$vars["fk{$i}"] = $keys['foreignKey'];
			$joins[] = "LEFT JOIN <p><join{$i}:noquote> ON <p><join{$i}:noquote>.<pk{$i}:noquote> = <p><table:noquote>.<fk{$i}:noquote>";
		}
		return implode(' ', $joins);
	}

	protected function buildGroup(array &$vars) {
		$group = array();
		foreach ($this->group as $i => $field) {
			$group[] = "<group{$i}:noquote>";
			$vars["group{$i}"] = $field;
		}
		return implode(', ', $group);
	}

	protected function buildOrder(array &$vars) {
		$order = array();
		foreach ($this->order as $field => $asc) {
			$i = count($vars);
			$ascDesc = $asc ? 'ASC' : 'DESC';
			$order[] = "<order{$i}:noquote> {$ascDesc}";
			$vars["order{$i}"] = $field;
		}
		return implode(', ', $order);
	}

	protected function buildLimit(array &$vars) {
		if ($this->num > 0) {
			$vars["offset"] = $this->offset;
			$vars["num"] = $this->num;
			return 'LIMIT <offset:int>, <num:int>';
		}
		else {
			return '';
		}
	}

	protected function buildWhere(CustomDataFilterConditionGroup &$group, array &$vars) {
		$conditions = array();
		foreach ($group->getConditions() as $c) {
			if ($c instanceof CustomDataFilterConditionGroup) {
				$conditions[] = '(' . $this->buildWhere($c, $vars) . ')';
			}
			else if ($c instanceof CustomDataFilterCondition) {
				$i = count($vars);
				$vars["wField{$i}"] = $c->getField();
				$vars["wOp{$i}"] = $c->getOperator();
				$vars["wValue{$i}"] = $c->getValue();
				$conditions[] = "<wField{$i}:noquote> <wOp{$i}:noquote> <wValue{$i}>";
			}
		}
		$link = $group->getLink();
		return implode(" {$link} ", $conditions);
	}

}

class CustomDataFilterConditionGroup {
	
	private $and;
	private $conditions;

	public function __construct(array $conditions, $and = true) {
		$this->and = $and;
		$this->conditions = $conditions;
	}
	
	public function newCondition($fieldName, $value, $operator = CustomDataFilterCondition::OP_EQUAL) {
		$this->conditions[] = new CustomDataFilterCondition($fieldName, $value, $operator);
	}

	public function getLink() {
		return $this->and ? 'AND' : 'OR';
	}
	
	public function getConditions() {
		return  $this->conditions;
	}

}

class CustomDataFilterCondition {

	const OP_EQUAL = '=';
	const OP_LOWER = '<';
	const OP_LOWER_EQUAL = '<=';
	const OP_GREATER = '>';
	const OP_GREATER_EQUAL = '>=';
	const OP_NOT_EQUAL = '<>';
	const OP_LIKE = 'LIKE';

	private $field;
	private $operator;
	private $value;

	public function __construct($fieldName, $value, $operator = self::OP_EQUAL) {
		$this->field = $fieldName;
		$this->value = $value;
		$this->operator = $operator;
	}

	public function getField() {
		return $this->field;
	}

	public function getValue() {
		return $this->value;
	}

	public function getOperator() {
		return $this->operator;
	}

}

?>