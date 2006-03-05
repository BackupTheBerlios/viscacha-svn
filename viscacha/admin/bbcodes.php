<?php
if (isset($_SERVER['PHP_SELF']) && basename($_SERVER['PHP_SELF']) == "bbcodes.php") die('Error: Hacking Attempt');

if ($job == 'man_smileys') {
	echo head();
	$fd = $gpc->get('fdelete');
	$deleteid = $gpc->get('delete', arr_int);
	$fe = $gpc->get('fedit');
	$editid = $gpc->get('edit', arr_int);
	if (!empty($fd) && count($deleteid) > 0) {
	   	$scache = new scache('smileys');
	   	$scache->deletedata();
		$db->query('DELETE FROM '.$db->pre.'smileys WHERE id IN ('.implode(',', $deleteid).')',__LINE__,__FILE__);
		$anz = $db->affected_rows();
	
		ok('admin.php?action=bbcodes&job='.$gpc->get('temp4', str), $anz.' entries were deleted successfully!');
	}
	elseif (!empty($fe) && count($editid) > 0) {
		
		$result = $db->query('SELECT * FROM '.$db->pre.'smileys WHERE id IN ('.implode(',', $editid).')',__LINE__,__FILE__);
		?>
<form name="form" method="post" enctype="multipart/form-data" action="admin.php?action=bbcodes&job=edit_smiley">
 <table class="border">
  <tr> 
   <td class="obox">Edit <?php echo count($editid); ?> Smileys</td>
  </tr>
  <tr> 
   <td class="ubox" align="center"><input type="submit" name="Submit" value="Send"></td> 
  </tr>
 </table><br />
 <?php while($row = $db->fetch_assoc($result)) { ?>
 <input type="hidden" name="id[]" value="<?php echo $row['id']; ?>">
 <table class="border">
  <tr> 
   <td class="mbox" width="50%">Code:</td>
   <td class="mbox" width="50%"><input type="text" name="search_<?php echo $row['id']; ?>" size="50" value="<?php echo $row['search']; ?>"></td> 
  </tr>
  <tr> 
   <td class="mbox" width="50%">Image:<br><font class="stext">URL or relative path to the image. Only when you do not upload an image.<br />{folder} is a placeholder for the adresses to the smiley directories.</font></td>
   <td class="mbox" width="50%"><input type="text" name="replace_<?php echo $row['id']; ?>" size="50" value="<?php echo $row['replace']; ?>"></td> 
  </tr>
  <tr>
   <td class="mbox" width="50%">Description:<br><font class="stext">Optional</font></td>
   <td class="mbox" width="50%"><input type="text" name="desc_<?php echo $row['id']; ?>" size="50" value="<?php echo $row['desc']; ?>"></td> 
  </tr>
  <tr> 
   <td class="mbox" width="50%">Show directly:<br><font class="stext">Gibt an ob der Smiley direkt zum schnellen klicken neben den BB-Codes angezeigt wird oder erst im Popup-Menü.</font></td>
   <td class="mbox" width="50%"><input type="checkbox" name="show_<?php echo $row['id']; ?>" value="1"<?php echo iif($row['show'] == 1, ' checked="checked"'); ?>></td> 
  </tr>
 </table><br />
 <?php } ?>
 <table class="border">
  <tr> 
   <td class="ubox" align="center"><input type="submit" name="Submit" value="Send"></td> 
  </tr>
 </table>
</form> 
		<?php
	}
	else {
		error('admin.php?action=bbcodes&job=smiley', 'Keine (gültige) Eingabe gemacht!');
	}
}
elseif ($job == 'edit_smiley') {
	echo head();
	$id = $gpc->get('id', arr_int);
	foreach ($id as $i) {
		$search = $gpc->get('search_'.$i, str);
		$replace = $gpc->get('replace_'.$i, str);
		$desc = $gpc->get('desc_'.$i, str);
		$show = $gpc->get('show_'.$i, int);
		$db->query("UPDATE {$db->pre}smileys AS s SET s.search = '{$search}', s.replace = '{$replace}', s.desc = '{$desc}', s.show = '{$show}' WHERE s.id = '{$i}' LIMIT 1",__LINE__,__FILE__);
	}
	$scache = new scache('smileys');
	$scache->deletedata();
	ok('admin.php?action=bbcodes&job=smiley', count($id).' Smileys wurden editiert.');
}
elseif ($job == 'smiley') {
	echo head();
	
	$result = $db->query("SELECT id, name, smileyfolder, smileypath FROM {$db->pre}designs WHERE publicuse = '1'",__LINE__,__FILE__);
	$design = array();
	$folders = array();
	$des = array();
	while ($row = $db->fetch_assoc($result)) {
		$design[$row['id']] = $row;
		$row['smileyfolder'] = str_replace('{folder}', $config['furl'], $row['smileyfolder']);
		$folders[$row['id']] = $row['smileyfolder'];
		$md5 = $row['smileyfolder'];
		if (!isset($des[$md5])) {
			$des[$md5] = array();
		}
		$des[$md5][] = $row['id'];
	}
	$folders = array_unique($folders);
	
	$result = $db->query("SELECT * FROM {$db->pre}smileys AS s ORDER BY s.show DESC");
?>
<form name="form" method="post" action="admin.php?action=bbcodes&job=man_smileys">
<input name="temp4" value="smiley" type="hidden">
 <table class="border">
  <tr> 
   <td class="obox" colspan="<?php echo 6+count($folders); ?>">Manage Smileys</td>
  </tr>
  <tr class="ubox">
   <td width="5%" rowspan="2">DEL</td>
   <td width="5%" rowspan="2">Edit</td>
   <td width="10%" rowspan="2">Code</td> 
   <td width="30%" rowspan="2">URL</td>
   <td width="10%" colspan="<?php echo count($folders); ?>">Images/Designs</td>
   <td width="5%" rowspan="2">Show directly</td> 
   <td width="35%" rowspan="2">Description</td> 
  </tr>
  <tr class="ubox">
  <?php foreach ($des as $id) { ?>
   <td align="center"><?php echo implode(',', $id); ?></td>
  <?php } ?>
  </tr>
<?php
	while ($row = $db->fetch_assoc($result)) {
		$imgsrc = array();
		foreach ($folders as $id => $url) {
			$imgsrc[$id] = str_replace('{folder}', $url, $row['replace']);
		}
?> 
  <tr class="mbox">
   <td width="5%"><input type="checkbox" name="delete[]" value="<?php echo $row['id']; ?>"></td>
   <td width="5%"><input type="checkbox" name="edit[]" value="<?php echo $row['id']; ?>"></td>
   <td width="10%"><?php echo $row['search']; ?></td> 
   <td width="30%"><?php echo $row['replace']; ?></td>
   <?php foreach ($imgsrc as $design => $src) { ?>
   <td align="center">
   <img src="<?php echo $src; ?>" alt="Design: <?php echo $design; ?>" border="0" />&nbsp;
   </td>
   <?php } ?>
   <td width="5%" align="center"><?php echo noki($row['show'], ' onmouseover="HandCursor(this)" onclick="ajax_noki(this, \'action=bbcodes&job=ajax_smileypos&id='.$row['id'].'\')"'); ?></td> 
   <td width="35%"><?php echo $row['desc']; ?></td> 
  </tr>
<?php } ?>
  <tr> 
   <td class="ubox" colspan="<?php echo 6+count($folders); ?>" align="center"><input type="submit" name="fdelete" value="Delete"> <input type="submit" name="fedit" value="Edit"></td> 
  </tr>
 </table>
</form>
<br>
<form name="form" method="post" enctype="multipart/form-data" action="admin.php?action=bbcodes&job=add_smiley">
 <table class="border">
  <tr> 
   <td class="obox" colspan="2">Add Smiley</td>
  </tr>
  <tr> 
   <td class="mbox" width="50%">Code:</td>
   <td class="mbox" width="50%"><input type="text" name="code" size="50"></td> 
  </tr>
  <tr> 
   <td class="mbox" width="50%">Image:<br><font class="stext">URL or relative path to the image. Only when you do not upload an image.<br />{folder} = <?php echo $my->smileyfolder; ?></font></td>
   <td class="mbox" width="50%"><input type="text" name="img" size="50"></td> 
  </tr>
   <td class="mbox" width="50%">Upload an image<br><font class="stext">Erlaubte Dateitypen: .gif, .jpg, .jpeg, .png, .jpe, .bmp<br />Maximale Dateigröße: 200 KB</font></td>
   <td class="mbox" width="50%"><input type="file" name="upload_0" size="40" /></td>
  <tr> 
   <td class="mbox" width="50%">Description:<br><font class="stext">Optional</font></td>
   <td class="mbox" width="50%"><input type="text" name="desc" size="50"></td> 
  </tr>
  <tr> 
   <td class="mbox" width="50%">Show directly:<br><font class="stext">Gibt an ob der Smiley direkt zum schnellen klicken neben den BB-Codes angezeigt wird oder erst im Popup-Menü.</font></td>
   <td class="mbox" width="50%"><input type="checkbox" name="show" value="1"></td> 
  </tr>
  <tr> 
   <td class="ubox" width="100%" colspan=2 align="center"><input type="submit" name="Submit" value="Add"></td> 
  </tr>
 </table>
</form> 
	<?php
	echo foot();
}
elseif ($job == 'ajax_smileypos') {
	$id = $gpc->get('id', int);
	$result = $db->query("SELECT b.show FROM {$db->pre}smileys AS b WHERE id = '{$id}' LIMIT 1",__LINE__,__FILE__);
	$use = $db->fetch_assoc($result);
	$use = invert($use['show']);
	$db->query("UPDATE {$db->pre}smileys AS b SET b.show = '{$use}' WHERE id = '{$id}' LIMIT 1",__LINE__,__FILE__);
    $scache = new scache('smileys');
    $scache->deletedata();
	die(strval($use));
}
elseif ($job == 'add_smiley') {
	echo head();
	$error = array();
	
	$ups = 1;
	$filesize = 200*1024;
	$filetypes = array('.gif', '.jpg', '.png', '.bmp', '.jpeg', '.jpe');
	$path = 'temp/';
	$dir = realpath($path);

	$insertuploads = array();
	$inserterrors = array();
	require("classes/class.upload.php");
	
	$img = $gpc->get('img', str);
	
	$has_upload = false;

	$result = $db->query("SELECT id, name, smileyfolder, smileypath FROM {$db->pre}designs WHERE publicuse = '1'",__LINE__,__FILE__);
	$folders = array();
	$folders2 = array();
	while ($row = $db->fetch_assoc($result)) {
		$row['smileypath'] = str_replace('{folder}', $config['fpath'], $row['smileypath']);
		$row['smileyfolder'] = str_replace('{folder}', $config['furl'], $row['smileyfolder']);
		$folders[] = $row['smileypath'];
		$folders2[] = $row['smileyfolder'];
	}
	$folders = array_unique($folders);
	$folders2 = array_unique($folders2);
	
	for ($i = 0; $i < $ups; $i++) {
	    if (empty($_FILES['upload_'.$i]['name'])) {
	    	continue;
	    }
	 
	    $my_uploader = new uploader();
		$my_uploader->max_filesize($filesize);
		if (isset($imgwidth) && isset($imgheight)) {
			$my_uploader->max_image_size($imgwidth, $imgheight);
		}
		if ($my_uploader->upload('upload_'.$i, $filetypes)) {
			$my_uploader->save_file($dir, 2);
			if ($my_uploader->return_error()) {
				$error[] = $my_uploader->return_error();
			}
			else {
				$has_upload = $gpc->save_str($my_uploader->fileinfo('name'));
			}
		}
		else {
			$error[] = $my_uploader->return_error();
		}
	}
	if (strlen($gpc->get('code', str)) < 2) {
		$error[] = 'Code ist zu kurz';
	}
	if (!$has_upload && strlen($img) < 5) {
		$error[] = 'Imagepfad zu kurz';
	}
	if (strlen($gpc->get('show', int)) != 1 && $gpc->get('show', int) != 0) {
		$error[] = 'Falsche Angabe(n)';
	}
	if (count($error) > 0) {
	    error('admin.php?action=bbcodes&job=smiley', $error);
	}
	if ($has_upload) {
		foreach ($folders as $dest) {
			$filesystem->copy($path.$has_upload, $dest.'/'.$has_upload);
		}
		$img = '{folder}/'.$has_upload;
	}
	else {
		foreach ($folders2 as $dir) {
			$img = str_replace($dir, '{folder}', $img);
		}
	}
	$db->query("INSERT INTO {$db->pre}smileys (`search`,`replace`,`desc`,`show`) VALUES ('".$gpc->get('code', str)."','".$img."','".$gpc->get('desc', str)."','".$gpc->get('show', int)."')",__LINE__,__FILE__);

    $scache = new scache('smileys');
    $scache->deletedata();

	ok('admin.php?action=bbcodes&job=smiley', 'Smiley was successfully added');
}
elseif ($job == 'word') {
	echo head();
	$result = $db->query("SELECT * FROM {$db->pre}textparser WHERE type = 'word'",__LINE__,__FILE__);
?>
<form name="form" method="post" action="admin.php?action=bbcodes&job=del">
<input name="temp4" value="word" type="hidden">
 <table class="border">
  <tr> 
   <td class="obox" colspan=4>Manage Glossary</b></td>
  </tr>
  <tr>
   <td class="ubox" width="5%">Delete</td>
   <td class="ubox" width="15%">Abbreviation</td> 
   <td class="ubox" width="30%">Phrase</td>
   <td class="ubox" width="50%">Description</td> 
  </tr>
<?php while ($row = $db->fetch_assoc($result)) { ?> 
  <tr>
   <td class="mbox" width="5%"><input type="checkbox" name="delete[]" value="<?php echo $row['id']; ?>"></td>
   <td class="mbox" width="15%"><?php echo $row['search']; ?></td> 
   <td class="mbox" width="30%"><?php echo $row['replace']; ?></td>
   <td class="mbox" width="50%"><?php echo $row['desc']; ?></td> 
  </tr>
<?php } ?>
  <tr> 
   <td class="ubox" width="100%" colspan=4 align="center"><input type="submit" name="Submit" value="Delete"></td> 
  </tr>
 </table>
</form>
<br>
<form name="form" method="post" action="admin.php?action=bbcodes&job=add">
<input name="temp4" value="word" type="hidden">
 <table class="border">
  <tr> 
   <td class="obox" colspan=2>Add Word</b></td>
  </tr>
  <tr> 
   <td class="mbox" width="50%">Abbreviation:</td>
   <td class="mbox" width="50%"><input type="text" name="temp1" size="50"></td> 
  </tr>
  <tr> 
   <td class="mbox" width="50%">Phrase:</td>
   <td class="mbox" width="50%"><input type="text" name="temp2" size="50"></td> 
  </tr>
  <tr> 
   <td class="mbox" width="50%">Description:</td>
   <td class="mbox" width="50%"><textarea name="temp3" cols="50" rows="3"></textarea></td> 
  </tr>
  <tr> 
   <td class="ubox" width="100%" colspan=2 align="center"><input type="submit" name="Submit" value="Add"></td> 
  </tr>
 </table>
</form> 
	<?php
	echo foot();
}
elseif ($job == 'censor') {
	echo head();
	$result = $db->query("SELECT * FROM {$db->pre}bbcode WHERE type = 'censor'",__LINE__,__FILE__);
?>
<form name="form" method="post" action="admin.php?action=bbcodes&job=del">
<input name="temp4" value="censor" type="hidden">
 <table class="border">
  <tr> 
   <td class="obox" colspan=3>Manage Censorship</b></td>
  </tr>
  <tr>
   <td class="ubox" width="10%">Delete</td>
   <td class="ubox" width="45%">Word</td> 
   <td class="ubox" width="45%">Censored Word</td>
  </tr>
<?php while ($row = $db->fetch_assoc($result)) { ?> 
  <tr>
   <td class="mbox" width="10%"><input type="checkbox" name="delete[]" value="<?php echo $row['id']; ?>"></td>
   <td class="mbox" width="45%"><?php echo $row['search']; ?></td> 
   <td class="mbox" width="45%"><?php echo $row['replace']; ?></td>
  </tr>
<?php } ?>
  <tr> 
   <td class="ubox" width="100%" colspan=3 align="center"><input type="submit" name="Submit" value="Delete"></td> 
  </tr>
 </table>
</form>
<br>
<form name="form" method="post" action="admin.php?action=bbcodes&job=add">
<input name="temp4" value="censor" type="hidden">
 <table class="border">
  <tr> 
   <td class="obox" colspan=2>Add Word</b></td>
  </tr>
  <tr> 
   <td class="mbox" width="50%">Word:</td>
   <td class="mbox" width="50%"><input type="text" name="temp1" size="50"></td> 
  </tr>
  <tr> 
   <td class="mbox" width="50%">Censored Word:</td>
   <td class="mbox" width="50%"><input type="text" name="temp2" size="50"></td> 
  </tr>
  <tr> 
   <td class="ubox" width="100%" colspan=2 align="center"><input type="submit" name="Submit" value="Add"></td> 
  </tr>
 </table>
</form> 
	<?php
	echo foot();
}
elseif ($job == 'replace') {
	echo head();
	$result = $db->query("SELECT * FROM {$db->pre}textparser WHERE type = 'replace'",__LINE__,__FILE__);
?>
<form name="form" method="post" action="admin.php?action=bbcodes&job=del">
<input name="temp4" value="replace" type="hidden">
 <table class="border">
  <tr> 
   <td class="obox" colspan=3>Manage Vocabulary</b></td>
  </tr>
  <tr>
   <td class="ubox" width="10%">Delete</td>
   <td class="ubox" width="45%">Word</td> 
   <td class="ubox" width="45%">Replacement</td>
  </tr>
<?php while ($row = $db->fetch_assoc($result)) { ?> 
  <tr>
   <td class="mbox" width="10%"><input type="checkbox" name="delete[]" value="<?php echo $row['id']; ?>"></td>
   <td class="mbox" width="45%"><?php echo $row['search']; ?></td> 
   <td class="mbox" width="45%"><?php echo $row['replace']; ?></td>
  </tr>
<?php } ?>
  <tr> 
   <td class="ubox" width="100%" colspan=3 align="center"><input type="submit" name="Submit" value="Delete"></td> 
  </tr>
 </table>
</form>
<br>
<form name="form" method="post" action="admin.php?action=bbcodes&job=add">
<input name="temp4" value="replace" type="hidden">
 <table class="border">
  <tr> 
   <td class="obox" colspan=2>Add Word</b></td>
  </tr>
  <tr> 
   <td class="mbox" width="50%">Word:</td>
   <td class="mbox" width="50%"><input type="text" name="temp1" size="50"></td> 
  </tr>
  <tr> 
   <td class="mbox" width="50%">Replacement:</td>
   <td class="mbox" width="50%"><input type="text" name="temp2" size="50"></td> 
  </tr>
  <tr> 
   <td class="ubox" width="100%" colspan=2 align="center"><input type="submit" name="Submit" value="Add"></td> 
  </tr>
 </table>
</form> 
	<?php
	echo foot();
}
elseif ($job == 'add') {
	echo head();
	$type = $gpc->get('temp4', str);
	
	$error = array();
	if ($type != 'word' && $type != 'censor' && $type != 'replace') {
		$error[] = "Kein gültiger Typ angegeben";
	}
	if (strlen($gpc->get('temp1', str)) < 2) {
		$error[] = "Angabe 1 zu kurz";
	}
	if (strlen($gpc->get('temp2', str)) < 2) {
		$error[] = "Angabe 2 zu kurz";
	}
	if (strlen($gpc->get('temp3', str)) < 2 && $type == 'word') {
		$error[] = "Beschreibung zu kurz";
	}
	if (count($error) > 0) {
		error('admin.php?action=bbcodes&job='.$type, $error);
	}
	
	$db->query("INSERT INTO {$db->pre}textparser (`search`,`replace`,`type`,`desc`) VALUES ('".$gpc->get('temp1', str)."','".$gpc->get('temp2', str)."','{$type}','".$gpc->get('temp3', str)."')",__LINE__,__FILE__);

	$scache = new scache('bbcode');
	$scache->deletedata();

	ok('admin.php?action=bbcodes&job='.$type, 'Daten wurden hinzugefügt');
}
elseif ($job == 'del') {
	echo head();
	$delete = $gpc->get('delete', arr_int);
	$type = $gpc->get('temp4', str);
	if (count($delete) > 0) {
		error('admin.php?action=bbcodes&job='.$type, 'Sie haben keine gültige Auswahl getroffen.');
	}
	$db->query('DELETE FROM '.$db->pre.'textparser WHERE id IN ('.implode(',',$delete).')',__LINE__,__FILE__);
	$anz = $db->affected_rows();
    $scache = new scache('bbcode');
    $scache->deletedata();
	ok('admin.php?action=bbcodes&job='.$type, $anz.' entries were deleted successfully!');
}
elseif ($job == 'codefiles') {
	echo head();
	include_once('classes/class.geshi.php');
	$clang = array();
	$d = dir("classes/geshi");
	while (false !== ($entry = $d->read())) {
		if (get_extension($entry,TRUE) == 'php' && !is_dir("classes/geshi/".$entry)) {
			include_once("classes/geshi/".$entry);
			$short = str_replace('.php','',$entry);
			$clang[$short]['file'] = $entry;
			$clang[$short]['name'] = $language_data['LANG_NAME'];
		}
	}
	$d->close();
	asort($clang);
?>
<form name="form" method="post" action="admin.php?action=bbcodes&job=del_codefiles">
 <table class="border">
  <tr> 
   <td class="obox" colspan="3">Syntax Highlighting Manager (<?php echo count($clang); ?> Languages)</b></td>
  </tr>
  <tr>
   <td class="ubox" width="10%">Delete</td>
   <td class="ubox" width="45%">Language</td> 
   <td class="ubox" width="45%">File</td>
  </tr>
<?php foreach ($clang as $row) { ?> 
  <tr>
   <td class="mbox" width="10%"><input type="checkbox" name="delete[]" value="<?php echo $row['file']; ?>"></td>
   <td class="mbox" width="45%"><?php echo $row['name']; ?></td> 
   <td class="mbox" width="45%"><?php echo $row['file']; ?></td>
  </tr>
<?php } ?>
  <tr> 
   <td class="ubox" width="100%" colspan="3" align="center"><input type="submit" name="Submit" value="Delete"></td> 
  </tr>
 </table>
</form>
<br>
<form name="form2" method="post" enctype="multipart/form-data" action="admin.php?action=explorer&job=upload&cfg=codefiles">
<table class="border">
<tr><td class="obox">Add Syntax Highlighting Files</td></tr>
<tr><td class="mbox">
Um eine Datei hinzuzufügen, klicken Sie auf die "Durchsuchen"-Schaltfläche und wählen Sie eine Datei aus.
Klicken Sie dann auf "Senden", um den Vorgang abzuschließen.<br /><br />
Erlaubte Dateitypen: .php<br />
Maximale Dateigröße: 200 KB<br /><br />
<strong>Datei hochladen:</strong>
<br /><input type="file" name="upload_0" size="40" />
</td></tr>
<tr><td class="ubox" align="center"><input accesskey="s" type="submit" value="Upload" /></td></tr>
</table>
</form>
	<?php
	echo foot();
}
elseif ($job == 'del_codefiles') {
	echo head();
	$d = $gpc->get('delete', arr_str);
	foreach ($d as $file) {
		$file = 'classes/geshi/'.$file;
		if (file_exists($file)) {
			$filesystem->unlink($file);
		}
	}
    $scache = new scache('syntax-highlight');
    $scache->deletedata();
    ok('admin.php?action=bbcodes&job=codefiles', 'Dateien wurden gelöscht');
}
elseif ($job == 'custombb_add') {
	echo head();
	?>
	<form action="admin.php?action=bbcodes&job=custombb_add2" name="form2" method="post">
	<table align="center" class="border">
	<tr>
		<td class="obox" align="center" colspan="2"><b>Add new BB Code</b></td>
	</tr>
	<tr>
		<td class="mbox" width="50%">Title</td>
		<td class="mbox" width="50%"><input type="text" name="title" value="" size="60" /></td>
	</tr>
	<tr>
		<td class="mbox">Tag<br />
		<span class="stext">This is the text for the BB code, which goes inside the square brackets.</span></td>
		<td class="mbox"><input type="text" name="bbcodetag" value="" size="60" /></td>
	</tr>
	<tr>
		<td class="mbox">Replacement<br />
		<span class="stext">This is the HTML code for the BB code replacement. Make sure that you include '{param}' (without the quotes) to insert the text between the opening and closing BB code tags, and '{option}' for the parameter within the BB code tag. You can only use {option} if 'Use Option' is set to yes.</span></td>
		<td class="mbox"><textarea name="bbcodereplacement" rows="6" cols="60" wrap="virtual"></textarea></td>
	</tr>
	<tr>
		<td class="mbox">Example<br />
		<span class="stext">This is a sample piece of BB code to use as an example for this particular BB code.</span></td>
		<td class="mbox"><input type="text" name="bbcodeexample" value="" size="60" /></td>
	</tr>
	<tr>
		<td class="mbox">Description<br />
		<span class="stext">This is a piece of text to describe the BB code tag. This can include HTML tags if you wish.</span></td>
		<td class="mbox"><textarea name="bbcodeexplanation" rows="8" cols="60" wrap="virtual"></textarea></td>
	</tr>
	<tr>
		<td class="mbox">Use {option}<br />
		<span class="stext">Setting this option to yes will allow you to create a [tag=option][/tag] style tag, rather than just a [tag][/tag] style tag.</span></td>
		<td class="mbox">
			<input type="radio" name="twoparams" value="1" />Yes<br />
			<input type="radio" name="twoparams" value="0" checked="checked" />No
		</td>
	</tr>
	<tr>
		<td class="mbox">Button Image<br />
		<span class="stext">Optional - If you would like this bbcode to appear as a clickable button on the message editor toolbar, enter the URL of an image 21 x 20 pixels in size that will act as the button to insert this bbcode.</span>
		</td>
		<td class="mbox"><input type="text" name="buttonimage" value="" size="60" /></td>
	</tr>
	<tr><td class="ubox" colspan="2" align="center"><input type="submit" value="Save" /></td></tr>
	</table>
	</form>
	<?php
	echo foot();
}
elseif ($job == 'custombb_add2') {
	$vars = array(
		'title'				=> str,
		'bbcodetag'			=> str,
		'bbcodereplacement' => str,
		'bbcodeexample'		=> str,
		'bbcodeexplanation' => str,
		'twoparams'			=> int,
		'buttonimage'		=> str
	);
	$query = array();
	foreach ($vars as $key => $type) {
		$query[$key] = $gpc->get($key, $type);
	}

	echo head();

	if (!$query['bbcodetag'] OR !$query['bbcodereplacement'] OR !$query['bbcodeexample']) {
		error('admin.php?action=bbcodes&job=custombb_add', 'Please complete all required fields');
	}

	$result = $db->query("SELECT * FROM {$db->pre}bbcode WHERE bbcodetag = '{$query['bbcodetag']}' AND twoparams = '{$query['twoparams']}'", __LINE__, __FILE__);
	if ($db->num_rows($result) > 0) {
		error('admin.php?action=bbcodes&job=custombb_add', 'There is already a BB Code named &quot;'.$query['bbcodetag'].'&quot;. You may not create duplicate names.');
	}
	
	$db->query("
	INSERT INTO {$db->pre}bbcode (bbcodetag, bbcodereplacement, bbcodeexample, bbcodeexplanation, twoparams, title, buttonimage)
	VALUES ('{$query['bbcodetag']}','{$query['bbcodereplacement']}','{$query['bbcodeexample']}','{$query['bbcodeexplanation']}','{$query['twoparams']}','{$query['title']}','{$query['buttonimage']}')
	", __LINE__, __FILE__);

    $scache = new scache('custombb');
    $scache->deletedata();

	ok('admin.php?action=bbcodes&job=custombb');
}
elseif ($job == 'custombb_edit') {
	echo head();
	$id = $gpc->get('id', int);

	$result = $db->query("SELECT * FROM {$db->pre}bbcode WHERE id = ".$id, __LINE__, __FILE__);
	$bbcode = $gpc->prepare($db->fetch_assoc($result));

	?>
	<form action="admin.php?action=bbcodes&job=custombb_edit2&amp;id=<?php echo $bbcode['id']; ?>" name="form2" method="post">
	<table align="center" class="border">
	<tr>
		<td class="obox" align="center" colspan="2"><b>Add new BB Code</b></td>
	</tr>
	<tr>
		<td class="mbox" width="50%">Title</td>
		<td class="mbox" width="50%"><input type="text" name="title" value="<?php echo $bbcode['title']; ?>" size="60" /></td>
	</tr>
	<tr>
		<td class="mbox">Tag<br />
		<span class="stext">This is the text for the BB code, which goes inside the square brackets.</span></td>
		<td class="mbox"><input type="text" name="bbcodetag" value="<?php echo $bbcode['bbcodetag']; ?>" size="60" /></td>
	</tr>
	<tr>
		<td class="mbox">Replacement<br />
		<span class="stext">This is the HTML code for the BB code replacement. Make sure that you include '{param}' (without the quotes) to insert the text between the opening and closing BB code tags, and '{option}' for the parameter within the BB code tag. You can only use {option} if 'Use Option' is set to yes.</span></td>
		<td class="mbox"><textarea name="bbcodereplacement" rows="6" cols="60" wrap="virtual"><?php echo $bbcode['bbcodereplacement']; ?></textarea></td>
	</tr>
	<tr>
		<td class="mbox">Example<br />
		<span class="stext">This is a sample piece of BB code to use as an example for this particular BB code.</span></td>
		<td class="mbox"><input type="text" name="bbcodeexample" value="<?php echo $bbcode['bbcodeexample']; ?>" size="60" /></td>
	</tr>
	<tr>
		<td class="mbox">Description<br />
		<span class="stext">This is a piece of text to describe the BB code tag. This can include HTML tags if you wish.</span></td>
		<td class="mbox"><textarea name="bbcodeexplanation" rows="8" cols="60" wrap="virtual"><?php echo $bbcode['bbcodeexplanation']; ?></textarea></td>
	</tr>
	<tr>
		<td class="mbox">Use {option}<br />
		<span class="stext">Setting this option to yes will allow you to create a [tag=option][/tag] style tag, rather than just a [tag][/tag] style tag.</span></td>
		<td class="mbox">
			<input type="radio" name="twoparams" value="1"<?php echo iif($bbcode['twoparams'], ' checked="checked"'); ?> />Yes<br />
			<input type="radio" name="twoparams" value="0"<?php echo iif(!$bbcode['twoparams'], ' checked="checked"'); ?> />No
		</td>
	</tr>
	<tr>
		<td class="mbox">Button Image<br />
		<span class="stext">Optional - If you would like this bbcode to appear as a clickable button on the message editor toolbar, enter the URL of an image 21 x 20 pixels in size that will act as the button to insert this bbcode.</span>
		</td>
		<td class="mbox"><input type="text" name="buttonimage" value="<?php echo $bbcode['buttonimage']; ?>" size="60" /></td>
	</tr>
	<tr><td class="ubox" colspan="2" align="center"><input type="submit" value="Save" /></td></tr>
	</table>
	</form>
	<?php
	echo foot();
}
elseif ($job == 'custombb_edit2') {
	$vars = array(
		'id'				=> int,
		'title'				=> str,
		'bbcodetag'			=> str,
		'bbcodereplacement' => str,
		'bbcodeexample'		=> str,
		'bbcodeexplanation' => str,
		'twoparams'			=> int,
		'buttonimage'		=> str
	);
	$query = array();
	foreach ($vars as $key => $type) {
		$query[$key] = $gpc->get($key, $type);
	}

	echo head();

	if (!$query['bbcodetag'] OR !$query['bbcodereplacement'] OR !$query['bbcodeexample']) {
		error('admin.php?action=bbcodes&job=custombb_add', 'Please complete all required fields');
	}

	$result = $db->query("SELECT * FROM {$db->pre}bbcode WHERE bbcodetag = '{$query['bbcodetag']}' AND twoparams = '{$query['twoparams']}'", __LINE__, __FILE__);
	if ($db->num_rows($result) > 0) {
		error('admin.php?action=bbcodes&job=custombb_add', 'There is already a BB Code named &quot;'.$query['bbcodetag'].'&quot;. You may not create duplicate names.');
	}

	$db->query("UPDATE {$db->pre}bbcode SET title = '{$query['title']}',bbcodetag = '{$query['bbcodetag']}',bbcodereplacement = '{$query['bbcodereplacement']}',bbcodeexample = '{$query['bbcodeexample']}',bbcodeexplanation = '{$query['bbcodeexplanation']}',twoparams = '{$query['twoparams']}',buttonimage = '{$query['buttonimage']}' WHERE id = '{$query['id']}'", __LINE__, __FILE__);

    $scache = new scache('custombb');
    $scache->deletedata();

	ok('admin.php?action=bbcodes&job=custombb');
}
elseif ($job == 'custombb_delete') {
	echo head();
	$id = $gpc->get('id', int);
	?>
	<table class="border" border="0" cellspacing="0" cellpadding="4" align="center">
	<tr><td class="obox">Delete Custom BB Code</td></tr>
	<tr><td class="mbox">
	<p align="center">Wollen Sie diesen BB-Code wirklich löschen?</p>
	<p align="center">
	<a href="admin.php?action=bbcodes&job=custombb_delete2&id=<?php echo $id; ?>"><img border="0" align="middle" alt="" src="admin/html/images/yes.gif"> Yes</a>
	&nbsp&nbsp;&nbsp;&nbsp&nbsp;&nbsp;
	<a href="javascript: history.back(-1);"><img border="0" align="middle" alt="" src="admin/html/images/no.gif"> No</a>
	</p>
	</td></tr>
	</table>
	<?php
	echo foot();
}
elseif ($job == 'custombb_delete2'){
	echo head();
	$id = $gpc->get('id', int);
	$db->query("DELETE FROM {$db->pre}bbcode WHERE id = ".$id, __LINE__, __FILE__);
    $scache = new scache('custombb');
    $scache->deletedata();
	ok('admin.php?action=bbcodes&job=custombb', 'Custom BB Code successfully deleted');
}
elseif ($job == 'custombb') {
	$result = $db->query("SELECT * FROM {$db->pre}bbcode", __LINE__, __FILE__);
	echo head();
	?>
	<table align="center" class="border">
	<tr>
		<td class="obox" align="center" colspan="4"><span style="float: right;">[<a href="admin.php?action=bbcodes&job=custombb_add">Add new BB Code</a>]</span>Custom BB Code Manager</td>
	</tr>
	<tr>
		<td class="ubox" width="30%">Title</td>
		<td class="ubox" width="35%">BB Code</td>
		<td class="ubox" width="10%">Button Image</td>
		<td class="ubox" width="25%">Action</td>
	</tr>
	<?php
	while ($bbcode = $db->fetch_assoc($result)) {
		if (!empty($bbcode['buttonimage'])) {
			$src = "<img style=\"background: buttonface; border:solid 1px highlight;\" src=\"{$bbcode['buttonimage']}\" alt=\"\" />";
		}
		else {
			$src = '-';
		}
		?>
		<tr>
			<td class="mbox"><?php echo $bbcode['title']; ?></td>
			<td class="mbox"><code><?php echo $bbcode['bbcodeexample']; ?></code></td>
			<td class="mbox" align="center"><?php echo $src; ?></td>
			<td class="mbox">[<a href="admin.php?action=bbcodes&job=custombb_edit&id=<?php echo $bbcode['id']; ?>">Edit</a>] [<a href="admin.php?action=bbcodes&job=custombb_delete&id=<?php echo $bbcode['id']; ?>">Delete</a>]</td>
		</tr>
		<?
	}
	?>
	</table>
	<?php
}
?>
