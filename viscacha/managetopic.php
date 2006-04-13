<?php
/*
	Viscacha - A bulletin board solution for easily managing your content
	Copyright (C) 2004-2006  Matthias Mohr, MaMo Net
	
	Author: Matthias Mohr
	Publisher: http://www.mamo-net.de
	Start Date: May 22, 2004

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

error_reporting(E_ALL);

DEFINE('SCRIPTNAME', 'managetopic');

include ("data/config.inc.php");
include ("classes/function.viscacha_frontend.php");

$zeitmessung1 = t1();

$slog = new slog();
$my = $slog->logged();
$lang->init($my->language);
$tpl = new tpl();

$action = $gpc->get('action', none);

$result = $db->query('SELECT board, mark, id, last_name, prefix, topic FROM '.$db->pre.'topics WHERE id = "'.$_GET['id'].'" LIMIT 1',__LINE__,__FILE__);
if ($db->num_rows($result) != 1) {
	error($lang->phrase('query_string_error'));
}
$info = $db->fetch_assoc($result);
$info['last_name'] = $gpc->prepare($info['last_name']);

$my->p = $slog->Permissions($info['board']);
$my->mp = $slog->ModPermissions($info['board']);

// preparing data for breadcrumb
$catbid = $scache->load('cat_bid');
$fc = $catbid->get();
$last = $fc[$info['board']];
$topforums = get_headboards($fc, $last, true);

$pre = '';
if ($info['prefix'] > 0) {
	$prefix_obj = $scache->load('prefix');
	$prefix = $prefix_obj->get($info['board']);
	if (isset($prefix[$info['prefix']])) {
		$pre = $prefix[$info['prefix']];
		$pre = $lang->phrase('showtopic_prefix_title');
	}
}

$breadcrumb->Add($last['name'], "showforum.php?id=".$last['id'].SID2URL_x);
$breadcrumb->Add($pre.$info['topic'], "showtopic.php?id=".$info['id'].SID2URL_x);
$breadcrumb->Add($lang->phrase('teamcp'));

echo $tpl->parse("header");

forum_opt($last['opt'], $last['optvalue'], $last['id']);

if ($my->vlogin && $my->mp[0] == 1) { 
	if ($action == "delete") {
		if ($my->mp[0] == 1 && $my->mp[4] == 0) {
			errorLogin($lang->phrase('not_allowed'), 'showtopic.php?id='.$info['id'].SID2URL_x);
		}
		echo $tpl->parse("menu");
		echo $tpl->parse("admin/topic/delete");
	}
	elseif ($action == "delete2") {
		if ($my->mp[0] == 1 && $my->mp[4] == 0) {
			errorLogin($lang->phrase('not_allowed'), 'showtopic.php?id='.$info['id'].SID2URL_x);
		}
		$db->query ("DELETE FROM {$db->pre}replies WHERE topic_id = '{$info['id']}'",__LINE__,__FILE__);
		$anz = $db->affected_rows();
		$uresult = $db->query ("SELECT file FROM {$db->pre}uploads WHERE topic_id = '{$info['id']}'",__LINE__,__FILE__);
		while ($urow = $db->fetch_array($uresult)) {
			@unlink('uploads/topics/'.$urow[0]);
			if (file_exists('uploads/topics/thumbnails/'.$urow[0])) {
				@unlink('uploads/topics/thumbnails/'.$urow[0]);
			}
		}
		$db->query ("DELETE FROM {$db->pre}postratings WHERE tid = '{$info['id']}'",__LINE__,__FILE__);
		$anz += $db->affected_rows();
		$db->query ("DELETE FROM {$db->pre}uploads WHERE topic_id = '{$info['id']}'",__LINE__,__FILE__);
		$anz += $db->affected_rows();
		$db->query ("DELETE FROM {$db->pre}abos WHERE tid = '{$info['id']}'",__LINE__,__FILE__);
		$anz += $db->affected_rows();
		$db->query ("DELETE FROM {$db->pre}topics WHERE id = '{$info['id']}'",__LINE__,__FILE__);
		$anz += $db->affected_rows();
		$votes = $db->query("SELECT id FROM {$db->pre}vote WHERE tid = '{$info['id']}'",__LINE__,__FILE__);
		$voteaids = array();
		while ($row = $db->fetch_array($votes)) {
			$voteaids[] = $row[0];
		}
		if (count($voteaids) > 0) {
			$db->query ("DELETE FROM {$db->pre}votes WHERE id IN (".implode(',', $voteaids).")",__LINE__,__FILE__);
			$anz += $db->affected_rows();
		}
		$db->query ("DELETE FROM {$db->pre}vote WHERE tid = '{$info['id']}'",__LINE__,__FILE__);
		$anz += $db->affected_rows();
		
		UpdateBoardStats($info['board']);
		ok($lang->phrase('x_entries_deleted'),"showforum.php?id=".$info['board'].SID2URL_x);
	}
	elseif ($action == "move") {
		$my->pb = $slog->GlobalPermissions();
		if ($my->mp[0] == 1 && $my->mp[5] == 0) {
			errorLogin($lang->phrase('not_allowed'), 'showtopic.php?id='.$info['id'].SID2URL_x);
		}
		$forums = BoardSubs();
		echo $tpl->parse("menu");
		echo $tpl->parse("admin/topic/move");  
	}
	elseif ($action == "move2") {		
		if ($my->mp[0] == 1 && $my->mp[5] == 0) {
			errorLogin($lang->phrase('not_allowed'), 'showtopic.php?id='.$info['id'].SID2URL_x);
		}
		
		$result = $db->query("SELECT r.date, r.topic, r.name, r.email, u.name AS uname, u.mail AS uemail FROM {$db->pre}replies AS r LEFT JOIN {$db->pre}user AS u ON u.id = r.name WHERE topic_id = '{$info['id']}' AND tstart = '1'",__LINE__,__FILE__);
		$old = $db->fetch_assoc($result);
		
		$board = $gpc->get('board', int);
		
		$db->query("UPDATE {$db->pre}topics SET board = '{$board}' WHERE id = '{$info['id']}' LIMIT 1",__LINE__,__FILE__);		
		$anz = $db->affected_rows();
		$db->query("UPDATE {$db->pre}replies SET board = '{$board}' WHERE topic_id = '{$info['id']}'",__LINE__,__FILE__); 
		$anz += $db->affected_rows();
		
		if ($_POST['temp'] == 1) {
			$db->query("INSERT INTO {$db->pre}topics SET status = '2', topic = '{$old['topic']}', board='{$info['board']}', name = '{$old['name']}', date = '{$old['date']}', last_name = '{$info['last_name']}', prefix = '{$info['prefix']}', last = '{$old['date']}'",__LINE__,__FILE__);	
			$tid = $db->insert_id();
			$db->query("INSERT INTO {$db->pre}replies SET tstart = '1', topic_id = '{$tid}', comment = '{$info['id']}', topic = '{$old['topic']}', board='{$info['board']}', name = '{$old['name']}', email = '{$old['email']}', date = '{$old['date']}'",__LINE__,__FILE__);	
		}
		if ($_POST['temp2'] == 1) {
			if (empty($old['email'])) {
				$old['email'] = $old['uemail'];
				$old['name'] = $old['uname'];
			}
			$data = $lang->get_mail('topic_moved');
			$to = array('0' => array('name' => $old['name'], 'mail' => $old['email']));
			$from = array();
			xmail($to, $from, $data['title'], $data['comment']);
		}
		UpdateBoardStats($info['board']);
		UpdateBoardStats($board);
		ok($lang->phrase('x_entries_moved'),'showtopic.php?id='.$info['id']);
	}
	
	elseif ($action == "status") {
		if ($my->mp[0] == 1 && $my->mp[1] == 0 && $my->mp[2] == 0 && $my->mp[3] == 0) {
			errorLogin($lang->phrase('not_allowed'),'showtopic.php?id='.$info['id'].SID2URL_x);
		}
		echo $tpl->parse("menu");
		echo $tpl->parse("admin/topic/status");
	}
	elseif ($action == "status2") {
		$input = '';
		$notallowed = FALSE;
		if ($my->mp[0] == 1 && $my->mp[1] == 0 && $my->mp[2] == 0 && $my->mp[3] == 0) {
			$notallowed = TRUE;
		}  
		if ($_POST['temp'] == '1') {
			if ($my->mp[1] == 1) {
				$input = 'g';
			}
			else {
				$notallowed = TRUE;
			}
		}
		if ($_POST['temp'] == '2') {
			if ($my->mp[1] == 1) {
				$input = 'b';
			}
			else {
				$notallowed = TRUE;
			}
		}
		if ($_POST['temp'] == '3') {
			if ($my->mp[3] == 1) {
				$input = 'a';
			}
			else {
				$notallowed = TRUE;
			}
		}
		if ($_POST['temp'] == '4') {
			if ($my->mp[2] == 1) {
				$input = 'n';
			}
			else {
				$notallowed = TRUE;
			}
		}
		if ($notallowed) {
			errorLogin($lang->phrase('not_allowed'), 'showtopic.php?id='.$info['id'].SID2URL_x);
		}
		$db->query("UPDATE {$db->pre}topics SET mark = '".$input."' WHERE id = '".$info['id']."'",__LINE__,__FILE__);	
		if ($db->affected_rows() == 1) {
			ok($lang->phrase('admin_topicstatus_changed'),'showtopic.php?id='.$info['id'].SID2URL_x);
		}
		else {
			error($lang->phrase('admin_failed'),'showtopic.php?id='.$info['id'].SID2URL_x);
		}
	}
	elseif ($action == "pin") {	
		$db->query("UPDATE {$db->pre}topics SET sticky = '1' WHERE id = '".$info['id']."'",__LINE__,__FILE__);	
		if ($db->affected_rows() == 1) {
			ok($lang->phrase('admin_topicstatus_changed'),'showtopic.php?id='.$info['id'].SID2URL_x);
		}
		else {
			error($lang->phrase('admin_failed'),'showtopic.php?id='.$info['id'].SID2URL_x);
		}
	}
	elseif ($action == "unpin") {
		$db->query("UPDATE {$db->pre}topics SET sticky = '0' WHERE id = '".$info['id']."'",__LINE__,__FILE__);	
		if ($db->affected_rows() == 1) {
			ok($lang->phrase('admin_topicstatus_changed'),'showtopic.php?id='.$info['id'].SID2URL_x);
		}
		else {
			error($lang->phrase('admin_failed'),'showtopic.php?id='.$info['id'].SID2URL_x);
		}   
	}
	elseif ($action == "close") {
		$db->query("UPDATE {$db->pre}topics SET status = '1' WHERE id = '".$info['id']."'",__LINE__,__FILE__);	
		if ($db->affected_rows() == 1) {
			ok($lang->phrase('admin_topicstatus_changed'),'showtopic.php?id='.$info['id'].SID2URL_x);
		}
		else {
			error($lang->phrase('admin_failed'),'showtopic.php?id='.$info['id'].SID2URL_x);
		}
	}
	elseif ($action == "open") {
		$db->query("UPDATE {$db->pre}topics SET status = '0' WHERE id = '".$info['id']."'",__LINE__,__FILE__);	
		if ($db->affected_rows() == 1) {
			ok($lang->phrase('admin_topicstatus_changed'),'showtopic.php?id='.$info['id'].SID2URL_x);
		}
		else {
			error($lang->phrase('admin_failed'),'showtopic.php?id='.$info['id'].SID2URL_x);
		}	   
	}
	elseif ($action == "stat") {
		UpdateTopicStats($info['id']);
		ok($lang->phrase('data_success'),'showtopic.php?id='.$info['id'].SID2URL_x);
	}
	elseif ($action == "vote_export") {
		require_once("classes/class.charts.php");
		$PG = new PowerGraphic();
		
		$skin = $gpc->get('skin', int, 1);
		$modus = $gpc->get('modus', int, 1);

		echo $tpl->parse("menu");
		echo $tpl->parse("admin/topic/vote_export");
	}
	elseif ($action == "vote_edit") {
		$error = array();

		$result = $db->query('SELECT id, topic, posts, sticky, status, last, board, vquestion, prefix FROM '.$db->pre.'topics WHERE id = '.$_GET['id'].' LIMIT 1',__LINE__,__FILE__);
		$info = $gpc->prepare($db->fetch_assoc($result));

		$result = $db->query("SELECT id, answer FROM {$db->pre}vote WHERE tid = '{$info['id']}' ORDER BY id",__LINE__,__FILE__);
		
		echo $tpl->parse("menu");
		echo $tpl->parse("admin/topic/vote_edit");
	}
	elseif ($action == "vote_edit2") {
		$error = array();
		if (strxlen($_POST['question']) > $config['maxtitlelength']) {
			$error[] = $lang->phrase('question_too_long');
		}
		if (strxlen($_POST['question']) < $config['mintitlelength']) {
			$error[] = $lang->phrase('question_too_short');
		}
		if (count_filled($_POST['notice']) < 2) {
			$error[] = $lang->phrase('min_replies_vote');
		}
		if (count_filled($_POST['notice']) > 50) {
			$error[] = $lang->phrase('max_replies_vote');
		}
		if (count($error) > 0) {
			error($error,'managetopic.php?action=vote_edit&amp;id='.$_GET['id'].SID2URL_x);
		}
		else {
			$db->query("UPDATE {$db->pre}topics SET vquestion = '{$_POST['question']}' WHERE id = '{$_GET['id']}' LIMIT 1",__LINE__,__FILE__);
			$result = $db->query("SELECT id, answer FROM {$db->pre}vote WHERE tid = '{$info['id']}' ORDER BY id",__LINE__,__FILE__);
			while($row = $db->fetch_assoc($result)) {
				if (!empty($_POST['notice'][$row['id']]) && strlen($_POST['notice'][$row['id']]) < 255) {
					$db->query("UPDATE {$db->pre}vote SET answer = '{$_POST['notice'][$row['id']]}' WHERE id = '{$row['id']}'",__LINE__,__FILE__);
				}
			}
			if (!empty($_POST['notice'][0]) && strlen($_POST['notice'][0]) < 255) {
				$db->query("INSERT INTO {$db->pre}vote (tid, answer) VALUES ('{$_GET['id']}','{$_POST['notice'][0]}')",__LINE__,__FILE__);
			}
			ok($lang->phrase('data_success'),"showtopic.php?id={$_GET['id']}");
		}
	}
	elseif ($action == "vote_delete") {
		if ($my->mp[0] == 1 && $my->mp[4] == 0) {
			errorLogin($lang->phrase('not_allowed'), 'showtopic.php?id='.$info['id'].SID2URL_x);
		}
		echo $tpl->parse("menu");
		echo $tpl->parse("admin/topic/vote_delete");
	}
	elseif ($action == "vote_delete2") {
		if ($my->mp[0] == 1 && $my->mp[4] == 0) {
			errorLogin($lang->phrase('not_allowed'), 'showtopic.php?id='.$info['id'].SID2URL_x);
		}
		$anz = 0;
		$votes = $db->query("SELECT id FROM {$db->pre}vote WHERE tid = '{$info['id']}'",__LINE__,__FILE__);
		$voteaids = array();
		while ($row = $db->fetch_array($votes)) {
			$voteaids[] = $row[0];
		}
		if (count($voteaids) > 0) {
			$db->query ("DELETE FROM {$db->pre}votes WHERE id IN (".implode(',', $voteaids).")",__LINE__,__FILE__);
			$anz += $db->affected_rows();
		}
		$db->query ("DELETE FROM {$db->pre}vote WHERE tid = '{$info['id']}'",__LINE__,__FILE__);
		$anz += $db->affected_rows();
		$db->query("UPDATE {$db->pre}topics SET vquestion = '' WHERE id = '{$info['id']}'");
		
		ok($lang->phrase('x_entries_deleted'),"showforum.php?id=".$info['board'].SID2URL_x);
	}
	elseif ($action == "pdelete") {
		if ($my->mp[0] == 1 && $my->mp[4] == 0) {
			errorLogin($lang->phrase('not_allowed'), 'showtopic.php?id='.$info['id'].SID2URL_x);
		}
		$ids = $gpc->get('ids', arr_int);
		if (count($ids) == 0) {
			error($lang->phrase('no_data_selected'));
		}
		
		$iid = implode(',', $ids);
		
		$db->query ("DELETE FROM {$db->pre}replies WHERE id IN ({$iid})",__LINE__,__FILE__);
		$anz = $db->affected_rows();
		$uresult = $db->query ("SELECT file FROM {$db->pre}uploads WHERE tid IN ({$iid})",__LINE__,__FILE__);
		while ($urow = $db->fetch_array($uresult)) {
			@unlink('uploads/topics/'.$urow[0]);
			if (file_exists('uploads/topics/thumbnails/'.$urow[0])) {
				@unlink('uploads/topics/thumbnails/'.$urow[0]);
			}
		}
		$db->query ("DELETE FROM {$db->pre}postratings WHERE pid IN ({$iid})",__LINE__,__FILE__);
		$db->query ("DELETE FROM {$db->pre}uploads WHERE tid IN ({$iid})",__LINE__,__FILE__);

		$result = $db->query("SELECT id FROM {$db->pre}replies WHERE topic_id = '{$info['id']}'");
		if ($db->num_rows() == 0) {
			$db->query ("DELETE FROM {$db->pre}abos WHERE tid = '{$info['id']}'",__LINE__,__FILE__);
			$db->query ("DELETE FROM {$db->pre}topics WHERE id = '{$info['id']}'",__LINE__,__FILE__);
			$votes = $db->query("SELECT id FROM {$db->pre}vote WHERE tid = '{$info['id']}'",__LINE__,__FILE__);
			$voteaids = array();
			while ($row = $db->fetch_array($votes)) {
				$voteaids[] = $row[0];
			}
			if (count($voteaids) > 0) {
				$db->query ("DELETE FROM {$db->pre}votes WHERE id IN (".implode(',', $voteaids).")",__LINE__,__FILE__);
			}
			$db->query ("DELETE FROM {$db->pre}vote WHERE tid = '{$info['id']}'",__LINE__,__FILE__);
			$redirect = "showforum.php?id=".$info['board'].SID2URL_x;
		}
		else {
			UpdateTopicStats($info['id']);
			$redirect = "showtopic.php?id=".$info['id'].SID2URL_x;
		}
		
		UpdateBoardStats($info['board']);
		ok($lang->phrase('x_entries_deleted'),$redirect);
	}
	
	elseif ($action == "pmerge") {
		$ids = $gpc->get('ids', arr_int);
		if (count($ids) < 2) {
			error($lang->phrase('no_data_selected'));
		}
		$iid = implode(',', $ids);
		
		$result = $db->query("SELECT r.*, u.name AS uname, u.id AS uid, u.mail AS umail FROM {$db->pre}replies AS r LEFT JOIN {$db->pre}user AS u ON r.name = u.id WHERE r.id IN ({$iid}) ORDER BY date ASC", __LINE__, __FILE__);
		$author = array();
		$comment = array();
		$posts = array();
		$topic = array();
		while ($row = $gpc->prepare($db->fetch_assoc($result))) {
			if (!empty($row['email'])) {
				$row['uid'] = 0;
				$row['uname'] = $row['name'];
				$row['umail'] = $row['email'];
			}
			$posts[$row['id']] = $row;
			$author[$row['id']] = $row['uname'];
			$topic[$row['id']] = $row['topic'];
			$comment[$row['id']] = $row['comment'];
		}
		$author = array_unique($author);
		$topic = array_unique($topic);
		$comment = array_unique($comment);
		
		BBProfile($bbcode);
		$inner['smileys'] = $bbcode->getsmileyhtml($config['smileysperrow']);
		$inner['bbhtml'] = $bbcode->getbbhtml();

		echo $tpl->parse("admin/topic/post_merge");
	}
	elseif ($action == "pmerge2") {
		$ids = $gpc->get('ids', arr_int);
		$iids = implode(',', $ids);
		$author = $gpc->get('author', int);
		$error = array();
		if (count($ids) < 2) {
			$error[] = $lang->phrase('no_data_selected');
		}
		if (empty($author)) {
			$error[] = $lang->phrase('name_too_short');
		}
		if (empty($_POST['topic_id'])) {
			$error[] = $lang->phrase('title_too_short');
		}
		 
		if (count($error) > 0) {
			error($error);
		}
		else {
			$cache = array();
			$base = array('date' => time());
			$result = $db->query("SELECT r.*, u.name AS uname FROM {$db->pre}replies AS r LEFT JOIN {$db->pre}user AS u ON r.name = u.id WHERE r.id IN ({$iids})", __LINE__, __FILE__);
			while ($row = $db->fetch_assoc($result)) {
				if (!empty($row['email'])) {
					$row['uname'] = $row['name'];
				}
				$cache[$row['id']] = $row;
				if ($row['date'] < $base['date']) {
					$base = $row;
				}
			}
			
			$old = array();
			foreach ($ids as $id) {
				if ($id != $base['id']) {
					$old[] = $id;
				}
			}
			$iold = implode(',', $old);
			
			$topic = $cache[$_POST['topic_id']]['topic'];
			$name = $cache[$author]['name'];
			$email = $cache[$author]['email'];
			$ip = $cache[$author]['ip'];
			
			$rev = array();
			foreach ($cache as $row) {
				$row['edit'] = explode("\n", $row['edit']);
				foreach ($row['edit'] as $row2) {
					$x = trim($row2);
					if (empty($x)) {
						continue;
					}
					$row2 = explode("\t", $row2);
					$rev[] = array(
						'name' => $row2[0],
						'date' => $row2[1],
						'reason' => (isset($row2[2]) ? $row2[2] : ''),
						'ip' => (isset($row2[3]) ? $row2[3] : '')
					);
				}
			}
			foreach ($old as $id) {
				$row = $cache[$id];
				$rev[] = array(
					'name' => $row['uname'],
					'date' => $row['date'],
					'reason' => $lang->phrase('admin_merge_edit_add'),
					'ip' => $row['ip']
				);
			}
			$rev[] = array(
				'name' => $my->name,
				'date' => time(),
				'reason' => $lang->phrase('admin_merge_edit_reason'),
				'ip' => $my->ip
			);
			
			usort($rev, "cmp_edit_date");

			$edit = '';
			foreach ($rev as $row) {
				$edit .= "{$row['name']}\t{$row['date']}\t{$row['reason']}\t{$row['ip']}\n";
			}
			$edit = trim($edit, "\n");

			$db->query ("UPDATE {$db->pre}postratings SET tid = '{$base['id']}' WHERE tid IN ({$iold})",__LINE__,__FILE__);
			$db->query ("UPDATE {$db->pre}uploads SET tid = '{$base['id']}' WHERE tid IN ({$iold})",__LINE__,__FILE__);
			$db->query ("UPDATE {$db->pre}vote SET tid = '{$base['id']}' WHERE tid IN ({$iold})",__LINE__,__FILE__);
			
			$db->query ("UPDATE {$db->pre}replies SET topic = '{$topic}', name = '{$name}', comment = '{$_POST['comment']}', dosmileys = '{$_POST['dosmileys']}', dowords = '{$_POST['dowords']}', email = '{$email}', ip = '{$ip}', edit = '{$edit}' WHERE id = '{$base['id']}'",__LINE__,__FILE__);
			$db->query ("DELETE FROM {$db->pre}replies WHERE id IN ({$iold})",__LINE__,__FILE__);
			
			UpdateTopicStats($info['id']);
			UpdateBoardStats($info['board']);
			
			$anz = count($ids);
			ok($lang->phrase('x_entries_merged'),"showtopic.php?topic_id=".$base['id']."&action=jumpto&id=".$base['topic_id'].SID2URL_x);
		}
	}
}
else {
	errorLogin($lang->phrase('not_allowed'));
}

$slog->updatelogged();
$zeitmessung = t2();
echo $tpl->parse("footer");
$phpdoc->Out();
$db->close();	
?>
