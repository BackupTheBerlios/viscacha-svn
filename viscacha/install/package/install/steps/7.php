<?php
require('../data/config.inc.php');
require_once('../classes/class.filesystem.php');
$filesystem = new filesystem($config['ftp_server'], $config['ftp_user'], $config['ftp_pw'], $config['ftp_port']);
$filesystem->set_wd($config['ftp_path']);
if (isset($_REQUEST['save']) && $_REQUEST['save'] == 1) {
	include('../classes/class.phpconfig.php');
	if (isset($_REQUEST['host'])) {
		$config['host'] = trim($_REQUEST['host']);
	}
	if (isset($_REQUEST['dbuser'])) {
		$config['dbuser'] = trim($_REQUEST['dbuser']);
	}
	if (isset($_REQUEST['dbpw'])) {
		$config['dbpw'] = trim($_REQUEST['dbpw']);
	}
	if (isset($_REQUEST['database'])) {
		$config['database'] = trim($_REQUEST['database']);
	}
	if (isset($_REQUEST['pconnect'])) {
		$config['pconnect'] = $_REQUEST['pconnect'];
	}
	if (isset($_REQUEST['dbprefix'])) {
		$config['dbprefix'] = trim($_REQUEST['dbprefix']);
	}
	if (isset($_REQUEST['dbsystem'])) {
		$config['dbsystem'] = $_REQUEST['dbsystem'];
	}
	$c = new manageconfig();
	$c->getdata('../data/config.inc.php');
	$c->updateconfig('host',str);
	$c->updateconfig('dbuser',str);
	$c->updateconfig('dbpw',str);
	$c->updateconfig('database',str);
	$c->updateconfig('pconnect',int);
	$c->updateconfig('dbprefix',str);
	$c->updateconfig('dbsystem',str);
	$c->savedata();
?>
<div class="bfoot center">Database Settings saved!</div>
<?php
}
require('../data/config.inc.php');
require_once('../classes/database/'.$config['dbsystem'].'.inc.php');
$db = new DB($config['host'], $config['dbuser'], $config['dbpw'], $config['database'], $config['pconnect'], false, $config['dbprefix']);
$db->pre = $db->prefix();
$db->connect(false);
if (!$db->conn) {
	?>
<div class="bbody">Could not connect to database! Pleasy try again later or check the database settings!</div>
<div class="bfoot center"><a class="submit" href="index.php?step=<?php echo $step-1; ?>">Go back</a> <a class="submit" href="index.php?step=<?php echo $step; ?>">Refresh</a></div>
	<?php
}
else {
	if (!$db->select_db()) {
		?>
<div class="bbody">Could not find database <em><?php echo $db->getcfg('database'); ?></em>! Pleasy create a new database with this name or choose another database!</div>
<div class="bfoot center"><a class="submit" href="index.php?step=<?php echo $step-1; ?>">Go back</a> <a class="submit" href="index.php?step=<?php echo $step; ?>">Refresh</a></div>
		<?php
	}
	else {
		?>
<div class="bbody">
	<input type="hidden" name="save" value="1" />
	<table class="tables">
	<tr>
		<td width="40%"><strong>Table</strong></td>
		<td width="10%"><strong>Exists</strong></td>
		<td width="10%"><strong>Entries</strong></td>
		<td width="40%"><strong>Action</strong></td>
	</tr>
	<?php
	$path = 'package/'.$package.'/db/';
	$tables = $db->list_tables();
	$dh = opendir($path);
	while (($file = readdir($dh)) !== false) {
		$info = pathinfo($path.$file);
		if ($info['extension'] == 'sql') {
			$basename = substr($info['basename'], 0, -(strlen($info['extension']) + ($info['extension'] == '' ? 0 : 1)));;
			$t = $db->pre.$basename;
			unset($counter);
			$select = array();
			if (in_array($t, $tables)) {
				$exists = '<span class="hl_false">Yes</span>';
				$result = $db->query('SELECT COUNT(*) AS c FROM '.$t);
				$counter = $db->fetch_assoc($result);
				$entries = $counter['c'];
				$select[] = '<option value="3" selected="selected">Tabelle l�schen + Neu erstellen</option>';
				$select[] = '<option value="2">Tabelle nicht �ndern</option>';
				if ($counter['c'] > 0) {
					$select[] = '<option value="1">Tabelle leeren</option>';
				}
			}
			else {
				$exists = '<span class="hl_true">No</span>';
				$entries = '-';
				$select[] = '<option value="0" selected="selected">Tabelle neu erstellen</option>';
			}
		?>
	<tr>
		<td><?php echo $db->pre; ?><strong><?php echo $basename; ?></strong></td>
		<td class="center"><?php echo $exists; ?></td>
		<td class="textright"><?php echo $entries; ?></td>
		<td>
		<select size="<?php echo count($select); ?>" name="action[<?php echo $basename; ?>]">
<?php echo implode("\n", $select); ?>
		</select>
		</td>
	</tr>
		<?php
		}
	}
	closedir($dh);
	?>
	</table>
</div>
<div class="bfoot center"><input type="submit" value="Continue" /></div>
		<?php
	}
}
$db->close();
?>
