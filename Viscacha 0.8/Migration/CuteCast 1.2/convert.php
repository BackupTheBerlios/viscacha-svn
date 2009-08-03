<html>
<head>
	<title>CuteCast 1.2 => Viscacha 0.8 Konverter</title>
	<style>
	* {
		font-family: Arial, Verdana, sans-serif;
		font-size: 10pt;
	}
	h1 {
		font-size: 16pt;
	}
	body {
		margin: 10% 25% 2% 25%;
	}
	button, a {
		border: 0;
		background-color: white;
		color: blue;
		text-decoration: underline;
	}
	input {
		border: 1px solid #cccccc;
		color: #666666;
	}
	input:hover {
		border: 1px solid maroon;
		color: black;
	}
	#copyright {
		font-size: 9pt;
		margin-top: 1em;
		text-align: center;
	}
	</style>
</head>
<body>
<h1>Konvertierung CuteCast 1.2 zu Viscacha 0.8</h1>
<?php
function convertBB($t) {
    $t = str_ireplace('[BR]', "\n", $t);
    $t = str_ireplace('[P]', "\n\n", $t);
    $t = str_ireplace('[left]', "[align=left]", $t);
    $t = str_ireplace('[/left]', "[/align]", $t);
    $t = str_ireplace('[center]', "[align=center]", $t);
    $t = str_ireplace('[/center]', "[/align]", $t);
    $t = str_ireplace('[right]', "[align=right]", $t);
    $t = str_ireplace('[/right]', "[/align]", $t);
    $t = str_ireplace('[size=6]', "[size=large]", $t);
    $t = str_ireplace('[size=5]', "[size=large]", $t);
    $t = str_ireplace('[size=4]', "[size=large]", $t);
    $t = str_ireplace('[size=2]', "[size=small]", $t);
    $t = str_ireplace('[size=1]', "[size=small]", $t);
    return $t;
}

$error = false;
$_REQUEST['path'] = realpath($_REQUEST['path']);
if (!empty($_REQUEST['path'])) {
	$conv_path = $_REQUEST['path'];
}
else {
	$conv_path = '';
	if (!empty($_REQUEST['action'])) {
		$error = true;
	}
	$_REQUEST['action'] = '';
}

if (!empty($_REQUEST['action'])) {
	DEFINE('SCRIPTNAME', 'admin');
	DEFINE('TEMPSHOWLOG', 1);
	DEFINE('VISCACHA_CORE', 1);
	include('data/config.inc.php');
	include('classes/function.viscacha_frontend.php');
}
else {
	$_REQUEST['action'] = null;
}

$path = $conv_path;

