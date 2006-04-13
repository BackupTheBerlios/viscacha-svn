<?php
class cache_smileys extends CacheItem {

	function load () {
		global $db;
		if ($this->exists() == true) {
			$this->import();
		}
		else {
			$this->data = array();
			$result = $db->query("SELECT * FROM {$db->pre}smileys",__LINE__,__FILE__);
			$this->data = array();
			while ($bb = $db->fetch_assoc($result)) {
				$this->data[] = $bb;
			}
			$this->export();
		}
		$this->smileys = $this->data;
	}

}
?>