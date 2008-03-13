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
$c->savedata();

echo "- Configuration updated.<br />";

// Old files
rmdirr('../classes/spellchecker/dict');
echo "- Old files deleted.<br />";

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
	'admin/forums' => array(
		'language' => array(
			'admin_forum_bbcode_html' => 'BB-Code is allowed; HTML is not allowed!'
		),
		'language_de' => array(
			'admin_forum_bbcode_html' => 'BB-Code ist erlaubt, HTML ist nicht erlaubt!',
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
			'upload_intro1b' => 'Max. image size: {@config->tpcwidth} x {@config->tpcheight} px'
		),
		'language_de' => array(
			'upload_intro1' => 'Um an diesen Beitrag eine Datei anzufügen, klicken Sie auf die "Durchsuchen" Schaltfläche und wählen Sie eine Datei aus. Klicken Sie dann auf "Senden", um den Vorgang abzuschließen.<br /><br />Erlaubte Dateitypen: {$filetypes}<br />Maximale Dateigröße: {$filesize}',
			'upload_intro1b' => 'Maxmimale Bildabmessungen: {@config->tpcwidth} x {@config->tpcheight} Pixel'
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