if ($_REQUEST['action'] == 'smileys') {
	$i = 0;
	if (file_exists($path.'/txt/emoticons.txt')) {
		$smileys = file($path.'/txt/emoticons.txt');
		$spath = '{folder}/';
		foreach ($smileys as $s) {
		    $s = explode("\t", $s);
		    $db->query("INSERT INTO `{$db->pre}smileys` ( `search` , `replace` , `desc`) VALUES ( '{$s[0]}', '{$spath}{$s[2]}', '{$s[1]}')");
		    $i++;
		}
		$message = "Es wurden zur Konvertierung der Smileys {$i} Zeilen abarbeitet. Um die Konvertierung der Smileys abzuschließen kopieren Sie bitte alle Smileys aus dem alten CuteCast-Smileyverzeichnis ('emoticons') in das Viscacha-Smileyverzeichnis ('images/smileys').";
	}
	else {
		$message = "Die Datei mit den Smiley-Informationen wurde nicht gefunden. Dieser Schritt kann übersprungen werden.";
	}
	?>
	<p><?php echo $message; ?></p>
	<p>Derzeit führen Sie Schritt 2 von 6 aus:
	<ol type="I">
	<li><s>Einführung / Vorbereitung</s></li>
	<li><strong>Konvertierung der Smileys</strong></li>
	<li>Konvertierung der Mitgliederdaten</li>
	<li>Konvertierung der Kategorien und Foren</li>
	<li>Konvertierung der Beiträge und Themen</li>
	<li>Konvertierung der Themen-Abonnements</li>
	</ol>
	</p>
	<p><a href="?action=members&amp;path=<?php echo rawurlencode($path); ?>">Nächsten Schritt ausführen (Mitglieder)</a></p>
	<?php
}
elseif ($_REQUEST['action'] == 'members') { // Avatare fehlen
    $i = 0;
    $db->query('TRUNCATE TABLE `'.$db->pre.'user`');
    chdir($path.'/members/');
    foreach (glob("*.user") as $f) {
        $data = file($f);
        $data = array_map("trim", $data);
        $u = array();
        foreach ($data as $d) {
            $d = explode('=', $d);
            $u[$d[0]] = $db->escape_string($d[1]);
        }
        $u['password'] = md5($u['password']);
        $u['signature'] = convertBB($u['signature']);
        if (empty($u['icq'])) {
            $u['icq'] = "0";
        }
        $groups = '';
        if ($u['level'] == 'GEN') {
            $groups = '1';
        }
        elseif ($u['level'] == 'LTG') {
            $groups = '2';
        }
        else {
        	$groups = null;
        }
        if (empty($u['homepage'])) {
            $u['homepage'] = '';
        }
        elseif (strlen($u['homepage']) < 8) {
            $u['homepage'] = '';
        }

        $db->query("
        INSERT INTO `{$db->pre}user` (`name` , `pw` , `mail` , `regdate` , `hp` , `signature` , `location` , `lastvisit` , 	`icq` , `yahoo` , `aol` , `msn` , `groups` , `posts` , `confirm`)
        VALUES (
         '{$u['username']}',
         '{$u['password']}',
         '{$u['email']}',
         '{$u['date registered']}',
         '{$u['homepage']}',
         '{$u['signature']}',
         '{$u['location']}',
         '{$u['last post']}',
         '{$u['icq']}',
         '{$u['ym']}',
         '{$u['aim']}',
         '{$u['msn']}',
         '{$groups}',
         '{$u['total posts']}',
         '11')");
        $i++;
    }
    ?>
	<p>Es wurden die Daten von <?php echo $i; ?> Mitgliedern konvertiert.</p>
	<p>Derzeit führen Sie Schritt 3 von 6 aus:
	<ol type="I">
	<li><s>Einführung / Vorbereitung</s></li>
	<li><s>Konvertierung der Smileys</s></li>
	<li><strong>Konvertierung der Mitgliederdaten</strong></li>
	<li>Konvertierung der Kategorien und Foren</li>
	<li>Konvertierung der Beiträge und Themen</li>
	<li>Konvertierung der Themen-Abonnements</li>
	</ol>
	</p>
	<p><a href="?action=forums&amp;path=<?php echo rawurlencode($path); ?>">Nächsten Schritt ausführen (Kategorien und Foren)</a></p>
	<?php
}
elseif ($_REQUEST['action'] == 'forums') {
	$db->query("TRUNCATE TABLE `{$db->pre}categories`");
	$db->query("TRUNCATE TABLE `{$db->pre}forums`");

	$forums = file($path.'/forums.db');
	array_shift($forums);
	$forums = array_map("trim", $forums);

    $bd = array();
    $boards = file($path.'/needata.nda');
    $boards = array_map("trim", $boards);
    foreach ($boards as $b) {
    	if (preg_match("/\d+=\d+\t\d+\t\*[\d\s]*/i", $b)) {
	        $bid = explode('=', $b);
	        $id = $bid[0];
	        $bid = explode('*', $bid[1]);
	        $lasts = explode(" ", $bid[1]);
	        $last = $lasts[0];
	        $bid = explode("\t", $bid[0]);
	        $bd[$id] = array('posts' => $bid[0], 'topics' => $bid[1], 'last' => $last);
    	}
    }

	$cats = array();
	$cat_pos = 0;
	$forum_pos = array();
	$fi = 0;

	foreach ($forums as $f) {
		list($id, $status, $cat, $name, $desc) = explode("\t", $f, 5);

		$name = $gpc->save_str($name);
		$cat = $gpc->save_str($cat);
		$desc = $gpc->save_str($desc);

		if (!isset($cats[$cat])) {
			$db->query("
			INSERT INTO `{$db->pre}categories`
				( `name` , `description` , `parent` , `position` )
			VALUES
				( '{$cat}', '', '0', '{$cat_pos}')
			");
			$cid = $db->insert_id();
			$cats[$cat] = $cid;
			$cat_pos++;
			$forum_pos[$cid] = 0;
		}
		else {
			$cid = $cats[$cat];
			$forum_pos[$cid]++;
		}

		if ($status == "closed") {
			$ro = 1;
		}
		else {
			$ro = 0;
		}

		$db->query("
		INSERT INTO `{$db->pre}forums`
			( `id` , `name` , `description` , `topics` , `replies` , `parent` , `position` , `last_topic` , `readonly` )
		VALUES
			({$id} , '{$name}', '{$desc}', '{$bd[$id]['topics']}', '{$bd[$id]['posts']}', '{$cid}', '{$forum_pos[$cid]}', '{$bd[$id]['last']}', '{$ro}')
		");
		$fi++;
	}
	?>
	<p>Es wurden <?php echo $cat_pos; ?> Kategorien und <?php echo $fi; ?> Foren konvertiert.</p>
	<p>Derzeit führen Sie Schritt 4 von 6 aus:
	<ol type="I">
	<li><s>Einführung / Vorbereitung</s></li>
	<li><s>Konvertierung der Smileys</s></li>
	<li><s>Konvertierung der Mitgliederdaten</s></li>
	<li><strong>Konvertierung der Kategorien und Fore</strong>n</li>
	<li>Konvertierung der Beiträge und Themen</li>
	<li>Konvertierung der Themen-Abonnements</li>
	</ol>
	</p>
	<p><a href="?action=posts&amp;path=<?php echo rawurlencode($path); ?>">Nächsten Schritt ausführen (Beiträge und Themen)</a></p>
	<?php
}
elseif ($_REQUEST['action'] == 'abos') {
    $db->query('TRUNCATE TABLE `'.$db->pre.'abos`');
    $i = 0;
	$g = 0;

    $result = $db->query('SELECT id, name FROM '.$db->pre.'user');
    $memberdata = array();
    while($row = $db->fetch_assoc()) {
        $memberdata[$row['id']] = $row['name'];
    }

    chdir($path.'/subscribe/');
    foreach (glob("*.db") as $f) {
        $id = basename($f);
        $id = str_replace('.db', '', $id);
        $id = intval($id);
        $data = file($f);
        $data = array_map("trim", $data);

        foreach ($data as $r) {

            $r = explode("\t", $r);

            $user = array_search($r[0], $memberdata);
            if (empty($user)) {
                continue;
            }

            $db->query("INSERT INTO `{$db->pre}abos` (`mid` , `tid`) VALUES ($user, $id)");
            $i++;
        }
		$g++;
    }

    // Cache löschen
	$cachedir = 'cache/';
	if ($dh = @opendir($dir)) {
		while (($file = readdir($dh)) !== false) {
			if (strpos($file, '.inc.php') !== false) {
				$fileTrim = str_replace('.inc.php', '', $file);
				$filesystem->unlink($cachedir.$file);
			}
	    }
		closedir($dh);
	}
    ?>
	<p>Es wurden für <?php echo $g; ?> Themen <?php echo $i; ?> Themen-Abonnements konvertiert.</p>
	<p>Derzeit führen Sie Schritt 6 von 6 aus:
	<ol type="I">
	<li><s>Einführung / Vorbereitung</s></li>
	<li><s>Konvertierung der Smileys</s></li>
	<li><s>Konvertierung der Mitgliederdaten</s></li>
	<li><s>Konvertierung der Kategorien und Foren</s></li>
	<li><s>Konvertierung der Beiträge und Themen</s></li>
	<li><strong>Konvertierung der Themen-Abonnements</strong></li>
	</ol>
	</p>
	<p>Die automatische Konvertierung wurde abgeschlossen. Viel Spaß nun mit Ihrem Viscacha!<br /><a href="admin.php">Weiter in das Admin Control Panel Ihrer Viscacha-Installation.</a></p>
	<?php
}
elseif ($_REQUEST['action'] == 'posts') {

    $db->query("TRUNCATE TABLE `{$db->pre}replies`");
    $db->query("TRUNCATE TABLE `{$db->pre}topics`");

    $t_i = 0;
    $p_i = 0;
    $bd = array();
    $boards = file($path.'/needata.nda');
    $boards = array_map("trim", $boards);
    foreach ($boards as $b) {
    	if (preg_match("/\d+=\d+\t\d+\t\*[\d\s]*/i", $b)) {
	        $bid = explode('=', $b);
	        $btids = explode('*', $b);
	        $btids = explode(' ', $btids[1]);
	        foreach ($btids as $btid) {
	            $bd[$btid] = $bid[0];
	        }
    	}
    }

    $result = $db->query("SELECT id, name FROM {$db->pre}user");
    $memberdata = array();
    while($row = $db->fetch_assoc()) {
        $memberdata[$row['id']] = $row['name'];
    }

    chdir($path.'/data/');
    foreach (glob("*.db") as $f) {
        $id = basename($f);
        $id = str_replace('.db', '', $id);
        $id = intval($id);
        $data = file($f);
        $data = array_map("trim", $data);

        // Topics
        $t = explode("\t", $data[0]);
        if ($t[0] == 'close') {
            $status = '1';
        }
        else {
            $status = '0';
        }
        if (isset($bd[$id])) {
            $board = $bd[$id];
        }
        else {
            continue;
        }
        $topic = $t[2];

        $topic = $db->escape_string($topic);
        $t[2] = $db->escape_string($t[2]);
        $posts = count($data)-2;
        $p1 = explode("\t", $data[1]);
        $pl = explode("\t", $data[$posts+1]);

        $user = array_search($t[1], $memberdata);
        if (empty($user)) {
            $user = $t[1];
        }

        $result = $db->query("
		INSERT INTO `{$db->pre}topics`
			( `id` , `board` , `topic` , `posts` , `name` , `date` , `status` , `last` , `last_name` )
		VALUES (
			'{$id}', '{$board}', '{$topic}', '{$posts}', '{$user}', '{$p1[3]}', '{$status}', '{$pl[3]}', '{$pl[1]}'
		)");
        $t_i++;
        $id = $db->insert_id($result);

        // Replies
        foreach ($data as $key => $r) {

            $r = explode("\t", $r);

            if ($key == 0) {
                continue;
            }
            if ($key > 1) {
                $prefix = 'Re: ';
                $tstart = 0;
            }
            else {
                $prefix = '';
                $tstart = 1;
            }

            $user = array_search($r[1], $memberdata);
            $email = '';
            $guest = 0;
            if (empty($user)) {
                $user = $r[1];
                $email = $config['forenmail'];
                $guest = 1;
            }

            if ($r[5] == 'N') {
                $dosmileys = 0;
            }
            else {
                $dosmileys = 1;
            }

            $comment = convertBB($r[7]);
            $comment = $db->escape_string($comment);

            $db->query("
			INSERT INTO `{$db->pre}replies`
				(`board` , `topic` , `topic_id` , `name` , `guest` , `comment` , `dosmileys` , `email` , `ip` , `date` , `tstart` )
			VALUES (
				{$board}, '{$prefix}{$t[2]}', '{$id}', '{$user}', '{$guest}', '{$comment}', '{$dosmileys}', '{$email}', '{$r[2]}' , '{$r[3]}', '{$tstart}'
			)");
            $p_i++;
        }
    }
    ?>
	<p>Es wurden <?php echo $t_i; ?> Themen mit <?php echo $p_i; ?> Beiträgen konvertiert.</p>
	<p>Derzeit führen Sie Schritt 5 von 6 aus:
	<ol type="I">
	<li><s>Einführung / Vorbereitung</s></li>
	<li><s>Konvertierung der Smileys</s></li>
	<li><s>Konvertierung der Mitgliederdaten</s></li>
	<li><s>Konvertierung der Kategorien und Foren</s></li>
	<li><strong>Konvertierung der Beiträge und Themen</strong></li>
	<li>Konvertierung der Themen-Abonnements</li>
	</ol>
	</p>
	<p><a href="?action=abos&amp;path=<?php echo rawurlencode($path); ?>">Nächsten Schritt ausführen (Themen-Abonnements)</a></p>
	<?php
}
else {
	?>
	<form action="?action=smileys" method="post">
	<p>Um die Konvertierung zu starten installiere zuerst das Viscacha komplett, aber ohne Beispieldaten. Der bei der Installation anzulegende Nutzer wird während der Konvertierung wieder gelöscht. Beachten Sie, dass diese Datei (<?php echo basename(__FILE__); ?>) im Viscacha-Hauptverzeichnis (dort liegen z.B. die Dateien portal.php und showtopic.php) liegen sollte. Es wird empfohlen nach der Installation die Foren- und Mitglieder-Statistiken jeweils neu zählen zu lassen. Für die Konvertierung benötigen Sie mindestens PHP ab Version 4.3 und MySQL ab Version 4. Falls Sie Fragen haben oder an irgendeiner Stelle auf Probleme stoßen so kontaktieren Sie uns einfach im <a href="http://www.mamo-net.de" target="_blank">Support-Forum unter www.mamo-net.de</a>. W&auml;hrend der Konvertierung kann es sein, dass einige Schritte sehr lange ben&ouml;tigen um geladen zu werden, da viele Daten verarbeitet werden m&uuml;ssen!</p>
	<p<?php echo $error == true ? ' style="font-weight: bold; color: maroon;"' : ''; ?>>Bitte geben Sie hier den Pfad vom Viscacha-Verzeichnis zu Ihrer CuteCast-Installation an:<br />
	<input type="text" value="" name="path" size="50" />
	</p>
	<p>Die Konvertierung erfolgt in 5 Schritten. Derzeit führen Sie Schritt 1 aus.
	<ol type="I">
	<li><strong>Einführung / Vorbereitung</strong></li>
	<li>Konvertierung der Smileys</li>
	<li>Konvertierung der Mitgliederdaten</li>
	<li>Konvertierung der Kategorien und Foren</li>
	<li>Konvertierung der Beiträge und Themen</li>
	<li>Konvertierung der Themen-Abonnements</li>
	</ol>
	</p>
	<p><button type="submit">Starte Konvertierung (Smileys)</button></p>
	</form>
	<?php
}
if (!empty($_REQUEST['action'])) {
	$db->close();
}
?>
<div id="copyright"><a href="http://www.viscacha.org" target="_blank">Copyright &copy; 2007 by Viscacha</a></div>
</body>
</html>