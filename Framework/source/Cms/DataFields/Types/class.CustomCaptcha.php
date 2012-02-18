<?php
/**
 * Simple text implementation for custom fields.
 *
 * @package		Cms
 * @subpackage	DataFields
 * @author		Matthias Mohr
 * @since 		1.0
 */

Core::loadClass('Core.Security.ReCaptcha');

class CustomCaptcha extends CustomField {

	public function getTypeName() {
		return 'Captcha';
	}
	public function getClassPath() {
		return 'Cms.DataFields.Types.CustomCaptcha';
	}
	public function getDbDataType() {
		return null;
	}
	public function getInputCode($data = null) {
		return recaptcha_get_html(Config::get('captcha.public_key'));
	}
	public function getOutputCode($data = null) {
		return '';
	}
	public function getValidation() {
		return array(
			Validator::MESSAGE => 'Feld "'.$this->getName().'" wurde nicht korrekt ausgefüllt.',
			Validator::CALLBACK => 'cb_captcha_check'
		);
	}
	
	public function getParamsCode($add = false) {
		return 'ACHTUNG: Interner Feldname muss zwingend "recaptcha_response_field" sein!';
	}

}
?>