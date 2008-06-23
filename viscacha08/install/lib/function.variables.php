<?php
function rmdirr($dirname) {
	global $filesystem;
	if (!file_exists($dirname)) {
		return false;
	}
	if (is_file($dirname)) {
		return $filesystem->unlink($dirname);
	}
	$dir = dir($dirname);
	while (false !== $entry = $dir->read()) {
		if ($entry == '.' || $entry == '..') {
			continue;
		}
		if (is_dir("$dirname/$entry")) {
			rmdirr("$dirname/$entry");
		}
		else {
			$filesystem->unlink("$dirname/$entry");
		}
	}
	$dir->close();
	return $filesystem->rmdir($dirname);
}
function getFilePath($package, $step) {
	$package2 = explode('_', $package, 2);
	if (!empty($package2[1]) && !file_exists('package/'.$package.'/steps/'.$step.'.php')) {
		return 'package/'.$package2[0].'/steps/'.$step.'.php';
	}
	return 'package/'.$package.'/steps/'.$step.'.php';
}
function return_array($group, $id) {
	$file = "../language/{$id}/{$group}.lng.php";
	if (file_exists($file)) {
		include($file);
	}
	if (!isset($lang) || !is_array($lang)) {
		$lang = array();
	}
	return $lang;
}

function getLangCodes() {
	global $db;
	$l = array();
	$result = $db->query('SELECT id FROM '.$db->pre.'language ORDER BY language',__LINE__,__FILE__);
	while($row = $db->fetch_assoc($result)) {
		$settings = return_array('settings', $row['id']);
		if (!isset($l[$settings['spellcheck_dict']])) {
			$l[$settings['spellcheck_dict']] = array();
		}
		$l[$settings['spellcheck_dict']] = $row['id'];
	}
	return $l;
}
function getLangCodesByKeys($keys) {
	$codes = array();
	foreach ($keys as $entry) {
		if (preg_match('~language_(\w{2})_?(\w{0,2})~i', $entry, $code)) {
			if (!isset($codes[$code[1]])) {
				$codes[$code[1]] = array();
			}
			if (isset($code[2])) {
				$codes[$code[1]][] = $code[2];
			}
			else {
				if (!in_array('', $codes[$code[1]])) {
					$codes[$code[1]][] = '';
				}
			}
		}
	}
	return $codes;
}
function setPackagesInactive() {
	global $db;
	require_once('../classes/class.ini.php');
	$myini = new INI();
	$result = $db->query("SELECT id, internal FROM {$db->pre}packages");
	$data = array();
	$disable = array();
	$dependencies = array();
	$assoc = array();
	while ($row = $db->fetch_assoc($result)) {
		$ini = $myini->read("../modules/{$row['id']}/package.ini");

		if (!isset($ini['dependency']) || !is_array($ini['dependency'])) {
			$ini['dependency'] = array();
		}

		$min_compatible = ((!empty($ini['min_version']) && version_compare(VISCACHA_VERSION, $ini['min_version'], '>=')) || empty($ini['min_version']));
		$max_compatible = ((!empty($ini['max_version']) && version_compare(VISCACHA_VERSION, $ini['max_version'], '<=')) || empty($ini['max_version']));

		$data[$row['id']] = array(
			'min_version' => !empty($ini['min_version']) ? $ini['min_version'] : '',
			'max_version' => !empty($ini['max_version']) ? $ini['max_version'] : '',
			'id' => $row['id'],
			'internal' => $row['internal'],
			'dependency' => (isset($ini['dependency']) && is_array($ini['dependency'])) ? $ini['dependency'] : array(),
			'compatible' => ($min_compatible && $max_compatible)
		);

		if ($data[$row['id']]['compatible'] == false) {
			$disable[$row['id']] = $row['internal'];
		}
		$dependencies = array_merge($dependencies, $ini['dependency']);
		if (isset($assoc[$row['internal']])) {
			$assoc[$row['internal']][] = $row['id'];
		}
		else {
			$assoc[$row['internal']] = array($row['id']);
		}
	}

	$n = 0;
	while (count($dependencies) > 0) {
		reset($dependencies);
		$value = current($dependencies);
		$key = key($dependencies);
		if (isset($assoc[$value])) {
			foreach ($assoc[$value] as $id) {
				if (isset($data[$id]['dependency']) && is_array($data[$id]['dependency'])) {
					foreach ($data[$id]['dependency'] as $int) {
						if (!in_array($int, $disable) && !in_array($int, $dependencies)) {
							$dependencies[] = $int;
						}
					}
				}
				if (!isset($disable[$id])) {
					$disable[$id] = $value;
				}
			}
		}
		unset($dependencies[$key]);

		$n++;
		if ($n > 10000) {
			trigger_error("setPackagesInactive(): Your database is inconsistent - Please ask the Viscacha support for help.", E_USER_ERROR); // Break loop, Database seems to be inconsistent (or thousands of packages are installed)
		}
	}
	if (count($disable) > 0) {
		$in = implode(',', array_keys($disable));
		$db->query("UPDATE {$db->pre}packages SET active = '0' WHERE id IN ({$in})");
	}
}


