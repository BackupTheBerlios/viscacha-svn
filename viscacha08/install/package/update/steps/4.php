<div class="bbody">
<?php
echo "<strong>Starting Update:</strong><br />";

require('../data/config.inc.php');
require_once('lib/function.variables.php');
require_once('../classes/class.phpconfig.php');

echo "- Source files loaded<br />";

if (!class_exists('filesystem')) {
	require_once('../classes/class.filesystem.php');
	$filesystem = new filesystem($config['ftp_server'], $config['ftp_user'], $config['ftp_pw'], $config['ftp_port']);
	$filesystem->set_wd($config['ftp_path']);
}
if (!class_exists('DB')) {
	require_once('../classes/database/'.$config['dbsystem'].'.inc.php');
	$db = new DB($config['host'], $config['dbuser'], $config['dbpw'], $config['database'], $config['dbprefix']);
	$db->setPersistence($config['pconnect']);
	$db->errlogfile = '../'.$db->errlogfile;
}

echo "- FTP class loaded, Database connection started.<br />";

// Config/Hooks
$c = new manageconfig();
$c->getdata('../data/config.inc.php');
$c->updateconfig('version', str, VISCACHA_VERSION);
$c->delete('asia');
$c->savedata();

$c = new manageconfig();
$c->getdata('../admin/data/config.inc.php', 'admconfig');
$c->updateconfig('default_language', int, 0);
$c->updateconfig('checked_package_updates', int, 0);
$c->savedata();

$hooks = file_get_contents('../admin/data/hooks.txt');
if (strpos($hooks, "-update") === false) {
	$hooks = str_replace("-uninstall", "-uninstall\r\n-update_init\r\n-update_finish", $hooks);
	$filesystem->file_put_contents('../admin/data/hooks.txt', $hooks);
}
echo "- Configuration and Hooks updated.<br />";

// Old files
$filesystem->unlink('../classes/class.imageconverter.php');
rmdirr('../classes/spellchecker/dict');
echo "- Old files deleted.<br />";

// Stylesheets
$dir = dir('../designs/');
while (false !== ($entry = $dir->read())) {
	$path = $dir->path.DIRECTORY_SEPARATOR.$entry.DIRECTORY_SEPARATOR;
	if (is_dir($path) && is_numeric($entry) && $entry > 0) {
   		if (file_exists($path.'standard.css')) {
   			$file = file_get_contents($path.'standard.css');
			$file .= "\r\n.tooltip {\r\n	left: -1000px;\r\n	top: -1000px;\r\n	visibility: hidden;\r\n	position: absolute;\r\n	max-width: 300px;\r\n	max-height: 300px;\r\n	overflow: auto;\r\n	border: 1px solid #336699;\r\n	background-color: #ffffff;\r\n	font-size: 8pt;\r\n}\r\n";
			$file .= "\r\n.tooltip_header {\r\n	display: block;\r\n	background-color: #E1E8EF;\r\n	color: #24486C;\r\n	padding: 3px;\r\n	border-bottom: 1px solid #839FBC;\r\n}\r\n";
			$file .= "\r\n.tooltip_body {\r\n	padding: 3px;\r\n}\r\n";
   			$filesystem->file_put_contents($path.'standard.css', $file);

   		}
   		if ($path.'ie.css') {
   			$file = file_get_contents($path.'ie.css');
   			$file .= "\r\n* html .tooltip {\r\n	width: 300px;\r\n}\r\n";
   			$filesystem->file_put_contents($path.'ie.css', $file);
   		}
	}
}
$dir->close();
echo "- Stylesheets updated.<br />";

// MySQL
$file = 'package/'.$package.'/db/db_changes.sql';
$sql = file_get_contents($file);
$sql = str_ireplace('{:=DBPREFIX=:}', $db->prefix(), $sql);
$db->multi_query($sql);
echo "- Database tables updated.<br />";

