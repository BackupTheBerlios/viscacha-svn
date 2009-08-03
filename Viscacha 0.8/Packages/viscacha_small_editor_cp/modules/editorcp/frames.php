<?php
if (defined('VISCACHA_CORE') == false) { die('Error: Hacking Attempt'); }
$lang->group("admin/frames");

if ($job == 'menu') {
	?>
	<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
	<html>
	<head>
	<title><?php echo $config['fname']; ?> - Editor CP</title>
	<meta http-equiv="content-type" content="text/html; charset=ISO-8859-1">
	<meta http-equiv="pragma" content="no-cache">
	<link rel="stylesheet" type="text/css" href="admin/html/menu.css">
	<link rel="copyright" href="http://www.viscacha.org">
	<script src="templates/global.js" language="Javascript" type="text/javascript"></script>
	<script src="editorcp/html/admin.js" language="Javascript" type="text/javascript"></script>
	</head>
	<body onload="init()">
	<p class="center">
	<a href="editorcp.php?action=frames&amp;job=start" target="Main"><img src="admin/html/images/logo.png" alt="Viscacha" /></a><br />
	<span style="font-weight: bold; letter-spacing: 3px; font-variant: small-caps;">Editor CP</span>
	</p>
	 <div class="border">
	  <h3><img id="img_editorcp_menu1" src="admin/html/images/plus.gif" alt="collapse" /> <?php echo $lang->phrase("admin_content_manager");?></h3>
	  <ul id="part_editorcp_menu1">
	   <li>&raquo; <a href="editorcp.php?action=cms&amp;job=doc" target="Main"><?php echo $lang->phrase("admin_documents_pages");?></a></li>
	   <li>&raquo; <a href="editorcp.php?action=cms&amp;job=nav" target="Main"><?php echo $lang->phrase("admin_navigation_manager");?></a></li>
	  </ul>
	 </div>
	 <div class="border">
	   <h3><img id="img_editorcp_menu3" src="admin/html/images/plus.gif" alt="collapse" /> <?php echo $lang->phrase("admin_useful_links");?></h3>
	  <ul id="part_editorcp_menu3">
	   <li>&raquo; <a href="index.php<?php echo SID2URL_1; ?>" target="_blank"><?php echo $lang->phrase("admin_goto_forum");?></a></li>
	   <li>&raquo; <a href="editorcp.php?action=logout<?php echo SID2URL_x; ?>" target="_top"><?php echo $lang->phrase("admin_signoff");?></a></li>
	   <li>&raquo; <a href="http://www.viscacha.org" target="_blank"><?php echo $lang->phrase("admin_supportlink");?></a></li>
	  </ul>
	 </div>
	</body>
	</html>
	<?php
}
elseif ($job == 'start') {
	$lang->group("admin/start");
	echo head();
	?>
	 <table class="border">
	  <tr>
	   <td class="obox" colspan="4">
		<span class="right"><a class="button" href="editorcp.php?action=logout<?php echo SID2URL_x; ?>" target="_top"><?php echo $lang->phrase('admin_sign_off'); ?></a></span>
		Editor CP
	   </td>
	  </tr>
	  <tr>
	   <td class="ubox" align="center" colspan="4"><?php echo $lang->phrase('admin_program_stats'); ?></td>
	  </tr>
	  <tr>
		<td class="mmbox" width="25%"><?php echo $lang->phrase('admin_viscacha_version'); ?></td>
		<td class="mbox"  width="25%"><?php echo $config['version']; ?></td>
		<td class="mmbox" width="25%"><?php echo $lang->phrase('admin_website_offline');?></td>
		<td class="mbox"  width="25%"><?php echo noki($config['foffline']); ?></td>
	  </tr>
	 </table>
	<br />
	 <table class="border">
	  <tr>
	   <td class="obox" align="center" colspan="2"><?php echo $lang->phrase('admin_useful_links'); ?></td>
	  </tr>
	  <tr>
	  	<td class="mbox"><?php echo $lang->phrase('admin_php_lookup'); ?></td>
		<td class="mbox">
		<form action="http://www.php.net/manual-lookup.php" method="get">
		<input type="text" name="function" size="30" />&nbsp;
		<input type="submit" value="<?php echo $lang->phrase('admin_button_find'); ?>" />
		</form>
		</td>
	  </tr>
	  <tr>
	  	<td class="mbox"><?php echo $lang->phrase('admin_mysql_lookup'); ?></td>
		<td class="mbox">
		<form action="http://www.mysql.com/search/" method="get">
		<input type="text" name="q" size="30" />&nbsp;
		<input type="submit" value="<?php echo $lang->phrase('admin_button_find'); ?>" />
		<input type="hidden" name="doc" value="1" />
		<input type="hidden" name="m" value="o" />
		</form>
		</td>
	  </tr>
	  <tr>
	  	<td class="mbox"><?php echo $lang->phrase('admin_useful_links'); ?></td>
		<td class="mbox">
	<form>
	<select onchange="if (this.options[this.selectedIndex].value != '') { window.open(this.options[this.selectedIndex].value); } return false;">
		<option value=""><?php echo $lang->phrase('admin_useful_links'); ?></option>
		<optgroup label="PHP">
		<option value="http://www.php.net/"><?php echo $lang->phrase('admin_documentation_homepage'); ?> (PHP.net)</option>
		<option value="http://www.php.net/manual/"><?php echo $lang->phrase('admin_reference_manual'); ?></option>
		<option value="http://www.php.net/downloads.php"><?php echo $lang->phrase('admin_download_latest_version'); ?></option>
		</optgroup>
		<optgroup label="MySQL">
		<option value="http://www.mysql.com/"><?php echo $lang->phrase('admin_documentation_homepage'); ?> (MySQL.com)</option>
		<option value="http://www.mysql.com/documentation/"><?php echo $lang->phrase('admin_reference_manual'); ?></option>
		<option value="http://www.mysql.com/downloads/"><?php echo $lang->phrase('admin_download_latest_version'); ?></option>
		</optgroup>
		<optgroup label="Viscacha">
		<option value="http://www.viscacha.org/"><?php echo $lang->phrase('admin_documentation_homepage'); ?> (viscacha.org)</option>
		<option value="http://docs.viscacha.org/"><?php echo $lang->phrase('admin_reference_manual'); ?></option>
		<option value="http://files.viscacha.org/"><?php echo $lang->phrase('admin_download_latest_version'); ?></option>
		<option value="http://bugs.viscacha.org/"><?php echo $lang->phrase('admin_bugtracker_todo'); ?></option>
		</optgroup>
	</select>
	</form>
		</td>
	  </tr>
	 </table>
	<?php
	echo foot();
}
else {
	$addr = rawurldecode($gpc->get('addr', none));
	$path = parse_url($addr);
	if (!empty($path['path'])) {
		$file = basename($path['path'], '.php');
	}
	else {
		$file = null;
	}
	if ($file != 'editorcp') {
		$addr = 'editorcp.php?action=frames&amp;job=start';
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Frameset//EN" "http://www.w3.org/TR/REC-html40/frameset.dtd">
<html>
 <head>
  <title><?php echo $config['fname']; ?> - Editor CP</title>
  <meta http-equiv="content-type" content="text/html; charset=ISO-8859-1" />
  <meta http-equiv="pragma" content="no-cache" />
  <link rel="copyright" href="http://www.viscacha.org" />
 </head>
 <frameset cols="200,*" frameborder="0" framespacing="0" border="0">
  <frame name="Menu" src="editorcp.php?action=frames&amp;job=menu" scrolling="auto" noresize="noresize" />
  <frame name="Main" src="<?php echo $addr; ?>" scrolling="auto" noresize="noresize" />
  <noframes>
   <body>
	<p>Your browser does not seem to support frames or frame support has been disabled.</p>
	What do you want to do?
	 <ul>
	  <li><a href="editorcp.php?action=frames&amp;job=menu">Viscacha Editor Control Panel Navigation</a></li>
	 </ul>
	<br />
	 Download a &quot;modern&quot; Browser:
	 <ul>
	  <li><a href="http://www.mozilla.com">Mozilla Firefox</a></li>
	  <li><a href="http://www.opera.com">Opera</a></li>
	  <li><a href="http://www.apple.com/safari">Safari (Only Mac)</a></li>
	 </ul>
   </body>
  </noframes>
 </frameset>
</html>
<?php
}

?>
