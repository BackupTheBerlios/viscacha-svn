<?php
/**
 * Simple spacer implementation for custom fields.
 *
 * @package		Cms
 * @subpackage	DataFields
 * @author		Matthias Mohr
 * @since 		1.0
 */

class CustomSpacer extends CustomText {

	public function getTypeName() {
		return 'Abstandshalter';
	}
	public function getClassPath() {
		return 'Cms.DataFields.Types.CustomSpacer';
	}
	public function getInputCode($data = null) {
		return $this->getDataCode('/Cms/bits/text/spacer', $data);
	}

}
?>