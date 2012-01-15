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
		$this->data = $data;
	}

	public function setData($data = null) {
		$this->data = $data;
	}

	public function getData() {
		return $this->data;
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
?>