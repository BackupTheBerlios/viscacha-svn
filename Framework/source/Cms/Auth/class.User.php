<?php
Core::loadClass("Cms.Auth.GuestUser");
/**
 * This is a registered user.
 *
 * @package		Cms
 * @subpackage 	Auth
 * @author		Matthias Mohr
 * @since 		1.0
 */
class User {

	protected $data;
	protected $acl;

	public function __construct(array $data) {
		$fields = UserUtils::getTableFields();
		foreach($fields as $f => $default) {
			if (!isset($data[$f])) {
				Core::throwError("User data is incomplete. Field {$f} is missing.");
				$data[$f] = $default;
			}
		}

		// Special case: Birthday -> Parse it into chunks
		if (strpos($data['birth'], '-') === false) {
			$data['birth'] = '0000-00-00';
		}
		$bday = explode('-', $data['birth']);
		$data['birthday'] = intval($bday[2]);
		$data['birthmonth'] = intval($bday[1]);
		$data['birthyear'] = intval($bday[0]);

		$this->data = $data;
		$this->acl = null;
	}

	// Properties

	public function getId() {
		return $this->data['id'];
	}

	public function getName() {
		return trim( $this->getForeName() . ' ' . $this->getSurName() );
	}

	public function getForeName() {
		return $this->data['forename'];
	}

	public function getSurName() {
		return $this->data['surname'];
	}

	public function getSalutation($includeForename = false) {
		return self::formatSalutation($this->getGender(), $this->getForeName(), $this->getSurName(), $includeForename);
	}

	public function getPassword() {
		return $this->data['pw'];
	}

	public function getEmail() {
		return $this->data['email'];
	}

	public function getGroupId() {
		return $this->data['group_id'];
	}

	public function getGender() {
		return $this->data['gender'];
	}

	public function getCity() {
		return $this->data['city'];
	}

	public function getCountry() {
		return $this->data['country'];
	}

	public function getRegDate() {
		return $this->data['regdate'];
	}

	public function getLastVisit() {
		return $this->data['lastvisit'];
	}

	public function isActive() {
		return $this->data['active'] == 1;
	}

	public function getVerificationCode() {
		return $this->data['verification'];
	}

	public function getBirth() {
		return $this->data['birth'];
	}

	public function getBirthYear() {
		return $this->data['birthyear'];
	}

	public function getBirthDay() {
		return $this->data['birthday'];
	}

	public function getBirthMonth() {
		return $this->data['birthmonth'];
	}

	public function hasValidBirthday() {
		return ($this->data['birthmonth'] > 0 && $this->data['birthday'] > 0 && $this->data['birthyear'] > 0);
	}

	public function getArray() {
		return $this->data;
	}

	// Permissions

	protected function loadACL() {
		if ($this->acl === null) {
			$cache = Core::getObject('Core.Cache.CacheServer');
			$p = $cache->load('permissions');
			$this->acl = $p->getPermissions($this->getGroupId());
		}
	}

	public function loggedIn() {
		return $this->isAllowed('registered');
	}

	public function isAllowed($right) {
		$this->loadACL();
		if (isset($this->acl[$right])) {
			return ($this->acl[$right] == 1);
		}
		else {
			Core::throwError('No permission with name "'.$right.'" found.', INTERNAL_NOTICE);
			return false;
		}
	}

}
?>