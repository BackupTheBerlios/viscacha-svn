<?php
/**
 * Simple auto complete text field implementation for custom fields.
 *
 * @package		Cms
 * @subpackage	DataFields
 * @author		Matthias Mohr
 * @since 		1.0
 */

class CustomAutoCompleteTextField extends CustomTextField {

	private $pk = null;

	public function getDbDataType() {
		return $this->usePk() ? 'INT(10)' : 'VARCHAR('.$this->getMaxPossibleLength().')';
	}
	public function getTypeName() {
		return 'Text, einzeilig (mit Vorschlägen)';
	}
	public function getClassPath() {
		return 'Cms.DataFields.Types.CustomAutoCompleteTextField';
	}
	public function getInputCode($data = null) {
		return $this->getDataCode('/Cms/bits/textfield/ac_input', $data, compact("strict"));
	}
	public function getValidation() {
		if ($this->params['strict']) {
			return array(
				Validator::MULTIPLE => array(
					parent::getValidation(),
					array(
						Validator::MESSAGE => 'Der Wert im Feld "'.$this->getName().'" ist nicht gültig.',
						Validator::CALLBACK => array($this, 'isValid'),
						Validator::OPTIONAL => $this->params['optional']
					)
				)
			);
		}
		else {
			return parent::getValidation();
		}
	}
	public function getParamNames($add = false) {
		return array_merge(parent::getParamNames($add), array('strict', 'source_type', 'source'));
	}
	public function getParamsCode($add = false) {
		return $this->getCodeImpl('/Cms/bits/textfield/ac_params', compact("add"));
	}
	public function getValidationParams($add = false) {
		if ($add == false) {
			return array();
		}
		else {
			return array_merge(
				parent::getValidationParams($add),
				array(
					'strict' => array(
						Validator::VAR_TYPE => VAR_INT
					),
					'source' => array(
						Validator::VAR_TYPE => VAR_NONE
					),
					'source_type' => array(
						Validator::MESSAGE => 'Geben Sie einen gültigen Typ der Datenquelle an.',
						Validator::LIST_CS => array('db', 'file', 'cb')
					)
				)
			);
		}
	}

	public function isValid($value) {
		return in_array(strtolower($value), array_map('strtolower', $this->getList()));
	}

	public function getList($keyword = '') {
		switch($this->params['source_type']) {
			case 'db':
				return $this->getListFromDatabase($keyword);
			case 'cb':
				return $this->getListFromCallback($keyword);
			case 'file':
				return $this->getListFromFile($keyword);
			default:
				return array();
		}
	}

	public function getListFromCallback($keyword = '') {
		$option = $this->params['source'];
		if (is_string($option) && strpos($option, '::') !== false) {
			$option = explode('::', $option);
		}
		return call_user_func($option, $keyword);
	}

	public function getListFromDatabase($keyword = '') {
		list($table, $column) = explode('.', $this->params['source']);
		$db = Database::getObject();
		$where = '';
		if (!empty($keyword)) {
			$where = "WHERE <column:noquote> LIKE '%<keyword:noquote>%'";
		}
		$db->query(
			"SELECT <column:noquote> FROM <p><table:noquote> {$where} ORDER BY <column:noquote> ASC",
			compact("table", "column", "keyword")
		);
		return $db->fetchAll(null, null, $column);
	}

	public function getListFromFile($keyword = '') {
		$f = new File($this->params['source']);
		$data = $f->read(File_LINES_TRIM);
		if (!empty($keyword)) {
			foreach ($data as $key => $value) {
				if (stripos($value, $keyword) === false) {
					unset($data[$key]);
				}
			}
		}
		return $data;
	}

	public function usePk() {
		return ($this->params['strict'] && $this->params['source_type'] == 'db');
	}
	public function getPkName() {
		if ($this->pk == null) {
			list($table,) = explode('.', $this->params['source']);
			$cache = Core::getObject('Core.Cache.CacheServer')->load('field_pk');
			$this->pk = $cache->getPk($table);
		}
		return $this->pk;
	}
	public function formatDataFromDb($value) {
		if ($this->usePk() && is_id($value)) {
			// Data is probably an id
			list($table, $column) = explode('.', $this->params['source']);
			$pkName = $this->getPkName();
			$db = Database::getObject();
			$db->query(
				"SELECT <column:noquote> FROM <p><table:noquote> WHERE <pkName:noquote> = <value>",
				compact("table", "column", "value", "pkName")
			);
			if ($db->numRows() == 1) {
				$value = $db->fetchOne();
			}
		}
		return $value;
	}
	public function formatDataForDb($value) {
		if ($value !== null && $this->usePk()) {
			list($table, $column) = explode('.', $this->params['source']);
			$pkName = $this->getPkName();
			$db = Database::getObject();
			$db->query(
				"SELECT <pkName:noquote> FROM <p><table:noquote> WHERE <column:noquote> = <value> LIMIT 1",
				compact("table", "column", "value", "pkName")
			);
			if ($db->numRows() == 1) {
				$value = $db->fetchOne();
			}
		}
		return $value;
	}

}
?>