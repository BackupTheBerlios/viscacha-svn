<?php
/**
 * Base class for mutiple fields stored in another table.
 *
 * @package		Cms
 * @subpackage	DataFields
 * @author		Matthias Mohr
 * @since 		1.0
 */

abstract class CustomExternalFields extends CustomField {

	public abstract function deleteData(CustomExternalFieldData &$data);
	
	public abstract function updateData(CustomExternalFieldData &$data);
	
	public abstract function insertData(CustomExternalFieldData &$data);
	
	public abstract function selectData(CustomExternalFieldData &$data);
	
}
?>
