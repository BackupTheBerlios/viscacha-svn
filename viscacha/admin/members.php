<?php
if (isset($_SERVER['PHP_SELF']) && basename($_SERVER['PHP_SELF']) == "members.php") die('Error: Hacking Attempt');

if ($job == 'newsletter') {
	echo head();
?>
<form name="form" method="post" action="admin.php?action=members&job=newsletter2">
 <table class="border" border="0" cellspacing="0" cellpadding="4" align="center">
  <tr> 
   <td class="obox" colspan="2"><b><span style="float: right;">[<a href="admin.php?action=members&job=newsletter_archive">Newsletter-Archiv</a>]</span>Newsletter verschicken</b></td>
  </tr>
  <tr> 
	<td class="mbox" width="50%">Empfänger:</td>
	<td class="mbox" width="50%"><select size="1" name="int1"><option value="1">Alle</option><option value="2" selected>Nur Mitglieder</option><option value="3">Nur Gäste</option></select></td> 
  </tr>
  <tr> 
   <td class="mbox" width="50%">Titel:</td>
   <td class="mbox" width="50%"><input type="text" name="temp1" size="60"></td> 
  </tr>
  <tr> 
   <td class="mbox" width="50%">Text:</td>
   <td class="mbox" width="50%"><textarea name="temp2" rows="8" cols="60"></textarea></td> 
  </tr>
  <tr> 
   <td class="mbox" width="50%">Anzahl Emails die pro Staffel verschickt werden:</td>
   <td class="mbox" width="50%"><input type="text" name="int2" size="10" value="100"></td> 
  </tr>
  <tr> 
   <td class="ubox" width="100%" colspan="2" align="center"><input type="submit" name="Submit" value="Abschicken"></td> 
  </tr>
 </table>
</form>
<?php
	echo foot();
}
elseif ($job == 'newsletter2') {
	
	$int1 = $gpc->get('int1', int);
	
	if ($int1 == 1) {
		$emails = array();
		$result = $db->query('SELECT mail FROM '.$db->pre.'user');
		while ($row = $db->fetch_num($result)) {
			$emails[] = $row[0];
		}
		$result = $db->query('SELECT email FROM '.$db->pre.'replies WHERE email != "" AND guest = "1"');
		while ($row = $db->fetch_num($result)) {
			$emails[] = $row[0];
		}
	}
	elseif ($int1 == 2) {
		$emails = array();
		$result = $db->query('SELECT mail FROM '.$db->pre.'user');
		while ($row = $db->fetch_num($result)) {
			$emails[] = $row[0];
		}
	}
	elseif ($int1 == 3) {
		$emails = array();
		$result = $db->query('SELECT email FROM '.$db->pre.'replies WHERE email != "" AND guest = "1"');
		while ($row = $db->fetch_num($result)) {
			$emails[] = $row[0];
		}
	}
	$emails = array_unique($emails);
	$anz = count($emails);
	if ($anz == 0) {
		echo head();
		error('admin.php?action=members&job=newsletter', 'Keine E-Mail-Adressen gefunden!');
	}
	$int2 = $gpc->get('int2', int, 100);
	$steps = ceil($anz/$int2);
	
	$db->query('INSERT INTO '.$db->pre.'newsletter (receiver, title, content, time) VALUES ("'.$int1.'","'.$gpc->get('temp1', str).'","'.$gpc->get('temp2', str).'","'.time().'")');
	$lid = $db->affected_rows();

	$cache = new CacheItem('newsletter_session');
    $cache->set($emails);
    $cache->export();

	$htmlhead .= '<meta http-equiv="refresh" content="2; url=admin.php?action=members&job=newsletter3&id='.$lid.'&int2='.$int2.'&page=1">';
	echo head();
	?>
 <table class="border" border="0" cellspacing="0" cellpadding="4" align="center">
  <tr> 
   <td class="obox"><b>Schritt 1 von <?php echo $steps+1; ?></b></td>
  </tr>
  <tr> 
   <td class="mbox">Die Daten wurden gespeichert<br>Die Emails werden nun in Staffeln versandt.</td>
  </tr>
 </table>	
<?php
	echo foot();
}
elseif ($job == 'newsletter3') {
	$cache = new CacheItem('newsletter_session');
    $emails = $cache->get();
	
	$int2 = $gpc->get('int2', int, 100);
	$page = $gpc->get('page', int, 1);

	$anz = count($emails);
	$steps = ceil($anz/$int2);
	
	$result = $db->query('SELECT * FROM '.$db->pre.'newsletter WHERE id = '.$gpc->get('id', int));
	$row = $db->fetch_assoc($result);
	
	$split = array_chunk($emails, $int2);
	$minus = $page-1;
	$plus = $page+1;
	
	$i = 0;
	if (!isset($split[$minus]) || !is_array($split[$minus])) {
		echo head();
		error('admin.php?action=members&job=newsletter', 'Keine E-Mail-Adressen zu dieser Staffel gefunden!');
	}
	foreach ($split[$minus] as $mail) {
		$i++;
		$comment = $row['content'];
		$to = array('0' => array('mail' => $mail));
		$topic = $row['title'];
		$from = array();
		xmail($to, $from, $topic, $comment);
	}
	
	$ready = $minus*$int2+$i;
	
	if ($page == $steps) {
		$htmlhead .= '<meta http-equiv="refresh" content="2; url=admin.php?action=members&job=newsletter">';
		$scache->deletedata();
		echo head();
?>
 <table class="border" border="0" cellspacing="0" cellpadding="4" align="center">
  <tr> 
   <td class="obox"><b>Staffel <?php echo $page+1; ?> von <?php echo $steps+1; ?>...</b></td>
  </tr>
  <tr> 
   <td class="mbox">E-Mail Staffel <?php echo $page; ?> versandt.<br>Insgesamt <?php echo $ready; ?> E-Mails gesandt!<br><br>Alle Emails erfolgreich versandt! <a href="admin.php?action=members&job=newsletter">Leite zur Verwaltung zurück.</a></td>
  </tr>
 </table>	
<?php
	}
	else {
		$htmlhead .= '<meta http-equiv="refresh" content="5; url=admin.php?action=members&job=newsletter3&id='.$gpc->get('id', int).'&int2='.$gpc->get('int2', int).'&page='.$plus.'">';
		echo head();
?>
 <table class="border" border="0" cellspacing="0" cellpadding="4" align="center">
  <tr> 
   <td class="obox"><b>Staffel <?php echo $page+1; ?> von <?php echo $steps+1; ?>...</b></td>
  </tr>
  <tr> 
   <td class="mbox">E-Mail Staffel <?php echo $page; ?> versandt.<br>Insgesamt <?php echo $ready; ?> E-Mails gesandt!</td>
  </tr>
 </table>	
<?php
	}
	echo foot();
}
elseif ($job == 'newsletter_archive') {
	$result = $db->query('SELECT id, title, receiver, time FROM '.$db->pre.'newsletter ORDER BY time');
	echo head();
	$receiver = array('1' => 'Alle','2' => 'Nur Mitglieder','3' => 'Nur Gäste');
?>
<form name="form" method="post" action="admin.php?action=members&job=newsletter_delete">
 <table class="border" border="0" cellspacing="0" cellpadding="4" align="center">
  <tr> 
   <td class="obox" colspan=4><b>Newsletter-Archiv</b></td>
  </tr>
  <tr> 
   <td class="ubox">Del</td>
   <td class="ubox">Betreff</td>
   <td class="ubox">Gesendet</td> 
   <td class="ubox">An</td> 
  </tr>
<?php while ($row = $db->fetch_assoc($result)) { ?>
  <tr>
   <td class="mbox"><input type="checkbox" name="delete[]" value="<?php echo $row['id']; ?>"></td> 
   <td class="mbox"><a href="admin.php?action=members&job=newsletter_view&id=<?php echo $row['id']; ?>"><?php echo $row['title']; ?></a></td>
   <td class="mbox"><?php echo date('d.m.Y, H:i', $row['time']); ?></td>
   <td class="mbox"><?php echo $receiver[$row['receiver']]; ?></td>
  </tr>
<?php } ?>
  <tr> 
   <td class="ubox" colspan=4 align="center"><input type="submit" name="Submit" value="Löschen"></td> 
  </tr>
 </table>
</form> 
<?php
	echo foot();
}
elseif ($job == 'newsletter_view') {
	$result = $db->query('SELECT * FROM '.$db->pre.'newsletter WHERE id = '.$gpc->get('id', int));
	$row = $db->fetch_assoc($result);
	echo head();
	$receiver = array('1' => 'Alle','2' => 'Nur Mitglieder','3' => 'Nur Gäste');
?>
<form name="form" method="post" action="admin.php?action=members&job=newsletter_delete">
 <table class="border" border="0" cellspacing="0" cellpadding="4" align="center">
  <tr> 
   <td class="obox" colspan=2><b>Newsletter-Archiv: Detail-Ansicht</b></td>
  </tr>
  <tr>
   <td class="mbox">Titel:</td> 
   <td class="mbox"><?php echo $row['title']; ?></td>
  </tr>
  <tr>
   <td class="mbox">Gesendet am:</td> 
   <td class="mbox"><?php echo date('d.m.Y, H:i', $row['time']); ?></td>
  </tr>
  <tr>
   <td class="mbox">Empfänger:</td> 
   <td class="mbox"><?php echo $receiver[$row['receiver']]; ?></td>
  </tr>
  <tr> 
   <td class="ubox" colspan=2>Newsletter-Text</td>
  </tr>
  <tr>
   <td class="mbox" colspan=2><pre><?php echo $row['content']; ?></pre></td>
  </tr>
  <tr> 
   <td class="ubox" colspan=2 align="center"><input type="hidden" name="delete[]" value="<?php echo $row['id']; ?>"><input type="submit" name="Submit" value="Löschen"></td> 
  </tr>
 </table> 
</form>
<?php
	echo foot();
}
elseif ($job == 'newsletter_delete') {
	echo head();
	$del = $gpc->get('delete', arr_int);
	if (count($del) > 0) {
		$deleteids = array();
		foreach ($del as $did) {
			$deleteids[] = 'id = '.$did; 
		}
		$db->query('DELETE FROM '.$db->pre.'newsletter WHERE '.implode(' OR ',$deleteids));
		$anz = $db->affected_rows();
		ok('admin.php?action=members&job=newsletter_archive', $anz.' Newsletter wurden gelöscht!');
	}
	else {
		error('admin.php?action=members&job=newsletter_archive', 'Keine Eingabe gemacht');
	}
	
}
elseif ($job == 'merge') {
	echo head();
	?>
<form name="form2" method="post" action="admin.php?action=members&job=merge2">
<table class="border">
<tr><td class="obox" colspan="2">Merge Users</td></tr>
<tr><td class="ubox" colspan="2">
Hier können Sie 2 Benutzeraccounts zu einem zusammenführen. 
Das "Basis-Mitglied" bleibt bestehen und die Daten werden als Standard benutzt. 
Die Beiträge, PNs etc. vom "überflüssigen Mitglied" werden auf das "Basis-Mitglied überschrieben. 
Beim "Basis-Mitglied" fehlende Daten werden vom "überflüssigen Mitglied" übernommen. 
Das "überflüssige Mitglied" wird danach gelöscht.
</td></tr>
<tr>
<td class="mbox">Basis-Mitglied:</td>
<td class="mbox">
	<input type="text" name="name1" id="name1" onkeyup="ajax_searchmember(this, 'sugg1');" size="40" /><br />
	<span class="stext">Suggestions: <span id="sugg1"></span></span>
</td>
</tr>
<td class="mbox">Überflüssiges Mitglied:</td>
<td class="mbox">
	<input type="text" name="name2" id="name2" onkeyup="ajax_searchmember(this, 'sugg2');" size="40" /><br />
	<span class="stext">Suggestions: <span id="sugg2"></span></span>
</td>
</tr>
<tr> 
<td class="ubox" colspan="2" align="center"><input type="submit" name="Submit" value="Löschen"></td> 
</tr>
</table>
</form>
	<?php
	echo foot();
}
elseif ($job == 'merge2') {
	echo head();
	$base = $gpc->get('name1', str);
	$old = $gpc->get('name2', str);

	// Step 1: Getting data
	$result = $db->query('SELECT * FROM '.$db->pre.'user WHERE name = "'.$base.'" LIMIT 1');
	$result2 = $db->query('SELECT * FROM '.$db->pre.'user WHERE name = "'.$old.'" LIMIT 1');
	if ($db->num_rows($result) != 1 || $db->num_rows($result2) != 1) {
		error('admin.php?action=members&job=merge', 'Mindestens einer der angegebenen Benutzernamen wurde nicht gefunden.');
	}
	$base = $db->fetch_assoc($result);
	$old = $db->fetch_assoc($result2);

	// Step 2: Update abos
	$db->query("UPDATE {$db->pre}abos SET mid = '".$base['id']."' WHERE mid = '".$old['id']."'");
	// Step 4: Update mods
	$db->query("UPDATE {$db->pre}moderators SET mid = '".$base['id']."' WHERE mid = '".$old['id']."'");
	// Step 5: Update pms
	$db->query("UPDATE {$db->pre}pm SET pm_to = '".$base['id']."' WHERE pm_to = '".$old['id']."'");
	$db->query("UPDATE {$db->pre}pm SET pm_from = '".$base['id']."' WHERE pm_from = '".$old['id']."'");
	// Step 6: Update posts
	$db->query("UPDATE {$db->pre}replies SET name = '".$base['id']."' WHERE name = '".$old['id']."' AND email = ''");
	// Step 7:Update topics
	$db->query("UPDATE {$db->pre}topics SET name = '".$base['id']."' WHERE name = '".$old['id']."'");
	$db->query("UPDATE {$db->pre}topics SET last_name = '".$base['id']."' WHERE last_name = '".$old['id']."'");
	// Step 8: Update uploads
	$db->query("UPDATE {$db->pre}uploads SET mid = '".$base['id']."' WHERE mid = '".$old['id']."'");
	// Step 9: Delete pic
	removeOldImages('uploads/pics/', $old['id']);
	// Step 10: Update votes
	$db->query("UPDATE {$db->pre}votes SET mid = '".$base['id']."' WHERE mid = '".$old['id']."'");
	// Setp 11: Update User data
	$newdata = array();
	$base = $gpc->save_str($base);
	$old = $gpc->save_str($old);
	if ($base['regdate'] > $old['regdate']) {
		$newdata[] = "regdate = '{$old['regdate']}'";
	}
	if (empty($base['fullname']) && !empty($old['fullname'])) {
		$newdata[] ="fullname = '{$old['fullname']}'";
	}
	if (empty($base['hp']) && !empty($old['hp'])) {
		$newdata[] ="hp = '{$old['hp']}'";
	}
	if (empty($base['signature']) && !empty($old['signature'])) {
		$newdata[] ="signature = '{$old['signature']}'";
	}
	if (empty($base['about']) && !empty($old['about'])) {
		$newdata[] ="about = '{$old['about']}'";
	}
	if (!empty($old['notice'])) {
		if (empty($base['notice'])) {
			$notice = $old['notice'];
		}
		else {
			$notice = $base['notice'].'[VSEP]'.$old['notice'];
		}
		$newdata[] ="notice = '{$notice}'";
	}
	if (empty($base['location']) && !empty($old['location'])) {
		$newdata[] ="location = '{$old['location']}'";
	}
	if (empty($base['pic']) && !empty($old['pic'])) {
		$newdata[] ="pic = '{$old['pic']}'";
	}
	if (empty($base['yahoo']) && !empty($old['yahoo'])) {
		$newdata[] ="yahoo = '{$old['yahoo']}'";
	}
	if (empty($base['msn']) && !empty($old['msn'])) {
		$newdata[] ="msn = '{$old['msn']}'";
	}
	if (empty($base['skype']) && !empty($old['skype'])) {
		$newdata[] ="skype = '{$old['skype']}'";
	}
	if (empty($base['jabber']) && !empty($old['jabber'])) {
		$newdata[] ="jabber = '{$old['jabber']}'";
	}
	if (empty($base['aol']) && !empty($old['aol'])) {
		$newdata[] ="aol = '{$old['aol']}'";
	}
	if (empty($base['icq']) && !empty($old['icq'])) {
		$newdata[] ="icq = '{$old['icq']}'";
	}
	if ($base['birthday'] == '0000-00-00' && $old['birthday'] != '0000-00-00') {
		$newdata[] ="birthday = '{$old['birthday']}'";
	}
	if (empty($base['timezone']) && !empty($old['timezone'])) {
		$newdata[] ="timezone = '{$old['timezone']}'";
	}
	$g1 = explode(',', $base['groups']);
	natsort($g1);
	$base['groups'] = implode(',', $g1);
	$g2 = explode(',', $old['groups']);
	$g = array_merge($g1, $g2);
	$g = array_unique($g);
	natsort($g);
	$groups = implode(',', $g);
	if ($groups != $base['groups']) {
		$newdata[] ="groups = '{$groups}'";
	}
	if (count($newdata) > 0) {
		$db->query("UPDATE {$db->pre}user SET ".implode(', ', $newdata)." WHERE id = '".$base['id']."' LIMIT 1");
	}
	// Step 12: Delete old user
	$db->query("DELETE FROM {$db->pre}user WHERE id = '".$old['id']."'");
	
	ok('admin.php?action=members&job=manage', "{$old['name']}'s data is converted to {$base['name']}'s Account.");
}
elseif ($job == 'manage') {
	echo head();
	$sort = $gpc->get('sort', str);
	$order = $gpc->get('order', int);
	$letter = $gpc->get('letter', str);
	$page = $gpc->get('page', int, 1);
	
	$count = $db->fetch_num($db->query('SELECT COUNT(*) FROM '.$db->pre.'user'));
	$temp = pages($count[0], "admin.php?action=members&job=manage&sort=".$sort."&amp;letter=".$letter."&amp;order=".$order."&amp;", 25);

    if ($order == '1') $order = 'desc';
	else $order = 'asc';
			
	if ($sort == 'regdate') $sort = 'regdate';
	elseif ($sort == 'location') $sort = 'location';
	elseif ($sort == 'gender') $sort = 'gender';
	elseif ($sort == 'lastvisit') $sort = 'lastvisit';
	else $sort = 'name';

	$start = $page*25;
	$start = $start-25;
	
	$change = array('m' => 'male', 'w' => 'female', '' => '-');

	$result = $db->query('SELECT * FROM '.$db->pre.'user ORDER BY '.$sort.' '.$order.' LIMIT '.$start.',25');
	?>
	<form name="form" action="admin.php?action=members&job=delete" method="post">
	<table class="border" border="0" cellspacing="0" cellpadding="4" align="center">
		<tr> 
		  <td class="obox" colspan="8">
		  <span style="float: right;">
		  [<a href="admin.php?action=members&job=search">Search for Members</a>]&nbsp;
		  [<a href="admin.php?action=members&job=merge">Merge Users</a>]
		  </span>
		  List of Members &amp; User Manager</td>
		</tr>
		<tr> 
		  <td class="ubox" colspan="8"><span style="float: right;"><?php echo $temp; ?></span><?php echo $count[0]; ?> Members</td>
		</tr>
		<tr>
		  <td class="obox">Del</td>
		  <td class="obox">Name
		  <a href="admin.php?action=members&job=manage&letter=<?php echo $letter; ?>&amp;page=<?php echo $page; ?>"><img src="admin/html/images/asc.gif" border=0 alt="Ascending"></a>
		  <a href="admin.php?action=members&job=manage&order=1&amp;page=<?php echo $page; ?>&amp;letter=<?php echo $letter; ?>"><img src="admin/html/images/desc.gif" border=0 alt="Descending"></a></td>
		  <td class="obox">Email</td>
		  <td class="obox">Gender
		  <a href="admin.php?action=members&job=manage&sort=gender&amp;letter=<?php echo $letter; ?>&amp;page=<?php echo $page; ?>"><img src="admin/html/images/asc.gif" border=0 alt="Ascending"></a>
		  <a href="admin.php?action=members&job=manage&sort=gender&amp;letter=<?php echo $letter; ?>&amp;order=1&amp;page=<?php echo $page; ?>"><img src="admin/html/images/desc.gif" border=0 alt="Descending"></a></td>
		  <td class="obox">Residence
		  <a href="admin.php?action=members&job=manage&sort=location&amp;letter=<?php echo $letter; ?>&amp;page=<?php echo $page; ?>"><img src="admin/html/images/asc.gif" border=0 alt="Ascending"></a>
		  <a href="admin.php?action=members&job=manage&sort=location&amp;letter=<?php echo $letter; ?>&amp;order=1&amp;page=<?php echo $page; ?>"><img src="admin/html/images/desc.gif" border=0 alt="Descending"></a></td>
		  <td class="obox">Last Visit
		  <a href="admin.php?action=members&job=manage&sort=lastvisit&amp;letter=<?php echo $letter; ?>&amp;page=<?php echo $page; ?>"><img src="admin/html/images/asc.gif" border=0 alt="Ascending"></a>
		  <a href="admin.php?action=members&job=manage&sort=lastvisit&amp;letter=<?php echo $letter; ?>&amp;order=1&amp;page=<?php echo $page; ?>"><img src="admin/html/images/desc.gif" border=0 alt="Descending"></a></td>
		  <td class="obox">Registered on
		  <a href="admin.php?action=members&job=manage&sort=regdate&amp;letter=<?php echo $letter; ?>&amp;page=<?php echo $page; ?>"><img src="admin/html/images/asc.gif" border=0 alt="Ascending"></a>
		  <a href="admin.php?action=members&job=manage&sort=regdate&amp;letter=<?php echo $letter; ?>&amp;order=1&amp;page=<?php echo $page; ?>"><img src="admin/html/images/desc.gif" border=0 alt="Descending"></a></td>
		</tr>
	<?php
	while ($row = $gpc->prepare($db->fetch_object($result))) { 
		$row->regdate = gmdate('d.m.Y', times($row->regdate));
		if ($row->lastvisit == 0) {
			$row->lastvisit = 'Never';
		} else {
			$row->lastvisit = gmdate('d.m.Y H:i', times($row->lastvisit));
		}
		?>
	    <tr>
	      <td class="mbox"><input type="checkbox" name="delete[]" value="<?php echo $row->id; ?>"></td> 
		  <td class="mbox"><a title="Edit" href="admin.php?action=members&job=edit&id=<?php echo $row->id; ?>"><?php echo $row->name; ?></a><?php echo iif($row->fullname,"<br><i>".$row->fullname."</i>"); ?></td> 
		  <td class="mbox" align="center"><a href="mailto:<?php echo $row->mail; ?>">Email</a></td> 
		  <td class="mbox"><?php echo $change[$row->gender]; ?></td>
		  <td class="mbox"><?php echo iif($row->location,$row->location,'-'); ?></td>
		  <td class="mbox"><?php echo $row->lastvisit; ?></td>
		  <td class="mbox"><?php echo $row->regdate; ?></td>
		</tr>
		<?php
	} 
	?>
		<tr> 
		  <td class="ubox" colspan="8"><span style="float: right;"><?php echo $temp; ?></span><input type="submit" name="submit" value="Delete"></td>
		</tr>
	</table>
	</form>
	<?php
    echo foot();
}
elseif ($job == 'memberrating') {
	echo head();
	$page = $gpc->get('page', int, 1);
	
	$count = $db->fetch_num($db->query('SELECT COUNT(*) FROM '.$db->pre.'postratings WHERE aid != "0" GROUP BY aid'));
	$temp = pages($count[0], "admin.php?action=members&job=memberrating&amp;", 25);

	$start = $page*25;
	$start = $start-25;
	
	$change = array('m' => 'male', 'w' => 'female', '' => '-');

	$result = $db->query('
	SELECT u.*, avg(p.rating) AS ravg, count(*) AS rcount 
	FROM '.$db->pre.'postratings AS p 
		LEFT JOIN '.$db->pre.'user AS u ON p.aid = u.id 
	WHERE aid != "0" 
	GROUP BY aid 
	ORDER BY ravg DESC 
	LIMIT '.$start.',25
	', __LINE__, __FILE__);
	?>
	<form name="form" action="admin.php?action=members&job=delete" method="post">
	<table class="border" border="0" cellspacing="0" cellpadding="4" align="center">
		<tr> 
		  <td class="obox" colspan="6">Memberrating</td>
		</tr>
		<tr> 
		  <td class="ubox" colspan="6"><span style="float: right;"><?php echo $temp; ?></span><?php echo $count[0]; ?> rated Members</td>
		</tr>
		<tr>
		  <td class="obox">Del</td>
		  <td class="obox">Name</td>
		  <td class="obox">Bewertung (Anz. Bewertungen)</td>
		  <td class="obox">Email</td>
		  <td class="obox">Last Visit</td>
		  <td class="obox">Registered on</td>
		</tr>
	<?php
	while ($row = $gpc->prepare($db->fetch_object($result))) { 
		$row->regdate = gmdate('d.m.Y', times($row->regdate));
		$row->lastvisit = gmdate('d.m.Y H:i', times($row->lastvisit));
		$percent = round((($row->ravg*50)+50));
		?>
	    <tr>
	      <td class="mbox"><input type="checkbox" name="delete[]" value="<?php echo $row->id; ?>"></td> 
		  <td class="mbox"><a title="Edit" href="admin.php?action=members&job=edit&id=<?php echo $row->id; ?>"><?php echo $row->name; ?></a><?php echo iif($row->fullname,"<br><i>".$row->fullname."</i>"); ?></td> 
		  <td class="mbox"><img src="images.php?action=memberrating&id=<?php echo $row->id; ?>" alt="<?php echo $percent; ?>%" title="<?php echo $percent; ?>%"  /> <?php echo $percent; ?>% (<?php echo $row->rcount; ?>)</td>
		  <td class="mbox" align="center"><a href="mailto:<?php echo $row->mail; ?>">Email</a></td> 
		  <td class="mbox"><?php echo $row->lastvisit; ?></td>
		  <td class="mbox"><?php echo $row->regdate; ?></td>
		</tr>
		<?php
	} 
	?>
		<tr> 
		  <td class="ubox" colspan="6"><span style="float: right;"><?php echo $temp; ?></span><input type="submit" name="submit" value="Delete"></td>
		</tr>
	</table>
	</form>
	<?php
    echo foot();
}
elseif ($job == 'edit') {
	include_once ("classes/function.profilefields.php");

	// About
	$id = $gpc->get('id', int);

	$result = $db->query('SELECT * FROM '.$db->pre.'user WHERE id = '.$id);
	if ($db->num_rows() != 1) {
		error('admin.php?action=members&job=manage', 'Keine gültige ID angegeben.');
	}
	$user = $gpc->prepare($db->fetch_assoc($result));
	
	$chars = $config['maxaboutlength'];
	
	if (empty($user['template'])) {
	    $user['template'] = $config['templatedir'];
	}
	if (empty($user['language'])) {
	    $user['language'] = $config['langdir'];
	}		
	
	// Settings
	$loaddesign_obj = $scache->load('loaddesign');
	$design = $loaddesign_obj->get();
	$mydesign = $design[$user['template']]['name'];
	
	$loadlanguage_obj = $scache->load('loadlanguage');
	$language = $loadlanguage_obj->get();
	$mylanguage = $language[$user['language']]['language'];
	
	// Profile
    $bday = explode('-',$user['birthday']);
    $year = gmdate('Y');
    $maxy = $year-6;
    $miny = $year-100;
    $result = $db->query("SELECT id, title, name, core FROM {$db->pre}groups ORDER BY admin DESC , guest ASC , core ASC");
    $random = md5(microtime());
    
    $customfields = admin_customfields($user['id']);

	echo head();
?>
<form name="form_<?php echo $random; ?>" method="post" action="admin.php?action=members&job=edit2&amp;id=<?php echo $id; ?>&amp;random=<?php echo $random; ?>">
<table class="border">
<tr><td class="obox" colspan="2">Mitglied bearbeiten</td></tr>
<tr><td class="mbox">Benutzername:</td><td class="mbox">
<input type="text" name="name_<?php echo $random; ?>" size="40" value="<?php echo $user['name']; ?>" />
</td></tr>
<tr><td class="mbox">Neues Passwort:</td><td class="mbox">
<input type="password" name="pw_<?php echo $random; ?>" size="40" value="" />
</td></tr>
<tr><td class="mbox" valign="top">Gruppenzugehörigkeit:<br />
<span class="stext">Mehrere Gruppen möglich. Eingabe der IDs mit Kommas!</span>
</td><td class="mbox">
<input type="text" name="groups" id="groups" size="40" value="<?php echo $user['groups']; ?>" />
<br />
<table class="border">
<tr>
<td class="ubox">ID</td>
<td class="ubox">Interner Gruppenname></td>
<td class="ubox">Öffentlicher Gruppentitel</td>
</tr>
<?php while ($row = $gpc->prepare($db->fetch_assoc($result))) { ?>
<tr>
<td class="mbox"><?php echo $row['id']; ?></td>
<td class="mbox"><?php echo $row['name']; ?></td>
<td class="mbox"><?php echo $row['title']; ?></td>
</tr>
<?php } ?>
</table>
</td></tr>
<tr><td class="mbox">Bürgerlicher Name:</td><td class="mbox">
<input type="text" name="fullname" id="fullname" size="40" value="<?php echo $user['fullname']; ?>" />
</td></tr>
<tr><td class="mbox">Emailadresse:</td><td class="mbox"> 
<input type="text" name="email" id="email" size="40" value="<?php echo $user['mail']; ?>" />
</td></tr>
<tr><td class="mbox">Wohnort:</td><td class="mbox"> 
<input type="text" name="location" id="location" size="40" value="<?php echo $user['location']; ?>" />
</td></tr>
<tr><td class="mbox">Geschlecht:</td><td class="mbox"> 
<select size="1" name="gender">
	<option value="">Keine Angabe</option>
	<option<?php echo iif($user['gender'] == 'm',' selected="selected"'); ?> value="m">Männlich</option>
	<option<?php echo iif($user['gender'] == 'w',' selected="selected"'); ?> value="w">Weiblich</option>
</select>
</td></tr>
<tr><td class="mbox">Geburtstag:</td><td class="mbox">
  <select size="1" name="birthday">
  <option value="00">--</option>
	<?php
	for ($i=1;$i<=31;$i++) {
		echo "<option value='".leading_zero($i)."'".iif($bday[2] == $i, ' selected="selected"').">".$i."</option>\n";
	}
	?>
  </select>. 
  <select size="1" name="birthmonth">
  <option value="00">--</option>
	<?php
	for ($i=1;$i<=12;$i++) {
		echo "<option value='".leading_zero($i)."'".iif($bday[1] == $i, ' selected="selected"').">".$i."</option>\n";
	}
	?>
  </select> 
  <select size="1" name="birthyear">
  <option value="0000">----</option>
	<?php
	for ($i=$maxy;$i>=$miny;$i--) {
		echo "<option value='".$i."'".iif($bday[0] == $i, ' selected="selected"').">".$i."</option>\n";
	}
	?>
  </select>
</td></tr>
<tr><td class="mbox">Homepage:</td><td class="mbox"> 
<input type="text" name="hp" id="hp" size="40" value="<?php echo $user['hp']; ?>" />
</td></tr>
<tr><td class="mbox">ICQ:</td><td class="mbox"> 
<input type="text" name="icq" id="icq" size="40" value="<?php echo iif(!empty($user['icq']), $user['icq']); ?>" />
</td></tr>
<tr><td class="mbox">AOL- & Netscape-Messenger:</td><td class="mbox"> 
<input type="text" name="aol" id="aol" size="40" value="<?php echo $user['aol']; ?>" />
</td></tr>
<tr><td class="mbox">Yahoo-Messenger:</td><td class="mbox"> 
<input type="text" name="yahoo" id="yahoo" size="40" value="<?php echo $user['yahoo']; ?>" />
</td></tr>
<tr><td class="mbox">MSN- & Windows-Messenger</td><td class="mbox"> 
<input type="text" name="msn" id="msn" size="40" value="<?php echo $user['msn']; ?>" />
</td></tr>
<tr><td class="mbox">Jabber:</td><td class="mbox"> 
<input type="text" name="jabber" id="jabber" size="40" value="<?php echo $user['jabber']; ?>" />
</td></tr>
<tr><td class="mbox">Skype</td><td class="mbox"> 
<input type="text" name="skype" id="skype" size="40" value="<?php echo $user['skype']; ?>" />
</td></tr>
<?php foreach ($customfields['1'] as $row1) { ?>
<tr><td class="mbox"><?php echo $row1['name'] . iif(!empty($row1['description']), '<br /><span class="stext">'.$row1['description'].'</span>'); ?></td>
<td class="mbox"> <?php echo $row1['input']; ?></td></tr>
<?php } ?>
<tr><td class="ubox" align="center" colspan="2"><input accesskey="s" type="submit" name="Submit1" value="Submit" /></td></tr>
</table>

<br class="minibr" />
<table class="border">
<tr><td class="obox">Signatur</td></tr>
<tr><td class="mbox" align="center"><textarea name="signature" rows="4" cols="110"><?php echo $user['signature']; ?></textarea></td></tr>
<tr><td class="ubox" align="center"><input accesskey="s" type="submit" name="Submit1" value="Submit" /></td></tr>
</table>
<br class="minibr" />

<table class="border">
<tr><td class="obox" colspan="2">Profilbild ändern</td></tr>
<tr>
<td class="mbox">Neues Profilbild per URL hinzufügen:</td>
<td class="mbox"><input type="text" name="pic" id="pic" size="70" value="<?php echo $user['pic']; ?>" /></td>
</tr>
<tr><td class="ubox" colspan="2" align="center"><input accesskey="s" type="submit" name="Submit1" value="Submit" /></td></tr>
</table>
<br class="minibr" />

<table class="border">
<tr><td class="obox" colspan="2">Optionen ändern</td></tr>
<tr><td class="mbox">Zeitzone:</td><td class="mbox">
<select id="temp" name="temp"> 
	<option selected="selected" value="<?php echo $user['timezone']; ?>">Zeitzone beibehalten (GMT <?php echo $user['timezone']; ?>)</option>
	<option value="-12">(GMT -12:00) Eniwetok, Kwajalein</option>
	<option value="-11">(GMT -11:00) Midway-Inseln, Samoa</option>
	<option value="-10">(GMT -10:00) Hawaii</option>
	<option value="-9">(GMT -09:00) Alaska</option>
	<option value="-8">(GMT -08:00) Tijuana, Lod Angeles, Seattle, Vancouver</option>
	<option value="-7">(GMT -07:00) Arizona, Denver, Salt Lake City, Calgary</option>
	<option value="-6">(GMT -06:00) Mexiko-Stadt, Saskatchewan, Zentralamerika</option>
	<option value="-5">(GMT -05:00)  Bogot&aacute;, Lima, Quito, Indiana (Ost), New York, Toronto</option>
	<option value="-4">(GMT -04:00) Caracas, La Paz, Montreal, Quebec, Santiago</option>
	<option value="-3.5">(GMT -03:30) Neufundland</option>
	<option value="-3">(GMT -03:00) Brasilia, Buenos Aires, Georgetown, Gr&ouml;nland</option>
	<option value="-2">(GMT -02:00) Mittelatlantik</option>
	<option value="-1">(GMT -01:00) Azoren, Kapverdische Inseln</option>
	<option value="0">(GMT) Casablance, Monrovia, Dublin, Edinburgh, Lissabon, London</option>
	<option value="+1">(GMT +01:00) Amsterdam, Berlin, Bern, Rom, Stockholm, Wien, Paris</option>
	<option value="+2">(GMT +02:00) Athen, Istanbul, Minsk, Kairo, Jerusalem</option>
	<option value="+3">(GMT +03:00) Bagdad, Moskau, Nairobi</option>
	<option value="+3.5">(GMT +03:30) Teheran</option>
	<option value="+4">(GMT +04:00) Muskat, Tiflis</option>
	<option value="+4.5">(GMT +04:30) Kabul</option>
	<option value="+5">(GMT +05:00) Islamabad</option>
	<option value="+5.5">(GMT +05:30) Kalkutta, Neu-Delhi</option>
	<option value="+5.75">(GMT +05:45) Katmandu</option>
	<option value="+6">(GMT +06:00) Almaty, Nowosibirsk, Dhaka</option>
	<option value="+6.5">(GMT +06:30) Rangun</option>
	<option value="+7">(GMT +07:00) Bangkok, Hanoi, Jakarta</option>
	<option value="+8">(GMT +08:00) Ulan Bator, Singapur, Peking, Hongkong</option>
	<option value="+9">(GMT +09:00) Irkutsk, Osaka, Sapporo, Tokyo, Seoul</option>
	<option value="+9.5">(GMT +09:30) Adelaide, Darwin</option>
	<option value="+10">(GMT +10:00) Brisbane, Canberra, Melbourne, Sydney, Wladiwostok</option>
	<option value="+11">(GMT +11:00) Salomonen, Neukaledonien</option>
	<option value="+12">(GMT +12:00) Auckland, Wellington, Fidschi, Kamtschatka</option>
</select>		
</td></tr>
<tr><td class="mbox">Beitragseditor:</td><td class="mbox">
<select id="opt_0" name="opt_0">
	<option<?php echo iif($user['opt_textarea'] == 0,' selected="selected"'); ?> value="0">Einfacher Editor</option>
	<option<?php echo iif($user['opt_textarea'] == 1,' selected="selected"'); ?> value="1">Erweiterter Editor</option>
</select>
</td></tr>
<tr><td class="mbox">Email bei neuer PN senden?</td><td class="mbox">
<input id="opt_1" type="checkbox" name="opt_1" <?php echo iif($user['opt_pmnotify'] == 1,' checked="checked"'); ?> value="1" />
</td></tr>
<tr><td class="mbox">Schlecht bewertete Themen automatisch ausblenden?</td><td class="mbox">
<input id="opt_2" type="checkbox" name="opt_2" <?php echo iif($user['opt_hidebad'] == 1,' checked="checked"'); ?> value="1" />
</td></tr>
<tr><td class="mbox">Wie soll Mitgliedern Ihre Email angezeigt werden?</td><td class="mbox">
<select id="opt_3" name="opt_3">
	<option<?php echo iif($user['opt_hidemail'] == 0,' selected="selected"'); ?> value="0">E-Mail verschlüsselt zeigen + Formular</option>
	<option<?php echo iif($user['opt_hidemail'] == 1,' selected="selected"'); ?> value="1">E-Mail nicht zeigen + Kein Formular</option>
	<option<?php echo iif($user['opt_hidemail'] == 2,' selected="selected"'); ?> value="2">E-Mail nicht zeigen + Formular</option>
</select>
</td></tr>
<tr><td class="mbox">Welches Design wollen Sie nutzen?</td><td class="mbox">
<select id="opt_4" name="opt_4">
	<option selected="selected" value="<?php echo $user['template']; ?>">Design beibehalten</option>
	<?php foreach ($design as $row) { ?>
	<option value="<?php echo $row['id']; ?>"><?php echo $row['name']; ?></option>
	<?php } ?>
</select>
</td></tr>
<tr><td class="mbox">Welche Sprache wollen Sie nutzen?</td><td class="mbox">
<select id="opt_5" name="opt_5">
	<option selected="selected" value="<?php echo $user['language']; ?>">Sprache beibehalten</option>
	<?php foreach ($language as $row) { ?>
	<option value="<?php echo $row['id']; ?>"><?php echo $row['language']; ?></option>
	<?php } ?>
</select>
</td></tr>
<?php foreach ($customfields['2'] as $row1) { ?>
<tr><td class="mbox"><?php echo $row1['name'] . iif(!empty($row1['description']), '<br /><span class="stext">'.$row1['description'].'</span>'); ?></td>
<td class="mbox"> <?php echo $row1['input']; ?></td></tr>
<?php } ?>
<?php foreach ($customfields['0'] as $row1) { ?>
<tr><td class="mbox"><?php echo $row1['name'] . iif(!empty($row1['description']), '<br /><span class="stext">'.$row1['description'].'</span>'); ?></td>
<td class="mbox"> <?php echo $row1['input']; ?></td></tr>
<?php } ?>
<tr><td class="ubox" colspan="2"><input accesskey="s" type="submit" name="Submit1" value="Submit" /></td></tr>
</table>
<br class="minibr" />

<table class="border">
<tr><td class="obox">Persönliche Seite ändern</td></tr>
<tr><td class="mbox" align="center"><textarea name="comment" id="comment" rows="15" cols="110"><?php echo $user['about']; ?></textarea></td></tr>
<tr><td class="ubox" align="center"><input accesskey="s" type="submit" name="Submit1" value="Submit" /></td></tr>
</table>
</form>
<?php
	echo foot();
}
elseif ($job == 'edit2') {
	include_once ("classes/function.profilefields.php");

	echo head();
	$loaddesign_obj = $scache->load('loaddesign');
	$cache = $loaddesign_obj->get();
	
	$loadlanguage_obj = $scache->load('loadlanguage');
	$cache2 = $loadlanguage_obj->get();
	
	$keys_int = array('id', 'birthday', 'birthmonth', 'birthyear', 'opt_0', 'opt_1', 'opt_2', 'opt_3', 'opt_4', 'opt_5');
	$keys_str = array('groups', 'fullname', 'email', 'location', 'icq', 'gender', 'hp', 'aol', 'yahoo', 'msn', 'jabber', 'signature', 'pic', 'temp', 'comment', 'skype');
	foreach ($keys_int as $val) {
		$query[$val] = $gpc->get($val, int);
	}
	foreach ($keys_str as $val) {
		$query[$val] = $gpc->get($val, str);
	}

	$result = $db->query('SELECT * FROM '.$db->pre.'user WHERE id = '.$query['id']);
	if ($db->num_rows() != 1) {
		error('admin.php?action=members&job=manage', 'Keine gültige ID angegeben.');
	}
	$user = $gpc->prepare($db->fetch_assoc($result));

	$random = $gpc->get('random', none);
	$name = $gpc->get('name_'.$random, str);
	if (empty($name)) {
		$query['name'] = $user['name'];
	}
	else {
		$query['name'] = $name;
	}
	$query['pw'] = $gpc->get('pw_'.$random, str);
	
	$query['hp'] = trim($query['hp']);
	if (strtolower(substr($query['hp'], 0, 4)) == 'www.') {
		$query['hp'] = "http://{$query['hp']}";
	}
	
	$error = array();
	if (strxlen($query['comment']) > $config['maxaboutlength']) {
		$error[] = 'Perönliche Seite ist zu lang';
	}
	if (check_mail($query['email']) == false) {
		 $error[] = 'Keine gültige Emailadresse angegeben';
	}
	if (strxlen($query['name']) > $config['maxnamelength']) {
		$error[] = 'Name ist zu lang';
	}
	if (strxlen($query['name']) < $config['minnamelength']) {
		$error[] = 'Name ist zu kurz';
	}
	if (strxlen($query['email']) > 200) {
		$error[] = 'Emailadresse ist zu lang (max. 200 Zeichen)';
	}
	if (strxlen($query['signature']) > $config['maxsiglength']) {
		$error[] = 'Signatur ist zu lang';
	}
	if (strxlen($query['hp']) > 254) {
		$error[] = 'Homepage ist zu lang';
	}
	if (!check_hp($query['hp'])) {
		$query['hp'] = '';
	}
	if (strxlen($query['location']) > 50) {
		$error[] = 'Wohnort ist zu lang (max. 50 Zeichen)';
	}
	if ($query['gender'] != 'm' && $query['gender'] != 'w' && $query['gender'] != '') {
		$error[] = "Geschlecht wurde falsch übermittelt";
	}
	if ($query['birthday'] > 31) {
		$error[] = "Geburtstag wurde falsch übermittelt";
	}
	if ($query['birthmonth'] > 12) {
		$error[] = "Geburtsmonat wurde falsch übermittelt";
	}
	if (($query['birthyear'] < gmdate('Y')-120 || $query['birthyear'] > gmdate('Y')) && $query['birthyear'] != 0 ) {
		$error[] = "Geburtsjahr wurde falsch übermittelt";
	}
	if (strxlen($query['fullname']) > 128) {
		$error[] = "Bürgerlicher Name ist zu lang";
	}
	if (intval($query['temp']) < -12 && intval($query['temp']) > 12) {
		$error[] = 'Sie haben keine gültige Zeitzone ausgewählt';
	}
	if (!isset($cache[$query['opt_4']])) {
		$error[] = 'Ungültiges Design ausgewählt';
	}
	if (!isset($cache2[$query['opt_5']])) {
		$error[] = 'Ungültige Sprache ausgewählt';
	}
	if (!empty($query['pic']) && preg_match('/^(http:\/\/|www.)([\wäöüÄÖÜ@\-_\.]+)\:?([0-9]*)\/(.*)$/', $query['pic'], $url_ary)) {
		$query['pic'] = checkRemotePic($query['pic'], $url_ary, $query['id']);
	}
	elseif (empty($query['pic']) || !file_exists($query['pic'])) {
		$query['pic'] = '';
	}
	
	if (count($error) > 0) {	
		error('admin.php?action=members&job=edit&id='.$query['id'], $error);
	}
	else {
	    // Now we create the birthday... 
	    if (!$query['birthmonth'] && !$query['birthday'] && !$query['birthyear']) {
	    	$bday = '0000-00-00';
	    }
	    else {
	        $query['birthmonth'] = leading_zero($query['birthmonth']);
	        $query['birthday'] = leading_zero($query['birthday']);
	        $query['birthyear'] = leading_zero($query['birthyear'],4);
	        $bday = $query['birthyear'].'-'.$query['birthmonth'].'-'.$query['birthday'];
	    }
	    $query['icq'] = str_replace('-', '', $query['icq']);
    	if (!is_id($query['icq'])) {
    		$query['icq'] = 0;
	    }

		if (!empty($query['pw']) && strlen($query['pw']) >= $config['minpwlength']) {
			$md5 = md5($query['pw']);
			$update_sql = ", pw = '{$md5}' ";
		}
		else {
			$update_sql = ' ';
		}
		
		admin_customsave($query['id']);

		$db->query("UPDATE {$db->pre}user SET groups = '".$query['groups']."', timezone = '".$query['temp']."', opt_textarea = '".$query['opt_0']."', opt_pmnotify = '".$query['opt_1']."', opt_hidebad = '".$query['opt_2']."', opt_hidemail = '".$query['opt_3']."', template = '".$query['opt_4']."', language = '".$query['opt_5']."', pic = '".$query['pic']."', about = '".$query['comment']."', icq = '".$query['icq']."', yahoo = '".$query['yahoo']."', aol = '".$query['aol']."', msn = '".$query['msn']."', jabber = '".$query['jabber']."', birthday = '".$bday."', gender = '".$query['gender']."', hp = '".$query['hp']."', signature = '".$query['signature']."', location = '".$query['location']."', fullname = '".$query['fullname']."', skype = '".$query['skype']."', mail = '".$query['email']."', name = '".$query['name']."'".$update_sql." WHERE id = '".$user['id']."' LIMIT 1",__LINE__,__FILE__); 
		ok("admin.php?action=members&job=manage", 'Daten erfolgreich gespeichert!');
	}
}
elseif ($job == 'delete') {
	echo head();
	$delete = $gpc->get('delete', arr_int);
	if (in_array($my->id, $delete)) {
		$mykey = array_search($my->id, $delete);
		unset($delete[$mykey]);
	}
	if (count($delete) > 0) {
		$did = implode(',', $delete);
		$result = $db->query('SELECT * FROM '.$db->pre.'user WHERE id IN ('.$did.')');
		$olduserdata = file_get_contents('data/deleteduser.php');
		while ($user = $gpc->prepare($db->fetch_assoc($result))) {
			// Step 1: Write Data to File with old Usernames
			$olduserdata .= "\n".$user['id']."\t".$user['name'];
			$olduserdata = trim($olduserdata);
			// Step 2: Delete all pms
			$db->query("DELETE FROM {$db->pre}pm WHERE pm_to IN ({$did})");
			// Step 3: Search all old posts by an user, and update to guests post
			$db->query("UPDATE {$db->pre}replies SET name = '".$user['name']."', email = '".$user['mail']."' WHERE name = '".$user['id']."' AND email = ''");
			// Step 4: Search all old topics by an user, and update to guests post
			$db->query("UPDATE {$db->pre}topics SET name = '".$user['name']."' WHERE name = '".$user['id']."'");
			$db->query("UPDATE {$db->pre}topics SET last_name = '".$user['name']."' WHERE last_name = '".$user['id']."'");
			// Step 5: Delete pic
			removeOldImages('uploads/pics/', $user['id']);
		}
		$filesystem->file_put_contents('data/deleteduser.php', $olduserdata);
		// Step 6: Delete all abos
		$db->query("DELETE FROM {$db->pre}abos WHERE mid IN ({$did})");
		// Step 8: Delete as mod
		$db->query("DELETE FROM {$db->pre}moderators WHERE mid IN ({$did})");
		$delete = $gpc->get('delete', arr_int);
		// Step 9: Set uploads from member to guests-group
		$db->query("UPDATE {$db->pre}uploads SET mid = '0' WHERE mid IN ({$did})");
		// Step 10: Set post ratings from member to guests-group I
		$db->query("UPDATE {$db->pre}postratings SET mid = '0' WHERE mid IN ({$did})");
		// Step 11: Set post ratings from member to guests-group II
		$db->query("UPDATE {$db->pre}postratings SET aid = '0' WHERE aid IN ({$did})");
		// Step 12: Delete user himself
		$db->query("DELETE FROM {$db->pre}user WHERE id IN ({$did})");
		$anz = $db->affected_rows();
		// Step 13: Delete user's custom profile fields
		$db->query("DELETE FROM {$db->pre}userfields WHERE ufid IN ({$did})");
		ok('javascript:history.back(-1);', $anz.' members deleted');
	}
	else {
		error('javascript:history.back(-1);', 'Keine gültige Angabe gemacht.');
	}

}
elseif ($job == 'emaillist') {
	echo head();
	?>
<form name="form" method="post" action="admin.php?action=members&job=emaillist2">
 <table class="border" border="0" cellspacing="0" cellpadding="4" align="center">
  <tr> 
   <td class="obox" colspan="2"><b>Email-Liste erstellen</b></td>
  </tr>
  <tr> 
   <td class="mbox" width="50%">Trennzeichen:<br><span class="stext">Trennzeichen zwischen den Emailadressen. Keine Angabe = Kommagetrennt</span></td>
   <td class="mbox" width="50%"><textarea name="template" cols="10" rows="2"></textarea></td> 
  </tr>
  <tr> 
	<td class="mbox" width="50%">Empfänger:</td>
	<td class="mbox" width="50%"><select size="1" name="int1"><option value="1">Alle</option><option value="2" selected>Nur Mitglieder</option><option value="3">Nur Gäste</option></select></td> 
  </tr>
  <tr> 
   <td class="ubox" width="100%" colspan=2 align="center"><input type="submit" name="Submit" value="Submit"></td> 
  </tr>
 </table>
</form> 
	<?php
	echo foot();
}
elseif ($job == 'emaillist2') {
	echo head();
	$int1 = $gpc->get('int1', int);
	if ($int1 == 1) {
		$emails = array();
		$result = $db->query('SELECT mail FROM '.$db->pre.'user');
		while ($row = $db->fetch_num($result)) {
			$emails[] = $row[0];
		}
		$result = $db->query('SELECT email FROM '.$db->pre.'replies WHERE email != ""');
		while ($row = $db->fetch_num($result)) {
			$emails[] = $row[0];
		}
	}
	elseif ($int1 == 2) {
		$emails = array();
		$result = $db->query('SELECT mail FROM '.$db->pre.'user');
		while ($row = $db->fetch_num($result)) {
			$emails[] = $row[0];
		}
	}
	elseif ($int1 == 3) {
		$emails = array();
		$result = $db->query('SELECT email FROM '.$db->pre.'replies WHERE email != ""');
		while ($row = $db->fetch_num($result)) {
			$emails[] = $row[0];
		}
	}
	$emails = array_unique($emails);
	$template = $gpc->get('template', none);
	if (empty($template)) {
		$template = ',';
	}
	?>
 <table class="border" border="0" cellspacing="0" cellpadding="4" align="center">
  <tr> 
   <td class="obox"><b>Email-Liste erstellen</b></td>
  </tr>
  <tr> 
   <td class="mbox"><textarea class="fullwidth" cols="125" rows="25"><?php echo implode($template, $emails); ?></textarea></td>
  </tr>
 </table>	
	<?php
	echo foot();
}
elseif ($job == 'banned') {
	echo head();
	$content = file_get_contents('data/banned.php');
	$b = file_get_contents('data/bannedip.php');
	?>
<form name="form" method="post" action="admin.php?action=members&job=banned2">
 <table class="border" border="0" cellspacing="0" cellpadding="4" align="center">
  <tr> 
   <td class="obox" colspan=2><b>Seite für gebannte IP-Adressen</b></td>
  </tr>
  <tr> 
   <td class="mbox" width="30%">Inhalt d. Seite:<br><span class="stext">HTML und PHP sind möglich!</span></td>
   <td class="mbox" width="70%"><textarea name="template" rows="10" cols="90"><?php echo $content; ?></textarea></td> 
  </tr>
  <tr> 
   <td class="ubox" width="100%" colspan=2 align="center"><input type="submit" name="Submit" value="Abschicken"></td> 
  </tr>
 </table>
</form><br>
<form name="form" method="post" action="admin.php?action=members&job=banned3">
 <table class="border" border="0" cellspacing="0" cellpadding="4" align="center">
  <tr> 
   <td class="obox" colspan="2"><b>IP-Adressen verwalten</b></td>
  </tr>
  <tr>
   <td class="mbox" width="30%">
   IP-Adresse:<br />
   <span class="stext">Pro Zeile eine Emailadresse.<br />Um einen IP-Adressenbereich anzugeben, geben sie nur die erstezn Zeichen des Bereichs an (Bsp: "127.0." findet z.Bsp: "127.0.0.1")</span>
   </td>
   <td class="mbox" width="70%"><textarea name="ips" rows="10" cols="90"><?php echo $b; ?></textarea></td> 
  </tr>
  <tr> 
   <td class="ubox" colspan="2" align="center"><input type="submit" name="Submit" value="Submit"></td> 
  </tr>
 </table>
</form>
	<?php
	echo foot();
}
elseif ($job == 'banned2') {
	echo head();
	$filesystem->file_put_contents('data/banned.php', $gpc->get('template', none));
	ok('admin.php?action=members&job=banned', 'Seite wurde gespeichert');
}
elseif ($job == 'banned3') {
	echo head();
	$bannedip = file('data/bannedip.php');
	$bannedip = array_map('trim', $bannedip);
	$file = $gpc->get('ips', none);
	$file = trim($file);
	$filesystem->file_put_contents('data/bannedip.php',$file);
	ok('admin.php?action=members&job=banned', 'IP-Adressen wurden gespeichert.');
}
elseif ($job == 'search') {
	echo head();
	
	$loaddesign_obj = $scache->load('loaddesign');
	$design = $loaddesign_obj->get();
	
	$loadlanguage_obj = $scache->load('loadlanguage');
	$language = $loadlanguage_obj->get();
	
	$result = $db->query("SELECT id, title, name FROM {$db->pre}groups ORDER BY admin DESC , guest ASC , core ASC");
	?>
<form name="form" method="post" action="admin.php?action=members&job=search2">
 <table class="border" border="0" cellspacing="0" cellpadding="4" align="center">
  <tr> 
   <td class="obox" colspan="4">Mitglieder suchen</td>
  </tr>
  <tr>
	<td class="mbox" width="50%" colspan="4">
	<b>Hilfe:</b>
	<ul>
	<li>Sie können "%" und "_" als Platzhalter in den Suchbegriff einfügen. Ein "_" steht für ein einziges Zeichen, ein "%" steht für beliebig viele Zeichen. Die Platzhalter können nur mit den Vergleichsoperatoren <b>!=</b> und <b>=</b> verwendet werden.</li>
	<li>=<b></b> bedeutet <i>gleich</i>, <b>&lt;</b> bedeutet <i>kleiner als</i>, <b>&gt;</b> bedeutet <i>größer als</i>, <b>!=</b> bedeutet <i>nicht gleich</i>.</li>
	</ul>
	</td>
  </tr>
  <tr> 
   <td class="mbox" colspan="2">Genauigkeit:</td>
   <td class="mbox" colspan="2">
   <input type="radio" name="type" value="0"> <b>Oder</b> (mindestens eine der Eingaben muss stimmen)<br>
   <input type="radio" name="type" value="1" checked="checked">  <b>Und</b> (alle Eingaben müssen stimmen)
   </td> 
  </tr>
  <tr> 
   <td class="ubox" width="40%">&nbsp;</td>
   <td class="ubox" width="5%">Vergleichsoperator</td>
   <td class="ubox" width="50%">&nbsp;</td> 
   <td class="ubox" width="5%">Anzeigen</td> 
  </tr>
  <tr> 
   <td class="mbox">ID:</td>
   <td class="mbox" align="center"><select size="1" name="compare[id]">
      <option value="-1">&lt;</option>
      <option value="0" selected="selected">=</option>
      <option value="1">&gt;</option>
    </select></td>
   <td class="mbox"><input type="text" name="id" size="12"></td> 
   <td class="mbox"><input type="checkbox" name="show[id]" value="1" checked>Yes</td> 
  </tr>
  <tr> 
   <td class="mbox">Nickname:</td>
   <td class="mbox" align="center">=</td>
   <td class="mbox"><input type="text" name="name" size="50"></td> 
   <td class="mbox"><input type="checkbox" name="show[name]" value="1" checked>Yes</td> 
  </tr>
  <tr> 
   <td class="mbox">Emailadresse:</td>
   <td class="mbox" align="center">=</td>
   <td class="mbox"><input type="text" name="mail" size="50"></td> 
   <td class="mbox"><input type="checkbox" name="show[mail]" value="1" checked></td> 
  </tr>
  <tr> 
   <td class="mbox">Datum d. Registrierung:</td>
   <td class="mbox" align="center"><select size="1" name="compare[regdate]">
      <option value="-1">&lt;</option>
      <option value="0" selected="selected">=</option>
      <option value="1">&gt;</option>
    </select></td>
   <td class="mbox"><input type="text" name="regdate[1]" size="3">. <input type="text" name="regdate[2]" size="3">. <input type="text" name="regdate[3]" size="5"> (DD. MM. YYYY)</td> 
   <td class="mbox"><input type="checkbox" name="show[regdate]" value="1" checked></td> 
  </tr>
  <tr> 
   <td class="mbox">Vollständiger Name:</td>
   <td class="mbox" align="center">=</td>
   <td class="mbox"><input type="text" name="fullname" size="50"></td> 
   <td class="mbox"><input type="checkbox" name="show[fullname]" value="1" checked></td> 
  </tr>
  <tr> 
   <td class="mbox">Homepage:</td>
   <td class="mbox" align="center">=</td>
   <td class="mbox"><input type="text" name="hp" size="50"></td> 
   <td class="mbox"><input type="checkbox" name="show[hp]" value="1"></td> 
  </tr>
  <tr> 
   <td class="mbox">Wohnort:</td>
   <td class="mbox" align="center"><select size="1" name="compare[location]">
      <option value="0" selected="selected">=</option>
      <option value="2">!=</option>
    </select></td>
   <td class="mbox"><input type="text" name="location" size="50"></td> 
   <td class="mbox"><input type="checkbox" name="show[location]" value="1"></td> 
  </tr>
  <tr> 
   <td class="mbox">Geschlecht:</td>
   <td class="mbox" align="center"><select size="1" name="compare[gender]">
      <option value="0" selected="selected">=</option>
      <option value="2">!=</option>
    </select></td>
   <td class="mbox"><select name="gender" size="1">
   <option selected="selected" value="">Egal</option>
   <option value="x">Keine Angabe</option>
   <option value="m">Männlich</option>
   <option value="w">Weiblich</option>
   </select></td> 
   <td class="mbox"><input type="checkbox" name="show[gender]" value="1"></td> 
  </tr>
  <tr> 
   <td class="mbox">Geburtstag:</td>
   <td class="mbox" align="center"><select size="1" name="compare[birthday]">
      <option value="-1">&lt;</option>
      <option value="0" selected="selected">=</option>
      <option value="1">&gt;</option>
    </select></td>
   <td class="mbox"><input type="text" name="birthday[1]" size="3">. <input type="text" name="birthday[2]" size="3">. <input type="text" name="birthday[3]" size="5"> (DD. MM. YYYY)</td> 
   <td class="mbox"><input type="checkbox" name="show[birthday]" value="1"></td> 
  </tr>
  <tr> 
   <td class="mbox">Letzter Besuch:</td>
   <td class="mbox" align="center"><select size="1" name="compare[lastvisit]">
      <option value="-1">&lt;</option>
      <option value="0" selected="selected">=</option>
      <option value="1">&gt;</option>
    </select></td>
   <td class="mbox"><input type="text" name="lastvisit[1]" size="3">. <input type="text" name="lastvisit[2]" size="3">. <input type="text" name="lastvisit[3]" size="5"> (DD. MM. YYYY)</td> 
   <td class="mbox"><input type="checkbox" name="show[lastvisit]" value="1" checked></td> 
  </tr>
  <tr> 
   <td class="mbox">ICQ-Nummer:</td>
   <td class="mbox" align="center"><select size="1" name="compare[icq]">
      <option value="-1">&lt;</option>
      <option value="0" selected="selected">=</option>
      <option value="1">&gt;</option>
    </select></td>
   <td class="mbox"><input type="text" name="icq" size="12"></td> 
   <td class="mbox"><input type="checkbox" name="show[icq]" value="1"></td> 
  </tr>
  <tr> 
   <td class="mbox">Yahoo-ID:</td>
   <td class="mbox" align="center">=</td>
   <td class="mbox"><input type="text" name="yahoo" size="50"></td> 
   <td class="mbox"><input type="checkbox" name="show[yahoo]" value="1"></td> 
  </tr>
  <tr> 
   <td class="mbox">AOL-Name:</td>
   <td class="mbox" align="center">=</td>
   <td class="mbox"><input type="text" name="aol" size="50"></td> 
   <td class="mbox"><input type="checkbox" name="show[aol]" value="1"></td> 
  </tr>
  <tr> 
   <td class="mbox">MSN-Adresse:</td>
   <td class="mbox" align="center">=</td>
   <td class="mbox"><input type="text" name="msn" size="50"></td> 
   <td class="mbox"><input type="checkbox" name="show[msn]" value="1"></td> 
  </tr>
  <tr> 
   <td class="mbox">Jabber-Adresse:</td>
   <td class="mbox" align="center">=</td>
   <td class="mbox"><input type="text" name="jabber" size="50"></td> 
   <td class="mbox"><input type="checkbox" name="show[jabber]" value="1"></td> 
  </tr>
  <tr> 
   <td class="mbox">Skype-Name:</td>
   <td class="mbox" align="center">=</td>
   <td class="mbox"><input type="text" name="skype" size="50"></td> 
   <td class="mbox"><input type="checkbox" name="show[skype]" value="1"></td> 
  </tr>
  <tr> 
   <td class="mbox">Zeitzone:</td>
   <td class="mbox" align="center"><select size="1" name="compare[timezone]">
      <option value="0" selected="selected">=</option>
      <option value="2">!=</option>
    </select></td>
   <td class="mbox"><select name="timezone"> 
	<option selected="selected" value="">Egal</option>
	<option value="-12">(GMT -12:00) Eniwetok, Kwajalein</option>
	<option value="-11">(GMT -11:00) Midway-Inseln, Samoa</option>
	<option value="-10">(GMT -10:00) Hawaii</option>
	<option value="-9">(GMT -09:00) Alaska</option>
	<option value="-8">(GMT -08:00) Tijuana, Lod Angeles, Seattle, Vancouver</option>
	<option value="-7">(GMT -07:00) Arizona, Denver, Salt Lake City, Calgary</option>
	<option value="-6">(GMT -06:00) Mexiko-Stadt, Saskatchewan, Zentralamerika</option>
	<option value="-5">(GMT -05:00) Bogotá, Lima, Quito, Indiana (Ost), New York, Toronto</option>
	<option value="-4">(GMT -04:00) Caracas, La Paz, Montreal, Quebec, Santiago</option>
	<option value="-3.5">(GMT -03:30) Neufundland</option>
	<option value="-3">(GMT -03:00) Brasilia, Buenos Aires, Georgetown, Grönland</option>
	<option value="-2">(GMT -02:00) Mittelatlantik</option>
	<option value="-1">(GMT -01:00) Azoren, Kapverdische Inseln</option>
	<option value="0">(GMT) Casablance, Monrovia, Dublin, Edinburgh, Lissabon, London</option>
	<option value="+1">(GMT +01:00) Amsterdam, Berlin, Bern, Rom, Stockholm, Wien, Paris</option>
	<option value="+2">(GMT +02:00) Athen, Istanbul, Minsk, Kairo, Jerusalem</option>
	<option value="+3">(GMT +03:00) Bagdad, Moskau, Nairobi</option>
	<option value="+3.5">(GMT +03:30) Teheran</option>
	<option value="+4">(GMT +04:00) Muskat, Tiflis</option>
	<option value="+4.5">(GMT +04:30) Kabul</option>
	<option value="+5">(GMT +05:00) Islamabad</option>
	<option value="+5.5">(GMT +05:30) Kalkutta, Neu-Delhi</option>
	<option value="+5.75">(GMT +05:45) Katmandu</option>
	<option value="+6">(GMT +06:00) Almaty, Nowosibirsk, Dhaka</option>
	<option value="+6.5">(GMT +06:30) Rangun</option>
	<option value="+7">(GMT +07:00) Bangkok, Hanoi, Jakarta</option>
	<option value="+8">(GMT +08:00) Ulan Bator, Singapur, Peking, Hongkong</option>
	<option value="+9">(GMT +09:00) Irkutsk, Osaka, Sapporo, Tokyo, Seoul</option>
	<option value="+9.5">(GMT +09:30) Adelaide, Darwin</option>
	<option value="+10">(GMT +10:00) Brisbane, Canberra, Melbourne, Sydney, Wladiwostok</option>
	<option value="+11">(GMT +11:00) Salomonen, Neukaledonien</option>
	<option value="+12">(GMT +12:00) Auckland, Wellington, Fidschi, Kamtschatka</option>
</select>	</td> 
   <td class="mbox"><input type="checkbox" name="show[timezone]" value="1"></td> 
  </tr>
  <tr> 
   <td class="mbox">Gruppen-ID:</td>
   <td class="mbox" align="center">=</td>
   <td class="mbox"><select size="3" name="groups" multiple="multiple">
      <option selected="selected" value="">Egal</option>
	  <?php while ($row = $gpc->prepare($db->fetch_assoc($result))) { ?>
		<option value="<?php echo $row['id']; ?>"><?php echo $row['name']; ?></option>
	  <?php } ?>
    </select></td> 
   <td class="mbox"><input type="checkbox" name="show[groups]" value="1"></td> 
  </tr>
  <tr> 
   <td class="mbox">Design:</td>
   <td class="mbox" align="center"><select size="1" name="compare[template]">
      <option value="0" selected="selected">=</option>
      <option value="2">!=</option>
    </select></td>
   <td class="mbox"><select name="template">
	<option selected="selected" value="">Egal</option>
	<?php foreach ($design as $row) { ?>
	<option value="<?php echo $row['id']; ?>"><?php echo $row['name']; ?></option>
	<?php } ?>
</select></td> 
   <td class="mbox"><input type="checkbox" name="show[template]" value="1"></td> 
  </tr>
  <tr> 
   <td class="mbox">Sprache:</td>
   <td class="mbox" align="center"><select size="1" name="compare[language]">
      <option value="0" selected="selected">=</option>
      <option value="2">!=</option>
    </select></td>
   <td class="mbox"><select name="language">
	<option selected="selected" value="">Egal</option>
	<?php foreach ($language as $row) { ?>
	<option value="<?php echo $row['id']; ?>"><?php echo $row['language']; ?></option>
	<?php } ?>
</select></td> 
   <td class="mbox"><input type="checkbox" name="show[language]" value="1"></td> 
  </tr>
  <tr> 
   <td class="mbox">Status:</td>
   <td class="mbox" align="center"><select size="1" name="compare[confirm]">
      <option value="0" selected="selected">=</option>
      <option value="2">!=</option>
    </select></td>
   <td class="mbox"><select size="1" name="confirm">
      <option selected="selected" value="">Egal</option>
      <option value="11">Freigeschaltet</option>
      <option value="10">User muss sich noch per E-Mail freischalten</option>
      <option value="01">User muss noch vom Admin freigeschaltet werden</option>
      <option value="00">User ist weder vom Admin noch per E-Mail freigeschaltet</option>
    </select></td> 
   <td class="mbox"><input type="checkbox" name="show[confirm]" value="1"></td> 
  </tr>
  <tr> 
   <td class="ubox" align="center" colspan="4"><input type="submit" value="Abschicken"></td> 
  </tr>
 </table>
</form>
	<?php
    echo foot();
}
elseif ($job == 'search2') {
	echo head();

	define('DONT_CARE', md5(microtime()));
	$fields = 	array(
	'id' => array('ID', int),
	'name' => array('Nickname', str),
	'mail' => array('Email', str),
	'regdate' => array('Registration', arr_int),
	'fullname' => array('Vollständiger Name', str),
	'hp' => array('Homepage', str),
	'location' => array('Wohnort', str),
	'gender' => array('Geschlecht', str),
	'birthday' => array('Geburtstag', arr_none),
	'lastvisit' => array('Letzter Besuch', arr_int),
	'icq' => array('ICQ', int),
	'yahoo' => array('Yahoo', str),
	'aol' => array('AOL', str),
	'msn' => array('MSN', str),
	'skype' => array('Skype', str),
	'jabber' => array('Jabber', str),
	'timezone' => array('Zeitzone', int),
	'groups' => array('Gruppen', arr_int),
	'template' => array('Design', int),
	'language' => array('Sprache', int),
	'confirm' => array('Status', none)
	);
	$change = array('m' => 'male', 'w' => 'female', '' => '-');

	$loaddesign_obj = $scache->load('loaddesign');
	$design = $loaddesign_obj->get();
	
	$loadlanguage_obj = $scache->load('loadlanguage');
	$language = $loadlanguage_obj->get();
	
	$type = $gpc->get('type', int);
	if ($type == 0) {
		$sep = ' OR ';
	}
	else {
		$sep = ' AND ';
	}
	
	$compare = $gpc->get('compare', arr_int);
	foreach ($compare as $key => $cmp) {
		if ($cmp == -1) {
			$compare[$key] = '<';
		}
		elseif ($cmp == 1) {
			$compare[$key] = '>';
		}
		elseif ($cmp == 2) {
			$compare[$key] = '!=';
		}
		else {
			$compare[$key] = '=';
		}
	}
	$show = $gpc->get('show', arr_none);
	$show = array_keys($show);
	$show = array_intersect($show, array_keys($fields));
	$sqlkeys = array_unique(array_intersect(array_merge($show, array('id', 'name')), array_keys($fields)));
	$sqlwhere = array();
	$input = array();
	foreach ($fields as $key => $data) {
		$value = $gpc->get($key, none);
		if (is_array($value)) {
			$value = implode('', $value);
		}
		if (strpos($value, '%') !== false || strpos($value, '_') !== false) {
			$value = $gpc->get($key, none, DONT_CARE);
			$value = $gpc->save_str($value);
		}
		else {
			$value = $gpc->get($key, $data[1], DONT_CARE);
		}
		if ($key == 'regdate' || $key == 'lastvisit') {
			if (array_sum($value) != 0) { // for php version >= 5.1.0
				$input[$key] =  @mktime(0, 0, 0, intval($value[2]), intval($value[1]), intval($value[3]));
				if ($input[$key] == -1 || $input[$key] == false) { // -1 for php version < 5.1.0, false for php version >= 5.1.0
					$input[$key] = DONT_CARE;
				}
			}
			else {
				$input[$key] = DONT_CARE;
			}
		}
		elseif ($key == 'birthday') {
			$value[1] = intval(trim($value[1]));
			if ($value[1] < 1 || $value[1] > 31) {
				$value[1] = '%';
			}
			$value[2] = intval(trim($value[2]));
			if ($value[2] < 1 || $value[2] > 12) {
				$value[2] = '%';
			}
			if (strlen($value[3]) == 2) {
				$value[3] += 2000;
			}
			else {
				$value[3] = intval(trim($value[3]));
			}
			if ($value[3] < 1900 || $value[3] > 2100) {
				$value[3] = '%';
			}
			if ($value[1] == '%' && $value[2] == '%' && $value[3] == '%') {
				$input[$key] = DONT_CARE;
			}
			else {
				$input[$key] = $value[3].'-'.$value[2].'-'.$value[1];
			}
		}
		elseif ($key == 'gender') {
			if (empty($value)) {
				$input[$key] = DONT_CARE;
			}
			elseif ($value == 'x') {
				$input[$key] = '';
			}
			else {
				$input[$key] = $value;
			}
		}
		else {
			if (isset($_REQUEST[$key]) && $_REQUEST[$key] == '') {
				$input[$key] = DONT_CARE;
			}
			else {
				$input[$key] = $value;
			}
		}

		if (!isset($compare[$key])) {
			$compare[$key] = '=';
		}

		if ($input[$key] != DONT_CARE) {
			if (strpos($input[$key], '%') !== false || strpos($input[$key], '_') !== false) {
				if ($compare[$key] == '=') {
					$compare[$key] = 'LIKE';
				}
				elseif ($compare[$key] == '!=') {
					$compare[$key] = 'NOT LIKE';
				}
			}
			$sqlwhere[] = " `{$key}` {$compare[$key]} '{$input[$key]}' ";
		}
	}
	$colspan = count($show) + 1;

	if (count($sqlwhere) > 0) {
		$query = 'SELECT '.implode(',',$sqlkeys).' FROM '.$db->pre.'user WHERE '.implode($sep, $sqlwhere).' ORDER BY name';
		$result = $db->query($query, __LINE__, __FILE__);
		$count = $db->num_rows($result);
	}
	else {
		$count = 0;
	}
	?>
	<form name="form" action="admin.php?action=members&job=delete" method="post">
	<table class="border" border="0" cellspacing="0" cellpadding="4" align="center">
		<tr> 
		  <td class="obox" colspan="<?php echo $colspan; ?>"><b>Mitglieder suchen</b></td>
		</tr>
		<?php if ($count == 0) { ?>
		<tr> 
		  <td class="mbox" colspan="<?php echo $colspan; ?>">Es konnte kein Mitglied gefunden werden.</td>
		</tr>
		<?php } else { ?>
			<tr> 
			  <td class="ubox" colspan="<?php echo $colspan; ?>"><?php echo $count; ?> gefundene Mitglieder</td>
			</tr>
			<tr>
			  <td class="obox">DEL</td>
			  <?php foreach ($show as $key) { ?>
			  <td class="obox"><?php echo $fields[$key][0]; ?></td>
			  <?php } ?>
			</tr>
			<?php
			while ($row = $gpc->prepare($db->fetch_assoc($result))) {
				if (isset($row['lastvisit'])) {
					$row['lastvisit'] = date('d.m.Y H:i', $row['lastvisit']);
				}
				if (isset($row['regdate'])) {
					$row['regdate'] = date('d.m.Y', $row['regdate']);
				}
				if (empty($row['icq'])) {
					$row['icq'] = '-';
				}
				if (empty($row['timezone'])) {
					$row['timezone'] = $config['timezone'];
				}
				if (isset($row['gender'])) {
					$row['gender'] = $change[$row['gender']];
				}
				if (!isset($row['birthday']) || intval($row['birthday']) == 0) {
					$row['birthday'] = '-';
				}
				else {
					$bd = explode('-', $row['birthday']);
					$bd = array_reverse($bd);
					$row['birthday'] = implode('.', $bd);
				}
				if (isset($row['template']) && isset($design[$row['template']])) {
					$row['template'] = $design[$row['template']]['name'];
				}
				if (isset($row['language']) && isset($language[$row['language']])) {
					$row['language'] = $language[$row['language']]['language'];
				}
				if (isset($row['confirm'])) {
			      	if ($row['confirm'] == "11") { $row['confirm'] = 'Freigeschaltet'; }
			      	elseif ($row['confirm'] == "10") { $row['confirm'] = 'User muss sich noch per E-Mail freischalten'; }
			      	elseif ($row['confirm'] == "01") { $row['confirm'] = 'User muss noch vom Admin freigeschaltet werden'; }
			      	elseif ($row['confirm'] == "00") { $row['confirm'] = 'User ist weder vom Admin noch per E-Mail freigeschaltet'; }
			    }
			?>
		    <tr>
		      <td class="mbox"><input type="checkbox" name="delete[]" value="<?php echo $row['id']; ?>"></td> 
			  <?php foreach ($show as $key) { ?>
			  <td class="mbox"><a href="admin.php?action=members&job=edit&id=<?php echo $row['id']; ?>"><?php echo $row[$key]; ?></a></td>
			  <?php } ?>
			</tr>
			<?php } ?>
			<tr> 
			  <td class="ubox" colspan="<?php echo $colspan; ?>"><input type="submit" name="submit" value="Löschen"></td>
			</tr>
		<?php } ?>
	</table>
	</form>
	<?php
    echo foot();
}
elseif ($job == 'activate') {
	echo head();

	$result = $db->query('SELECT * FROM '.$db->pre.'user WHERE confirm != "11" ORDER BY regdate DESC');
	?>
	<form name="form" action="admin.php?action=members&job=delete" method="post">
	<table class="border" border="0" cellspacing="0" cellpadding="4" align="center">
		<tr> 
		  <td class="obox" colspan="4">Moderate &amp; Unlock Members</td>
		</tr>
		<tr>
		  <td class="ubox" width="30%">Name</td>
		  <td class="ubox" width="10%">Email</td>
		  <td class="ubox" width="15%">Registered</td>
		  <td class="ubox" width="45%">Status</td>
		</tr>
	<?php
	while ($row = $gpc->prepare($db->fetch_object($result))) { 
		$row->regdate = gmdate('d.m.Y', times($row->regdate));
		if ($row->lastvisit == 0) {
			$row->lastvisit = 'Never';
		} else {
			$row->lastvisit = gmdate('d.m.Y', times($row->lastvisit));
		}
		?>
	    <tr>
		  <td class="mbox"><a title="Edit" href="admin.php?action=members&job=edit&id=<?php echo $row->id; ?>"><?php echo $row->name; ?></a></td> 
		  <td class="mbox" align="center"><a href="mailto:<?php echo $row->mail; ?>">Email</a></td> 
		  <td class="mbox"><?php echo $row->regdate; ?></td>
		  <td class="mbox"><ul>
		  <?php if ($row->confirm == '00' || $row->confirm == '01') { ?>
		  <li><strong><a href="admin.php?action=members&job=confirm&id=<?php echo $row->id; ?>">User freischalten</a></strong></li>
		  <?php } if ($row->confirm == '00' || $row->confirm == '10') { ?>
		  <li>User muss sich noch per E-Mail freischalten</li>
		  <?php } ?>
		  <li>User löschen: <input type="checkbox" name="delete[]" value="<?php echo $row->id; ?>"></li>
		  </ul></td>
		</tr>
		<?php
	} 
	?>
		<tr> 
		  <td class="ubox" colspan="4" align="center"><input type="submit" name="submit" value="Delete selected User"></td>
		</tr>
	</table>
	</form>
	<?php
    echo foot();
}
elseif ($job == 'confirm') {
	echo head();

	$id = $gpc->get('id', int);
	$result = $db->query('SELECT id, name, confirm, mail FROM '.$db->pre.'user WHERE id = "'.$id.'" LIMIT 1');
	$row = $db->fetch_assoc($result);
	
	if ($row['confirm'] == '00') {
		$confirm = '10';
	}
	else {
		$confirm = '11';
	}
	
	$db->query('UPDATE '.$db->pre.'user SET confirm = "'.$confirm.'" WHERE id = "'.$row['id'].'" LIMIT 1', __LINE__, __FILE__);
	
	// Send Mail
	$content = $lang->get_mail('admin_confirmed');
	xmail(array('0' => array('mail' => $row['mail'])), array(), $content['title'], $content['comment']);
	
	ok('admin.php?action=members&job=activate', 'Mitglied wurde freigeschaltet!');
}
elseif ($job == 'ips') {
	$username = $gpc->get('username', str);
	$ipaddress = $gpc->get('ipaddress', str);
	$userid = $gpc->get('id', int);

	if (!empty($username)) {
		$result = $db->query("SELECT id, name FROM {$db->pre}user WHERE name = '{$username}' LIMIT 1", __LINE__, __FILE__);
		$userinfo = $db->fetch_assoc($result);
		$userid = $userinfo['id'];
		if (!is_id($userid)) {
			error('admin.php?action=members&job=ip', 'Invalid user specified!');
		}
	}

	echo head();
	if (!empty($ipaddress) || $userid > 0) {
		if (!empty($ipaddress)) {
			$hostname = @gethostbyaddr($ipaddress);
			if (!$hostname OR $hostname == $ipaddress) {
				$hostname = 'Could not resolve Hostname';
			}
			$users = $db->query("SELECT DISTINCT u.id, u.name, r.ip FROM {$db->pre}replies AS r, {$db->pre}user AS u  WHERE u.id = r.name AND r.ip LIKE '{$ipaddress}%' AND r.ip != '' ORDER BY u.name", __LINE__, __FILE__);
			?>
			<table align="center" class="border">
			<tr>
				<td class="obox">IP Address search for IP Address &quot;<?php echo $ipaddress; ?>&quot;</td>
			</tr>
			<tr>
				<td class="ubox">
				<a href="usertools.php?do=iphost&amp;ip=<?php echo $ipaddress; ?>"><?php echo $ipaddress; ?></a>: <b><?php echo htmlspecialchars($hostname); ?></b>
				</td>
			</tr>
			<tr>
				<td class="mbox">
				<ul>
				<?php while ($user = $db->fetch_assoc($users)) { ?>
					<li>
					<a href="admin.php?action=members&amp;job=edit&amp;id=<?php echo $user['id']; ?>"><b><?php echo $user['name']; ?></b></a> &nbsp;&nbsp;&nbsp;
					<a href="admin.php?action=members&amp;job=iphost&amp;ip=<?php echo $user['ip']; ?>" title="Resolve Address"><?php echo $user['ip']; ?></a> &nbsp;&nbsp;&nbsp; 
					[<a href="admin.php?action=members&amp;job=ips&amp;id=<?php echo $user['id']; ?>&amp;username=<?php echo urlencode($user['name']); ?>">View other IP Addresses for this User</a>]
					</li>
					<?php
				}
				if ($db->num_rows() == 0) {
					?>
					<li>No matches found!</li>
					<?php
				}
				?>
				</ul>
				</td>
			</tr>
			</table>
			<br />
		<?php } if ($userid > 0) { ?>
			<table align="center" class="border">
			<tr>
				<td class="obox">Search IP Addresses for Username &quot;<?php echo $userinfo['name']; ?>&quot;</td>
			</tr>
			<tr>
				<td class="mbox">
				<ul>
				<?php
				$ips = $db->query("SELECT DISTINCT ip FROM {$db->pre}replies WHERE name = '{$userid}' AND ip != '{$ipaddress}' AND ip != '' ORDER BY ip", __LINE__, __FILE__);
				while ($ip = $db->fetch_assoc($ips)) {
					?>
					<li>
					<a href="admin.php?action=members&job=iphost&amp;ip=<?php echo $ip['ip']; ?>" title="Resolve Address"><?php echo $ip['ip']; ?></a> &nbsp;&nbsp;&nbsp;
					[<a href="admin.php?action=members&amp;job=ips&amp;ipaddress=<?php echo $ip['ip']; ?>">Find more Users with this IP Address</a>]
					</li>
					<?php
				}
				if ($db->num_rows() == 0) {
					?>
					<li>No matches found!</li>
					<?php
				}
				?>
				</ul>
				</td>
			</tr>
			</table>
			<br />
			<?php
		}
	}
	?>
	<form action="admin.php?action=members&amp;job=ips" method="post">
	<table align="center" class="border">
	<tr>
		<td class="obox" colspan="2">Search IP Addresses</td>
	</tr>
	<tr>
		<td class="mbox">Find Users by IP Address<br /><span class="stext">You may enter a partial IP Address</span></td>
		<td class="mbox"><input type="text" name="ipaddress" value="<?php echo $ipaddress; ?>" size="35" /></td>
	</tr>
	<tr>
		<td class="mbox">Find IP Addresses matching a Username</td>
		<td class="mbox"><input type="text" name="username" value="<?php echo $username; ?>" size="35" /></td>
	</tr>
	<tr>
		<td class="ubox" colspan="2" align="center"><input type="submit" value="Find" /></td>
	</tr>
	</table>
	</form>
	<?php
	echo foot();
}
elseif ($job == 'iphost') {
	$ip = $gpc->get('ip', str);
	$resolvedip = @gethostbyaddr($ip);
	if ($resolvedip == $ip) {
		$host = '<i>Not Available</i>';
	}
	else {
		$host = htmlspecialchars($resolvedip);
	}
	echo head();
	?>
	<table align="center" class="border">
	<tr>
		<td class="obox" colspan="2">Resolve IP Address</td>
	</tr>
	<tr>
		<td class="mbox">IP Address</td>
		<td class="mbox"><a href="http://www.ripe.net/perl/whois?form_type=simple&amp;do_search=Search&amp;searchtext=<?php echo $ip; ?>" target="_blank"><?php echo $ip; ?></a></td>
	</tr>
	<tr>
		<td class="mbox">Host Name</td>
		<td class="mbox"><?php echo $host; ?></td>
	</tr>
	</table>
	<?php
	echo foot();
}
?>
