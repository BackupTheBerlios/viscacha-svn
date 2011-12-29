<?php
/**
 * Helper-Class: Fetch, format and prepare user data
 *
 * @package		Cms
 * @subpackage 	Auth
 * @author		Matthias Mohr
 * @since 		1.0
 */
class UserUtils {

	public static function getById($id) {
		return self::getByField('id', $id);
	}

	public static function getByEmail($mail) {
		return self::getByField('email', $mail);
	}

	private static function getByField($field, $data) {
		$user = null;
		$db = Core::_(DB);
		$db->query("SELECT * FROM <p>user WHERE <field:noquote> = <data>", compact("field", "data"));
		if ($db->numRows() == 1) {
			$user = new User($db->fetchAssoc());
		}
		return $user;
	}

	public static function getSalutation($gender, $forename, $surname, $noGender = false) {
		$salutation = array();
		if ($gender == 'm') {
			$salutation[] = 'Herr';
		}
		elseif ($gender == 'w') {
			$salutation[] = 'Frau';
		}
		else {
			$noGender = true;
		}
		if ($noGender == true) {
			$salutation[] = $forename;
		}
		$salutation[] = $surname;
		return implode(' ', $salutation);
	}

	public static function getGender($gender) {
		if ($gender == 'm') {
			return 'Männlich';
		}
		elseif ($gender == 'w') {
			return 'Weiblich';
		}
		else {
			return '-';
		}
	}

	public static function getGroupName($gid) {
		$cache = Core::getObject('Core.Cache.CacheServer');
		$p = $cache->load('permissions');
		return $p->getTitle($gid);
	}

	public static function getTableFields() {
		return array(
			'id' => 0,
			'forename' => '',
			'surname' => '',
			'pw' => '',
			'group_id' => 0,
			'email' => '',
			'gender' => '',
			'birthday' => '0000-00-00',
			'city' => '',
			'country' => '',
			'regdate' => 0,
			'lastvisit' => 0,
			'active' => 0,
			'verification' => ''
		);
	}

	public static function checkACL($gid, $right) {
		$cache = Core::getObject('Core.Cache.CacheServer');
		$p = $cache->load('permissions');
		$acl = $p->getPermissions($gid);
		if (isset($acl[$right])) {
			return ($acl[$right] == 1);
		}
		else {
			Core::throwError('No permission with name "'.$right.'" found.', INTERNAL_NOTICE);
			return false;
		}
	}

}
?>