<?php
if (defined('VISCACHA_CORE') == false) { die('Error: Hacking Attempt'); }

require('classes/class.phpconfig.php');
$myini = new INI();

function BBCodeToolBox() {
	global $db, $scache, $config;

	$cache = $scache->load('smileys');
	$cache->seturl($config['smileyurl']);
	$csmileys = $cache->get();
	$smileys = array(0 => array(), 1 => array());
	foreach ($csmileys as $bb) {
	   	if ($bb['show'] == 1) {
			$smileys[1][] = $bb;
		}
		else {
			$smileys[0][] = $bb;
		}
	}
	$smileys[1] = array_chunk($smileys[1], 5);

	$cache = $scache->load('custombb');
	$cbb = $cache->get();
	foreach ($cbb as $key => $bb) {
   		if (empty($bb['buttonimage'])) {
			unset($cbb[$key]);
			continue;
		}
		$cbb[$key]['title'] = htmlspecialchars($bb['title']);
		if ($bb['twoparams']) {
			$cbb[$key]['href'] = "InsertTagsParams('[{$bb['bbcodetag']}={param1}]{param2}','[/{$bb['bbcodetag']}]');";
		}
		else {
			$cbb[$key]['href'] = "InsertTags('[{$bb['bbcodetag']}]','[/{$bb['bbcodetag']}]');";
		}
	}
	?>
<script type="text/javascript" src="admin/html/editor.js"></script>
<table class="invisibletable">
 <tr>
  <td width="30%">
	<table style="margin-bottom: 5px;width: 140px">
	<?php foreach ($smileys[1] as $row) { ?>
		<tr>
		<?php foreach ($row as $bb) { ?>
			<td class="center"><a href="javascript:InsertTagsMenu(' <?php echo $bb['jssearch'] ?> ', '', 'bbsmileys')"><img border="0" src="<?php echo $bb['replace']; ?>" alt="<?php echo $bb['desc']; ?>" /></a></td>
		<?php } ?>
		</tr>
	<?php } ?>
	</table>
	<a id="menu_bbsmileys" style="display: block;text-align: center;width: 140px;" href="javascript:Link()"><img border="0" src="admin/html/images/desc.gif" alt="" /> more Smileys...</a>
	<script type="text/javascript">RegisterMenu('bbsmileys');</script>
	<div class="popup" id="popup_bbsmileys" style="height: 200px;width: 255px;overflow: auto;">
	<strong>Smileys</strong>
	<table style="width: 250px;border-collapse: collapse;margin-bottom: 5px;">
	<?php foreach ($smileys[0] as $bb) { ?>
	  <tr class="mbox">
		<td width="20%" class="center"><a href="javascript:InsertTagsMenu(' <?php echo $bb['jssearch'] ?>', ' ', 'bbsmileys')"><img border="0" src="<?php echo $bb['replace']; ?>" alt="<?php echo $bb['desc']; ?>" /></a></td>
		<td width="20%" class="center"><?php echo $bb['search']; ?></td>
		<td width="60%"><span class="stext"><?php echo $bb['desc']; ?></span></td>
	  </tr>
	<?php } ?>
	</table>
	</div>
  </td>
  <td width="70%">
	<div class="label" id="codebuttons">
	<a id="menu_bbcolor" href="javascript:Link()"><img src="admin/html/images/desc.gif" alt="" /> Color</a>
		<script type="text/javascript">RegisterMenu('bbcolor');</script>
		<DIV class="popup" id="popup_bbcolor">
		<strong>Choose Color</strong>
		<div class="bbody">
		<script type="text/javascript">document.write(writeRow());</script>
		</div>
		</DIV>
	<a id="menu_bbsize" href="javascript:Link()"><img src="admin/html/images/desc.gif" alt="" /> Size</a>
		<script type="text/javascript">RegisterMenu('bbsize');</script>
		<div class="popup" id="popup_bbsize">
		<strong>Choose Size</strong>
	   	<ul>
			<li><span class="popup_line" onclick="InsertTagsMenu('[size=large]','[/size]','bbsize')" style="font-size: 1.3em;">Big Font</span></li>
			<li><span class="popup_line" onclick="InsertTagsMenu('[size=small]','[/size]','bbsize')" style="font-size: 0.8em;">Small Font</span></li>
			<li><span class="popup_line" onclick="InsertTagsMenu('[size=extended]','[/size]','bbsize')" style="letter-spacing: 3px;">Extended Font</span></li>
		</ul>
		</div>
	<a id="menu_bbalign" href="javascript:Link()"><img src="admin/html/images/desc.gif" alt="" /> Alignment</a>
		<script type="text/javascript">RegisterMenu('bbalign');</script>
		<DIV class="popup" id="popup_bbalign">
	   <strong>Choose Alignment</strong>
		<ul>
			<li><span class="popup_line" onclick="InsertTagsMenu('[align=left]','[/align]','bbalign')" style="text-align: left;">Left</span></li>
			<li><span class="popup_line" onclick="InsertTagsMenu('[align=center]','[/align]','bbalign')" style="text-align: center;">Center</span></li>
			<li><span class="popup_line" onclick="InsertTagsMenu('[align=right]','[/align]','bbalign')" style="text-align: right;">Right</span></li>
			<li><span class="popup_line" onclick="InsertTagsMenu('[align=justify]','[/align]','bbalign')" style="text-align: justify;">Justify</span></li>
		</ul>
		</DIV>
	<a id="menu_bbhx" href="javascript:Link()"><img src="admin/html/images/desc.gif" alt="" /> Heading</a>
		<script type="text/javascript">RegisterMenu('bbhx');</script>
		<div class="popup" id="popup_bbhx">
		<strong>Choose Heading</strong>
		<ul>
			<li><h4 class="popup_line" onclick="InsertTagsMenu('[h=large]','[/h]','bbhx')" style="margin: 0px; font-size: 14pt;">Heading 1</h4></li>
			<li><h5 class="popup_line" onclick="InsertTagsMenu('[h=middle]','[/h]','bbhx')" style=" margin: 0px; font-size: 13pt;">Heading 2</h5></li>
			<li><h6 class="popup_line" onclick="InsertTagsMenu('[h=small]','[/h]','bbhx')" style="margin: 0px; font-size: 12pt;">Heading 3</h6></li>
		</ul>
		</div>
	<a id="menu_help" href="misc.php?action=bbhelp<?php echo SID2URL_x; ?>" style="cursor: help;" target="_blank"><img src="./images/1/bbcodes/help.gif" alt="" /> <strong>Help</strong></a>
	<?php if ($config['spellcheck'] == 1) { ?>
	<script type="text/javascript" src="templates/spellChecker.js"></script>
	<a href="javascript:openSpellChecker(textfield);"><img src="./images/1/bbcodes/spellcheck.gif" alt="Spell Check" /></a>
	<?php } ?>
	<br />
	<a href="javascript:InsertTags('[b]','[/b]');" title="Boldface"><img src="./images/1/bbcodes/b.gif" alt="Boldface" /></a>
	<a href="javascript:InsertTags('[i]','[/i]');" title="Italic"><img src="./images/1/bbcodes/i.gif" alt="Italic" /></a>
	<a href="javascript:InsertTags('[u]','[/u]');" title="Underline"><img src="./images/1/bbcodes/u.gif" alt="Underline" /></a>
	<a href="javascript:InsertTags('[hr]','');" title="Horizontal Ruler"><img src="./images/1/bbcodes/hr.gif" alt="Horizontal Ruler" /></a>
	<a href="javascript:InsertTags('[img]','[/img]');" title="Image"><img src="./images/1/bbcodes/img.gif" alt="Image" /></a>
	<a href="javascript:InsertTagsParams('[url={param1}]{param2}','[/url]','Please provide URL (with http://)','Please provide text for the link');" title="Internet address (URL)"><img src="./images/1/bbcodes/url.gif" alt="Internet address (URL)" /></a>
	<a href="javascript:InsertTags('[email]','[/email]');" title="E-mail address"><img src="./images/1/bbcodes/email.gif" alt="E-mail address" /></a>
	<a href="javascript:InsertTags('[quote]','[/quote]');" title="Quote"><img src="./images/1/bbcodes/quote.gif" alt="Quote" /></a>
	<a href="javascript:InsertTags('[ot]','[/ot]');" title="Off Topic"><img src="./images/1/bbcodes/ot.gif" alt="Off Topic" /></a>
	<a href="javascript:popup_code();" title="Source Code (Syntax Highlighting)"><img src="./images/1/bbcodes/code.gif" alt="Source Code (Syntax Highlighting)" /></a>
	<a href="javascript:InsertTags('[edit]','[/edit]');" title="Later additions / Marking of edited passages"><img src="./images/1/bbcodes/edit.gif" alt="Later additions / Marking of edited passages" /></a>
	<a href="javascript:list();" title="Unordered list"><img src="./images/1/bbcodes/ul.gif" alt="Unordered list" /></a>
	<a href="javascript:list('ol');" title="Ordered list"><img src="./images/1/bbcodes/ol.gif" alt="Ordered list" /></a>
	<a title="Definition / Explanation" href="javascript:InsertTagsParams('[note={param1}]{param2}','[/note]','Please enter the definition of the word','Please enter the word to be defined');"><img src="./images/1/bbcodes/note.gif" alt="Definition / Explanation" /></a>
	<a href="javascript:InsertTags('[tt]','[/tt]');" title="Typewriter text"><img src="./images/1/bbcodes/tt.gif" alt="Typewriter text" /></a>
	<a href="javascript:InsertTags('[sub]','[/sub]');" title="Subscript"><img src="./images/1/bbcodes/sub.gif" alt="Subscript" /></a>
	<a href="javascript:InsertTags('[sup]','[/sup]');" title="Superscript"><img src="./images/1/bbcodes/sup.gif" alt="Superscript" /></a>
	<?php foreach ($cbb as $bb) { ?>
	<a href="javascript:<?php echo $bb['href']; ?>" title="<?php echo $bb['title']; ?>"><img src="<?php echo $bb['buttonimage']; ?>" alt="<?php echo $bb['title']; ?>" /></a>
	<?php } ?>
	</div>
  </td>
 </tr>
</table>
	<?php
}

