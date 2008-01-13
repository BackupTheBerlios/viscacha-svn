<?php
if (defined('VISCACHA_CORE') == false) { die('Error: Hacking Attempt'); }

// PK: MultiLangAdmin
$lang->group("admin/packages");

require('admin/lib/function.language.php');
require('classes/class.phpconfig.php');
include('admin/lib/function.settings.php');
$myini = new INI();

define('DO_UPD_ADD', '+');
define('DO_UPD_DEL', '-');
define('DO_UPD_CHNG', '~');
define('DO_UPD_EQU', '=');

function array_diff_all($arr_new, $arr_old, $assoc = false) {
	if ($assoc == true) {
		$intersect = 'array_intersect_assoc';
		$diff = 'array_diff_assoc';
	}
	else {
		$intersect = 'array_intersect';
		$diff = 'array_diff';
	}
    $arr_equ = $intersect($arr_new, $arr_old);
    $arr_del = $diff($arr_old, $arr_new);
    $arr_add = $diff($arr_new, $arr_old);
    return array(
    	DO_UPD_EQU => $arr_equ,
    	DO_UPD_DEL => $arr_del,
    	DO_UPD_ADD => $arr_add,
    	'count' => count($arr_del)+count($arr_add)+count($arr_equ)
    );
}

function browser_sort_date($a, $b) {
	if ($a['last_updated'] > $b['last_updated']) {
		return -1;
	}
	elseif ($a['last_updated'] < $b['last_updated']) {
		return 1;
	}
	else {
		return 0;
	}
}

($code = $plugins->load('admin_packages_jobs')) ? eval($code) : null;

