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

// To be added (RC4 => RC5)


?>
</div>
<div class="bfoot center"><input type="submit" value="Continue" /></div>