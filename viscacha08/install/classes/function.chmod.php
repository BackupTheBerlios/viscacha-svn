<?php
if (defined('VISCACHA_CORE') == false) { die('Error: Hacking Attempt'); }

define('CHMOD_FILE', 'is_file');
define('CHMOD_DIR', 'is_dir');
define('CHMOD_EX', 777);
define('CHMOD_WR', 666);

function set_chmod($dir, $chmod, $type = CHMOD_FILE, $stop = false) {
	global $filesystem;
	if (file_exists($dir) && $type($dir)) {
		if (!check_chmod(decoct($chmod), get_chmod($dir))) {
			$filesystem->chmod($dir, $chmod);
		}
	}
	else {
		if ($type == CHMOD_DIR && !$stop) {
			$filesystem->mkdir($dir, $chmod);
			set_chmod($dir, $chmod, CHMOD_DIR, true);
		}
		elseif (!$stop) {
			$filesystem->file_put_contents($dir, '');
			set_chmod($dir, $chmod, CHMOD_FILE, true);
		}
	}
}
function set_chmod_r($dir, $chmod, $type = CHMOD_DIR, $files = array()) {
	$dh = opendir($dir);
	if (count($files) == 0 && $type == CHMOD_DIR) {
		set_chmod($dir, $chmod, $type);
		$files[] = $dir;
	}
	while ($file = readdir($dh)) {
		if($file != '.' && $file != '..') {
			$fullpath = $dir.'/'.$file;
			if($type($fullpath)) {
				set_chmod($fullpath, $chmod, $type);
				$files[] = $fullpath;
			}
			if (is_dir($fullpath)) {
				$files = set_chmod_r($fullpath, $chmod, $type, $files);
			}
		}
	}
	closedir($dh);
	return $files;
}

function chmod_str2oct($mode) {
	return octdec("0{$mode}");
}

function check_chmod($min, $given) {
	$min = explode("\r\n", chunk_split($min, 1));
	$given = explode("\r\n", chunk_split($given, 1));

	if ($given[0] >= $min[0] && $given[1] >= $min[1] && $given[2] >= $min[2]) {
		return true;
	}
	else {
		return false;
	}

}

function get_chmod($file, $octal = false) {
	if (!file_exists($file)) {
		$chmod = '000';
	}
	else {
		$perms = fileperms($file);
		$chmod = substr(sprintf('%o', $perms), -3);
	}
	if ($octal == true) {
		$chmod = chmod_str2oct($chmod);
	}
	return $chmod;
}

function getViscachaCHMODs() {
	$chmod = array(
		array('path' => '.htaccess', 'chmod' => CHMOD_WR, 'recursive' => false, 'req' => false),

		array('path' => 'admin/backup', 'chmod' => CHMOD_EX, 'recursive' => false, 'req' => false),
		array('path' => 'admin/data/bbcode_test.php', 'chmod' => CHMOD_WR, 'recursive' => false, 'req' => false),
		array('path' => 'admin/data/config.inc.php', 'chmod' => CHMOD_WR, 'recursive' => false, 'req' => false),
		array('path' => 'admin/data/hooks.txt', 'chmod' => CHMOD_WR, 'recursive' => false, 'req' => false),

		array('path' => 'data', 'chmod' => CHMOD_EX, 'recursive' => false, 'req' => true),
		array('path' => 'data/cron', 'chmod' => CHMOD_EX, 'recursive' => false, 'req' => true),
		array('path' => 'data', 'chmod' => CHMOD_WR, 'recursive' => true, 'req' => true),

		array('path' => 'cache', 'chmod' => CHMOD_EX, 'recursive' => true, 'req' => true),
//		array('path' => 'cache', 'chmod' => CHMOD_WR, 'recursive' => true, 'req' => false),

		array('path' => 'classes/cron/jobs', 'chmod' => CHMOD_EX, 'recursive' => false, 'req' => false),
		array('path' => 'classes/feedcreator', 'chmod' => CHMOD_EX, 'recursive' => false, 'req' => false),
		array('path' => 'classes/fonts', 'chmod' => CHMOD_EX, 'recursive' => false, 'req' => false),
		array('path' => 'classes/geshi', 'chmod' => CHMOD_EX, 'recursive' => false, 'req' => false),
		array('path' => 'classes/graphic/noises', 'chmod' => CHMOD_EX, 'recursive' => false, 'req' => false),

		array('path' => 'feeds', 'chmod' => CHMOD_EX, 'recursive' => false, 'req' => true),
//		array('path' => 'feeds', 'chmod' => CHMOD_WR, 'recursive' => true, 'req' => true),

		array('path' => 'images', 'chmod' => CHMOD_EX, 'recursive' => true, 'req' => false),

		array('path' => 'modules', 'chmod' => CHMOD_EX, 'recursive' => true, 'req' => false),
		array('path' => 'modules', 'chmod' => CHMOD_WR, 'recursive' => true, 'req' => false),

		array('path' => 'temp', 'chmod' => CHMOD_EX, 'recursive' => true, 'req' => true),
		array('path' => 'uploads', 'chmod' => CHMOD_EX, 'recursive' => true, 'req' => true)

	);

	$path = 'language';
	$dh = opendir($path);
	while ($file = readdir($dh)) {
		$fullpath = $path.'/'.$file;
		if($file != '.' && $file != '..' && is_id($file) && is_dir($fullpath)) {
			$chmod[] = array('path' => $fullpath.'/modules', 'chmod' => CHMOD_EX, 'recursive' => false, 'req' => false);
			$chmod[] = array('path' => $fullpath, 'chmod' => CHMOD_WR, 'recursive' => true, 'req' => false);
		}
	}
	closedir($dh);

	$path = 'templates';
	$dh = opendir($path);
	while ($file = readdir($dh)) {
		$fullpath = $path.'/'.$file;
		if($file != '.' && $file != '..' && is_id($file) && is_dir($fullpath)) {
			$chmod[] = array('path' => $fullpath, 'chmod' => CHMOD_EX, 'recursive' => false, 'req' => false);
			$chmod[] = array('path' => $fullpath.'/modules', 'chmod' => CHMOD_EX, 'recursive' => false, 'req' => false);
			$chmod[] = array('path' => $fullpath, 'chmod' => CHMOD_WR, 'recursive' => true, 'req' => false);
		}
	}
	closedir($dh);

	$path = 'designs';
	$dh = opendir($path);
	while ($file = readdir($dh)) {
		$fullpath = $path.'/'.$file;
		if($file != '.' && $file != '..' && is_id($file) && is_dir($fullpath)) {
			$dh2 = opendir($fullpath);
			while ($file = readdir($dh2)) {
				$stylesheet = $fullpath.'/'.$file;
				if(preg_match('~\.css$~i', $file)) {
					$chmod[] = array('path' => $stylesheet, 'chmod' => CHMOD_WR, 'recursive' => false, 'req' => false);
				}
			}
			closedir($dh2);
			$chmod[] = array('path' => $fullpath, 'chmod' => CHMOD_EX, 'recursive' => true, 'req' => false);
		}
	}
	closedir($dh);

	return $chmod;
}
?>