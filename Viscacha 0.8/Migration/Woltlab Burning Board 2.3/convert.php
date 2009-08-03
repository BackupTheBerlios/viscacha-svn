<?php
/**
 * Konverter: WBB 2.3.* zu Viscacha 0.8 Beta 4 / RC1
 * Getestet mit WBB 2.3.3 und Viscacha 0.8 RC1
 *
 * Copyright (C) 2005-2007 MaMo Net, http://www.mamo-net.de
**/

error_reporting(E_ALL);

if (empty($_GET['action']) || $_GET['action'] < 2 || $_GET['action'] > 6) {
	$_GET['action'] = 1;
}
$install = true;

?>
<html>
<head>
	<title>Konvertierung einer WBB-Installation zu Viscacha</title>
	<style type="text/css">
	* {
		font-family: monospace;
		color: #000000;
	}
	a:hover {
		font-weight: bold;
	}
	body {
		background-color: #cccccc;
	}
	h1 {
		font-size: 14pt;
		text-align: center;
		padding: 0.5em;
		border-bottom: 1px dotted #000000;
		margin: 0px;
	}
	h3 {
		font-size: 10pt;
		padding: 0.5em;
		border-bottom: 1px dotted #000000;
		margin: 0px;
	}
	#container {
		font-size: 10pt;
		width: 60%;
		margin: 2em auto 2em auto;
		border: 3px double #000000;
		background-color: #ffffff;
	}
	pre {
		width: 100%;
	}
	#content {
		padding: 0.5em;
	}
	.continue {
		border-top: 1px dotted #000000;
		display: block;
		padding: 0.5em;
		text-align: center;
	}
	</style>
</head>
<body>
<div id="container">
<h1>Konvertierung: WBB 2.3.* -> Viscacha 0.8 Beta 4 / RC1</h1>
<h3>Status: Schritt <?php echo $_GET['action']; ?> von 6</h3>
<div id="content">
<?php

if (file_exists('data/config.inc.php')) {
	include('data/config.inc.php');
	if ($config['version'] != '0.8 Beta 4' && $config['version'] != '0.8 RC1') {
		$install = false;
		echo 'ERROR: Viscacha in Version 0.8 Beta 4 oder 0.8 RC1 benötigt. Es wurde Version '.$config['verion'].' gefunden.<br />';
	}
}
else {
	$install = false;
	echo 'ERROR: Datei data/config.inc.php des Viscacha nicht gefunden!<br />';
}

/////////// CONFIG START ///////////
/** CONFIG: Pfad zum WBB Hauptverzeichnis, ausgehen vom Viscacha-Verzeichnis (ohne Slash am Ende) **/
$wbbdir = '../wbb';
/** CONFIG: ID des Mitglieds (WBB-User-Basis), dass nach der Konvertierung Administrator sein soll **/
$adminid = 30;
/////////// CONFIG ENDE ///////////

if (!file_exists($wbbdir.'/acp/lib/config.inc.php')) {
	$install = false;
	echo 'ERROR: Das Verzeichnis des WBB scheint falsch angegeben zu sein!<br />';
}
else {
	include($wbbdir.'/acp/lib/config.inc.php');
}

if (!empty($config['database'])) {
	if ($sqldb != $config['database']) {
		$install = false;
		echo 'ERROR: Die Datenbank des Viscacha und des WBB sind nicht gleich!<br />';
	}
	else {
		if (file_exists('classes/function.frontend_init.php')) {
			define('TEMPSHOWLOG', true);
			include('classes/function.frontend_init.php');
		}
		else {
			$install = false;
			echo 'ERROR: Datei classes/function.frontend_init.php des Viscacha nicht gefunden!<br />';
		}
	}
}
else {
	$install = false;
	echo 'ERROR: Viscacha scheint noch nicht installiert worden zu sein.<br />';
}

if ($install) {
	$res = $db->query("SELECT * FROM bb{$n}_users WHERE userid = '{$adminid}'");
	if ($db->num_rows($res) != 1) {
		$install = false;
		echo 'ERROR: Kein User (baldiger Admin-User) mit der entsprechenden ID gefunden!<br />';
	}
}

