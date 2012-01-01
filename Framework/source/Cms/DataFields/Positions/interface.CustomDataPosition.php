<?php
/**
 * Interface for custom data field data storage and view positions.
 *
 * @package		Cms
 * @subpackage	DataFields
 * @author		Matthias Mohr
 * @since 		1.0
 */

interface CustomDataPosition {

	public function getDbTable(); // Database table name for the data, without prefix
	public function getName();
	public function getClassPath(); // Example: Cms.DataFields.CustomDataPosition

}
?>
