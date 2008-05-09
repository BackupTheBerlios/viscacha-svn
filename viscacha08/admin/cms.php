<?php
if (defined('VISCACHA_CORE') == false) { die('Error: Hacking Attempt'); }

// PK: MultiLangAdmin
$lang->group("admin/cms");

require('classes/class.phpconfig.php');
$myini = new INI();

function BBCodeToolBox($id, $content = '', $taAttr = '') {
	global $tpl, $lang, $scache, $config;

	$lang->group("bbcodes");

	$taAttr = ' '.trim($taAttr);

	$cache = $scache->load('custombb');
	$cbb = $cache->get();
	foreach ($cbb as $key => $bb) {
		if (empty($bb['buttonimage'])) {
			unset($cbb[$key]);
			continue;
		}
		$cbb[$key]['title'] = htmlspecialchars($bb['title']);
		if ($bb['twoparams']) {
			$cbb[$key]['href'] = "InsertTags('{$id}', '[{$bb['bbcodetag']}=]','[/{$bb['bbcodetag']}]');";
		}
		else {
			$cbb[$key]['href'] = "InsertTags('{$id}', '[{$bb['bbcodetag']}]','[/{$bb['bbcodetag']}]');";
		}
	}

	$codelang = $scache->load('syntaxhighlight');
	$clang = $codelang->get();

	$cache = $scache->load('smileys');
	$cache->seturl($config['smileyurl']);
	$smileydata = $cache->get();
	$smileys = array(0 => array(), 1 => array());
	foreach ($smileydata as $bb) {
	   	if ($bb['show'] == 1) {
			$smileys[1][] = $bb;
		}
		else {
			$smileys[0][] = $bb;
		}
	}
	?>
	<script src="templates/editor/bbcode.js" type="text/javascript"></script>
	<table class="editor_textarea_outer">
		<tr><td class="editor_toolbar">
			<a id="menu_bbcolor_<?php echo $id; ?>" href="#" onmouseover="RegisterMenu('bbcolor_<?php echo $id; ?>');" class="editor_toolbar_dropdown"><img src="<?php echo $tpl->img('desc'); ?>" alt="<?php echo $lang->phrase('bbcodes_expand'); ?>" /> <?php echo $lang->phrase('bbcodes_color'); ?></a>
			<div class="popup" id="popup_bbcolor_<?php echo $id; ?>">
			<strong><?php echo $lang->phrase('bbcodes_color_title'); ?></strong>
			<div class="bbcolor"><script type="text/javascript">document.write(writeRow('<?php echo $id; ?>'));</script></div>
			</div>
			<img src="templates/editor/images/seperator.gif" alt="" />
			<a id="menu_bbsize" href="#" onmouseover="RegisterMenu('bbsize');" class="editor_toolbar_dropdown"><img src="<?php echo $tpl->img('desc'); ?>" alt="<?php echo $lang->phrase('bbcodes_expand'); ?>" /> <?php echo $lang->phrase('bbcodes_size'); ?></a>
		    <div class="popup" id="popup_bbsize">
		    <strong><?php echo $lang->phrase('bbcodes_size_title'); ?></strong>
		   	<ul>
		    	<li><span class="popup_line" onclick="InsertTagsMenu('<?php echo $id; ?>', '[size=large]','[/size]','bbsize_<?php echo $id; ?>')" style="font-size: 1.3em;"><?php echo $lang->phrase('bbcodes_size_large'); ?></span></li>
		    	<li><span class="popup_line" onclick="InsertTagsMenu('<?php echo $id; ?>', '[size=small]','[/size]','bbsize_<?php echo $id; ?>')" style="font-size: 0.8em;"><?php echo $lang->phrase('bbcodes_size_small'); ?></span></li>
		    	<li><span class="popup_line" onclick="InsertTagsMenu('<?php echo $id; ?>', '[size=extended]','[/size]','bbsize_<?php echo $id; ?>')" style="letter-spacing: 3px;"><?php echo $lang->phrase('bbcodes_size_extended'); ?></span></li>
		    </ul>
		    </div>
		    <img src="templates/editor/images/seperator.gif" alt="" />
			<a id="menu_bbhx_<?php echo $id; ?>" href="#" onmouseover="RegisterMenu('bbhx_<?php echo $id; ?>');" class="editor_toolbar_dropdown"><img src="<?php echo $tpl->img('desc'); ?>" alt="<?php echo $lang->phrase('bbcodes_expand'); ?>" /> <?php echo $lang->phrase('bbcodes_header'); ?></a>
			<div class="popup" id="popup_bbhx_<?php echo $id; ?>">
			<strong><?php echo $lang->phrase('bbcodes_header_title'); ?></strong>
			<ul>
				<li><h4 class="popup_line" onclick="InsertTagsMenu('<?php echo $id; ?>', '[h=large]','[/h]','bbhx_<?php echo $id; ?>')" style="margin: 0px; font-size: 14pt;"><?php echo $lang->phrase('bbcodes_header_h1'); ?></h4></li>
				<li><h5 class="popup_line" onclick="InsertTagsMenu('<?php echo $id; ?>', '[h=middle]','[/h]','bbhx_<?php echo $id; ?>')" style=" margin: 0px; font-size: 13pt;"><?php echo $lang->phrase('bbcodes_header_h2'); ?></h5></li>
				<li><h6 class="popup_line" onclick="InsertTagsMenu('<?php echo $id; ?>', '[h=small]','[/h]','bbhx_<?php echo $id; ?>')" style="margin: 0px; font-size: 12pt;"><?php echo $lang->phrase('bbcodes_header_h3'); ?></h6></li>
			</ul>
			</div>
			<img src="templates/editor/images/seperator.gif" alt="" />
			<a id="menu_bbtable_<?php echo $id; ?>" href="#" onmouseover="RegisterMenu('bbtable_<?php echo $id; ?>');" class="editor_toolbar_dropdown"><img src="<?php echo $tpl->img('desc'); ?>" alt="<?php echo $lang->phrase('bbcodes_expand'); ?>" /> <?php echo $lang->phrase('bbcodes_table'); ?></a>
			<div class="popup" id="popup_bbtable_<?php echo $id; ?>">
			<strong><?php echo $lang->phrase('bbcodes_create_table'); ?></strong>
			<div class="bbtable">
				<input type="checkbox" style="height: 2em;" id="table_head_<?php echo $id; ?>" value="1" /> <?php echo $lang->phrase('bbcodes_table_show_head'); ?>
				<br class="newinput" /><hr class="formsep" />
				<input type="text" size="4" id="table_rows_<?php echo $id; ?>" value="2" /> <?php echo $lang->phrase('bbcodes_table_rows'); ?>
				<br class="newinput" /><hr class="formsep" />
				<input type="text" size="4" id="table_cols_<?php echo $id; ?>" value="2" /> <?php echo $lang->phrase('bbcodes_table_cols'); ?>
				<br class="newinput" /><hr class="formsep" />
				<div class="center">[ <b><a href="javascript:InsertTable('<?php echo $id; ?>')"><?php echo $lang->phrase('bbcodes_table_insert_table'); ?></a></b> ]</div>
				<br class="iefix_br" />
			</div>
			</div>
			<img src="templates/editor/images/seperator.gif" alt="" />
			<a id="menu_bbsourcecode_<?php echo $id; ?>" href="#" onmouseover="RegisterMenu('bbsourcecode_<?php echo $id; ?>');" class="editor_toolbar_dropdown"><img src="<?php echo $tpl->img('desc'); ?>" alt="<?php echo $lang->phrase('bbcodes_expand'); ?>" /> <?php echo $lang->phrase('bbcodes_code_short'); ?></a>
			<div class="popup" id="popup_bbsourcecode_<?php echo $id; ?>">
			<strong><?php echo $lang->phrase('bbcodes_code'); ?></strong>
			<ul>
				<li><span class="popup_line" onclick="InsertTagsMenu('<?php echo $id; ?>', '[code]','[/code]', 'bbsourcecode_<?php echo $id; ?>')"><?php echo $lang->phrase('geshi_bbcode_nohighlighting'); ?></span></li>
				<?php foreach ($clang as $row) { ?>
				<li><span class="popup_line" onclick="InsertTagsMenu('<?php echo $id; ?>', '[code=<?php echo $row['short']; ?>]','[/code]', 'bbsourcecode_<?php echo $id; ?>')"><?php echo $row['name']; ?></span></li>
				<?php } ?>
			</ul>
			</div>
			<?php
			echo iif(count($cbb), '<img src="templates/editor/images/seperator.gif" alt="" />');
			foreach ($cbb as $bb) { ?>
			<img src="<?php echo $bb['buttonimage']; ?>" onclick="<?php echo $bb['href']; ?>" title="<?php echo $bb['title']; ?>" alt="<?php echo $bb['title']; ?>" class="editor_toolbar_button" onmouseover="buttonOver(this)" onmouseout="buttonOut(this)" />
			<?php } ?>
		</td></tr>
		<tr><td class="editor_toolbar">
			<img src="templates/editor/images/bold.gif" onclick="InsertTags('<?php echo $id; ?>', '[b]','[/b]');" title="<?php echo $lang->phrase('bbcodes_bold'); ?>" alt="<?php echo $lang->phrase('bbcodes_bold'); ?>" class="editor_toolbar_button" onmouseover="buttonOver(this)" onmouseout="buttonOut(this)" />
			<img src="templates/editor/images/italic.gif" onclick="InsertTags('<?php echo $id; ?>', '[i]','[/i]');" title="<?php echo $lang->phrase('bbcodes_italic'); ?>" alt="<?php echo $lang->phrase('bbcodes_italic'); ?>" class="editor_toolbar_button" onmouseover="buttonOver(this)" onmouseout="buttonOut(this)" />
			<img src="templates/editor/images/underline.gif" onclick="InsertTags('<?php echo $id; ?>', '[u]','[/u]');" title="<?php echo $lang->phrase('bbcodes_underline'); ?>" alt="<?php echo $lang->phrase('bbcodes_underline'); ?>" class="editor_toolbar_button" onmouseover="buttonOver(this)" onmouseout="buttonOut(this)" />
			<img src="templates/editor/images/seperator.gif" alt="" />
			<img src="templates/editor/images/left.gif" onclick="InsertTags('<?php echo $id; ?>','[align=left]','[/align]');" title="<?php echo $lang->phrase('bbcodes_align_left'); ?>" alt="<?php echo $lang->phrase('bbcodes_align_left'); ?>" class="editor_toolbar_button" onmouseover="buttonOver(this)" onmouseout="buttonOut(this)" />
			<img src="templates/editor/images/center.gif" onclick="InsertTags('<?php echo $id; ?>','[align=center]','[/align]');" title="<?php echo $lang->phrase('bbcodes_align_center'); ?>" alt="<?php echo $lang->phrase('bbcodes_align_center'); ?>" class="editor_toolbar_button" onmouseover="buttonOver(this)" onmouseout="buttonOut(this)" />
			<img src="templates/editor/images/right.gif" onclick="InsertTags('<?php echo $id; ?>','[align=right]','[/align]');" title="<?php echo $lang->phrase('bbcodes_align_right'); ?>" alt="<?php echo $lang->phrase('bbcodes_align_right'); ?>" class="editor_toolbar_button" onmouseover="buttonOver(this)" onmouseout="buttonOut(this)" />
			<img src="templates/editor/images/justify.gif" onclick="InsertTags('<?php echo $id; ?>','[align=justify]','[/align]');" title="<?php echo $lang->phrase('bbcodes_align_justify'); ?>" alt="<?php echo $lang->phrase('bbcodes_align_justify'); ?>" class="editor_toolbar_button" onmouseover="buttonOver(this)" onmouseout="buttonOut(this)" />
			<img src="templates/editor/images/seperator.gif" alt="" />
			<img src="templates/editor/images/img.gif" onclick="InsertTags('<?php echo $id; ?>', '[img]','[/img]');" title="<?php echo $lang->phrase('bbcodes_img'); ?>" alt="<?php echo $lang->phrase('bbcodes_img'); ?>" class="editor_toolbar_button" onmouseover="buttonOver(this)" onmouseout="buttonOut(this)" />
			<img src="templates/editor/images/url.gif" onclick="InsertTagsURL('<?php echo $id; ?>', '[url={param1}]{param2}','[/url]');" title="<?php echo $lang->phrase('bbcodes_url'); ?>" alt="<?php echo $lang->phrase('bbcodes_url'); ?>" class="editor_toolbar_button" onmouseover="buttonOver(this)" onmouseout="buttonOut(this)" />
			<img src="templates/editor/images/email.gif" onclick="InsertTags('<?php echo $id; ?>', '[email]','[/email]');" title="<?php echo $lang->phrase('bbcodes_email'); ?>" alt="<?php echo $lang->phrase('bbcodes_email'); ?>" class="editor_toolbar_button" onmouseover="buttonOver(this)" onmouseout="buttonOut(this)" />
			<img src="templates/editor/images/seperator.gif" alt="" />
			<img src="templates/editor/images/quote.gif" onclick="InsertTags('<?php echo $id; ?>', '[quote]','[/quote]');" title="<?php echo $lang->phrase('bbcodes_quote'); ?>" alt="<?php echo $lang->phrase('bbcodes_quote'); ?>" class="editor_toolbar_button" onmouseover="buttonOver(this)" onmouseout="buttonOut(this)" />
			<img src="templates/editor/images/ot.gif" onclick="InsertTags('<?php echo $id; ?>', '[ot]','[/ot]');" title="<?php echo $lang->phrase('bbcodes_ot'); ?>" alt="<?php echo $lang->phrase('bbcodes_ot'); ?>" class="editor_toolbar_button" onmouseover="buttonOver(this)" onmouseout="buttonOut(this)" />
			<img src="templates/editor/images/edit.gif" onclick="InsertTags('<?php echo $id; ?>', '[edit]','[/edit]');" title="<?php echo $lang->phrase('bbcodes_edit'); ?>" alt="<?php echo $lang->phrase('bbcodes_edit'); ?>" class="editor_toolbar_button" onmouseover="buttonOver(this)" onmouseout="buttonOut(this)" />
			<img src="templates/editor/images/seperator.gif" alt="" />
			<img src="templates/editor/images/list_unordered.gif" onclick="InsertTagsList('<?php echo $id; ?>');" title="<?php echo $lang->phrase('bbcodes_list'); ?>" alt="<?php echo $lang->phrase('bbcodes_list'); ?>" class="editor_toolbar_button" onmouseover="buttonOver(this)" onmouseout="buttonOut(this)" />
			<img src="templates/editor/images/list_ordered.gif" onclick="InsertTagsList('<?php echo $id; ?>', 'ol');" title="<?php echo $lang->phrase('bbcodes_list_ol'); ?>" alt="<?php echo $lang->phrase('bbcodes_list_ol'); ?>" class="editor_toolbar_button" onmouseover="buttonOver(this)" onmouseout="buttonOut(this)" />
			<img src="templates/editor/images/seperator.gif" alt="" />
			<img src="templates/editor/images/hr.gif" onclick="InsertTags('<?php echo $id; ?>', '[hr]','');" title="<?php echo $lang->phrase('bbcodes_hr'); ?>" alt="<?php echo $lang->phrase('bbcodes_hr'); ?>" class="editor_toolbar_button" onmouseover="buttonOver(this)" onmouseout="buttonOut(this)" />
			<img src="templates/editor/images/note.gif" onclick="InsertTagsNote('<?php echo $id; ?>', '[note={param1}]{param2}','[/note]');" title="<?php echo $lang->phrase('bbcodes_note'); ?>" alt="<?php echo $lang->phrase('bbcodes_note'); ?>" class="editor_toolbar_button" onmouseover="buttonOver(this)" onmouseout="buttonOut(this)" />
			<img src="templates/editor/images/tt.gif" onclick="InsertTags('<?php echo $id; ?>', '[tt]','[/tt]');" title="<?php echo $lang->phrase('bbcodes_tt'); ?>" alt="<?php echo $lang->phrase('bbcodes_tt'); ?>" class="editor_toolbar_button" onmouseover="buttonOver(this)" onmouseout="buttonOut(this)" />
			<img src="templates/editor/images/seperator.gif" alt="" />
			<img src="templates/editor/images/subscript.gif" onclick="InsertTags('<?php echo $id; ?>', '[sub]','[/sub]');" title="<?php echo $lang->phrase('bbcodes_sub'); ?>" alt="<?php echo $lang->phrase('bbcodes_sub'); ?>" class="editor_toolbar_button" onmouseover="buttonOver(this)" onmouseout="buttonOut(this)" />
			<img src="templates/editor/images/superscript.gif" onclick="InsertTags('<?php echo $id; ?>', '[sup]','[/sup]');" title="<?php echo $lang->phrase('bbcodes_sup'); ?>" alt="<?php echo $lang->phrase('bbcodes_sup'); ?>" class="editor_toolbar_button" onmouseover="buttonOver(this)" onmouseout="buttonOut(this)" />
		</td></tr>
		<tr><td class="editor_toolbar" style="height: auto; overflow: auto;">
			<?php foreach ($smileys[1] as $bb) { ?>
			<img src="<?php echo $bb['replace']; ?>" onclick="InsertTags('<?php echo $id; ?>', ' <?php echo $bb['jssearch'] ?> ', '');" title="<?php echo $bb['desc']; ?>" alt="<?php echo $bb['desc']; ?>" class="editor_toolbar_smiley" onmouseover="buttonOverSmiley(this)" onmouseout="buttonOutSmiley(this)" /></a>
			<?php } ?>
		<img src="templates/editor/images/seperator.gif" alt="" />
			<?php if (count($smileys[0]) > 0) { ?>
			<a id="menu_bbsmileys_<?php echo $id; ?>" href="#" onmouseover="RegisterMenu('bbsmileys_<?php echo $id; ?>');" class="editor_toolbar_dropdown"><img src="<?php echo $tpl->img('desc'); ?>" alt="<?php echo $lang->phrase('box_collapse'); ?>" /> <?php echo $lang->phrase('more_smileys'); ?></a>
			<div class="popup" id="popup_bbsmileys_<?php echo $id; ?>">
			<strong><?php echo $lang->phrase('more_smileys'); ?></strong>
			<ul class="bbsmileys">
			<?php foreach ($smileys[0] as $bb) { ?>
			  <li><span class="popup_line stext" onclick="InsertTagsMenu('<?php echo $id; ?>', ' <?php echo $bb['jssearch'] ?> ', '', 'bbsmileys_<?php echo $id; ?>')"><img src="<?php echo $bb['replace']; ?>" alt="<?php echo $bb['desc']; ?>" /> <?php echo $bb['desc']; ?></span></li>
			<?php }?>
			</ul>
			</div>
			<?php } ?>
		</td></tr>
		<tr><td class="editor_textarea_td">
			<textarea name="<?php echo $id; ?>" id="<?php echo $id; ?>" class="editor_textarea_inner"<?php echo $taAttr; ?>><?php echo $content; ?></textarea>
		</td></tr>
		<tr><td class="editor_statusbar" style="text-align: right;">
			<a href="javascript:resize_textarea('<?php echo $id; ?>', 1);"><?php echo $lang->phrase('textarea_increase_size'); ?></a> &middot;
			<a href="javascript:resize_textarea('<?php echo $id; ?>', -1);"><?php echo $lang->phrase('textarea_decrease_size'); ?></a>
		</td></tr>
	</table>
	<?php
}
function parseNavPosSetting() {
	global $admconfig;
	$explode = preg_split("~(\r\n|\r|\n)+~", trim($admconfig['nav_positions']));
	$arr = array();
	foreach ($explode as $val) {
		$dat = explode('=', $val, 2);
		$arr[$dat[0]] = $dat[1];
	}
	return $arr;
}
function attachWYSIWYG() {
	$r = '<link rel="stylesheet" type="text/css" href="admin/html/wysiwyg.css" />';
	$r .= '<script type="text/javascript" src="templates/editor/wysiwyg.js"></script>';
	$r .= '<script type="text/javascript"> WYSIWYG.attach("all", full); </script>';
	return $r;
}
function getNavTitle() {
	global $gpc, $db;
	$title = $gpc->get('title', none);
	$title = trim($title);
	$parts = explode('->', $title);
	if (!empty($parts[0])) {
		$parts[0] = strtolower($parts[0]);
		if ($parts[0] == 'doc' || $parts[0] == 'lang') {
			$title = $db->escape_string($title);
		}
		else {
			$title = $gpc->save_str($title);
		}
		return $title;
	}
	else {
		return '';
	}
}

