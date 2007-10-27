<?php
class cache_package_browser extends CacheItem {

	var $types = array(
	 	1 => array(
		 		'name' => 'Packages',
		 		'name2' => 'package',
		 		'import' => 'admin.php?action=packages&job=package_import&file='
	 		),
	 	2 => array(
	 			'name' => 'Designs',
	 			'name2' => 'design',
				'import' => 'admin.php?action=designs&job=design_import&file='
	 		),
	 	3 => array(
	 			'name' => 'Smiley Packs',
	 			'name2' => 'smiley pack',
				'import' => 'admin.php?action=bbcodes&job=smileys_import&file='
	 		),
	 	4 => array(
	 			'name' => 'Language Packs',
	 			'name2' => 'language pack',
				'import' => 'admin.php?action=language&job=import&file='
	 		),
	 	5 => array(
	 			'name' => 'BB-Codes',
	 			'name2' => 'BB-Code',
				'import' => 'admin.php?action=bbcodes&job=custombb_import&file='
	 		),
	);

	function load () {
		global $config;
		if ($this->exists() == true) {
			$this->import();
		}
		else {
			global $config, $admconfig;
			$this->data = array();
			$myini = new INI();
			$servers = explode(';', $admconfig['package_server']);
			foreach ($servers as $server) {
				$content = get_remote($server.'/external.ini');
				if ($content != REMOTE_CLIENT_ERROR) {
					$inis = $myini->parse($content);
					foreach ($inis['files'] as $type => $remotefile) {
						if (!isset($this->data[$type])) {
							$this->data[$type] = array();
						}
						$data = array();
						$path = $server.'/'.$remotefile;
						$content = get_remote($path);
						if ($content != REMOTE_CLIENT_ERROR) {
							$new_data = $myini->parse($content);
							foreach ($row['categories'] as $cid => $cname) {
								if (!isset($data[$type][$cid])) {
									$data[$type][$cid] = array();
								}
								$data['categories'][$cid] = array(
									'name' => $cname,
									'entries' => 0
								);
							}
							foreach ($new_data as $key => $row) {
								if ($key == 'categories') {
									continue;
								}
								else {
									if (!isset($row['category']) || !isset($data['categories'][$row['category']])) {
										continue;
									}
									$data['categories'][$row['category']]['entries']++;
									$data[$row['category']][] = $row;
								}
							}
						}
						$this->data[$type] = array_merge($data, $this->data[$type]);
					}
				}
			}
			$this->export();
		}
	}

	function types() {
		return $this->types;
	}

	function categories($type = IMPTYPE_PACKAGE, $id = null) {
		if ($id == null) {
			return isset($this->data[$type]['categories']) ? $this->data[$type]['categories'] : array() ;
		}
		else {
			return isset($this->data[$type]['categories'][$id]) ? $this->data[$type]['categories'][$id] : array();
		}
	}

	function get ($type = IMPTYPE_PACKAGE, $category = null) {
		$max_age = 60*60*6; // Update every six hours
		if ($this->data == null || ($max_age != null && $this->expired($max_age))) {
			$this->load();
		}
		if ($category == null) {
			$ret = isset($this->data[$type]) ? $this->data[$type] : array();
		}
		else {
			$ret = isset($this->data[$type][$category]) ? $this->data[$type][$category] : array();
		}
		return $ret;
	}
}
?>