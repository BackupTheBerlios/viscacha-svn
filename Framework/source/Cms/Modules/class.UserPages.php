<?php
/**
 * This contains everything related to users: profile pages, login, registration etc.
 *
 * @package		Cms
 * @subpackage	Modules
 * @author		Matthias Mohr
 * @since 		1.0
 */
class UserPages extends CmsModuleObject {

	const DEFAULT_MEMBER_GID = 3;

	public function __construct() {
		parent::__construct();
	}

	public function about() {
		$id = Request::get(1, VAR_INT);
		$user = UserUtils::getById($id);

		$this->breadcrumb->add('Mitglieder');

		if ($user !== null && ($user->isActive() || Me::get()->isAllowed('admin'))) {
			$this->breadcrumb->add($user->getName());
			$this->header();

			$data = $user->getArray();
			$data['name'] = $user->getName();
			$data['gender'] = UserUtils::getGender($data['gender']);
			if ($user->hasValidBirthday()) {
				$dt = new DT($user->getBirth());
				$data['birth'] = $dt->date();
			}
			else {
				$data['birth'] = '-';
			}
			$data['regdate'] = DT::fromTimestamp($data['regdate'])->date();

			$db = Database::getObject();
			$db->query("SELECT user_id FROM <p>session WHERE user_id = <id:int> LIMIT 1", compact("id"));
			$status = ($db->numRows() > 0);

			if (!$user->isActive()) {
				$this->notice("Dieser Benutzer ist noch nicht freigschaltet und das Profil nur den Administratoren dieser Seite zugänglich.");
			}
			
			$tpl = Response::getObject()->appendTemplate('Cms/user/about');
			$tpl->assign('status', $status, false);
			$tpl->assign('data', $data);
			$tpl->output();
		}
		else {
			$this->header();
			$this->error('Das von Ihnen angeforderte Profil wurde leider nicht gefunden.');
		}
		$this->footer();
	}

	public function settings() {
		$action = Request::get(1, VAR_URI);

		$min_year = date('Y')-110;
		$max_year = date('Y')-8;
		$countries = CmsTools::getCountries();

		$options = $this->getFieldValidation($countries, $min_year, $max_year);
		$options['pw1'][Validator::OPTIONAL] = true;
		$options['old_pw'] = array(
			Validator::OPTIONAL => true,
			Validator::MESSAGE => 'Das derzeitige Passwort ist nicht korrekt.',
			Validator::CALLBACK => 'UserPages::checkPW'
		);
		if (Me::get()->loggedIn()) {
			$this->enableClientFormValidation($options);
		}
		else {
			$this->enableClientFormValidationOnError();
		}

		$this->breadcrumb->add('Einstellungen');
		$this->header();
		if (!Me::get()->loggedIn()) {
			$this->error('Sie müssen sich erst anmelden, bevor Sie auf diese Seite zugreifen können!');
		}
		else {
			$error = array();
			$db = Database::getObject();
			
			$data = array();
			if ($action == 'send') {
				extract(Validator::checkRequest($options));
				if (count($error) > 0) {
					$this->error($error);
				}
				else {
					// Update data
					if (!empty($data['old_pw']) && !empty($data['pw1']) && !empty($data['pw2'])) {
						$data['pw'] = Hash::generate($data['pw1']);
					}
					
					// prepare SQL update
					$sql = $data;
					unset($sql['old_pw'], $sql['pw1'], $sql['pw2'], $sql['birthday'], $sql['birthmonth'], $sql['birthyear']);
					$dt = new DT();
					$dt->setDate($data['birthyear'], $data['birthmonth'], $data['birthday']);
					$sql['birth'] = $dt->dbDate();

					$update = array();
					foreach ($sql as $field => $value) {
						$update[] = "{$field} = <{$field}>";
					}
					$update = implode(', ', $update);

					$sql['id'] = Me::get()->getId();
					$db->query("UPDATE <p>user SET {$update} WHERE id = <id:int>", $sql);

					// Update global data about me
					Session::getObject()->refreshMe();

					$this->ok("Ihre Angaben wurden erfolgreich gespeichert.");
				}
			}

			$my = Me::get()->getArray();
			$my = array_merge($data, $my);

			$tpl = Response::getObject()->appendTemplate('Cms/user/settings');
			$tpl->assign('my', $my);
			$tpl->assign('r_birthday', range(1, 31));
			$tpl->assign('r_birthmonth', range(1, 12));
			$tpl->assign('r_birthyear', range($min_year, $max_year));
			$tpl->assign('countries', $countries);
			$tpl->output();
		}
		$this->footer();
	}

