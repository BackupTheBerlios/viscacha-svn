<?php
Core::loadInterface('Cms.DataFields.CustomFieldInfo');

/**
 * Base class for custom field with data.
 *
 * @package		Cms
 * @subpackage	DataFields
 * @author		Matthias Mohr
 * @since 		1.0
 */

class CustomFieldData implements CustomFieldInfo {

	protected $field;
	protected $data;

	public function  __construct(CustomField &$field, $data = null) {
		if ($field === null) {
			Core::throwError('First parameter is null.');
		}
		$this->field = $field;
		$this->setData($data);
	}

	public function setData($data = null) {
		if ($this->field instanceof CustomExternalFields) {
			$this->data = new CustomExternalFieldData($this, $data);
		}
		else {
			$this->data = $data;
		}
	}

	public function getData($key = null) {
		if ($key != null && $this->data instanceof CustomExternalFieldData) {
			return $this->data->getData($key);
		}
		else {
			return $this->data;
		}
	}

	public function getField() {
		return $this->field;
	}

	public function getFieldName() {
		return $this->field->getFieldName();
	}
	public function getName() {
		return $this->field->getName();
	}
	public function getDescription() {
		return $this->field->getDescription();
	}
	public function getPriority() {
		return $this->field->getPriority();
	}
	public function getPosition() {
		return $this->field->getPosition();
	}
	public function noLabel() {
		return $this->field->noLabel();
	}
	public function getPermissions() {
		return $this->field->getPermissions();
	}
	public function canRead(User $user = null) {
		return $this->field->canRead($user);
	}
	public function canWrite(User $user = null) {
		return $this->field->canWrite($user);
	}
	public function getTypeName() {
		return $this->field->getTypeName();
	}
	public function getClassPath() {
		return $this->field->getClassPath();
	}
	public function formatDataForDb() {
		return $this->field->formatDataForDb($this->data);
	}
	public function formatDataFromDb() {
		return $this->field->formatDataFromDb($this->data);
	}
	public function getDbDataType() {
		return $this->field->getDbDataType();
	}
	public function getDefaultData() {
		return $this->field->getDefaultData();
	}
	public function getInputCode() {
		return $this->field->getInputCode($this->data);
	}
	public function getOutputCode() {
		return $this->field->getOutputCode($this->data);
	}
	public function getValidation() {
		return $this->field->getValidation();
	}

}

class CustomExternalFieldData implements ArrayAccess {
	
	private $data;
	private $external;
	private $parent;
	
	public function __construct(CustomFieldData &$parent, $data) {
		$this->data = $data;
		$this->parent = $parent;
		$this->external = null;
	}
	
	public function __toString() {
		return strval($this->data);
	}
	
	public function changeData($data) {
		$this->data = $data;
	}
	
	public function setData($data, array $external) {
		$this->data = $data;
		$this->external = $external;
	}
	
	public function getExternal() {
		$this->loadData();
		return $this->external;
	}
	
	public function getData($key = null) {
		if ($key !== null) {
			$this->loadData();
			return $this->external[$key];
		}
		else {
			return $this->data;
		}
	}
	
	protected function selectData() {
		$field = $this->parent->getField();
		$field->selectData($this);
	}
	
	public function deleteData() {
		$field = $this->parent->getField();
		$field->deleteData($this);
	}
	
	public function insertData() {
		$field = $this->parent->getField();
		$field->injectData($this);
	}
	
	public function updateData() {
		$field = $this->parent->getField();
		$field->updateData($this);
	}
	
	protected function loadData() {
		if ($this->external === null) {
			$this->selectData();
		}
	}

    public function offsetSet($offset, $value) {
		$this->loadData();
        if (is_null($offset)) {
            $this->external[] = $value;
        } else {
            $this->external[$offset] = $value;
        }
    }
    public function offsetExists($offset) {
		$this->loadData();
        return isset($this->external[$offset]);
    }
    public function offsetUnset($offset) {
		$this->loadData();
        unset($this->external[$offset]);
    }
    public function offsetGet($offset) {
		$this->loadData();
        return isset($this->external[$offset]) ? $this->external[$offset] : null;
    }
	
}
?>