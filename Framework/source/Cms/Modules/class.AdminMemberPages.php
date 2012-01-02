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
		$this->module = 'Admin CP: Members';
		parent::__construct();
	}

	public function __destruct() {
		parent::__destruct();
	}

	public function main(){
		$this->breadcrumb->add("Mitglieder");
		$this->header();
		$this->members();
		$this->footer();
	}

	public function emailexport() {
		$this->breadcrumb->add("E-Mail-Adressen exportieren");
		$this->header();
		$this->tpl->output("admin/emailexport");
		$this->footer();
	}

	public function emailexport2() {
		$this->breadcrumb->add("E-Mail-Adressen exportieren");
		$this->header();

		$db = Core::_(DB);
		$sep = Request::get('sep');

		$db->query('SELECT email FROM <p>user WHERE active');
		$emails = $db->fetchAll(null, null, 'email');

		$this->tpl->assign('data', Sanitize::saveHtml(implode($sep, $emails)));
		$this->tpl->output("admin/emailexport2");
		$this->footer();
	}

	public function edit() {
		$id = Request::get(1, VAR_INT);
		$action = Request::get(2, VAR_URI);

		$this->breadcrumb->add('Bearbeiten');
		$this->header();
		$member = UserUtils::getById($id);
		if ($member === null) {
			$this->error('Das angeforderte Mitglied wurde leider nicht gefunden.');
			$this->members();
		}
		else {
			$min_year = date('Y')-110;
			$max_year = date('Y')-8;
			$countries = CmsTools::getCountries();

			$db = Core::_(DB);
			$db->query("SELECT id, title FROM <p>group WHERE registered = 1 ORDER BY admin ASC, editor ASC, title");
			$groups = array();
			while ($row = $db->fetchAssoc()) {
				$groups[$row['id']] = $row['title'];
			}

			$options = UserPages::getFieldValidation($countries, $min_year, $max_year);
			$options['pw1'][Validator::OPTIONAL] = true;
			$options['email'] = array(
				Validator::MULTIPLE => array(
					array(
						Validator::MESSAGE => 'Die E-Mail-Adresse ist nicht korrekt.',
						Validator::CALLBACK => Validator::CB_MAIL
					),
					array(
						Validator::MESSAGE => 'Diese E-Mail-Adresse ist bereits registriert.',
						Validator::CLOSURE => function ($mail) use ($id) {
							$other = UserUtils::getByEmail($mail);
							return !($other !== null && $id != $other->getId());
						}
					)
				)
			);
			if (Me::get()->getId() != $id) {
				$options['group_id'] = array(
					Validator::MESSAGE => 'Die Gruppe ist nicht gültig.',
					Validator::LIST_CS => array_keys($groups)
				);
				$options['active'] = array(
					Validator::OPTIONAL => true,
					Validator::EQUALS => 1,
					Validator::VAR_TYPE => VAR_INT
				);
			}

			$error = array();
			$data = array();
			if ($action == 'send') {
				extract(Validator::checkRequest($options));

				if (count($error) > 0) {
					$this->error($error);
				}
				else {
					// Update data
					if (!empty($data['pw1']) && !empty($data['pw2'])) {
						$data['pw'] = Hash::generate($data['pw1']);
					}

					// prepare SQL update
					$sql = $data;
					unset($sql['pw1'], $sql['pw2'], $sql['birthmonth'], $sql['birthyear']);
					if (Me::get()->getId() == $id) {
						unset($sql['group_id'], $sql['active']); // Don't allow to change own group or active state
					}

					$dt = new DT();
					$dt->setDate($data['birthyear'], $data['birthmonth'], $data['birthday']);
					$sql['birthday'] = $dt->dbDate();

					$update = array();
					foreach ($sql as $field => $value) {
						$update[] = "{$field} = <{$field}>";
					}
					$update = implode(', ', $update);

					$sql['id'] = $id;
					$db->query("UPDATE <p>user SET {$update} WHERE id = <id:int>", $sql);

					// Update global data about me
					Session::getObject()->refreshMe();

					$this->ok("Ihre Angaben wurden erfolgreich gespeichert.");
				}
			}

			$user = $member->getArray();
			$user = array_merge($user, $data);

			$this->tpl->assign('user', Sanitize::saveHTML($user));
			$this->tpl->assign('r_birthday', range(1, 31));
			$this->tpl->assign('r_birthmonth', range(1, 12));
			$this->tpl->assign('r_birthyear', range($min_year, $max_year));
			$this->tpl->assign('countries', Sanitize::saveHTML($countries));
			$this->tpl->assign('groups', Sanitize::saveHTML($groups));
			$this->tpl->output('admin/members_edit');
		}
		$this->footer();
	}

	public function delete() {
		$this->breadcrumb->add("Löschen");
		$this->header();
		$id = Request::get(1, VAR_INT);
		if (Request::get(2) == 'yes') {
			$db = Core::_(DB);

			try {
				$db->query("DELETE FROM <p>user WHERE id = <id:int>", compact("id"));
				$this->ok("Das gewählte Mitglied wurde gelöscht.");
			} catch (QueryException $e) {
				$this->error("Das gewählte Mitglied konnte leider nicht gelöscht werden. Möglicherweise referenzieren noch Daten auf dieses Mitglied.");
			}
			$this->members();
		}
		else {
			$this->yesNo(
				"Möchten Sie das gewählte Mitglied wirklich löschen?",
				URI::build('cms/admin/members/delete/'.$id.'/yes'),
				URI::build('cms/admin/members')
			);
		}
		$this->footer();
	}

	public function groups() {
		$this->breadcrumb->add("Gruppen");
		$this->header();
		$db = Core::_(DB);
		$db->query("SELECT * FROM <p>group");
		$this->tpl->assign("data", Sanitize::saveHTML($db->fetchAll()));
		$this->tpl->output("admin/groups");
		$this->footer();
	}

	protected function members() {
		$db = Core::_(DB);
		$db->query("SELECT * FROM <p>user ORDER BY surname, forename");
		$data = array();
		while($row = $db->fetchAssoc()){
			$row['group'] = UserUtils::getGroupName($row['group_id']);
			$data[] = Sanitize::saveHTML($row);
		}
		$this->tpl->assign("data", $data);
		$this->tpl->output("admin/members");
	}

}
?>