($code = $plugins->load('admin_cms_jobs')) ? eval($code) : null;

if ($job == 'nav') {
	send_nocache_header();
	echo head();
?>
 <table class="border" border="0" cellspacing="0" cellpadding="4" align="center">
  <tr>
   <td class="obox" colspan="4">
   	<span style="float: right;">
   	<a class="button" href="admin.php?action=cms&job=nav_add">Add Link</a>
   	<a class="button" href="admin.php?action=cms&job=nav_addbox">Add Box</a>
   	<a class="button" href="admin.php?action=cms&job=nav_addplugin">Add Plugin</a>
   	</span>Manage Navigation
   </td>
  </tr>
  <tr>
   <td class="ubox">Link</td>
   <td class="ubox">Status</td>
   <td class="ubox">Order</td>
   <td class="ubox">Action</td>
  </tr>
<?php
	$result = $db->query("SELECT * FROM {$db->pre}menu ORDER BY ordering, id", __LINE__, __FILE__);
	$sqlcache = array();
	$cat = array();
	$sub = array();
	while ($row = $db->fetch_assoc($result)) {
		$sqlcache[] = $row;
		if ($row['sub'] > 0) {
			if (!isset($sub[$row['sub']]) || !is_array($sub[$row['sub']])) {
				$sub[$row['sub']] = array();
			}
			$sub[$row['sub']][] = $row;
		}
		else {
			$cat[] = $row;
		}
	}

	foreach ($cat as $head) {
		$type = array();
		if ($head['module'] > 0) {
			$type[] = '<em>Plugin</em>';
		}
		if ($head['active'] == 0) {
			$type[] = '<em>Inactive</em>';
		}
	?>
	<tr class="mmbox">
	<td width="50%">
	<?php echo $head['name']; ?><?php echo iif(count($type) > 0, ' ('.implode('; ', $type).')' ); ?>
	</td>
	<td width="10%">
	<?php
	if ($head['active'] == 1) {
		echo '<a href="admin.php?action=cms&job=nav_active&id='.$head['id'].iif($head['module'] > 0, '&plug='.$head['module']).'&act=0">Deactivate</a>';
	}
	else {
		echo '<a href="admin.php?action=cms&job=nav_active&id='.$head['id'].iif($head['module'] > 0, '&plug='.$head['module']).'&act=1">Activate</a>';
	}
	?>
	</td>
	<td width="15%"><?php echo $head['ordering']; ?>&nbsp;&nbsp;
	<a href="admin.php?action=cms&job=nav_move&id=<?php echo $head['id']; ?>&value=-1"><img src="admin/html/images/asc.gif" border="0" alt="Up"></a>&nbsp;
	<a href="admin.php?action=cms&job=nav_move&id=<?php echo $head['id']; ?>&value=1"><img src="admin/html/images/desc.gif" border="0" alt="Down"></a>
	</td>
	<td width="35%">
	 <a class="button" href="admin.php?action=cms&job=nav_edit&id=<?php echo $head['id']; ?>">Edit</a>
	 <a class="button" href="admin.php?action=cms&job=nav_delete&id=<?php echo $head['id']; ?>">Delete</a>
	</td>
	</tr>
	<?php
	if (isset($sub[$head['id']]) && count($sub[$head['id']]) > 0) {
		foreach ($sub[$head['id']] as $link) {
			?>
			<tr class="mbox">
			<td width="50%">&nbsp;&middot;&nbsp;
			<?php
			if (empty($link['link'])) {
				echo $link['name'];
			}
			else {
				?>
				<a href="<?php echo $link['link']; ?>" target="<?php echo $link['param']; ?>"><?php echo $link['name']; ?></a>
				<?php } echo iif ($link['active'] == '0', ' (<em>Inactive</em>)'); ?><br />
				</td>
				<td class="mbox" width="10%">
				<?php
				if ($link['active'] == 1) {
					echo '<a href="admin.php?action=cms&job=nav_active&id='.$link['id'].'&act=0">Deactivate</a>';
				}
				else {
					echo '<a href="admin.php?action=cms&job=nav_active&id='.$link['id'].'&act=1">Activate</a>';
				}
				?>
				</td>
				<td class="mbox" width="15%" nowrap="nowrap" align="center"><?php echo $link['ordering']; ?>&nbsp;&nbsp;
				<a href="admin.php?action=cms&job=nav_move&id=<?php echo $link['id']; ?>&value=-1"><img src="admin/html/images/asc.gif" border="0" alt="Up"></a>&nbsp;
				<a href="admin.php?action=cms&job=nav_move&id=<?php echo $link['id']; ?>&value=1"><img src="admin/html/images/desc.gif" border="0" alt="Down"></a>
				</font></td>
				<td class="mbox" width="25%">
				 <a class="button" href="admin.php?action=cms&job=nav_edit&id=<?php echo $link['id'] ?>">Edit</a>
				 <a class="button" href="admin.php?action=cms&job=nav_delete&id=<?php echo $link['id']; ?>">Delete</a>
				</td>
				</tr>
				<?php
				if (isset($sub[$link['id']]) && count($sub[$link['id']]) > 0) {
					foreach ($sub[$link['id']] as $sublink) {
						?>
						<tr class="mbox">
						<td width="50%">&nbsp;&nbsp;&nbsp;<img src='admin/html/images/list.gif' border="0" alt="">&nbsp;
						<?php
						if (empty($sublink['link'])) {
							echo $sublink['name'];
						}
						else {
							?>
							<a href='<?php echo $sublink['link']; ?>' target='<?php echo $sublink['param']; ?>'><?php echo $sublink['name']; ?></a>
							<?php } echo iif ($sublink['active'] == '0', ' (<i>Inactive</i>)'); ?></font><br>
							</td>
							<td class="mbox" width="10%">
							<?php
							if ($sublink['active'] == 1) {
								echo '<a href="admin.php?action=cms&job=nav_active&id='.$sublink['id'].'&act=0">Deactivate</a>';
							}
							else {
								echo '<a href="admin.php?action=cms&job=nav_active&id='.$sublink['id'].'&act=1">Activate</a>';
							}
							?>
							</td>
							<td class="mbox" width="15%" nowrap="nowrap" align="right"><?php echo $sublink['ordering']; ?>&nbsp;&nbsp;
							<a href="admin.php?action=cms&job=nav_move&id=<?php echo $sublink['id']; ?>&value=-1"><img src="admin/html/images/asc.gif" border="0" alt="Up"></a>&nbsp;
							<a href="admin.php?action=cms&job=nav_move&id=<?php echo $sublink['id']; ?>&value=1"><img src="admin/html/images/desc.gif" border="0" alt="Down"></a>
							</td>
							<td class="mbox" width="25%">
							 <a class="button" href="admin.php?action=cms&job=nav_edit&id=<?php echo $sublink['id']; ?>">Edit</a>
							 <a class="button" href="admin.php?action=cms&job=nav_delete&id=<?php echo $sublink['id']; ?>">Delete</a>
							</td>
							</tr>
							<?php
						}
					}
			}
		}
	}
	?></table><?php
	echo foot();
}
elseif ($job == 'nav_edit') {
	echo head();
	$id = $gpc->get('id', int);
	$result = $db->query("SELECT * FROM {$db->pre}menu WHERE id = '{$id}' LIMIT 1", __LINE__, __FILE__);
	$data = $db->fetch_assoc($result);
	$data['group_array'] = explode(',', $data['groups']);

	$groups = $db->query("SELECT id, name FROM {$db->pre}groups", __LINE__, __FILE__);

	if ($data['sub'] > 0) {
		$result = $db->query("SELECT id, name, sub FROM {$db->pre}menu WHERE module = '0' ORDER BY ordering, id", __LINE__, __FILE__);
		$cache = array(0 => array());
		while ($row = $db->fetch_assoc($result)) {
			if (!isset($cache[$row['sub']]) || !is_array($cache[$row['sub']])) {
				$cache[$row['sub']] = array();
			}
			$cache[$row['sub']][] = $row;
		}
	}

	if ($data['module'] > 0) {
		$plugs = $db->query("SELECT * FROM {$db->pre}plugins WHERE position = 'navigation' ORDER BY ordering", __LINE__, __FILE__);
	}
	?>
<form name="form" method="post" action="admin.php?action=cms&job=nav_edit2&id=<?php echo $id; ?>">
 <table class="border" border="0" cellspacing="0" cellpadding="4" align="center">
  <tr>
   <td class="obox" colspan="2">Edit <?php echo iif ($data['sub'] > 0, 'link', 'box'); ?></td>
  </tr>
  <tr>
   <td class="mbox" width="50%">Title:</td>
   <td class="mbox" width="50%"><input type="text" name="title" size="40" value="<?php echo $data['name']; ?>" /></td>
  </tr>
<?php if ($data['sub'] > 0) { ?>
  <tr>
   <td class="mbox" width="50%">File/URL:<br />
   <span class="stext">
   - <a href="javascript:docs();">Existing Documents</a><br />
   - <a href="javascript:coms();">Existing Components</a>
   </span></td>
   <td class="mbox" width="50%"><input type="text" name="url" size="40" value="<?php echo $data['link']; ?>" /></td>
  </tr>
  <tr>
   <td class="mbox" width="50%">Target:<br /><span class="stext">All links will be opened in the same window by default. This option defines the target window for the link. For example: "_blank" will open links in a new window.</span></td>
   <td class="mbox" width="50%"><input type="text" name="target" size="40" value="<?php echo $data['param']; ?>" /></td>
  </tr>
  <tr>
   <td class="mbox" width="50%">Parent Box/Link:</td>
   <td class="mbox" width="50%">
   <select name="sub">
   <?php foreach ($cache[0] as $row) { ?>
   <option style="font-weight: bold;" value="<?php echo $row['id']; ?>"<?php echo iif($row['id'] == $data['sub'], ' selected="selected"'); ?>><?php echo $row['name']; ?></option>
   <?php
   if (isset($cache[$row['id']])) {
   foreach ($cache[$row['id']] as $row) {
   ?>
   <option value="<?php echo $row['id']; ?>"<?php echo iif($row['id'] == $data['sub'], ' selected="selected"'); ?>>+&nbsp;<?php echo $row['name']; ?></option>
   <?php }}} ?>
   </select>
   </td>
  </tr>
<?php } if ($data['module'] > 0) { ?>
  <tr>
   <td class="mbox" width="50%">Plugin:</td>
   <td class="mbox" width="50%">
   <select name="plugin">
   <?php while ($row = $db->fetch_assoc($plugs)) { ?>
   <option value="<?php echo $row['id']; ?>"<?php echo iif($row['id'] == $data['module'], ' selected="selected"'); ?>><?php echo $row['name']; ?></option>
   <?php } ?>
   </select>
   </td>
  </tr>
<?php } ?>
  <tr>
   <td class="mbox" width="50%">Groups:<br /><span class="stext">Groups which have the ability to view the box.</span></td>
   <td class="mbox" width="50%">
   <?php while ($row = $db->fetch_assoc($groups)) { ?>
	<input type="checkbox" name="groups[]"<?php echo iif($data['groups'] == 0 || in_array($row['id'], $data['group_array']), ' checked="checked"'); ?> value="<?php echo $row['id']; ?>"> <?php echo $row['name']; ?><br />
   <?php } ?>
   </td>
  </tr>
  <tr>
   <td class="mbox" width="50%">Active:</td>
   <td class="mbox" width="50%"><input type="checkbox" name="active" value="1"<?php echo iif($data['active'] == 1, ' checked="checked"'); ?> /></td>
  </tr>
  <tr>
   <td class="ubox" colspan="2" align="center"><input type="submit" value="Save" /></td>
  </tr>
 </table>
</form>
	<?php
	echo foot();
}
elseif ($job == 'nav_edit2') {
	echo head();

	$id = $gpc->get('id', int);
	$result = $db->query("SELECT * FROM {$db->pre}menu WHERE id = '{$id}' LIMIT 1", __LINE__, __FILE__);
	$data = $db->fetch_assoc($result);

	$title = $gpc->get('title', str);
	$title = trim($title);
	if (empty($title)) {
		error('admin.php?action=cms&job=nav_addbox', 'Sie haben keinen Titel angegeben.');
	}
	$active = $gpc->get('active', int);
	$groups = $gpc->get('groups', arr_int);
	$result = $db->query('SELECT COUNT(*) FROM '.$db->pre.'groups', __LINE__, __FILE__);
	$count = $db->fetch_num($result);
	if (count($groups) == $count[0]) {
		$groups = 0;
	}
	else {
		$groups = implode(',', $groups);
	}
	if ($data['sub'] > 0) {
		$target = $gpc->get('target', str);
		$url = $gpc->get('url', str);
		$sub = $gpc->get('sub', int);
		$db->query("UPDATE {$db->pre}menu SET name = '{$title}', link = '{$url}', param = '{$target}', groups = '{$groups}', sub = '{$sub}', active = '{$active}' WHERE id = '{$id}' LIMIT 1", __LINE__, __FILE__);
	}
	else {
		if ($data['module'] > 0) {
			$plug = $gpc->get('plugin', int);
			$result = $db->query("SELECT position FROM {$db->pre}plugins WHERE id = '{$plug}'", __LINE__, __FILE__);
			if ($db->num_rows($result) > 0) {
				$module_sql = ", module = '{$plug}'";
				$row = $db->fetch_assoc($result);
				$filesystem->unlink('cache/modules/'.$plugins->_group($row['position']).'.php');
				// Do not do that anymore, because it may be required
				// $db->query("UPDATE {$db->pre}plugins SET active = '{$active}' WHERE id = '{$plug}' LIMIT 1", __LINE__, __FILE__);
			}
			$db->query("UPDATE {$db->pre}menu SET name = '{$title}', groups = '{$groups}', active = '{$active}'{$module_sql} WHERE id = '{$id}' LIMIT 1", __LINE__, __FILE__);
		}
		else {
			$db->query("UPDATE {$db->pre}menu SET name = '{$title}', groups = '{$groups}', active = '{$active}' WHERE id = '{$id}' LIMIT 1", __LINE__, __FILE__);
		}
	}
	$delobj = $scache->load('modules_navigation');
	$delobj->delete();
	ok('admin.php?action=cms&job=nav', 'Data successfully changed!');
}
elseif ($job == 'nav_delete') {
	echo head();
	$id = $gpc->get('id', int);
?>
	<table class="border" border="0" cellspacing="0" cellpadding="4" align="center">
	<tr><td class="obox">Delete Box or Link</td></tr>
	<tr><td class="mbox">
	<p align="center">Do you really want to delete this box or link (to a plugin) including all child-links?</p>
	<p align="center">
	<a href="admin.php?action=cms&job=nav_delete2&id=<?php echo $id; ?>"><img border="0" alt="" src="admin/html/images/yes.gif"> Yes</a>
	&nbsp&nbsp;&nbsp;&nbsp&nbsp;&nbsp;
	<a href="javascript: history.back(-1);"><img border="0" alt="" src="admin/html/images/no.gif"> No</a>
	</p>
	</td></tr>
	</table>
<?php
	echo foot();
}
elseif ($job == 'nav_delete2') {
	echo head();
	$id = $gpc->get('id', int);
	$delete = array($id);

	$result = $db->query("SELECT id, sub FROM {$db->pre}menu WHERE sub = '{$id}'", __LINE__, __FILE__);
	while($row = $db->fetch_assoc($result)) {
		$delete[] = $row['id'];
		$result2 = $db->query("SELECT id FROM {$db->pre}menu WHERE sub = '{$row['id']}'", __LINE__, __FILE__);
		while($row2 = $db->fetch_assoc($result2)) {
			$delete[] = $row2['id'];
		}
	}

	$count = count($delete);
	$ids = implode(',', $delete);
	$db->query("DELETE FROM {$db->pre}menu WHERE id IN ({$ids}) LIMIT {$count}", __LINE__, __FILE__);
	$anz = $db->affected_rows();

	$delobj = $scache->load('modules_navigation');
	$delobj->delete();

	ok('admin.php?action=cms&job=nav', $anz.' entries deleted.');
}
elseif ($job == 'nav_move') {
	$id = $gpc->get('id', int);
	$pos = $gpc->get('value', int);
	if ($id < 1) {
		error('admin.php?action=cms&job=nav', 'Invalid ID given');
	}
	if ($pos < 0) {
		$db->query('UPDATE '.$db->pre.'menu SET ordering = ordering-1 WHERE id = '.$id, __LINE__, __FILE__);
	}
	elseif ($pos > 0) {
		$db->query('UPDATE '.$db->pre.'menu SET ordering = ordering+1 WHERE id = '.$id, __LINE__, __FILE__);
	}

	$delobj = $scache->load('modules_navigation');
	$delobj->delete();

	viscacha_header('Location: admin.php?action=cms&job=nav');
}
elseif ($job == 'nav_active') {
	$id = $gpc->get('id', int);
	$pos = $gpc->get('act', int);
	if ($id < 1) {
		error('admin.php?action=cms&job=nav', 'Invalid ID given');
	}
	if ($pos != 0 && $pos != 1) {
		error('admin.php?action=cms&job=nav', 'Invalid status specified');
	}
	$db->query('UPDATE '.$db->pre.'menu SET active = "'.$pos.'" WHERE id = '.$id, __LINE__, __FILE__);

	$plug = $gpc->get('plug', int);
	if ($plug > 0) {
		$result = $db->query("SELECT position FROM {$db->pre}plugins WHERE id = '{$plug}'", __LINE__, __FILE__);
		if ($db->num_rows($result) > 0) {
			$module_sql = ", module = '{$plug}'";
			$row = $db->fetch_assoc($result);
			$filesystem->unlink('cache/modules/'.$plugins->_group($row['position']).'.php');
			// Do not do that anymore, because it may be required
			// $db->query("UPDATE {$db->pre}plugins SET active = '{$pos}' WHERE id = '{$plug}' LIMIT 1", __LINE__, __FILE__);
		}
	}

	$delobj = $scache->load('modules_navigation');
	$delobj->delete();
	viscacha_header('Location: admin.php?action=cms&job=nav');
}
elseif ($job == 'nav_addplugin') {
	echo head();
	$id = $gpc->get('id', int);
	$sort = $db->query("SELECT ordering, name FROM {$db->pre}menu WHERE sub = '0' ORDER BY ordering, id", __LINE__, __FILE__);
	$plugs = $db->query("SELECT id, name FROM {$db->pre}plugins WHERE position = 'navigation' ORDER BY ordering", __LINE__, __FILE__);
	$groups = $db->query("SELECT id, name FROM {$db->pre}groups", __LINE__, __FILE__);
	?>
<form name="form" method="post" action="admin.php?action=cms&job=nav_addplugin2">
 <table class="border" border="0" cellspacing="0" cellpadding="4" align="center">
  <tr>
   <td class="obox" colspan="2">Add Plugin to navigation</td>
  </tr>
  <tr>
   <td class="mbox" width="50%">Title:<br /><span class="stext">Leave empty to use default.</span></td>
   <td class="mbox" width="50%"><input type="text" name="title" size="40" /></td>
  </tr>
  <tr>
   <td class="mbox" width="50%">PlugIn:</td>
   <td class="mbox" width="50%">
   <select name="plugin">
   <?php while ($row = $db->fetch_assoc($plugs)) { ?>
   <option value="<?php echo $row['id']; ?>"<?php echo iif($row['id'] == $id, ' selected="selected"'); ?>><?php echo $row['name']; ?></option>
   <?php } ?>
   </select>
   </td>
  </tr>
  <tr>
   <td class="mbox" width="50%">Sort in after:</td>
   <td class="mbox" width="50%">
   <select name="sort">
   <?php while ($row = $db->fetch_assoc($sort)) { ?>
	<option value="<?php echo $row['ordering']; ?>"><?php echo $row['name']; ?></option>
   <?php } ?>
   </select>
   </td>
  </tr>
  <tr>
   <td class="mbox" width="50%">Groups:<br /><span class="stext">Groups which have the ability to view the PlugIn.</span></td>
   <td class="mbox" width="50%">
   <?php while ($row = $db->fetch_assoc($groups)) { ?>
	<input type="checkbox" name="groups[]" checked="checked" value="<?php echo $row['id']; ?>"> <?php echo $row['name']; ?><br />
   <?php } ?>
   </td>
  </tr>
  <tr>
   <td class="ubox" colspan="2" align="center"><input type="submit" value="Add" /></td>
  </tr>
 </table>
</form>
	<?php
	echo foot();
}
elseif ($job == 'nav_addplugin2') {
	echo head();
	$plug = $gpc->get('plugin', int);
	$result = $db->query("SELECT id, name, active FROM {$db->pre}plugins WHERE id = '{$plug}' AND position = 'navigation'", __LINE__, __FILE__);
	$data = $db->fetch_assoc();
	$title = $gpc->get('title', str);
	$title = trim($title);
	if (empty($title)) {
		$title = $data['name'];
	}
	$sort = $gpc->get('sort', int);
	$groups = $gpc->get('groups', arr_int);
	$result = $db->query('SELECT COUNT(*) FROM '.$db->pre.'groups', __LINE__, __FILE__);
	$count = $db->fetch_num($result);
	if (count($groups) == $count[0]) {
		$groups = 0;
	}
	else {
		$groups = implode(',', $groups);
	}
	$db->query("INSERT INTO {$db->pre}menu (name, groups, ordering, active, module) VALUES ('{$title}','{$groups}','{$sort}','{$data['active']}','{$data['id']}')", __LINE__, __FILE__);
	$delobj = $scache->load('modules_navigation');
	$delobj->delete();
	ok('admin.php?action=cms&job=nav', 'PlugIn successful added');
}
elseif ($job == 'nav_add') {
	echo head();
	$groups = $db->query("SELECT id, name FROM {$db->pre}groups", __LINE__, __FILE__);
	$result = $db->query("SELECT id, name, sub FROM {$db->pre}menu WHERE module = '0' ORDER BY ordering, id", __LINE__, __FILE__);
	$cache = array(0 => array());
	while ($row = $db->fetch_assoc($result)) {
		if (!isset($cache[$row['sub']]) || !is_array($cache[$row['sub']])) {
			$cache[$row['sub']] = array();
		}
		$cache[$row['sub']][] = $row;
	}
	?>
<form name="form" method="post" action="admin.php?action=cms&job=nav_add2">
 <table class="border" border="0" cellspacing="0" cellpadding="4" align="center">
  <tr>
   <td class="obox" colspan="2">Add a new link</td>
  </tr>
  <tr>
   <td class="mbox" width="50%">Title:</td>
   <td class="mbox" width="50%"><input type="text" name="title" size="40" /></td>
  </tr>
  <tr>
   <td class="mbox" width="50%">File/URL:<br />
   <span class="stext">
   - <a href="javascript:docs();">Existing Documents</a><br />
   - <a href="javascript:coms();">Existing Components</a>
   </span></td>
   <td class="mbox" width="50%"><input type="text" name="url" size="40" /></td>
  </tr>
  <tr>
   <td class="mbox" width="50%">Target:<br /><span class="stext">All links will be opened in the same window by default. This option defines the target window for the link. For example: "_blank" will open links in a new window.</span></td>
   <td class="mbox" width="50%"><input type="text" name="target" size="40" /></td>
  </tr>
  <tr>
   <td class="mbox" width="50%">Parent Box/Link:</td>
   <td class="mbox" width="50%">
   <select name="sub">
   <?php foreach ($cache[0] as $row) { ?>
   <option style="font-weight: bold;" value="<?php echo $row['id']; ?>"><?php echo $row['name']; ?></option>
   <?php
   if (isset($cache[$row['id']])) {
   foreach ($cache[$row['id']] as $row) {
   ?>
   <option value="<?php echo $row['id']; ?>">+&nbsp;<?php echo $row['name']; ?></option>
   <?php }}} ?>
   </select>
   </td>
  </tr>
  </tr>
  <tr>
   <td class="mbox" width="50%">Sort in:</td>
   <td class="mbox" width="50%">
   <select name="sort">
	<option value="0">at the Beginning</option>
	<option value="1">at the End</option>
   </select>
   </td>
  </tr>
  <tr>
   <td class="mbox" width="50%">Groups:<br /><span class="stext">Groups which have the ability to view the box.</span></td>
   <td class="mbox" width="50%">
   <?php while ($row = $db->fetch_assoc($groups)) { ?>
	<input type="checkbox" name="groups[]" checked="checked" value="<?php echo $row['id']; ?>"> <?php echo $row['name']; ?><br />
   <?php } ?>
   </td>
  </tr>
  <tr>
   <td class="ubox" colspan="2" align="center"><input type="submit" value="Add" /></td>
  </tr>
 </table>
</form>
	<?php
	echo foot();
}
elseif ($job == 'nav_add2') {
	echo head();
	$title = $gpc->get('title', str);
	$target = $gpc->get('target', str);
	$url = $gpc->get('url', str);
	$sub = $gpc->get('sub', int);
	$sort = $gpc->get('sort', int);
	$groups = $gpc->get('groups', arr_int);
	if (empty($title)) {
		error('admin.php?action=cms&job=nav_addbox', 'Sie haben keinen Titel angegeben.');
	}
	if ($sort == 1) {
		$sortx = $db->fetch_num($db->query("SELECT MAX(ordering) FROM {$db->pre}menu WHERE sub = '{$sub}' LIMIT 1", __LINE__, __FILE__));
		$sort = $sortx[0]+1;
	}
	elseif ($sort == 0) {
		$sortx = $db->fetch_num($db->query("SELECT MIN(ordering) FROM {$db->pre}menu WHERE sub = '{$sub}' LIMIT 1", __LINE__, __FILE__));
		$sort = $sortx[0]-1;
	}
	else {
		$sort = 0;
	}
	$result = $db->query('SELECT COUNT(*) FROM '.$db->pre.'groups', __LINE__, __FILE__);
	$count = $db->fetch_num($result);
	if (count($groups) == $count[0]) {
		$groups = 0;
	}
	else {
		$groups = implode(',', $groups);
	}
	$db->query("INSERT INTO {$db->pre}menu (name, groups, ordering, link, param, sub) VALUES ('{$title}','{$groups}','{$sort}','{$url}','{$target}','{$sub}')", __LINE__, __FILE__);
	$delobj = $scache->load('modules_navigation');
	$delobj->delete();
	ok('admin.php?action=cms&job=nav', 'Link successfully added.');
}
elseif ($job == 'nav_addbox') {
	echo head();
	$sort = $db->query("SELECT ordering, name FROM {$db->pre}menu WHERE sub = '0' ORDER BY ordering, id", __LINE__, __FILE__);
	$groups = $db->query("SELECT id, name FROM {$db->pre}groups", __LINE__, __FILE__);
	?>
<form name="form" method="post" action="admin.php?action=cms&job=nav_addbox2">
 <table class="border" border="0" cellspacing="0" cellpadding="4" align="center">
  <tr>
   <td class="obox" colspan="2">Create a new box</td>
  </tr>
  <tr>
   <td class="mbox" width="50%">Title:</td>
   <td class="mbox" width="50%"><input type="text" name="title" size="40" /></td>
  </tr>
  <tr>
   <td class="mbox" width="50%">Sort in after:</td>
   <td class="mbox" width="50%">
   <select name="sort">
   <?php while ($row = $db->fetch_assoc($sort)) { ?>
	<option value="<?php echo $row['ordering']; ?>"><?php echo $row['name']; ?></option>
   <?php } ?>
   </select>
   </td>
  </tr>
  <tr>
   <td class="mbox" width="50%">Groups:<br /><span class="stext">Groups which have the ability to view the box.</span></td>
   <td class="mbox" width="50%">
   <?php while ($row = $db->fetch_assoc($groups)) { ?>
	<input type="checkbox" name="groups[]" checked="checked" value="<?php echo $row['id']; ?>"> <?php echo $row['name']; ?><br />
   <?php } ?>
   </td>
  </tr>
  <tr>
   <td class="ubox" colspan="2" align="center"><input type="submit" value="Add" /></td>
  </tr>
 </table>
</form>
	<?php
	echo foot();
}
elseif ($job == 'nav_addbox2') {
	echo head();
	$title = $gpc->get('title', str);
	if (empty($title)) {
		error('admin.php?action=cms&job=nav_addbox', 'Sie haben keinen Titel angegeben.');
	}
	$sort = $gpc->get('sort', int);
	$groups = $gpc->get('groups', arr_int);
	$result = $db->query('SELECT COUNT(*) FROM '.$db->pre.'groups', __LINE__, __FILE__);
	$count = $db->fetch_num($result);
	if (count($groups) == $count[0]) {
		$groups = 0;
	}
	else {
		$groups = implode(',', $groups);
	}
	$db->query("INSERT INTO {$db->pre}menu (name, groups, ordering) VALUES ('{$title}','{$groups}','{$sort}')", __LINE__, __FILE__);
	$delobj = $scache->load('modules_navigation');
	$delobj->delete();
	ok('admin.php?action=cms&job=nav', 'Box successfully added');
}
elseif ($job == 'nav_docslist') {
	echo head();
	$result = $db->query('SELECT id, title FROM '.$db->pre.'documents');
	?>
	 <table class="border" border="0" cellspacing="0" cellpadding="4" align="center">
	  <tr>
	   <td class="obox">Existing Documents and Pages</td>
	  </tr>
	  <tr>
	   <td class="mbox">
	   <?php while ($row = $db->fetch_assoc($result)) { ?>
	   <input type="radio" name="data" onclick="insert_doc('docs.php?id=<?php echo $row['id']; ?>','<?php echo htmlentities($row['title']); ?>')"> <?php echo $row['title']; ?><br>
	   <?php } ?>
	   </td>
	 </table>
	<?php
	echo foot();
}
elseif ($job == 'nav_comslist') {
	echo head();
	$result = $db->query("
		SELECT c.id, c.package, p.title
		FROM {$db->pre}component AS c
			LEFT JOIN {$db->pre}packages AS p ON c.package = p.id
		WHERE p.active = '1' AND c.active = '1'
	", __LINE__, __FILE__);
	?>
	 <table class="border" border="0" cellspacing="0" cellpadding="4" align="center">
	  <tr>
	   <td class="obox">Existing Components</td>
	  </tr>
	  <tr>
	   <td class="mbox">
	   <?php
		while ($row = $db->fetch_assoc($result)) {
			$head = array();
			$ini = $myini->read('modules/'.$row['package'].'/component.ini');
	   ?>
	   <input type="radio" name="data" onclick="insert_doc('components.php?cid=<?php echo $row['id']; ?>','<?php echo htmlentities($ini['info']['title']); ?>')"> <?php echo  $ini['info']['title']; ?> (Package: <?php echo $row['title']; ?>)<br />
	   <?php } ?>
	   </td>
	 </table>
	<?php
	echo foot();
}
elseif ($job == 'doc') {
	$result = $db->query('SELECT * FROM '.$db->pre.'documents', __LINE__, __FILE__);
	echo head();
?>
<form name="form" method="post" action="admin.php?action=cms&job=doc_delete">
 <table class="border" border="0" cellspacing="0" cellpadding="4" align="center">
  <tr>
   <td class="obox" colspan="7">
   <span style="float: right;"><a class="button" href="admin.php?action=cms&job=doc_add">Create new document</a></span>
   Manage Documents &amp; Pages
   </td>
  </tr>
  <tr>
   <td class="ubox" width="5%">Delete<br /><span class="stext"><input type="checkbox" onclick="check_all('delete[]');" name="all" value="1" /> All</span></td>
   <td class="ubox" width="40%">Title</td>
   <td class="ubox" width="5%">ID</td>
   <td class="ubox" width="20%">Author</td>
   <td class="ubox" width="15%">Last change</td>
   <td class="ubox" width="5%">Published</td>
   <td class="ubox" width="10%">Action</td>
  </tr>
<?php
	$memberdata_obj = $scache->load('memberdata');
	$memberdata = $memberdata_obj->get();

	while ($row = $db->fetch_assoc($result)) {
		if(is_id($row['author']) && isset($memberdata[$row['author']])) {
			$row['author'] = $memberdata[$row['author']];
		}
		else {
			$row['author'] = 'Unknown';
		}
		if ($row['update'] > 0) {
			$row['update'] = gmdate('d.m.Y H:i', times($row['update']));
		}
		else {
			$row['update'] = 'Unknown';
		}
?>
  <tr>
   <td class="mbox" width="5%"><input type="checkbox" name="delete[]" value="<?php echo $row['id']; ?>"></td>
   <td class="mbox" width="40%"><a href="admin.php?action=cms&job=doc_edit&id=<?php echo $row['id']; ?>"><?php echo $row['title']; ?></a></td>
   <td class="mbox" width="5%"><?php echo $row['id']; ?></td>
   <td class="mbox" width="20%"><?php echo $row['author']; ?></td>
   <td class="mbox" width="15%"><?php echo $row['update']; ?></td>
   <td class="mbox center" width="5%"><?php echo noki($row['active'], ' onmouseover="HandCursor(this)" onclick="ajax_noki(this, \'action=cms&job=doc_ajax_active&id='.$row['id'].'\')"'); ?></td>
   <td class="mbox" width="10%">
   <a class="button" href="docs.php?id=<?php echo $row['id'].SID2URL_x; ?>" target="_blank">View</a>
   <a class="button" href="admin.php?action=cms&job=doc_edit&id=<?php echo $row['id']; ?>">Edit</a>
   </td>
  </tr>
<?php } ?>
  <tr>
   <td class="ubox" width="100%" colspan="7" align="center"><input type="submit" name="Submit" value="Delete"></td>
  </tr>
 </table>
</form>
<?php
	echo foot();
}
elseif ($job == 'doc_ajax_active') {
	$id = $gpc->get('id', int);
	$result = $db->query("SELECT active FROM {$db->pre}documents WHERE id = '{$id}' LIMIT 1", __LINE__, __FILE__);
	$use = $db->fetch_assoc($result);
	$use = invert($use['active']);
	$db->query("UPDATE {$db->pre}documents SET active = '{$use}' WHERE id = '{$id}' LIMIT 1", __LINE__, __FILE__);
	$delobj = $scache->load('wraps');
	$delobj->delete();
	die(strval($use));
}
elseif ($job == 'doc_add') {
	echo head();
	$type = doctypes();
	$parser = array(
		'0' => 'No Parser',
		'1' => 'HTML',
		'2' => 'PHP (HTML)',
		'3' => 'BB-Codes'
	);
	?>
 <table class="border" border="0" cellspacing="0" cellpadding="4" align="center">
  <tr>
   <td class="obox" colspan="4">Create a new document - Step 1</td>
  </tr>
  <tr>
   <td class="ubox">Title</td>
   <td class="ubox">Template</td>
   <td class="ubox">Parser</td>
   <td class="ubox">Integration of Templates</td>
  </tr>
<?php
foreach ($type as $id => $row) {
	$row['parser'] = isset($parser[$row['parser']]) ? $parser[$row['parser']] : 'Unknown';
	$row['inline'] = ($row['inline'] == 1) ? 'Static' : 'Dynamic';
?>
  <tr>
   <td class="mbox"><a href="admin.php?action=cms&job=doc_add2&type=<?php echo $id; ?>"><?php echo $row['title']; ?></a></td>
   <td class="mbox"><?php echo $row['template']; ?></td>
   <td class="mbox"><?php echo $row['parser']; ?></td>
   <td class="mbox"><?php echo $row['inline']; ?></td>
  </tr>
<?php } ?>
 </table>
	<?php
	echo foot();
}
elseif ($job == 'doc_add2') {
	$tpl = new tpl();
	$type = $gpc->get('type', int);
	$types = doctypes();
	$format = $types[$type];
	echo head();
  	$groups = $db->query("SELECT id, name FROM {$db->pre}groups", __LINE__, __FILE__);
?>
<form id="form" method="post" action="admin.php?action=cms&job=doc_add3&type=<?php echo $type; ?>">
 <table class="border" border="0" cellspacing="0" cellpadding="4" align="center">
  <tr>
   <td class="obox" colspan="4">Create a new document - Step 2</td>
  </tr>
  <tr>
   <td class="mbox">
	<?php if ($format['inline'] == 1 && empty($format['template'])) { ?><span class="stext right">If no &lt;title&gt; can be parsed.</span><?php } ?>
	Title:<br />
	<input type="text" name="title" size="60" />
   </td>
  </tr>
  <?php if($format['remote'] != 1) { ?>
  <tr>
   <td class="mbox">
	Sourcecode:<br />
	<?php
	$editorpath = 'templates/editor/';
	$path = $tpl->altdir.'docs/'.$format['template'].'.html';
	if ($format['inline'] == 1 && file_exists($path)) {
		$preload = file_get_contents($path);
	}
	else {
		$preload = '';
	}
	if($format['parser'] == 3) {
		BBCodeToolBox();
	}
	?>
	<textarea id="template" name="template" rows="20" cols="110" class="texteditor"><?php echo $preload; ?></textarea>
	<?php if ($format['parser'] == 1) { ?>
	<link rel="stylesheet" type="text/css" href="<?php echo $editorpath; ?>rte.css" />
	<script language="JavaScript" type="text/javascript" src="<?php echo $editorpath; ?>lang/en.js"></script>
	<script language="JavaScript" type="text/javascript" src="<?php echo $editorpath; ?>richtext.js"></script>
	<script language="JavaScript" type="text/javascript" src="<?php echo $editorpath; ?>html2xhtml.js"></script>
	<script language="JavaScript" type="text/javascript">
	<!--
	window.onload = function() {
		forms = FetchElement('form');
		ta = FetchElement('template');
		forms.onsubmit = function() {
	   		updateRTE('rte');
	  		ta.value = forms.rte.value;
	  		forms.submit();
		};
		ta.style.display = 'none';
	};
	var lang = "en";
	var encoding = "iso-8859-1";
	initRTE("templates/editor/images/", "<?php echo $editorpath; ?>", '', true);
	writeRichText('rte', FetchElement('template').value, '', 750, 350, true, false, false);
	//-->
	</script>
	<?php } ?>
   </td>
  </tr>
  <?php } ?>
  <tr>
   <td class="mbox">
   <?php if($format['remote'] != 1) { ?><span class="stext right">If a path is given, the file will be saved on the filesystem instead of saving it to the database.</span><?php } ?>
   File:<br />
	<input type="text" name="file" size="60" />
   </td>
  </tr>
  <tr>
   <td class="mbox"><span class="stext right">Groups which have the ability to view the box.</span>Groups:<br />
   <?php while ($row = $db->fetch_assoc($groups)) { ?>
	<input type="checkbox" name="groups[]" checked="checked" value="<?php echo $row['id']; ?>"> <?php echo $row['name']; ?><br />
   <?php } ?>
   </td>
  </tr>
  <tr>
   <td class="mbox">
	Active:<br />
	<input type="checkbox" value="1" name="active" />
   </td>
  </tr>
  <tr><td class="ubox" align="center"><input type="submit" name="Submit" value="Add" /></td></tr>
 </table>
</form>
<?php
echo foot();
}
elseif ($job == 'doc_add3') {
	echo head();

	$type = $gpc->get('type', int);
	$title = $gpc->get('title', str);
	$active = $gpc->get('active', int);
  	$groups = $gpc->get('groups', arr_int);
  	$file = $gpc->get('file', none);
  	$file = trim($file);

	$types = doctypes();
	$format = $types[$type];

	if ($format['remote'] != 1) {
	  	if (empty($file)) {
	  		$content = $gpc->get('template', str);
	  	}
	  	else {
	  		$content = $gpc->get('template', none);
	  		if ($filesystem->file_put_contents($file, $content) > 0) {
	  			$content = '';
	  		}
	  		else {
	  			$content = $gpc->save_str($content);
	  			$file = '';
	  		}
		}
	}
	else {
		$content = '';
	}

	if (empty($title)) {
		error('admin.php?action=cms&job=doc_add', 'Title is empty!');
	}

	$result = $db->query('SELECT COUNT(*) FROM '.$db->pre.'groups', __LINE__, __FILE__);
	$count = $db->fetch_num($result);
	if (count($groups) == $count[0]) {
		$groups = 0;
	}
	else {
		$groups = implode(',', $groups);
	}

	$time = time();

	$db->query("INSERT INTO {$db->pre}documents ( `title` , `content` , `author` , `date` , `update` , `type` , `groups` , `active` , `file` ) VALUES ('{$title}', '{$content}', '{$my->id}', '{$time}' , '{$time}' , '{$type}', '{$groups}', '{$active}', '{$file}')", __LINE__, __FILE__);

	$delobj = $scache->load('wraps');
	$delobj->delete();

	ok('admin.php?action=cms&job=doc', 'Document successfully added!');
}
elseif ($job == 'doc_delete') {
	echo head();
	$delete = $gpc->get('delete', arr_int);
	if (count($delete) > 0) {
		$deleteids = array();
		foreach ($delete as $did) {
			$deleteids[] = 'id = '.$did;
		}
		$result = $db->query('SELECT file FROM '.$db->pre.'documents WHERE '.implode(' OR ',$deleteids), __LINE__, __FILE__);
		while ($row = $db->fetch_assoc($result)) {
			$rest = @substr(strtolower($row['file']), 0, 7);
			if (!empty($row['file']) && $rest != 'http://') {
				$filesystem->unlink($row['file']);
			}
		}

		$db->query('DELETE FROM '.$db->pre.'documents WHERE '.implode(' OR ',$deleteids), __LINE__, __FILE__);
		$anz = $db->affected_rows();

		$delobj = $scache->load('wraps');
		$delobj->delete();

		ok('admin.php?action=cms&job=doc', $anz.' documents deleted');
	}
	else {
		error('admin.php?action=cms&job=doc', 'You haven\'t checked any box.');
	}
}
elseif ($job == 'doc_edit') {
	$tpl = new tpl();
	$id = $gpc->get('id', int);
	$types = doctypes();
	$result = $db->query('SELECT * FROM '.$db->pre.'documents WHERE id = '.$id, __LINE__, __FILE__);
	$row = $db->fetch_assoc($result);
	if ($db->num_rows() == 0) {
		error('admin.php?action=cms&job=doc', 'Keine gültige ID übergeben');
	}
	$format = $types[$row['type']];
	if (!empty($row['file']) && $format['remote'] != 1 && !check_hp($row['file'])) {
		$row['content'] = file_get_contents($row['file']);
	}
	$groups = $db->query("SELECT id, name FROM {$db->pre}groups", __LINE__, __FILE__);
	$garr = explode(',', $row['groups']);

	$memberdata_obj = $scache->load('memberdata');
	$memberdata = $memberdata_obj->get();

	echo head();
?>
<form id="form" method="post" action="admin.php?action=cms&job=doc_edit2&id=<?php echo $id; ?>">
 <table class="border" border="0" cellspacing="0" cellpadding="4" align="center">
  <tr>
   <td class="obox" colspan="4">Create a new document - Step 2</td>
  </tr>
  <tr>
   <td class="mbox">
	<?php if ($format['inline'] == 1 && empty($format['template'])) { ?><span class="stext right">If no &lt;title&gt; can be parsed.</span><?php } ?>
	Title:<br />
	<input type="text" name="title" size="60" value="<?php echo $gpc->prepare($row['title']); ?>" />
   </td>
  </tr>
  <?php if($format['remote'] != 1) { ?>
  <tr>
   <td class="mbox">
	Sourcecode:<br />
	<?php
	if($format['parser'] == 3) {
		BBCodeToolBox();
	}
	?>
	<textarea id="template" name="template" rows="20" cols="110" class="texteditor"><?php echo $row['content']; ?></textarea>
	<?php if ($format['parser'] == 1) { ?>
	<link rel="stylesheet" type="text/css" href="templates/editor/rte.css" />
	<script language="JavaScript" type="text/javascript" src="templates/editor/lang/en.js"></script>
	<script language="JavaScript" type="text/javascript" src="templates/editor/richtext.js"></script>
	<script language="JavaScript" type="text/javascript" src="templates/editor/html2xhtml.js"></script>
	<script language="JavaScript" type="text/javascript">
	<!--
	window.onload = function() {
		forms = FetchElement('form');
		ta = FetchElement('template');
		forms.onsubmit = function() {
	   		updateRTE('rte');
	  		ta.value = forms.rte.value;
	  		forms.submit();
		};
		ta.style.display = 'none';
	};
	var lang = "en";
	var encoding = "iso-8859-1";
	initRTE("templates/editor/images/", "templates/editor/", '', true);
	writeRichText('rte', FetchElement('template').value, '', 750, 350, true, false, false);
	//-->
	</script>
	<?php } ?>
   </td>
  </tr>
  <?php } ?>
  <tr>
   <td class="mbox">
   <?php if($format['remote'] != 1) { ?><span class="stext right">If a path is given, the file will be saved on the filesystem instead of saving it to the database.</span><?php } ?>
   File:<br />
	<input type="text" name="file" value="<?php echo $row['file']; ?>" size="60" />
   </td>
  </tr>
  <tr>
   <td class="mbox"><span class="stext right">Groups which have the ability to view the box.</span>Groups:<br />
   <?php while ($g = $db->fetch_assoc($groups)) { ?>
	<input type="checkbox" name="groups[]"<?php echo iif($row['groups'] == 0 || in_array($g['id'], $garr),'checked="checked"'); ?> value="<?php echo $g['id']; ?>"> <?php echo $g['name']; ?><br />
   <?php } ?>
   </td>
  </tr>
  <tr>
   <td class="mbox">
	Author:<br />
	<input type="radio" value="<?php echo $row['author']; ?>" name="author" checked="checked" /> Keep current Author: <strong><?php echo isset($memberdata[$row['author']]) ? $memberdata[$row['author']] : 'Unknown'; ?></strong><br />
	<input type="radio" value="<?php echo $my->id; ?>" name="author" /> Change author to: <strong><?php echo $my->name; ?></strong>
   </td>
  </tr>
  <tr>
   <td class="mbox">
	Active:<br />
	<input type="checkbox" value="1" name="active"<?php echo iif($row['active'] == 1, ' checked="checked"'); ?> />
   </td>
  </tr>
  <tr><td class="ubox" align="center"><input type="submit" name="Submit" value="Edit" /></td></tr>
 </table>
</form>
<?php
echo foot();
}
elseif ($job == 'doc_edit2') {
	echo head();

	$id = $gpc->get('id', int);
	$title = $gpc->get('title', str);
	$active = $gpc->get('active', int);
	$author = $gpc->get('author', int);
  	$groups = $gpc->get('groups', arr_int);
  	$file = $gpc->get('file', none);
  	$file = trim($file);

	$result = $db->query("SELECT type FROM {$db->pre}documents WHERE id = '{$id}' LIMIT 1", __LINE__, __FILE__);
	if ($db->num_rows($result) == 0) {
		error('admin.php?action=cms&job=doc', 'Document does not exist.');
	}
	$doc = $db->fetch_assoc($result);
	$types = doctypes();
	$format = $types[$doc['type']];

	if ($format['remote'] != 1) {
	  	if (empty($file)) {
	  		$content = $gpc->get('template', str);
	  	}
	  	else {
	  		$content = $gpc->get('template', none);
	  		if ($filesystem->file_put_contents($file, $content) > 0) {
	  			$content = '';
	  		}
	  		else {
	  			$content = $gpc->save_str($content);
	  			$file = '';
	  		}
		}
	}
	else {
		$content = '';
	}

	$result = $db->query('SELECT COUNT(*) FROM '.$db->pre.'groups');
	$count = $db->fetch_num($result);
	if (count($groups) == $count[0]) {
		$groups = 0;
	}
	else {
		$groups = implode(',', $groups);
	}

	$time = time();

	$db->query("UPDATE {$db->pre}documents SET `title` = '{$title}', `content` = '{$content}', `update` = '{$time}', `groups` = '{$groups}', `active` = '{$active}', `file` = '{$file}', `author` = '{$author}' WHERE id = '{$id}' LIMIT 1",__LINE__,__FILE__);

	$delobj = $scache->load('wraps');
	$delobj->delete();

	ok('admin.php?action=cms&job=doc', 'Document successfully changed!');
}
elseif ($job == 'doc_code') {
	echo head();
	$codelang = $scache->load('syntaxhighlight');
	$clang = $codelang->get();
	?>
	<script src="admin/html/editor.js" type="text/javascript"></script>
	<table class="border">
	<tr><td class="obox">BB-Code Tag: Code</td></tr>
	<tr><td class="mbox">
	<strong>Choose the programming language for the highlighting:</strong><br /><br />
	<ul>
	   <li><input type="radio" name="data" onclick="InsertTagsCode('[code]','[/code]')" /> No Syntax Highlighting</li>
	   <?php foreach ($clang as $row) { ?>
	   <li><input type="radio" name="data" onclick="InsertTagsCode('[code=<?php echo $row['short']; ?>]','[/code]')" /> <?php echo $row['name']; ?></li>
	   <?php } ?>
	</ul>
	</td></tr>
	</table>
	<?php
	echo foot();
}
elseif ($job == 'feed') {
	$result = $db->query('SELECT * FROM '.$db->pre.'grab', __LINE__, __FILE__);
	echo head();
?>
<form name="form" method="post" action="admin.php?action=cms&job=feed_delete">
 <table class="border" border="0" cellspacing="0" cellpadding="4" align="center">
  <tr>
   <td class="obox" colspan="5"><span style="float: right;"><a class="button" href="admin.php?action=cms&job=feed_add">Add a new Newsfeed</a></span>Import of Newsfeeds (<?php echo $db->num_rows(); ?>)</td>
  </tr>
  <tr>
   <td class="ubox" width="5%">Delete<br /><span class="stext"><input type="checkbox" onclick="check_all('delete[]');" name="all" value="1" /> All</span></td>
   <td class="ubox" width="5%">ID</td>
   <td class="ubox" width="35%">Title</td>
   <td class="ubox" width="45%">File</td>
   <td class="ubox" width="10%">Entries</td>
  </tr>
<?php
	while ($row = $db->fetch_assoc($result)) {
	if ($row['entries'] == 0) {
		$row['entries'] = 'All';
	}
?>
  <tr>
   <td class="mbox" width="5%"><input type="checkbox" name="delete[]" value="<?php echo $row['id']; ?>"></td>
   <td class="mbox" width="5%"><?php echo $row['id']; ?></td>
   <td class="mbox" width="35%"><a href="admin.php?action=cms&job=feed_edit&id=<?php echo $row['id']; ?>"><?php echo $row['title']; ?></a></td>
   <td class="mbox" width="45%"><a href="<?php echo $row['file']; ?>" target="_blank"><?php echo $row['file']; ?></a></td>
   <td class="mbox" width="10%"><?php echo $row['entries']; ?></td>
  </tr>
<?php } ?>
  <tr>
   <td class="ubox" width="100%" colspan="5" align="center"><input type="submit" name="Submit" value="Delete"></td>
  </tr>
 </table>
</form>
<?php
	echo foot();
}
elseif ($job == 'feed_add') {
echo head();
?>
<form name="form" method="post" action="admin.php?action=cms&job=feed_add2">
 <table class="border" border="0" cellspacing="0" cellpadding="4" align="center">
  <tr>
   <td class="obox" colspan="2">Add a new Newsfeed</td>
  </tr>
  <tr>
   <td class="mbox">Titel:<br><span class="stext">If no title can be read from the newsfeed.</td>
   <td class="mbox"><input type="text" name="temp1" size="60"></td>
  </tr>
  <tr>
   <td class="mbox">URL of the Newsfeed:<br><span class="stext">RSS 0.91, RSS 1.0, RSS 2.0 or ATOM-Newsfeed</td>
   <td class="mbox"><input type="text" name="temp2" size="60"></td>
  </tr>
  <tr>
   <td class="mbox">Number of Entries:<br><span class="stext">Maximum number of entries to show, 0 = all. Newsfeed are (normally) limited to 15 entries!</td>
   <td class="mbox"><input type="text" name="value" size="3"></td>
  </tr>
  <tr>
   <td class="ubox" width="100%" colspan="2" align="center"><input type="submit" name="Submit" value="Send"></td>
  </tr>
 </table>
</form>
<?php
	echo foot();
}
elseif ($job == 'feed_add2') {
	echo head();

	$title = $gpc->get('temp1', str);
	$file = $gpc->get('temp2', str);
	$entries = $gpc->get('value', int);

	if (empty($title)) {
		error('admin.php?action=cms&job=feed_add', 'No title specified');
	}
	if (empty($file)) {
		error('admin.php?action=cms&job=feed_add', 'No URL specified');
	}
	if (empty($entries)) {
		$entries = 0;
	}

	$db->query('INSERT INTO '.$db->pre.'grab (title, file, entries) VALUES ("'.$title.'","'.$file.'","'.$entries.'")', __LINE__, __FILE__);

	$delobj = $scache->load('grabrss');
	$delobj->delete();

	ok('admin.php?action=cms&job=feed', 'Newsfeed successfully added');
}
elseif ($job == 'feed_delete') {
	echo head();
	$delete = $gpc->get('delete', arr_int);
	if (count($delete) > 0) {
		$deleteids = array();
		foreach ($delete as $did) {
			$deleteids[] = 'id = '.$did;
		}

		$db->query('DELETE FROM '.$db->pre.'grab WHERE '.implode(' OR ',$deleteids), __LINE__, __FILE__);
		$anz = $db->affected_rows();

		$delobj = $scache->load('grabrss');
		$delobj->delete();

		ok('admin.php?action=cms&job=feed', $anz.' Newsfeed(s) successfully deleted');
	}
	else {
		error('admin.php?action=cms&job=feed', 'No newsfeed selected');
	}
}
elseif ($job == 'feed_edit') {
echo head();
$id = $gpc->get('id', int);
if (empty($id)) {
	error('admin.php?action=cms&job=feed', 'Invalid ID given');
}
$result = $db->query('SELECT * FROM '.$db->pre.'grab WHERE id = '.$id, __LINE__, __FILE__);
$row = $db->fetch_assoc($result);

?>
<form name="form" method="post" action="admin.php?action=cms&job=feed_edit2&id=<?php echo $id; ?>">
 <table class="border" border="0" cellspacing="0" cellpadding="4" align="center">
  <tr>
   <td class="obox" colspan="2">Edit Document</td>
  </tr>
  <tr>
   <td class="mbox">Title:<br><span class="stext">If no title can be read from the newsfeed.</span></td>
   <td class="mbox"><input type="text" name="temp1" size="60" value="<?php echo $gpc->prepare($row['title']); ?>"></td>
  </tr>
  <tr>
   <td class="mbox">URL of the Newsfeed:<br><span class="stext">RSS 0.91, RSS 1.0, RSS 2.0 or ATOM-Newsfeed</span></td>
   <td class="mbox"><input type="text" name="temp2" size="60" value="<?php echo $row['file']; ?>"></td>
  </tr>
  <tr>
   <td class="mbox">Number of Entries:<br><span class="stext">Maximum number of entries for output, 0 = all. Newsfeed are (normally) limited to 15 entries!</span></td>
   <td class="mbox"><input type="text" name="value" size="3" value="<?php echo $row['entries']; ?>"></td>
  </tr>
  <tr>
   <td class="ubox" width="100%" colspan=2 align="center"><input type="submit" name="Submit" value="Send"></td>
  </tr>
 </table>
</form>
<?php
	echo foot();
}
elseif ($job == 'feed_edit2') {
	echo head();

	$title = $gpc->get('temp1', str);
	$file = $gpc->get('temp2', str);
	$entries = $gpc->get('value', int);
	$id = $gpc->get('id', int);
	if (!is_id($id)) {
		error('admin.php?action=cms&job=feed', 'Invalid ID given');
	}
	if (empty($title)) {
		error('admin.php?action=cms&job=feed_edit&id='.$id, 'No title specified');
	}
	if (empty($file)) {
		error('admin.php?action=cms&job=feed_edit&id='.$id, 'No URL specified');
	}
	if (empty($entries)) {
		$entries = 0;
	}

	$db->query('UPDATE '.$db->pre.'grab SET file = "'.$file.'", title = "'.$title.'", entries = "'.$entries.'" WHERE id = "'.$id.'"', __LINE__, __FILE__);

	$delobj = $scache->load('grabrss');
	$delobj->delete();

	ok('admin.php?action=cms&job=feed', 'Newsfeed successfully updated');
}
?>