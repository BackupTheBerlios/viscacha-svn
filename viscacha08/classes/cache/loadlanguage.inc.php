<?php
class cache_loadlanguage extends CacheItem {

	function load () {
		global $db;
		if ($this->exists() == true) {
		    $this->import();
		}
		else {
		    $result = $db->query("SELECT id, language, detail FROM {$db->pre}language WHERE publicuse != '0'");
		    $this->data = array();
		    while ($row = $db->fetch_assoc($result)) {
		        $this->data[$row['id']] = $row;
		    }
		    $this->createJavascript();
		    $this->export();
		}
	}

	function createJavascript() {
		global $lang, $config, $filesystem;
		$old_lang = $lang->getdir(true);
		foreach ($this->data as $id => $details) {
			$lang->setdir($id);
			$prefix = "// JS Language file for Viscacha {$config['version']} - Language: {$details['language']}\n";
			$prefix .= "var cookieprefix = '{$config['cookie_prefix']}'\n";
			$sections = array(
				'javascript' => "templates/language_{$id}.js", // Frontend
				'admin/javascript' => "admin/html/language_{$id}.js" // Backend
			);
			foreach ($sections as $lngfile => $jsfile) {
				$code = $lang->javascript($lngfile);
				if ($code === false) {
					$code = 'alert("Could not load language file (JS)!");';
				}
				if (file_exists($jsfile)) {
					$filesystem->chmod($jsfile, 0666);
				}
				$filesystem->file_put_contents($jsfile, $prefix.$code);
			}
		}
		$lang->setdir($old_lang);
	}

}
?>