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
		return 'Cms.DataFields.CustomHeader';
	}
	public function getInputCode() {
		return $this->getCodeImpl('/Cms/bits/text/header');
	}
	public function getOutputCode() {
		return $this->getCodeImpl('/Cms/bits/text/header');
	}
}
?>