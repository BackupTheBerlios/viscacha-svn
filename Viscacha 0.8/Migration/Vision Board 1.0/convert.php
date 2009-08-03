<?php
include('data/config.inc.php');
DEFINE('SCRIPTNAME', 'admin');
DEFINE('TEMPSHOWLOG', 1);
include('classes/function.viscacha_frontend.php');

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

if ($_GET['action'] == 'smileys') {
	$i = 0;
	if (file_exists('OLD/txt/emoticons.txt')) {
	$smileys = file('OLD/txt/emoticons.txt');
	foreach ($smileys as $s) {
	    $s = explode("\t", $s);
	    $db->query("INSERT INTO `v_smileys` ( `search` , `replace` , `desc`) VALUES ( '{$s[0]}', '{$s[2]}', '{$s[1]}')") or print($db->error());
	    $i++;
	}
    //unlink('OLD/txt/emoticons.txt');
	}
	echo 'Smileys eingefügt: '.$i.' Zeilen!<br><a href="?action=members">Weiter (Mitglieder)</a>';
}
elseif ($_GET['action'] == 'pms') {
    $i = 0;
    $db->query('TRUNCATE TABLE `v_pm`');
    chdir('OLD/members/');
    foreach (glob("*.pm") as $f) {
        $data = file($f);
        
		$asdffile = str_replace('.pm', '.user', $f);
		if (!file_exists($asdffile)) {
			continue;
		}
        $mdata = file($asdffile);
        $mdata = array_map("trim", $mdata);
        $mu = array();
        foreach ($mdata as $md) {
            $md = explode('=', $md);
            $mu[$md[0]] = $db->escape_string($md[1]);
        }
        
        $result = $db->query('SELECT id FROM v_user WHERE name LIKE "'.$mu['username'].'"');
        $me = $db->fetch_assoc($result);
        if ($me['id'] > 0 && $db->num_rows() == 1) {
        $data = array_map("trim", $data);
        foreach ($data as $d) {
            $d = explode("\t", $d);
            $u = array();
            foreach ($d as $dt) {
                $u[] = $db->escape_string($dt);
            }
            $u[7] = convertBB($u[7]);
            $result = $db->query('SELECT id FROM v_user WHERE name LIKE "'.$u[0].'"');
            $me2 = $db->fetch_assoc($result);
            if ($me2['id'] > 0) {
                 $db->query("
                INSERT INTO `v_pm`
                (`topic` , `pm_from` , `pm_to` , `comment` , `date` ) 
                VALUES (
                '{$u[6]}', '{$me2['id']}', '{$me['id']}', '{$u[7]}', '{$u[2]}'
                )") or print($db->error()); $i++;
            }
        }
        //unlink($f);
        }
    }
	echo 'PMs eingefügt: '.$i.'!<br><a href="?action=posts">Weiter (Beiträge)</a>';
}
elseif ($_GET['action'] == 'members') {
    $i = 0;
    $db->query('TRUNCATE TABLE `v_user`');
    chdir('OLD/members/');
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
            $u['icq'] = "'0'";
        }
        else {
            $u['icq'] = "'{$u['icq']}'";
        }
        if ($u['newsletter'] == "yes") {
            $u['newsletter'] = '1';
        }
        else {
            $u['newsletter'] = '0';
        }
        if (intval($u['showmail']) == 1) {
            $u['showmail'] = '0';
        }
        elseif (intval($u['showmail']) == 0) {
            $u['showmail'] = '1';
        }
        $groups = '';
        if ($u['level'] == 'GEN') {
            $groups = '1';
        }
        elseif ($u['level'] == 'LTG') {
            $groups = '2';
        }
        if (strlen($u['homepage']) < 10) {
            $u['homepage'] = '';
        }
        
        $db->query("
        INSERT INTO `v_user` (`name` , `pw` , `mail` , `regdate` , `hp` , `signature` , `location` ,`gender` , 	`birthday` , `lastvisit` , 	`icq` , `yahoo` , `aol` , `msn` , `groups` ,`opt_pmnotify` , `opt_hidemail` , `opt_newsletter` , `confirm`) 
        VALUES (
         '{$u['username']}', 
         '{$u['password']}', 
         '{$u['email']}', 
         '{$u['date registered']}', 
         '{$u['homepage']}', 
         '{$u['signature']}', 
         '{$u['location']}', 
         '{$u['gender']}', 
         '{$u['birthyear']}-{$u['birthmonth']}-{$u['birthday']}', 
         '".$u['last post']."', 
         {$u['icq']}, 
         '{$u['ym']}', 
         '{$u['aim']}', 
         '{$u['msn']}', 
         '{$groups}', 
         '{$u['pmnotiz']}', 
         '{$u['showmail']}', 
         '{$u['newsletter']}',
         '11')") or print($db->error());
        $i++;
    }
	echo 'Mitglieder eingefügt: '.$i.'!<br><a href="?action=pms">Weiter (PMs)</a>';
}
elseif ($_GET['action'] == 'abos') {
    $db->query('TRUNCATE TABLE `v_abos`');
    $i = 0;
	$g = 0;
    
    $result = $db->query('SELECT id, name FROM v_user');
    $memberdata = array();
    while($row = $db->fetch_assoc()) {
        $memberdata[$row['id']] = $row['name'];
    }
    
    chdir('OLD/subscribe/');
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
            
            $db->query("INSERT INTO `v_abos` (`mid` , `tid`) VALUES ($user, $id)") or print($db->error());
            $i++;
        }
	$g++;
    }
	echo 'Abos ('.$g.') eingefügt: '.$i.'!<br>Automatisierte Konvertierung fertiggestellt!';
}
elseif ($_GET['action'] == 'posts') {

    $db->query('TRUNCATE TABLE `v_replies`');
    $db->query('TRUNCATE TABLE `v_topics`');

    $t_i = 0;
    $p_i = 0;
    $bd = array();
    $boards = file('OLD/data.db');
    $boards = array_map("trim", $boards);
    foreach ($boards as $b) {
        $bid = explode('=', $b);
        $btids = explode('*', $b);
        $btids = explode(' ', $btids[1]);
        foreach ($btids as $btid) {
            $bd[$btid] = $bid[0];
        }
    }

    $result = $db->query('SELECT id, name FROM v_user');
    $memberdata = array();
    while($row = $db->fetch_assoc()) {
        $memberdata[$row['id']] = $row['name'];
    }
    
    chdir('OLD/data/');
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
        if (!empty($t[7]) && strlen($t[7]) > 2) {
            $topic .= " ({$t[7]})";
        }
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
INSERT INTO `v_topics` ( `id` , `board` , `topic` , `posts` , `name` , `date` , `status` , `last` , `last_name` ) 
VALUES (
$id, {$board}, '{$topic}', $posts, '{$user}', '{$p1[3]}', '{$status}', '{$pl[3]}', '{$pl[1]}'
)") or print($db->error());
        $t_i++;
        $id = $db->insert_id($result);
        
        // Replies
        foreach ($data as $key => $r) {
            
            $r = explode("\t", $r);
            
            if ($key == 0) {
                continue;
            }
            if ($key > 1) {
                $prefix = 'RE: ';
                $tstart = 0;
            }
            else {
                $prefix = '';
                $tstart = 1;
            }
            
            $user = array_search($r[1], $memberdata);
            $email = '';
            if (empty($user)) {
                $user = $r[1];
                $email = $config['forenmail'];
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
INSERT INTO `v_replies` (`board` , `topic` , `topic_id` , `name` , `comment` , `dosmileys` , `email` , `date` , `tstart` ) 
VALUES (
{$board}, '{$prefix}{$t[2]}', '{$id}', '{$user}', '{$comment}', '{$dosmileys}', '{$email}', '{$r[3]}', '{$tstart}'
)") or print($db->error());
            $p_i++;
        }
    }
    $bids = array_unique(array_keys($bd));
    foreach ($bd as $biid) {
        UpdateBoardStats($biid);
    }
	echo 'Antworten eingefügt: '.$p_i.'!  Themen eingefügt: '.$t_i.'!<br><a href="?action=votes">Weiter (Votes)</a>';
}
elseif ($_GET['action'] == 'votes') {
    $ii = 0;

    $db->query('TRUNCATE TABLE `v_votes`');
    $db->query('TRUNCATE TABLE `v_vote`');

    $result = $db->query('SELECT id, name FROM v_user') or print($db->error());
    $memberdata = array();
    while($row = $db->fetch_assoc()) {
        $memberdata[$row['id']] = $row['name'];
    }
    
    chdir('OLD/data/');
    foreach (glob("*.vote") as $f) {
        $id = basename($f);
        $id = str_replace('.vote', '', $id);
        $id = intval($id);
        $data = file($f);
        $data = array_map("trim", $data);
        $vd = array();
        $vq = array();
        foreach ($data as $d) {
            $d = explode('=', $d);
            if ($d[0]{0} != 'v') {
                $vd[$d[0]] = $d[1];
            }
            else {
                $vq[] = $d[1];
            }
        }
        
		$vd['question'] = $db->escape_string($vd['question']);
        $db->query('UPDATE v_topics SET vquestion = "'.$vd['question'].'*" WHERE id = '.$id) or print($db->error());
        
		$vd['alreadyvoteduser'] = explode("\t", $vd['alreadyvoteduser']);
        
        $ui = 0;
        foreach ($vq as $q) {
            $q = explode("\t", $q);
            $q[0] = $db->escape_string($q[0]);
            $db->query("INSERT INTO `v_vote` (`tid` , `answer`) VALUES ({$id}, '{$q[0]}')") or print($db->error());
            $aid = $db->insert_id();
            for ($i=0;$i<$q[1];$i++) {
                $user = array_search($vd['alreadyvoteduser'][$ui], $memberdata);
                if (empty($user)) {
                    $user = 0;
                }
            	$db->query("INSERT INTO `v_votes` (`mid` , `aid`) VALUES ({$user}, '{$aid}')") or print($db->error());
                if ($user == 0) {
                    echo "Proof-ID: ".$db->insert_id()."<br />";
                }
                $ui++;
            }

        }
        $ii++;
    }
	echo 'Votes angehängt: '.$ii.'!<br><a href="?action=abos">Weiter (Abos)</a>';
}
else {
    echo '<a href="?action=smileys">Starte Konvertierung (Smileys)</a>';
}

$db->close();
?>