if ($job == 'package') {
	echo head();
	$result = $db->query("
		SELECT p.*, s.id AS config
		FROM {$db->pre}packages AS p
			LEFT JOIN {$db->pre}settings_groups AS s ON p.internal = s.name
		GROUP BY p.internal
		ORDER BY p.title
	", __LINE__, __FILE__);
	?>
	 <table class="border" border="0" cellspacing="0" cellpadding="4" align="center">
	  <tr>
	   <td class="obox"><?php echo $lang->phrase('admin_packages_head_package_manager'); ?></td>
	  </tr>
	  <tr>
	  	<td class="mbox center">
	  		<?php if ($my->settings['admin_interface'] != 1) { ?>
			<a class="button" href="admin.php?action=packages&amp;job=com" target="Main"><?php echo $lang->phrase('admin_packages_component_manager'); ?></a>
			<a class="button" href="admin.php?action=packages&amp;job=plugins" target="Main"><?php echo $lang->phrase('admin_packages_plugin_manager'); ?></a>
	  		<?php } ?>
	  		<a class="button" href="admin.php?action=packages&amp;job=browser"><?php echo $lang->phrase('admin_packages_browse_packages'); ?></a>
	  		<a class="button" href="admin.php?action=packages&amp;job=package_import"><?php echo $lang->phrase('admin_packages_import_package'); ?></a>
	  		<a class="button" href="admin.php?action=packages&amp;job=package_add"><?php echo $lang->phrase('admin_packages_create_package'); ?></a>
	  		<a class="button" href="admin.php?action=packages&amp;job=package_updates"><?php echo $lang->phrase('admin_packages_check_for_updates'); ?></a>
	  	</td>
	  </tr>
	 </table><br class="minibr" />
	 <table class="border" border="0" cellspacing="0" cellpadding="4" align="center">
	  <tr>
	   <td class="obox" colspan="4"><?php echo $lang->phrase('admin_packages_head_installed_packages'); ?></td>
	  </tr>
	  <tr>
	  	<td class="ubox" width="30%"><?php echo $lang->phrase('admin_packages_info_name'); ?></td>
	  	<td class="ubox center" width="10%"><?php echo $lang->phrase('admin_packages_active'); ?></td>
	  	<td class="ubox center" width="10%"><?php echo $lang->phrase('admin_packages_th_core'); ?></td>
	  	<td class="ubox" width="50%"><?php echo $lang->phrase('admin_packages_th_actions'); ?></td>
	  </tr>
	  <?php while($row = $db->fetch_assoc($result)) { ?>
	  <tr>
	  	<td class="mbox"><a href="admin.php?action=packages&amp;job=package_info&amp;id=<?php echo $row['id']; ?>"><strong><?php echo $row['title']; ?></strong> <?php echo $row['version']; ?></a></td>
	  	<td class="mbox center"><?php echo noki($row['active']); ?></td>
	  	<td class="mbox center"><?php echo noki($row['core']); ?></td>
	  	<td class="mbox">
	  		<a class="button" href="admin.php?action=packages&amp;job=package_info&amp;id=<?php echo $row['id']; ?>"><?php echo $lang->phrase('admin_packages_package_details'); ?></a>
	  		<?php
	  		if (file_exists("modules/{$row['id']}/component.ini")) {
				$com = $myini->read("modules/{$row['id']}/component.ini");
	  			if (!empty($com['admin']['frontpage']) == true) {
	  		?>
	  		<a class="button" href="admin.php?action=packages&amp;job=package_admin&amp;cid=<?php echo $row['id']; ?>"><?php echo $lang->phrase('admin_packages_administration'); ?></a>
	  		<?php } } if ($row['config'] > 0) { ?>
	  		<a class="button" href="admin.php?action=settings&amp;job=custom&amp;id=<?php echo $row['config']; ?>&amp;package=<?php echo $row['id']; ?>"><?php echo $lang->phrase('admin_packages_configuration'); ?></a>
	  		<?php } ?>
	  		<a class="button" href="admin.php?action=packages&amp;job=package_edit&amp;id=<?php echo $row['id']; ?>"><?php echo $lang->phrase('admin_packages_edit'); ?></a>
	  		<?php if ($row['core'] != '1') { ?>
	  		<a class="button" href="admin.php?action=packages&amp;job=package_active&amp;id=<?php echo $row['id']; ?>"><?php echo iif($row['active'] == 1, $lang->phrase('admin_packages_plugins_deactivate'), $lang->phrase('admin_packages_plugins_activate')); ?></a>
	  		<?php } ?>
	  		<a class="button" href="admin.php?action=packages&amp;job=package_updates&amp;id=<?php echo $row['id']; ?>"><?php echo $lang->phrase('admin_packages_check_for_updates'); ?></a>
	  		<a class="button" href="admin.php?action=packages&amp;job=package_export&amp;id=<?php echo $row['id']; ?>"><?php echo $lang->phrase('admin_packages_export'); ?></a>
	  		<?php if ($row['core'] != '1') { ?>
	  		<a class="button" href="admin.php?action=packages&amp;job=package_delete&amp;id=<?php echo $row['id']; ?>"><?php echo $lang->phrase('admin_packages_delete'); ?></a>
	  		<?php } ?>
	  	</td>
	  </tr>
	  <?php } ?>
	 </table>
	<?php
	echo foot();
}
elseif ($job == 'package_admin') {
	$id = $gpc->get('cid', int);
	$mod = $gpc->get('file', str, 'frontpage');
	$result = $db->query("SELECT * FROM {$db->pre}packages WHERE id = '{$id}' LIMIT 1", __LINE__, __FILE__);
	if ($db->num_rows($result) == 0) {
		echo head();
		error('admin.php?action=cms&job=package',$lang->phrase('admin_packages_err_no_package_with_this_id'));
	}
	$pack = $row = $db->fetch_assoc($result); // $row for compatibility
	$com = $myini->read('modules/'.$pack['id'].'/component.ini');
	if (!isset($com['admin'][$mod])) {
		echo head();
		error('admin.php?action=cms&job=package',$lang->phrase('admin_packages_err_section_not_found'));
	}

	DEFINE('COM_ID', $pack['id']);
	DEFINE('COM_DIR', 'modules/'.COM_ID.'/');
	if (!isset($com['admin'][$mod])) {
		DEFINE('COM_MODULE', 'frontpage');
	}
	else {
		DEFINE('COM_MODULE', $mod);
	}
	DEFINE('COM_MODULE_FILE', $com['admin'][COM_MODULE]);
	DEFINE('COM_FILE', $com['admin']['frontpage']);

	$uri = explode('?', $com['admin'][$mod]);
	$file = basename($uri[0]);
	if (isset($uri[1])) {
		parse_str($uri[1], $input);
	}
	else {
		$input = array();
	}
	include("modules/{$pack['id']}/{$file}");
}
elseif ($job == 'package_update') {
	echo head();
	$file = $gpc->get('file', str);
	$id = $gpc->get('id', int);
	?>
<form name="form" method="post" action="admin.php?action=packages&amp;job=package_update2" enctype="multipart/form-data">
 <table class="border" border="0" cellspacing="0" cellpadding="4" align="center">
  <tr>
   <td class="obox" colspan="2"><?php echo $lang->phrase('admin_packages_head_update_a_component'); ?></td>
  </tr>
  <tr>
   <td class="mbox" width="50%">
   	<?php
   		$max_filesize = formatFilesize(ini_maxupload());
   		echo $lang->phrase('admin_packages_import_upload_file');
   		echo '<br />';
   		echo '<span class="stext">'.$lang->phrase('admin_packages_import_text_upload_file_desc').'</span>';
   	?>
   	</td>
   <td class="mbox" width="50%"><input type="file" name="upload" size="40" /></td>
  </tr>
  <tr>
   <td class="mbox">
   		<?php
   			echo $lang->phrase('admin_packages_import_select_file');
   			echo '<br />';
   			echo '<span class="stext">'.$lang->phrase('admin_packages_import_select_file_desc').'</span>';
   		?>
   </td>
   <td class="mbox"><input type="text" name="server" size="50" value="<?php echo $file; ?>" /></td>
  </tr>
  <tr>
   <td class="mbox"><?php echo $lang->phrase('admin_packages_skip_version_check'); ?></td>
   <td class="mbox"><input type="checkbox" name="version" value="1" /></td>
  </tr>
  <tr>
   <td class="mbox"><?php echo $lang->phrase('admin_packages_delete_file_after_import'); ?></td>
   <td class="mbox"><input type="checkbox" name="delete" value="1" checked="checked" /></td>
  </tr>
  <tr>
   <td class="ubox" width="100%" colspan="2" align="center">
   	<input type="hidden" name="id" value="<?php echo $id; ?>" />
   	<input type="submit" name="Submit" value="<?php echo $lang->phrase('admin_packages_button_upload'); ?>">
   </td>
  </tr>
 </table>
</form>
	<?php
	echo foot();
}
elseif ($job == 'package_update2') {
	echo head();

	$del = $gpc->get('delete', int);
	$id = $gpc->get('id', int);
	$versioncheck = $gpc->get('version', int);
	$server = $gpc->get('server', none);
	$inserterrors = array();

	$sourcefile = '';
	if (!empty($_FILES['upload']['name'])) {
		require("classes/class.upload.php");
		$dir = 'temp/';
		$my_uploader = new uploader();
		$my_uploader->file_types(array('zip'));
		$my_uploader->set_path($dir);
		$my_uploader->max_filesize(ini_maxupload());
		if ($my_uploader->upload('upload')) {
			if ($my_uploader->save_file()) {
				$sourcefile = $dir.$my_uploader->fileinfo('filename');
			}
		}
		if ($my_uploader->upload_failed()) {
			array_push($inserterrors,$my_uploader->get_error());
		}
	}
	elseif (file_exists($server)) {
		$ext = get_extension($server);
		if ($ext == 'zip') {
			$sourcefile = $server;
		}
		else {
			$inserterrors[] = $lang->phrase('admin_packages_err_selected_file_is_not_a_zipfile');
		}
	}
	if (!file_exists($sourcefile)) {
		$inserterrors[] = $lang->phrase('admin_packages_err_selected_file_does_not_exist');
	}
	if (count($inserterrors) > 0) {
		error('admin.php?action=designs&job=package_update', $inserterrors);
	}
	else {
		$c = new manageconfig();

		$tdir = "temp/".md5(microtime()).'/';
		$filesystem->mkdir($tdir, 0777);
		if (!is_dir($tdir)) {
			error('admin.php?action=packages&job=package_update', $lang->phrase('admin_packages_err_temporary_directory_could_not_be_created'));
		}
		include('classes/class.zip.php');
		$archive = new PclZip($sourcefile);
		if ($archive->extract(PCLZIP_OPT_PATH, $tdir) == 0) {
			error('admin.php?action=packages&job=package_update', $archive->errorInfo(true));
		}

		if (file_exists($tdir.'modules/package.ini')) {
			$package = $myini->read($tdir.'modules/package.ini');
			if ($versioncheck != 1) {
				if (!empty($package['info']['min_version']) && version_compare($config['version'], $package['info']['min_version'], '<')) {
					error('admin.php?action=packages&job=package_update', $lang->phrase('admin_packages_err_required_min_version'));
				}
				if (!empty($package['info']['max_version']) && version_compare($config['version'], $package['info']['max_version'], '>')) {
					error('admin.php?action=packages&job=package_update', $lang->phrase('admin_packages_err_required_max_version'));
				}
			}
			$package = $gpc->save_str($package);
			if (!isset($package['core'])) {
				$package['info']['core'] = 0;
			}
		}
		else {
			error('admin.php?action=packages&job=package_update', $lang->phrase('admin_packages_err_package_ini_does_not_exist'));
		}

		$result = $db->query("SELECT id FROM {$db->pre}packages WHERE internal = '{$package['info']['internal']}'", __LINE__, __FILE__);
		if ($db->num_rows($result) == 0) {
			error('admin.php?action=packages&job=package_update', $lang->phrase('admin_packages_package_with_name_not_installed'));
		}
		list($packageid) = $db->fetch_num($result);
		if (is_id($id) == true && $packageid != $id) {
			error('admin.php?action=packages&job=package_update', $lang->phrase('admin_packages_packache_id_doesnt_match'));
		}
		if (isset($package['dependency']) && count($package['dependency']) > 0) {
			$result = $db->query("SELECT internal FROM {$db->pre}packages", __LINE__, __FILE__);
			$internals = array();
			while ($row = $db->fetch_assoc($result)) {
				$internals[] = $row['internal'];
			}
			$missing = array_diff($package['dependency'], $internals);
			if (count($missing) > 0) {
				error('admin.php?action=packages&job=package_import', $lang->phrase('admin_packages_dependency_missing').implode(', ', $missing), 60000);
			}
		}

		$db->query("UPDATE {$db->pre}packages SET title = '{$package['info']['title']}', version = '{$package['info']['version']}' WHERE id = ''", __LINE__, __FILE__);

		$moddir = "./modules/{$packageid}/";
		$old = $myini->read($moddir.'package.ini');

		// Abgleich von Einstellungs-Gruppen
		if (!empty($package['config']['title'])) {
			if (!isset($package['config']['description'])) {
				$package['config']['description'] = '';
			}
			$result = $db->query("SELECT id FROM {$db->pre}settings_groups WHERE name = '{$package['info']['internal']}'", __LINE__, __FILE__);
			if ($db->num_rows($result) > 0) {
				$db->query("UPDATE {$db->pre}settings_groups SET title = '{$package['config']['title']}', description = '{$package['config']['description']}' WHERE name = '{$package['info']['internal']}'", __LINE__, __FILE__);
				list($sg) = $db->fetch_num($result);
			}
			else {
				$db->query("INSERT INTO {$db->pre}settings_groups (title, name, description) VALUES ('{$package['config']['title']}', '{$package['info']['internal']}', '{$package['config']['description']}')", __LINE__, __FILE__);
				$sg = $db->insert_id();
			}
		}
		elseif (!empty($old['config']['title'])) {
			$result = $db->query("SELECT id FROM {$db->pre}settings_groups WHERE name = '{$package['info']['internal']}'");
			if ($db->num_rows($result) > 0) {
				list($sg) = $db->fetch_num($result);
				$db->query("DELETE FROM {$db->pre}settings_groups WHERE id = '{$sg}'");
			}
			$sg = null;
		}

		$settings = array_merge(array_keys($old), array_keys($package));
		// Abgleich von Einstellungen
		$c->getdata();
		foreach ($settings as $section) {
			if (substr($section, 0, 8) == 'setting_') {
				$name = substr($section, 8);
				if ($sg != null && isset($old[$section]) && isset($package[$section])) { // Nur aktualisieren
					$values = $package[$section];
					$db->query("UPDATE {$db->pre}settings SET title = '{$values['title']}', description = '{$values['description']}', type = '{$values['type']}', optionscode = '{$values['optionscode']}', value = '{$values['value']}') WHERE name = '{$name}' AND sgroup = '{$sg}'", __LINE__, __FILE__);
				}
				elseif ($sg != null && !isset($old[$section]) && isset($package[$section])) { // Hinzuf�gen
					$values = $package[$section];
					$db->query("
					INSERT INTO {$db->pre}settings (name, title, description, type, optionscode, value, sgroup)
					VALUES ('{$name}', '{$values['title']}', '{$values['description']}', '{$values['type']}', '{$values['optionscode']}', '{$values['value']}', '{$sg}')
					", __LINE__, __FILE__);
					$c->updateconfig(array($package['info']['internal'], $name), none, $values['value']);
				}
				else { // L�schen
					$c->delete(array($package['info']['internal'], $name));
					$db->query("DELETE FROM {$db->pre}settings WHERE sgroup = '{$sg}' AND name = '{$name}'");
				}
			}
		}
		$c->savedata();

		$result = $db->query("SELECT template, stylesheet, images FROM {$db->pre}designs WHERE id = '{$config['templatedir']}'",__LINE__,__FILE__);
		$design = $db->fetch_assoc($result);

		// Components Start
		$result = $db->query("SELECT id FROM {$db->pre}component WHERE package = '{$packageid}'", __LINE__, __FILE__);
		$do = null;
		if ($db->num_rows($result) == 0) {
			if (file_exists($tdir.'component.ini')) {
				$do = DO_UPD_ADD; // Insert
			}
		}
		else {
			list($comid) = $db->fetch_num($result);
			if (file_exists($tdir.'component.ini')) {
				$do = DO_UPD_CHNG; // Update
			}
			else {
				$do = DO_UPD_DEL; // Delete
			}
		}


		$com = $old_com = array();
		if (file_exists($moddir.'component.ini')) {
			$old_com = $myini->read($moddir.'component.ini');
		}
		if (file_exists($tdir.'component.ini')) {
			$com = $myini->read($tdir.'component.ini');
			if (!isset($com['module']['frontpage'])) {
				$com['module']['frontpage'] = '';
			}
		}

		if ($do == DO_UPD_ADD) {
			$db->query("INSERT INTO {$db->pre}component (file, package, required) VALUES ('".$gpc->save_str($com['module']['frontpage'])."', '{$packageid}', '".$gpc->save_str($com['info']['required'])."')", __LINE__, __FILE__);
			$comid = $db->insert_id();
		}
		elseif ($do == DO_UPD_CHNG) {
			$db->query("UPDATE {$db->pre}component SET file = '".$gpc->save_str($com['module']['frontpage'])."', required = '".$gpc->save_str($com['info']['required'])."' WHERE id = '{$comid}'", __LINE__, __FILE__);
		}
		elseif ($do == DO_UPD_DEL) {
			$db->query("DELETE FROM {$db->pre}component WHERE id = '{$comid}'", __LINE__, __FILE__);
		}

		if ($do != null) {
			// Images
			$diff = array_diff_all(
				isset($com['images']) ? $com['images'] : array(),
				isset($old_com['images']) ? $old_com['images'] : array()
			);
			if ($diff['count'] > 0) {
				foreach ($diff as $handler => $files) {
					foreach($files as $file) {
						$dir1 = "{$tdir}images/{$file}";
						$dir2 = "./images/{$design['images']}/{$file}";
						if ($handler == DO_UPD_ADD) {
							$filesystem->unlink($dir2);
							$filesystem->rename($dir1, $dir2);
						}
						elseif ($handler == DO_UPD_DEL) {
							$filesystem->unlink($dir2);
						}
						// Dont update equal files. That has to be done by the custom updater when its required. We dont do that on account of loss of customized data.
					}
				}
			}

			// Stylesheets
			$diff = array_diff_all(
				isset($com['designs']) ? $com['designs'] : array(),
				isset($old_com['designs']) ? $old_com['designs'] : array()
			);
			if ($diff['count'] > 0) {
				$default_dir = "./designs/{$css['stylesheet']}/";
				foreach ($diff as $handler => $files) {
					foreach ($com['designs'] as $file) {
						$dir1 = "{$tdir}designs/{$file}";
						if ($handler == DO_UPD_ADD) {
							$filesystem->unlink($default_dir.$file); // Delete old data (rename will fail if file exists)
							$filesystem->rename($dir1, $default_dir.$file);
						}
						elseif ($handler == DO_UPD_DEL) {
							$filesystem->unlink($default_dir.$file);
						}
					}
				}
				// Get non standard stylesheets. We have to copy them from the default... (safe mode workaround)
				$result = $db->query("SELECT DISTINCT stylesheet FROM {$db->pre}designs WHERE stylesheet != '{$design['stylesheet']}'",__LINE__,__FILE__);
				while ($css = $db->fetch_assoc($result)) {
					foreach ($diff as $handler => $files) {
						foreach ($com['designs'] as $file) {
							$dir2 = "./designs/{$css['stylesheet']}/{$file}";
							if ($handler == DO_UPD_ADD) {
								$filesystem->unlink($dir2);  // Delete old data (rename will fail if file exists)
								$filesystem->rename($default_dir.$file, $dir2);
							}
							elseif ($handler == DO_UPD_DEL) {
								$filesystem->unlink($dir2);
							}
							// Dont update equal files. Like above...
						}
					}
				}
			}

			// Language-Files
			$diff = array_diff_all(
				isset($com['language']) ? $com['language'] : array(),
				isset($old_com['language']) ? $old_com['language'] : array()
			);
			if ($diff['count'] > 0) {
				$codes = getLangCodesByDir($tdir.'language/');
				$langcodes = getLangCodes();
				foreach ($langcodes as $code => $lid) {
					$ldat = explode('_', $code);
					if (isset($codes[$ldat[0]])) {
						$count = count($codes[$ldat[0]]);
						if (in_array('', $codes[$ldat[0]])) {
							$count--;
						}
					}
					else {
						$count = -1;
					}
					$filesystem->mkdir("./language/{$lid}/modules/{$packageid}", 0777);
					if (isset($codes[$ldat[0]]) && !empty($ldat[1]) && in_array($ldat[1], $codes[$ldat[0]])) { // Nehme Original
						$src = $code;
					}
					elseif(isset($codes[$ldat[0]]) && in_array('', $codes[$ldat[0]])) { // Nehme gleichen Langcode, aber ohne Countrycode
						$src = $ldat[0];
					}
					elseif(isset($codes[$ldat[0]]) && $count > 0) { // Nehme gleichen Langcode, aber falchen Countrycode
						$src = $ldat[0].'_'.reset($codes[$ldat[0]]);
					}
					else { // Nehme Standard
						$src = '';
					}
					$src = iif(!empty($src), '/'.$src);
					$langcodes[$code] = array('lid' => $lid, 'src' => $src);
				}

				foreach ($diff as $handler => $files) {
					foreach($files as $file) {
						foreach ($langcodes as $code => $dat) {
							if (file_exists("{$tdir}/language/{$dat['src']}/{$file}") == false) {
								$dat['src'] = '';
							}
							if (!empty($dat['src'])) {
								$source = "{$tdir}/language/{$dat['src']}/{$file}";
							}
							else {
								$source = "{$tdir}/language/{$file}";
							}
							$target = "./language/{$dat['lid']}/modules/{$packageid}/{$file}";
							if ($handler == DO_UPD_ADD) {
								$filesystem->unlink($target);
								$filesystem->copy($source, $target);
							}
							elseif ($handler == DO_UPD_DEL) {
								$filesystem->unlink($target);
							}
							elseif ($handler == DO_UPD_EQU) {
								// Add new phrases, delete old phrases.
								// Update of existing phrases has to be done by the custom updater when its required.
								$l1 = arrayFromFile($source);
								$c = new manageconfig();
								$c->getdata($target, 'lang');
								$diff2 = array_diff_all(array_keys($l1), array_keys($c->data)); // Check the keys only
								if ($diff['count'] > 0) {
									foreach ($diff2 as $handler2 => $keys) {
										foreach($keys as $key) {
											if ($handler2 == DO_UPD_ADD) {
												$c->updateconfig($key, str, $l1[$key]);
											}
											elseif ($handler2 == DO_UPD_DEL) {
												$c->delete($key);
											}
										}
									}
									$c->savedata();
								}
							}
						}
					}
				}
			}

			$delobj = $scache->load('components');
			$delobj->delete();
		}
		// Components End

		// Plugins Start
		$result = $db->query("SELECT id FROM {$db->pre}plugins WHERE module = '{$packageid}' LIMIT 1", __LINE__, __FILE__);
		$do = null;
		if ($db->num_rows($result) == 0) {
			if (file_exists($tdir.'modules/plugin.ini')) {
				$do = DO_UPD_ADD; // Insert
			}
		}
		else {
			if (file_exists($tdir.'modules/plugin.ini')) {
				$do = DO_UPD_CHNG; // Update
			}
			else {
				$do = DO_UPD_DEL; // Delete
			}
		}


		$plug = $old_plug = array();
		if (file_exists($moddir.'plugin.ini')) {
			$old_plug = $myini->read($moddir.'plugin.ini');
		}
		if (file_exists($tdir.'plugin.ini')) {
			$plug = $myini->read($tdir.'plugin.ini');
		}


		// Todo
		if (isset($plug['language']) && count($plug['language']) > 0) {
			$keys = array_keys($plug);
			$codes = getLangCodesByKeys($keys);
			$langcodes = getLangCodes();
			foreach ($langcodes as $code => $lid) {
				$ldat = explode('_', $code);
				if (isset($codes[$ldat[0]])) {
					$count = count($codes[$ldat[0]]);
					if (in_array('', $codes[$ldat[0]])) {
						$count--;
					}
				}
				else {
					$count = -1;
				}
				if (isset($codes[$ldat[0]]) && !empty($ldat[1]) && in_array($ldat[1], $codes[$ldat[0]])) { // Nehme Original
					$src = 'language_'.$code;
				}
				elseif(isset($codes[$ldat[0]]) && in_array('', $codes[$ldat[0]])) { // Nehme gleichen Langcode, aber ohne Countrycode
					$src = 'language_'.$ldat[0];
				}
				elseif(isset($codes[$ldat[0]]) && $count > 0) { // Nehme gleichen Langcode, aber falchen Countrycode
					$src = 'language_'.$ldat[0].'_'.reset($codes[$ldat[0]]);
				}
				else { // Nehme Standard
					$src = 'language';
				}
				$c->getdata("language/{$lid}/modules.lng.php", 'lang');
				foreach ($plug[$src] as $varname => $text) {
					$c->updateconfig($varname, str, $text);
				}
				$c->savedata();
			}
		}
		// Todo End

		$diff = array_diff_all(
			isset($plug['php']) ? array_keys($plug['php']) : array(),
			isset($old_plug['php']) ? array_keys($old_plug['php']) : array(),
			true // Controll also the keys
		);
		if ($diff['count'] > 0) {
			$result = $db->query("SELECT position, id FROM {$db->pre}plugins WHERE module = '{$packageid}'");
			while ($row = $db->fetch_assoc($result)) {
				$plugs[$row['position']] = $row['id'];
			}
			foreach ($diff as $handler => $files) {
				foreach ($files as $hook => $plugfile) {
					if (isInvisibleHook($hook)) {
						continue;
					}
					$source = $tdir.'modules/'.$plugfile;
					$target = $moddir.$plugfile;
					if ($handler == DO_UPD_ADD) {
						$result = $db->query("SELECT MAX(ordering) AS maximum FROM {$db->pre}plugins WHERE position = '{$hook}'", __LINE__, __FILE__);
						$row = $db->fetch_assoc($result);
						$priority = $row['maximum']+1;
						$db->query("
						INSERT INTO {$db->pre}plugins
						(`name`,`module`,`ordering`,`required`,`position`)
						VALUES
						('{$plug['names'][$hook]}','{$packageid}','{$priority}','{$plug['required'][$hook]}','{$hook}')
						", __LINE__, __FILE__);
						if (file_exists($source)) { // Doesn't exist? => Already moved (or package not ok)
							$filesystem->unlink($target);
							$filesystem->rename($source, $target);
						}
					}
					elseif ($handler == DO_UPD_DEL) {
						$delete = true;
						foreach ($plug['php'] as $pos => $val) {
							if ($pos != $hook && $plugfile == $val) {
								$delete = false;
							}
						}
						if (file_exists($target) && $delete == true) {
							$filesystem->unlink($target);
						}

						$db->query("DELETE FROM {$db->pre}plugins WHERE id = '{$plugs[$hook]}' LIMIT 1", __LINE__, __FILE__);
						$db->query("DELETE FROM {$db->pre}menu WHERE module = '{$plugs[$hook]}'", __LINE__, __FILE__);
					}
					elseif ($handler == DO_UPD_EQU) {
						// ToDo
					}
					$filesystem->unlink('cache/modules/'.$plugins->_group($hook).'.php');
				}
			}
			$delobj = $scache->load('modules_navigation');
			$delobj->delete();
		}
		// Plugins End


		// Templates
		$templates = array_merge(
			isset($plug['template']) ? $plug['template'] : array(),
			isset($com['template']) ? $com['template'] : array()
		);
		$old_templates = array_merge(
			isset($old_plug['template']) ? $old_plug['template'] : array(),
			isset($old_com['template']) ? $old_com['template'] : array()
		);
		$tpldir = "templates/{$design['template']}/modules/{$packageid}/";
		if (count($templates) > 0) { // Add/update files
			$diff = array_diff_all($template, $old_templates);
			if ($diff['count'] > 0) {
				if (is_dir($tpldir)) {
					$filesystem->chmod($tpldir, 0777);
				}
				else {
					$filesystem->mkdir($tpldir, 0777);
				}
				$temptpldir = "{$tdir}templates/";
				foreach ($diff as $handler => $files) {
					foreach($files as $file) {
						if ($handler == DO_UPD_ADD) {
							$dir = dirname($tpldir.$file);
							if (!is_dir($dir)) {
								$filesystem->mkdir($dir, 0777);
							}
							else { // Delete old data (rename will fail if file exists)
								$filesystem->unlink($tpldir.$file);
							}
							$filesystem->rename($temptpldir.$file, $tpldir.$file);
						}
						elseif ($handler == DO_UPD_DEL) {
							$filesystem->unlink($tpldir.$file);
						}
						// Dont update equal files. Like above...
					}
				}
			}
		}
		else { // Delete files
			if (is_dir($tpldir)) {
				rmdirr($tpldir);
			}
		}

		// ToDo: Copy files (ini's)

		// Custom Updater
		$confirm = true;
		($code = $plugins->update($packageid)) ? eval($code) : null;

		rmdirr($tdir);

		unset($archive);
		if ($del > 0) {
			$filesystem->unlink($sourcefile);
		}
		if ($confirm) {
			echo head();
			ok('admin.php?action=packages&job=package_info&id='.$packageid, $lang->phrase('admin_packages_successfully_updated'));
		}
	}
}
elseif ($job == 'package_import') {
	echo head();
	$file = $gpc->get('file', str);
	?>
<form name="form" method="post" action="admin.php?action=packages&amp;job=package_import2" enctype="multipart/form-data">
 <table class="border" border="0" cellspacing="0" cellpadding="4" align="center">
  <tr>
   <td class="obox" colspan="2"><?php echo $lang->phrase('admin_packages_head_import_a_new_component'); ?></td>
  </tr>
  <tr>
   <td class="mbox" width="50%">
   	<?php
   		$max_filesize = formatFilesize(ini_maxupload());
   		echo $lang->phrase('admin_packages_import_upload_file');
   		echo '<br />';
   		echo '<span class="stext">'.$lang->phrase('admin_packages_import_text_upload_file_desc').'</span>';
   	?>
   	</td>
   <td class="mbox" width="50%"><input type="file" name="upload" size="40" /></td>
  </tr>
  <tr>
   <td class="mbox">
   		<?php
   			echo $lang->phrase('admin_packages_import_select_file');
   			echo '<br />';
   			echo '<span class="stext">'.$lang->phrase('admin_packages_import_select_file_desc').'</span>';
   		?>
   </td>
   <td class="mbox"><input type="text" name="server" size="50" value="<?php echo $file; ?>" /></td>
  </tr>
  <tr>
   <td class="mbox"><?php echo $lang->phrase('admin_packages_skip_version_check'); ?></td>
   <td class="mbox"><input type="checkbox" name="version" value="1" /></td>
  </tr>
  <tr>
   <td class="mbox"><?php echo $lang->phrase('admin_packages_delete_file_after_import'); ?></td>
   <td class="mbox"><input type="checkbox" name="delete" value="1" checked="checked" /></td>
  </tr>
  <tr>
   <td class="ubox" width="100%" colspan="2" align="center"><input type="submit" name="Submit" value="<?php echo $lang->phrase('admin_packages_button_upload'); ?>"></td>
  </tr>
 </table>
</form>
	<?php
	echo foot();
}
elseif ($job == 'package_import2') {
	echo head();

	$del = $gpc->get('delete', int);
	$versioncheck = $gpc->get('version', int);
	$server = $gpc->get('server', none);
	$inserterrors = array();

	$sourcefile = '';
	if (!empty($_FILES['upload']['name'])) {
		require("classes/class.upload.php");
		$dir = 'temp/';
		$my_uploader = new uploader();
		$my_uploader->file_types(array('zip'));
		$my_uploader->set_path($dir);
		$my_uploader->max_filesize(ini_maxupload());
		if ($my_uploader->upload('upload')) {
			if ($my_uploader->save_file()) {
				$sourcefile = $dir.$my_uploader->fileinfo('filename');
			}
		}
		if ($my_uploader->upload_failed()) {
			array_push($inserterrors,$my_uploader->get_error());
		}
	}
	elseif (file_exists($server)) {
		$ext = get_extension($server);
		if ($ext == 'zip') {
			$sourcefile = $server;
		}
		else {
			$inserterrors[] = $lang->phrase('admin_packages_err_selected_file_is_not_a_zipfile');
		}
	}
	if (!file_exists($sourcefile)) {
		$inserterrors[] = $lang->phrase('admin_packages_err_selected_file_does_not_exist');
	}
	if (count($inserterrors) > 0) {
		error('admin.php?action=designs&job=package_import', $inserterrors);
	}
	else {
		$c = new manageconfig();

		$tdir = "temp/".md5(microtime()).'/';
		$filesystem->mkdir($tdir, 0777);
		if (!is_dir($tdir)) {
			error('admin.php?action=packages&job=package_import', $lang->phrase('admin_packages_err_temporary_directory_could_not_be_created'));
		}
		include('classes/class.zip.php');
		$archive = new PclZip($sourcefile);
		if ($archive->extract(PCLZIP_OPT_PATH, $tdir) == 0) {
			error('admin.php?action=packages&job=package_import', $archive->errorInfo(true));
		}

		if (file_exists($tdir.'modules/package.ini')) {
			$package = $myini->read($tdir.'modules/package.ini');
			if ($versioncheck != 1) {
				if (!empty($package['info']['min_version']) && version_compare($config['version'], $package['info']['min_version'], '<')) {
					error('admin.php?action=packages&job=package_import', $lang->phrase('admin_packages_err_required_min_version'));
				}
				if (!empty($package['info']['max_version']) && version_compare($config['version'], $package['info']['max_version'], '>')) {
					error('admin.php?action=packages&job=package_import', $lang->phrase('admin_packages_err_required_max_version'));
				}
			}
			$package = $gpc->save_str($package);
			if (!isset($package['core'])) {
				$package['info']['core'] = 0;
			}
		}
		else {
			error('admin.php?action=packages&job=package_import', $lang->phrase('admin_packages_err_package_ini_does_not_exist'));
		}

		$result = $db->query("SELECT id FROM {$db->pre}packages WHERE internal = '{$package['info']['internal']}'", __LINE__, __FILE__);
		if ($db->num_rows($result) > 0 && $package['info']['multiple'] == 0) {
			error('admin.php?action=packages&job=package_import', $lang->phrase('admin_packages_a_package_with_this_name_does_allready_exist'));
		}
		if (isset($package['dependency']) && count($package['dependency']) > 0) {
			$result = $db->query("SELECT internal FROM {$db->pre}packages", __LINE__, __FILE__);
			$internals = array();
			while ($row = $db->fetch_assoc($result)) {
				$internals[] = $row['internal'];
			}
			$missing = array_diff($package['dependency'], $internals);
			if (count($missing) > 0) {
				error('admin.php?action=packages&job=package_import', $lang->phrase('admin_packages_dependency_missing').implode(', ', $missing), 60000);
			}
		}

		$db->query("INSERT INTO {$db->pre}packages (title, version, internal, core) VALUES ('{$package['info']['title']}', '{$package['info']['version']}', '{$package['info']['internal']}', '{$package['info']['core']}')", __LINE__, __FILE__);
		$packageid = $db->insert_id();

		$filesystem->mkdir("./modules/{$packageid}", 0777);
		mover("{$tdir}modules", "./modules/{$packageid}");
		$moddir = "./modules/{$packageid}/";

		if (!empty($package['config']['title'])) {
			if (!isset($package['config']['description'])) {
				$package['config']['description'] = '';
			}
			$db->query("INSERT INTO {$db->pre}settings_groups (title, name, description) VALUES ('{$package['config']['title']}', '{$package['info']['internal']}', '{$package['config']['description']}')", __LINE__, __FILE__);
			$sg = $db->insert_id();
			foreach ($package as $section => $values) {
				if (substr($section, 0, 8) == 'setting_') {
					$name = $gpc->save_str(substr($section, 8));
					$db->query("
					INSERT INTO {$db->pre}settings (name, title, description, type, optionscode, value, sgroup)
					VALUES ('{$name}', '{$values['title']}', '{$values['description']}', '{$values['type']}', '{$values['optionscode']}', '{$values['value']}', '{$sg}')
					", __LINE__, __FILE__);

					$c->getdata();
					$c->updateconfig(array($package['info']['internal'], $name), none, $values['value']);
					$c->savedata();
				}
			}
		}

		$result = $db->query("SELECT template, stylesheet, images FROM {$db->pre}designs WHERE id = '{$config['templatedir']}'",__LINE__,__FILE__);
		$design = $db->fetch_assoc($result);

		if (file_exists($moddir.'component.ini')) {
			$com = $myini->read($moddir.'component.ini');
			if (isset($com['info']) && count($com['info']) > 0) {
				$com['info'] = $gpc->save_str($com['info']);

				if (!isset($com['module']['frontpage'])) {
					$com['module']['frontpage'] = '';
				}

				$db->query("INSERT INTO {$db->pre}component (file, package, required) VALUES ('".$gpc->save_str($com['module']['frontpage'])."', '{$packageid}', '{$com['info']['required']}')", __LINE__, __FILE__);
				$comid = $db->insert_id();

				if (isset($com['images']) && count($com['images']) > 0) {
					foreach ($com['images'] as $file) {
						$filesystem->rename("{$tdir}images/{$file}", "./images/{$design['images']}/{$file}");
					}
				}

				if (isset($com['designs']) && count($com['designs']) > 0) {
					$stdcssdir = "./designs/{$design['stylesheet']}/";
					foreach ($com['designs'] as $file) {
						$filesystem->rename("{$tdir}designs/{$file}", $stdcssdir.$file);
					}
					$result = $db->query("SELECT DISTINCT stylesheet FROM {$db->pre}designs WHERE stylesheet != '{$design['stylesheet']}'",__LINE__,__FILE__);
					while ($css = $db->fetch_assoc($result)) {
						foreach ($com['designs'] as $file) {
							$filesystem->copy($stdcssdir.$file, "./designs/{$css['stylesheet']}/{$file}");
						}
					}
				}

				if (isset($com['language']) && count($com['language']) > 0) {
					$codes = getLangCodesByDir($tdir.'language/');
					$langcodes = getLangCodes();
					foreach ($langcodes as $code => $lid) {
						$ldat = explode('_', $code);
						if (isset($codes[$ldat[0]])) {
							$count = count($codes[$ldat[0]]);
							if (in_array('', $codes[$ldat[0]])) {
								$count--;
							}
						}
						else {
							$count = -1;
						}
						$filesystem->mkdir("./language/{$lid}/modules/{$packageid}", 0777);
						if (isset($codes[$ldat[0]]) && !empty($ldat[1]) && in_array($ldat[1], $codes[$ldat[0]])) { // Nehme Original
							$src = $code;
						}
						elseif(isset($codes[$ldat[0]]) && in_array('', $codes[$ldat[0]])) { // Nehme gleichen Langcode, aber ohne Countrycode
							$src = $ldat[0];
						}
						elseif(isset($codes[$ldat[0]]) && $count > 0) { // Nehme gleichen Langcode, aber falchen Countrycode
							$src = $ldat[0].'_'.reset($codes[$ldat[0]]);
						}
						else { // Nehme Standard
							$src = '';
						}
						$src = iif(!empty($src), '/'.$src);
						foreach ($com['language'] as $file) {
							if (file_exists("{$tdir}/language/{$src}/{$file}") == false) {
								$src = '';
							}
							if (!empty($src)) {
								$src = '/'.$src;
							}
							// ToDo: Move it first to standard dir, then copy it from there
							$filesystem->copy("{$tdir}/language{$src}/{$file}", "./language/{$lid}/modules/{$packageid}/{$file}");
						}
					}
				}

				$delobj = $scache->load('components');
				$delobj->delete();
			}
		}

		if (file_exists($moddir.'plugin.ini')) {
			$plug = $myini->read($moddir.'plugin.ini');

			if (isset($plug['language']) && count($plug['language']) > 0) {

				$codes = array();
				$keys = array_keys($plug);
				foreach ($keys as $entry) {
				   	if (preg_match('~language_(\w{2})_?(\w{0,2})~i', $entry, $code)) {
				   		if (!isset($codes[$code[1]])) {
				   			$codes[$code[1]] = array();
				   		}
				   		if (isset($code[2])) {
				   			$codes[$code[1]][] = $code[2];
				   		}
				   		else {
				   			if (!in_array('', $codes[$code[1]])) {
				   				$codes[$code[1]][] = '';
				   			}
				   		}
				   	}
				}
				$langcodes = getLangCodes();
				foreach ($langcodes as $code => $lid) {
					$ldat = explode('_', $code);
					if (isset($codes[$ldat[0]])) {
						$count = count($codes[$ldat[0]]);
						if (in_array('', $codes[$ldat[0]])) {
							$count--;
						}
					}
					else {
						$count = -1;
					}
					if (isset($codes[$ldat[0]]) && !empty($ldat[1]) && in_array($ldat[1], $codes[$ldat[0]])) { // Nehme Original
						$src = 'language_'.$code;
					}
					elseif(isset($codes[$ldat[0]]) && in_array('', $codes[$ldat[0]])) { // Nehme gleichen Langcode, aber ohne Countrycode
						$src = 'language_'.$ldat[0];
					}
					elseif(isset($codes[$ldat[0]]) && $count > 0) { // Nehme gleichen Langcode, aber falchen Countrycode
						$src = 'language_'.$ldat[0].'_'.reset($codes[$ldat[0]]);
					}
					else { // Nehme Standard
						$src = 'language';
					}
					$c->getdata("language/{$lid}/modules.lng.php", 'lang');
					foreach ($plug[$src] as $varname => $text) {
						$c->updateconfig($varname, str, $text);
					}
					$c->savedata();
				}
			}

			if (isset($plug['php']) && count($plug['php']) > 0) {
				foreach ($plug['php'] as $hook => $plugfile) {
					if (isInvisibleHook($hook)) {
						continue;
					}
					$result = $db->query("SELECT MAX(ordering) AS maximum FROM {$db->pre}plugins WHERE position = '{$hook}'", __LINE__, __FILE__);
					$row = $db->fetch_assoc($result);
					$priority = $row['maximum']+1;
					$db->query("
					INSERT INTO {$db->pre}plugins
					(`name`,`module`,`ordering`,`required`,`position`)
					VALUES
					('{$plug['names'][$hook]}','{$packageid}','{$priority}','{$plug['required'][$hook]}','{$hook}')
					", __LINE__, __FILE__);
					$filesystem->unlink('cache/modules/'.$plugins->_group($hook).'.php');
				}
			}
		}

		// Templates
		$templates = array_merge(
			isset($plug['template']) ? $plug['template'] : array(),
			isset($com['template']) ? $com['template'] : array()
		);
		if (count($templates) > 0) {
			$tpldir = "templates/{$design['template']}/modules/{$packageid}/";
			if (is_dir($tpldir)) {
				$filesystem->chmod($tpldir, 0777);
			}
			else {
				$filesystem->mkdir($tpldir, 0777);
			}
			$temptpldir = "{$tdir}templates/";
			mover($temptpldir, $tpldir);
		}

		// Custom Installer
		$confirm = true;
		($code = $plugins->install($packageid)) ? eval($code) : null;

		rmdirr($tdir);

		unset($archive);
		if ($del > 0) {
			$filesystem->unlink($sourcefile);
		}
		if ($confirm) {
			echo head();
			ok('admin.php?action=packages&job=package_info&id='.$packageid, $lang->phrase('admin_packages_ok_package_successfully_imported'));
		}

	}
}
elseif ($job == 'package_export') {
	$id = $gpc->get('id', int);
	$result = $db->query("SELECT id, internal FROM {$db->pre}packages WHERE id = '{$id}' LIMIT 1", __LINE__, __FILE__);
	if ($db->num_rows($result) != 1) {
		echo head();
		error('admin.php?action=packages&job=package', $lang->phrase('admin_packages_err_package_does_not_exist'));
	}
	$data = $db->fetch_assoc($result);

	// Save all languages to plugin.ini
	$pini = "modules/{$data['id']}/plugin.ini";
	if (file_exists($pini)) {
		$ini = $myini->read($pini);
		if (!isset($ini['language']) || !is_array($ini['language']) || (is_array($ini['language']) && count($ini['language']) == 0)) {
			$ini['language'] = array();
		}
		$has_plugins = true;
	}
	else {
		$has_plugins = false;
	}
	$cini = "modules/{$data['id']}/component.ini";
	if (file_exists($cini)) {
		$ini2 = $myini->read($cini);
		$has_component = true;
	}
	else {
		$has_component = false;
	}

	$dirs = array();
	$langcodes = getLangCodes();
	foreach ($langcodes as $code => $lid) {
		if ($has_plugins == true) {
			// Plugins
			$langdata = return_array('modules', $lid);
			$langdata = array_intersect_key($langdata, $ini['language']);
			if ($lid == $config['langdir']) {
				$ini['language'] = $langdata;
			}
			else {
				$ini['language_'.$code] = $langdata;
			}
		}
		// Component
		if ($has_component == true) {
			$lngdir = "language/{$lid}/modules/{$id}/";
			if (is_dir($lngdir)) {
				if ($lid != $config['langdir']) {
					$key = 'language_'.$code;
					$ini2[$key] = array();
					foreach ($ini2['language'] as $lngfile) {
						if (file_exists($lngdir.$lngfile)) {
							$ini2[$key][] = $lngfile;
						}
					}
				}
				else {
					$key = 'language';
				}
				$dirs[] = array(
					'orig' => $lngdir,
					'new' => str_replace('_', '/', $key).'/'
				);
			}
		}
	}
	if ($has_plugins == true) {
		$myini->write($pini, $ini);
	}
	if ($has_component == true) {
		$myini->write($cini, $ini2);
	}

	// Determine standard template pack
	$loaddesign_obj = $scache->load('loaddesign');
	$cache = $loaddesign_obj->get();
	$design = $cache[$config['templatedir']];

	// ZIP-File
	$tempdir = "temp/";
	$file = $data['internal'].'.zip';
	require_once('classes/class.zip.php');
	$error = array();
	$archive = new PclZip($tempdir.$file);

	// Add modules directory
	$v_list = $archive->create(
		"modules/{$id}/",
		PCLZIP_OPT_REMOVE_PATH, "modules/{$id}/",
		PCLZIP_OPT_ADD_PATH, "modules/"
	);
	if ($v_list == 0) {
		$error[] = $archive->errorInfo(true);
	}

	$tpl_orig_path = "templates/{$design['template']}/modules/{$id}/";
	// Add template directory
	if (is_dir($tpl_orig_path) && count($error) == 0) {
		$archive = new PclZip($tempdir.$file);
		$v_list = $archive->add(
			$tpl_orig_path,
			PCLZIP_OPT_REMOVE_PATH, $tpl_orig_path,
			PCLZIP_OPT_ADD_PATH, "templates/"
		);
		if ($v_list == 0) {
			$error[] = $archive->errorInfo(true);
		}
	}

	// Add languages
	if (count($error) == 0 && $has_component == true) {
		foreach ($dirs as $dir) {
			$v_list = $archive->add(
				$dir['orig'],
				PCLZIP_OPT_REMOVE_PATH, $dir['orig'],
				PCLZIP_OPT_ADD_PATH, $dir['new']
			);
			if ($v_list == 0) {
				$error[] = $archive->errorInfo(true);
			}
		}
	}

	// Add images
	if (count($error) == 0 && $has_component == true) {
		$files = array();
		$dir = "images/{$design['images']}/";
		if (isset($ini2['images']) && count($ini2['images']) > 0) {
			foreach ($ini2['images'] as $data) {
				$files[] = $dir.$data;
			}
			$v_list = $archive->add(
				$files,
				PCLZIP_OPT_REMOVE_PATH, $dir,
				PCLZIP_OPT_ADD_PATH, 'images/'
			);
			if ($v_list == 0) {
				$error[] = $archive->errorInfo(true);
			}
		}
	}


	// Add styles
	if (count($error) == 0 && $has_component == true) {
		$files = array();
		$dir = "designs/{$design['stylesheet']}/";
		if (isset($ini2['designs']) && count($ini2['designs']) > 0) {
			foreach ($ini2['designs'] as $data) {
				$files[] = $dir.$data;
			}
			$v_list = $archive->add(
				$files,
				PCLZIP_OPT_REMOVE_PATH, $dir,
				PCLZIP_OPT_ADD_PATH, 'designs/'
			);
			if ($v_list == 0) {
				$error[] = $archive->errorInfo(true);
			}
		}
	}

	if (count($error) > 0) {
		echo head();
		unset($archive);
		$filesystem->unlink($tempdir.$file);
		error('admin.php?action=packages&job=package_info&id='.$id, $error);
	}
	else {
		viscacha_header('Content-Type: application/zip');
		viscacha_header('Content-Disposition: attachment; filename="'.$file.'"');
		viscacha_header('Content-Length: '.filesize($tempdir.$file));
		readfile($tempdir.$file);
		unset($archive);
		$filesystem->unlink($tempdir.$file);
	}
}
elseif ($job == 'package_delete') {
	echo head();
	$id = $gpc->get('id', int);
	$result = $db->query("SELECT id, core, internal FROM {$db->pre}packages WHERE id = '{$id}' LIMIT 1", __LINE__, __FILE__);
	$data = $db->fetch_assoc($result);
	if ($db->num_rows($result) == 0) {
		error('admin.php?action=packages&job=package', $lang->phrase('admin_packages_err_package_does_not_exist'));
	}
	elseif ($data['core'] == '1') {
		error('admin.php?action=packages&job=package', $lang->phrase('admin_packages_err_this_is_a_core_package_and_cannot_be_deleted'));
	}
	else {
		$result2 = $db->query("SELECT id, title, internal FROM {$db->pre}packages WHERE id != '{$id}'", __LINE__, __FILE__);
		while ($row = $db->fetch_assoc($result)) {
			$pack = $myini->read("modules/{$row['id']}/package.ini");
			if (isset($pack['dependency']) && in_array($data['internal'], $pack['dependency'])) {
				error('admin.php?action=packages&job=package_info&id='.$data['id'], $lang->phrase('admin_packages_err_package_required'));
			}
		}
		?>
		<table class="border" border="0" cellspacing="0" cellpadding="4" align="center">
		<tr><td class="obox"><?php echo $lang->phrase('admin_packages_head_delete_package'); ?></td></tr>
		<tr><td class="mbox">
		<p align="center"><?php echo $lang->phrase('admin_packages_do_you_really_want_to_delete_this_package'); ?></p>
		<p align="center">
		<a href="admin.php?action=packages&job=package_delete2&id=<?php echo $data['id']; ?>"><img border="0" alt="Yes" src="admin/html/images/yes.gif"> <?php echo $lang->phrase('admin_packages_yes'); ?></a>
		&nbsp&nbsp;&nbsp;&nbsp&nbsp;&nbsp;
		<a href="javascript: history.back(-1);"><img border="0" alt="No" src="admin/html/images/no.gif"> <?php echo $lang->phrase('admin_packages_no'); ?></a>
		</p>
		</td></tr>
		</table>
		<?php
		echo foot();
	}
}
elseif ($job == 'package_delete2') {
	$id = $gpc->get('id', int);
	$result = $db->query("SELECT id, core, internal FROM {$db->pre}packages WHERE id = '{$id}' LIMIT 1", __LINE__, __FILE__);
	$package = $db->fetch_assoc($result);
	if ($db->num_rows($result) == 0) {
		echo head();
		error('admin.php?action=packages&job=package', $lang->phrase('admin_packages_err_package_not_found'));
	}
	elseif ($package['core'] == '1') {
		echo head();
		error('admin.php?action=packages&job=package', $lang->phrase('admin_packages_err_this_is_a_core_package_and_cannot_be_deleted'));
	}
	else {
		$result2 = $db->query("SELECT id, title, internal FROM {$db->pre}packages WHERE id != '{$id}'", __LINE__, __FILE__);
		while ($row = $db->fetch_assoc($result)) {
			$pack = $myini->read("modules/{$row['id']}/package.ini");
			if (isset($pack['dependency']) && in_array($package['internal'], $pack['dependency'])) {
				echo head();
				error('admin.php?action=packages&job=package_info&id='.$package['id'], $lang->phrase('admin_packages_err_package_required'));
			}
		}
		$c = new manageconfig();

		$dir = "modules/{$package['id']}/";
		if (file_exists("{$dir}component.ini")) {
			$com = $myini->read("{$dir}component.ini");
		}
		if (file_exists("{$dir}plugin.ini")) {
			$plug = $myini->read("{$dir}plugin.ini");
		}

		$confirm = true;
		($code = $plugins->uninstall($package['id'])) ? eval($code) : null;

		$db->query("DELETE FROM {$db->pre}plugins WHERE module = '{$package['id']}'", __LINE__, __FILE__);
		$db->query("DELETE FROM {$db->pre}component WHERE package = '{$package['id']}' LIMIT 1", __LINE__, __FILE__);
		$db->query("DELETE FROM {$db->pre}packages WHERE id = '{$package['id']}' LIMIT 1", __LINE__, __FILE__);
		// Delete references in navigation aswell
		$db->query("DELETE FROM {$db->pre}menu WHERE module = '{$package['id']}'", __LINE__, __FILE__);
		// Delete settings
		$result = $db->query("
		SELECT g.id, s.name, g.name AS groupname
		FROM {$db->pre}settings AS s
			LEFT JOIN {$db->pre}settings_groups AS g ON s.sgroup = g.id
		WHERE g.name = '{$package['internal']}'");
		while ($row = $db->fetch_assoc($result)) {
			$c->getdata();
			$c->delete(array($row['groupname'], $row['name']));
			$c->savedata();
		}
		$result = $db->query("SELECT id FROM {$db->pre}settings_groups WHERE name = '{$package['internal']}'");
		if ($db->num_rows($result) > 0) {
			while ($row = $db->fetch_assoc($result)) {
				$db->query("DELETE FROM {$db->pre}settings WHERE sgroup = '{$row['id']}'");
				$db->query("DELETE FROM {$db->pre}settings_groups WHERE id = '{$row['id']}'");
			}
		}

		$result = $db->query("SELECT * FROM {$db->pre}plugins WHERE module = '{$package['id']}' GROUP BY position", __LINE__, __FILE__);
		while ($data = $db->fetch_assoc($result)) {
			$filesystem->unlink('cache/modules/'.$plugins->_group($data['position']).'.php');
		}
		$delobj = $scache->load('components');
		$delobj->delete();

		$cache = array();
		$result = $db->query("SELECT template, stylesheet, images FROM {$db->pre}designs",__LINE__,__FILE__);
		$design = $db->fetch_assoc($result);
		while ($row = $db->fetch_assoc($design)) {
			$cache[] = $row;
		}
		// Delete templates
		foreach ($cache as $row) {
			$tpldir = "templates/{$row['template']}/modules/{$package['id']}/";
			if (file_exists($tpldir)) {
				rmdirr($tpldir);
			}
		}
		// Delete phrases
		$result = $db->query("SELECT id FROM {$db->pre}language",__LINE__,__FILE__);
		$cache2 = array();
		while ($language = $db->fetch_assoc($result)) {
			$cache2[] = $language['id'];
		}
		if (isset($plug['language']) && count($plug['language']) > 0) {
			foreach ($cache2 as $lid) {
				$path = "language/{$lid}/modules.lng.php";
				if (file_exists($path)) {
					$c->getdata($path, 'lang');
					foreach ($plug['language'] as $phrase => $value) {
						$c->delete($phrase);
					}
					$c->savedata();
				}
			}
		}
		// Delete language files
		foreach ($cache2 as $lid) {
			rmdirr("./language/{$lid}/modules/{$package['id']}");
		}
		// Delete images
		if (isset($com['images']) && count($com['images']) > 0) {
			foreach ($cache as $design) {
				foreach ($com['images'] as $file) {
					$filesystem->unlink("./images/{$design['images']}/{$file}");
				}
			}
		}
		if (isset($com['designs']) && count($com['designs']) > 0) {
			foreach ($cache as $design) {
				foreach ($com['designs'] as $file) {
					$filesystem->unlink("./designs/{$design['stylesheet']}/{$file}");
				}
			}
		}
		// Delete modules
		if (file_exists($dir)) {
			rmdirr($dir);
		}

		if ($confirm == true) {
			echo head();
			ok('admin.php?action=packages&job=package', $lang->phrase('admin_packages_ok_package_successfully_deleted'));
		}
	}
}
elseif ($job == 'package_edit') {
	$id = $gpc->get('id', int);
	$result = $db->query("SELECT * FROM {$db->pre}packages WHERE id = '{$id}'", __LINE__, __FILE__);
	$row = $gpc->prepare($db->fetch_assoc($result));

	$ini = $myini->read("modules/{$row['id']}/package.ini");

	$dependency = false;
	$result = $db->query("SELECT id, title, internal FROM {$db->pre}packages WHERE id != '{$id}'", __LINE__, __FILE__);
	$depend_packs = array();
	while ($row2 = $db->fetch_assoc($result)) {
		$depend_packs[] = $row2;
		if ($row['active'] == 1) { // Verhindere nur das deaktivieren...
			$pack = $myini->read("modules/{$row2['id']}/package.ini");
			if (isset($pack['dependency']) && in_array($row['internal'], $pack['dependency'])) {
				$dependency = true;
			}
		}
	}
	if (!isset($ini['dependency']) || count($ini['dependency']) == 0) {
		$ini['dependency'] = array();
	}

	echo head();
	?>
	<form method="post" action="admin.php?action=packages&amp;job=package_edit2&amp;id=<?php echo $row['id']; ?>">
	<table class="border" border="0" cellspacing="0" cellpadding="4" align="center">
	 <tr>
	  <td class="obox" colspan="2">
	  	<?php echo $lang->phrase('admin_packages_head_edit_the_package_foo'); ?>
	  </td>
	 </tr>
	 <tr class="mbox">
	  <td><?php echo $lang->phrase('admin_packages_edit_title'); ?><br /><span class="stext"><?php echo $lang->phrase('admin_packages_edit_title_text'); ?></span></td>
	  <td><input type="text" name="title" size="60" value="<?php echo $row['title']; ?>" /></td>
	 </tr>
	 <?php if ($row['core'] != '1' && $dependency == false) { ?>
	 <tr class="mbox">
	  <td><?php echo $lang->phrase('admin_packages_edit_active'); ?></td>
	  <td><input type="checkbox" name="active" value="1"<?php echo iif($row['active'] == 1, ' checked="checked"'); ?> /></td>
	 </tr>
	 <?php } ?>
	 <tr class="mbox">
	  <td><?php echo $lang->phrase('admin_packages_info_description'); ?><br /><span class="stext"><?php echo $lang->phrase('admin_packages_edit_optional'); ?></span></td>
	  <td><textarea cols="60" rows="4" name="summary"><?php echo $ini['info']['summary']; ?></textarea></td>
	 </tr>
	 <tr class="mbox">
	  <td><?php echo $lang->phrase('admin_packages_info_version'); ?><br /><span class="stext"><?php echo $lang->phrase('admin_packages_edit_optional'); ?></span></td>
	  <td><input type="text" name="version" size="60" value="<?php echo $row['version']; ?>" /></td>
	 </tr>
	 <tr class="mbox">
	  <td><?php echo $lang->phrase('admin_packages_edit_minimum_viscacha_version'); ?><br /><span class="stext"><?php echo $lang->phrase('admin_packages_edit_optional'); ?></span></td>
	  <td><input type="text" name="min_version" size="60" value="<?php echo $ini['info']['min_version']; ?>" /></td>
	 </tr>
	 <tr class="mbox">
	  <td><?php echo $lang->phrase('admin_packages_edit_maximum_viscacha_version'); ?><br /><span class="stext"><?php echo $lang->phrase('admin_packages_edit_optional'); ?></span></td>
	  <td><input type="text" name="max_version" size="60" value="<?php echo $ini['info']['max_version']; ?>" /></td>
	 </tr>
	 <tr class="mbox">
	  <td><?php echo $lang->phrase('admin_packages_dependency_label'); ?><br /><span class="stext"><?php echo $lang->phrase('admin_packages_dependency_info'); ?></span></td>
	  <td>
	  	<select name="dependency[]" multiple="multiple" size="5">
	  		<?php foreach ($depend_packs as $d) { ?>
	  		<option value="<?php echo $d['internal']; ?>"<?php echo iif(in_array($d['internal'], $ini['dependency']), ' selected="selected"'); ?>><?php echo $d['title']; ?> (<?php echo $d['internal']; ?>)</option>
	  		<?php } ?>
	  	</select>
	  </td>
	 </tr>
	 <tr class="mbox">
	  <td><?php echo $lang->phrase('admin_packages_info_copyright'); ?><br /><span class="stext"><?php echo $lang->phrase('admin_packages_edit_optional'); ?></span></td>
	  <td><input type="text" name="copyright" size="60" value="<?php echo $ini['info']['copyright']; ?>" /></td>
	 </tr>
	 <tr class="mbox">
	  <td><?php echo $lang->phrase('admin_packages_einfo_license'); ?><br /><span class="stext"><?php echo $lang->phrase('admin_packages_edit_optional'); ?></span></td>
	  <td><input type="text" name="license" size="60" value="<?php echo $ini['info']['license']; ?>" /></td>
	 </tr>
	 <tr class="mbox">
	  <td><?php echo $lang->phrase('admin_packages_edit_url_homepage'); ?><br /><span class="stext"><?php echo $lang->phrase('admin_packages_edit_optional'); ?></span></td>
	  <td><input type="text" name="url" size="60" value="<?php echo $ini['info']['url']; ?>" /></td>
	 </tr>
	 <tr>
	  <td class="ubox" colspan="2" align="center"><input type="submit" value="<?php echo $lang->phrase('admin_packages_button_edit_save_your_changes'); ?>" /> <?php echo $lang->phrase('admin_packages_edit_before_working_on_the_settings_below'); ?></td>
	 </tr>
	</table>
	</form>
	<br class="minibr" />
	<?php
	$settings = $sg = array();
	$result = $db->query("SELECT id, title FROM {$db->pre}settings_groups WHERE name = '{$ini['info']['internal']}' LIMIT 1");
	if ($db->num_rows($result) > 0) {
		$sg = $db->fetch_assoc($result);
		$result = $db->query("SELECT name, title, sgroup FROM {$db->pre}settings WHERE sgroup = '{$sg['id']}' ORDER BY name", __LINE__, __FILE__);
		while ($row2 = $db->fetch_assoc($result)) {
			$settings[] = $row2;
		}
	}
	?>
	 <table class="border" border="0" cellspacing="0" cellpadding="4" align="center">
	  <tr>
	   <td class="obox" colspan="4">
	   <span class="right">
	   <?php if (count($sg) > 0) { ?>
	   <a class="button" href="admin.php?action=settings&amp;job=custom&amp;id=<?php echo $sg['id']; ?>&amp;package=<?php echo $row['id']; ?>"><?php echo $lang->phrase('admin_packages_info_change_settings'); ?></a>
	   <a class="button" href="admin.php?action=settings&amp;job=new&amp;package=<?php echo $row['id']; ?>"><?php echo $lang->phrase('admin_packages_conf_add_a_new_setting'); ?></a>
	   <a class="button" href="admin.php?action=settings&amp;job=delete_group&amp;id=<?php echo $sg['id']; ?>&amp;package=<?php echo $row['id']; ?>"><?php echo $lang->phrase('admin_packages_edit_delete_all_settings'); ?></a>
	   <?php } ?>
	   </span>
	   <?php echo $lang->phrase('admin_packages_info_configuration'); ?>
	   </td>
	  </tr>
	  <?php if (is_array($settings) && count($settings) > 0) { ?>
	  <tr class="ubox">
	   <td width="50%"><?php echo $lang->phrase('admin_packages_th_title'); ?></td>
	   <td width="30%"><?php echo $lang->phrase('admin_packages_info_internal_name'); ?></td>
	   <td width="20%"><?php echo $lang->phrase('admin_packages_delete'); ?></td>
	  </tr>
	  <?php foreach ($settings as $setting) { ?>
	  <tr class="mbox">
		<td><?php echo $setting['title']; ?></td>
		<td class="monospace"><?php echo $setting['name']; ?></td>
	  	<td><a class="button" href="admin.php?action=settings&job=delete&name=<?php echo $setting['name']; ?>&id=<?php echo $setting['sgroup']; ?>&amp;package=<?php echo $row['id']; ?>"><?php echo $lang->phrase('admin_packages_conf_delete_setting'); ?></a></td>
	  </tr>
	  <?php } } else { ?>
		<tr class="mbox">
			<td colspan="4">
				<?php echo $lang->phrase('admin_packages_info_no_settings_specified_for_this_package'); ?>&nbsp;&nbsp;&nbsp;&nbsp;
				<?php if (count($sg) == 0) { ?>
				<a class="button" href="admin.php?action=settings&amp;job=new_group&amp;package=<?php echo $row['id']; ?>"><?php echo $lang->phrase('admin_packages_conf_add_a_new_group_for_settings'); ?></a>
				<?php } else { ?>
				<a class="button" href="admin.php?action=settings&amp;job=new&amp;package=<?php echo $row['id']; ?>"><?php echo $lang->phrase('admin_packages_conf_add_a_new_setting'); ?></a>
				<?php } ?>
			</td>
		</tr>
	  <?php } ?>
	 </table>
	<?php
	echo foot();
}
elseif ($job == 'package_edit2') {
	echo head();
	$id = $gpc->get('id', int);
	$result = $db->query("SELECT id, core FROM {$db->pre}packages WHERE id = '{$id}'", __LINE__, __FILE__);
	if ($db->num_rows($result) == 0) {
		error('admin.php?action=packages&job=package', $lang->phrase('admin_packages_err_could_not_find_a_package_with_this_id'));
	}
	$row = $db->fetch_assoc($result);
	if ($row['core'] != '1') { // ToDo: Add Dependency check
		$active = $gpc->get('active', int);
	}
	else {
		$active = 1;
	}
	$title = $gpc->get('title', none);
	$summary = $gpc->get('summary', none);
	$version = $gpc->get('version', none);
	$copyright = $gpc->get('copyright', none);
	$license = $gpc->get('license', none);
	$max = $gpc->get('max_version', none);
	$min = $gpc->get('min_version', none);
	$url = $gpc->get('url', none);
	$dependency = $gpc->get('dependency', arr_none);

	if (strlen($title) < 4) {
		error('admin.php?action=packages&job=package_edit&id='.$id, $lang->phrase('admin_packages_err_minimum_number_of_characters_for_title'));
	}
	elseif (strlen($title) > 200) {
		error('admin.php?action=packages&job=package_edit&id='.$id, $lang->phrase('admin_packages_err_maximum_number_of_characters_for_title'));
	}

	$dbtitle = $gpc->save_str($title);
	$dbversion = $gpc->save_str($version);
	$db->query("UPDATE {$db->pre}packages SET `title` = '{$dbtitle}', `version` = '{$dbversion}', `active` = '{$active}' WHERE id = '{$id}'", __LINE__, __FILE__);

	$ini = $myini->read("modules/{$id}/package.ini");
	$ini['info']['title'] = $title;
	$ini['info']['version'] = $version;
	$ini['info']['copyright'] = $copyright;
	$ini['info']['summary'] = $summary;
	$ini['info']['min_version'] = $min;
	$ini['info']['max_version'] = $max;
	$ini['info']['license'] = $license;
	$ini['info']['url'] = $url;
	$ini['dependency'] = $dependency;
	$filesystem->chmod("modules/{$id}/package.ini", 0666);
	$myini->write("modules/{$id}/package.ini", $ini);


	ok('admin.php?action=packages&job=package_info&id='.$id, $lang->phrase('admin_packages_ok_package_successfully_edited'));
}
elseif ($job == 'package_info') {
	echo head();
	$id = $gpc->get('id', int);

	$result = $db->query("SELECT * FROM {$db->pre}packages WHERE id = '{$id}' LIMIT 1", __LINE__, __FILE__);
	$package = $db->fetch_assoc($result);
	$package_ini = $myini->read("modules/{$package['id']}/package.ini");

	$result = $db->query("SELECT * FROM {$db->pre}plugins WHERE module = '{$package['id']}'", __LINE__, __FILE__);
	$modules = array();
	while ($row = $db->fetch_assoc($result)) {
		$modules[$row['position']] = $row;
	}
	if (file_exists("modules/{$package['id']}/plugin.ini")) {
		$plugin_ini = $myini->read("modules/{$package['id']}/plugin.ini");
		if (isset($plugin_ini['names'])) {
			foreach ($plugin_ini['names'] as $hook => $name) {
				if (isset($modules[$hook])) {
					continue;
				}
				$modules[$hook] = array(
					'id' => 0,
					'name' => $name,
					'module' => $id,
					'ordering' => 0,
					'active' => 1,
					'required' => $plugin_ini['required'][$hook],
					'position' => $hook
				);
			}
		}
	}
	ksort($modules);

	if (file_exists("modules/{$package['id']}/component.ini") == true) {
		$result = $db->query("SELECT * FROM {$db->pre}component WHERE package = '{$package['id']}'", __LINE__, __FILE__);
		$component = $db->fetch_assoc($result);
		$component_ini = $myini->read("modules/{$package['id']}/component.ini");
	}
	else {
		$component = $component_ini = null;
	}

	$settings = $sg = array();
	$result = $db->query("SELECT id, title, name FROM {$db->pre}settings_groups WHERE name = '{$package_ini['info']['internal']}' LIMIT 1");
	if ($db->num_rows($result) > 0) {
		$sg = $db->fetch_assoc($result);
		$result = $db->query("SELECT * FROM {$db->pre}settings WHERE sgroup = '{$sg['id']}' ORDER BY name", __LINE__, __FILE__);
		while ($row = $db->fetch_assoc($result)) {
			$row['current'] = $config[$sg['name']][$row['name']];
			if ($row['type'] == 'select') {
				$val = prepare_custom($row['optionscode']);
				$row['current'] = isset($val[$row['current']]) ? $gpc->prepare($val[$row['current']]) : '<em>'.$lang->phrase('admin_packages_iunknown').'</em>';
			}
			$settings[] = $row;
		}
	}

	$dependencies = array();
	if (isset($package_ini['dependency']) && count($package_ini['dependency']) > 0) {
		$result = $db->query("SELECT id, title, internal FROM {$db->pre}packages WHERE internal IN ('".implode("','", $package_ini['dependency'])."')", __LINE__, __FILE__);
		while ($row = $db->fetch_assoc($result)) {
			$dependencies[] = $row;
		}
	}

	$plug_count = count($modules);

	?>
	 <table class="border" border="0" cellspacing="0" cellpadding="4" align="center">
	  <tr>
	   <td class="obox" colspan="2">
	   <span class="right">
	    <a class="button" href="admin.php?action=packages&amp;job=package_delete&amp;id=<?php echo $package['id']; ?>"><?php echo $lang->phrase('admin_packages_delete'); ?></a>
	    <a class="button" href="admin.php?action=packages&amp;job=package_edit&amp;id=<?php echo $package['id']; ?>"><?php echo $lang->phrase('admin_packages_info_edit'); ?></a>
	    <?php if (!empty($component_ini['admin']['frontpage']) == true) { ?>
	  	 <a class="button" href="admin.php?action=packages&amp;job=package_admin&amp;cid=<?php echo $package['id']; ?>"><?php echo $lang->phrase('admin_packages_administration'); ?></a>
	  	<?php } ?>
	   </span>
	   	<?php
	   		echo $lang->phrase('admin_packages_info_package_details_for_foo');
	   	?>
	   </td>
	  </tr>
	  <tr>
	   <td class="ubox" colspan="2"><?php echo $lang->phrase('admin_packages_info_general_information'); ?></td>
	  </tr>
	  <tr>
	   <td class="mbox" width="30%"><?php echo $lang->phrase('admin_packages_info_description'); ?></td>
	   <td class="mbox" width="70%"><?php echo nl2br($package_ini['info']['summary']); ?></td>
	  </tr>
	  <tr>
	   <td class="mbox" width="30%"><?php echo $lang->phrase('admin_packages_info_copyright'); ?></td>
	   <td class="mbox" width="70%"><?php echo $package_ini['info']['copyright']; ?></td>
	  </tr>
	  <tr>
	   <td class="mbox" width="30%"><?php echo $lang->phrase('admin_packages_info_license'); ?></td>
	   <td class="mbox" width="70%"><?php echo $package_ini['info']['license']; ?></td>
	  </tr>
	  <tr>
	   <td class="mbox" width="30%"><?php echo $lang->phrase('admin_packages_info_version'); ?></td>
	   <td class="mbox" width="70%">
	   	<?php echo $package_ini['info']['version']; ?>&nbsp;&nbsp;&nbsp;&nbsp;
	   	<a class="button" href="admin.php?action=packages&amp;job=package_updates&amp;id=<?php echo $package['id']; ?>"><?php echo $lang->phrase('admin_packages_check_for_updates'); ?></a>
	   	<a class="button" href="admin.php?action=packages&amp;job=package_update&amp;id=<?php echo $package['id']; ?>"><?php echo $lang->phrase('admin_packages_install_update_manually'); ?></a>
	   	</td>
	  </tr>
	  <tr>
	   <td class="mbox" width="30%"><?php echo $lang->phrase('admin_packages_info_compatibility'); ?></td>
	   <td class="mbox" width="70%">
	   	<?php if (!empty($package_ini['info']['min_version'])) { $min = $package_ini['info']['min_version']; ?>
	   	<div><?php echo $lang->phrase('admin_packages_minimum_v'); ?></div>
	   	<?php } ?>
	   	<?php if (!empty($package_ini['info']['max_version'])) { $max = $package_ini['info']['max_version']; ?>
	   	<div><?php echo $lang->phrase('admin_packages_maximum_v'); ?></div>
	   	<?php } ?>
	   </td>
	  </tr>
	  <tr>
	   <td class="mbox" width="30%"><?php echo $lang->phrase('admin_packages_dependency_label'); ?></td>
	   <td class="mbox" width="70%">
	   <?php if (count($dependencies) > 0) { ?>
	   	<ul>
	   		<?php foreach ($dependencies as $row) { ?>
	   		<li><a href="admin.php?action=packages&amp;job=package_info&amp;id=<?php echo $row['id']; ?>"><?php echo $row['title']; ?></a> (<?php echo $row['internal']; ?>)</li>
	   		<?php } ?>
	   	</ul>
	   	<?php } else { echo $lang->phrase('admin_package_no_dependency'); } ?>
	   </td>
	  </tr>
	  <tr>
	   <td class="mbox" width="30%"><?php echo $lang->phrase('admin_packages_info_internal_name2'); ?></td>
	   <td class="mbox" width="70%"><tt><?php echo $package_ini['info']['internal']; ?></tt></td>
	  </tr>
	 </table>
	 <br class="minibr" />
	 <table class="border" border="0" cellspacing="0" cellpadding="4" align="center">
	  <tr>
	   <td class="obox" colspan="4">
	   <span class="right">
	   <?php if (count($sg) > 0) { ?>
	   <a class="button" href="admin.php?action=settings&amp;job=custom&amp;id=<?php echo $sg['id']; ?>&amp;package=<?php echo $package['id']; ?>"><?php echo $lang->phrase('admin_packages_info_change_settings'); ?></a>
	   <?php } ?>
	   </span>
	   <?php echo $lang->phrase('admin_packages_info_configuration'); ?>
	   </td>
	  </tr>
	  <?php if (is_array($settings) && count($settings) > 0) { ?>
	  <tr class="ubox">
	   <td width="40%"><?php echo $lang->phrase('admin_packages_info_title'); ?></td>
	   <td width="40%"><?php echo $lang->phrase('admin_packages_info_current_value'); ?></td>
	   <td width="20%"><?php echo $lang->phrase('admin_packages_info_internal_name'); ?></td>
	  </tr>
	  <?php foreach ($settings as $setting) { ?>
	  <tr class="mbox">
		<td><?php echo $setting['title']; ?></td>
		<td><?php echo $setting['current']; ?></td>
		<td class="monospace"><?php echo $setting['name']; ?></td>
	  </tr>
	  <?php } } else { ?>
		<tr class="mbox">
			<td colspan="4"><?php echo $lang->phrase('admin_packages_info_no_settings_specified_for_this_package'); ?></td>
		</tr>
	  <?php } ?>
	 </table>
	 <br class="minibr" />
	 <table class="border" border="0" cellspacing="0" cellpadding="4" align="center">
	  <tr>
	   <td class="obox" colspan="4"><?php echo $lang->phrase('admin_packages_info_component'); ?></td>
	  </tr>
	  <?php if (is_array($component_ini) && count($component_ini) > 0) { ?>
	  <tr class="ubox">
	   <td width="80%"><?php echo $lang->phrase('admin_packages_info_name'); ?></td>
	   <td width="10%"><?php echo $lang->phrase('admin_packages_active'); ?></td>
	   <td width="10%"><?php echo $lang->phrase('admin_packages_info_required'); ?></td>
	  </tr>
	  <tr class="mbox">
		<td><?php echo $component_ini['info']['title']; ?></td>
		<td class="center">
		<?php if ($component['active'] == 1 && $package['active'] == 1) { ?>
		<img class="valign" src="admin/html/images/yes.gif" border="0" alt="Active" title="<?php echo $lang->phrase('admin_packages_component_is_active'); ?>" />
		<?php } elseif ($component['active'] == 1 && $package['active'] == 0) { ?>
		<img class="valign" src="admin/html/images/avg.gif" border="0" alt="Partially" title="<?php echo $lang->phrase('admin_packages_component_is_active_but_package_is_not_active'); ?>" />
		<?php } else { ?>
		<img class="valign" src="admin/html/images/no.gif" border="0" alt="Inactive" title="<?php echo $lang->phrase('admin_packages_component_is_not_active'); ?>" />
		<?php } ?>
		</td>
	  	<td class="center"><?php echo noki($component['required']); ?></td>
	  </tr>
	  <?php } else { ?>
		<tr class="mbox">
			<td colspan="4"><?php echo $lang->phrase('admin_packages_info_for_this_package_is_no_component_specified'); ?></td>
		</tr>
	  <?php } ?>
	 </table>
	 <br class="minibr" />
	 <table class="border" border="0" cellspacing="0" cellpadding="4" align="center">
	  <tr>
	   <td class="obox" colspan="4">
	   	<span class="right"><a class="button" href="admin.php?action=packages&amp;job=plugins_add&amp;id=<?php echo $package['id']; ?>"><?php echo $lang->phrase('admin_packages_info_add_plugin'); ?></a></span>
	   	<?php echo $lang->phrase('admin_packages_plugins_count'); ?>
	   </td>
	  </tr>
	  <?php if (count($modules) > 0) { ?>
		  <tr class="ubox">
		   <td width="40%"><?php echo $lang->phrase('admin_packages_info_th_plugin'); ?></td>
		   <td width="40%"><?php echo $lang->phrase('admin_packages_info_th_hook'); ?></td>
		   <td width="10%"><?php echo $lang->phrase('admin_packages_active'); ?></td>
		   <td width="10%"><?php echo $lang->phrase('admin_packages_info_th_required'); ?></td>
		  </tr>
		 <?php
		 foreach ($modules as $plugin) {
		 	if ($plugin['id'] == 0) {
		 		$id = $plugin['position'];
		 		$pid = $plugin['module'];
		 	}
		 	else {
		 		$pid = 0;
		 		$id = $plugin['id'];
		 	}
			?>
			<tr class="mbox">
				<td><a href="admin.php?action=packages&amp;job=plugins_edit&amp;id=<?php echo $id; ?>&amp;package=<?php echo $pid; ?>"><?php echo $plugin['name']; ?></a></td>
				<td><?php echo $plugin['position']; ?></td>
				<td class="center">
				<?php if ($plugin['active'] == 1 && $package['active'] == 1) { ?>
				<img class="valign" src="admin/html/images/yes.gif" border="0" alt="Active" title="<?php echo $lang->phrase('admin_packages_info_plugin_is_active'); ?>" />
				<?php } elseif ($plugin['active'] == 1 && $package['active'] == 0) { ?>
				<img class="valign" src="admin/html/images/avg.gif" border="0" alt="Partially" title="<?php echo $lang->phrase('admin_packages_info_plugin_is_active_but_package_is_not_active'); ?>" />
				<?php } else { ?>
				<img class="valign" src="admin/html/images/no.gif" border="0" alt="Inactive" title="<?php echo $lang->phrase('admin_packages_plugin_is_not_active'); ?>" />
				<?php } ?>
				</td>
				<td class="center"><?php echo noki($plugin['required']); ?></td>
			</tr>
			<?php
		}
	  }
	  else {
	  	?>
		<tr class="mbox">
			<td colspan="4"><?php echo $lang->phrase('admin_packages_info_for_this_package_is_no_plugin_specified'); ?></td>
		</tr>
	  	<?php
	  }
	echo '</table>';
	echo foot();
}
elseif ($job == 'package_add') {
	echo head();
	$result = $db->query("SELECT id, title, internal FROM {$db->pre}packages", __LINE__, __FILE__);
	?>
	<form method="post" action="admin.php?action=packages&job=package_add2">
	<table class="border" border="0" cellspacing="0" cellpadding="4" align="center">
	 <tr>
	  <td class="obox" colspan="2"><?php echo $lang->phrase('admin_packages_edit_create_a_new_package'); ?></td>
	 </tr>
	 <tr class="mbox">
	  <td><?php echo $lang->phrase('admin_packages_edit_title'); ?><br /><span class="stext"><?php echo $lang->phrase('admin_packages_edit_title_text'); ?></span></td>
	  <td><input type="text" name="title" size="60" /></td>
	 </tr>
	 <tr class="mbox">
	  <td><?php echo $lang->phrase('admin_packages_info_description'); ?><br /><span class="stext"><?php echo $lang->phrase('admin_packages_edit_optional'); ?></span></td>
	  <td><textarea cols="60" rows="4" name="summary" /></textarea></td>
	 </tr>
	 <tr class="mbox">
	  <td><?php echo $lang->phrase('admin_packages_edit_internal_name'); ?><br /><span class="stext"><?php echo $lang->phrase('admin_packages_edit_internal_name_text'); ?></span></td>
	  <td><input type="text" name="internal" size="60" /></td>
	 </tr>
	 <tr class="mbox">
	  <td><?php echo $lang->phrase('admin_packages_info_version'); ?><br /><span class="stext"><?php echo $lang->phrase('admin_packages_edit_optional'); ?></span></td>
	  <td><input type="text" name="version" size="60" value="1.0" /></td>
	 </tr>
	 <tr class="mbox">
	  <td><?php echo $lang->phrase('admin_packages_edit_minimum_viscacha_version'); ?><br /><span class="stext"><?php echo $lang->phrase('admin_packages_edit_optional'); ?></span></td>
	  <td><input type="text" name="min_version" size="60" /></td>
	 </tr>
	 <tr class="mbox">
	  <td><?php echo $lang->phrase('admin_packages_edit_maximum_viscacha_version'); ?><br /><span class="stext"><?php echo $lang->phrase('admin_packages_edit_optional'); ?></span></td>
	  <td><input type="text" name="max_version" size="60" /></td>
	 </tr>
	 <tr class="mbox">
	  <td><?php echo $lang->phrase('admin_packages_dependency_label'); ?><br /><span class="stext"><?php echo $lang->phrase('admin_packages_dependency_info'); ?></span></td>
	  <td>
	  	<select name="dependency[]" multiple="multiple" size="5">
	  		<?php while ($d = $db->fetch_assoc($result)) { ?>
	  		<option value="<?php echo $d['internal']; ?>"><?php echo $d['title']; ?> (<?php echo $d['internal']; ?>)</option>
	  		<?php } ?>
	  	</select>
	  </td>
	 </tr>
	 <tr class="mbox">
	  <td><?php echo $lang->phrase('admin_packages_info_copyright'); ?><br /><span class="stext"><?php echo $lang->phrase('admin_packages_edit_optional'); ?></span></td>
	  <td><input type="text" name="copyright" size="60" /></td>
	 </tr>
	 <tr class="mbox">
	  <td><?php echo $lang->phrase('admin_packages_info_license'); ?><br /><span class="stext"><?php echo $lang->phrase('admin_packages_edit_optional'); ?></span></td>
	  <td><input type="text" name="license" size="60" value="GNU General Public License" /></td>
	 </tr>
	 <tr class="mbox">
	  <td><?php echo $lang->phrase('admin_packages_edit_url_homepage'); ?><br /><span class="stext"><?php echo $lang->phrase('admin_packages_edit_optional'); ?></span></td>
	  <td><input type="text" name="url" size="60" value="" /></td>
	 </tr>
	 <tr>
	  <td class="ubox" colspan="2" align="center"><input type="submit" value="Create" /></td>
	 </tr>
	</table>
	</form>
	<?php
	echo foot();
}
elseif ($job == 'package_add2') {
	echo head();
	$title = $gpc->get('title', none);
	$summary = $gpc->get('summary', none);
	$internal = $gpc->get('internal', none);
	$version = $gpc->get('version', none);
	$copyright = $gpc->get('copyright', none);
	$license = $gpc->get('license', none);
	$max = $gpc->get('max_version', none);
	$min = $gpc->get('min_version', none);
	$url = $gpc->get('url', none);
	$dependency = $gpc->get('dependency', arr_none);

	if (strlen($title) < 4) {
		error('admin.php?action=packages&job=package_add', $lang->phrase('admin_packages_err_minimum_number_of_characters_for_title'));
	}
	elseif (strlen($title) > 200) {
		error('admin.php?action=packages&job=package_add', $lang->phrase('admin_packages_err_maximum_number_of_characters_for_title'));
	}
	if (strlen($internal) < 10) {
		error('admin.php?action=packages&job=package_add', $lang->phrase('admin_packages_err_internal_name_is_too_short'));
	}

	$dbtitle = $gpc->save_str($title);
	$dbversion = $gpc->save_str($version);
	$dbinternal = $gpc->save_str($internal);
	$db->query("INSERT INTO {$db->pre}packages (`title`,`version`,`internal`) VALUES ('{$dbtitle}','{$dbversion}','{$dbinternal}')");
	$packageid = $db->insert_id();

	$filesystem->mkdir("modules/{$packageid}/", 0777);

	$ini = array(
		'info' => array(
			'title' => $title,
			'version' => $version,
			'copyright' => $copyright,
			'summary' => $summary,
			'internal' => $internal,
			'min_version' => $min,
			'max_version' => $max,
			'license' => $license,
			'url' => $url,
			'multiple' => 0,
			'core' => 0
		),
		'dependency' => $dependency,
		'config' => array()
	);
	$myini->write("modules/{$packageid}/package.ini", $ini);
	$filesystem->chmod("modules/{$packageid}/package.ini", 0666);

	ok('admin.php?action=packages&job=package_info&id='.$packageid, $lang->phrase('admin_packages_ok_package_successfully_added'));
}
elseif ($job == 'package_active') {
	$id = $gpc->get('id', int);
	$result = $db->query('SELECT id, active, core FROM '.$db->pre.'packages WHERE id = "'.$id.'"', __LINE__, __FILE__);
	$row = $db->fetch_assoc($result);
	if ($db->num_rows($result) == 0) {
		echo head();
		error('admin.php?action=packages&job=package', $lang->phrase('admin_packages_err_specified_id_is_not_correct'));
	}
	elseif ($row['core'] == '1') {
		echo head();
		error('admin.php?action=packages&job=package', $lang->phrase('admin_packages_err_this_package_is_required'));
	}
	else {
		if ($row['active'] == 1) {
			$result2 = $db->query("SELECT id, title, internal FROM {$db->pre}packages WHERE id != '{$row['id']}'", __LINE__, __FILE__);
			while ($row2 = $db->fetch_assoc($result)) {
				$pack = $myini->read("modules/{$row2['id']}/package.ini");
				if (isset($pack['dependency']) && in_array($row['internal'], $pack['dependency'])) {
					error('admin.php?action=packages&job=package_info&id='.$row['id'], $lang->phrase('admin_packages_err_package_required'));
				}
			}
		}
		$active = $row['active'] == 1 ? 0 : 1;
		$db->query('UPDATE '.$db->pre.'packages SET active = "'.$active.'" WHERE id = "'.$id.'"', __LINE__, __FILE__);
		$result = $db->query("SELECT DISTINCT position FROM {$db->pre}plugins WHERE module = '{$id}'");
		while ($row = $db->fetch_assoc($result)) {
			$filesystem->unlink('cache/modules/'.$plugins->_group($row['position']).'.php');
		}
		viscacha_header('Location: admin.php?action=packages&job=package');
	}
}
elseif ($job == 'com') {
	send_nocache_header();
	echo head();
?>
 <table class="border" border="0" cellspacing="0" cellpadding="4" align="center">
  <tr>
   <td class="obox" colspan="5"><?php echo $lang->phrase('admin_packages_component_manager'); ?></td>
  </tr>
  <tr class="ubox">
   <td width="30%"><?php echo $lang->phrase('admin_packages_com_th_component'); ?></td>
   <td width="30%"><?php echo $lang->phrase('admin_packages_com_th_package'); ?></td>
   <td width="10%"><?php echo $lang->phrase('admin_packages_active'); ?></td>
   <td width="30%"><?php echo $lang->phrase('admin_packages_action'); ?></td>
  </tr>
<?php
	$result = $db->query("
		SELECT c.*, p.title, p.core, p.active AS pactive
		FROM {$db->pre}component AS c
			LEFT JOIN {$db->pre}packages AS p ON c.package = p.id
		ORDER BY active DESC
	", __LINE__, __FILE__);
	while ($row = $db->fetch_assoc($result)) {
		if (!file_exists('modules/'.$row['package'].'/component.ini')) {
			continue;
		}
		$component = $myini->read('modules/'.$row['package'].'/component.ini');
	?>
	<tr class="mbox">
	<td><?php echo $component['info']['title']; ?></td>
	<td><?php echo $row['title']; ?></td>
	<td class="center">
	<?php if ($row['active'] == 1 && $row['pactive'] == 1) { ?>
	<img class="valign" src="admin/html/images/yes.gif" border="0" alt="Active" title="<?php echo $lang->phrase('admin_packages_component_is_active'); ?>" />
	<?php } elseif ($row['active'] == 1 && $row['pactive'] == 0) { ?>
	<img class="valign" src="admin/html/images/avg.gif" border="0" alt="Partially" title="<?php echo $lang->phrase('admin_packages_component_is_active_but_package_is_not_active'); ?>" />
	<?php } else { ?>
	<img class="valign" src="admin/html/images/no.gif" border="0" alt="Inactive" title="<?php echo $lang->phrase('admin_packages_component_is_not_active'); ?>" />
	<?php } ?>
	</td>
	<td>
	 <?php if ($row['required'] == 0) { ?>
	 <a class="button" href="admin.php?action=packages&amp;job=com_active&amp;id=<?php echo $row['id']; ?>"><?php echo iif($row['active'] == 1, $lang->phrase('admin_packages_com_deactivate'), $lang->phrase('admin_packages_com_activate')); ?></a>
	 <a class="button" href="admin.php?action=packages&amp;job=com_delete&amp;id=<?php echo $row['id']; ?>"><?php echo $lang->phrase('admin_packages_delete'); ?></a>
	 <?php } else { echo "<em>".$lang->phrase('admin_packages_com_component_is_required')."</em>"; } ?>
	</td>
	</tr>
	<?php
}
?>
 </table>
<?php
	echo foot();
}
elseif ($job == 'com_delete') {
	echo head();
	$id = $gpc->get('id', int);
	$result = $db->query("SELECT id, required FROM {$db->pre}component WHERE id = '{$id}' LIMIT 1", __LINE__, __FILE__);
	$row = $db->fetch_assoc($result);
	if ($db->num_rows($result) == 0) {
		error('admin.php?action=packages&job=com', $lang->phrase('admin_packages_err_specified_component_not_found'));
	}
	elseif ($row['required'] == 1) {
		error('admin.php?action=packages&job=com', $lang->phrase('admin_packages_err_specified_component_is_required'));
	}
	else {
		?>
		<table class="border" border="0" cellspacing="0" cellpadding="4" align="center">
		<tr><td class="obox"><?php echo $lang->phrase('admin_packages_delete_head_delete_component'); ?></td></tr>
		<tr><td class="mbox">
		<p align="center"><?php echo $lang->phrase('admin_packages_delete_do_you_really_want_to_delete_this_component'); ?></p>
		<p align="center">
		<a href="admin.php?action=packages&amp;job=com_delete2&amp;id=<?php echo $id; ?>"><img border="0" alt="Yes" src="admin/html/images/yes.gif"> <?php echo $lang->phrase('admin_packages_yes'); ?></a>
		&nbsp&nbsp;&nbsp;&nbsp&nbsp;&nbsp;
		<a href="javascript: history.back(-1);"><img border="0" alt="No" src="admin/html/images/no.gif"> <?php echo $lang->phrase('admin_packages_no'); ?></a>
		</p>
		</td></tr>
		</table>
		<?php
		echo foot();
	}
}
elseif ($job == 'com_delete2') {
	echo head();
	$id = $gpc->get('id', int);
	$result = $db->query("SELECT id, required, package FROM {$db->pre}component WHERE id = '{$id}' LIMIT 1", __LINE__, __FILE__);
	$row = $db->fetch_assoc($result);
	if ($db->num_rows($result) == 0) {
		error('admin.php?action=packages&job=com', $lang->phrase('admin_packages_err_specified_component_not_found'));
	}
	elseif ($row['required'] == 1) {
		error('admin.php?action=packages&job=com', $lang->phrase('admin_packages_err_specified_component_is_required'));
	}
	else {
		$cfg = $myini->read("modules/{$row['package']}/component.ini");

		$db->query("DELETE FROM {$db->pre}component WHERE id = '{$id}' LIMIT 1", __LINE__, __FILE__);

		$cache = array();
		$result = $db->query("SELECT template, stylesheet, images FROM {$db->pre}designs",__LINE__,__FILE__);
		$design = $db->fetch_assoc($result);
		while ($row = $db->fetch_assoc($design)) {
			$cache[] = $row;
		}
		$result = $db->query("SELECT id FROM {$db->pre}language",__LINE__,__FILE__);
		$languages = $db->fetch_assoc($result);

		while ($lng = $db->fetch_assoc($languages)) {
			rmdirr("./language/{$lng['id']}/modules/{$row['package']}");
		}

		foreach ($cache as $design) {
			rmdirr("./templates/{$design['template']}/modules/{$row['package']}");
		}
		if (isset($cfg['images']) && count($cfg['images']) > 0) {
			foreach ($cache as $design) {
				foreach ($cfg['images'] as $file) {
					$filesystem->unlink("./images/{$design['images']}/{$file}");
				}
			}
		}
		if (isset($cfg['designs']) && count($cfg['designs']) > 0) {
			foreach ($cache as $design) {
				foreach ($cfg['designs'] as $file) {
					$filesystem->unlink("./designs/{$design['stylesheet']}/{$file}");
				}
			}
		}

		if (isset($cfg['php']) && count($cfg['php']) > 0) {
			foreach ($cfg['php'] as $file) {
				$filesystem->unlink("./modules/{$row['package']}/{$file}");
			}
		}

		$filesystem->unlink("modules/{$row['package']}/component.ini");

		$delobj = $scache->load('components');
		$delobj->delete();

		ok('admin.php?action=packages&job=com', $lang->phrase('admin_packages_ok_component_successfully_removed'));
	}
}
elseif ($job == 'com_active') {
	$id = $gpc->get('id', int);
	$result = $db->query('SELECT id, active, required FROM '.$db->pre.'component WHERE id = "'.$id.'"', __LINE__, __FILE__);
	$row = $db->fetch_assoc($result);
	if ($db->num_rows($result) == 0) {
		echo head();
		error('admin.php?action=packages&job=com', $lang->phrase('admin_packages_err_specified_id_is_not_correct'));
	}
	elseif ($row['required'] == 1) {
		echo head();
		error('admin.php?action=packages&job=com', $lang->phrase('admin_packages_err_this_package_is_required_you_cannot_change_the_status'));
	}
	else {
		$active = $row['active'] == 1 ? 0 : 1;
		$delobj = $scache->load('components');
		$delobj->delete();
		$db->query('UPDATE '.$db->pre.'component SET active = "'.$active.'" WHERE id = "'.$id.'"', __LINE__, __FILE__);
		viscacha_header('Location: admin.php?action=packages&job=com');
	}
}
elseif ($job == 'plugins') {
	send_nocache_header();
	echo head();
	if (!isset($my->settings['admin_plugins_sort'])) {
		$my->settings['admin_plugins_sort'] = 0;
	}
	$sort = $gpc->get('sort', int, $my->settings['admin_plugins_sort']);
	?>
	 <table class="border" border="0" cellspacing="0" cellpadding="4" align="center">
	  <tr>
	   <td class="obox"><?php echo $lang->phrase('admin_packages_plugins_head_plugin_manager'); ?> (<?php echo iif($sort == 1, $lang->phrase('admin_packages_plugins_packages'), $lang->phrase('admin_packages_plugins_hooks')); ?>)</td>
	  </tr>
	  <tr>
	   <td class="mbox">
		<span class="right">
			<a class="button" href="admin.php?action=packages&amp;job=plugins_add"><?php echo $lang->phrase('admin_packages_info_add_plugin'); ?></a>
			<a class="button" href="admin.php?action=packages&amp;job=plugins_hook_add"><?php echo $lang->phrase('admin_packages_plugins_add_new_hook'); ?></a>
		</span>
	   <?php echo $lang->phrase('admin_packages_plugins_group_plugins_by'); ?>
	   <a<?php echo iif($sort == 0, ' style="font-weight: bold;"'); ?> class="button" href="admin.php?action=packages&amp;job=plugins&amp;sort=0"><?php echo $lang->phrase('admin_packages_plugins_hooks'); ?></a>
	   <a<?php echo iif($sort == 1, ' style="font-weight: bold;"'); ?> class="button" href="admin.php?action=packages&amp;job=plugins&amp;sort=1"><?php echo $lang->phrase('admin_packages_plugins_packages'); ?></a>
	   </td>
	  </tr>
	 </table>
	 <br class="minibr" />
	<?php
	if ($sort == 1) {
		$package = null;
		$my->settings['admin_plugins_sort'] = 1;

		$result = $db->query("
		SELECT p.*, m.title, m.core, m.active AS mactive
		FROM {$db->pre}packages AS m
			LEFT JOIN {$db->pre}plugins AS p ON p.module = m.id
		ORDER BY m.id, p.position
		", __LINE__, __FILE__);
		?>
		 <table class="border" border="0" cellspacing="0" cellpadding="4" align="center">
		  <tr class="ubox">
		   <td width="30%"><?php echo $lang->phrase('admin_packages_plugins_th_plugin'); ?></td>
		   <td width="20%"><?php echo $lang->phrase('admin_packages_plugins_th_hook'); ?></td>
		   <td width="10%"><?php echo $lang->phrase('admin_packages_active'); ?></td>
		   <td width="40%"><?php echo $lang->phrase('admin_packages_action'); ?></td>
		  </tr>
		<?php
		while ($head = $db->fetch_assoc($result)) {
			if ($head['module'] != $package) {
				?>
				<tr class="obox">
				  <td colspan="3"><?php echo $head['title']; ?></td>
				  <td>
				  	<a class="button" href="admin.php?action=packages&amp;job=package_info&amp;id=<?php echo $head['module']; ?>"><?php echo $lang->phrase('admin_packages_plugins_go_to_package'); ?></a>
				  	<a class="button" href="admin.php?action=packages&amp;job=plugins_add&id=<?php echo $head['module']; ?>"><?php echo $lang->phrase('admin_packages_info_add_plugin'); ?></a>
				  </td>
				</tr>
				<?php
				$package = $head['module'];
			}
			if ($head['id'] > 0) {
				?>
				<tr class="mbox">
					<td><?php echo $head['name']; ?></td>
					<td><?php echo $head['position']; ?></td>
					<td class="center">
					<?php if ($head['active'] == 1 && $head['mactive'] == 1) { ?>
					<img class="valign" src="admin/html/images/yes.gif" border="0" alt="Active" title="<?php echo $lang->phrase('admin_packages_info_plugin_is_active'); ?>" />
					<?php } elseif ($head['active'] == 1 && $head['mactive'] == 0) { ?>
					<img class="valign" src="admin/html/images/avg.gif" border="0" alt="Partially" title="<?php echo $lang->phrase('admin_packages_info_plugin_is_active_but_package_is_not_active'); ?>" />
					<?php } else { ?>
					<img class="valign" src="admin/html/images/no.gif" border="0" alt="Inactive" title="<?php echo $lang->phrase('admin_packages_plugin_is_not_active'); ?>" />
					<?php } ?>
					</td>
					<td>
					 <a class="button" href="admin.php?action=packages&amp;job=plugins_edit&amp;id=<?php echo $head['id']; ?>"><?php echo $lang->phrase('admin_packages_plugins_edit'); ?></a>
					 <?php if ($head['required'] == 0) { ?>
					 <a class="button" href="admin.php?action=packages&amp;job=plugins_active&amp;id=<?php echo $head['id']; ?>"><?php echo iif($head['active'] == 1, $lang->phrase('admin_packages_plugins_deactivate'), $lang->phrase('admin_packages_active')); ?></a>
					 <a class="button" href="admin.php?action=packages&amp;job=plugins_delete&amp;id=<?php echo $head['id']; ?>"><?php echo $lang->phrase('admin_packages_delete'); ?></a>
					 <?php } ?>
					</td>
				</tr>
				<?php
			}
			else {
				?>
				<tr class="mbox">
					<td colspan="4"><?php echo $lang->phrase('admin_packages_info_for_this_package_is_no_plugin_specified'); ?> <a href="admin.php?action=packages&amp;job=plugins_add&id=<?php echo $head['module']; ?>"><?php echo $lang->phrase('admin_packages_plugins_add_a_new_plugin'); ?></a></td>
				</tr>
				<?php
			}
		}
		echo '</table>';
	}
	else {
		$pos = null;
		$my->settings['admin_plugins_sort'] = 0;

		$result = $db->query("
		SELECT p.*, m.title, m.core, m.active AS mactive
		FROM {$db->pre}plugins AS p
			LEFT JOIN {$db->pre}packages AS m ON p.module = m.id
		ORDER BY p.position, p.ordering
		", __LINE__, __FILE__);
		?>
		 <table class="border" border="0" cellspacing="0" cellpadding="4" align="center">
		  <tr class="ubox">
		   <td width="30%"><?php echo $lang->phrase('admin_packages_plugins_plugin'); ?></td>
		   <td width="28%"><?php echo $lang->phrase('admin_packages_plugins_package'); ?></td>
		   <td width="11%"><?php echo $lang->phrase('admin_packages_active'); ?></td>
		   <td width="9%"><?php echo $lang->phrase('admin_packages_plugins_priority'); ?></td>
		   <td width="22%"><?php echo $lang->phrase('admin_packages_action'); ?></td>
		  </tr>
		<?php
		while ($head = $db->fetch_assoc($result)) {
			if ($head['position'] != $pos) {
				?>
				<tr>
					<td class="obox" colspan="5"><?php echo $head['position']; ?></td>
				</tr>
				<?php
				$pos = $head['position'];
			}
			?>
			<tr class="mbox">
				<td><?php echo $head['name']; ?></td>
				<td><?php echo $head['title']; ?></td>
				<td class="center">
					<?php if ($head['active'] == 1 && $head['mactive'] == 1) { ?>
					<img class="valign" src="admin/html/images/yes.gif" border="0" alt="Active" title="<?php echo $lang->phrase('admin_packages_info_plugin_is_active'); ?>" />
					<?php } elseif ($head['active'] == 1 && $head['mactive'] == 0) { ?>
					<img class="valign" src="admin/html/images/avg.gif" border="0" alt="Partially" title="<?php echo $lang->phrase('admin_packages_info_plugin_is_active_but_package_is_not_active'); ?>" />
					<?php } else { ?>
					<img class="valign" src="admin/html/images/no.gif" border="0" alt="Inactive" title="<?php echo $lang->phrase('admin_packages_plugin_is_not_active'); ?>" />
					<?php } ?>
				</td>
				<td nowrap="nowrap" align="right">
					<?php echo $head['ordering']; ?>&nbsp;&nbsp;
		 			<a href="admin.php?action=packages&amp;job=plugins_move&amp;id=<?php echo $head['id']; ?>&amp;value=-1"><img src="admin/html/images/asc.gif" border="0" alt="Up"></a>&nbsp;
		 			<a href="admin.php?action=packages&amp;job=plugins_move&amp;id=<?php echo $head['id']; ?>&amp;value=1"><img src="admin/html/images/desc.gif" border="0" alt="Down"></a>
				</td>
				<td>
				 <a class="button" href="admin.php?action=packages&amp;job=plugins_edit&amp;id=<?php echo $head['id']; ?>"><?php echo $lang->phrase('admin_packages_plugins_edit'); ?></a>
				 <?php if ($head['required'] == 0) { ?>
				 <a class="button" href="admin.php?action=packages&amp;job=plugins_active&amp;id=<?php echo $head['id']; ?>"><?php echo iif($head['active'] == 1, $lang->phrase('admin_packages_plugins_deactivate'), $lang->phrase('admin_packages_active')); ?></a>
				 <a class="button" href="admin.php?action=packages&amp;job=plugins_delete&amp;id=<?php echo $head['id']; ?>"><?php echo $lang->phrase('admin_packages_delete'); ?></a>
				 <?php } ?>
				</td>
			</tr>
			<?php
		}
		echo '</table>';
	}
	echo foot();
}
elseif ($job == 'plugins_hook_add') {
	echo head();
	?>
	<form method="post" action="admin.php?action=packages&amp;job=plugins_hook_add2">
	<table class="border" border="0" cellspacing="0" cellpadding="4" align="center">
	 <tr>
	  <td class="obox" colspan="2"><?php echo $lang->phrase('admin_packages_plugins_head_add_a_hook'); ?></td>
	 </tr>
	 <tr>
	  <td class="ubox" colspan="2"><?php echo $lang->phrase('admin_packages_plugins_if_you_need_a_special_hook_report_it_to_us'); ?></td>
	 </tr>
	 <tr class="mbox">
	  <td width="40%"><?php echo $lang->phrase('admin_packages_plugins_name_for_the_hook'); ?><br /><span class="stext"><?php echo $lang->phrase('admin_packages_plugins_name_for_the_hook_text'); ?></span></td>
	  <td width="60%"><input type="text" name="group" size="15" />_<input type="text" name="name" size="35" /></td>
	 </tr>
	 <tr class="mbox">
	  <td><?php echo $lang->phrase('admin_packages_plugins_file'); ?><br /><span class="stext"><?php echo $lang->phrase('admin_packages_plugins_file_text'); ?></span></td>
	  <td><input type="text" name="file" size="60" value="" /></td>
	 </tr>
	 <tr class="mbox">
	  <td><?php echo $lang->phrase('admin_packages_plugins_where_is_the_hook_used'); ?></td>
	  <td>
	   <input type="radio" name="place" value="0" /><?php echo $lang->phrase('admin_packages_plugins_directly_in_php_code'); ?><br />
	   <input type="radio" name="place" value="1" /><?php echo $lang->phrase('admin_packages_plugins_somewhere_else'); ?>
	  </td>
	 </tr>
	 <tr>
	  <td class="ubox center" colspan="2"><input type="submit" value="<?php echo $lang->phrase('admin_packages_buttons_generate_code_and_add_hook'); ?>"></td>
	 </tr>
	</table>
	</form>
	<?php
	echo foot();
}
elseif ($job == 'plugins_hook_add2') {
	echo head();
	$group = $gpc->get('group', none);
	$name = $gpc->get('name', none);
	$file = $gpc->get('file', none);
	$unphp = $gpc->get('place', int);
	$hook = $group.'_'.$name;
	if (addHookToArray($hook, $file) == false) {
		error('admin.php?action=packages&amp;job=plugins_hook_add', $lang->phrase('admin_packages_err_there_is_already_a_hook_with_this_name'));
	}
	?>
	<table class="border" border="0" cellspacing="0" cellpadding="4" align="center">
	 <tr>
	  <td class="obox" colspan="2"><?php echo $lang->phrase('admin_packages_ok_hook_successfully_added'); ?></td>
	 </tr>
	 <tr class="mbox">
	  <td width="40%"><?php echo $lang->phrase('admin_packages_plugins_name_of_the_new_hook'); ?></td>
	  <td width="60%"><code><?php echo $hook; ?></code></td>
	 </tr>
	 <tr class="mbox">
	  <td><?php echo $lang->phrase('admin_packages_plugins_generated_code'); ?><br /><span class="stext"><?php echo $lang->phrase('admin_packages_plugins_generated_code_text'); ?></span></td>
	  <td>
	  	<textarea cols="60" rows="2"><?php echo iif($unphp == 1, '&lt;?php '); ?>($code = $plugins-&gt;load('<?php echo $hook; ?>')) ? eval($code) : null;<?php echo iif($unphp == 1, ' ?&gt;'); ?></textarea>
	  </td>
	 </tr>
	</table>
	<?php
	echo foot();
}
elseif ($job == 'plugins_move') {
	$id = $gpc->get('id', int);
	$pos = $gpc->get('value', int);
	$result = $db->query('SELECT id, position FROM '.$db->pre.'plugins WHERE id = "'.$id.'"', __LINE__, __FILE__);
	if ($db->num_rows($result) == 0) {
		error('admin.php?action=packages&job=plugins', $lang->phrase('admin_packages_err_specified_id_is_not_correct'));
	}
	else {
		$row = $db->fetch_assoc($result);
		if ($pos < 0) {
			$db->query('UPDATE '.$db->pre.'plugins SET ordering = ordering-1 WHERE id = "'.$id.'"', __LINE__, __FILE__);
		}
		elseif ($pos > 0) {
			$db->query('UPDATE '.$db->pre.'plugins SET ordering = ordering+1 WHERE id = "'.$id.'"', __LINE__, __FILE__);
		}
		$filesystem->unlink('cache/modules/'.$plugins->_group($row['position']).'.php');
		viscacha_header('Location: admin.php?action=packages&job=plugins');
	}
}
elseif ($job == 'plugins_active') {
	$id = $gpc->get('id', int);
	$result = $db->query('SELECT id, active, required, position FROM '.$db->pre.'plugins WHERE id = "'.$id.'"', __LINE__, __FILE__);
	$row = $db->fetch_assoc($result);
	if ($db->num_rows($result) == 0) {
		echo head();
		error('admin.php?action=packages&job=plugins', $lang->phrase('admin_packages_err_specified_id_is_not_correct'));
	}
	elseif ($row['required'] == 1) {
		echo head();
		error('admin.php?action=packages&job=plugins', $lang->phrase('admin_packages_err_this_plugin_is_required_you_cannot_change_the_status'));
	}
	else {
		$active = $row['active'] == 1 ? 0 : 1;
		$db->query('UPDATE '.$db->pre.'plugins SET active = "'.$active.'" WHERE id = "'.$id.'"', __LINE__, __FILE__);
		$filesystem->unlink('cache/modules/'.$plugins->_group($row['position']).'.php');
		viscacha_header('Location: admin.php?action=packages&job=plugins');
	}
}
elseif ($job == 'plugins_delete') {
	echo head();
	$id = $gpc->get('id', int);
	$result = $db->query("SELECT id, required FROM {$db->pre}plugins WHERE id = '{$id}' LIMIT 1", __LINE__, __FILE__);
	$row = $db->fetch_assoc($result);
	if ($db->num_rows($result) == 0) {
		error('admin.php?action=packages&job=plugins', $lang->phrase('admin_packages_err_specified_plugin_not_found'));
	}
	elseif ($row['required'] == 1) {
		error('admin.php?action=packages&job=plugins', $lang->phrase('admin_packages_err_specified_plugin_is_required_by_a_package_and_cannot_be_deleted'));
	}
	else {
		?>
		<table class="border" border="0" cellspacing="0" cellpadding="4" align="center">
		<tr><td class="obox"><?php echo $lang->phrase('admin_packages_delete_head_delete_package'); ?></td></tr>
		<tr><td class="mbox">
		<p align="center"><?php echo $lang->phrase('admin_packages_delete_do_you_really_want_to_delete_this_plugin'); ?></p>
		<p align="center">
		<a href="admin.php?action=packages&job=plugins_delete2&id=<?php echo $id; ?>"><img border="0" alt="" src="admin/html/images/yes.gif"> <?php echo $lang->phrase('admin_packages_yes'); ?></a>
		&nbsp&nbsp;&nbsp;&nbsp&nbsp;&nbsp;
		<a href="javascript: history.back(-1);"><img border="0" alt="" src="admin/html/images/no.gif"> <?php echo $lang->phrase('admin_packages_no'); ?></a>
		</p>
		</td></tr>
		</table>
		<?php
		echo foot();
	}
}
elseif ($job == 'plugins_delete2') {
	echo head();
	$id = $gpc->get('id', int);
	$result = $db->query("SELECT * FROM {$db->pre}plugins WHERE id = '{$id}' LIMIT 1", __LINE__, __FILE__);
	$data = $db->fetch_assoc($result);
	if ($db->num_rows($result) == 0) {
		error('admin.php?action=packages&job=plugins', $lang->phrase('admin_packages_err_specified_plugin_not_found'));
	}
	elseif ($data['required'] == 1) {
		error('admin.php?action=packages&job=plugins', $lang->phrase('admin_packages_err_specified_plugin_is_required_by_another_plugin_and_cannot_be_deleted'));
	}
	else {
		$dir = "modules/{$data['module']}/";
		$ini = $myini->read($dir."plugin.ini");
		$delete = true;
		$file = $ini['php'][$data['position']];
		foreach ($ini['php'] as $pos => $val) {
			if ($pos != $data['position'] && $file == $val) {
				$delete = false;
			}
		}
		unset($ini['php'][$data['position']]);
		unset($ini['names'][$data['position']]);
		unset($ini['required'][$data['position']]);
		if (file_exists($dir.$file) && $delete == true) {
			$filesystem->unlink($dir.$file);
		}
		$myini->write($dir."plugin.ini", $ini);

		$db->query("DELETE FROM {$db->pre}plugins WHERE id = '{$id}' LIMIT 1", __LINE__, __FILE__);
		// Delete references in navigation aswell
		$db->query("DELETE FROM {$db->pre}menu WHERE module = '{$id}'", __LINE__, __FILE__);

		$delobj = $scache->load('modules_navigation');
		$delobj->delete();
		$filesystem->unlink('cache/modules/'.$plugins->_group($data['position']).'.php');

		ok('admin.php?action=packages&job=plugins', $lang->phrase('admin_packages_ok_plugin_successfully_deleted'));
	}
}
elseif ($job == 'plugins_hook_pos') {
	echo head();
	$hook = $gpc->get('hook', none);
	$hooks = getHookArray();
	foreach ($hooks as $file => $positions) {
		foreach ($positions as $h) {
			if ($hook == $h) {
				break 2;
			}
		}
	}
	?>
	<table class="border" border="0" cellspacing="0" cellpadding="4" align="center">
	 <tr>
	  <td class="obox">
	   <span class="right"><a class="button" href="javascript: self.close();"><?php echo $lang->phrase('admin_packages_hook_pos_close_window'); ?></a></span>
	   <?php
	   	$hook_temp = "<em>$hook</em>";
	   	$file_temp = "<em>$file</em>";
	   	echo $lang->phrase('admin_packages_hook_pos_source_code_around_the_hook_foo_in_the_file_bar');
	   ?>
	  </td>
	 </tr>
	 <tr>
	  <td class="mbox">
	  <?php
	  if (file_exists($file)) {
		$data = htmlspecialchars(file_get_contents($file));
		$data = str_replace("\t", "    ", $data);
		$data = str_replace("  ", "&nbsp;&nbsp;", $data);
		$search = preg_quote(htmlspecialchars('$plugins->load(\''.$hook.'\')'), '~');
		$data = preg_replace('~('.$search.')~i', '<a name="key"><span style="font-weight: bold; color: maroon;">\1</span></a>', $data);
		$data = preg_split("~(\r\n|\r|\n)~", $data);
		echo "<ol style='width: 560px;'>";
		foreach ($data as $row) {
			echo "<li class=\"monospace\">{$row}</li>";
		}
		echo "</ol>";
	  }
	  else {
		echo $lang->phrase('admin_packages_hook_pos_there_is_no_file_for_this_hook');
	  }
	  ?>
	  </td>
	 </tr>
	</table>
	<?php
	echo foot();
}
elseif ($job == 'plugins_edit') {
	echo head();
	$pos = $gpc->get('id', none);
	$packageid = $gpc->get('package', int);

	if (is_id($packageid)) {
		$dir = "modules/{$packageid}/";
		if (file_exists("{$dir}plugin.ini") == false) {
			error("admin.php?action=packages&job=plugins", $lang->phrase('admin_packages_err_plugin_not_found_in_plugin_ini'));
		}
		$ini = $myini->read("{$dir}plugin.ini");
		$package = array(
			'module' => $packageid,
			'position' => $pos,
			'title' => $ini['names'][$pos],
			'active' => 1,
			'required' => $ini['required'][$pos]
		);
		$pluginid = 0;
	}
	else {
		$pluginid = $pos = $gpc->save_int($pos);
		$result = $db->query("
		SELECT p.*, m.title
		FROM {$db->pre}plugins AS p
			LEFT JOIN {$db->pre}packages AS m ON p.module = m.id
		WHERE p.id = '{$pluginid}'
		LIMIT 1
		", __LINE__, __FILE__);
		$package = $db->fetch_assoc($result);
		if ($db->num_rows($result) != 1) {
			error("admin.php?action=packages&job=plugins", $lang->phrase('admin_packages_err_plugin_not_found_in_database'));
		}
		$dir = "modules/{$package['module']}/";
		$ini = $myini->read($dir.'plugin.ini');
	}


	$hooks = getHookArray();
	if (!isset($ini['php'][$package['position']])) {
		$code = '';
		$codefile = 'Unknown';
	}
	else {
		$codefile = $ini['php'][$package['position']];
		$code = file_get_contents($dir.$codefile);
	}
	$cp = array();
	foreach ($ini['php'] as $ihook => $ifile) {
		if ($ifile == $codefile) {
			$cp[] = $ihook;
		}
	}
	sort($cp);
	?>
	<form method="post" action="admin.php?action=packages&amp;job=plugins_edit2&amp;id=<?php echo $pos; ?>&amp;package=<?php echo $packageid; ?>">
	<table class="border" border="0" cellspacing="0" cellpadding="4" align="center">
	 <tr>
	  <td class="obox" colspan="2"><?php echo $lang->phrase('admin_packages_plugins_edit_head_edit_plugin'); ?></td>
	 </tr>
	 <tr class="mbox">
	  <td width="25%"><?php echo $lang->phrase('admin_packages_plugins_edit_title_for_plugin'); ?><br /><span class="stext"><?php echo $lang->phrase('admin_packages_edit_title_text'); ?></span></td>
	  <td width="75%"><input type="text" name="title" size="40" value="<?php echo $package['title']; ?>" /></td>
	 </tr>
	 <tr class="mbox">
	  <td><?php echo $lang->phrase('admin_packages_plugins_edit_package'); ?></td>
	  <td><strong><?php echo $package['title']; ?></strong></td>
	 </tr>
	 <tr class="mbox">
	  <td><?php echo $lang->phrase('admin_packages_plugins_add_hook'); ?></td>
	  <td>
	  <?php if (is_id($pluginid) && $package['required'] == 0) { ?>
	  <select name="hook" id="hook">
	  <?php foreach ($hooks as $group => $positions) { ?>
	  <optgroup label="<?php echo $group; ?>">
		  <?php foreach ($positions as $hook) { ?>
		  <option value="<?php echo $hook; ?>"<?php echo iif($hook == $package['position'], ' selected="selected"'); ?>><?php echo $hook; ?></option>
		  <?php } ?>
	  </optgroup>
	  <?php } ?>
	  </select> <a class="button" href="#" onclick="return openHookPosition();" target="_blank"><?php echo $lang->phrase('admin_packages_plugins_edit_show_source_code_around_this_hook'); ?></a>
	  <?php } else { echo $package['position']; ?>
		<input type="hidden" name="hook" value="<?php echo $package['position']; ?>" />
	  <?php } ?>
	  </td>
	 </tr>
	 <tr class="mbox" valign="top">
	  <td>
	  <?php echo $lang->phrase('admin_packages_plugins_edit_code'); ?><br /><br />
	  <ul>
		<li><a href="admin.php?action=packages&amp;job=plugins_template&amp;id=<?php echo $package['module']; ?>" target="_blank"><?php echo $lang->phrase('admin_packages_plugins_edit_add_edit_templates'); ?></a></li>
		<li><a href="admin.php?action=packages&amp;job=plugins_language&amp;id=<?php echo $package['module']; ?>" target="_blank"><?php echo $lang->phrase('admin_packages_plugins_add_add_edit_phrase'); ?></a></li>
	  </ul>
	  <?php if (count($cp) > 0) { ?>
	  <br /><br /><span class="stext"><?php echo $lang->phrase('admin_packages_plugins_edit_caution'); ?></span>
	  <ul>
	  <?php foreach ($cp as $ihook) { ?>
	  	<li class="stext"><?php echo $ihook; ?></li>
	  <?php } ?>
	  </ul>
	  <?php } ?>
	  </td>
	  <td><textarea name="code" rows="10" cols="80" class="texteditor"><?php echo htmlspecialchars($code); ?></textarea></td>
	 </tr>
	 <tr class="mbox">
	  <td width="25%">
	  	<?php
	  		$path_temp = '<code>'.$config['fpath'].'/modules/'.$package['module'].'</code>';
	  		echo $lang->phrase('admin_packages_file_for_code');
	  		echo '<br />';
	  		echo '<span class="stext">'.$lang->phrase('admin_packages_file_for_code_text').'</span>';
	  	?>
	  </td>
	  <td width="75%"><?php echo $codefile; ?></td>
	 </tr>
	 <?php if ($package['required'] == 0 && is_id($pluginid)) { ?>
	 <tr class="mbox">
	  <td><?php echo $lang->phrase('admin_packages_plugins_add_active'); ?></td>
	  <td><input type="checkbox" name="active" value="1"<?php echo iif($package['active'] == 1, ' checked="checked"'); ?> /></td>
	 </tr>
	 <?php } else { ?>
	 	<input type="hidden" name="active" value="1" />
	 <?php } ?>
	 <tr>
	  <td class="ubox" colspan="2" align="center"><input type="submit" value="<?php echo $lang->phrase('admin_packages_button_save'); ?>" /></td>
	 </tr>
	</table>
	</form>
	<?php
	echo foot();
}
elseif ($job == 'plugins_edit2') {
	echo head();
	$id = $gpc->get('id', none);
	$package = $gpc->get('package', int);
	$title = $gpc->get('title', str);
	$hook = $gpc->get('hook', str);
	$code = $gpc->get('code', none);
	$active = $gpc->get('active', int);

	if (is_id($package) == true) {
		$dir = "modules/{$package}/";
		$ini = $myini->read($dir."plugin.ini");
		$data = array(
			'module' => $package,
			'position' => $id,
			'required' => $ini['required'][$id]
		);
	}
	else {
		$id = $pos = $gpc->save_int($id);
		$result = $db->query("SELECT module, position, required FROM {$db->pre}plugins WHERE id = '{$id}' LIMIT 1", __LINE__, __FILE__);
		$data = $db->fetch_assoc($result);
		if ($db->num_rows($result) != 1) {
			error("admin.php?action=packages&job=plugins", $lang->phrase('admin_packages_err_plugin_not_found'));
		}
		$dir = "modules/{$data['module']}/";
		$ini = $myini->read($dir."plugin.ini");
	}

	if (strlen($title) < 4) {
		error('admin.php?action=packages&job=plugins_edit&id='.$id, $lang->phrase('admin_packages_err_minimum_number_of_characters_for_title'));
	}
	elseif (strlen($title) > 200) {
		error('admin.php?action=packages&job=plugins_edit&id='.$id, $lang->phrase('admin_packages_err_maximum_number_of_characters_for_title'));
	}

	if (is_id($package) == false) {
		$db->query("UPDATE {$db->pre}plugins SET `name` = '{$title}', `active` = '{$active}', `position` = '{$hook}' WHERE id = '{$id}' LIMIT 1", __LINE__, __FILE__);
	}

	$file = $ini['php'][$data['position']];

	if (!is_dir($dir.$file)) {
		$filesystem->chmod($dir.$file, 0666);
	}
	$filesystem->file_put_contents($dir.$file, $code);

	if ($data['position'] != $hook && is_id($package) == false) {
		$ini['php'][$hook] = $file;
		$ini['names'][$hook] = $title;
		$ini['required'][$hook] = 0;
		unset($ini['php'][$data['position']]);
		unset($ini['names'][$data['position']]);
		unset($ini['required'][$data['position']]);
		$filesystem->unlink('cache/modules/'.$plugins->_group($hook).'.php');
	}

	$myini->write($dir."plugin.ini", $ini);
	$filesystem->unlink('cache/modules/'.$plugins->_group($data['position']).'.php');

	ok('admin.php?action=packages&job=plugins', $lang->phrase('admin_packages_ok_plugin_successfully_edited'));
}
elseif ($job == 'plugins_add') {
	echo head();
	$packageid = $gpc->get('id', int);
	if ($packageid > 0) {
		$result = $db->query("SELECT title FROM {$db->pre}packages WHERE id = '{$packageid}' LIMIT 1");
		$package = $db->fetch_assoc($result);
	}
	else {
		$result = $db->query("SELECT id, title FROM {$db->pre}packages");
	}
	$hooks = getHookArray();
	?>
	<form method="post" action="admin.php?action=packages&job=plugins_add2">
	<table class="border" border="0" cellspacing="0" cellpadding="4" align="center">
	 <tr>
	  <td class="obox" colspan="2"><?php echo $lang->phrase('admin_packages_plugins_add_head_add_plugin_1'); ?></td>
	 </tr>
	 <tr class="mbox">
	  <td width="25%"><?php echo $lang->phrase('admin_packages_plugins_edit_title_for_plugin'); ?><br /><span class="stext"><?php echo $lang->phrase('admin_packages_edit_title_text'); ?></span></td>
	  <td width="75%"><input type="text" name="title" size="40" /></td>
	 </tr>
	 <tr class="mbox">
	  <td><?php echo $lang->phrase('admin_packages_plugins_edit_package'); ?></td>
	  <td>
	  <?php if ($packageid > 0) { ?>
		<strong><?php echo $package['title']; ?></strong>
		<input type="hidden" name="package" value="<?php echo $packageid; ?>" />
	  <?php } else { ?>
	  <select name="package">
	  	<?php while ($row = $db->fetch_assoc($result)) { ?>
	  	<option value="<?php echo $row['id']; ?>"><?php echo $row['title']; ?></option>
	  	<?php } ?>
	  </select>
	  <?php } ?>
	  </td>
	 </tr>
	 <tr class="mbox">
	  <td><?php echo $lang->phrase('admin_packages_plugins_add_hook'); ?></td>
	  <td><select name="hook" id="hook">
	  <?php foreach ($hooks as $group => $positions) { ?>
	  <optgroup label="<?php echo $group; ?>">
		  <?php foreach ($positions as $hook) { ?>
		  <option value="<?php echo $hook; ?>"><?php echo $hook; ?></option>
		  <?php } ?>
	  </optgroup>
	  <?php } ?>
	  </select> <a class="button" href="#" onclick="return openHookPosition();" target="_blank"><?php echo $lang->phrase('admin_packages_plugins_edit_show_source_code_around_this_hook'); ?></a></td>
	 </tr>
	 <tr>
	  <td class="ubox" colspan="2" align="center"><input type="submit" value="<?php echo $lang->phrase('admin_packages_button_save'); ?>" /></td>
	 </tr>
	</table>
	</form>
	<?php
	echo foot();
}
elseif ($job == 'plugins_add2') {
	echo head();
	$hook = $gpc->get('hook', str);
	$isInvisibleHook = isInvisibleHook($hook);
	$packageid = $id = $gpc->get('package', int);
	$title = $gpc->get('title', str);
	$result = $db->query("SELECT id, title FROM {$db->pre}packages WHERE id = '{$packageid}' LIMIT 1", __LINE__, __FILE__);
	if ($db->num_rows($result) != 1) {
		echo head();
		error('admin.php?action=packages&job=plugins_add', $lang->phrase('admin_packages_err_specified_package_foo_does_not_exist'));
	}
	$package = $db->fetch_assoc($result);
	if (strlen($title) < 4) {
		error('admin.php?action=packages&job=plugins_add&id='.$package['id'], $lang->phrase('admin_packages_err_minimum_number_of_characters_for_title'));
	}
	elseif (strlen($title) > 200) {
		error('admin.php?action=packages&job=plugins_add&id='.$package['id'], $lang->phrase('admin_packages_err_maximum_number_of_characters_for_title'));
	}

	if (!$isInvisibleHook) {
		$hookPriority = $db->query("SELECT id, name, ordering FROM {$db->pre}plugins WHERE position = '{$hook}' ORDER BY ordering", __LINE__, __FILE__);

		$db->query("
		INSERT INTO {$db->pre}plugins
		(`name`,`module`,`ordering`,`active`,`position`)
		VALUES
		('{$title}','{$package['id']}','-1','0','{$hook}')
		", __LINE__, __FILE__);
		$pluginid = $db->insert_id();
	}
	else {
		$pluginid = 0;
	}

	$filetitle = convert2adress($title);
	$dir = "modules/{$package['id']}/";
	$codefile = "{$filetitle}.php";
	$i = 1;
	while (file_exists($dir.$codefile)) {
		$codefile = "{$filetitle}_{$i}.php";
		$i++;
	}

	$last = null;
	?>
	<form method="post" action="admin.php?action=packages&job=plugins_add3&id=<?php echo $pluginid; ?>&package=<?php echo $package['id']; ?>">
	<table class="border" border="0" cellspacing="0" cellpadding="4" align="center">
	 <tr>
	  <td class="obox" colspan="2"><?php echo $lang->phrase('admin_packages_plugins_add_head_add_plugin_2'); ?></td>
	 </tr>
	 <tr class="mbox">
	  <td width="25%"><?php echo $lang->phrase('admin_packages_plugins_edit_title_for_plugin'); ?><br /><span class="stext"><?php echo $lang->phrase('admin_packages_edit_title_text'); ?></span></td>
	  <td width="75%"><input type="text" name="title" size="40" value="<?php echo htmlspecialchars($title); ?>" /></td>
	 </tr>
	 <tr class="mbox">
	  <td><?php echo $lang->phrase('admin_packages_plugins_edit_package'); ?></td>
	  <td><strong><?php echo $package['title']; ?></strong> (<?php echo $package['id']; ?>)</td>
	 </tr>
	 <tr class="mbox">
	  <td><?php echo $lang->phrase('admin_packages_plugins_add_hook'); ?></td>
	  <td><strong><?php echo $hook; ?></strong><input type="hidden" name="hook" value="<?php echo $hook; ?>"> <a class="button" href="#" onclick="return openHookPosition('<?php echo $hook; ?>');" target="_blank"><?php echo $lang->phrase('admin_packages_plugins_edit_show_source_code_around_this_hook'); ?></a></td>
	 </tr>
	 <tr class="mbox" valign="top">
	  <td>
	  <?php echo $lang->phrase('admin_packages_plugins_edit_code'); ?><br /><br />
	  <span class="stext"><?php echo $lang->phrase('admin_packages_plugins_add_code_text'); ?></span>
	  <br /><br />
	  <ul>
		<li><a href="admin.php?action=packages&amp;job=plugins_template&amp;id=<?php echo $package['id']; ?>" target="_blank"><?php echo $lang->phrase('admin_packages_plugins_edit_add_edit_templates'); ?></a></li>
		<li><a href="admin.php?action=packages&amp;job=plugins_language&amp;id=<?php echo $package['id']; ?>" target="_blank"><?php echo $lang->phrase('admin_packages_plugins_add_add_edit_phrase'); ?></a></li>
	  </ul>
	  </td>
	  <td><textarea name="code" rows="10" cols="80" class="texteditor"></textarea></td>
	 </tr>
	 <tr class="mbox">
	  <td width="25%">
	   <?php echo $lang->phrase('admin_packages_file_for_code'); ?>
	  	<br />
	  	<?php
	  		$path_temp = '<code>'.$config['fpath'].'/modules/'.$package['id'].'/</code>';
	  		echo '<span class="stext">'.$lang->phrase('admin_packages_file_for_code_text').$lang->phrase('admin_packages_file_for_code_text_ext').'</span>';
	  	?>
	  </td>
	  <td width="75%"><input type="text" name="file" size="40" value="<?php echo $codefile; ?>" /></td>
	 </tr>
	 <?php if (!$isInvisibleHook) { ?>
	 <tr class="mbox">
	  <td><?php echo $lang->phrase('admin_packages_plugins_add_priority'); ?></td>
	  <td><select name="priority">
	  <?php while ($row = $db->fetch_assoc($hookPriority)) { $last = $row['name']; ?>
	  <option value="<?php echo $row['id']; ?>"><?php echo $lang->phrase('admin_packages_plugins_add_before'); ?> <?php echo $row['name']; ?></option>
	  <?php } ?>
	  <option value="max"><?php echo $lang->phrase('admin_packages_plugins_add_after'); ?> <?php echo $last; ?></option>
	  </select></td>
	 </tr>
	 <tr class="mbox">
	  <td><?php echo $lang->phrase('admin_packages_plugins_add_required_by_package'); ?></td>
	  <td><input type="checkbox" name="required" value="1" /></td>
	 </tr>
	 <tr class="mbox">
	  <td><?php echo $lang->phrase('admin_packages_plugins_add_active'); ?></td>
	  <td><input type="checkbox" name="active" value="1" /></td>
	 </tr>
	 <?php } ?>
	 <tr>
	  <td class="ubox" colspan="2" align="center"><input type="submit" value="Save" /></td>
	 </tr>
	</table>
	</form>
	<?php
}
elseif ($job == 'plugins_add3') {
	echo head();
	$id = $gpc->get('id', int);
	$package = $gpc->get('package', int);
	$title = $gpc->get('title', str);
	$code = $gpc->get('code', none);
	$file = $gpc->get('file', none);
	$priority = $gpc->get('priority', none);
	$required = $gpc->get('required', int);
	$active = $gpc->get('active', int);

	$isInvisibleHook = (is_id($id) == false);

	if (!$isInvisibleHook) {
		$result = $db->query("SELECT module, name, position FROM {$db->pre}plugins WHERE id = '{$id}' LIMIT 1", __LINE__, __FILE__);
		$data = $db->fetch_assoc($result);
		$package = $data['module'];
		$hook = $data['position'];
		$dir = "modules/{$data['module']}/";

		if (strlen($title) < 4 || strlen($title) > 200) {
			$title = $data['title'];
		}
		if ($required == 1) {
			$active = 1;
		}

		if (is_id($priority)) {
			$result = $db->query("SELECT id, ordering FROM {$db->pre}plugins WHERE id = '{$priority}' LIMIT 1", __LINE__, __FILE__);
			$row = $db->fetch_assoc($result);
			$priority = $row['ordering']-1;
			$result = $db->query("UPDATE {$db->pre}plugins SET ordering = ordering-1 WHERE ordering < '{$priority}' AND position = '{$data['position']}'", __LINE__, __FILE__);
		}
		else {
			$result = $db->query("SELECT MAX(ordering) AS maximum FROM {$db->pre}plugins WHERE position = '{$data['position']}'", __LINE__, __FILE__);
			$row = $db->fetch_assoc($result);
			$priority = $row['maximum']+1;
		}

		$db->query("UPDATE {$db->pre}plugins SET `name` = '{$title}', `ordering` = '{$priority}', `active` = '{$active}', `required` = '{$required}' WHERE id = '{$id}' LIMIT 1", __LINE__, __FILE__);
	}
	else {
		$dir = "modules/{$package}/";
		$required = 1;
		$hook = $gpc->get('hook', none);
	}

	if (file_exists($dir.$file) == false) {
		$filesystem->file_put_contents($dir.$file, $code);
		$filesystem->chmod($dir.$file, 0666);
	}

	if (file_exists($dir."plugin.ini") == true) {
		$ini = $myini->read($dir."plugin.ini");
	}
	else {
		$ini = array();
	}
	$ini['php'][$hook] = $file;
	$ini['names'][$hook] = $title;
	$ini['required'][$hook] = $required;
	$myini->write($dir."plugin.ini", $ini);

	if (!$isInvisibleHook) {
		$filesystem->unlink('cache/modules/'.$plugins->_group($hook).'.php');
	}
	if ($hook == 'navigation') {
		ok('admin.php?action=cms&job=nav_addplugin&id='.$package, $lang->phrase('admin_packages_ok_step_3_of_3_plugin_successfully_added_to_navigation'));
	}
	else {
		ok('admin.php?action=packages&job=plugins_add&id='.$package, $lang->phrase('admin_packages_ok_step_3_of_3_plugin_successfully_added'));
	}
}
elseif ($job == 'plugins_template') {
	$id = $gpc->get('id', int);

	$result = $db->query("SELECT id, title FROM {$db->pre}packages WHERE id = '{$id}' LIMIT 1", __LINE__, __FILE__);
	if ($db->num_rows($result) != 1) {
		echo head();
		error('javascript: self.close();', $lang->phrase('admin_packages_err_specified_package_foo_does_not_exist'));
	}
	$data = $db->fetch_assoc($result);
	$dir = "modules/{$data['id']}/";
	if (file_exists($dir."plugin.ini")) {
		$ini = $myini->read($dir."plugin.ini");
	}
	else {
		$ini = array();
	}

	$designObj = $scache->load('loaddesign');
	$designs = $designObj->get(true);
	$standardDesign = $designs[$config['templatedir']]['template'];
	$tpldir = "templates/{$standardDesign}/modules/{$data['id']}/";

	// ToDo: Pr�fen ob .html variabel sein sollte (class.template.php => Endung der Templates ist variabel, nur standardm��ig html)
	$filetitle = convert2adress($data['title']);
	$codefile = "{$filetitle}.html";
	$i = 1;
	while (file_exists($tpldir.$codefile)) {
		$codefile = "{$filetitle}_{$i}.html";
		$i++;
	}

	echo head();
	?>
	<form method="post" action="admin.php?action=packages&job=plugins_template_edit&id=<?php echo $data['id']; ?>">
	<table class="border" border="0" cellspacing="0" cellpadding="4" align="center">
	 <tr>
	  <td class="obox" colspan="3">
	  <span style="float: right;"><a class="button" href="javascript: self.close();"><?php echo $lang->phrase('admin_packages_plugins_template_close_window'); ?></a></span>
	  <?php echo $lang->phrase('admin_packages_plugins_template_manage_templates_for_package').$data['title']; ?></td>
	 </tr>
	 <?php if (isset($ini['template']) && count($ini['template']) > 0) { ?>
	 <tr class="mbox">
	  <td width="10%"><?php echo $lang->phrase('admin_packages_plugins_template_th_edit'); ?></td>
	  <td width="10%"><?php echo $lang->phrase('admin_packages_delete'); ?></td>
	  <td width="80%"><?php echo $lang->phrase('admin_packages_plugins_template_th_file'); ?></td>
	 </tr>
	 <?php foreach ($ini['template'] as $key => $file) { ?>
	 <tr class="mbox">
	  <td><input type="radio" name="edit" value="<?php echo $key; ?>" /></td>
	  <td><input type="checkbox" name="delete[]" value="<?php echo $key; ?>" /></td>
	  <td><?php echo $file; ?></td>
	 </tr>
	 <?php } ?>
	 <tr>
	  <td class="ubox" colspan="3" align="center"><input type="submit" value="<?php echo $lang->phrase('admin_packages_form_submit'); ?>" /></td>
	 </tr>
	 <?php } else { ?>
	 <tr class="mbox">
	  <td colspan="3"><?php echo $lang->phrase('admin_packages_plugins_template_no_template_available_for_this_package'); ?></td>
	 </tr>
	 <?php } ?>
	</table>
	</form>
	<br class="minibr" />
	<form method="post" action="admin.php?action=packages&job=plugins_template_add&id=<?php echo $data['id']; ?>">
	<table class="border" border="0" cellspacing="0" cellpadding="4" align="center">
	 <tr>
	  <td class="obox" colspan="2"><?php echo $lang->phrase('admin_packages_plugins_template_add_template'); ?></td>
	 </tr>
	 <tr class="mbox" valign="top">
	  <td>
	  <?php echo $lang->phrase('admin_packages_plugins_template_code'); ?><br /><br />
	  <ul>
		<li><a href="admin.php?action=packages&amp;job=plugins_language&amp;id=<?php echo $data['id']; ?>" target="_blank"><?php echo $lang->phrase('admin_packages_plugins_add_add_edit_phrase'); ?></a></li>
	  </ul>
	  </td>
	  <td><textarea name="code" rows="8" cols="80" class="texteditor"></textarea></td>
	 </tr>
	 <tr class="mbox">
	  <td width="25%"><?php echo $lang->phrase('admin_packages_file_for_code'); ?>
	  <br />
	  	<?php
	  		$path_temp = '<code>'.$config['fpath'].'/'.$tpldir.'</code>';
	  		echo '<span class="stext">'.$lang->phrase('admin_packages_file_for_code_text').'</span></td>';
	  	?>
	  <td width="75%"><input type="text" name="file" size="40" value="<?php echo $codefile; ?>" /></td>
	 </tr>
	 <tr>
	  <td class="ubox" colspan="2" align="center"><input type="submit" value="<?php echo $lang->phrase('admin_packages_button_save'); ?>" /></td>
	 </tr>
	</table>
	</form>
	<?php
	echo foot();
}
elseif ($job == 'plugins_template_add') {
	$id = $gpc->get('id', int);
	$code = $gpc->get('code', none);
	$file = $gpc->get('file', none);

	$result = $db->query("SELECT id, title FROM {$db->pre}packages WHERE id = '{$id}' LIMIT 1", __LINE__, __FILE__);
	if ($db->num_rows($result) != 1) {
		echo head();
		error('javascript: self.close();', $lang->phrase('admin_packages_err_specified_package_foo_does_not_exist'));
	}
	$data = $db->fetch_assoc($result);
	$dir = "modules/{$data['id']}/";

	$designObj = $scache->load('loaddesign');
	$designs = $designObj->get(true);
	$standardDesign = $designs[$config['templatedir']]['template'];
	$tpldir = "templates/{$standardDesign}/modules/{$data['id']}/";
	if (!is_dir($tpldir)) {
		$filesystem->mkdir($tpldir, 0777);
	}
	$filesystem->file_put_contents($tpldir.$file, $code);
	$filesystem->chmod($tpldir.$file, 0666);

	if (file_exists($dir."plugin.ini")) {
		$ini = $myini->read($dir."plugin.ini");
	}
	else {
		$ini = array();
	}
	$ini['template'][] = $file;
	$myini->write($dir."plugin.ini", $ini);

	echo head();
	ok('admin.php?action=packages&job=plugins_template&id='.$data['id']);
}
elseif ($job == 'plugins_template_edit') {
	echo head();

	$id = $gpc->get('id', int);
	$editId = $gpc->get('edit', int, -1);
	$deleteId = $gpc->get('delete', arr_int);
	$output = -1;

	$result = $db->query("SELECT id, title FROM {$db->pre}packages WHERE id = '{$id}' LIMIT 1", __LINE__, __FILE__);
	if ($db->num_rows($result) != 1) {
		echo head();
		error('javascript: self.close();', $lang->phrase('admin_packages_err_specified_package_foo_does_not_exist'));
	}
	$data = $db->fetch_assoc($result);
	$dir = "modules/{$data['id']}/";
	$ini = $myini->read($dir."plugin.ini");

	if (count($deleteId) > 0) {
		$designObj = $scache->load('loaddesign');
		$designs = $designObj->get(true);

		foreach ($deleteId as $key) {
			if (!isset($ini['template'][$key])) {
				continue;
			}
			$file = $ini['template'][$key];
			foreach ($designs as $row) {
				$tplfile = "templates/{$row['template']}/modules/{$data['id']}/{$file}";
				if (file_exists($tplfile)) {
					$filesystem->unlink($tplfile);
				}
			}
			unset($ini['template'][$key]);
		}

		$myini->write($dir."plugin.ini", $ini);
		$output = 0;
	}

	if ($editId > -1 && isset($ini['template'][$editId])) {
		if ($output == 0) {
			?>
			<table class="border" border="0" cellspacing="0" cellpadding="4" align="center">
			  <tr><td class="obox"><?php echo $lang->phrase('admin_packages_template_edit_head_confirmation'); ?></td></tr>
			  <tr><td class="mbox" align="center"><?php echo $lang->phrase('admin_packages_template_edit_template_successfully_deleted'); ?></td></tr>
			</table><br class="minibr" />
			<?php
		}
		$codefile = $ini['template'][$editId];
		$designObj = $scache->load('loaddesign');
		$designs = $designObj->get(true);

		$tpldirs = array();
		foreach ($designs as $designId => $row) {
			$dir = "templates/{$row['template']}/modules/{$data['id']}/";
			if (file_exists($dir.$codefile)) {
				$tpldirs[$row['template']]['names'][] = $row['name'];
				$tpldirs[$row['template']]['ids'][] = $row['id'];
			}
		}

		?>
		<form method="post" action="admin.php?action=packages&job=plugins_template_edit2&id=<?php echo $data['id']; ?>&edit=<?php echo $editId; ?>">
		<table class="border" border="0" cellspacing="0" cellpadding="4" align="center">
		 <tr>
		  <td class="obox" colspan="2"><?php echo $lang->phrase('admin_packages_template_edit_add_edit_template'); ?></td>
		 </tr>
		 <tr class="mbox" valign="top">
		  <td rowspan="<?php echo count($tpldirs); ?>">
			<?php echo $lang->phrase('admin_packages_template_edit_code'); ?><br /><br />
			<ul><li><a href="admin.php?action=packages&amp;job=plugins_language&amp;id=<?php echo $data['id']; ?>" target="_blank"><?php echo $lang->phrase('admin_packages_template_edit_add_edit_phrase'); ?></a></li></ul>
		  </td>
		  <?php
		  $first = true;
		  foreach ($tpldirs as $tplid => $designId) {
		  	if ( in_array($config['templatedir'], $designId['ids']) ) {
		  		$affected = $lang->phrase('admin_packages_template_edit_all_designs_that_have_not_defined_an_own_template');
		  	}
		  	else {
		  		$affected = implode(', ', $designId['names']);
		  	}
		  	$dir = "templates/{$tplid}/modules/{$data['id']}/";
		  	$content = file_get_contents($dir.$codefile);
		  	if ($first == false) {
		  		echo '<tr>';
		  		$first = false;
		  	}
		  	echo '<td>';
		  	echo $lang->phrase('admin_packages_template_edit_template_groups').'<b>'.$tplid.'</b><br />';
		  	echo $lang->phrase('admin_packages_template_edit_designs affected_by_changes').$affected.'<br />';
		  	echo '<textarea name="code['.$tplid.']" rows="8" cols="80" class="texteditor">'.$content.'</textarea>';
		  	echo '</td></tr>';
		  }
		  ?>
		 <tr class="mbox">
		  <td width="25%"><?php echo $lang->phrase('admin_packages_file_for_code'); ?></td>
		  <td width="75%"><?php echo $codefile; ?></td>
		 </tr>
		 <tr>
		  <td class="ubox" colspan="2" align="center"><input type="submit" value="<?php echo $lang->phrase('admin_packages_button_save'); ?>" /></td>
		 </tr>
		</table>
		</form>
		<?php
		$output = 1;
	}

	if ($output == -1) {
		error('admin.php?action=packages&job=plugins_template&id='.$data['id'], $lang->phrase('admin_packages_err_please_choose_at_least_one_template'));
	}
	elseif ($output == 0) {
		ok('admin.php?action=packages&job=plugins_template&id='.$data['id'], $lang->phrase('admin_packages_ok_template_successfully_deleted'));
	}
}
elseif ($job == 'plugins_template_edit2') {
	$id = $gpc->get('id', int);
	$editId = $gpc->get('edit', int, -1);
	$code = $gpc->get('code', arr_none);

	echo head();
	$result = $db->query("SELECT id FROM {$db->pre}packages WHERE id = '{$id}' LIMIT 1", __LINE__, __FILE__);
	if ($db->num_rows($result) != 1) {
		error('javascript: self.close();', $lang->phrase('admin_packages_err_specified_package_foo_does_not_exist'));
	}
	$data = $db->fetch_assoc($result);
	$ini = $myini->read("modules/{$data['id']}/plugin.ini");
	if (!isset($ini['template'][$editId])) {
		error('javascript: self.close();', $lang->phrase('admin_packages_err_specified_template_foo_does_not_exist_in_ini_file'));
	}
	$file = $ini['template'][$editId];

	foreach ($code as $tpldir => $html) {
		$filepath = "templates/{$tpldir}/modules/{$data['id']}/";
		if (is_dir($filepath)) {
			$filesystem->file_put_contents($filepath.$file, $html);
		}
	}
	ok('admin.php?action=packages&job=plugins_template&id='.$id);
}
elseif ($job == 'plugins_language') {
	echo head();

	$id = $gpc->get('id', int);
	$result = $db->query("SELECT id, title FROM {$db->pre}packages WHERE id = '{$id}' LIMIT 1", __LINE__, __FILE__);
	if ($db->num_rows($result) != 1) {
		echo head();
		error('javascript: self.close();', $lang->phrase('admin_packages_err_specified_package_foo_does_not_exist'));
	}
	$data = $db->fetch_assoc($result);

	if (file_exists("modules/{$data['id']}/plugin.ini")) {
		$ini = $myini->read("modules/{$data['id']}/plugin.ini");
	}
	else {
		$ini = array();
	}
	if (!isset($ini['language'])) {
		$ini['language'] = array();
	}

	$file = 'modules.lng.php';
	$group = substr($file, 0, strlen($file)-8);
	$page = $gpc->get('page', int, 1);
	$cache = array();
	$diff = array();
	$complete = array();
	$result = $db->query('SELECT * FROM '.$db->pre.'language ORDER BY language',__LINE__,__FILE__);
	while($row = $db->fetch_assoc($result)) {
		$cache[$row['id']] = $row;
		$diff[$row['id']] = array_keys(return_array($group, $row['id']));
		$complete = array_merge($complete, array_diff($diff[$row['id']], $complete) );
	}
	sort($complete);
	$width = floor(75/count($cache));
	?>
<form name="form" method="post" action="admin.php?action=packages&job=plugins_language_delete&id=<?php echo $id; ?>">
 <table class="border" border="0" cellspacing="0" cellpadding="4" align="center">
  <tr>
   <td class="obox" colspan="<?php echo count($cache)+1; ?>">
   <span style="float: right;"><a class="button" href="admin.php?action=packages&job=plugins_language_add&id=<?php echo $id; ?>"><?php echo $lang->phrase('admin_packages_plugins_language_add_new_phrases'); ?></a></span>
   <?php echo $lang->phrase('admin_packages_plugins_language_phrase_manager'); ?></td>
  </tr>
  <?php if (count($ini['language']) == 0) { ?>
  <tr>
   <td class="mbox" colspan="<?php echo count($cache)+1; ?>"><?php echo $lang->phrase('admin_packages_plugins_language_there_are_no_phrases_for_this_package'); ?> <a class="button" href="admin.php?action=packages&job=plugins_language_add&id=<?php echo $id; ?>"><?php echo $lang->phrase('admin_packages_plugins_language_add_a_new_phrase'); ?></a></td>
  </tr>
  <?php } else { ?>
  <tr>
   <td class="mmbox" width="25%">&nbsp;</td>
   <?php foreach ($cache as $row) { ?>
   <td class="mmbox" align="center" width="<?php echo $width; ?>%"><?php echo $row['language']; ?></td>
   <?php } ?>
  </tr>
  <?php foreach ($ini['language'] as $phrase => $value) { ?>
  <tr>
   <td class="mmbox"><input type="checkbox" name="delete[]" value="<?php echo $phrase; ?>">&nbsp;<a class="button" href="admin.php?action=packages&job=plugins_language_edit&phrase=<?php echo $phrase; ?>&id=<?php echo $id; ?>"><?php echo $lang->phrase('admin_packages_plugins_language_edit'); ?></a>&nbsp;<?php echo $phrase; ?></td>
   <?php
   foreach ($cache as $row) {
   	$status = in_array($phrase, $diff[$row['id']]);
   ?>
   <td class="mbox" align="center"><?php echo noki($status); ?></td>
   <?php } ?>
  </tr>
  <?php } ?>
  <tr>
   <td class="ubox" align="center" colspan="<?php echo count($cache)+1; ?>"><input type="submit" value="<?php echo $lang->phrase('admin_packages_button_delete_selected_phrases'); ?>"></td>
  </tr>
  <?php } ?>
 </table>
</form>
	<?php
	echo foot();
}
elseif ($job == 'plugins_language_add') {
	echo head();

	$id = $gpc->get('id', int);
	$result = $db->query("SELECT id, title FROM {$db->pre}packages WHERE id = '{$id}' LIMIT 1", __LINE__, __FILE__);
	if ($db->num_rows($result) != 1) {
		echo head();
		error('javascript: self.close();', $lang->phrase('admin_packages_err_specified_package_foo_does_not_exist'));
	}
	$data = $db->fetch_assoc($result);

	$result = $db->query('SELECT * FROM '.$db->pre.'language ORDER BY language',__LINE__,__FILE__);
	?>
<form name="form" method="post" action="admin.php?action=packages&job=plugins_language_save2&id=<?php echo $id; ?>">
 <table class="border" border="0" cellspacing="0" cellpadding="4" align="center">
  <tr>
   <td class="obox" colspan="2"><?php echo $lang->phrase('admin_packages_language_add_phrase_manager_add_new_phrase_to_package'); ?></td>
  </tr>
  <tr>
   <td class="mbox" width="50%"><?php echo $lang->phrase('admin_packages_language_edit_varname'); ?><br />
   <span class="stext"><?php echo $lang->phrase('admin_packages_language_edit_varname_text'); ?></span></td>
   <td class="mbox" width="50%"><input type="text" name="varname" size="50" value="" /></td>
  </tr>
  <tr>
   <td class="mbox" width="50%"><?php echo $lang->phrase('admin_packages_language_edit_text'); ?><br />
   <span class="stext"><?php echo $lang->phrase('admin_packages_language_edit_text_text'); ?></span></td>
   <td class="mbox" width="50%"><input type="text" name="text" size="50" /></td>
  </tr>
  <tr>
   <td class="obox" colspan="2"><?php echo $lang->phrase('admin_packages_language_edit_translations'); ?></td>
  </tr>
  <tr>
   <td class="ubox" colspan="2"><ul>
	<li><?php echo $lang->phrase('admin_packages_language_edit_translations_text_1'); ?></li>
	<li><?php echo $lang->phrase('admin_packages_language_edit_translations_text_2'); ?></li>
   </ul></td>
  </tr>
  <?php while($row = $db->fetch_assoc($result)) { ?>
  <tr>
   <td class="mbox" width="50%"><em><?php echo $row['language']; ?></em> <?php echo $lang->phrase('admin_packages_language_edit_translation'); ?><br /><span class="stext"><?php echo $lang->phrase('admin_packages_language_edit_translation_text'); ?></span></td>
   <td class="mbox" width="50%"><input type="text" name="langt[<?php echo $row['id']; ?>]" size="50" /></td>
  </tr>
  <?php } ?>
  <tr>
   <td class="ubox" colspan="2" align="center"><input type="submit" name="Submit" value="<?php echo $lang->phrase('admin_packages_button_save'); ?>" /></td>
  </tr>
 </table>
</form>
	<?php
	echo foot();
}
elseif ($job == 'plugins_language_save2') {
	echo head();

	$id = $gpc->get('id', int);
	$varname = $gpc->get('varname', none);
	$text = $gpc->get('text', none);
	$langt = $gpc->get('langt', none);

	if (empty($text)) {
		error('javascript: history.back(-1);', $lang->phrase('admin_packages_err_no_default_text_specified'));
	}

	$result = $db->query("SELECT id, title FROM {$db->pre}packages WHERE id = '{$id}' LIMIT 1", __LINE__, __FILE__);
	if ($db->num_rows($result) != 1) {
		error('javascript: self.close();', $lang->phrase('admin_packages_err_specified_package_foo_does_not_exist'));
	}
	$data = $db->fetch_assoc($result);

	$c = new manageconfig();
	foreach ($langt as $id => $t) {
		if (empty($t)) {
			$t = $text;
		}
		$c->getdata("language/{$id}/modules.lng.php", 'lang');
		$c->updateconfig($varname, str, $t);
		$c->savedata();
	}

	if (file_exists("modules/{$data['id']}/plugin.ini")) {
		$ini = $myini->read("modules/{$data['id']}/plugin.ini");
	}
	else {
		$ini = array();
	}
	if (!isset($ini['language']) || !is_array($ini['language']) || (is_array($ini['language']) && count($ini['language']) == 0)) {
		$ini['language'] = array();
	}
	$ini['language'][$varname] = $text;
	$dirs = array();
	$langcodes = getLangCodes();
	foreach ($langcodes as $code => $lid) {
		$langdata = return_array('modules', $lid);
		$langdata = array_intersect_key($langdata, $ini['language']);
		if ($lid == $config['langdir']) {
			$ini['language'] = $langdata;
		}
		else {
			$ini['language_'.$code] = $langdata;
		}
	}
	$myini->write("modules/{$data['id']}/plugin.ini", $ini);

	ok('admin.php?action=packages&job=plugins_language&id='.$data['id']);
}
elseif ($job == 'plugins_language_delete') {
	echo head();

	$id = $gpc->get('id', int);
	$delete = $gpc->get('delete', arr_str);

	$result = $db->query("SELECT id, title FROM {$db->pre}packages WHERE id = '{$id}' LIMIT 1", __LINE__, __FILE__);
	if ($db->num_rows($result) != 1) {
		echo head();
		error('javascript: self.close();', $lang->phrase('admin_packages_err_specified_package_foo_does_not_exist'));
	}
	$data = $db->fetch_assoc($result);

	$ini = $myini->read("modules/{$data['id']}/plugin.ini");
	$langkeys = array();
	foreach ($ini as $key => $x) {
		if (substr($key, 0, 8) == 'language') {
			$langkeys[] = $key;
		}
	}
	foreach ($delete as $phrase) {
		foreach ($langkeys as $key) {
			unset($ini[$key][$phrase]);
		}
	}
	$myini->write("modules/{$data['id']}/plugin.ini", $ini);

	$result = $db->query('SELECT * FROM '.$db->pre.'language ORDER BY language',__LINE__,__FILE__);
	$c = new manageconfig();
	while($row = $db->fetch_assoc($result)) {
		$path = "language/{$row['id']}/modules.lng.php";
		if (file_exists($path)) {
			$c->getdata($path, 'lang');
			foreach ($delete as $phrase) {
				$c->delete($phrase);
			}
			$c->savedata();
		}
	}
	ok('admin.php?action=packages&job=plugins_language&id='.$data['id'], $lang->phrase('admin_packages_ok_selected_phrase_were_successfully_deleted'));
}
elseif ($job == 'plugins_language_edit') {
	echo head();

	$phrase = $gpc->get('phrase', none);
	$id = $gpc->get('id', int);
	$result = $db->query("SELECT id, title FROM {$db->pre}packages WHERE id = '{$id}' LIMIT 1", __LINE__, __FILE__);
	if ($db->num_rows($result) != 1) {
		echo head();
		error('javascript: self.close();', $lang->phrase('admin_packages_err_specified_package_foo_does_not_exist'));
	}
	$data = $db->fetch_assoc($result);

	$dir = "modules/{$data['id']}/";
	$ini = $myini->read($dir."plugin.ini");
	if (!isset($ini['language'][$phrase])) {
		error('admin.php?action=packages&job=plugins_edit&id=7', $lang->phrase('admin_packages_err_phrase_not_found'));
	}

	$result = $db->query('SELECT * FROM '.$db->pre.'language ORDER BY language',__LINE__,__FILE__);
	?>
<form name="form" method="post" action="admin.php?action=packages&job=plugins_language_save2&id=<?php echo $id; ?>">
 <table class="border" border="0" cellspacing="0" cellpediting="4" align="center">
  <tr>
   <td class="obox" colspan="2"><?php echo $lang->phrase('admin_packages_language_edit_head_phrase_manager_edit_phrase'); ?></td>
  </tr>
  <tr>
   <td class="mbox" width="50%"><?php echo $lang->phrase('admin_packages_language_edit_varname'); ?><br />
   <span class="stext"><?php echo $lang->phrase('admin_packages_language_edit_varname_text'); ?></span></td>
   <td class="mbox" width="50%"><input type="hidden" name="varname" size="50" value="<?php echo $phrase; ?>" /><code><?php echo $phrase; ?></code></td>
  </tr>
  <tr>
   <td class="mbox" width="50%"><?php echo $lang->phrase('admin_packages_language_edit_text'); ?><br />
   <span class="stext"><?php echo $lang->phrase('admin_packages_language_edit_text_text'); ?></span></td>
   <td class="mbox" width="50%"><input type="text" name="text" size="50" value="<?php echo htmlspecialchars(nl2whitespace($ini['language'][$phrase])); ?>" /></td>
  </tr>
  <tr>
   <td class="obox" colspan="2"><?php echo $lang->phrase('admin_packages_language_edit_translations'); ?></td>
  </tr>
  <tr>
   <td class="ubox" colspan="2"><ul>
	<li><?php echo $lang->phrase('admin_packages_language_edit_translations_text_1'); ?></li>
	<li><?php echo $lang->phrase('admin_packages_language_edit_translations_text_2'); ?></li>
   </ul></td>
  </tr>
  <?php
  while($row = $db->fetch_assoc($result)) {
  	$phrases = return_array('modules', $row['id']);
  	if (!isset($phrases[$phrase])) {
  		$phrases[$phrase] = '';
  	}
  ?>
  <tr>
   <td class="mbox" width="50%"><em><?php echo $row['language']; ?></em> <?php echo $lang->phrase('admin_packages_language_edit_translation'); ?><br /><span class="stext"><?php echo $lang->phrase('admin_packages_language_edit_translation_text'); ?></span></td>
   <td class="mbox" width="50%"><input type="text" name="langt[<?php echo $row['id']; ?>]" size="50" value="<?php echo htmlspecialchars(nl2whitespace($phrases[$phrase])); ?>" /></td>
  </tr>
  <?php } ?>
  <tr>
   <td class="ubox" colspan="2" align="center"><input type="submit" name="Submit" value="Save" /></td>
  </tr>
 </table>
</form>
	<?php
	echo foot();
}
elseif ($job == 'package_updates') {
	$id = $gpc->get('id', int);
	echo head();
	if (is_id($id)) {
		$result = $db->query("SELECT internal, version FROM {$db->pre}packages WHERE id = '{$id}'");
		$data = $db->fetch_assoc($result);
		if (empty($data['version'])) {
			error('admin.php?action=packages&job=package', $lang->phrase('admin_packages_err_no_information_about_the_current_version_found'));
		}
		$pb = $scache->load('package_browser');
		$row = $pb->getOne(IMPTYPE_PACKAGE, $data['internal']);
		if ($row !== false && !empty($row['version'])) {
			if (version_compare($row['version'], $data['version'], '>')) {
				ok('admin.php?action=packages&job=browser_detail&id='.$row['internal'].'&package='.IMPTYPE_PACKAGE, $lang->phrase('admin_packages_ok_there_is_a_new_version_foo_on_the_server'), 3000);
			}
			else {
				ok('admin.php?action=packages&job=package_info&id='.$id, $lang->phrase('admin_packages_ok_this_package_seems_to_be_up_to_date'), 3000);
			}
			break;
		}
		ok('admin.php?action=packages&job=package_info&id='.$id, $lang->phrase('admin_packages_ok_the_package_was_not_found_on_one_of_the_known_servers'), 3000);
	}
	else {
		$result = $db->query("SELECT * FROM {$db->pre}packages GROUP BY internal ORDER BY title", __LINE__, __FILE__);
		$pb = $scache->load('package_browser');
		?>
		 <table class="border" border="0" cellspacing="0" cellpadding="4" align="center">
		  <tr>
		   <td class="obox" colspan="4"><?php echo $lang->phrase('admin_packages_version_check_all'); ?></td>
		  </tr>
		  <tr>
		  	<td class="ubox" width="30%"><?php echo $lang->phrase('admin_packages_info_name'); ?></td>
		  	<td class="ubox center" width="15%"><?php echo $lang->phrase('admin_packages_active_version'); ?></td>
		  	<td class="ubox center" width="15%"><?php echo $lang->phrase('admin_packages_newest_version'); ?></td>
		  	<td class="ubox" width="40%"><?php echo $lang->phrase('admin_packages_action'); ?></td>
		  </tr>
		  <?php
		  while($row = $db->fetch_assoc($result)) {
			if (empty($row['version'])) {
				$row['version'] = $lang->phrase('admin_packages_version_na');
			}
			$data = $pb->getOne(IMPTYPE_PACKAGE, $row['internal']);
			$new = null;
			if ($data !== false && !empty($data['version'])) {
				if (version_compare($data['version'], $row['version'], '>')) {
					$new = true;
				}
				elseif (version_compare($data['version'], $row['version'], '<=')) {
					$new = false;
				}
			}
			else {
				$data = array(
					'version' => $lang->phrase('admin_packages_version_na')
				);
			}
			$hl = iif($new !== null, iif($new === true, ' style="color: firebrick;"', ' style="color: forestgreen;"'));
		  	?>
		  <tr>
		  	<td class="mbox"><strong<?php echo $hl; ?>><?php echo $row['title']; ?></strong></td>
		  	<td class="mbox center"><?php echo $row['version']; ?></td>
		  	<td class="mbox center"><?php echo $data['version']; ?></td>
		  	<td class="mbox">
		  		<?php if (!empty($data['update'])) { ?>
		  		<a class="button" href="admin.php?action=packages&amp;job=browser_update&amp;id=<?php echo $row['internal']; ?>"><?php echo $lang->phrase('admin_packages_install_update'); ?></a>
		  		<?php } ?>
		  		<a class="button" href="admin.php?action=packages&amp;job=package_info&amp;id=<?php echo $row['id']; ?>"><?php echo $lang->phrase('admin_packages_current_details'); ?></a>
		  		<a class="button" href="admin.php?action=packages&amp;job=browser_detail&amp;id=<?php echo $row['internal']; ?>&amp;package=<?php echo IMPTYPE_PACKAGE; ?>"><?php echo $lang->phrase('admin_packages_browser_details'); ?></a>
		  	</td>
		  </tr>
		  <?php } ?>
		 </table>
		<?php
		echo foot();
	}
}
elseif ($job == 'browser') {
	$pb = $scache->load('package_browser');
	$types = $pb->types();
	$type = $gpc->get('type', int, IMPTYPE_PACKAGE);
	$cats = $pb->categories($type);
	if (count($cats) > 0) {
		// Calculate random entry
		unset($cat);
		$i = 0;
		do {
			$keys = array_keys($cats);
			shuffle($keys);
			$rid = current($keys);
			$cat = $pb->categories($type, $rid);
			$i++;
		} while (empty($cat['entries']) && $i < 200);
		$e = $pb->get($type, $rid);
		shuffle($e);
		$random = current($e);
	}
	else {
		$random = array();
	}
	echo head();
	?>
 <table class="border" border="0" cellspacing="0" cellpediting="4" align="center">
  <tr>
   <td class="obox" colspan="2"><?php $foo = $types[$type]; echo $lang->phrase('admin_packages_browser_head_browse_foo'); ?></td>
  </tr>
  <tr>
   <td class="ubox" width="50%"><strong><?php echo $lang->phrase('admin_packages_browser_categories'); ?></strong></td>
   <td class="ubox" width="50%"><strong><?php echo $lang->phrase('admin_packages_browser_useful_links'); ?></strong></td>
  <tr>
   <td class="mbox" valign="top" rowspan="3">
   	<?php if (count($cats) > 0) { ?>
	<ul>
		<?php foreach ($cats as $id => $row) { ?>
		<li><a href="admin.php?action=packages&amp;job=browser_list&amp;type=<?php echo $type; ?>&amp;id=<?php echo $id; ?>"><?php echo $row['name']; ?></a> (<?php echo $row['entries']; ?>)</li>
		<?php } ?>
	</ul>
	<?php } else { $foo = $types[$type]; echo $lang->phrase('admin_packages_no_x_found'); } ?>
   </td>
   <td class="mbox" valign="top">
	<ul>
		<?php if (count($cats) > 0) { ?>
		<li><a href="admin.php?action=packages&amp;job=browser_list&amp;type=<?php echo $type; ?>&amp;id=last"><?php echo $lang->phrase('admin_packages_browser_recently_updated'); ?> <?php echo $types[$type]['name']; ?></a></li>
		<?php } ?>
		<li><a href="admin.php?action=settings&amp;job=admin"><?php $foo = $types[$type]; echo $lang->phrase('admin_packages_browser_change_servers_that_offer_foo'); ?></a></li>
	</ul>
   </td>
  </tr>
  <tr>
   <td class="ubox" valign="top"><?php $foo = ucfirst($types[$type]['name2']); echo $lang->phrase('admin_packages_browser_foo_of_the_moment');?></td>
  </tr>
  <tr>
   <td class="mbox" valign="top">
   <?php if (count($random) > 0) { ?>
	<a href="admin.php?action=packages&amp;job=browser_detail&amp;id=<?php echo $random['internal']; ?>&amp;type=<?php echo $type; ?>"><strong><?php echo $random['title']; ?></strong> <?php echo $random['version']; ?></a><br />
	<?php echo $random['summary'];
   }
   else {
   	$foo = $types[$type];
	echo $lang->phrase('admin_packages_no_x_found');
   }
   ?>
   </td>
  </tr>
 </table>
	<?php
	echo foot();
}
elseif ($job == 'browser_list') {
	$id = $gpc->get('id', none);
	$type = $gpc->get('type', int, IMPTYPE_PACKAGE);
	$pb = $scache->load('package_browser');
	$types = $pb->types();
	if (is_numeric($id)) {
		$data = $pb->get($type, $id);
		$cat = $pb->categories($type, $id);
		$title = $cat['name'];
		$show_cat = false;
	}
	else {
		$data2 = $pb->get($type);
		$data = array();
		foreach ($data2 as $key => $rows) {
			if (is_numeric($key)) {
				$data = array_merge($data, $rows);
			}
		}
		unset($data2);
		uasort($data, "browser_sort_date");
		$data = array_slice($data, 0, 10);
		$show_cat = true;
		$title = $lang->phrase('admin_packages_browser_recently_updated').' '.$types[$type]['name'];
	}

	if ($type == IMPTYPE_PACKAGE) {
		$result = $db->query("SELECT id, internal, version FROM {$db->pre}packages");
		$installed = array();
		while($row = $db->fetch_assoc($result)) {
			$installed[$row['internal']] = $row;
		}
	}
	elseif ($type == IMPTYPE_BBCODE) {
		$installed = null; // ToDo: Check for installed bb-codes
	}
	else {
		$installed = null;
	}

	echo head();
	?>
 <table class="border" border="0" cellspacing="0" cellpediting="4" align="center">
  <tr>
   <td class="obox" colspan="4"><?php $foo = $types[$type]; echo $lang->phrase('admin_packages_browser_browse_foo'); ?> &raquo; <?php echo $title; ?></td>
  </tr>
  <?php
  if (count($data) == 0) {
  	$foo = $types[$type];
  	?>
  <tr>
   <td class="mbox" colspan="4"><?php echo $lang->phrase('admin_packages_no_x_found'); ?></td>
  </tr>
  	<?php
  }
  else {
  	?>
  <tr>
   <td class="ubox" width="60%"><?php echo $lang->phrase('admin_packages_info_name'); ?><br /><?php echo $lang->phrase('admin_packages_head_description'); ?></td>
   <?php if (is_array($installed)) { ?>
   <td class="ubox" width="10%"><?php echo $lang->phrase('admin_packages_browser_installed'); ?></td>
   <?php } ?>
   <td class="ubox" width="10%"><?php echo $lang->phrase('admin_packages_info_compatible'); ?></td>
   <td class="ubox" width="30%"><?php echo $lang->phrase('admin_packages_browser_last_update2'); ?><br /><?php echo $lang->phrase('admin_packages_browser_license2'); ?></td>
  </tr>
  	<?php
  foreach ($data as $key => $row) {
 	$min_compatible = ((!empty($row['min_version']) && version_compare($config['version'], $row['min_version'], '>=')) || empty($row['min_version']));
	$max_compatible = ((!empty($row['max_version']) && version_compare($config['version'], $row['max_version'], '<=')) || empty($row['max_version']));
	$compatible = ($min_compatible && $max_compatible);
	$install = isset($installed[$row['internal']]);
	$update = $install && version_compare($installed[$row['internal']]['version'], $row['version'], '<');
  	?>
  <tr class="mbox">
   <td valign="top">
    <span class="right">
    	<?php if (!$install || $row['multiple'] == 1) { ?>
    	<a class="button" href="admin.php?action=packages&amp;job=browser_import&amp;id=<?php echo $key; ?>&amp;type=<?php echo $type; ?>"><?php echo $lang->phrase('admin_packages_browser_import'); ?></a>
    	<?php } if ($install) { ?>
    	<a class="button" href="admin.php?action=packages&amp;job=package_info&amp;id=<?php echo $installed[$row['internal']]['id']; ?>"><?php echo $lang->phrase('admin_packages_browser_go_to_installed_package'); ?></a>
    	<?php } ?>
    </span>
   	<a href="admin.php?action=packages&amp;job=browser_detail&amp;id=<?php echo $key; ?>&amp;type=<?php echo $type; ?>"><strong><?php echo $row['title']; ?></strong> <?php echo $row['version']; ?></a><br />
   	<span class="stext"><?php echo $row['summary']; ?></span>
   </td>
   <?php if (is_array($installed)) { ?>
   <td align="center"><?php echo iif($install, 'Yes'.iif($update, '<br /><span class="stext" style="font-color: darkred;">'.$lang->phrase('admin_packages_browser_update_available').'</span>'), $lang->phrase('admin_packages_no')); ?></td>
   <?php } ?>
   <td align="center"><?php echo noki($compatible); ?></td>
   <td valign="top">
    <?php echo $lang->phrase('admin_packages_browser_last_update'); ?> <?php echo gmdate('d.m.Y', times($row['last_updated'])); ?><br />
    <?php echo $lang->phrase('admin_packages_info_license'); ?> <?php echo empty($row['license']) ? $lang->phrase('admin_packages_unknown') : $row['license']; ?>
   	<?php if($show_cat == true) { $cat = $pb->categories($type, $row['category']); ?><br /><?php echo $lang->phrase('admin_packages_browser_category'); ?> <?php echo $cat['name']; } ?>
   	</td>
  </tr>
  <?php } } ?>
 </table>
	<?php
	echo foot();
}
elseif ($job == 'browser_import') {
	$type = $gpc->get('type', int, IMPTYPE_PACKAGE);
	$id = $gpc->get('id', str);
	$pb = $scache->load('package_browser');
	$row = $pb->getOne($type, $id);
	$types = $pb->types();
	$file = 'temp/'.basename($row['file']);
	$filesystem->file_put_contents($file, get_remote($row['file']));
	header('Location: '.$types[$type]['import'].$file);
}
elseif ($job == 'browser_update') {
	$id = $gpc->get('id', str);
	$pb = $scache->load('package_browser');
	$row = $pb->getOne(IMPTYPE_PACKAGE, $id);
	$types = $pb->type(IMPTYPE_PACKAGE);
	$file = 'temp/'.basename($row['file']);
	$filesystem->file_put_contents($file, get_remote($row['file']));
	header('Location: '.$types['update'].$file);
}
elseif ($job == 'browser_detail') {
	$type = $gpc->get('type', int, IMPTYPE_PACKAGE);
	$id = $gpc->get('id', str);
	$pb = $scache->load('package_browser');
	$types = $pb->types();
	$row = $pb->getOne($type, $id);
	if ($row == null) {
		$foo = $types[$type];
		echo head();
		error('admin.php?action=packages&job=browser', $lang->phrase('admin_packages_no_x_found'));
	}
	$cat = $pb->categories($type, $row['category']);
	$result = $db->query("SELECT id, version FROM {$db->pre}packages WHERE internal = '{$row['internal']}' LIMIT 1");
	if ($db->num_rows($result) == 1) {
		$pack = $db->fetch_assoc($result);
		$installed = $pack['id'];
	}
	else {
		$installed = false;
	}
	echo head('onload="ResizeImg(FetchElement(\'preview\'),640)"');
	$foo = $types[$type];
	?>
	 <table class="border" border="0" cellspacing="0" cellpadding="4" align="center">
	  <tr>
	   <td class="obox" colspan="2">
	  	<?php if ($installed === false) { ?>
	  	<span class="right"><a class="button" href="admin.php?action=packages&amp;job=browser_import&amp;id=<?php echo $id; ?>&amp;type=<?php echo $type; ?>"><?php $foo = $types[$type]; echo $lang->phrase('admin_packages_browser_import_this'); ?></a></span>
	  	<?php } ?>
	    <?php echo $lang->phrase('admin_packages_browser_browse_foo'); ?> &raquo; <?php echo $cat['name']; ?> &raquo; <?php echo $row['title']; ?>
	   </td>
	  </tr>
	  <tr>
	   <td class="mbox" width="30%"><?php echo $lang->phrase('admin_packages_browser_det_name'); ?></td>
	   <td class="mbox" width="70%"><a href="<?php echo $row['url']; ?>" target="_blank"><?php echo $row['title']; ?></a></td>
	  </tr>
	  <tr>
	   <td class="mbox" width="30%"><?php echo $lang->phrase('admin_packages_info_description'); ?></td>
	   <td class="mbox" width="70%"><?php echo nl2br($row['summary']); ?></td>
	  </tr>
	  <?php if ($type == IMPTYPE_PACKAGE) { ?>
	  <tr>
	   <td class="mbox" width="30%"><?php echo $lang->phrase('admin_packages_browser_status'); ?></td>
	   <td class="mbox" width="70%">
	   <?php if ($installed === false) { ?>
	   <?php echo $lang->phrase('admin_packages_browser_you_have_not_installed_this_package'); ?>&nbsp;&nbsp;&nbsp;&nbsp;<a class="button" href="admin.php?action=packages&amp;job=browser_import&amp;id=<?php echo $id; ?>&amp;type=<?php echo $type; ?>"><?php echo $lang->phrase('admin_packages_browser_import_this'); ?>!</a>
	   <?php }
	   else {
	   		$vc = version_compare($pack['version'], $row['version']);
	   		?>
	   		<span class="right">
	   		<?php if ($vc == -1 && !empty($row['update'])) { ?>
	   		<a class="button" href="admin.php?action=packages&amp;job=browser_update&amp;id=<?php echo $id; ?>"><?php echo $lang->phrase('admin_packages_install_update'); ?></a>
	   		<?php } ?>
	   		<a class="button" href="admin.php?action=packages&amp;job=package_info&amp;id=<?php echo $installed; ?>"><?php echo $lang->phrase('admin_packages_browser_go_to_installed_package'); ?></a>
	   		</span>
	   		<?php
	   		if ($vc == 1) { ?>
	   		<strong style="color: goldenrod;"><?php $foo = $types[$type]; echo $lang->phrase('admin_packages_browser_you_have_installed_a_newer_version_of_this_foo'); ?>.</strong>
		    <?php } elseif($vc == -1) { ?>
		    <strong style="color: firebrick;"><?php echo $lang->phrase('admin_packages_browser_you_have_installed_an_old_version'); ?> (<?php echo $pack['version']; ?>)!</strong>
		    <?php } else { ?>
		    <strong style="color: forestgreen;"><?php $foo = $types[$type]; echo $lang->phrase('admin_packages_browser_you_have_installed_this_foo'); ?>.</strong>
	    <?php } } ?>
	   </td>
	  </tr>
	  <?php } if (!empty($row['last_updated'])) { ?>
	  <tr>
	   <td class="mbox" width="30%"><?php echo $lang->phrase('admin_packages_browser_last_update'); ?></td>
	   <td class="mbox" width="70%"><?php echo gmdate('d.m.Y H:i', times($row['last_updated'])); ?></td>
	  </tr>
	  <?php } if (!empty($row['copyright'])) { ?>
	  <tr>
	   <td class="mbox" width="30%"><?php echo $lang->phrase('admin_packages_info_copyright'); ?></td>
	   <td class="mbox" width="70%"><?php echo str_ireplace('(C)', '&copy;', $row['copyright']); ?></td>
	  </tr>
	  <?php } if (!empty($row['license'])) { ?>
	  <tr>
	   <td class="mbox" width="30%"><?php echo $lang->phrase('admin_packages_info_license'); ?></td>
	   <td class="mbox" width="70%"><?php echo $row['license']; ?></td>
	  </tr>
	  <?php } if (!empty($row['version'])) { ?>
	  <tr>
	   <td class="mbox" width="30%"><?php echo $lang->phrase('admin_packages_info_version'); ?></td>
	   <td class="mbox" width="70%"><?php echo $row['version']; ?></td>
	  </tr>
	  <?php } if (!empty($row['min_version']) || !empty($row['max_version'])) { ?>
	  <tr>
	   <td class="mbox" width="30%"><?php echo $lang->phrase('admin_packages_info_compatibility'); ?></td>
	   <td class="mbox" width="70%">
	   	<?php if (!empty($row['min_version'])) { $min = $row['min_version']; ?>
	   	<div><?php echo $lang->phrase('admin_packages_minimum_v'); ?></div>
	   	<?php } ?>
	   	<?php if (!empty($row['max_version'])) { $max = $row['max_version']; ?>
	   	<div><?php echo $lang->phrase('admin_packages_maximum_v'); ?></div>
	   	<?php } ?>
	   </td>
	  </tr>
	  <?php } if (isset($row['license'])) { ?>
	  <tr>
	   <td class="mbox" width="30%"><?php echo $lang->phrase('admin_packages_browser_multiple_installations_allowed'); ?></td>
	   <td class="mbox" width="70%"><?php echo noki($row['multiple']); ?></td>
	  </tr>
	  <?php } ?>
	  <tr>
	   <td class="mbox" width="30%"><?php echo $lang->phrase('admin_packages_browser_server'); ?></td>
	   <td class="mbox" width="70%"><?php echo $row['server']; ?></td>
	  </tr>
	  <tr>
	   <td class="mbox" width="30%"><?php echo $lang->phrase('admin_packages_browser_file'); ?></td>
	   <td class="mbox" width="70%"><a href="<?php echo $row['file']; ?>"><?php echo $row['file']; ?></a></td>
	  </tr>
	  <tr>
	   <td class="mbox" width="30%"><?php echo $lang->phrase('admin_packages_info_internal_name'); ?></td>
	   <td class="mbox" width="70%"><tt><?php echo $row['internal']; ?></tt></td>
	  </tr>
	  <?php if (!empty($row['preview'])) { ?>
	  <tr>
	   <td class="ubox" colspan="2"><?php echo $lang->phrase('admin_packages_browser_preview'); ?></td>
	  </tr>
	  <tr>
	   <td class="mbox center" colspan="2"><img id="preview" src="<?php echo $row['preview']; ?>" border="0" alt="Preview/Screenshot" /></td>
	  </tr>
	  <?php } ?>
	 </table>
	<?php
	echo foot();
}
?>
