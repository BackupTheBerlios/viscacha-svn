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
		parent::__construct();
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
		Response::getObject()->appendTemplate("Cms/admin/emailexport")->output();
		$this->footer();
	}

	public function emailexport2() {
		$this->breadcrumb->add("E-Mail-Adressen exportieren");
		$this->header();

		$db = Database::getObject();
		$sep = Request::get('sep');

		$db->query('SELECT email FROM <p>user WHERE active');
		$emails = $db->fetchAll(null, null, 'email');

		$tpl = Response::getObject()->appendTemplate("Cms/admin/emailexport2");
		$tpl->assign('data', implode($sep, $emails));
		$tpl->output();
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

			$db = Database::getObject();
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
					unset($sql['pw1'], $sql['pw2'], $sql['birthday'], $sql['birthmonth'], $sql['birthyear']);
					if (Me::get()->getId() == $id) {
						unset($sql['group_id'], $sql['active']); // Don't allow to change own group or active state
					}

					$dt = new DT();
					$dt->setDate($data['birthyear'], $data['birthmonth'], $data['birthday']);
					$sql['birth'] = $dt->dbDate();

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

			$tpl = Response::getObject()->appendTemplate("Cms/admin/members_edit");
			$tpl->assign('user', $user);
			$tpl->assign('r_birthday', range(1, 31));
			$tpl->assign('r_birthmonth', range(1, 12));
			$tpl->assign('r_birthyear', range($min_year, $max_year));
			$tpl->assign('countries', $countries);
			$tpl->assign('groups', $groups);
			$tpl->output();
		}
		$this->footer();
	}

	public function delete() {
		$this->breadcrumb->add("Löschen");
		$this->header();
		$id = Request::get(1, VAR_INT);
		if (Request::get(2) == 'yes') {
			$db = Database::getObject();

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
		$db = Database::getObject();
		$db->query("SELECT * FROM <p>group");
		$tpl = Response::getObject()->appendTemplate("Cms/admin/groups");
		$tpl->assign("data", $db->fetchAll());
		$tpl->output();
		$this->footer();
	}

	protected function members() {
		$db = Database::getObject();
		$db->query("SELECT * FROM <p>user ORDER BY surname, forename");
		$data = array();
		while($row = $db->fetchAssoc()){
			$row['group'] = UserUtils::getGroupName($row['group_id']);
			$data[] = $row;
		}
		$tpl = Response::getObject()->appendTemplate("Cms/admin/members");
		$tpl->assign("data", $data);
		$tpl->output();
	}

}
?>