define('EDITOR_IMAGEDIR', './uploads/images/');
$supportedextentions = array('gif','png','jpeg','jpg');

($code = $plugins->load('admin_cms_jobs')) ? eval($code) : null;

if ($job == 'nav') {
	send_nocache_header();
	echo head();
?>
 <table class="border" border="0" cellspacing="0" cellpadding="4" align="center">
  <tr>
   <td class="obox"><?php echo $lang->phrase('admin_cms_head_manage_navigation'); ?></td>
  </tr>
  <tr>
   <td class="mbox center">
   	<a class="button" href="admin.php?action=cms&amp;job=nav_add"><?php echo $lang->phrase('admin_cms_manage_navigation_add_link'); ?></a>
   	<a class="button" href="admin.php?action=cms&amp;job=nav_addbox"><?php echo $lang->phrase('admin_cms_manage_navigation_add_box'); ?></a>
   	<a class="button" href="admin.php?action=cms&amp;job=nav_addplugin"><?php echo $lang->phrase('admin_cms_manage_navifation_add_plugin'); ?></a>
   </td>
  </tr>
 </table>
 <br />
<?php
	$result = $db->query("SELECT * FROM {$db->pre}menu ORDER BY position, ordering, id", __LINE__, __FILE__);
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
	$pos = parseNavPosSetting();
	$last = null;
	foreach ($cat as $head) {
		if ($head['position'] != $last) {
			if ($last != null) {
				echo '</table><br class="minibr" />';
			}
			?>
		 <table class="border" border="0" cellspacing="0" cellpadding="4" align="center">
		  <tr>
		   <td class="obox" colspan="4"><?php echo $lang->phrase('admin_cms_position'); ?> <?php echo $pos[$head['position']]; ?></td>
		  </tr>
		  <tr>
		   <td class="ubox"><?php echo $lang->phrase('admin_cms_link'); ?></td>
		   <td class="ubox"><?php echo $lang->phrase('admin_cms_status'); ?></td>
		   <td class="ubox"><?php echo $lang->phrase('admin_cms_order'); ?></td>
		   <td class="ubox"><?php echo $lang->phrase('admin_cms_action'); ?></td>
		  </tr>
			<?php
			$last = $head['position'];
		}
		$type = array();
		if ($head['module'] > 0) {
			$type[] = $lang->phrase('admin_cms_plugin');
		}
		if ($head['active'] == 0) {
			$type[] = $lang->phrase('admin_cms_inactive');
		}
	?>
	<tr class="mmbox">
	<td width="50%">
	<?php echo $plugins->navLang($head['name'], true); ?><?php echo iif(count($type) > 0, ' ['.implode('; ', $type).']' ); ?>
	</td>
	<td width="10%">
	<?php
	if ($head['active'] == 1) {
		echo '<a href="admin.php?action=cms&job=nav_active&id='.$head['id'].iif($head['module'] > 0, '&plug='.$head['module']).'&act=0">'.$lang->phrase('admin_cms_deactivate').'</a>';
	}
	else {
		echo '<a href="admin.php?action=cms&job=nav_active&id='.$head['id'].iif($head['module'] > 0, '&plug='.$head['module']).'&act=1">'.$lang->phrase('admin_cms_activate').'</a>';
	}
	?>
	</td>
	<td width="15%"><?php echo $head['ordering']; ?>&nbsp;&nbsp;
	<a href="admin.php?action=cms&job=nav_move&id=<?php echo $head['id']; ?>&value=-1"><img src="admin/html/images/asc.gif" border="0" alt="Up"></a>&nbsp;
	<a href="admin.php?action=cms&job=nav_move&id=<?php echo $head['id']; ?>&value=1"><img src="admin/html/images/desc.gif" border="0" alt="Down"></a>
	</td>
	<td width="35%">
	 <a class="button" href="admin.php?action=cms&job=nav_edit&id=<?php echo $head['id']; ?>"><?php echo $lang->phrase('admin_cms_edit'); ?></a>
	 <a class="button" href="admin.php?action=cms&job=nav_delete&id=<?php echo $head['id']; ?>"><?php echo $lang->phrase('admin_cms_delete'); ?></a>
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
				echo $plugins->navLang($link['name'], true);
			}
			else {
				?>
				<a href="<?php echo $link['link']; ?>" target="<?php echo $link['param']; ?>"><?php echo $plugins->navLang($link['name'], true); ?></a>
				<?php } echo iif ($link['active'] == '0', ' ['.$lang->phrase('admin_cms_inactive').']'); ?><br />
				</td>
				<td class="mbox" width="10%">
				<?php
				if ($link['active'] == 1) {
					echo '<a href="admin.php?action=cms&job=nav_active&id='.$link['id'].'&act=0">'.$lang->phrase('admin_cms_deactivate').'</a>';
				}
				else {
					echo '<a href="admin.php?action=cms&job=nav_active&id='.$link['id'].'&act=1">'.$lang->phrase('admin_cms_activate').'</a>';
				}
				?>
				</td>
				<td class="mbox" width="15%" nowrap="nowrap" align="center"><?php echo $link['ordering']; ?>&nbsp;&nbsp;
				<a href="admin.php?action=cms&job=nav_move&id=<?php echo $link['id']; ?>&value=-1"><img src="admin/html/images/asc.gif" border="0" alt="<?php echo $lang->phrase('admin_cms_move_up'); ?>"></a>&nbsp;
				<a href="admin.php?action=cms&job=nav_move&id=<?php echo $link['id']; ?>&value=1"><img src="admin/html/images/desc.gif" border="0" alt="<?php echo $lang->phrase('admin_cms_move_down'); ?>"></a>
				</font></td>
				<td class="mbox" width="25%">
				 <a class="button" href="admin.php?action=cms&job=nav_edit&id=<?php echo $link['id'] ?>"><?php echo $lang->phrase('admin_cms_edit'); ?></a>
				 <a class="button" href="admin.php?action=cms&job=nav_delete&id=<?php echo $link['id']; ?>"><?php echo $lang->phrase('admin_cms_delete'); ?></a>
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
							echo $plugins->navLang($sublink['name'], true);
						}
						else {
							?>
							<a href='<?php echo $sublink['link']; ?>' target='<?php echo $sublink['param']; ?>'><?php echo $plugins->navLang($sublink['name'], true); ?></a>
							<?php } echo iif ($sublink['active'] == '0', ' ['.$lang->phrase('admin_cms_inactive').']'); ?></font><br>
							</td>
							<td class="mbox" width="10%">
							<?php
							if ($sublink['active'] == 1) {
								echo '<a href="admin.php?action=cms&job=nav_active&id='.$sublink['id'].'&act=0">'.$lang->phrase('admin_cms_deactivate').'</a>';
							}
							else {
								echo '<a href="admin.php?action=cms&job=nav_active&id='.$sublink['id'].'&act=1">'.$lang->phrase('admin_cms_activate').'</a>';
							}
							?>
							</td>
							<td class="mbox" width="15%" nowrap="nowrap" align="right"><?php echo $sublink['ordering']; ?>&nbsp;&nbsp;
							<a href="admin.php?action=cms&job=nav_move&id=<?php echo $sublink['id']; ?>&value=-1"><img src="admin/html/images/asc.gif" border="0" alt="<?php echo $lang->phrase('admin_cms_move_up'); ?>"></a>&nbsp;
							<a href="admin.php?action=cms&job=nav_move&id=<?php echo $sublink['id']; ?>&value=1"><img src="admin/html/images/desc.gif" border="0" alt="<?php echo $lang->phrase('admin_cms_move_down'); ?>"></a>
							</td>
							<td class="mbox" width="25%">
							 <a class="button" href="admin.php?action=cms&job=nav_edit&id=<?php echo $sublink['id']; ?>"><?php echo $lang->phrase('admin_cms_edit'); ?></a>
							 <a class="button" href="admin.php?action=cms&job=nav_delete&id=<?php echo $sublink['id']; ?>"><?php echo $lang->phrase('admin_cms_delete'); ?></a>
							</td>
							</tr>
							<?php
						}
					}
			}
		}
	}
	echo '</table>';
	echo foot();
}
elseif ($job == 'nav_edit') {
	echo head();
	$id = $gpc->get('id', int);
	$result = $db->query("SELECT * FROM {$db->pre}menu WHERE id = '{$id}' LIMIT 1", __LINE__, __FILE__);
	$data = $db->fetch_assoc($result);
	$data['group_array'] = explode(',', $data['groups']);
	$pos = parseNavPosSetting();

	$groups = $db->query("SELECT id, name FROM {$db->pre}groups", __LINE__, __FILE__);

	if ($data['sub'] > 0) {
		$result = $db->query("SELECT id, name, sub, position FROM {$db->pre}menu WHERE module = '0' ORDER BY position, ordering, id", __LINE__, __FILE__);
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

	$last = null;
	?>
<form name="form" method="post" action="admin.php?action=cms&job=nav_edit2&id=<?php echo $id; ?>">
 <table class="border" border="0" cellspacing="0" cellpadding="4" align="center">
  <tr>
   <td class="obox" colspan="2"><?php echo $lang->phrase('admin_cms_nav_edit'); ?> <?php echo iif ($data['sub'] > 0, $lang->phrase('admin_cms_link'), $lang->phrase('admin_cms_box')); ?></td>
  </tr>
  <tr>
   <td class="mbox" width="50%"><?php echo $lang->phrase('admin_cms_nav_title'); ?><br /><span class="stext"><?php echo $lang->phrase('admin_cms_nav_title_text'); ?></span></td>
   <td class="mbox" width="50%"><input type="text" name="title" size="40" value="<?php echo $data['name']; ?>" /></td>
  </tr>
<?php if ($data['sub'] > 0) { ?>
  <tr>
   <td class="mbox" width="50%"><?php echo $lang->phrase('admin_cms_nav_file_url'); ?><br />
   <span class="stext">
   - <a href="javascript:docs();"><?php echo $lang->phrase('admin_cms_nav_existing_documents'); ?></a><br />
   - <a href="javascript:coms();"><?php echo $lang->phrase('admin_cms_nav_existing_components'); ?></a>
   </span></td>
   <td class="mbox" width="50%"><input type="text" name="url" size="40" value="<?php echo $data['link']; ?>" /></td>
  </tr>
  <tr>
   <td class="mbox" width="50%"><?php echo $lang->phrase('admin_cms_nav_target'); ?><br /><span class="stext"><?php echo $lang->phrase('admin_cms_nav_target_text'); ?></span></td>
   <td class="mbox" width="50%"><input type="text" name="target" size="40" value="<?php echo $data['param']; ?>" /></td>
  </tr>
  <tr>
   <td class="mbox" width="50%"><?php echo $lang->phrase('admin_cms_nav_parent_box'); ?></td>
   <td class="mbox" width="50%">
   <select name="sub">
   	<?php
	foreach ($cache[0] as $row) {
	   	if ($last != $row['position']) {
	   		if ($last != null) {
				echo '</optgroup>';
	   		}
	   		$last = $row['position'];
	   		echo '<optgroup label="'.htmlspecialchars($pos[$last], ENT_QUOTES).'">';
	   	}
   		$select = iif($row['id'] == $data['sub'], ' selected="selected"');
   		echo '<option style="font-weight: bold;" value="'.$row['id'].'"'.$select.'>'.$plugins->navLang($row['name'], true).'</option>';
   		if (isset($cache[$row['id']])) {
   			foreach ($cache[$row['id']] as $row) {
   				$select = iif($row['id'] == $data['sub'], ' selected="selected"');
   				echo '<option value="'.$row['id'].'"'.$select.'>+&nbsp;'.$plugins->navLang($row['name'], true).'</option>';
   			}
   		}
	}
	?>
	</optgroup>
   </select>
   </td>
  </tr>
<?php } if ($data['module'] > 0) { ?>
  <tr>
   <td class="mbox" width="50%"><?php echo $lang->phrase('admin_cms_nav_plugin'); ?></td>
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
   <td class="mbox" width="50%"><?php echo $lang->phrase('admin_cms_nav_groups'); ?><br /><span class="stext"><?php echo $lang->phrase('admin_cms_nav_groups_text'); ?></span></td>
   <td class="mbox" width="50%">
   <?php while ($row = $db->fetch_assoc($groups)) { ?>
	<input type="checkbox" name="groups[]"<?php echo iif($data['groups'] == 0 || in_array($row['id'], $data['group_array']), ' checked="checked"'); ?> value="<?php echo $row['id']; ?>"> <?php echo $row['name']; ?><br />
   <?php } ?>
   </td>
  </tr>
  <tr>
   <td class="mbox" width="50%"><?php echo $lang->phrase('admin_cms_nav_active'); ?></td>
   <td class="mbox" width="50%"><input type="checkbox" name="active" value="1"<?php echo iif($data['active'] == 1, ' checked="checked"'); ?> /></td>
  </tr>
  <tr>
   <td class="ubox" colspan="2" align="center"><input type="submit" value="<?php echo $lang->phrase('admin_cms_form_edit'); ?>" /></td>
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

	$title = getNavTitle();
	if (empty($title)) {
		error('admin.php?action=cms&job=nav_addbox', $lang->phrase('admin_cms_err_no_title'));
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
		$result = $db->query("SELECT position FROM {$db->pre}menu WHERE id = '{$sub}'");
		$pos = $gpc->save_str($db->fetch_assoc($result));
		$db->query("UPDATE {$db->pre}menu SET name = '{$title}', link = '{$url}', param = '{$target}', groups = '{$groups}', sub = '{$sub}', active = '{$active}', position = '{$pos['position']}' WHERE id = '{$id}' LIMIT 1", __LINE__, __FILE__);
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
	ok('admin.php?action=cms&job=nav', $lang->phrase('admin_cms_data_successfully_changed'));
}
elseif ($job == 'nav_delete') {
	echo head();
	$id = $gpc->get('id', int);
?>
	<table class="border" border="0" cellspacing="0" cellpadding="4" align="center">
	<tr><td class="obox"><?php echo $lang->phrase('admin_cms_nav_delete_box_or_link'); ?></td></tr>
	<tr><td class="mbox">
	<p align="center"><?php echo $lang->phrase('admin_cms_nav_really_want_to_delete'); ?></p>
	<p align="center">
	<a href="admin.php?action=cms&job=nav_delete2&id=<?php echo $id; ?>"><img border="0" alt="" src="admin/html/images/yes.gif"> <?php echo $lang->phrase('admin_cms_yes'); ?></a>
	&nbsp&nbsp;&nbsp;&nbsp&nbsp;&nbsp;
	<a href="javascript: history.back(-1);"><img border="0" alt="" src="admin/html/images/no.gif"> <?php echo $lang->phrase('admin_cms_no'); ?></a>
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

	ok('admin.php?action=cms&job=nav', $lang->phrase('admin_cms_entries_deleted'));
}
elseif ($job == 'nav_move') {
	$id = $gpc->get('id', int);
	$pos = $gpc->get('value', int);
	if ($id < 1) {
		error('admin.php?action=cms&job=nav', $lang->phrase('admin_cms_invalid_id_given'));
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
		error('admin.php?action=cms&job=nav', $lang->phrase('admin_cms_invalid_id_given'));
	}
	if ($pos != 0 && $pos != 1) {
		error('admin.php?action=cms&job=nav', $lang->phrase('admin_cms_invalid_status_specified'));
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
	$sort = $db->query("SELECT id, name, position FROM {$db->pre}menu WHERE sub = '0' ORDER BY position, ordering, id", __LINE__, __FILE__);
	$plugs = $db->query("SELECT id, name FROM {$db->pre}plugins WHERE position = 'navigation' ORDER BY ordering", __LINE__, __FILE__);
	$groups = $db->query("SELECT id, name FROM {$db->pre}groups", __LINE__, __FILE__);
	$pos = parseNavPosSetting();
	?>
<form name="form" method="post" action="admin.php?action=cms&job=nav_addplugin2">
 <table class="border" border="0" cellspacing="0" cellpadding="4" align="center">
  <tr>
   <td class="obox" colspan="2"><?php echo $lang->phrase('admin_cms_nav_add_plugin'); ?></td>
  </tr>
  <tr>
   <td class="mbox" width="50%"><?php echo $lang->phrase('admin_cms_nav_title'); ?><br /><span class="stext"><?php echo $lang->phrase('admin_cms_nav_plug_title_text'); ?></span></td>
   <td class="mbox" width="50%"><input type="text" name="title" size="40" /></td>
  </tr>
  <tr>
   <td class="mbox" width="50%"><?php echo $lang->phrase('admin_cms_nav_plugin'); ?></td>
   <td class="mbox" width="50%">
   <select name="plugin">
   <?php while ($row = $db->fetch_assoc($plugs)) { ?>
   <option value="<?php echo $row['id']; ?>"<?php echo iif($row['id'] == $id, ' selected="selected"'); ?>><?php echo $row['name']; ?></option>
   <?php } ?>
   </select>
   </td>
  </tr>
  <tr>
   <td class="mbox" width="50%"><?php echo $lang->phrase('admin_cms_nav_sort_in_after'); ?></td>
   <td class="mbox" width="50%">
   <select name="sort">
   	<?php
   	$last = null;
   	while ($row = $db->fetch_assoc($sort)) {
	   	if ($last != $row['position']) {
	   		if ($last != null) {
				echo '</optgroup>';
	   		}
	   		$last = $row['position'];
	   		if (!isset($pos[$last])) {
	   			$pos[$last] = $row['position'];
	   		}
		   	echo '<optgroup label="'.htmlspecialchars($pos[$last], ENT_QUOTES).'">';
		   	unset($pos[$last]);
	   	}
   		echo '<option value="'.$row['id'].'">'.$plugins->navLang($row['name'], true).'</option>';
	}
	foreach ($pos as $key => $name) {
		?>
		</optgroup>
		<optgroup label="<?php echo htmlspecialchars($name, ENT_QUOTES); ?>">
		<option value="pos_<?php echo $key; ?>">&lt;<?php echo $lang->phrase('admin_cms_sort_in_here'); ?>&gt;</option>
		<?php
	}
	?>
	</optgroup>
   </select>
   </select>
   </td>
  </tr>
  <tr>
   <td class="mbox" width="50%"><?php echo $lang->phrase('admin_cms_nav_groups'); ?><br /><span class="stext"><?php echo $lang->phrase('admin_cms_nav_plug_groups_text'); ?></span></td>
   <td class="mbox" width="50%">
   <?php while ($row = $db->fetch_assoc($groups)) { ?>
	<input type="checkbox" name="groups[]" checked="checked" value="<?php echo $row['id']; ?>"> <?php echo $row['name']; ?><br />
   <?php } ?>
   </td>
  </tr>
  <tr>
   <td class="ubox" colspan="2" align="center"><input type="submit" value="<?php echo $lang->phrase('admin_cms_form_add'); ?>" /></td>
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
	$title = getNavTitle();
	if (empty($title)) {
		$title = $data['name'];
	}
	$sort = $gpc->get('sort', str);
	if (substr($sort, 0, 4) == 'pos_') {
		$sort = array(
			'ordering' => 0,
			'position' => substr($sort, 4)
		);
	}
	else {
		$result = $db->query("SELECT ordering, position FROM {$db->pre}menu WHERE id = '{$sort}'");
		$sort = $db->fetch_assoc($result);
	}
	$groups = $gpc->get('groups', arr_int);
	$result = $db->query('SELECT COUNT(*) FROM '.$db->pre.'groups', __LINE__, __FILE__);
	$count = $db->fetch_num($result);
	if (count($groups) == $count[0]) {
		$groups = 0;
	}
	else {
		$groups = implode(',', $groups);
	}
	$db->query("INSERT INTO {$db->pre}menu (name, groups, ordering, active, module, position) VALUES ('{$title}','{$groups}','{$sort['ordering']}','{$data['active']}','{$data['id']}','{$sort['position']}')", __LINE__, __FILE__);
	$delobj = $scache->load('modules_navigation');
	$delobj->delete();
	ok('admin.php?action=cms&job=nav', $lang->phrase('admin_cms_plugins_successfully_added'));
}
elseif ($job == 'nav_add') {
	echo head();
	$groups = $db->query("SELECT id, name FROM {$db->pre}groups", __LINE__, __FILE__);
	$result = $db->query("SELECT id, name, sub, position FROM {$db->pre}menu WHERE module = '0' ORDER BY position, ordering, id", __LINE__, __FILE__);
	$cache = array(0 => array());
	while ($row = $db->fetch_assoc($result)) {
		if (!isset($cache[$row['sub']]) || !is_array($cache[$row['sub']])) {
			$cache[$row['sub']] = array();
		}
		$cache[$row['sub']][] = $row;
	}
	$pos = parseNavPosSetting();
	?>
<form name="form" method="post" action="admin.php?action=cms&job=nav_add2">
 <table class="border" border="0" cellspacing="0" cellpadding="4" align="center">
  <tr>
   <td class="obox" colspan="2"><?php echo $lang->phrase('admin_cms_add_new_link'); ?></td>
  </tr>
  <tr>
   <td class="mbox" width="50%"><?php echo $lang->phrase('admin_cms_nav_title'); ?><br /><span class="stext"><?php echo $lang->phrase('admin_cms_nav_title_text'); ?></span></td>
   <td class="mbox" width="50%"><input type="text" name="title" size="40" /></td>
  </tr>
  <tr>
   <td class="mbox" width="50%"><?php echo $lang->phrase('admin_cms_nav_file_url'); ?><br />
   <span class="stext">
   - <a href="javascript:docs();"><?php echo $lang->phrase('admin_cms_nav_existing_documents'); ?></a><br />
   - <a href="javascript:coms();"><?php echo $lang->phrase('admin_cms_nav_existing_components'); ?></a>
   </span></td>
   <td class="mbox" width="50%"><input type="text" name="url" size="40" /></td>
  </tr>
  <tr>
   <td class="mbox" width="50%"><?php echo $lang->phrase('admin_cms_nav_target'); ?><br /><span class="stext"><?php echo $lang->phrase('admin_cms_nav_target_text'); ?></span></td>
   <td class="mbox" width="50%"><input type="text" name="target" size="40" /></td>
  </tr>
  <tr>
   <td class="mbox" width="50%"><?php echo $lang->phrase('admin_cms_nav_parent_box'); ?></td>
   <td class="mbox" width="50%">
   <select name="sub">
   	<?php
   	$last = null;
   	foreach ($cache[0] as $row) {
	   	if ($last != $row['position']) {
	   		if ($last != null) {
				echo '</optgroup>';
	   		}
	   		$last = $row['position'];
	   		echo '<optgroup label="'.htmlspecialchars($pos[$last], ENT_QUOTES).'">';
	   	}
   		echo '<option style="font-weight: bold;" value="'.$row['id'].'">'.$plugins->navLang($row['name'], true).'</option>';
   		if (isset($cache[$row['id']])) {
   			foreach ($cache[$row['id']] as $row) {
   				echo '<option value="'.$row['id'].'">+&nbsp;'.$plugins->navLang($row['name'], true).'</option>';
   			}
   		}
	}
	?>
	</optgroup>
   </select>
   </td>
  </tr>
  <tr>
   <td class="mbox" width="50%"><?php echo $lang->phrase('admin_cms_nav_sort_in'); ?></td>
   <td class="mbox" width="50%">
   <select name="sort">
	<option value="0"><?php echo $lang->phrase('admin_cms_nav_at_the_beginning'); ?></option>
	<option value="1"><?php echo $lang->phrase('admin_cms_nav_at_the_end'); ?></option>
   </select>
   </td>
  </tr>
  <tr>
   <td class="mbox" width="50%"><?php echo $lang->phrase('admin_cms_nav_groups'); ?><br /><span class="stext"><?php echo $lang->phrase('admin_cms_nav_groups_text'); ?></span></td>
   <td class="mbox" width="50%">
   <?php while ($row = $db->fetch_assoc($groups)) { ?>
	<input type="checkbox" name="groups[]" checked="checked" value="<?php echo $row['id']; ?>"> <?php echo $row['name']; ?><br />
   <?php } ?>
   </td>
  </tr>
  <tr>
   <td class="ubox" colspan="2" align="center"><input type="submit" value="<?php echo $lang->phrase('admin_cms_form_add'); ?>" /></td>
  </tr>
 </table>
</form>
	<?php
	echo foot();
}
elseif ($job == 'nav_add2') {
	echo head();
	$title = getNavTitle();
	$target = $gpc->get('target', str);
	$url = $gpc->get('url', str);
	$sub = $gpc->get('sub', int);
	$sort = $gpc->get('sort', int);
	$groups = $gpc->get('groups', arr_int);
	if (empty($title)) {
		error('admin.php?action=cms&job=nav_addbox', $lang->phrase('admin_cms_err_no_title'));
	}

	$pos = $db->fetch_num($db->query("SELECT position FROM {$db->pre}menu WHERE id = '{$sub}' LIMIT 1", __LINE__, __FILE__));
	if (empty($pos[0])) {
		$pos = array('left');
	}

	if ($sort == 1) {
		$sort = $db->fetch_num($db->query("SELECT MAX(ordering) FROM {$db->pre}menu WHERE sub = '{$sub}' LIMIT 1", __LINE__, __FILE__));
		$sort = $sort[0]+1;
	}
	elseif ($sort == 0) {
		$sort = $db->fetch_num($db->query("SELECT MIN(ordering) FROM {$db->pre}menu WHERE sub = '{$sub}' LIMIT 1", __LINE__, __FILE__));
		$sort = $sort[0]-1;
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
	$db->query("INSERT INTO {$db->pre}menu (name, groups, ordering, link, param, sub, position) VALUES ('{$title}','{$groups}','{$sort}','{$url}','{$target}','{$sub}','{$pos[0]}')", __LINE__, __FILE__);
	$delobj = $scache->load('modules_navigation');
	$delobj->delete();
	ok('admin.php?action=cms&job=nav', $lang->phrase('admin_cms_link_successfully_added'));
}
elseif ($job == 'nav_addbox') {
	echo head();
	$sort = $db->query("SELECT id, name, position FROM {$db->pre}menu WHERE sub = '0' ORDER BY position, ordering, id", __LINE__, __FILE__);
	$groups = $db->query("SELECT id, name FROM {$db->pre}groups", __LINE__, __FILE__);
	$pos = parseNavPosSetting();
	?>
<form name="form" method="post" action="admin.php?action=cms&job=nav_addbox2">
 <table class="border" border="0" cellspacing="0" cellpadding="4" align="center">
  <tr>
   <td class="obox" colspan="2"><?php echo $lang->phrase('admin_cms_create_a_new_box'); ?></td>
  </tr>
  <tr>
   <td class="mbox" width="50%"><?php echo $lang->phrase('admin_cms_nav_title'); ?><br /><span class="stext"><?php echo $lang->phrase('admin_cms_nav_title_text'); ?></span></td>
   <td class="mbox" width="50%"><input type="text" name="title" size="40" /></td>
  </tr>
  <tr>
   <td class="mbox" width="50%"><?php echo $lang->phrase('admin_cms_nav_sort_in_after'); ?></td>
   <td class="mbox" width="50%">
   <select name="sort">
   	<?php
   	$last = null;
	while ($row = $db->fetch_assoc($sort)) {
	   	if ($last != $row['position']) {
	   		if ($last != null) {
				echo '</optgroup>';
	   		}
	   		$last = $row['position'];
	   		echo '<optgroup label="'.htmlspecialchars($pos[$last], ENT_QUOTES).'">';
	   		unset($pos[$last]);
	   	}
   		echo '<option value="'.$row['id'].'">'.$plugins->navLang($row['name'], true).'</option>';
	}
	foreach ($pos as $key => $name) {
		?>
		</optgroup>
		<optgroup label="<?php echo htmlspecialchars($name, ENT_QUOTES); ?>">
		<option value="pos_<?php echo $key; ?>">&lt;<?php echo $lang->phrase('admin_cms_nav_sort_in_here'); ?>&gt;</option>
		<?php
	}
	?>
	</optgroup>
   </select>
   </td>
  </tr>
  <tr>
   <td class="mbox" width="50%"><?php echo $lang->phrase('admin_cms_nav_groups'); ?><br /><span class="stext"><?php echo $lang->phrase('admin_cms_nav_groups_text'); ?></span></td>
   <td class="mbox" width="50%">
   <?php while ($row = $db->fetch_assoc($groups)) { ?>
	<input type="checkbox" name="groups[]" checked="checked" value="<?php echo $row['id']; ?>"> <?php echo $row['name']; ?><br />
   <?php } ?>
   </td>
  </tr>
  <tr>
   <td class="ubox" colspan="2" align="center"><input type="submit" value="<?php echo $lang->phrase('admin_cms_form_add'); ?>" /></td>
  </tr>
 </table>
</form>
	<?php
	echo foot();
}
elseif ($job == 'nav_addbox2') {
	echo head();
	$title = getNavTitle();
	if (empty($title)) {
		error('admin.php?action=cms&job=nav_addbox', $lang->phrase('admin_cms_err_no_title'));
	}
	$sort = $gpc->get('sort', str);
	if (substr($sort, 0, 4) == 'pos_') {
		$sort = array(
			'ordering' => 0,
			'position' => substr($sort, 4)
		);
	}
	else {
		$sort = $gpc->save_int($sort);
		$result = $db->query("SELECT ordering, position FROM {$db->pre}menu WHERE id = '{$sort}'");
		$sort = $db->fetch_assoc($result);
	}
	$groups = $gpc->get('groups', arr_int);
	$result = $db->query('SELECT COUNT(*) FROM '.$db->pre.'groups', __LINE__, __FILE__);
	$count = $db->fetch_num($result);
	if (count($groups) == $count[0]) {
		$groups = 0;
	}
	else {
		$groups = implode(',', $groups);
	}
	$db->query("INSERT INTO {$db->pre}menu (name, groups, ordering, position) VALUES ('{$title}','{$groups}','{$sort['ordering']}','{$sort['position']}')", __LINE__, __FILE__);
	$delobj = $scache->load('modules_navigation');
	$delobj->delete();
	ok('admin.php?action=cms&job=nav', $lang->phrase('admin_cms_box_successfully_added'));
}
elseif ($job == 'nav_docslist') {
	echo head();
	$wrap_obj = $scache->load('wraps');
	$wraps = $wrap_obj->get();
	?>
	 <table class="border" border="0" cellspacing="0" cellpadding="4" align="center">
	  <tr>
	   <td class="obox"><?php echo $lang->phrase('admin_cms_existing_documents_and_pages'); ?></td>
	  </tr>
	  <tr>
	   <td class="mbox">
	   <?php foreach ($wraps as $id => $data) { ksort($data['titles']); ?>
	   <input type="radio" name="data" onclick="insert_doc('docs.php?id=<?php echo $id; ?>','doc-><?php echo $id; ?>')"> <?php echo implode(' / ', $data['titles']); ?><br>
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
	   <td class="obox"><?php echo $lang->phrase('admin_cms_existing_documents'); ?></td>
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
elseif ($job == 'doc_select_image') {
	/********************************************************************
	 * openImageLibrary addon Copyright (c) 2006 openWebWare.com
	 * Contact us at devs@openwebware.com
	 * This copyright notice MUST stay intact for use.
	 ********************************************************************/
	$leadon = realpath(EDITOR_IMAGEDIR).DIRECTORY_SEPARATOR;
	$leadon = str_replace('\\', '/', $leadon);
	$dir = $gpc->get('dir', none);
	$dotdotdir = false;
	$dirok = false;
	if(!empty($dir)) {
		if ($dir == '..') {
			$leadon = extract_dir($leadon, true).DIRECTORY_SEPARATOR;
			$leadon = str_replace('\\', '/', $leadon);
			$dir = '';
		}
		else {
			$leadon .= $dir.DIRECTORY_SEPARATOR;
			$dotdotdir = true;
		}
	}

	if(!file_exists($leadon)) {
		$leadon = realpath(EDITOR_IMAGEDIR).DIRECTORY_SEPARATOR;
		$leadon = str_replace('\\', '/', $leadon);
	}

	$sort = $gpc->get('sort', none);

	clearstatcache();
	$n = 0;
	if ($handle = opendir($leadon)) {
		while (false !== ($file = readdir($handle))) {
			//first see if this file is required in the listing
			if ($file == "." || $file == "..")  continue;
			if (@filetype($leadon.$file) == "dir") {

				$n++;
				if($sort=="date") {
					$key = @filemtime($leadon.$file) . ".$n";
				}
				else {
					$key = $n;
				}
				$dirs[$key] = $file . "/";
			}
			else {
				$n++;
				if($sort=="date") {
					$key = @filemtime($leadon.$file) . ".$n";
				}
				elseif($sort=="size") {
					$key = @filesize($leadon.$file) . ".$n";
				}
				else {
					$key = $n;
				}
				$files[$key] = $file;
			}
		}
		closedir($handle);
	}

	if($sort=="date") {
		@ksort($dirs, SORT_NUMERIC);
		@ksort($files, SORT_NUMERIC);
	}
	elseif($sort=="size") {
		@natcasesort($dirs);
		@ksort($files, SORT_NUMERIC);
	}
	else {
		@natcasesort($dirs);
		@natcasesort($files);
	}

	$order = $gpc->get('order', none);

	if($order=="desc" && $sort!="size") {$dirs = @array_reverse($dirs);}
	if($order=="desc") {$files = @array_reverse($files);}
	$dirs = @array_values($dirs); $files = @array_values($files);

	$fileicons_obj = $scache->load('fileicons');
	$fileicons = $fileicons_obj->get();

	echo head('style="background-color: #ffffff;"');
	?>
	<script type="text/javascript">
		function selectImage(url) {
			if(parent) {
				parent.document.getElementById("src").value = url;
			}
		}

		if(parent) {
			parent.document.getElementById("dir").value = '<?php echo iif($dotdotdir, $dir); ?>';
		}

	</script>
	<table class="border" border="0" cellspacing="0" cellpadding="4" align="center" style="width: 230px;">
		<tr>
			<td>
			  <?php
				if($dotdotdir) {
					?>
					<a href="admin.php?action=cms&job=doc_select_image&dir=<?php echo extract_dir($dir); ?>"><img src="<?php echo $tpl->img('filetypes/folder'); ?>" alt="" border="0" />&nbsp;<em>Previous Directory</em></a><br>
					<?php
				}
				$arsize = count($dirs);
				for($i=0;$i<$arsize;$i++) {
					$dir = substr($dirs[$i], 0, strlen($dirs[$i]) - 1);
					?>
					<a href="admin.php?action=cms&job=doc_select_image&dir=<?php echo urlencode($dirs[$i]); ?>"><img src="<?php echo $tpl->img('filetypes/folder'); ?>" alt="" border="0" />&nbsp;<?php echo $dir; ?></a><br>
					<?php
				}
				if ($arsize > 0 || $dotdotdir) {
					echo "</td></tr><tr><td>";
				}
				$arsize = count($files);
				for($i=0;$i<$arsize;$i++) {
					$ext = strtolower(substr($files[$i], strrpos($files[$i], '.')+1));
					if(in_array($ext, $supportedextentions)) {
						$filename = $files[$i];
						if (!isset($fileicons[$ext])) {
							$icon = 'unknown';
						}
						else {
							$icon = $fileicons[$ext];
						}
					?>
					<a href="javascript:void(0)" onclick="selectImage('<?php echo EDITOR_IMAGEDIR.$filename; ?>');">
					<img src="<?php echo $tpl->img('filetypes/'.$icon); ?>" alt="" border="0" />&nbsp;<?php echo $filename; ?>
					</a><br>
					<?php
					}
				}
				?>
			</td>
		</tr>
	</table>
	<?php
	echo foot(true);
}
elseif ($job == 'doc_insert_image') {
	$wysiwyg = $gpc->get('wysiwyg', str);
	$leadon = realpath(EDITOR_IMAGEDIR).DIRECTORY_SEPARATOR;
	$leadon = str_replace('\\', '/', $leadon);
	$dir = $gpc->get('dir', none);
	if(!empty($dir)) {
		if(substr($dir, -1, 1)!='/') {
			$dir = $dir . '/';
		}
		$dirok = true;
		$dirnames = split('/', $dir);
		$count = count($dirnames);
		for($di=0; $di < $count; $di++) {
			if($di<(sizeof($dirnames)-2)) {
				$dotdotdir = $dotdotdir . $dirnames[$di] . '/';
			}
		}
		if(substr($dir, 0, 1)=='/') {
			$dirok = false;
		}
		if($dir == $leadon) {
			$dirok = false;
		}
		if($dirok) {
			$leadon .= $dir;
		}
		else {
			$dir = '';
		}
	}

	// upload file
	$error = null;
    if (!empty($_FILES['file']['name'])) {
    	require("classes/class.upload.php");
		$my_uploader = new uploader();
		$my_uploader->max_filesize(ini_maxupload());
		$my_uploader->file_types($supportedextentions);
		$my_uploader->set_path($leadon);
		if ($my_uploader->upload('file')) {
			$my_uploader->save_file();
		}
		if ($my_uploader->upload_failed()) {
			$error = $my_uploader->get_error();
		}
		$file = $leadon.$my_uploader->fileinfo('filename');
		if (!file_exists($file)) {
		    $error = $lang->phrase('admin_cms_file_does_not_exist');
		}
    }
    $htmlhead .= '<script type="text/javascript" src="templates/editor/wysiwyg-popup.js"></script>';
    $htmlhead .= '<script type="text/javascript" src="templates/editor/wysiwyg-color.js"></script>';
    $htmlhead .= '<script type="text/javascript"> function onloader() { WYSIWYG_ColorInst.init(); loadImage(); } </script>';
    echo head(' onLoad="onloader();"');
	?>
<form method="post" action="admin.php?action=cms&amp;job=doc_insert_image&amp;wysiwyg=<?php echo $wysiwyg; ?>" enctype="multipart/form-data">
<input type="hidden" id="dir" name="dir" value="">
<table class="border" border="0" cellspacing="0" cellpadding="4" align="center" style="width: 700px;">
	<tr>
		<td class="obox" colspan="4">Insert Image</td>
		<td class="obox">Select Image</td>
	</tr>
	<tr class="mbox">
		<td width="120">Upload:<br /><span class="stext">Max Filesize: <?php echo formatFilesize(ini_maxupload()); ?></span></td>
		<td colspan="3" width="330">
			<input type="file" name="file" size="30" />
			<?php
			if ($error !== null) {
				echo '<br /><span class="stext">'.$error.'</span>';
			}
			?>
		</td>
		<td rowspan="8" width="250">
			<iframe id="chooser" height="260" width="250" frameborder="0" src="admin.php?action=cms&amp;job=doc_select_image&amp;dir=<?php echo urlencode($dir); ?>"></iframe>
		</td>
	</tr><tr class="mbox">
		<td>Image URL:</td>
		<td colspan="3"><input type="text" name="src" id="src" value="" size="50" /></td>
	</tr><tr class="mbox">
		<td>Alternate Text:</td>
		<td colspan="3"><input type="text" name="alt" id="alt" value="" size="50" /></td>
	</tr>
	<tr><td class="obox" colspan="4">Layout</td></tr>
	<tr class="mbox">
	  <td width="120">Width:</td>
	  <td width="105"><input type="text" name="width" id="width" value="" size="10" />px</td>
	  <td width="120">Height:</td>
	  <td width="105"><input type="text" name="height" id="height" value="" size="10" />px</td>
	</tr>
	<tr class="mbox">
	  <td>Horizontal Space:</td>
	  <td><input type="text" name="hspace" id="hspace" value="" size="10" /></td>
	  <td>Vertical Space:</td>
	  <td><input type="text" name="vspace" id="vspace" value="" size="10" /></td>
	</tr>
	<tr class="mbox">
	  <td>Border-Width:</td>
	  <td><input type="text" name="border" id="border" value="0" size="10" />px</td>
	  <td>Alignment:</td>
	  <td>
		<select name="align" id="align">
		 <option value="">Not Set</option>
		 <option value="left">Left</option>
		 <option value="right">Right</option>
		 <option value="bottom">Bottom</option>
		 <option value="middle">Middle</option>
		 <option value="top">Top</option>
		</select>
	  </td>
	</tr>
	<tr class="mbox">
	  <td>Border-Color:</td>
	  <td colspan="3">
	  	<input type="text" name="bordercolor" id="bordercolor" value="none" size="10" />
	  	<input type="button" value="Choose" onClick="WYSIWYG_ColorInst.choose('bordercolor');" />
	  </td>
	</tr>
	<tr class="mbox">
	  <td colspan="5" class="ubox" align="center">
		<input type="submit" value="Submit" onclick="insertImage();return false;">
		<input type="submit" value="Upload">
		<input type="button" value="Cancel" onclick="window.close();">
	  </td>
	</tr>
	</table>
	</form>
	<?php
	echo foot();
}
elseif ($job == 'doc') {
	$memberdata_obj = $scache->load('memberdata');
	$memberdata = $memberdata_obj->get();
	$language_obj = $scache->load('loadlanguage');
	$language = $language_obj->get();

	$result = $db->query("
		SELECT d.id, d.author, d.update, d.icomment, c.lid, c.title, c.active
		FROM {$db->pre}documents AS d
			LEFT JOIN {$db->pre}documents_content AS c ON d.id = c.did
		ORDER BY c.title
	", __LINE__, __FILE__);
	$data = array();
	while ($row = $db->fetch_assoc($result)) {
		if(is_id($row['author']) && isset($memberdata[$row['author']])) {
			$row['author'] = $memberdata[$row['author']];
		}
		else {
			$row['author'] = $lang->phrase('admin_cms_unknown');
		}
		if ($row['update'] > 0) {
			$row['update'] = gmdate('d.m.Y', times($row['update'])).'<br />'.gmdate('H:i', times($row['update']));
		}
		else {
			$row['update'] = $lang->phrase('admin_cms_unknown');
		}
		if (strlen($row['icomment']) > 100) {
			$row['icomment'] = substr($row['icomment'], 0, 100).'...';
		}
		$newRow = array(
			'title' => $row['title'],
			'active' => $row['active']
		);
		if (!isset($data[$row['id']])) {
			$row['languages'] = array($row['lid'] => $newRow);
			$data[$row['id']] = $row;
		}
		else if (!in_array($row['lid'], $data[$row['id']]['languages'])) {
			$data[$row['id']]['languages'][$row['lid']] = $newRow;
		}
	}


	echo head();
?>
<form name="form" method="post" action="admin.php?action=cms&job=doc_delete">
 <table class="border" border="0" cellspacing="0" cellpadding="4" align="center">
  <tr>
   <td class="obox" colspan="8">
   <span style="float: right;"><a class="button" href="admin.php?action=cms&job=doc_add"><?php echo $lang->phrase('admin_cms_create_new_document'); ?></a></span>
	<?php echo $lang->phrase('admin_cms_manage_documents_and_pages'); ?>
   </td>
  </tr>
  <tr>
   <td class="ubox" width="2%"><?php echo $lang->phrase('admin_cms_doc_delete'); ?><br /><span class="stext"><input type="checkbox" onclick="check_all('delete[]');" name="all" value="1" /> <?php echo $lang->phrase('admin_cms_doc_delete_all'); ?></span></td>
   <td class="ubox" width="30%"><?php echo $lang->phrase('admin_cms_doc_title'); ?></td>
   <td class="ubox" width="14%"><?php echo $lang->phrase('admin_cms_doc_av_languages'); ?></td>
   <td class="ubox" width="3%"><?php echo $lang->phrase('admin_cms_doc_published'); ?></td>
   <td class="ubox" width="12%"><?php echo $lang->phrase('admin_cms_doc_author'); ?></td>
   <td class="ubox" width="8%"><?php echo $lang->phrase('admin_cms_doc_last_change'); ?></td>
   <td class="ubox" width="14%"><?php echo $lang->phrase('admin_cms_doc_id'); ?></td>
   <td class="ubox" width="14%"><?php echo $lang->phrase('admin_cms_doc_action'); ?></td>
  </tr>
<?php
	foreach ($data as $id => $row) {
		$rowspan = count($data[$id]['languages']);
		$i = 0;
		foreach ($data[$id]['languages'] as $lid => $row2) {
			$i++;
			?>
  			<tr>
  			<?php if ($i == 1) { ?>
  			 <td class="mbox center" rowspan="<?php echo $rowspan; ?>"><input type="checkbox" name="delete[]" value="<?php echo $id; ?>"></td>
   			<?php } ?>
   			 <td class="mbox"><a href="admin.php?action=cms&job=doc_edit&id=<?php echo $id; ?>"><?php echo $row2['title']; ?></a></td>
   			 <td class="mbox stext">
   			<?php
   			 if (isset($row['languages'][$lid]) && isset($language[$lid])) {
	   			echo $language[$lid]['language'];
	   		 }
	   		 else if (isset($row['languages'][$lid]) && !isset($language[$lid])) {
	   			echo "<em>".$lang->phrase('admin_cms_unknown')."</em> ({$lid})";
	   		 }
	   		?>
   			 </td>
  			 <td class="mbox center"><?php echo noki($row2['active'], ' onmouseover="HandCursor(this)" onclick="ajax_noki(this, \'action=cms&job=doc_ajax_active&id='.$id.'&lid='.$lid.'\')"'); ?></td>
			<?php if ($i == 1) { ?>
  			 <td class="mbox" rowspan="<?php echo $rowspan; ?>"><?php echo $row['author']; ?></td>
			 <td class="mbox center" rowspan="<?php echo $rowspan; ?>"><?php echo $row['update']; ?></td>
			 <td class="mbox stext" rowspan="<?php echo $rowspan; ?>"><?php echo nl2br($row['icomment']); ?></td>
			 <td class="mbox" rowspan="<?php echo $rowspan; ?>">
			  <a class="button" href="docs.php?id=<?php echo $id.SID2URL_x; ?>" target="_blank"><?php echo $lang->phrase('admin_cms_view'); ?></a>
			  <a class="button" href="admin.php?action=cms&job=doc_edit&id=<?php echo $id; ?>"><?php echo $lang->phrase('admin_cms_edit'); ?></a>
			 </td>
			<?php } ?>
			</tr>
<?php
		}
	}
?>
  <tr>
   <td class="ubox" width="100%" colspan="8" align="center"><input type="submit" name="Submit" value="<?php echo $lang->phrase('admin_cms_form_delete'); ?>"></td>
  </tr>
 </table>
</form>
<?php
	echo foot();
}
elseif ($job == 'doc_ajax_active') {
	$id = $gpc->get('id', int);
	$lid = $gpc->get('lid', int);
	$result = $db->query("SELECT active FROM {$db->pre}documents_content WHERE did = '{$id}' AND lid = '{$lid}' LIMIT 1", __LINE__, __FILE__);
	$use = $db->fetch_assoc($result);
	$use = invert($use['active']);
	$db->query("UPDATE {$db->pre}documents_content SET active = '{$use}' WHERE did = '{$id}' AND lid = '{$lid}' LIMIT 1", __LINE__, __FILE__);
	$delobj = $scache->load('wraps');
	$delobj->delete();
	die(strval($use));
}
elseif ($job == 'doc_add') {
	echo head();
	$type = doctypes();
	$parser = array(
		'0' => $lang->phrase('admin_cms_doc_no_parser'),
		'1' => $lang->phrase('admin_cms_doc_html'),
		'2' => $lang->phrase('admin_cms_doc_php_html'),
		'3' => $lang->phrase('admin_cms_doc_bbcodes')
	);
	?>
 <table class="border" border="0" cellspacing="0" cellpadding="4" align="center">
  <tr>
   <td class="obox" colspan="4"><?php echo $lang->phrase('admin_cms_create_doc_step_1'); ?></td>
  </tr>
  <tr>
   <td class="ubox"><?php echo $lang->phrase('admin_cms_doc_title'); ?></td>
   <td class="ubox"><?php echo $lang->phrase('admin_cms_doc_template'); ?></td>
   <td class="ubox"><?php echo $lang->phrase('admin_cms_doc_parser'); ?></td>
   <td class="ubox"><?php echo $lang->phrase('admin_cms_doc_integration_of_templates'); ?></td>
  </tr>
<?php
foreach ($type as $id => $row) {
	$row['parser'] = isset($parser[$row['parser']]) ? $parser[$row['parser']] : $lang->phrase('admin_cms_doc_parser_unknown');
	$row['inline'] = ($row['inline'] == 1) ? $lang->phrase('admin_cms_doc_static') : $lang->phrase('admin_cms_doc_dynamic');
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
	$type = $gpc->get('type', int);
	$types = doctypes();
	if (!isset($types[$type])) {
		$type = 3;
	}
	$format = $types[$type];
	$language_obj = $scache->load('loadlanguage');
	$language = $language_obj->get();
	if ($format['parser'] == 1) {
		$tas = array();
		foreach ($language as $lid => $data) {
			$tas[] = "template[{$lid}]";
		}
		$htmlhead .= attachWYSIWYG();
	}
	echo head(' onload="hideLanguageBoxes()"');
  	$groups = $db->query("SELECT id, name FROM {$db->pre}groups", __LINE__, __FILE__);
?>
<form id="form" method="post" action="admin.php?action=cms&job=doc_add3&type=<?php echo $type; ?>">
 <table class="border" border="0" cellspacing="0" cellpadding="4" align="center">
  <tr>
   <td class="obox"><?php echo $lang->phrase('admin_cms_create_doc_step_2'); ?></td>
  </tr>
 <tr>
  <td class="ubox"><?php echo $lang->phrase('admin_cms_doc_global_settings'); ?></td>
  </tr>
  <tr>
   <td class="mbox"><span class="stext right"><?php echo $lang->phrase('admin_cms_doc_groups_text'); ?></span><?php echo $lang->phrase('admin_cms_doc_groups'); ?><br />
   <?php while ($row = $db->fetch_assoc($groups)) { ?>
	<input type="checkbox" name="groups[]" checked="checked" value="<?php echo $row['id']; ?>"> <?php echo $row['name']; ?><br />
   <?php } ?>
   </td>
  </tr>
  <tr>
   <td class="mbox"><?php echo $lang->phrase('admin_cms_doc_internal_note'); ?><br />
   <textarea name="icomment" class="texteditor" cols="80" rows="3"></textarea>
   </td>
  </tr>
<?php foreach ($language as $lid => $data) { ?>
  <tr>
   <td class="ubox">
   	<input type="checkbox" id="use_<?php echo $lid; ?>" name="use[<?php echo $lid; ?>]" value="1" title="<?php echo $lang->phrase('admin_cms_doc_click_for_adding_lang'); ?>" onclick="return changeLanguageUsage(<?php echo $lid; ?>)" />
   	<strong><?php echo $data['language']; ?></strong>
   </td>
  </tr>
  <tbody id="language_<?php echo $lid; ?>">
  <tr>
   <td class="mbox">
	<?php if ($format['inline'] == 1 && empty($format['template'])) { ?><span class="stext right"><?php echo $lang->phrase('admin_cms_if_no_title_can_be_parsed'); ?></span><?php } ?>
	<?php echo $lang->phrase('admin_cms_news_title'); ?><br />
	<input type="text" name="title[<?php echo $lid; ?>]" size="60" />
   </td>
  </tr>
  <tr>
   <td class="mbox">
   <?php
	if($format['remote'] != 1) {
		if($format['parser'] == 3) {
			?>
			<a class="right" href="misc.php?action=bbhelp<?php echo SID2URL_x; ?>" target="_blank"><?php echo $lang->phrase('bbcode_help'); ?></a>
			<?php echo $lang->phrase('admin_cms_doc_sourcecode'); ?>
			<br />
			<?php
			BBCodeToolBox("template[{$lid}]", '', 'rows="18" cols="110" class="texteditor editor_textarea_inner"');
		}
		else {
			echo $lang->phrase('admin_cms_doc_sourcecode');
			?>
			<br /><textarea id="template[<?php echo $lid; ?>]" name="template[<?php echo $lid; ?>]" rows="20" cols="110" class="texteditor"></textarea>
			<?php
		}
	}
	else {
	   	echo $lang->phrase('admin_cms_nav_file_url');
		?>
		<br />
		<input type="text" name="template[<?php echo $lid; ?>]" size="60" />
	<?php } ?>
   </td>
  </tr>
  <tr>
   <td class="mbox">
	<?php echo $lang->phrase('admin_cms_doc_active'); ?><br />
	<input type="checkbox" value="1" name="active[<?php echo $lid; ?>]" />
   </td>
  </tr>
  </tbody>
<?php } ?>
  <tr><td class="ubox" align="center"><input type="submit" name="Submit" value="<?php echo $lang->phrase('admin_cms_form_add'); ?>" /></td></tr>
 </table>
</form>
<?php
echo foot();
}
elseif ($job == 'doc_add3') {
	echo head();

	$type = $gpc->get('type', int);
	$icomment = $gpc->get('icomment', str);
	$title = $gpc->get('title', arr_str);
	$active = $gpc->get('active', arr_int);
	$use = $gpc->get('use', arr_int);
  	$groups = $gpc->get('groups', arr_int);
  	$content = $gpc->get('template', arr_none);

	$types = doctypes();
	$format = $types[$type];

	$i = 0;
	foreach ($use as $lid => $usage) {
		if ($format['remote'] == 1) {
			$content[$lid] = '';
		}
		if ($usage == 1) {
			$i++;
		}
	}
	if ($i == 0) {
		error('javascript:history.back(-1);', $lang->phrase('admin_cms_havent_checked_box'));
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

	$db->query("INSERT INTO {$db->pre}documents (`author`, `date`, `update`, `type`, `groups`, `icomment`) VALUES ('{$my->id}', '{$time}' , '{$time}' , '{$type}', '{$groups}', '{$icomment}')", __LINE__, __FILE__);
	$did = $db->insert_id();

	foreach ($use as $lid => $usage) {
		if ($usage == 1) {
			if (strlen($content[$lid]) < 20) {
				$content[$lid] = trim(strip_tags($content[$lid]));
			}
			if (empty($content[$lid])) {
				continue;
			}
			if (empty($title[$lid])) {
				$title[$lid] = substr(strip_tags($content[$lid]), 0, 50).'...';
			}
			if (empty($active[$lid])) {
				$active[$lid] = 0;
			}
			if ($format['parser'] == 3) {
				// Handle bb-code like in the forums (entites etc.)
				$content[$lid] = $gpc->save_str($content[$lid]);
			}
			else {
				$content[$lid] = $db->escape_string($content[$lid]);
			}
			$lid = $gpc->save_int($lid);
			$db->query("INSERT INTO {$db->pre}documents_content ( `did` , `lid` , `title` , `content` , `active` ) VALUES ('{$did}', '{$lid}', '{$title[$lid]}', '{$content[$lid]}', '{$active[$lid]}')", __LINE__, __FILE__);
		}
	}

	$delobj = $scache->load('wraps');
	$delobj->delete();

	ok('admin.php?action=cms&job=doc', $lang->phrase('admin_cms_document_successfully_added'));
}
elseif ($job == 'doc_delete') {
	echo head();
	$delete = $gpc->get('delete', arr_int);
	if (count($delete) > 0) {
		$deleteids = implode(',', $delete);
		$db->query("DELETE FROM {$db->pre}documents WHERE id IN ({$deleteids})", __LINE__, __FILE__);
		$anz = $db->affected_rows();
		$db->query("DELETE FROM {$db->pre}documents_content WHERE did IN ({$deleteids})", __LINE__, __FILE__);

		$delobj = $scache->load('wraps');
		$delobj->delete();

		ok('admin.php?action=cms&job=doc', $lang->phrase('admin_cms_documents_deleted'));
	}
	else {
		error('admin.php?action=cms&job=doc', $lang->phrase('admin_cms_havent_checked_box'));
	}
}
elseif ($job == 'doc_edit') {
	$id = $gpc->get('id', int);
	$types = doctypes();

	$result = $db->query("SELECT * FROM {$db->pre}documents WHERE id = '{$id}'", __LINE__, __FILE__);
	if ($db->num_rows($result) == 0) {
		error('admin.php?action=cms&job=doc', $lang->phrase('admin_cms_invalid_id_given'));
	}
	$row = $db->fetch_assoc($result);

	$result = $db->query("SELECT content, active, title, lid FROM {$db->pre}documents_content WHERE did = '{$id}'", __LINE__, __FILE__);
	$content = array();
	while ($row2 = $db->fetch_assoc($result)) {
		$content[$row2['lid']] = $row2;
	}

	$format = $types[$row['type']];
	$groups = $db->query("SELECT id, name FROM {$db->pre}groups", __LINE__, __FILE__);
	$garr = explode(',', $row['groups']);

	$memberdata_obj = $scache->load('memberdata');
	$memberdata = $memberdata_obj->get();

	$language_obj = $scache->load('loadlanguage');
	$language = $language_obj->get();

	if ($format['parser'] == 1) {
		$htmlhead .= attachWYSIWYG();
	}
	echo head(' onload="hideLanguageBoxes()"');
?>
<form id="form" method="post" action="admin.php?action=cms&job=doc_edit2&id=<?php echo $id; ?>">
 <table class="border" border="0" cellspacing="0" cellpadding="4" align="center">
  <tr>
   <td class="obox"><?php echo $lang->phrase('admin_cms_edit_doc'); ?></td>
  </tr>
 <tr>
  <td class="ubox"><?php echo $lang->phrase('admin_cms_doc_global_settings'); ?></td>
  </tr>
  <tr>
   <td class="mbox"><span class="stext right"><?php echo $lang->phrase('admin_cms_doc_groups_text'); ?></span><?php echo $lang->phrase('admin_cms_doc_groups'); ?><br />
   <?php while ($g = $db->fetch_assoc($groups)) { ?>
	<input type="checkbox" name="groups[]"<?php echo iif($row['groups'] == 0 || in_array($g['id'], $garr),'checked="checked"'); ?> value="<?php echo $g['id']; ?>"> <?php echo $g['name']; ?><br />
   <?php } ?>
   </td>
  </tr>
  <tr>
   <td class="mbox"><?php echo $lang->phrase('admin_cms_doc_internal_note'); ?><br />
   <textarea name="icomment" class="texteditor" cols="80" rows="3"><?php echo $gpc->prepare($row['icomment']); ?></textarea>
   </td>
  </tr>
  <tr>
   <td class="mbox">
	<?php echo $lang->phrase('admin_cms_doc_author_change'); ?><br />
	<input type="radio" value="<?php echo $row['author']; ?>" name="author" checked="checked" /> <?php echo $lang->phrase('admin_cms_keep_current_author'); ?> <strong><?php echo isset($memberdata[$row['author']]) ? $memberdata[$row['author']] : $lang->phrase('admin_cms_unknown'); ?></strong><br />
	<input type="radio" value="<?php echo $my->id; ?>" name="author" /> <?php echo $lang->phrase('admin_cms_change_author_to'); ?> <strong><?php echo $my->name; ?></strong>
   </td>
  </tr>
<?php
	foreach ($language as $lid => $data) {
		if (isset($content[$lid])) {
			$row2 = $content[$lid];
		}
		else {
			$row2 = array(
				'content' => '',
				'active' => 0,
				'title' => '',
				'lid' => $lid
			);
		}
?>
  <tr>
   <td class="ubox">
   	<input type="checkbox"<?php echo iif(isset($content[$lid]), ' checked="checked"'); ?> id="use_<?php echo $lid; ?>" name="use[<?php echo $lid; ?>]" value="1" title="<?php echo $lang->phrase('admin_cms_doc_click_for_adding_lang'); ?>" onclick="return changeLanguageUsage(<?php echo $lid; ?>)" />
   	<strong><?php echo $data['language']; ?></strong>
   </td>
  </tr>
  <tbody id="language_<?php echo $lid; ?>">
  <tr>
   <td class="mbox">
	<?php if ($format['inline'] == 1 && empty($format['template'])) { ?><span class="stext right"><?php echo $lang->phrase('admin_cms_if_no_title_can_be_parsed'); ?></span><?php } ?>
	<?php echo $lang->phrase('admin_cms_news_title'); ?><br />
	<input type="text" name="title[<?php echo $lid; ?>]" size="60" value="<?php echo $gpc->prepare($row2['title']); ?>" />
   </td>
  </tr>
  <tr>
   <td class="mbox">
   <?php
	if($format['remote'] != 1) {
		if($format['parser'] == 3) {
			?>
			<a class="right" href="misc.php?action=bbhelp<?php echo SID2URL_x; ?>" target="_blank"><?php echo $lang->phrase('bbcode_help'); ?></a>
			<?php echo $lang->phrase('admin_cms_doc_sourcecode'); ?>
			<br />
			<?php
			BBCodeToolBox("template[{$lid}]", $row2['content'], 'rows="18" cols="110" class="texteditor editor_textarea_inner"');
		}
		else {
			echo $lang->phrase('admin_cms_doc_sourcecode');
			?>
			<br /><textarea id="template[<?php echo $lid; ?>]" name="template[<?php echo $lid; ?>]" rows="20" cols="110" class="texteditor"><?php echo $row2['content']; ?></textarea>
			<?php
		}
	}
	else {
	   	echo $lang->phrase('admin_cms_nav_file_url');
		?>
		<br />
		<input type="text" name="template[<?php echo $lid; ?>]" size="60" value="<?php echo $gpc->prepare($row2['content']); ?>" />
	<?php } ?>
   </td>
  </tr>
  <tr>
   <td class="mbox">
	<?php echo $lang->phrase('admin_cms_doc_active'); ?><br />
	<input type="checkbox" value="1" name="active[<?php echo $lid; ?>]"<?php echo iif($row2['active'] == 1, ' checked="checked"'); ?> />
   </td>
  </tr>
  </tbody>
<?php } ?>
  <tr><td class="mbox"><?php echo $lang->phrase('admin_cms_doc_checkboxes_help'); ?></td></tr>
  <tr><td class="ubox" align="center"><input type="submit" name="Submit" value="<?php echo $lang->phrase('admin_cms_form_edit'); ?>" /></td></tr>
 </table>
</form>
<?php
echo foot();
}
elseif ($job == 'doc_edit2') {

	echo head();

	$id = $gpc->get('id', int);
	$icomment = $gpc->get('icomment', str);
	$title = $gpc->get('title', arr_str);
	$active = $gpc->get('active', arr_int);
	$author = $gpc->get('author', int);
	$use = $gpc->get('use', arr_int);
  	$groups = $gpc->get('groups', arr_int);
  	$content = $gpc->get('template', arr_none);

	$result = $db->query("SELECT type FROM {$db->pre}documents WHERE id = '{$id}' LIMIT 1", __LINE__, __FILE__);
	if ($db->num_rows($result) == 0) {
		error('admin.php?action=cms&job=doc', $lang->phrase('admin_cms_document_doesnt_exist'));
	}
	$doc = $db->fetch_assoc($result);
	$types = doctypes();
	$format = $types[$doc['type']];

	$i = 0;
	foreach ($use as $lid => $usage) {
		if ($format['remote'] == 1) {
			$content[$lid] = '';
		}
		if ($usage == 1) {
			$i++;
		}
	}
	if ($i == 0) {
		error('javascript:history.back(-1);', $lang->phrase('admin_cms_havent_checked_box'));
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

	$db->query("UPDATE {$db->pre}documents SET `update` = '{$time}', `groups` = '{$groups}', `author` = '{$author}', `icomment` = '{$icomment}' WHERE id = '{$id}' LIMIT 1",__LINE__,__FILE__);

	$language_obj = $scache->load('loadlanguage');
	$language = $language_obj->get();

	foreach ($language as $lid => $x) {
		if (empty($use[$lid])) {
			$usage = 0;
		}
		else {
			$usage = 1;
		}
		$lid = $gpc->save_int($lid);
		if (strlen($content[$lid]) < 20) {
			$content[$lid] = trim(strip_tags($content[$lid]));
		}
		if (empty($content[$lid]) || $usage != 1) {
			$db->query("DELETE FROM v_documents_content WHERE did = '{$id}' AND lid = '{$lid}'");
		}
		elseif ($usage == 1) {
			if (empty($title[$lid])) {
				$title[$lid] = substr(strip_tags($content[$lid]), 0, 50).'...';
			}
			if (empty($active[$lid])) {
				$active[$lid] = 0;
			}
			$result = $db->query("SELECT lid FROM v_documents_content WHERE did = '{$id}' AND lid = '{$lid}'");
			if ($format['parser'] == 3) {
				// Handle bb-code like in the forums (entites etc.)
				$content[$lid] = $gpc->save_str($content[$lid]);
			}
			else {
				$content[$lid] = $db->escape_string($content[$lid]);
			}
			if ($db->num_rows($result) == 1) {
				$db->query("UPDATE {$db->pre}documents_content SET `title` = '{$title[$lid]}', `content` = '{$content[$lid]}', `active` = '{$active[$lid]}' WHERE did = '{$id}' AND lid = '{$lid}'", __LINE__, __FILE__);
			}
			else {
				$db->query("INSERT INTO {$db->pre}documents_content ( `did` , `lid` , `title` , `content` , `active` ) VALUES ('{$id}', '{$lid}', '{$title[$lid]}', '{$content[$lid]}', '{$active[$lid]}')", __LINE__, __FILE__);
			}
		}
	}

	$delobj = $scache->load('wraps');
	$delobj->delete();

	ok('admin.php?action=cms&job=doc', $lang->phrase('admin_cms_document_successfully_changed'));
}
elseif ($job == 'doc_code') {
	echo head();
	$codelang = $scache->load('syntaxhighlight');
	$clang = $codelang->get();
	?>
	<script src="templates/editor/bbcode.js" type="text/javascript"></script>
	<table class="border">
	<tr><td class="obox"><?php echo $lang->phrase('admin_cms_bb_tag_code'); ?></td></tr>
	<tr><td class="mbox">
	<strong><?php echo $lang->phrase('admin_cms_choose_programming_language'); ?></strong><br /><br />
	<ul>
	   <li><input type="radio" name="data" onclick="InsertTagsCode('[code]','[/code]')" /> <?php echo $lang->phrase('admin_cms_no_syntax_highlighting'); ?></li>
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
	$result = $db->query("SELECT * FROM {$db->pre}grab ORDER BY title", __LINE__, __FILE__);
	$num = $db->num_rows($result);
	echo head();
?>
<form name="form" method="post" action="admin.php?action=cms&job=feed_delete">
 <table class="border" border="0" cellspacing="0" cellpadding="4" align="center">
  <tr>
   <td class="obox" colspan="5"><span style="float: right;"><a class="button" href="admin.php?action=cms&job=feed_add"><?php echo $lang->phrase('admin_cms_add_newsfeed'); ?></a></span><?php echo $lang->phrase('admin_cms_impor_of_newsfeeds'); ?> (<?php echo $num; ?>)</td>
  </tr>
  <tr>
   <td class="ubox" width="5%"><?php echo $lang->phrase('admin_cms_news_delete'); ?><br /><span class="stext"><input type="checkbox" onclick="check_all('delete[]');" name="all" value="1" /> <?php echo $lang->phrase('admin_cms_news_delete_all'); ?></span></td>
   <td class="ubox" width="5%"><?php echo $lang->phrase('admin_cms_news_id'); ?></td>
   <td class="ubox" width="35%"><?php echo $lang->phrase('admin_cms_news_title_head'); ?></td>
   <td class="ubox" width="45%"><?php echo $lang->phrase('admin_cms_news_file'); ?></td>
   <td class="ubox" width="10%"><?php echo $lang->phrase('admin_cms_news_entries'); ?></td>
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
   <td class="ubox" width="100%" colspan="5" align="center"><input type="submit" name="Submit" value="<?php echo $lang->phrase('admin_cms_form_delete'); ?>"></td>
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
   <td class="obox" colspan="2"><?php echo $lang->phrase('admin_cms_add_newsfeed'); ?></td>
  </tr>
  <tr>
   <td class="mbox"><?php echo $lang->phrase('admin_cms_news_title'); ?><br><span class="stext"><?php echo $lang->phrase('admin_cms_news_title_text'); ?></td>
   <td class="mbox"><input type="text" name="temp1" size="60"></td>
  </tr>
  <tr>
   <td class="mbox"><?php echo $lang->phrase('admin_cms_news_url'); ?><br><span class="stext"><?php echo $lang->phrase('admin_cms_news_url_text'); ?></td>
   <td class="mbox"><input type="text" name="temp2" size="60"></td>
  </tr>
  <tr>
   <td class="mbox"><?php echo $lang->phrase('admin_cms_news_number_of_entries'); ?><br><span class="stext"><?php echo $lang->phrase('admin_cms_news_number_of_entries_text'); ?></td>
   <td class="mbox"><input type="text" name="value" size="3"></td>
  </tr>
  <tr>
   <td class="ubox" width="100%" colspan="2" align="center"><input type="submit" name="Submit" value="<?php echo $lang->phrase('admin_cms_form_add'); ?>"></td>
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
		error('admin.php?action=cms&job=feed_add', $lang->phrase('admin_cms_no_title_specified'));
	}
	if (empty($file)) {
		error('admin.php?action=cms&job=feed_add', $lang->phrase('admin_cms_no_url_specified'));
	}
	if (empty($entries)) {
		$entries = 0;
	}

	$db->query("INSERT INTO {$db->pre}grab (title, file, entries) VALUES ('{$title}','{$file}','{$entries}')", __LINE__, __FILE__);

	$delobj = $scache->load('grabrss');
	$delobj->delete();

	ok('admin.php?action=cms&job=feed', $lang->phrase('admin_cms_newsfeed_successfully_added'));
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

		ok('admin.php?action=cms&job=feed', $lang->phrase('admin_cms_newsfeeds_successfully_deleted'));
	}
	else {
		error('admin.php?action=cms&job=feed', $lang->phrase('admin_cms_no_newsfeed_selected'));
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
   <td class="obox" colspan="2"><?php echo $lang->phrase('admin_cms_news_edit_document'); ?></td>
  </tr>
  <tr>
   <td class="mbox"><?php echo $lang->phrase('admin_cms_news_title'); ?><br><span class="stext"><?php echo $lang->phrase('admin_cms_news_title_text'); ?></span></td>
   <td class="mbox"><input type="text" name="temp1" size="60" value="<?php echo $gpc->prepare($row['title']); ?>"></td>
  </tr>
  <tr>
   <td class="mbox"><?php echo $lang->phrase('admin_cms_news_url'); ?><br><span class="stext"><?php echo $lang->phrase('admin_cms_news_url_text'); ?></span></td>
   <td class="mbox"><input type="text" name="temp2" size="60" value="<?php echo $row['file']; ?>"></td>
  </tr>
  <tr>
   <td class="mbox"><?php echo $lang->phrase('admin_cms_news_number_of_entries'); ?><br><span class="stext"><?php echo $lang->phrase('admin_cms_news_number_of_entries_text'); ?></span></td>
   <td class="mbox"><input type="text" name="value" size="3" value="<?php echo $row['entries']; ?>"></td>
  </tr>
  <tr>
   <td class="ubox" width="100%" colspan=2 align="center"><input type="submit" name="Submit" value="<?php echo $lang->phrase('admin_cms_form_edit'); ?>"></td>
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

	ok('admin.php?action=cms&job=feed', $lang->phrase('admin_cms_newsfeed_successfully_updated'));
}
?>