// Languages
$ini = array(
	'settings' => array(
		'language' => array(
			'compatible_version' => VISCACHA_VERSION
		),
		'language_de' => array(
			'compatible_version' => VISCACHA_VERSION
		)
	),
	'admin/profilefield' => array(
		'language_de' => array(
			'admin_editable_change_settings' => '"Optionen ändern"',
			'admin_editable_change_user_data' => '"Daten ändern"'
		)
	),
	'admin/spider' => array(
		'language_de' => array(
			'admin_spider_no_pending_bots' => 'Es sind derzeit leider keine neu erkannten Spider vorhanden.'
		)
	),
	'global' => array(
		'language' => array(
			'upload_intro1' => 'To attach a file to this post, click the file upload button, select a file and press "submit" to start the upload.<br /><br />Allowed filetypes: {$filetypes}<br />max filesize: {$filesize}',
			'upload_intro1b' => 'Max. image size: {@config->tpcwidth} x {@config->tpcheight} px',
			'upload_error_default' => null,
			'upload_error_fileexists' => null,
			'upload_error_maxfilesize' => null,
			'upload_error_maximagesize' => null,
			'upload_error_noaccess' => null,
			'upload_error_noupload' => null,
			'upload_error_wrongfiletype' => null,
			'ats_select9' => null,
			'ats_choose' => 'No Status',
			'ats_choose_standard_a' => 'Use default setting (Article)',
			'ats_choose_standard_n' => 'Use default setting (News)',
			'profile_never' => 'Never'
		),
		'language_de' => array(
			'upload_intro1' => 'Um an diesen Beitrag eine Datei anzufügen, klicken Sie auf die "Durchsuchen" Schaltfläche und wählen Sie eine Datei aus. Klicken Sie dann auf "Senden", um den Vorgang abzuschließen.<br /><br />Erlaubte Dateitypen: {$filetypes}<br />Maximale Dateigröße: {$filesize}',
			'upload_intro1b' => 'Maxmimale Bildabmessungen: {@config->tpcwidth} x {@config->tpcheight} Pixel',
			'upload_error_default' => null,
			'upload_error_fileexists' => null,
			'upload_error_maxfilesize' => null,
			'upload_error_maximagesize' => null,
			'upload_error_noaccess' => null,
			'upload_error_noupload' => null,
			'upload_error_wrongfiletype' => null,
			'ats_select9' => null,
			'ats_choose' => 'Kein Status',
			'ats_choose_standard_a' => 'Standardeinstellung nutzen (Artikel)',
			'ats_choose_standard_n' => 'Standardeinstellung nutzen (News)',
			'editprofile_about_longdesc' => 'Hier können Sie sich eine persönliche "Forenseite" erstellen.<br /><br />Sie können BB-Codes und maximal <em>{$chars}</em> Zeichen für die Seite nutzen.',
			'profile_about' => 'Persönliche Seite',
			'profile_never' => 'Nie'
		)
	)
);

$c = new manageconfig();
$codes = array();
$keys = array('language', 'language_de');
$codes = getLangCodesByKeys($keys);
$langcodes = getLangCodes();
foreach ($langcodes as $code => $lid) {
	$ldat = explode('_', $code);
	if (isset($codes[$ldat[0]])) {
		$count = count($codes[$ldat[0]]);
		if (in_array('', $codes[$ldat[0]])) {
			$count--;
		}
	}
	else {
		$count = -1;
	}
	if (isset($codes[$ldat[0]]) && !empty($ldat[1]) && in_array($ldat[1], $codes[$ldat[0]])) { // Nehme Original
		$src = 'language_'.$code;
	}
	elseif(isset($codes[$ldat[0]]) && in_array('', $codes[$ldat[0]])) { // Nehme gleichen Langcode, aber ohne Countrycode
		$src = 'language_'.$ldat[0];
	}
	elseif(isset($codes[$ldat[0]]) && $count > 0) { // Nehme gleichen Langcode, aber falchen Countrycode
		$src = 'language_'.$ldat[0].'_'.reset($codes[$ldat[0]]);
	}
	else { // Nehme Standard
		$src = 'language';
	}
	foreach($ini as $file => $data){
		if (!isset($data[$src])) {
			continue;
		}
		$c->getdata("../language/{$lid}/{$file}.lng.php", 'lang');
		foreach ($data[$src] as $varname => $text) {
			if ($text === null) {
				$c->delete($varname);
			}
			else {
				$c->updateconfig($varname, str, $text);
			}
		}
		$c->savedata();
	}
}

echo "- Language files updated.<br />";

// Set incompatible packages inactive
setPackagesInactive();
echo "- Incompatible Packages set as 'inactive'.<br />";

// Refresh Cache
$dirs = array('../cache/', '../cache/modules/');
foreach ($dirs as $dir) {
	if ($dh = @opendir($dir)) {
		while (($file = readdir($dh)) !== false) {
			if (strpos($file, '.php') !== false) {
				$filesystem->unlink($dir.$file);
			}
	    }
		closedir($dh);
	}
}
echo "- Cache cleared.<br />";
echo "<br /><strong>Finished Update!</strong>";
?>
</div>
<div class="bfoot center"><input type="submit" value="Continue" /></div>