<div class="bbody">
Preparing update...<br />
<?php
require('../data/config.inc.php');
require_once('lib/function.variables.php');
require_once('../classes/class.phpconfig.php');

if (!class_exists('filesystem')) {
	require_once('../classes/class.filesystem.php');
	$filesystem = new filesystem($config['ftp_server'], $config['ftp_user'], $config['ftp_pw'], $config['ftp_port']);
	$filesystem->set_wd($config['ftp_path']);
}
if (!class_exists('DB')) {
	require_once('../classes/database/'.$config['dbsystem'].'.inc.php');
	$db = new DB($config['host'], $config['dbuser'], $config['dbpw'], $config['database'], $config['pconnect'], true, $config['dbprefix']);
	$db->pre = $db->prefix();
	$db->errlogfile = '../'.$db->errlogfile;
}

//  ToDo

?>
Finished Update!
</div>
<div class="bfoot center"><input type="submit" value="Continue" /></div>