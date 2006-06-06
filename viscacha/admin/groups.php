<?php
if (isset($_SERVER['PHP_SELF']) && basename($_SERVER['PHP_SELF']) == "groups.php") die('Error: Hacking Attempt');

$gls = array(
'admin' => 'Is Administrator',
'gmod' => 'Is Global Moderator',
'guest' => 'Is Guest',
'members' => 'Can view Memberlist',
'profile' => 'Can view Profiles',
'pdf' => 'Can view PDF-Files',
'pm' => 'Can use PM',
'wwo' => 'Can view Who is Online',
'search' => 'Can use Search',
'team' => 'Can view Teamlist',
'usepic' => 'Can use (own) Avatar',
'useabout' => 'Create (own) Personal Page',
'usesignature' => 'Can use (own) Signature',
'downloadfiles' => 'Can download Attachements',
'forum' => 'Can view Forums',
'posttopics' => 'Can start a new Thread',
'postreplies' => 'Can write a reply',
'addvotes' => 'Can start a Poll',
'attachments' => 'Can add Attachements',
'edit' => 'Can edit own Posts',
'voting' => 'Can vote',
'docs' => 'Can view Documents/Pages'
);
$glk = array_keys($gls);
$gll = array(
'admin' => 'The user ist he highest ranked Administrator in the forum. He may use this admincenter and has full control of the forum!',
'gmod' => 'The user will automatically be moderator in all forums and can use all options and actions on topics.',
'guest' => 'The users in this usergroup are (not registered) guests.',
'members' => 'May view the memberlist and use eventually observably data.',
'profile' => 'The user may view the profiles of the members and use eventually observably data.',
'pdf' => 'The user may download particular topics as PDF-file.',
'pm' => 'The user may use the Private Messaging (PM) System. He can send, receive, administer and archive private messages.',
'wwo' => 'May view the where-is-who-online-list with the users residence.',
'search' => 'May use the Search and view the results.',
'team' => 'May view the teamlist with administrators, global moderators and moderators.',
'usepic' => 'May upload his own picture for his profile (frequently named avatar) or indicate an URL to a picture.',
'useabout' => 'May create a personal site in his user profile.',
'usesignature' => 'The user may create his own signature.',
'downloadfiles' => 'The user may view and download attached files.',
'forum' => 'The user may generally view the forums and read them.',
'posttopics' => 'New topics may be started.',
'postreplies' => 'Answers to topics may be written.',
'addvotes' => 'Polls may be created within topics.',
'attachments' => 'The user may attach files to his post.',
'edit' => 'The user may edit and delete his own posts.',
'voting' => 'The user may participate in polls in topics.',
'docs' => 'May view all documents &amp; pages.'
);

$guest_limitation = array('admin', 'gmod', 'pm', 'usepic', 'useabout', 'usesignature', 'voting', 'edit');

