<?php
/**
 * This user is a guest.
 *
 * @package		Cms
 * @subpackage 	Auth
 * @author		Matthias Mohr
 * @since 		1.0
 */
class GuestUser extends User {

	public function __construct() {
		$this->data = UserUtils::getTableFields();
	}

	public function getId() {
		return 0;
	}

	public function getSalutation($includeForename = false) {
		return 'Gast';
	}

	public function getGroupId() {
		$cache = Core::getObject('Core.Cache.CacheServer');
		$p = $cache->load('permissions');
		return $p->getGuestID();
	}

	public function isActive() {
		return true;
	}

	public function loggedIn() {
		return false;
	}

}
?>