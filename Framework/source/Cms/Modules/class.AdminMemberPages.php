<?php
/**
 * This is the admin control panel.
 *
 * @package		Cms
 * @subpackage	Modules
 * @author		Matthias Mohr
 * @since 		1.0
 */
class AdminMemberPages extends AdminModuleObject {

	public function __construct() {
		$this->version = '1.0.0';
		$this->module = 'Admin CP';
		parent::__construct();
	}

	public function __destruct() {
		parent::__destruct();
	}

	public function main(){
		$action = Request::get('action', VAR_URI);
		switch($action) {
			case 'members':
			case 'members_delete':
				$this->members($action);
				break;
			case 'members_edit':
			case 'members_edit2':
				$this->members_edit($action);
				break;
			default:
				$this->defaultPage();
				break;
		}
	}

	private function members_edit($action) {
		$id = Request::get('id', VAR_INT);

		$this->breadcrumb->add("Mitglieder");
		$this->breadcrumb->add("Mitglied editieren");
		$this->header();

		$db = Core::_(DB);
		$db->query("SELECT * FROM <p>user WHERE id = <id:int> AND active = '1'", compact("id"));
		if ($db->numRows() == 0) {
			$this->error("Es wurde leider kein Benutzer gefunden.");
			return;
		}
		$udata = $db->fetchAssoc();
		if (!isset($udata['birthyear']) && !isset($udata['birthmonth'])) {
			 $bday = explode('-', $udata['birthday']);
			 $udata['birthday'] = (int) $bday[2];
			 $udata['birthmonth'] = (int) $bday[1];
			 $udata['birthyear'] = (int) $bday[0];
		}

		$error_status = false;
		$min_year = date('Y')-110;
		$max_year = date('Y')-8;
		$options = array(
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
			'street' => array(
				Validator::MESSAGE => 'Die Straße und Hausnummer muss angegeben werden.',
				Validator::MIN_LENGTH => 6
			),
			'plz' => array(
				Validator::MESSAGE => 'Die Postleitzahl muss angegeben werden.',
				Validator::MIN_LENGTH => 3,
				Validator::MAX_LENGTH => 8,
				Validator::CALLBACK => Validator::CB_NUM
			),
			'city' => array(
				Validator::MESSAGE => 'Die Stadt muss mindestens 2 und darf maximal 100 Zeichen lang sein.',
				Validator::MIN_LENGTH => 2,
				Validator::MAX_LENGTH => 100
			),
			'country' => array(
				Validator::MESSAGE => 'Das Land / der Staat muss mindestens 2 und darf maximal 100 Zeichen lang sein.',
				Validator::MIN_LENGTH => 2,
				Validator::MAX_LENGTH => 100
			),
			'company' => array(
				Validator::MESSAGE => 'Die Firma darf maximal 100 Zeichen lang sein.',
				Validator::MAX_LENGTH => 100
			),
			'taxid' => array(),
			'email' => array(
				Validator::MESSAGE => 'Deie E-Mail-Adresse ist nicht korrekt.',
				Validator::CALLBACK => Validator::CB_MAIL
			),
			'phone' => array(
				Validator::MESSAGE => 'Die Telefonnummer darf maximal 30 Zeichen lang sein.',
				Validator::MAX_LENGTH => 30
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
				Validator::MIN_VALUE => $min_year,
				Validator::MAX_VALUE => $max_year,
				Validator::CALLBACK => Validator::CB_NUM
			),
			'alt_contact' => array(),
			'homepage' => array(
				Validator::OPTIONAL => true,
				Validator::CALLBACK => Validator::CB_URL
			),
			'new_pw' => array(
				Validator::OPTIONAL => true,
				Validator::MESSAGE => 'Das neue Passwort ist nicht sicher genug.',
				Validator::CALLBACK => Validator::CB_PW
			),
			'new_pwx' => array(
				Validator::OPTIONAL => true,
				Validator::MESSAGE => 'Die beiden neuen Passwörter stimmen nicht überein.',
				Validator::COMPARE_EQUAL => 'new_pw'
			),
			'usage_price' => array(
				Validator::OPTIONAL => true,
				Validator::MESSAGE => 'Die Angabe des monatlichen Nutzungs-Preises ist nicht korrekt (Beispiel: 22,50)',
				Validator::CALLBACK => 'CmsPages::checkPrice'
			),
			'about' => array(),
			'group_id' => array()
		);

		if ($action == 'members_edit2') {
			extract(Validator::checkRequest($options));

			if (count($error) > 0) {
				$error_status = true;
				$this->error($error);
			}
			else {
				// Update data
				$data['birthday'] = $data['birthyear'].'-'.CmsTools::leadingZero($data['birthmonth']).'-'.CmsTools::leadingZero($data['birthday']);

				$fields = array(
					'forename', 'surname', 'email', 'gender', 'birthday', 'street', 'plz', 'city', 'country',
					'phone', 'homepage', 'alt_contact', 'phone', 'company', 'taxid', 'group_id'
				);
				$fields = array_combine($fields, $fields);

				if (!empty($data['new_pw']) && !empty($data['new_pwx'])) {
					$data['new_pw'] = Hash::generate($data['new_pw']);
					$fields['pw'] = 'new_pw';
				}
				if ($avatar !== false) {
					$fields['avatar'] = 'new_avatar';
					$data['new_avatar'] = $avatar;
				}

				$update = array();
				foreach ($fields as $field => $var) {
					$update[] = "{$field} = <{$var}>";
				}
				$update = implode(', ', $update);

				$data['id'] = $id;
				$db = Core::_(DB);
				$db->query("UPDATE <p>user SET {$update} WHERE id = <id:int>", $data);

				$this->ok("Ihre Angaben wurden erfolgreich verarbeitet und gespeichert.");
			}
		}
		if ($action == 'members_edit' || $error_status == true) {
			if ($error_status == true) {
				foreach ($data as $key => $value) {
					$udata[$key] = $value;
				}
			}

			$cache = Core::getObject('Core.Cache.CacheServer');
			$gCache = $cache->load('permissions');

			$this->tpl->assign('my', Sanitize::saveHTML($udata));
			$this->tpl->assign('r_birthday', range(1, 31));
			$this->tpl->assign('r_birthmonth', range(1, 12));
			$this->tpl->assign('r_birthyear', range(date('Y')-110, date('Y')-8));
			$this->tpl->assign('groups', $gCache->getTitles(false));
			$this->tpl->output('admin/members_edit');
		}
		$this->footer();
	}

	/**
	 * @todo Echtes Löschen ermöglichen, nicht nur sperren
	 */
	private function members($action) {
		$this->breadcrumb->add("Mitglieder");
		$this->breadcrumb->add("Übersicht");
		$this->header();
		$db = Core::_(DB);
		if ($action == 'members_delete') {
			$id = Request::get('id', VAR_INT);
			$db->query("UPDATE <p>user SET active = '0' WHERE id = <id:int>", compact("id"));
			$this->ok('Der Benutzer wurde (wiederherstellbar) gelöscht.');
		}
		$db->query("SELECT id, forename, surname, group_id, email FROM <p>user WHERE active = '1' ORDER BY surname, forename");
		$data = array();
		while($row = $db->fetchAssoc()){
			$row['group'] = UserInfo::getGroupName($row['group_id']);
			$data[] = Sanitize::saveHTML($row);
		}
		$this->tpl->assign("data", $data);
		$this->tpl->output("admin/members");
		$this->footer();
	}

}
?>