if ($job == 'manage') {
	echo head();
	$result = $db->query("SELECT * FROM {$db->pre}groups ORDER BY admin DESC , guest ASC , core ASC");
	$cache = array();
	$delete = 0;
    while ($row = $db->fetch_assoc($result)) {
    	if ($row['core'] == 0) {
    		$delete = 1;
    	}
        $cache[] = $row;
    }
    $colspan = count($glk)+$delete;
	?>
<form name="form" method="post" action="admin.php?action=groups&job=delete">
 <table class="border">
  <tr> 
   <td class="obox" colspan="<?php echo $colspan+4; ?>">Usergroup Manager</td>
  </tr>
  <tr class="ubox">
  	<?php if ($delete == 1) { ?><td valign="bottom"><b>Delete</b></td><?php } ?>
  	<td valign="bottom"><b>Edit</b></td>
    <td valign="bottom"><b>Name<br />Public Title</b></td>
	<td valign="bottom"><b>ID</b></td>
	<?php foreach ($gls as $txt) { ?>
   	<td valign="bottom"><?php txt2img($txt); ?></td>
	<?php } ?>
   	<td valign="bottom"><?php txt2img('Floodcheck (sec.)'); ?></td>
  </tr>
  <?php
  foreach ($cache as $row) {
  	$guest = ($row['guest'] == 1 && $row['core'] == 1);
  ?>
  <tr class="mbox">
  <?php if ($delete == 1) { ?>
  	<td>
  	<?php if ($row['core'] == 0) { ?>
  		<input type="checkbox" name="delete[]" value="<?php echo $row['id']; ?>">
  	<?php } else { ?>
  		<font class="stext">Core</font>
  	<?php } ?>
  	</td>
  <?php } ?>
  	<td><input type="radio" name="edit" value="<?php echo $row['id']; ?>"></td>
    <td nowrap="nowrap"><?php echo $row['name']; ?><br /><?php echo $row['title']; ?></td>
	<td><?php echo $row['id']; ?></td>
	<?php 
	foreach ($glk as $txt) {
	   	$clickable = !($guest && array_search($txt, $guest_limitation) !== false);
	   	if ($txt == 'guest') {
	   		$clickable = false;
	   	}
	   	$js = iif ($clickable, ' onmouseover="HandCursor(this)" onclick="ajax_noki(this, \'action=groups&job=ajax_changeperm&id='.$row['id'].'&key='.$txt.'\')"');
	   	$js .= iif(!$clickable, ' style="-moz-opacity: 0.4; opacity: 0.4; filter:Alpha(opacity=40, finishopacity=0);"', '');
	   	echo '<td align="center"'.iif(!$clickable, ' class="mmbox"').'>';
	   	echo noki($row[$txt], $js);
	   	echo '</td>';
	}
	?>
   	<td><?php echo $row['flood']; ?></td>
  </tr>
  <?php } ?>
  <tr> 
   <td class="ubox" colspan="<?php echo $colspan+4; ?>" align="center">
   <?php if ($delete == 1) { ?><input type="submit" name="submit_delete" value="Delete"><?php } ?>
   <input type="submit" name="submit_edit" value="Edit">
   </td> 
  </tr>
 </table>
</form> 
	<?php
	echo foot();
}
elseif ($job == 'ajax_changeperm') {
	$id = $gpc->get('id', int);
	$key = $gpc->get('key', str);
	if(!is_id($id) || !isset($gls[$key])) {
		die('The id or the key is not valid!');
	}
	$result = $db->query("SELECT g.{$key}, g.core FROM {$db->pre}groups AS g WHERE id = '{$id}' LIMIT 1");
	$perm = $db->fetch_assoc($result);
	if (($key == 'admin' || $key == 'guest') && $perm['core'] == 1) {
		die('This is not allowed!');
	}
	$perm = invert($perm[$key]);
	$db->query("UPDATE {$db->pre}groups AS g SET g.{$key} = '{$perm}' WHERE id = '{$id}' LIMIT 1");
	$delobj = $scache->load('group_status');
	$delobj->delete();
	$delobj = $scache->load('team_ag');
	$delobj->delete();
	die(strval($perm));
}
elseif ($job == 'add') {
	echo head();
	$result = $db->query("SELECT * FROM {$db->pre}groups ORDER BY admin DESC , guest ASC , core ASC");
	$cache = array();
	while ($row = $db->fetch_assoc($result)) {
		$cache[] = $row;
	}
	?>
<form name="form" method="post" action="admin.php?action=groups&job=add2">
 <table class="border">
  <tr> 
   <td class="obox" colspan="2">Add a new Usergroup - Settings and Permissions</td>
  </tr>
  <tr> 
   <td class="ubox" colspan="2">Assumption of permissions of other groups:</td>
  </tr>
  <tr>
   <td class="mbox" width="50%">Group:<br /><span class="stext">If you indicate a group here, the data below will be ignored!</span></td>
   <td class="mbox" width="50%">
   <select name="copy">
   <option value="0">-Set data "by hand" (manually)-</option>
   <?php foreach ($cache as $row) { ?>
   <option value="<?php echo $row['id']; ?>"><?php echo $row['name']; ?></option>
   <?php } ?>
   </select>
   </td>
  </tr>
  <tr>
   <td class="mbox" width="50%">Also assumpt Forum rights<br /><span class="stext">Use the permissions set for the group indicated above also for this group.</span></td>
   <td class="mbox" width="50%"><input type="checkbox" name="copyf" value="1" /></td>
  </tr>
  <tr> 
   <td class="ubox" colspan="2">Gruppenrechte "von Hand" setzen:</td>
  </tr>
  <tr>
   <td class="mbox" width="50%">Werte aus einer anderen Gruppe eintragen:<br />
   <span class="stext">Sie k�nnen die Checkboxen unterhalb mit den Werten einer anderen Gruppe f�llen und dann �ndern.</span></td>
   <td class="mbox" width="50%">
<script language="JavaScript" type="text/javascript">
<!--
var v = new Array();
<?php foreach ($cache as $row) { ?>
v['<?php echo $row['id']; ?>'] = new Array();
v['<?php echo $row['id']; ?>']['flood'] = <?php echo $row['flood']; ?>;
<?php foreach ($glk as $key) { if ($key != 'guest') { ?>
v['<?php echo $row['id']; ?>']['<?php echo $key; ?>'] = <?php echo $row[$key]; ?>;
<?php } } } ?>
function setGroupBoxes(sel) {
	id = sel.value;
	f = FetchElement('flood');
	if (id == 0) { f.value = 20; }
	else { f.value = v[id]['flood']; }
<?php foreach ($glk as $key) { if ($key != 'guest') { ?>
	f = FetchElement('<?php echo $key; ?>');
   	if (id == 0) { f.checked = 0; }
   	else { f.checked = v[id]['<?php echo $key; ?>']; }
<?php } } ?>
}
-->
</script>
   <select name="template" onchange="setGroupBoxes(this);">
   <option value="0">-Keine Checkbox setzen-</option>
   <?php foreach ($cache as $row) { ?>
   <option value="<?php echo $row['id']; ?>"><?php echo $row['name']; ?></option>
   <?php } ?>
   </select>
   </td>
  </tr>
  <?php
  foreach ($glk as $key) {
  if ($key != 'guest') {
  ?>
  <tr>
   <td class="mbox" width="50%"><?php echo $gls[$key]; ?><br /><span class="stext"><?php echo $gll[$key]; ?></span></td>
   <td class="mbox" width="50%"><input type="checkbox" name="<?php echo $key; ?>" id="<?php echo $key; ?>" value="1" /></td>
  </tr>
  <?php } } ?>
  <tr>
	<td class="mbox" width="50%">Floodcheck (in sec.)<br /><span class="stext">Zeit in der max. 1 Formular angesandt werden darf - um Spam zu verhindern</span></td>
   	<td class="mbox" width="50%"><input type="text" name="flood" id="flood" size="3" value="20"></td>
  </tr>
 </table>
 <br />
 <table class="border">
  <tr> 
   <td class="obox" colspan="2">Add a new Usergroup - Settings</td>
  </tr>
  <tr>
      <td class="mbox" width="50%">Interner Name d. Gruppe:<br /><span class="stext">Ein interner Name f�r die Benutzergruppe. Wird im Forum nicht angezeigt!</span></td>
      <td class="mbox" width="50%"><input type="text" name="name" size="35"></td>
  </tr><tr>
      <td class="mbox" width="50%">�ffentlicher Titel:<br /><span class="stext">Der �ffentliche Benutzertitel der den im Forum Benutzer tr�gt.</span></td>
      <td class="mbox" width="50%"><input type="text" name="title" size="35"></td>
  </tr>
  <tr> 
   <td class="ubox" colspan="2" align="center"><input type="hidden" name="guest" value="0"><input type="submit" name="Submit" value="Add"></td> 
  </tr>
 </table>
</form>
	<?php
	echo foot();
}
elseif ($job == 'add2') {
	echo head();
	$copy = $gpc->get('copy', int);
	
	$sql_values = '';
	if ($copy == 0) {
		foreach ($glk as $key) {
			$sql_values .= '"'.$gpc->get($key, int).'",';
		}
	}
	else {
		$result = $db->query("SELECT ".implode(', ', $glk)." FROM {$db->pre}groups WHERE id = '{$copy}' LIMIT 1");
		$row = $db->fetch_assoc($result);
		foreach ($glk as $key) {
			$sql_values .= '"'.$row[$key].'",';
		}
	}
	
	$db->query('INSERT INTO '.$db->pre.'groups ('.implode(',', $glk).',flood,title,name) VALUES ('.$sql_values.'"'.$gpc->get('flood', int).'","'.$gpc->get('title', str).'","'.$gpc->get('name', str).'")', __LINE__, __FILE__);
	$gid = $db->insert_id();

	$copyf = $gpc->get('copyf', int);
	if ($copy == 1 && $copyf == 1) {
		$fields = array('f_downloadfiles', 'f_forum', 'f_posttopics', 'f_postreplies', 'f_addvotes', 'f_attachments', 'f_edit', 'f_voting');
		$result = $db->query("SELECT * FROM {$db->pre}fgroups WHERE gid = '{$gid}'");
		while ($row = $db->fetch_assoc($result)) {
			$sql_fvalues = '';
			foreach ($glk as $key) {
				$sql_fvalues .= '"'.$gpc->get($key, int).'",';
			}
			$db->query("INSERT INTO {$db->pre}fgroups (gid,".implode(',', $fields).",bid) VALUES ('{$gid}',{$sql_fvalues},'{$row['bid']}')");
		}
	}
	
	$delobj = $scache->load('group_status');
	$delobj->delete();
	if ($db->affected_rows()) {
		ok('admin.php?action=groups&job=manage');
	}
	else {
		error('admin.php?action=groups&job=add', 'Die Gruppe konnte nicht eingef�gt werden!');
	}
}
elseif ($job == 'delete') {
	$del = $gpc->get('delete', arr_int);
	$edit = $gpc->get('edit', int);
	if (isset($_POST['submit_delete']) && count($del) > 0) {
		$db->query("DELETE FROM {$db->pre}groups WHERE id IN (".implode(',',$del).")");
		$anz = $db->affected_rows();
		$delobj = $scache->load('group_status');
		$delobj->delete();
		echo head();
		ok('admin.php?action=groups&job=manage', $anz.' Eintr�ge gel�scht');
	}
	elseif (isset($_POST['submit_edit']) && $edit > 0) {
		viscacha_header('Location: admin.php?action=groups&job=edit&id='.$edit);
	}
	else {
		viscacha_header('Location: admin.php?action=groups&job=manage');
	}
}
elseif ($job == 'edit') {
	$id = $gpc->get('id', int);
	echo head();
	$result = $db->query("SELECT * FROM {$db->pre}groups WHERE id = '{$id}' LIMIT 1", __LINE__, __FILE__);
	if ($db->num_rows($result) != 1) {
		error('admin.php?action=groups&job=manage', 'Keine g�ltige ID angegeben');
	}
	$data = $db->fetch_assoc($result);
	?>
<form name="form" method="post" action="admin.php?action=groups&amp;job=edit2&amp;id=<?php echo $id; ?>">
 <table class="border">
  <tr> 
   <td class="obox" colspan="2">Edit an Usergroup - Settings and Permissions</td>
  </tr>
  <?php
  foreach ($glk as $key) {
  	$editable = !(($data['guest'] == 1 && $data['core'] == 1) && array_search($key, $guest_limitation) !== false);
  	if ($key != 'guest' && $editable) {
  ?>
  <tr>
   <td class="mbox" width="50%"><?php echo $gls[$key]; ?><br /><span class="stext"><?php echo $gll[$key]; ?></span></td>
   <td class="mbox" width="50%"><input type="checkbox" name="<?php echo $key; ?>" value="1"<?php echo iif($data[$key] == 1, ' checked="checked"'); ?>/></td>
  </tr>
  <?php } } ?>
  <tr>
	<td class="mbox" width="50%">Floodcheck (in sec.)<br /><span class="stext">Zeit in der max. 1 Formular angesandt werden darf - um Spam zu verhindern</span></td>
   	<td class="mbox" width="50%"><input type="text" name="flood" size="3" value="<?php echo $data['flood']; ?>"></td>
  </tr>
 </table>
 <br />
 <table class="border">
  <tr> 
   <td class="obox" colspan="2">Add a new Usergroup - Settings</td>
  </tr>
  <tr>
      <td class="mbox" width="50%">Interner Name d. Gruppe:<br /><span class="stext">Ein interner Name f�r die Benutzergruppe. Wird im Forum nicht angezeigt!</span></td>
      <td class="mbox" width="50%"><input type="text" name="name" size="35" value="<?php echo $data['name']; ?>"></td>
  </tr><tr>
      <td class="mbox" width="50%">�ffentlicher Titel:<br /><span class="stext">Der �ffentliche Benutzertitel der den im Forum Benutzer tr�gt.</span></td>
      <td class="mbox" width="50%"><input type="text" name="title" size="35" value="<?php echo $data['title']; ?>"></td>
  </tr>
  <tr> 
   <td class="ubox" colspan="2" align="center"><input type="submit" name="Submit" value="Edit"></td> 
  </tr>
 </table>
</form>
	<?php
	echo foot();
}
elseif ($job == 'edit2') {
	echo head();

	$id = $gpc->get('id', int);
	$result = $db->query("SELECT * FROM {$db->pre}groups WHERE id = {$id} LIMIT 1");
	if ($db->num_rows($result) != 1) {
		error('admin.php?action=groups&job=manage', 'Keine g�ltige ID angegeben');
	}

	$sql_values = '';
	foreach ($glk as $key) {
		$editable = !(($data['guest'] == 1 && $data['core'] == 1) && array_search($key, $guest_limitation) !== false);
		if ($key != 'guest' && $editable) {
			$sql_values .= $key.' = "'.$gpc->get($key, int).'", ';
		}
	}
	
	$db->query('UPDATE '.$db->pre.'groups SET '.$sql_values.'flood = "'.$gpc->get('flood', int).'", title = "'.$gpc->get('title', str).'", name = "'.$gpc->get('name', str).'" WHERE id = "'.$id.'" LIMIT 1', __LINE__, __FILE__);
	
	$delobj = $scache->load('group_status');
	$delobj->delete();
	
	if ($db->affected_rows()) {
		ok('admin.php?action=groups&job=manage');
	}
	else {
		error('admin.php?action=groups&job=add', 'Die Gruppe konnte nicht ge�ndert werden!');
	}
}
?>
