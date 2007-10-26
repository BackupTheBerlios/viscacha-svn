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
					foreach ($inis as $type => $remotefile) {
						if (!isset($this->data[$type])) {
							$this->data[$type] = array();
						}
						$data = array();
						$path = $server.'/'.$remotefile;
						$content = get_remote($path);
						if ($content != REMOTE_CLIENT_ERROR) {
							$data = $myini->parse($content);
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

	function get ($type = IMPTYPE_PACKAGE) {
		$max_age = 60*60*6; // Update every six hours
		if ($this->data == null || ($max_age != null && $this->expired($max_age))) {
			$this->load();
		}
		return isset($this->data[$type]) ? $this->data[$type] : array();
	}
}
?>