<?php
/**
 * Interface for custom fields.
 *
 * @package		Cms
 * @subpackage	DataFields
 * @author		Matthias Mohr
 * @since 		1.0
 */

interface CustomFieldInfo {

	public function getFieldName();
	public function getName();
	public function getDescription();
	public function getPriority();
	public function getPosition();
	public function noLabel();

	public function getPermissions();
	public function canRead(User $user = null);
	public function canWrite(User $user = null);

	public function getTypeName();
	public function getClassPath(); // Example: Cms.DataFields.CustomField

	public function getDbDataType(); // Example: INT(10)
	public function getDefaultData();
	public function getValidation();

}
?>
