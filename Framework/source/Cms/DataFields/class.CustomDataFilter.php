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

	private $order;
	private $offset;
	private $num;
	private $conditions;
	private $position;

	public function  __construct(CustomDataPosition $position) {
		$this->fields = array();
		$this->offset = 0;
		$this->num = 0;
		$this->order = array();
		$this->conditions = null;
		$this->position = $position;
	}

	public function getPosition() {
		return $this->position;
	}

	public function field($fieldName) {
		$this->fields[] = $fieldName;
	}
	
	public function orderBy($fieldName, $ascending = true) {
		$this->order[$fieldName] = $ascending;
	}

	public function limit($num, $offset = 0) {
		$this->offset = $offset;
		$this->num = $num;
	}

	public function condition($fieldName, $value, $operator = CustomDataFilterCondition::OP_EQUAL) {
		$this->conditions = new CustomDataFilterConditionGroup(
			array(
				new CustomDataFilterCondition($fieldName, $value, $operator)
			)
		);
	}

	public function conditions(CustomDataFilterConditionGroup $conditions) {
		$this->conditions = $conditions;
	}

	public function retrieveList() {
		$list = new CustomDataList($this->position);
		$list->setFields($this->fields);
		$result = $this->execute();
		$db = Database::getObject();
		while($row = $db->fetchAssoc($result)) {
			$list->addData($row);
		}
		return $list;
	}

	public function execute() {
		$vars = array('table' => $this->position->getDbTable());

		$fields = $this->buildFields($vars);

		$where = '';
		if ($this->conditions != null) {
			$where = 'WHERE ' . $this->buildWhere($this->conditions, $vars);
		}

		$order = '';
		if (count($this->order) > 0) {
			$order = 'ORDER BY ' . $this->buildOrder($vars);
		}

		$limit = $this->buildLimit($vars);

		return Database::getObject()->query("SELECT {$fields} FROM <p><table:noquote> {$where} {$order} {$limit}", $vars);
	}

	protected function buildFields(array &$vars) {
		if (count($this->fields) > 0) {
			$fields = array($this->position->getPrimaryKey());
			foreach ($this->fields as $i => $field) {
				$fields[] = "<field{$i}:noquote>";
				$vars["field{$i}"] = $field;
			}
			return implode(', ', $fields);
		}
		else {
			return '*';
		}
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