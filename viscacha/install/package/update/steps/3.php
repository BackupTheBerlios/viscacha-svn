<div class="bbody">
<?php
include('../data/config.inc.php');
require_once('lib/function.variables.php');
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

include('../classes/class.phpconfig.php');
$c = new manageconfig();
$c->getdata('../data/config.inc.php');
$c->updateconfig('version', str, VISCACHA_VERSION);
$c->savedata();

// ...

$filesystem->unlink('../admin/form.php');
$filesystem->unlink('../templates/newsfeed.js');

rmdirr('../classes/magpie_rss/extlib/');

$filesystem->mkdir('../cache/modules/', 0777);

$filesystem->file_put_contents('../data/errlog_php.inc.php', '');

$db->query("ALTER TABLE `{$db->pre}abos` CHANGE `type` `type` ENUM( '', 'd', 'w', 'f' ) NOT NULL", __LINE__, __FILE__,false);
$result = $db->query("SELECT mid, tid FROM {$db->pre}fav", __LINE__, __FILE__,false);
while ($row = $db->fetch_assoc($result)) {
	$db->query("INSERT INTO `{$db->pre}abos` (`mid`, `tid`, `type`) VALUES ('{$row['mid']}', '{$row['tid']}', 'f')", __LINE__, __FILE__,false);
}
$db->query("DROP TABLE `{$db->pre}fav`", __LINE__, __FILE__,false);

$db->query("ALTER TABLE `{$db->pre}bbcode` CHANGE `type` `type` ENUM('censor','word','replace') NOT NULL DEFAULT 'word'", __LINE__, __FILE__,false);
$db->query("ALTER TABLE `{$db->pre}bbcode` RENAME `{$db->pre}textparser`", __LINE__, __FILE__,false);
$db->query("DROP TABLE `{$db->pre}bbcode`", __LINE__, __FILE__,false);

$db->query("ALTER TABLE `{$db->pre}cat` ADD `forumzahl` tinyint(3) unsigned NOT NULL default '0' AFTER `optvalue`", __LINE__, __FILE__,false);
$db->query("ALTER TABLE `{$db->pre}cat` ADD `topiczahl` tinyint(3) unsigned NOT NULL default '0' AFTER `forumzahl`", __LINE__, __FILE__,false);
$db->query("ALTER TABLE `{$db->pre}cat` ADD `invisible` enum('0','1') NOT NULL default '0' AFTER `prefix`", __LINE__, __FILE__,false);
$db->query("ALTER TABLE `{$db->pre}cat` ADD INDEX ( `last_topic` )", __LINE__, __FILE__,false);

$db->query("ALTER TABLE `{$db->pre}designs` DROP COLUMN `smileyfolder`", __LINE__, __FILE__,false);
$db->query("ALTER TABLE `{$db->pre}designs` DROP COLUMN `smileypath`", __LINE__, __FILE__,false);

$result = $db->query("SELECT * FROM `{$db->pre}menu`", __LINE__, __FILE__,false);
$cache = array();
while ($row = $db->fetch_assoc($result)) {
	$cache[] = $row;
}
$db->query("ALTER TABLE `{$db->pre}menu` DROP COLUMN `position`", __LINE__, __FILE__,false);
$db->query("ALTER TABLE `{$db->pre}menu` CHANGE `module` `module` int(10) NOT NULL default '0'", __LINE__, __FILE__,false);
foreach ($cache as $row) {
	if ($row['position'] != 'navigation') {
		$db->query("DELETE FROM `{$db->pre}menu` WHERE id = '{$row['id']}'", __LINE__, __FILE__,false);
	}
	else {
		if ($row['link'] == '1') {
			$db->query("UPDATE `{$db->pre}menu` SET `module` = '5' WHERE id = '{$row['id']}'", __LINE__, __FILE__,false);
		}
		elseif ($row['link'] == '3') {
			$db->query("UPDATE `{$db->pre}menu` SET `module` = '19' WHERE id = '{$row['id']}'", __LINE__, __FILE__,false);
		}
		else {
			$db->query("UPDATE `{$db->pre}menu` SET `module` = '0' WHERE id = '{$row['id']}'", __LINE__, __FILE__,false);
		}
	}
}

$db->query("ALTER TABLE `{$db->pre}pm` DROP INDEX ( `date` )", __LINE__, __FILE__,false);
$db->query("ALTER TABLE `{$db->pre}pm` ADD INDEX ( `pm_to` )", __LINE__, __FILE__,false);

$db->query("ALTER TABLE `{$db->pre}replies` DROP INDEX ( `name` )", __LINE__, __FILE__,false);
$db->query("ALTER TABLE `{$db->pre}replies` ADD `guest` enum('0','1') NOT NULL default '0'", __LINE__, __FILE__,false);
$db->query("ALTER TABLE `{$db->pre}replies` ADD `ip` varchar(20) NOT NULL", __LINE__, __FILE__,false);
$result = $db->query("SELECT * FROM `{$db->pre}replies`", __LINE__, __FILE__,false);
while ($row = $db->fetch_assoc($result)) {
	if (!empty($row['email'])) {
		$db->query("UPDATE `{$db->pre}replies` SET `guest` = '1' WHERE id = '{$row['id']}'", __LINE__, __FILE__,false);
	}
}

$db->query("ALTER TABLE `{$db->pre}session` ADD `is_bot` mediumint(6) unsigned NOT NULL default '0'", __LINE__, __FILE__,false);
$db->query("ALTER TABLE `{$db->pre}session` ADD INDEX ( `sid` )", __LINE__, __FILE__,false);

$db->query("DROP TABLE `{$db->pre}settings`", __LINE__, __FILE__,false);

$db->query("DROP TABLE `{$db->pre}spider`", __LINE__, __FILE__,false);

$db->query("ALTER TABLE `{$db->pre}topics` DROP INDEX ( `date` )", __LINE__, __FILE__,false);
$db->query("ALTER TABLE `{$db->pre}topics` DROP INDEX ( `mark` )", __LINE__, __FILE__,false);
$db->query("ALTER TABLE `{$db->pre}topics` ADD INDEX ( `board` )", __LINE__, __FILE__,false);

$db->query("ALTER TABLE `{$db->pre}user` ADD `skype` varchar(128) NOT NULL default ''", __LINE__, __FILE__,false);

$db->query("ALTER TABLE `{$db->pre}votes` DROP INDEX ( `mid` )", __LINE__, __FILE__,false);

$tables = array('bbcode', 'language', 'packages', 'plugins', 'postratings', 'profilefields', 'settings', 'settings_groups', 'spider', 'textparser', 'userfields');
foreach ($tables as $table) {
	$file = 'package/'.$package.'/db/'.$table.'.sql';
	$sql = implode('', file($file));
	$sql = str_replace('{:=DBPREFIX=:}', $db->pre, $sql);
	$db->multi_query($sql, false);
}
?>
</div>
<div class="bfoot center"><input type="submit" value="Continue" /></div>