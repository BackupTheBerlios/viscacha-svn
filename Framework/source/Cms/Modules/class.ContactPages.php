<?php
/**
 * This is the default and content pages package.
 *
 * @package		Cms
 * @subpackage	Modules
 * @author		Matthias Mohr
 * @since 		1.0
 */
class ContactPages extends CmsModuleObject {

	public function __construct() {
		$this->version = '1.0.0';
		$this->module = 'Contact form';
		parent::__construct();
	}

	public function __destruct() {
		parent::__destruct();
	}

	public function main(){
		$this->contact();
	}

	private function contact() {
		$isSent = (Request::get(0, VAR_URI) == 'send');
		$options = array(
			'name' => array(
				Validator::MESSAGE => 'Der Name muss mindestens 5 und darf maximal 150 Zeichen lang sein.',
				Validator::MIN_LENGTH => 5,
				Validator::MAX_LENGTH => 150
			),
			'email' => array(
				Validator::MESSAGE => 'Die E-Mail-Adresse ist nicht korrekt.',
				Validator::CALLBACK => Validator::CB_MAIL
			),
			'message' => array(
				Validator::MESSAGE => 'Die Nachricht entspricht nicht den Vorgaben (mindestens 10 Zeichen, maximal 1000 Zeichen).',
				Validator::MIN_LENGTH => 10,
				Validator::MAX_LENGTH => 1000
			),
			'title' => array(
				Validator::MESSAGE => 'Der Titel entspricht nicht den Vorgaben (mindestens 5 Zeichen, maximal 100 Zeichen).',
				Validator::MIN_LENGTH => 5,
				Validator::MAX_LENGTH => 100
			)
		);
		$this->enableClientFormValidation($options);
		// Don't validate the captcha via ajax as the session would end
		if (Config::get('captcha.enable')) {
			Core::loadClass('Core.Security.ReCaptcha');
			$options['recaptcha_response_field'] = array(
				Validator::MESSAGE => 'Der Sicherheitscode wurde nicht korrekt eingegeben.',
				Validator::CALLBACK => 'cb_captcha_check'
			);
		}

		$data = array_fill_keys(array_keys($options), '');
		$data['name'] = iif(Me::get()->loggedIn(), Me::get()->getName());
		$data['email'] = iif(Me::get()->loggedIn(), Me::get()->getEmail());

		$this->breadcrumb->add('Kontakt');
		$this->header();
		if ($isSent) {
			extract(Validator::checkRequest($options));
			if (count($error) > 0) {
				$this->error($error);
			}
			else {
				CmsTools::sendMail(Config::get('general.email'), $data['title'], $data['message'], $data['email'], $data['name']);
				$this->ok('Die Anfrage wurde erfolgreich verschickt. Vielen Dank!');
				$data['title'] = '';
				$data['message'] = '';
			}
		}

		$this->tpl->assign('data', Sanitize::saveHTML($data));
		if (Config::get('captcha.enable')) {
			$this->tpl->assign('captcha', recaptcha_get_html(Config::get('captcha.public_key')));
		}
		$this->tpl->output('contact/contact');
		$this->footer();
	}

}
?>
