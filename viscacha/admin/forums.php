<?php
if (isset($_SERVER['PHP_SELF']) && basename($_SERVER['PHP_SELF']) == "forums.php") die('Error: Hacking Attempt');

function ForumSubs ($tree, $cat, $board, $char = '+', $level = 0) {
	foreach ($tree as $cid => $boards) {
		$cdata = $cat[$cid];
		?>
		<tr> 
			<td class="mmbox" width="50%"><?php echo str_repeat($char, $level).' <b>'.$cdata['name']; ?></b></td>
			<td class="mmbox" width="10%"><?php echo $cdata['position']; ?>&nbsp;&nbsp;
				<a href="admin.php?action=forums&job=move&temp1=c_<?php echo $cdata['id']; ?>&int1=-1"><img src="admin/html/images/asc.gif" border="0" alt="Up"></a>&nbsp;
				<a href="admin.php?action=forums&job=move&temp1=c_<?php echo $cdata['id']; ?>&int1=1"><img src="admin/html/images/desc.gif" border="0" alt="Down"></a>
			</td>
			<td class="mmbox" width="30%">
			  <form name="act" action="admin.php?action=locate" method="post">
			  	<select size="1" name="url" onchange="locate(this.value)">
			  	<option value="" selected="selected">Please choose</option>
				 <optgroup label="General">
				  <option value="admin.php?action=forums&job=cat_edit&id=<?php echo $cdata['id']; ?>">Edit Category</option>
				  <option value="admin.php?action=forums&job=cat_delete&id=<?php echo $cdata['id']; ?>">Delete Category</option>
				 </optgroup>
				</select>
				<input type="submit" value="Go">
			  </form>
			</td> 
		</tr>
		<?php
		foreach ($boards as $bid => $sub) {
			$bdata = $board[$bid];
			?>
			  <tr>
			    <td class="mbox"><?php echo str_repeat($char, $level+1).' '.$bdata['name']; ?></td>
				<td class="mbox" width="10%" align="right"><?php echo $bdata['position']; ?>&nbsp;&nbsp;
				<a href="admin.php?action=forums&job=move&temp1=f_<?php echo $bdata['id']; ?>&int1=-1"><img src="admin/html/images/asc.gif" border="0" alt="Up"></a>&nbsp;
				<a href="admin.php?action=forums&job=move&temp1=f_<?php echo $bdata['id']; ?>&int1=1"><img src="admin/html/images/desc.gif" border="0" alt="Down"></a>
				</td>
			   <td class="mbox" width="30%">
				<form name="act" action="admin.php?action=locate" method="post">
			  		<select size="1" name="url" onchange="locate(this.value)">
			  		<option value="" selected="selected">Please choose</option>
					 <optgroup label="General">
					  <option value="admin.php?action=forums&job=edit&id=<?php echo $bdata['id']; ?>">Edit Forum</option>
					  <option value="admin.php?action=forums&job=delete&id=<?php echo $bdata['id']; ?>">Delete Forum</option>
					 </optgroup>
					 <?php if ($bdata['opt'] != 're') { ?>
					 <optgroup label="Permissions">
					  <option value="admin.php?action=forums&job=rights&id=<?php echo $bdata['id']; ?>">Manage Usergroups</option>
					  <option value="admin.php?action=forums&job=add_rights&id=<?php echo $bdata['id']; ?>">Add Usergroup</option>
					 </optgroup>
					 <optgroup label="Prefix">
					  <option value="admin.php?action=forums&job=prefix&id=<?php echo $bdata['id']; ?>">Manage</option>
					 </optgroup>
					 <optgroup label="Statistics">
					  <option value="admin.php?action=forums&job=updatestats&id=<?php echo $bdata['id']; ?>">Recount</option>
					 </optgroup>
					 <optgroup label="Moderators">
					  <option value="admin.php?action=forums&job=mods&id=<?php echo $bdata['id']; ?>">Manage</option>
					  <option value="admin.php?action=forums&job=mods_add&id=<?php echo $bdata['id']; ?>">Add</option>
					 </optgroup>
					 <?php } ?>
					</select>
					<input type="submit" value="Go" />
				</form>
			   </td> 
			  </tr>	
	    	<?php
	    	ForumSubs($sub, $cat, $board, $char, $level+2);
	    }
	}
}
if ($job == 'mods_ajax_changeperm') {
	$mid = $gpc->get('mid', int);
	$bid = $gpc->get('bid', int);
	$key = $gpc->get('key', str);
	if(!is_id($mid) || !is_id($bid) || empty($key)) {
		die('The ids or the key is not valid!');
	}
	$result = $db->query("SELECT {$key} FROM {$db->pre}moderators WHERE bid = '{$bid}' AND mid = '{$mid}' LIMIT 1", __LINE__, __FILE__);
	$perm = $db->fetch_assoc($result);
	if ($db->num_rows($result) == 0) {
		die('Not found!');
	}
	$perm = invert($perm[$key]);
	$db->query("UPDATE {$db->pre}moderators SET {$key} = '{$perm}' WHERE bid = '{$bid}' AND mid = '{$mid}' LIMIT 1", __LINE__, __FILE__);
	die(strval($perm));
}
elseif ($job == 'mods') {
	echo head();
	$orderby = $gpc->get('order', str);
	$bid = $gpc->get('id', int);
	
	$colspan = iif($bid > 0, '8', '9');

	$result = $db->query("
	SELECT m.*, u.name as user, c.name as cat, c.id AS cat_id
	FROM {$db->pre}moderators AS m 
		LEFT JOIN {$db->pre}user AS u ON u.id = m.mid 
		LEFT JOIN {$db->pre}forums AS c ON c.id = m.bid 
	".iif($bid > 0, "WHERE m.bid = '{$bid}'")." 
	ORDER BY ".iif($orderby == 'member' || $bid > 0, "u.name, c.name", "c.name, u.name")
	, __LINE__, __FILE__);
	?>
<form name="form" method="post" action="admin.php?action=forums&job=mods_delete<?php echo iif($bid > 0, '&id='.$bid); ?>">
 <table class="border">
  <tr> 
   <td class="obox" colspan="<?php echo $colspan; ?>"><span style="float: right;">[<a href="admin.php?action=forums&amp;job=mods_add&amp;id=<?php echo $bid; ?>">Add Moderator</a>]</span>Moderator Manager</td>
  </tr>
  <tr class="ubox">
    <td width="5%" rowspan="2">Delete</td>
    <td width="30%" rowspan="2">
    	<?php if ($bid == 0) { ?>
    	<a<?php echo iif($orderby == 'member', ' style="font-weight: bold;"'); ?> href="admin.php?action=forums&job=mods&order=member">
    		Name
    	</a>
    	<?php } else { ?>
    		Name
    	<?php } ?>
    </td>
    <?php if ($bid == 0) { ?>
    <td width="30%" rowspan="2">
    	<a<?php echo iif($orderby != 'member', ' style="font-weight: bold;"'); ?> href="admin.php?action=forums&job=mods&order=board">
    		Forum
    	</a>
    </td>
    <?php } ?>
    <td width="20%" rowspan="2">Period</td>
    <td width="21%" colspan="3" align="center">Status</td>
    <td width="14%" colspan="2" align="center">Topics</td>
  </tr>
  <tr class="ubox">
    <td width="7%">Rating</td>
    <td width="7%">Articles</td>
    <td width="7%">News</td>
    <td width="7%">move</td>
    <td width="7%">delete</td>
  </tr>
<?php 
	while ($row = $db->fetch_assoc($result)) {
	if ($row['time'] > -1) {
		$row['time'] = 'until '.gmdate('M d, Y',times($row['time']));
	}
	else {
	    $row['time'] = '<em>No restriction!</em>';
	}
    $p1 = ' onmouseover="HandCursor(this)" onclick="ajax_noki(this, \'action=forums&job=mods_ajax_changeperm&mid='.$row['mid'].'&bid='.$row['bid'].'&key=';
    $p2 = '\')"';
?>
  <tr> 
   <td class="mbox" width="5%" align="center"><input type="checkbox" value="<?php echo $row['mid'].'_'.$row['bid']; ?>" name="delete[]"></td>
   <td class="mbox" width="30%"><?php echo $row['user']; ?></td>
   <?php if ($bid == 0) { ?>
   <td class="mbox" width="30%"><a href="admin.php?action=forums&job=mods&id=<?php echo $row['cat_id']; ?>"><?php echo $row['cat']; ?></a></td>
   <?php } ?>
   <td class="mbox" width="20%"><?php echo $row['time']; ?></td>
   <td class="mbox" width="7%" align="center"><?php echo noki($row['s_rating'], $p1.'s_rating'.$p2); ?></td>
   <td class="mbox" width="7%" align="center"><?php echo noki($row['s_article'], $p1.'s_article'.$p2); ?></td>
   <td class="mbox" width="7%" align="center"><?php echo noki($row['s_news'], $p1.'s_news'.$p2); ?></td>
   <td class="mbox" width="7%" align="center"><?php echo noki($row['p_mc'], $p1.'p_mc'.$p2); ?></td>
   <td class="mbox" width="7%" align="center"><?php echo noki($row['p_delete'], $p1.'p_delete'.$p2); ?></td>
  </tr>
<?php } ?>
  <tr> 
   <td class="ubox" width="100%" colspan="<?php echo $colspan; ?>" align="center"><input type="submit" name="Submit" value="Delete"></td> 
  </tr>
 </table>
</form> 
	<?php
	echo foot();
}
elseif ($job == 'mods_delete') {
	echo head();
	$id = $gpc->get('id', int);
	if (count($gpc->get('delete', none)) > 0) {
		$deleteids = array();
		foreach ($gpc->get('delete', none) as $did) {
			list($mid, $bid) = explode('_',$did);
			$mid = $gpc->save_int($mid);
			$bid = $gpc->save_int($bid);
			$deleteids[] = " (mid = '{$mid}' AND bid = '{$bid}') "; 
		}
		$db->query("DELETE FROM {$db->pre}moderators WHERE ".implode(' OR ',$deleteids), __LINE__, __FILE__);
		$anz = $db->affected_rows();
		$delobj = $scache->load('index-moderators');
		$delobj->delete();
		ok('admin.php?action=forums&job=mods'.iif($id > 0, '&id='.$id), $anz.' entries deleted!');
	}
	else {
		error('admin.php?action=forums&job=mods'.iif($id > 0, '&id='.$id), 'Invalid data sent!');
	}
}
elseif ($job == 'mods_add') {
	echo head();
    $id = $gpc->get('id', int);
	?>
<form name="form" method="post" action="admin.php?action=forums&amp;job=mods_add2">
<?php echo iif(is_id($id), '<input type="hidden" name="id" value="'.$id.'" /><input type="hidden" name="bid" value="'.$id.'" />'); ?>
 <table class="border">
  <tr> 
   <td class="obox" colspan="2">Add Moderator</td>
  </tr>
  <tr>
   <td class="mbox" width="50%">Forum:</td>
   <td class="mbox" width="50%">
   <?php
	$catbid = $scache->load('cat_bid');
	$boards = $catbid->get();
   	if (!isset($boards[$id]['name'])) {
   		echo SelectBoardStructure('id', ADMIN_SELECT_FORUMS);
    }
   	else {
		echo $boards[$id]['name'];
   	}
   ?>
   </td>
  </tr>
  <tr>
   <td class="mbox" width="50%">Username:</td>
   <td class="mbox" width="50%">
   	<input type="text" name="name" id="name" size="50" onkeyup="ajax_searchmember(this, 'sugg');" /><br />
   	<span class="stext">Suggestions: <span id="sugg">-</span></span>
   </td> 
  </tr>
  <tr>
   <td class="mbox" width="50%">Period:<br />
   <span class="stext">Entering a date here will cause that the moderator has the specified permissions only until the entered date. The moderator will loose his permissions at the specified date at 0 o'clock! This is optional!</span></td>
   <td class="mbox" width="50%">Day: <input type="text" name="day" size="4" />&nbsp;&nbsp;&nbsp;&nbsp;Month: <input type="text" name="month" size="4" />&nbsp;&nbsp;&nbsp;&nbsp;Year: <input type="text" name="weekday" size="6" /></td> 
  </tr>
  <tr>
   <td class="mbox" width="50%">Status: Is allowed to...</td>
   <td class="mbox" width="50%">
   <input type="checkbox" name="ratings" value="1" checked="checked" /> set Ratings (Good, Bad)<br />
   <input type="checkbox" name="news" value="1" /> specify a topic as news<br />
   <input type="checkbox" name="article" value="1" /> specify a topic as article
   </td> 
  </tr>
  <tr>
   <td class="mbox" width="50%">Manage Posts: Is allowed to...</td>
   <td class="mbox" width="50%">
   <input type="checkbox" name="delete" value="1" checked="checked" /> delete posts and topics<br />
   <input type="checkbox" name="move" value="1" checked="checked" /> move posts and topics
   </td> 
  </tr>
  </tr>
  <tr>
   <td class="ubox" width="100%" colspan="2" align="center"><input type="submit" name="Submit" value="Add"></td> 
  </tr>
 </table>
</form> 
	<?php
	echo foot();
}
elseif ($job == 'mods_add2') {
	echo head();
	
    $id = $gpc->get('id', int);
    $bid = $gpc->get('bid', int);
    $temp1 = $gpc->get('name', str);
    $month = $gpc->get('month', int);
    $day = $gpc->get('day', int);
    $weekday = $gpc->get('weekday', int);
	if (!is_id($id)) {
		error('admin.php?action=forums&job=manage', 'Forum or Category was not found on account of an invalid ID.');
	}
	$uid = $db->fetch_num($db->query('SELECT id FROM '.$db->pre.'user WHERE name = "'.$temp1.'" LIMIT 1', __LINE__, __FILE__));
	if ($uid[0] < 1) {
		error('admin.php?action=forums&job=mods_add'.iif($bid > 0, '&id='.$id), 'Member not found!');
	}
	if ($month > 0 && $day > 0 && $weekday > 0) {
		$timestamp = "'".mktime(0, 0, 0, $month, $day, $weekday, -1)."'";
	}
	else {
		$timestamp = 'NULL';
	}
	
	$news = $gpc->get('news', int);
	$article = $gpc->get('article', int);
	$rating = $gpc->get('rating', int);
	$move = $gpc->get('move', int);
	$delete = $gpc->get('delete', int);
	
	$db->query("
	INSERT INTO {$db->pre}moderators (mid, bid, s_rating, s_news, s_article, p_delete, p_mc, time) 
	VALUES ('{$uid[0]}', '{$id}', '{$rating}', '{$news}', '{$article}', '{$delete}', '{$move}', {$timestamp})
	", __LINE__, __FILE__);
	
	if ($db->affected_rows() == 1) {
		$delobj = $scache->load('index-moderators');
		$delobj->delete();
		ok('admin.php?action=forums&job=mods'.iif($bid > 0, '&id='.$id), 'Moderator successfully added!');
	}
	else {
		error('admin.php?action=forums&job=mods'.iif($bid > 0, '&id='.$id), 'Could not insert data into database.');
	}
}
elseif ($job == 'delete') {
	echo head();
	?>
	<table class="border">
	<tr><td class="obox">Delete Forum</td></tr>
	<tr><td class="mbox">
	    <p align="center">Do you really want to delete this forum?</p>
	    <p align="center">
	        <a href="admin.php?action=forums&amp;job=delete2&amp;id=<?php echo $_GET['id']; ?>">
	        	<img alt="Yes" border="0" src="admin/html/images/yes.gif" /> Yes
	        </a>
	        &nbsp;&nbsp;&nbsp;&nbsp;
	        <a href="javascript: history.back(-1);"><img border="0" alt="No" src="admin/html/images/no.gif" /> No</a>
	    </p>
	</td></tr>
	</table>
	<?php
	echo foot();
}
elseif ($job == 'delete2') {
	echo head();
	$id = array();
	$result = $db->query("SELECT id FROM {$db->pre}topics WHERE board = '{$_GET['id']}'", __LINE__, __FILE__);
		if ($db->num_rows($result) > 0) {
		while ($row = $db->fetch_assoc($result)) {
			$id[] = $row['id'];
		}
		$ids = implode(',', $id);
	
		$db->query ("DELETE FROM {$db->pre}replies WHERE board = '{$_GET['id']}'",__LINE__,__FILE__);
		$uresult = $db->query ("SELECT id, source FROM {$db->pre}uploads WHERE topic_id IN({$ids})",__LINE__,__FILE__);
		while ($urow = $db->fetch_assoc($uresult)) {
			$filesystem->unlink('uploads/topics/'.$urow['source']);
			$thumb = 'uploads/topics/thumbnails/'.$urow['id'].get_extension($urow['source'], true);
			if (file_exists($thumb)) {
				$filesystem->unlink($thumb);
			}
		}
		$db->query ("DELETE FROM {$db->pre}uploads WHERE topic_id IN({$ids})",__LINE__,__FILE__);
		$db->query ("DELETE FROM {$db->pre}postratings WHERE tid IN({$ids})",__LINE__,__FILE__);
		$db->query ("DELETE FROM {$db->pre}abos WHERE tid IN({$ids})",__LINE__,__FILE__);
		$db->query ("DELETE FROM {$db->pre}topics WHERE board = '{$_GET['id']}'",__LINE__,__FILE__);
		$votes = $db->query("SELECT id FROM {$db->pre}vote WHERE tid IN({$ids})",__LINE__,__FILE__);
		$voteaids = array();
		while ($row = $db->fetch_num($votes)) {
			$voteaids[] = $row[0];
		}
		if (count($voteaids) > 0) {
			$db->query ("DELETE FROM {$db->pre}votes WHERE id IN(".implode(',', $voteaids).")",__LINE__,__FILE__);
		}
		$db->query ("DELETE FROM {$db->pre}vote WHERE tid IN({$ids})",__LINE__,__FILE__);
	}
	$db->query("DELETE FROM {$db->pre}fgroups WHERE bid = '{$_GET['id']}'", __LINE__, __FILE__);
	$db->query("DELETE FROM {$db->pre}moderators WHERE bid = '{$_GET['id']}'", __LINE__, __FILE__);
	$db->query("DELETE FROM {$db->pre}prefix WHERE bid = '{$_GET['id']}'", __LINE__, __FILE__);
	$db->query("DELETE FROM {$db->pre}forums WHERE id = '{$_GET['id']}' LIMIT 1", __LINE__, __FILE__);
	
	$delobj = $scache->load('cat_bid');
	$delobj->delete();
	$delobj = $scache->load('forumtree');
	$delobj->delete();
	$delobj = $scache->load('parent_forums');
	$delobj->delete();
	
	ok('admin.php?action=forums&job=manage', 'Board was successfully deleted!');
}
elseif ($job == 'edit') {
	echo head();
	$id = $gpc->get('id', int);
	$result = $db->query('SELECT * FROM '.$db->pre.'forums WHERE id = '.$id, __LINE__, __FILE__);
	if ($db->num_rows() == 0) {
		error('admin.php?action=forums&job=manage', 'Invalid ID given');
	}
	$row = $db->fetch_assoc($result);
	?>
<form name="form" method="post" action="admin.php?action=forums&job=editforum2&id=<?php echo $id; ?>">
 <table class="border" border="0" cellspacing="0" cellpadding="4" align="center">
  <tr> 
   <td class="obox" colspan="2">Edit a forum</td>
  </tr>
  <tr> 
   <td class="mbox" width="50%">Title:</td>
   <td class="mbox" width="50%"><input type="text" name="name" size="50" value="<?php echo $gpc->prepare($row['name']); ?>"></td> 
  </tr>
  <tr> 
   <td class="mbox" width="50%">Description:</font><br /><font class="stext">Optional. HTML is allowed.</td>
   <td class="mbox" width="50%"><textarea name="desc" rows="4" cols="50"><?php echo $row['desc']; ?></textarea></td> 
  </tr>
  <tr> 
   <td class="mbox" width="50%">Parent Forum/Category:</font></td>
   <td class="mbox" width="50%">
   <select name="parent">
   <option value="NULL">Do not change</option>
   <?php
	$forumtree = $scache->load('forumtree');
	$tree = $forumtree->get();
	$categories_obj = $scache->load('categories');
	$categories = $categories_obj->get();
	$catbid = $scache->load('cat_bid');
	$boards = $catbid->get();
	AdminSelectForum($tree, $categories, $boards);
   ?>
   </select>
   </td> 
  </tr>
  <tr> 
   <td class="mbox" width="50%">Forum Link:<br /><span class="stext">Entering a URL here will cause anyone clicking the forum link to be redirected to that URL.</span></td>
   <td class="mbox" width="50%"><input type="text" value="<?php echo iif($row['opt'] == 're', $row['optvalue']); ?>" name="link" size="50" id="dis1" onmouseover="disable(this)" onmouseut="disable(this)"></td> 
  </tr>
  <tr> 
   <td class="mbox" width="50%">Forum Password:<br /><span class="stext">Subforums are protected with this password, too!</span></td>
   <td class="mbox" width="50%"><input type="text" value="<?php echo iif($row['opt'] == 'pw', $row['optvalue']); ?>" name="text" size="50" id="dis2" onmouseover="disable(this)" onmouseut="disable(this)"></td> 
  </tr>
  <tr> 
   <td class="mbox" width="50%">Number of Posts per Page:<br /><span class="stext">0 = Use default value (<?php echo $config['topiczahl']; ?>)</span></td>
   <td class="mbox" width="50%"><input type="text" name="topiczahl" size="5" value="<?php echo $row['topiczahl']; ?>" /></td> 
  </tr>
  <tr> 
   <td class="mbox" width="50%">Number of Topics per Forumpage:<br /><span class="stext">0 = Use default value (<?php echo $config['forumzahl']; ?>)</span></td>
   <td class="mbox" width="50%"><input type="text" name="forumzahl" size="5" value="<?php echo $row['forumzahl']; ?>" /></td> 
  </tr>
  <tr> 
   <td class="mbox" width="50%">Hide forum from users without authorization:<br /><span class="stext">Forum will not appear if it is locked and option is checked. This only affects forums without password.</span></td>
   <td class="mbox" width="50%"><input type="checkbox" name="invisible" value="1"<?php echo iif($row['invisible'] == 1, ' checked="checked"'); ?> /></td> 
  </tr>
  <tr> 
   <td class="ubox" colspan="2" align="center"><input type="submit" name="Submit" value="Submit"></td> 
  </tr>
 </table>
</form> 
	<?php
	echo foot();
}
elseif ($job == 'editforum2') {
	echo head();

	$id = $gpc->get('id', int);
	$name = $gpc->get('name', str);
	$desc = $gpc->get('desc', str);
	$pw = $gpc->get('pw', str);
	$link = $gpc->get('link', str);
	$parent = $gpc->get('parent', str);
	$invisible = $gpc->get('invisible', int);
	$topiczahl = $gpc->get('topiczahl', int);
	$forumzahl = $gpc->get('forumzahl', int);

	if (!$id) {
		error('admin.php?action=forums&job=edit&id='.$id, 'EInvalid ID given');
	}

	$option = '';
	if ($parent != 'NULL') {
		if (preg_match("/c_\d{1,}/", $parent) == 1) {
			$cid = str_replace("c_", "", $parent);
			$array = $db->fetch_num($db->query("SELECT bid FROM {$db->pre}forums WHERE cid = $cid LIMIT 1", __LINE__, __FILE__));
			$bid = $array[0];
		}
		elseif (preg_match("/f_\d{1,}/", $parent) == 1) {
			$bid = str_replace("f_", "", $parent);
			$array = $db->fetch_num($db->query("SELECT cid FROM {$db->pre}forums WHERE bid = $bid LIMIT 1", __LINE__, __FILE__));
			if ($array[0] < 1) {
				$array2 = $db->fetch_num($db->query("SELECT name, c.desc FROM {$db->pre}forums AS c WHERE id = $bid LIMIT 1", __LINE__, __FILE__));
				$db->query("INSERT INTO {$db->pre}categories (name, description, position) VALUES ('{$array2[0]}', '{$array2[1]}', 0)", __LINE__, __FILE__);
				$cid = $db->insert_id();
			}
			else {
				$cid = $array[0];
			}
		}
		else {
			error('admin.php?action=forums&job=edit&id='.$id, 'Could not retrieve forum or categorie!');
		}
		$option .= ", bid = '$bid', cid = '$cid'";
	}
	
	if (strlen($name) < 2) {
		error('admin.php?action=forums&job=edit&id='.$id, 'Name is too short (< 2 chars)');
	}
	if (strlen($name) > 200) {
		error('admin.php?action=forums&job=edit&id='.$id, 'Name is too long (> 200 chars)');
	}
	if (strlen($link) > 0) {
		$opt = 're';
		$optvalue = $link;
	}
	elseif (strlen($pw) > 0) {
		$opt = 'pw';
		$optvalue = $pw;
		$invisible = 0;
	}
	else {
		$opt = '';
		$optvalue = '';	
	}
	$db->query("UPDATE {$db->pre}forums SET name = '{$name}', `desc` = '{$desc}', forumzahl = '{$forumzahl}', topiczahl = '{$topiczahl}', invisible = '{$invisible}', opt = '{$opt}', optvalue = '{$optvalue}' {$option} WHERE id = '{$id}' LIMIT 1", __LINE__, __FILE__);
	
	$delobj = $scache->load('categories');
	$delobj->delete();
	$delobj = $scache->load('cat_bid');
	$delobj->delete();
	$delobj = $scache->load('forumtree');
	$delobj->delete();
	$delobj = $scache->load('parent_forums');
	$delobj->delete();
	
	ok('admin.php?action=forums&job=manage','Forum changed successfully!');
}
elseif ($job == 'addforum') {
	echo head();
	
	$forumtree = $scache->load('forumtree');
	$tree = $forumtree->get();
	$categories_obj = $scache->load('categories');
	$categories = $categories_obj->get();
	$catbid = $scache->load('cat_bid');
	$boards = $catbid->get();
	
	?>
<form name="form" method="post" action="admin.php?action=forums&job=addforum2">
 <table class="border">
  <tr> 
   <td class="obox" colspan="2">Add a new forum</td>
  </tr>
  <tr> 
   <td class="mbox" width="50%">Title:</td>
   <td class="mbox" width="50%"><input type="text" name="name" size="50" /></td> 
  </tr>
  <tr> 
   <td class="mbox" width="50%">Description:<br /><span class="stext">Optional. HTML is allowed.</span></td>
   <td class="mbox" width="50%"><textarea name="desc" rows="4" cols="50"></textarea></td> 
  </tr>
  <tr> 
   <td class="mbox" width="50%">Parent Forum/Category:</font></td>
   <td class="mbox" width="50%">
	<select name="parent" size="1">
	 <?php AdminSelectForum($tree, $categories, $boards); ?>
	</select>
   </td> 
  </tr>
  <tr> 
   <td class="mbox" width="50%">Forum Link :<br /><span class="stext">Entering a URL here will cause anyone clicking the forum link to be redirected to that URL.</span></td>
   <td class="mbox" width="50%"><input type="text" name="link" size="50" id="dis1" onchange="disable(this)" /></td> 
  </tr>
  <tr> 
   <td class="mbox" width="50%">Forum Password:<br /><span class="stext">Subforums are protected with this password, too!</span></td>
   <td class="mbox" width="50%"><input type="text" name="pw" size="50" id="dis2" onchange="disable(this)" /></td> 
  </tr>
  <tr> 
   <td class="mbox" width="50%">Number of Posts per Page:<br /><span class="stext">0 = Use default value (<?php echo $config['topiczahl']; ?>)</span></td>
   <td class="mbox" width="50%"><input type="text" name="topiczahl" size="5" value="0" /></td> 
  </tr>
  <tr> 
   <td class="mbox" width="50%">Number of Topics per Forumpage:<br /><span class="stext">0 = Use default value (<?php echo $config['forumzahl']; ?>)</span></td>
   <td class="mbox" width="50%"><input type="text" name="forumzahl" size="5" value="0" /></td> 
  </tr>
  <tr> 
   <td class="mbox" width="50%">Hide forum from users without authorization:<br /><span class="stext">Forum will not appear if it is locked and option is checked. This only affects forums without password.</span></td>
   <td class="mbox" width="50%"><input type="checkbox" name="invisible" value="1" /></td> 
  </tr>
  <tr> 
   <td class="mbox" width="50%">Copy permissions from:<br /><span class="stext">The forum will have the same permissions as the one you select here. If no forum is selected the default settings are used. <em>Caution: This is experimental! Use with care and report bugs, please.</em></span></td>
   <td class="mbox" width="50%">
	<select name="copypermissions">
   		<option value="0">Default</option>
   		<?php AdminSelectForum($tree, $categories, $boards); ?>
   	</select>
   </td> 
  </tr>
  <tr> 
   <td class="mbox" width="50%">Sort in:</font></td>
   <td class="mbox" width="50%"><select name="sort">
   <option value="0">before existing forums</option>
   <option value="1" selected="selected">after existing forums</option>
   </select></td> 
  </tr>
  <tr> 
   <td class="ubox" width="100%" colspan="2" align="center"><input type="submit" name="Submit" value="Submit" /></td> 
  </tr>
 </table>
</form> 
	<?php
	echo foot();
}
elseif ($job == 'addforum2') {
	echo head();
	
	$name = $gpc->get('name', str);
	$desc = $gpc->get('desc', str);
	$sort = $gpc->get('sort', int);
	$pw = $gpc->get('pw', str);
	$link = $gpc->get('link', str);
	$parent = $gpc->get('parent', str);
	$invisible = $gpc->get('invisible', int);
	$topiczahl = $gpc->get('topiczahl', int);
	$forumzahl = $gpc->get('forumzahl', int);
	$perm = $gpc->get('copypermissions', str);
	
	if (preg_match("/c_\d{1,}/", $parent) == 1) {
		$cid = str_replace("c_", "", $parent);
		$array = $db->fetch_num($db->query("SELECT bid FROM {$db->pre}forums WHERE cid = '{$cid}' LIMIT 1", __LINE__, __FILE__));
		$bid = $array[0];
	}
	elseif (preg_match("/f_\d{1,}/", $parent) == 1) {
		$bid = str_replace("f_", "", $parent);
		$array = $db->fetch_num($db->query("SELECT cid FROM {$db->pre}forums WHERE bid = '{$bid}' LIMIT 1", __LINE__, __FILE__));
		if ($array[0] < 1) {
			$array2 = $db->fetch_num($db->query("SELECT name, description FROM {$db->pre}forums WHERE id = $bid LIMIT 1", __LINE__, __FILE__));
			$db->query("INSERT INTO {$db->pre}categories (name, description, position) VALUES ('{$array2[0]}', '{$array2[1]}', 0)", __LINE__, __FILE__);
			$cid = $db->insert_id();
		}
		else {
			$cid = $array[0];
		}
	}
	else {
		error('admin.php?action=forums&job=addforum','Could not retrieve forum or category!');
	}
	
	if ($sort == 1) {
		$sortx = $db->fetch_num($db->query("SELECT MAX(position) FROM {$db->pre}forums WHERE cid = {$cid} LIMIT 1", __LINE__, __FILE__));
		$sort = $sortx[0]+1;
	}
	elseif ($sort == 0) {
		$sortx = $db->fetch_num($db->query("SELECT MIN(position) FROM {$db->pre}forums WHERE cid = {$cid} LIMIT 1", __LINE__, __FILE__));
		$sort = $sortx[0]-1;
	}
	else {
		$sort = 0;
	}
	if (strlen($name) < 2) {
		error('admin.php?action=forums&job=addforum', 'Name is too short (< 2 chars)');
	}
	if (strlen($name) > 200) {
		error('admin.php?action=forums&job=addforum', 'Name is too long (> 200 chars)');
	}
	if (strlen($link) > 0) {
		$opt = 're';
		$optvalue = $link;
	}
	elseif (strlen($pw) > 0) {
		$opt = 'pw';
		$optvalue = $pw;
		$invisible = 0;
	}
	else {
		$opt = '';
		$optvalue = '';	
	}
	
	$db->query("
	INSERT INTO {$db->pre}forums (name, `desc`, bid, cid, position, opt, optvalue, forumzahl, topiczahl, invisible)
	VALUES ('{$name}', '{$desc}', '{$bid}', '{$cid}', '{$sort}', '{$opt}', '{$optvalue}','{$forumzahl}','{$topiczahl}','{$invisible}')
	", __LINE__, __FILE__);

	if (preg_match("/f_\d{1,}/", $perm) == 1) {
		$newid = $db->insert_id();
		$fid = str_replace("f_", "", $perm);
		$result = $db->query("SELECT * FROM {$db->pre}fgroups WHERE bid = '{$fid}'", __LINE__, __FILE__);
		while($row = $db->fetch_assoc($result)) {
			unset($row['bid'], $row['fid']);
			$keys = array_keys($row);
			sort($keys, SORT_STRING);
			ksort($row, SORT_STRING);
			$row_str = implode("','", $row);
			$keys_str = implode(',', $keys);
			$db->query("INSERT INTO {$db->pre}fgroups ({$keys_str}, bid) VALUES ('{$row_str}', '{$newid}')", __LINE__, __FILE__);
		}
	}

	$delobj = $scache->load('cat_bid');
	$delobj->delete();
	$delobj = $scache->load('forumtree');
	$delobj->delete();
	$delobj = $scache->load('categories');
	$delobj->delete();
	$delobj = $scache->load('parent_forums');
	$delobj->delete();
	
	ok('admin.php?action=forums&job=addforum', 'Forum successfully added!');
}
elseif ($job == 'manage') {
	send_nocache_header();
	echo head();
	?>
	<table class="border">
	<tr><td class="obox" colspan="3">Manage Forums &amp; Categories</td></tr>
	<tr> 
	<td class="ubox" width="50%"><b>Title</b></td>
	<td class="ubox" width="20%"><b>Ordering</b></td> 
	<td class="ubox" width="30%"><b>Action</b></td> 
	</tr>
	<?php
	$forumtree = $scache->load('forumtree');
	$tree = $forumtree->get();
	$categories_obj = $scache->load('categories');
	$categories = $categories_obj->get();
	$catbid = $scache->load('cat_bid');
	$boards = $catbid->get();
	ForumSubs($tree, $categories, $boards);
	?>
	</table>
	<?php
	echo foot();
}
elseif ($job == 'updatestats') {
	echo head();
	UpdateBoardStats($gpc->get('id', int));
	ok('admin.php?action=forums&job=manage', 'Statistics successfully recounted!');
}
elseif ($job == 'move') {
    $id = $gpc->get('temp1', str);
	if (strpos($id, '_') === false) {
		echo head();
		error('admin.php?action=forums&job=manage', 'No correct data specified.');
	}
	list($type, $gid) = explode('_',$id);
	$gid = $gpc->save_int($gid);
	if (!is_id($gid)) {
		echo head();
		error('admin.php?action=forums&job=manage', 'Forum or Category was not found on account of an invalid ID.');
	}
	$move = $gpc->get('int1', int);
	
	if ($move == -1 && $type == 'c') {
		$db->query('UPDATE '.$db->pre.'categories SET position = position-1 WHERE id = '.$gid, __LINE__, __FILE__);
	}
	elseif ($move == 1 && $type == 'c') {
		$db->query('UPDATE '.$db->pre.'categories SET position = position+1 WHERE id = '.$gid, __LINE__, __FILE__);
	}
	elseif ($move == -1 && $type == 'f') {
		$db->query('UPDATE '.$db->pre.'forums SET position = position-1 WHERE id = '.$gid, __LINE__, __FILE__);
	}
	elseif ($move == 1 && $type == 'f') {
		$db->query('UPDATE '.$db->pre.'forums SET position = position+1 WHERE id = '.$gid, __LINE__, __FILE__);
	}
	else {
		echo head();
		error('admin.php?action=forums&job=manage','Invalid data sent!');
	}

	$delobj = $scache->load('forumtree');
	$delobj->delete();
	if ($type == 'c') {
		$delobj = $scache->load('categories');
		$delobj->delete();
	}
	else{
		$delobj = $scache->load('cat_bid');
		$delobj->delete();
	}
	
	viscacha_header('Location: admin.php?action=forums&job=manage');
}
elseif ($job == 'rights') {
	echo head();
	$id = $gpc->get('id', int);
	if ($id == 0) {
		error('admin.pgp?action=forums&job=manage', 'Forum not found');
	}
	$result = $db->query("SELECT f.*, g.name, g.title FROM {$db->pre}fgroups AS f LEFT JOIN {$db->pre}groups AS g ON g.id = f.gid WHERE f.bid = '{$id}' ORDER BY f.gid", __LINE__, __FILE__);
	$cache = array();
	?>
<form name="form" method="post" action="admin.php?action=forums&job=delete_rights&id=<?php echo $id; ?>">
 <table class="border">
  <tr> 
   <td class="obox" colspan="10"><span style="float: right;">[<a href="admin.php?action=forums&job=add_rights&id=<?php echo $id; ?>">Add Usergroup</a>]</span>Forum Permission Manager</td>
  </tr>
  <tr>
  	<td class="ubox" valign="bottom"><b>Delete</b></td>
    <td class="ubox" valign="bottom"><b>Name / Public Title</b></td>
   	<td class="ubox" valign="bottom"><?php txt2img('Download Attachements'); ?></td>
   	<td class="ubox" valign="bottom"><?php txt2img('View Forum'); ?></td>
   	<td class="ubox" valign="bottom"><?php txt2img('Start a new Topic'); ?></td>
   	<td class="ubox" valign="bottom"><?php txt2img('Write a reply'); ?></td>
   	<td class="ubox" valign="bottom"><?php txt2img('Start a Poll'); ?></td>
   	<td class="ubox" valign="bottom"><?php txt2img('Add Attachements'); ?></td>
   	<td class="ubox" valign="bottom"><?php txt2img('Edit own Posts'); ?></td>
   	<td class="ubox" valign="bottom"><?php txt2img('Can vote'); ?></td>
  </tr>
  <?php while ($row = $db->fetch_assoc($result)) { ?>
  <tr>
  	<td class="mbox">
	<input type="checkbox" name="delete[]" value="<?php echo $row['fid']; ?>"></td>
    <td class="mbox">
    <?php
    if ($row['gid'] > 0) {
    	echo $row['name'].' / '.$row['title'];
    } else {
    	echo '<i>Valid for all groups except the groups shown below!</i>';
    }
    $p1 = ' onmouseover="HandCursor(this)" onclick="ajax_noki(this, \'action=forums&job=ajax_changeperm&id='.$row['fid'].'&key=';
    $p2 = '\')"';
    ?>
    </td>
   	<td class="mbox"><?php echo noki($row['f_downloadfiles'], $p1.'f_downloadfiles'.$p2); ?></td>
   	<td class="mbox"><?php echo noki($row['f_forum'], $p1.'f_forum'.$p2); ?></td>
   	<td class="mbox"><?php echo noki($row['f_posttopics'], $p1.'f_posttopics'.$p2); ?></td>
   	<td class="mbox"><?php echo noki($row['f_postreplies'], $p1.'f_postreplies'.$p2); ?></td>
   	<td class="mbox"><?php echo noki($row['f_addvotes'], $p1.'f_addvotes'.$p2); ?></td>
   	<td class="mbox"><?php echo noki($row['f_attachments'], $p1.'f_attachments'.$p2); ?></td>
   	<td class="mbox"><?php echo noki($row['f_edit'], $p1.'f_edit'.$p2); ?></td>
   	<td class="mbox"><?php echo noki($row['f_voting'], $p1.'f_voting'.$p2); ?></td>
  </tr>
  <?php } ?>
  <tr> 
   <td class="ubox" width="100%" colspan="10" align="center"><input type="submit" name="Submit" value="Delete"></td> 
  </tr>
 </table>
</form> 
	<?php
	echo foot();
}
elseif ($job == 'ajax_changeperm') {
	$id = $gpc->get('id', int);
	$key = $gpc->get('key', str);
	if(!is_id($id) || empty($key)) {
		die('The id or the key is not valid!');
	}
	$result = $db->query("SELECT f.{$key} FROM {$db->pre}fgroups AS f WHERE f.fid = '{$id}' LIMIT 1", __LINE__, __FILE__);
	$perm = $db->fetch_assoc($result);
	if ($db->num_rows($result) == 0) {
		die('Not found!');
	}
	$perm = invert($perm[$key]);
	$db->query("UPDATE {$db->pre}fgroups AS f SET f.{$key} = '{$perm}' WHERE f.fid = '{$id}' LIMIT 1", __LINE__, __FILE__);
	die(strval($perm));
}
elseif ($job == 'add_rights') {
	echo head();
	$id = $gpc->get('id', int);
	if ($id == 0) {
		error('admin.pgp?action=forums&job=manage', 'Forum not found');
	}
	$result = $db->query("SELECT id, name FROM {$db->pre}groups ORDER BY admin DESC , guest ASC , core ASC", __LINE__, __FILE__);
	$result2 = $db->query("SELECT gid FROM {$db->pre}fgroups WHERE bid = '{$id}'", __LINE__, __FILE__);
	$cache = array();
	$cache2 = array();
	while ($row = $db->fetch_num($result2)) {
		$cache2[] = $row[0];
	}
	while ($row = $db->fetch_assoc($result)) {
		if (in_array($row['id'],$cache2) == FALSE) {
			$cache[] = $row;
		}
	}
	?>
<form name="form" method="post" action="admin.php?action=forums&job=add_rights2&id=<?php echo $id; ?>">
 <table class="border">
  <tr> 
   <td class="obox" colspan="2">Add a new Usergroup - Settings and Permissions</td>
  </tr>
  <tr> 
   <td class="ubox" colspan="2">Settings:</td>
  </tr>
  <tr>
      <td class="mbox">Use for group(s):<br /><span class="stext">Choose the usergroup (or all groups) which will be affected by the below specified permissions.</span></td>
      <td class="mbox">
      <select name="int1">
      <option value="0">All Groups</option>
      <?php
      foreach($cache as $row) {
      	echo '<option value="'.$row['id'].'">'.$row['name'].'</option>';
      }
      ?>
      </select>
      </td>
  </tr>
  <tr> 
   <td class="ubox" colspan="2">Permissions:</td>
  </tr>
  <?php foreach ($glk_forums as $key) { ?>
  <tr>
   <td class="mbox" width="50%"><?php echo $gls[$key]; ?><br /><span class="stext"><?php echo $gll[$key]; ?></span></td>
   <td class="mbox" width="50%"><input type="checkbox" name="<?php echo $key; ?>" id="<?php echo $key; ?>" value="1" /></td>
  </tr>
  <?php } ?>
  <tr> 
   <td class="ubox" colspan="2" align="center"><input type="submit" name="Submit" value="Add" /></td> 
  </tr>
 </table>
</form>
	<?php
	echo foot();
}
elseif ($job == 'add_rights2') {
	echo head();

	$id = $gpc->get('id', int);
	$int1 = $gpc->get('int1', int);

	$db->query('SELECT * FROM '.$db->pre.'fgroups WHERE bid = "'.$id.'" AND gid = "'.$int1.'"', __LINE__, __FILE__);
	if ($db->num_rows() > 0) {
		error('admin.php?action=forums&job=rights&id='.$id, 'F�r die angegebene Gruppe besteht schon ein Eintrag!');
	}

	// ToDo: G�ste-Limitierungen (kein voten und editieren) beachten!

	$db->query('
	INSERT INTO '.$db->pre.'fgroups (bid,gid,f_downloadfiles,f_forum,f_posttopics,f_postreplies,f_addvotes,f_attachments,f_edit,f_voting) 
	VALUES ("'.$id.'","'.$int1.'","'.$gpc->get('downloadfiles', int).'","'.$gpc->get('forum', int).'","'.$gpc->get('posttopics', int).'","'.$gpc->get('postreplies', int).'","'.$gpc->get('addvotes', int).'","'.$gpc->get('attachments', int).'","'.$gpc->get('edit', int).'","'.$gpc->get('voting', int).'")
	', __LINE__, __FILE__);
	if ($db->affected_rows() == 1) {
		ok('admin.php?action=forums&job=rights&id='.$id, 'Data successfully inserted!');
	}
	else {
		error('admin.php?action=forums&job=add_rights&id='.$id, 'There was an error while inserting data!');
	}
}
elseif ($job == 'delete_rights') {
	echo head();
	$id = $gpc->get('id', int);
	if (!is_id($id)) {
		error('admin.pgp?action=forums&job=manage', 'Forum not found');
	}
	$did = $gpc->get('delete', arr_int);
	if (count($did) > 0) {
		$db->query('DELETE FROM '.$db->pre.'fgroups WHERE fid IN('.implode(',',$did).') AND bid = "'.$id.'"', __LINE__, __FILE__);
		$anz = $db->affected_rows();	
		ok('admin.php?action=forums&job=rights&id='.$id, $anz.' entries deleted!');
	}
	else {
		error('admin.php?action=forums&job=rights&id='.$id, 'You have not chosen which entry shall be deleted!');
	}
}
elseif ($job == 'cat_add') {
	echo head();
	?>
<form name="form" method="post" action="admin.php?action=forums&job=cat_add2">
 <table class="border">
  <tr> 
   <td class="obox" colspan="2">Add Category</td>
  </tr>
  <tr>
   <td class="mbox" width="50%">Name:<br />
   <span class="stext">Maximum: 200 characters</span>
   </td>
   <td class="mbox" width="50%"><input type="text" name="name" size="50" /></td> 
  </tr>
  <tr>
   <td class="mbox" width="50%">Description:<br />
   <span class="stext">
   You can optionally type in a short description for this category.<br />
   HTML is allowed; BB-Code is not allowed!</span></td>
   <td class="mbox" width="50%"><textarea name="description" rows="2" cols="50"></textarea></td> 
  </tr>
  <tr>
   <td class="mbox" width="50%">Position:</td>
   <td class="mbox" width="50%">
    <select name="sort_where">
     <option value="-1">Before</option>
     <option value="1" selected="selected">After</option>
    </select>&nbsp;<?php echo SelectBoardStructure('sort', ADMIN_SELECT_CATEGORIES); ?>
   </td>
  </tr>
  <tr>
   <td class="mbox" width="50%">Parent Forum:</td>
   <td class="mbox" width="50%">
   	<select name="parent" size="1">
   	 <option value="0" selected="selected">No one</option>
   	 <?php echo SelectBoardStructure('parent', ADMIN_SELECT_FORUMS, null, true); ?>
   	</select>
   </td> 
  </tr>
  <tr> 
   <td class="ubox" width="100%" colspan="2" align="center"><input type="submit" name="Submit" value="Add" /></td> 
  </tr>
 </table>
</form>
	<?php
	echo foot();
}
elseif ($job == 'cat_add2') {
	echo head();
	
	$sort = $gpc->get('sort', int);
	$sortx = $gpc->get('sort_where', int);
	$parent = $gpc->get('parent', int);
	$name = $gpc->get('name', str);
	$description = $gpc->get('description', str);
	$position = null;
	
	if (strlen($name) < 2) {
		error('admin.php?action=forums&job=cat_add', 'Name is too short (Minimum: 2 characters)');
	}
	elseif (strlen($name) > 200) {
		error('admin.php?action=forums&job=cat_add', 'Name is too long (Maximum: 200 characters)');
	}

	$positions = array();
	$result = $db->query("SELECT id, position FROM {$db->pre}categories WHERE parent = '{$parent}' ORDER BY position");
	while ($pos = $db->fetch_assoc($result)) {
		if ($pos['id'] == $sort) {
			$position = $pos['position']+$sortx;
		}
		else {
			$positions[$pos['id']] = $pos['position'];
		}
	}
	if ($position == null) {
		if (count($positions) > 0) {
			$position = iif($sortx == 1, max($positions), min($positions));
		}
		else {
			$position = 0;
		}
	}
	else {
		$id = array_search($position, $positions);
		$move = array();
		while (is_id($id)) {
			$move[$id] = $positions[$id]+$sortx;
			$id = array_search($move[$id], $positions);
		}
		if (count($move) > 0) {
			$op = iif($sortx == 1, '+', '-');
			$idlist = implode(',', array_keys($move));
			$db->query("UPDATE {$db->pre}categories SET position = position {$op} 1 WHERE id IN({$idlist})");
		}
	}
	
	$db->query("
	INSERT INTO {$db->pre}categories (name, description, position, parent) 
	VALUES ('{$name}', '{$description}', '{$position}', '{$parent}')
	", __LINE__, __FILE__);

	$delobj = $scache->load('categories');
	$delobj->delete();
	$delobj = $scache->load('forumtree');
	$delobj->delete();

	ok('admin.php?action=forums&job=manage', 'Category successfully created!');
}
elseif ($job == 'cat_edit') {
	echo head();
	$id = $gpc->get('id', int);

	$result = $db->query("SELECT id, name, description, parent FROM {$db->pre}categories WHERE id = '{$id}' LIMIT 1", __LINE__, __FILE__);
	if ($db->num_rows($result) == 0) {
		error('admin.pgp?action=forums&job=manage', 'Category not found');
	}
	$row = $gpc->prepare($db->fetch_assoc($result));
	?>
<form name="form" method="post" action="admin.php?action=forums&job=cat_edit2&id=<?php echo $row['id']; ?>">
 <table class="border">
  <tr> 
   <td class="obox" colspan="2">Edit Category</td>
  </tr>
  <tr>
   <td class="mbox" width="50%">Name:<br />
   <span class="stext">Maximum: 200 characters</span>
   </td>
   <td class="mbox" width="50%"><input type="text" name="name" size="50" value="<?php echo $row['name']; ?>" /></td> 
  </tr>
  <tr>
   <td class="mbox" width="50%">Description:<br />
   <span class="stext">
   You can optionally type in a short description for this category.<br />
   HTML is allowed; BB-Code is not allowed!</span></td>
   <td class="mbox" width="50%"><textarea name="description" rows="2" cols="50"><?php echo $row['description']; ?></textarea></td> 
  </tr>
  <tr>
   <td class="mbox" width="50%">Parent Forum:</td>
   <td class="mbox" width="50%">
   	<select name="parent" size="1">
   	 <option value="0"<?php echo iif($row['parent'] == '0', ' selected="selected"'); ?>>No one</option>
   	 <?php echo SelectBoardStructure('parent', ADMIN_SELECT_FORUMS, $row['parent'], true); ?>
   	</select>
   </td> 
  </tr>
  <tr> 
   <td class="ubox" width="100%" colspan="2" align="center"><input type="submit" name="Submit" value="Add" /></td> 
  </tr>
 </table>
</form>
	<?php
	echo foot();
}
elseif ($job == 'cat_edit2') {
	echo head();
	
	$id = $gpc->get('id', int);
	$parent = $gpc->get('parent', int);
	$name = $gpc->get('name', str);
	$description = $gpc->get('description', str);

	$parent_notice = false;
	if ($parent > 0) {
		$subs = array();
		$parent_forums = $scache->load('parent_forums');
		$parents = $parent_forums->get();
		$result = $db->query("SELECT parent FROM {$db->pre}categories WHERE id = '{$id}' LIMIT 1", __LINE__, __FILE__);
		$row = $db->fetch_assoc($result);
		foreach ($parents as $id => $p_arr) {
			array_shift($p_arr);
			if (in_array($row['parent'], $p_arr)) {
				$subs[] = $id;
			}
		}
		if (in_array($parent, $subs)) {
			$parent_notice = true;
			$parent = $row['parent'];
		}
	}

	if (strlen($name) < 2) {
		error('admin.php?action=forums&job=cat_edit&id='.$id, 'Name is too short (Minimum: 2 characters)');
	}
	elseif (strlen($name) > 200) {
		error('admin.php?action=forums&job=cat_edit&id='.$id, 'Name is too long (Maximum: 200 characters)');
	}
	
	$db->query("UPDATE {$db->pre}categories SET name = '{$name}', description = '{$description}', parent = '{$parent}' WHERE id = '{$id}' LIMIT 1", __LINE__, __FILE__);

	$delobj = $scache->load('categories');
	$delobj->delete();
	$delobj = $scache->load('forumtree');
	$delobj->delete();
	
	if ($parent_notice == false) {
		ok('admin.php?action=forums&job=manage', 'Category successfully edited!');
	}
	else{
		error('admin.php?action=forums&job=manage', 'Category successfully edited, but the parent forum was not changed, because you had specified a subforum of this category.');
	}
}
elseif ($job == 'cat_delete') {
	echo head();
	$id = $gpc->get('id', int);
	
	$result = $db->query("SELECT id FROM {$db->pre}forums WHERE parent = '{$id}' LIMIT 1", __LINE__, __FILE__);
	if ($db->num_rows() > 0) {
		error('admin.php?action=forums&job=manage', 'Until you can delete this category, you have to delete all forums this category contains.');
	}
	
	$db->query("DELETE FROM {$db->pre}categories WHERE id = '{$id}' LIMIT 1", __LINE__, __FILE__);

	$delobj = $scache->load('categories');
	$delobj->delete();
	$delobj = $scache->load('forumtree');
	$delobj->delete();

	ok('admin.php?action=forums&job=manage', 'Category successfully deleted!');
}
elseif ($job == 'prefix') {
	$id = $gpc->get('id', int);
	$result = $db->query('SELECT * FROM '.$db->pre.'prefix WHERE bid = "'.$id.'" ORDER BY value', __LINE__, __FILE__);
	echo head();
?>
<form name="form" method="post" action="admin.php?action=forums&job=prefix_delete&id=<?php echo $id; ?>">
 <table class="border">
  <tr> 
   <td class="obox" colspan="3">Manage Prefix</td>
  </tr>
  <tr> 
   <td class="ubox" width="10%">Delete</td>
   <td class="ubox" width="70%">Value</td> 
   <td class="ubox" width="20%">Standard</td> 
  </tr>
  <?php
  $has_standard = false;
  while($prefix = $db->fetch_assoc($result)) {
  	if ($prefix['standard'] == 1) {
  		$has_standard = true;
  	}
  ?>
  <tr> 
   <td class="mbox" width="10%"><input type="checkbox" name="delete[]" value="<?php echo $prefix['id']; ?>"></td>
   <td class="mbox" width="70%"><a href="admin.php?action=forums&amp;job=prefix_edit&amp;id=<?php echo $prefix['id']; ?>"><?php echo $prefix['value']; ?></a></td>
   <td class="mbox" width="20%" align="center"><?php echo noki($prefix['standard']); ?></td> 
  </tr>
  <?php } ?>
  <tr>
   <td class="ubox" colspan="3" align="center"><input type="submit" name="Submit" value="Delete"></td> 
  </tr>
 </table>
</form><br />
<form name="form" method="post" action="admin.php?action=forums&job=prefix_add&id=<?php echo $id; ?>">
 <table class="border">
  <tr> 
   <td class="obox" colspan="2">Add Prefix</td>
  </tr>
  <tr> 
   <td class="mbox" width="50%">Value:</td>
   <td class="mbox" width="50%"><input type="text" name="name" size="50" /></td> 
  </tr>
<?php if ($has_standard == false) { ?>
  <tr> 
   <td class="mbox" width="50%">Standard:</td>
   <td class="mbox" width="50%"><input type="checkbox" name="standard" value="1" /></td> 
  </tr>
<?php } ?>
  <tr>
   <td class="ubox" colspan="2" align="center"><input type="submit" name="Submit" value="Add"></td> 
  </tr>
 </table>
</form> 
<?php
	echo foot();
}
elseif ($job == 'prefix_edit') {
	echo head();
	$id = $gpc->get('id', int);
	$result = $db->query("SELECT * FROM {$db->pre}prefix WHERE id = '{$id}'", __LINE__, __FILE__);
	$row = $db->fetch_assoc($result);
?>
<form name="form" method="post" action="admin.php?action=forums&job=prefix_edit2&id=<?php echo $id; ?>">
 <table class="border">
  <tr> 
   <td class="obox" colspan="2">Edit Prefix</td>
  </tr>
  <tr> 
   <td class="mbox" width="50%">Value:</td>
   <td class="mbox" width="50%"><input type="text" name="name" size="50" value="<?php echo htmlspecialchars($row['value']); ?>" /></td> 
  </tr>
  <tr> 
   <td class="mbox" width="50%">Standard:<br /><span class="stext">If another prefix is standard in this category, the status will be removed.</span></td>
   <td class="mbox" width="50%"><input type="checkbox" name="standard" value="1" <?php echo iif($row['standard'] == 1, ' checked="checked"'); ?> /></td> 
  </tr>
  <tr>
   <td class="ubox" colspan="2" align="center"><input type="submit" name="Submit" value="Edit"></td> 
  </tr>
 </table>
</form> 
<?php
	echo foot();
}
elseif ($job == 'prefix_edit2') {
	echo head();
	$id = $gpc->get('id', int);
	$val = $gpc->get('name', str);
	$standard = $gpc->get('standard', int);
	
	$result = $db->query('SELECT bid, standard FROM '.$db->pre.'prefix WHERE id = "'.$id.'"', __LINE__, __FILE__);
	$row = $db->fetch_assoc($result);

	$result = $db->query('SELECT id FROM '.$db->pre.'prefix WHERE bid = "'.$row['bid'].'" AND value = "'.$val.'" AND id != "'.$id.'" LIMIT 1', __LINE__, __FILE__);
	if ($db->num_rows() > 0) {
		error('admin.php?action=forums&job=prefix&id='.$id, 'This value already exists!');
	}
	else {
		if ($row['standard'] != $standard && $standard == 1) {
			$db->query("UPDATE {$db->pre}prefix SET standard = '0' WHERE standard = '1' AND bid = '{$row['bid']}' LIMIT 1", __LINE__, __FILE__);
		}
		$db->query("UPDATE {$db->pre}prefix SET value = '{$val}', standard = '{$standard}' WHERE id = '{$id}' LIMIT 1", __LINE__, __FILE__);
		$delobj = $scache->load('prefix');
		$delobj->delete();
		ok('admin.php?action=forums&job=prefix&id='.$row['bid'], 'Prefix successfully edited!');
	}
}
elseif ($job == 'prefix_delete') {
	echo head();
	$id = $gpc->get('id', int);
	$did = $gpc->get('delete', arr_int);
	$did = implode(',', $did);
	$delobj = $scache->load('prefix');
	$delobj->delete();
	$db->query('DELETE FROM '.$db->pre.'prefix WHERE id IN('.$did.') AND bid = "'.$id.'"', __LINE__, __FILE__);
	$i = $db->affected_rows();
	ok('admin.php?action=forums&job=prefix&id='.$id, $i.' prefixes deleted!');
}
elseif ($job == 'prefix_add') {
	echo head();
	$id = $gpc->get('id', int);
	$val = $gpc->get('name', str);
	$standard = $gpc->get('standard', int);
	$result = $db->query('SELECT id FROM '.$db->pre.'prefix WHERE bid = "'.$id.'" AND value = "'.$val.'" LIMIT 1', __LINE__, __FILE__);
	if ($db->num_rows() > 0) {
		error('admin.php?action=forums&job=prefix&id='.$id, 'This value already exists!');
	}
	else {
		$db->query("INSERT INTO {$db->pre}prefix (bid, value, standard) VALUES ('{$id}', '{$val}', '{$standard}')", __LINE__, __FILE__);
		$delobj = $scache->load('prefix');
		$delobj->delete();
		ok('admin.php?action=forums&job=prefix&id='.$id, 'Prefix successfully added!');
	}
}
?>