// Übernahme von Themen-Abos (keine Foren-Abos)
if ($_GET['action'] == '6' && $install) {
	echo '<strong>Themen Abonnements</strong><br /><br />';
	$db->query('TRUNCATE TABLE `'.$config['dbprefix'].'abos`');
	$i = 0;

	$res = $db->query("SELECT * FROM bb{$n}_subscribethreads");
	while ($r = $db->fetch_assoc($res)) {
		$r = array_map('addslashes', $r);
		$db->query("INSERT INTO `{$config['dbprefix']}abos` (`mid` , `tid`) VALUES ({$r['userid']}, {$r['threadid']})") or print($db->error());
		$i++;
	}
	echo 'Abos eingefügt: '.$i.'!<br />Automatisierte Konvertierung fertiggestellt!<br />';
	echo '</div><a href="admin.php" class="continue">Gehe zur Viscacha Administration</a>';
}
// Übernahme von Foren und Kategorien (keine Präfixe)
elseif ($_GET['action'] == '5' && $install) {
	echo '<strong>Foren und Kategorien</strong><br /><br />';
	$db->query('TRUNCATE TABLE `'.$config['dbprefix'].'categories`');
	$db->query('TRUNCATE TABLE `'.$config['dbprefix'].'forums`');
	$ic = 0;
	$ib = 0;
	$cid = array();

	$res = $db->query("SELECT * FROM bb{$n}_boards WHERE isboard = '0' ORDER BY boardid");
	while ($r = $db->fetch_assoc($res)) {
		$r = array_map('addslashes', $r);
		$db->query("
			INSERT INTO `{$config['dbprefix']}categories` (id,  `name` , `description` , `parent`, `position` )
			VALUES ('{$r['boardid']}', '{$r['title']}', '{$r['description']}', '{$r['parentid']}', '{$r['boardorder']}');
		") or print($db->error());
		$ic++;
		$cid[$r['boardid']] = $r['parentid'];
	}
	$res = $db->query("SELECT MAX(boardid) as maxi FROM bb{$n}_boards");
	$auto = $db->fetch_assoc($res);
	$auto = $auto['maxi']+1;

	$res = $db->query("SELECT * FROM bb{$n}_boards WHERE isboard = '1' ORDER BY boardid");
	while ($r = $db->fetch_assoc($res)) {
		$r = array_map('addslashes', $r);
		$res2 = $db->query("SELECT isboard, title, description, boardorder FROM bb{$n}_boards WHERE boardid = '{$r['parentid']}'");
		$p = $db->fetch_assoc($res2);
		$last = $r['parentid'];
		if ($p['isboard'] == 1) {
			if (isset($cid[$r['parentid']])) {
				$last = $cid[$r['parentid']];
			}
			else {
				$p = array_map('addslashes', $p);
				$db->query("
					INSERT INTO `{$config['dbprefix']}categories` (id,  `name` , `description` , `parent`, `position` )
					VALUES ('{$auto}', '{$p['title']}', '{$p['description']}', '{$r['parentid']}', '0');
				") or print($db->error());
				$ic++;
				$auto++;
				$last = $db->insert_id();
				$cid[$r['parentid']] = $last;
			}
		}
		if (!empty($r['externalurl'])) {
			$opt = 're';
			$optvalue = $r['externalurl'];
		}
		elseif (!empty($r['password'])) {
			$opt = 'pw';
			$optvalue = $r['password'];
		}
		else {
			$opt = '';
			$optvalue = '';
		}
		// invisible und closed könnten falsch implementiert sein(?)
		$db->query("
			INSERT INTO `{$config['dbprefix']}forums` (
				`id` , `name` , `description` , `topics` , `replies` ,
				`parent` , `position` , `last_topic` , `count_posts` , `opt` , `optvalue` ,
				`forumzahl` , `topiczahl` , `prefix` , `invisible` , `readonly`)
			VALUES (
				'{$r['boardid']}' , '{$r['title']}', '{$r['description']}', '{$r['threadcount']}', '{$r['postcount']}',
				'{$last}', '{$r['boardorder']}', '{$r['lastthreadid']}', '{$r['countuserposts']}', '{$opt}', '{$optvalue}',
				'{$r['postsperpage']}', '{$r['threadsperpage']}', '0', '{$r['invisible']}', '{$r['closed']}'
			)
		") or print($db->error());
		$ib++;
	}
	echo 'Kategorien eingefügt: '.$ic.'! Foren eingefügt: '.$ib.'!<br /></div><a class="continue" href="?action=6">Weiter (Abos)</a>';
}
// Übernahme von Beiträgen und Themen (keine Umfragen, keine Präfixe)
elseif ($_GET['action'] == '4' && $install) {
	echo '<strong>Beiträge und Themen</strong><br /><br />';
    $db->query('TRUNCATE TABLE `'.$config['dbprefix'].'replies`');
    $db->query('TRUNCATE TABLE `'.$config['dbprefix'].'topics`');

    $result = $db->query('SELECT id, name FROM '.$config['dbprefix'].'user');
    $memberdata = array();
    while($row = $db->fetch_assoc()) {
        $memberdata[$row['id']] = $row['name'];
    }

    $ti = 0;
    $pi = 0;

	$tc = array();
	$pc = array();

	$res = $db->query("SELECT * FROM bb{$n}_threads");
	while ($t = $db->fetch_assoc($res)) {
		$t = array_map('addslashes', $t);
		$tc[$t['threadid']] = $t;
	}

	$res = $db->query("SELECT * FROM bb{$n}_posts ORDER BY threadid, postid");
	while ($p = $db->fetch_assoc($res)) {
		$p = array_map('addslashes', $p);
		if (!isset($pc[$p['threadid']]) || !is_array($pc[$p['threadid']])) {
			$pc[$p['threadid']] = array();
			$p['start'] = 1;
		}
		else {
			$p['start'] = 0;
		}
		$pc[$p['threadid']][$p['postid']] = $p;
	}

	foreach ($tc as $t) {
		$db->query("
		INSERT INTO `{$config['dbprefix']}topics` ( `id` , `board` , `topic` , `posts` , `name` ,
		`date` , `status` , `last` , `sticky` , `last_name`)
		VALUES (
		'{$t['threadid']}', '{$t['boardid']}', '{$t['topic']}', '{$t['replycount']}', '{$t['starterid']}',
		'{$t['starttime']}', '{$t['closed']}', '{$t['lastposttime']}', '{$t['important']}', '{$t['lastposter']}'
		);
		");
		$ti++;
		if (isset($pc[$t['threadid']])) {
			foreach ($pc[$t['threadid']] as $r) {
				$edit = '';
				if ($r['editcount'] > 0) {
					$edits = $r['editor']."\t".$r['edittime']."\t\n";
				}
				$r['message'] = convertBB($r['message']);
				$db->query("INSERT INTO `{$config['dbprefix']}replies` (
				`id` , `board` , `topic` , `topic_id` , `name` ,
				`comment` , `dosmileys` , `date` , `edit` , `tstart`, `ip` )
				VALUES (
				'{$r['postid']}', '{$t['boardid']}', '{$r['posttopic']}', '{$t['threadid']}', '{$r['userid']}',
				'{$r['message']}', '{$r['allowsmilies']}', '{$r['posttime']}', '{$edit}', '{$r['start']}', '{$r['ipaddress']}'
				);");
				$pi++;
			}
		}
	}

	echo 'Antworten eingefügt: '.$pi.'!<br />Themen eingefügt: '.$ti.'!<br /></div><a class="continue" href="?action=5">Weiter (Foren & Kategorien)</a>';
}
// Übernahme von PNs (keine Attachments, keine Verzeichnisse (nur In- und Outbox), keine Lesebestätigungen)
elseif ($_GET['action'] == '3' && $install) {
	echo '<strong>Private Nachrichten</strong><br /><br />';
    $i = 0;
    $db->query('TRUNCATE TABLE `'.$config['dbprefix'].'pm`');
    $res = $db->query("SELECT * FROM bb{$n}_privatemessage AS p LEFT JOIN bb{$n}_privatemessagereceipts AS r ON p.privatemessageid = r.privatemessageid");
    while($p = $db->fetch_assoc($res)) {
    	$p = array_map('addslashes', $p);
		$p['message'] = convertBB($p['message']);
		if ($p['view'] > 0) {
			$p['view'] = 1;
		}
		if ($p['inoutbox'] == 1) {
			$db->query("
			INSERT INTO `{$config['dbprefix']}pm`
			(`topic` , `pm_from` , `pm_to` , `comment` , `date` , `status`, `dir` )
			VALUES (
			'{$p['subject']}', '{$p['recipientid']}', '{$p['senderid']}', '{$p['message']}', '{$p['sendtime']}', '1', '2'
			)") or die($db->error());
		}
		$db->query("
		INSERT INTO `{$config['dbprefix']}pm`
		(`topic` , `pm_from` , `pm_to` , `comment` , `date` , `status`, `dir` )
		VALUES (
		'{$p['subject']}', '{$p['senderid']}', '{$p['recipientid']}', '{$p['message']}', '{$p['sendtime']}', '{$p['view']}', '1'
		)") or die($db->error());
		$i++;
	}
	echo 'PMs eingefügt: '.$i.'!<br /></div><a href="?action=4" class="continue">Weiter (Beiträge)</a>';
}
// Übernahme von Mitgliedern (inkl.Avatare, keine eigenen Profilfelder außer Standard-Feld Wohnort, keine Übernahme von Benutzerrechten)
elseif ($_GET['action'] == '2' && $install) {
	echo '<strong>Mitglieder</strong><br /><br />';
    $i = 0;
    $db->query('TRUNCATE TABLE `'.$config['dbprefix'].'user`');
	$res = $db->query("
	SELECT *
	FROM bb{$n}_users AS u
		LEFT JOIN bb{$n}_avatars AS a ON u.avatarid = a.avatarid
		LEFT JOIN bb{$n}_userfields AS f ON u.userid = f.userid
	ORDER BY u.userid
	");
	while ($u = $db->fetch_assoc($res)) {
		$u = array_map('addslashes', $u);
		$u['signature'] = convertBB($u['signature']);
		if (empty($u['icq'])) {
			$u['icq'] = 0;
		}
        if ($u['gender'] == 1) {
            $u['gender'] = 'm';
        }
        elseif ($u['gender'] == 2) {
            $u['gender'] = 'w';
        }
        else {
            $u['gender'] = '';
        }
        if ($u['avatarid'] > 0) {
        	$oldfile = $wbbdir.'/images/avatars/avatar-'.$u['avatarid'];
        	$ext = '';
        	if (file_exists($oldfile.'.png')) {
        		$ext = '.png';
        	}
        	elseif (file_exists($oldfile.'.gif')) {
        		$ext = '.gif';
        	}
        	elseif (file_exists($oldfile.'.jpg')) {
        		$ext = '.jpg';
        	}
        	if (!empty($ext)) {
	        	$oldfile .= $ext;
	        	$newfile = 'uploads/pics/'.$u['userid'].$ext;
	            $u['avatar'] = $config['furl'].'/'.$newfile;
	            @copy($oldfile, $newfile);
            }
            else {
            	$u['avatar'] = '';
            }
        }
        else {
        	$u['avatar'] = '';
        }
        if (strlen($u['activation']) > 1) {
            $u['activation'] = '01';
        }
        else {
            $u['activation'] = '11';
        }
        if (empty($u['birthday'])) {
        	$u['birthday'] = '0000-00-00';
        }
        $u['groupids'] = '';
        if ($u['userid'] == $adminid) {
        	$u['groupids'] = 1;
        }
        $db->query("
        INSERT INTO `{$config['dbprefix']}user` (
        `id` , `name` , `pw` , `mail` , `regdate` , `hp` , `signature` , `location` ,`gender` ,
        `birthday` , `pic` , `lastvisit` , 	`icq` , `yahoo` , `aol` , `msn` , `groups` ,
        `opt_hidemail` , `opt_newsletter` , `confirm`, `posts`)
        VALUES (
         '{$u['userid']}',
         '{$u['username']}',
         '{$u['password']}',
         '{$u['email']}',
         '{$u['regdate']}',
         '{$u['homepage']}',
         '{$u['signature']}',
         '{$u['field1']}',
         '{$u['gender']}',
         '{$u['birthday']}',
         '{$u['avatar']}',
         '{$u['lastactivity']}',
         '{$u['icq']}',
         '{$u['yim']}',
         '{$u['aim']}',
         '{$u['msn']}',
         '{$u['groupids']}',
         '2',
         '{$u['admincanemail']}',
         '{$u['activation']}',
         '{$u['userposts']}'
		 )", __LINE__, __FILE__);
		$i++;
	}
	echo 'Mitglieder eingefügt: '.$i.'!<br /></div><a class="continue" href="?action=3">Weiter (PMs)</a>';
}
elseif ($install) {
	?>
<strong>Readme</strong>
<pre>
Konverter: WBB 2.3.* zu Viscacha 0.8 Beta 4 / RC1
Getestet mit WBB 2.3.3 und Viscacha 0.8 RC1

Copyright (C) 2005-2007 MaMo Net, http://www.mamo-net.de

Vor der Konvertierung Viscacha komplett installieren. Die WBB-Datenbank und die Viscacha-
Datenbank m&uuml;ssen die selben seien! Der Administratoraccount wird jedoch wieder gelöscht,
der aus dem WBB steht später zur Verfügung. Bei der Konvertierung bleiben die alten WBB-Daten
bestehen und werden nicht ver&auml;ndert. Falls ein Fehler auftritt ist der Konverter so
programmiert, dass die Installation nochmal begonnen werden kann oder der Schritt wiederholt
werden kann. Nach dem Konvertieren sollte der Cache des Viscacha einmal komplett geleert werden!

Um den Konverter zu konfigurieren &auml;ndern Sie in dieser Datei:
 - die Variable $wbbdir in Zeile 86 korrekt (s.u.) und
 - die Variable $adminid in Zeile 88 korrekt (s.u.).

Der Konbverter übernimmt:
 - PMs (keine Attachments, keine Verzeichnisse (nur In- und Outbox), keine Lesebestätigungen)
 - Mitglieder und Avatare (keine eigenen Profilfelder außer Standard-Feld Wohnort, keine Übernahme
                           von Benutzerrechten)
 - Themen-Abos (keine Foren-Abos)
 - Beiträge und Themen (keine Umfragen, keine Präfixe, nur letzte Editierung eines Beitrags)
 - Foren und Kategorien (keine Präfixe)

Hinweis zum Konverter bzgl. Vollst&auml;ndigkeit:
Ich bin noch nicht ganz durch das WBB-Rechtesystem durchgestiegen. Falls jemand das
übernehmen m&ouml;chte bitte ich um eine E-Mail an webmaster@mamo-net.de Derzeit wird nur
ein Administrator nach der Konfiguration unterhalb übernommen. Alle anderen User sind
normale Mitglieder. Die Rechte müssen leider alle einzeln zurückverteilt werden.
Es werden au&szlig;erdem noch keine Umfragen und P&aumlr&auml;fixe &uuml;bernommen,
dies wird aber bald noch implementiert. Alle anderen nicht &uuml;bernommenen Daten
k&ouml;nnen entweder nicht &uuml;bernommen werden weil sie nicht kompatibel sind oder
weil das Viscacha diese Features nicht unterst&uuml;tzt.
</pre><br />
</div>
<a class="continue" href="?action=2">Starte Konvertierung (Mitglieder)</a>
	<?php
}
else {
	?>
</div>
<a class="continue" href="?action=1">Seite neu laden</a>
	<?php
}
if (isset($db) && is_resource($db)) {
	$db->close();
}
?>
</div>
</body>
</html>
<?php
// Functions
function convertBB($t) {
    $t = str_ireplace('[BR]', "\n", $t);
    $t = str_ireplace('[P]', "\n\n", $t);
    $t = str_ireplace('[left]', "[align=left]", $t);
    $t = str_ireplace('[/left]', "[/align]", $t);
    $t = str_ireplace('[center]', "[align=center]", $t);
    $t = str_ireplace('[/center]', "[/align]", $t);
    $t = str_ireplace('[php]', "[code=php]", $t);
    $t = str_ireplace('[/php]', "[/code]", $t);
    $t = str_ireplace('[size=6]', "[size=large]", $t);
    $t = str_ireplace('[size=5]', "[size=large]", $t);
    $t = str_ireplace('[size=4]', "[size=large]", $t);
    $t = str_ireplace('[size=2]', "[size=small]", $t);
    $t = str_ireplace('[size=1]', "[size=small]", $t);
    return $t;
}
?>