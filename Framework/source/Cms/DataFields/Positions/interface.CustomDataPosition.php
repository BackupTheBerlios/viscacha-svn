<?php
/**
 * Interface for custom data field data storage and view positions.
 *
 * @package		Cms
 * @author		Matthias Mohr
 * @since 		1.0
 */

interface CustomDataPosition {

	public function getDbTable(); // Tabeellen-Name für die Daten, ohne Prefix
	public function getName();
	public function getClassPath(); // Example: Cms.DataFields.CustomDataPosition

}
?>
