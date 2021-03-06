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

DEFINE('SCRIPTNAME', 'manageforum');

include ("data/config.inc.php");
include ("classes/function.viscacha_frontend.php");

$zeitmessung1 = t1();

$board = $gpc->get('id', int);

$slog = new slog();
$my = $slog->logged();
$lang->init($my->language);
$tpl = new tpl();

$fc = cache_cat_bid();
if (empty($board) || !isset($fc[$board])) {
	error($lang->phrase('query_string_error'));
}
$info = $fc[$board];

$my->p = $slog->Permissions($info['id']);
$my->mp = $slog->ModPermissions($info['id']);

forum_opt($info['opt'], $info['optvalue'], $info['id']);

$breadcrumb->Add($lang->phrase('teamcp'));

echo $tpl->parse("header");
if ($my->vlogin && $my->mp[0] == 1) { 
	if ($_GET['action'] == "index") {
        if ($_GET['type'] == 'open') {
        	$marksql = ' AND status = "1" ';
        }
        elseif ($_GET['type'] == 'close') {
        	$marksql = ' AND status = "0" ';
        }
        else { //  'close' or 'move'
        	$marksql = '';
        }
        
        if (!empty($marksql)) {
        	$result = $db->query("SELECT COUNT(*) FROM {$db->pre}topics WHERE board = '$board' ".$marksql,__LINE__,__FILE__);
        	$vlasttopics = $db->fetch_array($result);
        	$info['topics'] = $vlasttopics[0];
        }
        
        $pages = pages($info['topics'], 'forumzahl', 'manageforum.php?action=index&amp;id='.$board.'&amp;type='.$_GET['type'].'&amp;');
        $inner['index_bit'] = '';
        if ($info['topics'] > 0) {
        	$start = $_GET['page']*$config['forumzahl'];
        	$start = $start-$config['forumzahl'];
        	$result = $db->query("
        	SELECT prefix, vquestion, posts, mark, id, board, topic, date, status, last, last_name, sticky, name 
        	FROM {$db->pre}topics WHERE board = '$board' $marksql
        	ORDER BY sticky DESC, last DESC LIMIT $start, ".$config['forumzahl']
        	,__LINE__,__FILE__);
        	
        	$prefix = cache_prefix($board);
        	$memberdata = cache_memberdata();
        
        	while ($row = $gpc->prepare($db->fetch_object($result))) {
        		$pref = '';
        		$showprefix = FALSE;
        		if (isset($prefix[$row->prefix]) && $row->prefix > 0) {
        			$showprefix = TRUE;
        		}
        		else {
        			$prefix[$row->prefix] = '';
        		}
        		
        		if(is_id($row->name) && isset($memberdata[$row->name])) {
        			$row->mid = $row->name;
        			$row->name = $memberdata[$row->name];
        		}
        		else {
        			$row->mid = FALSE;
        		}
        		
        		if (is_id($row->last_name) && isset($memberdata[$row->last_name])) {
        			$row->last_name = $memberdata[$row->last_name];
        		}
        		
        		$rstart = str_date($lang->phrase('dformat1'),times($row->date));
        		$rlast = str_date($lang->phrase('dformat1'),times($row->last));
        		
        		if ($row->mark == 'n') {
        			$pref .= $lang->phrase('forum_mark_n'); 
        		}
        		elseif ($row->mark == 'a') {
        			$pref .= $lang->phrase('forum_mark_a');
        		}
        		elseif ($row->mark == 'b') {
        			$pref .= $lang->phrase('forum_mark_b');
        		}
        		elseif ($row->mark == 'g') {
        			$pref .= $lang->phrase('forum_mark_g');
        		}
        		elseif ($row->status == '2') {
        			$pref .= $lang->phrase('forum_moved');
        		}
        		elseif ($row->sticky == '1') {
        			$pref .= $lang->phrase('forum_announcement');
        		}

        		$inner['index_bit'] .= $tpl->parse("admin/forum/index_bit");
        	}
        }
        else {
        	$inner['index_bit'] .= $tpl->parse("admin/forum/index_bit_empty");
        }
        
        echo $tpl->parse("menu");
        echo $tpl->parse("admin/forum/index");
	}
	elseif ($_GET['action'] == "close") {
		if (count($_POST['delete']) == 0) {
			viscacha_header('Location: manageforum.php?action=index&id='.$board.'&type='.$_GET['type'].SID2URL_JS_x);
		}
	    $db->query("UPDATE {$db->pre}topics SET status = '1' WHERE board = '{$board}' AND id IN(".implode(',', $_POST['delete']).")",__LINE__,__FILE__);	
	    if ($db->affected_rows() > 0) {
	        ok($lang->phrase('admin_topicstatus_changed'),'showforum.php?id='.$board.SID2URL_x);
	    }
	    else {
	        error($lang->phrase('admin_failed'),'showforum.php?id='.$board.SID2URL_x);
	    }
	}
	elseif ($_GET['action'] == "open") {
		if (count($_POST['delete']) == 0) {
			viscacha_header('Location: manageforum.php?action=index&id='.$board.'&type='.$_GET['type'].SID2URL_JS_x);
		}
	    $db->query("UPDATE {$db->pre}topics SET status = '0' WHERE board = '{$board}' AND id IN(".implode(',', $_POST['delete']).")",__LINE__,__FILE__);	
	    if ($db->affected_rows() > 0) {
	        ok($lang->phrase('admin_topicstatus_changed'),'showforum.php?id='.$board.SID2URL_x);
	    }
	    else {
	        error($lang->phrase('admin_failed'),'showforum.php?id='.$board.SID2URL_x);
	    }
	}
	elseif ($_GET['action'] == "move") {
	    $my->pb = $slog->GlobalPermissions();
		if ($my->mp[0] == 1 && $my->mp[5] == 0) {
	    	errorLogin($lang->phrase('not_allowed'), 'showforum.php?id='.$board.SID2URL_x);
	    }
		$forums = BoardSubs();
		echo $tpl->parse("menu");
		echo $tpl->parse("admin/forum/move");  
	}
	elseif ($_GET['action'] == "move2") {
	    if ($my->mp[0] == 1 && $my->mp[5] == 0) {
	    	errorLogin($lang->phrase('not_allowed'), 'manageforum.php?action=index&amp;id='.$board.'&amp;type='.$_GET['type'].SID2URL_x.'&amp;');
	    }
	    $anz = 0;
	    foreach ($_POST['delete'] as $id) {
    	    $result = $db->query("SELECT r.date, r.topic, r.name, r.email, u.name AS uname, u.mail AS uemail FROM {$db->pre}replies AS r LEFT JOIN {$db->pre}user AS u ON u.id = r.name WHERE topic_id = '{$id}' AND tstart = '1'",__LINE__,__FILE__);
    	    $old = $db->fetch_assoc($result);
    	    $db->query("UPDATE {$db->pre}topics SET board = '{$_POST['opt_0']}' WHERE id = '{$id}' LIMIT 1",__LINE__,__FILE__); 	    
    	    $anz += $db->affected_rows();
    	    $db->query("UPDATE {$db->pre}replies SET board = '{$_POST['opt_0']}' WHERE topic_id = '{$id}'",__LINE__,__FILE__); 
    		$anz += $db->affected_rows();
    		
    		if ($_POST['temp'] == 1) {
    		    // Prefix wird nicht übernommen!
    	    	$db->query("INSERT INTO {$db->pre}topics SET status = '2', topic = '{$old['topic']}', board='{$board}', name = '{$old['name']}', date = '{$old['date']}', last_name = '{$old['name']}', last = '{$old['date']}'",__LINE__,__FILE__);	
    	    	$tid = $db->insert_id();
    	    	$db->query("INSERT INTO {$db->pre}replies SET tstart = '1', topic_id = '{$tid}', comment = '{$id}', topic = '{$old['topic']}', board='{$board}', name = '{$old['name']}', email = '{$old['email']}', date = '{$old['date']}'",__LINE__,__FILE__);	
    		}
    	    if ($_POST['temp2'] == 1) {
    	    	if (empty($old['email'])) {
    	    		$old['email'] = $old['uemail'];
    	    		$old['name'] = $old['uname'];
    	    	}
    		    $data = $lang->get_mail('mass_topic_moved');
    			$to = array('0' => array('name' => $old['name'], 'mail' => $old['email']));
    			$from = array();
    			xmail($to, $from, $data['title'], $data['comment']);
    	    }
	    }
	    UpdateBoardStats($board);
	    UpdateBoardStats($_POST['opt_0']);
	    ok($lang->phrase('x_entries_moved'),'showforum.php?id='.$board.SID2URL_x);
	}
	elseif ($_GET['action'] == "delete") {
	    if ($my->mp[0] == 1 && $my->mp[4] == 0) {
	    	errorLogin($lang->phrase('not_allowed'),'manageforum.php?action=index&amp;id='.$board.'&amp;type='.$_GET['type'].SID2URL_x.'&amp;');
	    }
		if (count($_POST['delete']) == 0) {
			viscacha_header('Location: manageforum.php?action=index&id='.$board.'&type='.$_GET['type'].SID2URL_JS_x);
		}
	    $ids = implode(',', $_POST['delete']);
		$db->query ("DELETE FROM {$db->pre}replies WHERE topic_id IN({$ids})",__LINE__,__FILE__);
		$anz = $db->affected_rows();
		$uresult = $db->query ("SELECT file FROM {$db->pre}uploads WHERE topic_id IN({$ids})",__LINE__,__FILE__);
		while ($urow = $db->fetch_array($uresult)) {
		    @unlink('uploads/topics/'.$urow[0]);
		    if (file_exists('uploads/topics/thumbnails/'.$urow[0])) {
		    	@unlink('uploads/topics/thumbnails/'.$urow[0]);
		    }
		}
		$db->query ("DELETE FROM {$db->pre}uploads WHERE topic_id IN({$ids})",__LINE__,__FILE__);
		$anz += $db->affected_rows();
		$db->query ("DELETE FROM {$db->pre}abos WHERE tid IN({$ids})",__LINE__,__FILE__);
		$anz += $db->affected_rows();
		$db->query ("DELETE FROM {$db->pre}fav WHERE tid IN({$ids})",__LINE__,__FILE__);
		$anz += $db->affected_rows();
		$db->query ("DELETE FROM {$db->pre}topics WHERE id IN({$ids})",__LINE__,__FILE__);
		$anz += $db->affected_rows();
		$votes = $db->query("SELECT id FROM {$db->pre}vote WHERE tid IN({$ids})",__LINE__,__FILE__);
		$voteaids = array();
		while ($row = $db->fetch_array($votes)) {
			$voteaids[] = $row[0];
		}
		if (count($voteaids) > 0) {
			$db->query ("DELETE FROM {$db->pre}votes WHERE id IN (".implode(',', $voteaids).")",__LINE__,__FILE__);
			$anz += $db->affected_rows();
		}
		$db->query ("DELETE FROM {$db->pre}vote WHERE id IN({$ids})",__LINE__,__FILE__);
		$anz += $db->affected_rows();
		
		UpdateBoardStats($board);
		ok($lang->phrase('x_entries_deleted'),"showforum.php?id=".$board.SID2URL_x);
	}
	elseif ($_GET['action'] == "stat") {
	    UpdateBoardStats($info['id']);
	    ok($lang->phrase('data_success'),'showforum.php?id='.$board.SID2URL_x);
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