	public function login() {
		$action = Request::get(1, VAR_URI);
		$this->breadcrumb->add('Anmelden');
		$this->header();
		if (Me::get()->loggedIn()) {
			$this->error('Sie sind bereits angemeldet!');
		}
		else {
			$tpl = Response::getObject()->appendTemplate('Cms/user/login');
			if ($action == 'send') {
				$status = Session::getObject()->open(Request::get('email'), Request::get('pw'));
				if ($status == true) {
					$this->ok('Sie haben sich erfolgreich angemeldet!');
				}
				else {
					$this->error('Die Anmeldung ist leider fehlgeschlagen, da entweder Ihre Zugangsdaten nicht korrekt waren oder Ihr Account noch nicht freigeschaltet ist.');
					$tpl->output();
				}
			}
			else {
				$tpl->output();
			}
		}
		$this->footer();
	}

	public function logout() {
		$this->breadcrumb->add('Abmelden');
		$this->header();
		if (!Me::get()->loggedIn()) {
			$this->error('Sie sind bereits abgelemdet!');
		}
		else {
			Session::getObject()->close();
			$this->ok('Sie haben sich erfolgreich abgemeldet!', URI::frontPage());
		}
		$this->footer();
	}

	public function register() {
		$action = Request::get(1, VAR_URI);

		$min_year = date('Y')-110;
		$max_year = date('Y')-8;
		$countries = CmsTools::getCountries();
		$options = $this->getFieldValidation($countries, $min_year, $max_year);
		$this->enableClientFormValidation($options);
		
		$this->breadcrumb->add('Registrieren');
		$this->header();
		if (Me::get()->loggedIn()) {
			$this->error('Sie sind bereits registriert!');
		}
		else {
			// Don't validate the captcha via ajax as the session would end
			if (Config::get('captcha.enable')) {
				Core::loadClass('Core.Security.ReCaptcha');
				$options['recaptcha_response_field'] = array(
					Validator::MESSAGE => 'Der Sicherheitscode wurde nicht korrekt eingegeben.',
					Validator::CALLBACK => 'cb_captcha_check'
				);
			}

			$error = array();
			$data = array_fill_keys(array_keys($options), '');

			if ($action == 'send') {
				extract(Validator::checkRequest($options));
				if (count($error) > 0) {
					$this->error($error);
				}
				else {
					// Insert data
					$dt = new DT();
					$dt->setDate($data['birthyear'], $data['birthmonth'], $data['birthday']);
					$data['birth'] = $dt->dbDate();
					$data['pw1'] = Hash::generate($data['pw1']);
					$data['group_id'] = UserPages::DEFAULT_MEMBER_GID;
					$data['regdate'] = time();
					if (Config::get('security.validate_registered_email') == 1) {
						$data['active'] = 0;
						$data['verification'] = Hash::getRandom();
					}
					else {
						$data['active'] = 1;
						$data['verification'] = '';
					}

					$db = Database::getObject();
					$db->query("
						INSERT INTO <p>user
						(forename, surname, pw, group_id, email, gender, birth, city, country, regdate, active, verification)
						VALUES
						(<forename>, <surname>, <pw1>, <group_id:int>, <email>, <gender>, <birth>, <city>, <country>, <regdate:int>, <active:int>, <verification>)
					", $data);
					$mid = $db->insertID();

					$tpl = Response::getObject()->getTemplate('Cms/mails/register' . iif (!$data['active'], '_confirm') );
					$tpl->assign('mid', $mid, false);
					$tpl->assign('name', UserUtils::getSalutation($data['gender'], $data['forename'], $data['surname']), false);
					$tpl->assign('data', $data, false);
					CmsTools::sendMail(
						$data['email'],
						'Betätigung der Anmeldung bei '.Config::get('general.title'),
						$tpl->parse()
					);

					$this->ok(
						"Sie haben sich erfolgreich registriert." . iif(!$data['active'], ' Bitte aktivieren Sie Ihren Account, in dem Sie auf den Link klicken, der Ihnen an Ihre E-Mail-Adresse geschickt wurde.'),
						URI::build('Cms/user/login')
					);
				}
			}
			if ($action != 'send' || count($error) > 0) {
				$tpl = Response::getObject()->appendTemplate('Cms/user/register');
				$tpl->assign('data', $data);
				$tpl->assign('r_birthday', range(1, 31));
				$tpl->assign('r_birthmonth', range(1, 12));
				$tpl->assign('r_birthyear', range($min_year, $max_year));
				$tpl->assign('countries', $countries);
				if (Config::get('captcha.enable')) {
					$tpl->assign('captcha', recaptcha_get_html(Config::get('captcha.public_key')), false);
				}
				$tpl->output();
			}
		}
		$this->footer();
	}

	public function validate() {
		$this->breadcrumb->add('Registrieren', URI::build('cms/user/register'));
		$this->breadcrumb->add('Benutzerkonto freischalten');
		$this->header();

		$uid = Request::get(1, VAR_INT);
		$hash = Request::get(2, VAR_ALNUM);
		$user = UserUtils::getById($uid);
		if ($user !== null && !$user->isActive() && strcmp($hash, $user->getVerificationCode()) == 0) {
			$db = Database::getObject();
			$db->query("UPDATE <p>user SET active = 1, verification = '' WHERE id = <uid:int>", compact("uid"));
			if ($db->affectedRows() == 1) {
				$this->ok("Ihr Benutzerkonto wurde erfolgreich freigeschaltet.", URI::build("cms/user/login"));
			}
			else {
				$this->error("Es ist ein Fehler beim Freischalten Ihres Benutzerkontos aufgetreten. Bitte wernden Sie sich an den Systemadministrator.", URI::build("cms/contact"));
			}
		}
		else {
			$this->error("Die angegebenen Daten sind nicht mehr gültig. Vermutlich wurde Ihr Benutzerkonto bereits freigeschaltet.", URI::build("cms/user/login"));
		}
		$this->footer();
	}

	public function pwverify() {
		$this->breadcrumb->add('Neues Passwort generieren');
		$this->header();
		if (Me::get()->loggedIn()) {
			$this->error('Sie sind bereits angemeldet!');
		}
		else {
			$uid = Request::get(1, VAR_INT);
			$hash = Request::get(2, VAR_ALNUM);
			$user = UserUtils::getById($uid);
			if ($user !== null && $user->isActive() && strcmp($hash, $user->getVerificationCode()) == 0) {
				$pw = Password::generate();
				$pwh = Hash::generate($pw);
				$db = Database::getObject();
				$db->query("UPDATE <p>user SET pw = <pwh>, verification = '' WHERE id = <uid:int>", compact("uid", "pwh"));
				if ($db->affectedRows() == 1) {
					$db = Database::getObject();
					$db->query("UPDATE <p>user SET verification = <pwh> WHERE id = <uid:int> AND active = 1", compact("pwh", "uid"));

					$tpl = Response::getObject()->getTemplate('Cms/mails/pwremind_newpw');
					$tpl->assign('pw', $pw, false);
					$tpl->assign('name', UserUtils::getSalutation($user->getGender(), $user->getForeName(), $user->getSurName()), false);
					CmsTools::sendMail(
						$user->getEmail(),
						Config::get('general.title') . ': Ihr neues Passwort',
						$tpl->parse()
					);
					$this->ok("Ihr neues Passwort wurden Ihnen per E-Mail zugeschicht.", URI::build("cms/user/login"));
				}
				else {
					$this->error("Es ist ein Fehler bei der Generierung eines neuen Passworts aufgetreten. Bitte wernden Sie sich an den Systemadministrator.", URI::build("cms/contact"));
				}
			}
			else {
				$this->error("Die angegebenen Daten sind nicht mehr gültig.", URI::build("cms/user/pwremind"));
			}
		}
		$this->footer();
	}

	public function pwremind() {
		$action = Request::get(1, VAR_URI);
		$this->breadcrumb->add('Neues Passwort anfordern');
		$this->header();
		$tpl = Response::getObject()->appendTemplate('Cms/user/pwremind');
		if (Me::get()->loggedIn()) {
			$this->error('Sie sind bereits angemeldet!');
		}
		else if ($action == 'send') {
			$mail = Request::get("email");
			$user = UserUtils::getByEmail($mail);
			if ($user !== null) {
				if (!$user->isActive()) {
					$this->error("Ihr Benutzerkonto ist leider noch nicht freigeschaltet.");
				}
				else {
					$data = array(
						'hash' => Hash::getRandom(),
						'id' => $user->getId(),
						'name' => UserUtils::getSalutation($user->getGender(), $user->getForeName(), $user->getSurName())
					);
					$db = Database::getObject();
					$db->query("UPDATE <p>user SET verification = <hash> WHERE id = <id:int> AND active = 1", $data);

					$tpl = Response::getObject()->getTemplate('Cms/mails/pwremind_verify');
					$tpl->assign('data', $data, false);
					CmsTools::sendMail(
						$user->getEmail(),
						Config::get('general.title') . ': Bestätigung deiner Passwortanfrage',
						$tpl->parse()
					);

					$this->ok("Wir haben Ihnen eine E-Mail geschickt. Bitte folgen Sie den dortigen Anweisungen.");
				}
			}
			else {
				$this->error("Die von Ihnen angegebene E-Mail-Adresse wurde leider nicht gefunden.");
				$tpl->output();
			}
		}
		else {
			$tpl->output();
		}
		$this->footer();
	}

	// Validation
	public static function getFieldValidation($countries, $bdayMinYear, $bdayMaxYear) {
		return array(
			'forename' => array(
				Validator::MESSAGE => 'Der Vorname muss mindestens 2 und darf maximal 100 Zeichen lang sein.',
				Validator::MIN_LENGTH => 2,
				Validator::MAX_LENGTH => 100
			),
			'surname' => array(
				Validator::MESSAGE => 'Der Nachname muss mindestens 2 und darf maximal 100 Zeichen lang sein.',
				Validator::MIN_LENGTH => 2,
				Validator::MAX_LENGTH => 100
			),
			'city' => array(
				Validator::MESSAGE => 'Die Stadt muss mindestens 2 und darf maximal 100 Zeichen lang sein.',
				Validator::MIN_LENGTH => 2,
				Validator::MAX_LENGTH => 100
			),
			'country' => array(
				Validator::MESSAGE => 'Bitte wählen Sie ein Land aus der Liste aus.',
				Validator::LIST_CS => $countries
			),
			'email' => array(
				Validator::MULTIPLE => array(
					array(
						Validator::MESSAGE => 'Die E-Mail-Adresse ist nicht korrekt.',
						Validator::CALLBACK => Validator::CB_MAIL
					),
					array(
						Validator::MESSAGE => 'Diese E-Mail-Adresse ist bereits registriert.',
						Validator::CALLBACK => 'UserPages::isMyMailNotDuplicate'
					)
				)
			),
			'gender' => array(
				Validator::MESSAGE => 'Kein gültiges Geschlecht angegeben.',
				Validator::LIST_CS => array('m', 'w')
			),
			'birthday' => array(
				Validator::MESSAGE => 'Der Tag der Geburt ist nicht korrekt.',
				Validator::MIN_VALUE => 1,
				Validator::MAX_VALUE => 31,
				Validator::CALLBACK => Validator::CB_NUM
			),
			'birthmonth' => array(
				Validator::MESSAGE => 'Der Monat der Geburt ist nicht korrekt.',
				Validator::MIN_VALUE => 1,
				Validator::MAX_VALUE => 12,
				Validator::CALLBACK => Validator::CB_NUM
			),
			'birthyear' => array(
				Validator::MESSAGE => 'Das Jahr der Geburt ist nicht korrekt.',
				Validator::MIN_VALUE => $bdayMinYear,
				Validator::MAX_VALUE => $bdayMaxYear,
				Validator::CALLBACK => Validator::CB_NUM
			),
			'pw1' => array(
				Validator::MESSAGE => 'Das Passwort ist nicht sicher genug.',
				Validator::CALLBACK => Validator::CB_PW
			),
			'pw2' => array(
				Validator::MESSAGE => 'Die beiden Passwörter stimmen nicht überein.',
				Validator::COMPARE_EQUAL => 'pw1'
			)
		);
	}
	public static function checkPW($pw) {
		$db = Database::getObject();
		$data = array(
			'id' => Me::get()->getId(),
			'pw' => Hash::generate($pw)
		);
		$db->query("SELECT id FROM <p>user WHERE id = <id:int> AND pw = <pw> AND active = '1' LIMIT 1", $data);
		return ($db->numRows() == 1);
	}
	public static function isMyMailNotDuplicate($mail) {
		// Prüfen ob Mail bereits in der DB existiert
		$user = UserUtils::getByEmail($mail);
		// Nicht existent
		if ($user === null) {
			return true;
		}
		// Existent, aber nur in Ordnung wenn es vom eigenen Account ist
		else if ($user->getId() == Me::get()->getId()) {
			return true;
		}
		return false;
	}

}
?>