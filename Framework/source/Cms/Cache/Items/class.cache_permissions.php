<?php
/**
 * Caches the categories.
 *
 * @package		Core
 * @subpackage	Cache
 * @author		Matthias Mohr
 * @since 		1.0
 */
class cache_permissions extends CacheItem implements CacheObject {

	public function load() {
		$this->data = array(
			'permissions' => array(),
			'guest' => 0,
			'titles' => array()
		);
		$db = Core::_(DB);
		$db->query("SELECT * FROM <p>group");
		while($row = $db->fetchAssoc()) {
			$id = $row['id'];
			unset($row['id']);
			$this->data['titles'][$id] = $row['title'];
			unset($row['title']);
			if ($row['registered'] == 0) {
				$this->data['guest'] = $id;
			}
			$this->data['permissions'][$id] = $row;
		}
	}

	public function getPermissions($gid) {
		$this->get();
		if (isset($this->data['permissions'][$gid])) {
			return $this->data['permissions'][$gid];
		}
		else {
			return array_fill_keys(array_keys(end($this->data['permissions'])), 0);
		}
	}

	public function getGuestID() {
		$this->get();
		return $this->data['guest'];
	}

	public function getTitle($gid) {
		$this->get();
		if (!isset($this->data['titles'][$gid])) {
			$this->data['titles'][$gid] = '';
		}
		return $this->data['titles'][$gid];
	}

	public function getTitles($guest = true) {
		$this->get();
		$titles = $this->data['titles'];
		if ($guest != true) {
			unset($titles[$this->data['guest']]);
		}
		return $titles;
	}

}
?>