function GPC_escape($var){
	global $db, $config, $lang;
	if (is_numeric($var) || empty($var)) {
		// Do nothing to save time
	}
	elseif (is_array($var)) {
		$cnt = count($var);
		$keys = array_keys($var);
		for ($i = 0; $i < $cnt; $i++){
			$key = $keys[$i];
			$var[$key] = $this->save_str($var[$key]);
		}
	}
	elseif (is_string($var)){
		$var = preg_replace('#(script|about|applet|activex|chrome|mocha):#is', "\\1&#058;", $var);
		$var = str_replace("\0", '', $var);
		if (version_compare(PHP_VERSION, '5.2.3', '>=')) {
			$var = htmlentities($var, ENT_QUOTES, $lang->charset(), false);
		}
		else {
			$var = htmlentities($var, ENT_QUOTES, $lang->charset());
			$var = str_replace('&amp;#', '&#', $var);
		}
		$var = addslashes($var);
	}
	return $var;
}

function GPC_unescape($var){
	if (is_numeric($var) || empty($var)) {
		// Do nothing to save time
	}
	elseif (is_array($var)) {
		foreach ($var as $key => $value) {
			$var[$key] = GPC_unescape($value);
		}
	}
	elseif (is_string($var)){
		$var = stripslashes($var);
	}
	return $var;
}

// Variables
@set_magic_quotes_runtime(0);
@ini_set('magic_quotes_gpc',0);
// Start - Thanks to phpBB for this code
if (isset($_POST['GLOBALS']) || isset($_FILES['GLOBALS']) || isset($_GET['GLOBALS']) || isset($_COOKIE['GLOBALS'])) {
	die("Hacking attempt (Globals)");
}
if (isset($_SESSION) && !is_array($_SESSION)) {
	die("Hacking attempt (Session Variable)");
}
if (@ini_get('register_globals') == '1' || strtolower(@ini_get('register_globals')) == 'on') {
	unset($not_used, $input);
	$not_unset = array('_GET', '_POST', '_COOKIE', '_SERVER', '_SESSION', '_ENV', '_FILES');

	$input = array_merge($_GET, $_POST, $_COOKIE, $_ENV, $_FILES);
	if (isset($_SERVER) && is_array($_SERVER)) {
		$input = array_merge($input, $_SERVER);
	}
	if (isset($_SESSION) && is_array($_SESSION)) {
		$input = array_merge($input, $_SESSION);
	}

	unset($input['input'], $input['not_unset']);

	while (list($var,) = @each($input)) {
		if (!in_array($var, $not_unset)) {
			unset($$var);
			// Testen
			if (isset($GLOBALS[$var])) {
				unset($GLOBALS[$var]);
			}
		}
	}

	unset($input);
}
// End

if (get_magic_quotes_gpc() == 1) {
	$_REQUEST = GPC_unescape($_REQUEST);
}

$_REQUEST = GPC_escape($_REQUEST);
?>
