<?php
/**
 * Simple header implementation for custom fields.
 *
 * @package		Cms
 * @subpackage	DataFields
 * @author		Matthias Mohr
 * @since 		1.0
 */

class CustomHeader extends CustomText {

	public function getTypeName() {
		return 'Überschrift';
	}
	public function getClassPath() {
		return 'Cms.DataFields.Types.CustomHeader';
	}
	public function getInputCode($data = null) {
		return $this->getDataCode('/Cms/bits/text/header', $data);
	}
}
?>