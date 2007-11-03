<?php
if (defined('VISCACHA_CORE') == false) { die('Error: Hacking Attempt'); }

require('admin/lib/function.language.php');
require('classes/class.phpconfig.php');
include('admin/lib/function.settings.php');
$myini = new INI();

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
	   <td class="obox">Package Manager</td>
	  </tr>
	  <tr>
	  	<td class="mbox center">
	  		<?php if ($my->settings['admin_interface'] != 1) { ?>
			<a class="button" href="admin.php?action=packages&amp;job=com" target="Main">Component Manager</a>
			<a class="button" href="admin.php?action=packages&amp;job=plugins" target="Main">Plugin Manager</a>
	  		<?php } ?>
	  		<a class="button" href="admin.php?action=packages&amp;job=browser">Browse Packages</a>
	  		<a class="button" href="admin.php?action=packages&amp;job=package_import">Import Package</a>
	  		<a class="button" href="admin.php?action=packages&amp;job=package_add">Create Package</a>
	  	</td>
	  </tr>
	 </table><br class="minibr" />
	 <table class="border" border="0" cellspacing="0" cellpadding="4" align="center">
	  <tr>
	   <td class="obox" colspan="4">Installed Packages</td>
	  </tr>
	  <tr>
	  	<td class="ubox" width="30%">Name</td>
	  	<td class="ubox center" width="10%">Active</td>
	  	<td class="ubox center" width="10%">Core</td>
	  	<td class="ubox" width="50%">Actions</td>
	  </tr>
	  <?php while($row = $db->fetch_assoc($result)) { ?>
	  <tr>
	  	<td class="mbox"><a href="admin.php?action=packages&amp;job=package_info&amp;id=<?php echo $row['id']; ?>"><strong><?php echo $row['title']; ?></strong> <?php echo $row['version']; ?></a></td>
	  	<td class="mbox center"><?php echo noki($row['active']); ?></td>
	  	<td class="mbox center"><?php echo noki($row['core']); ?></td>
	  	<td class="mbox">
	  		<a class="button" href="admin.php?action=packages&amp;job=package_info&amp;id=<?php echo $row['id']; ?>">Package Details</a>
	  		<?php
	  		if (file_exists("modules/{$row['id']}/component.ini")) {
				$com = $myini->read("modules/{$row['id']}/component.ini");
	  			if (!empty($com['admin']['frontpage']) == true) {
	  		?>
	  		<a class="button" href="admin.php?action=packages&amp;job=package_admin&amp;cid=<?php echo $row['id']; ?>">Administration</a>
	  		<?php } } if ($row['config'] > 0) { ?>
	  		<a class="button" href="admin.php?action=settings&amp;job=custom&amp;id=<?php echo $row['config']; ?>&amp;package=<?php echo $row['id']; ?>">Configuration</a>
	  		<?php } ?>
	  		<a class="button" href="admin.php?action=packages&amp;job=package_edit&amp;id=<?php echo $row['id']; ?>">Edit</a>
	  		<?php if ($row['core'] != '1') { ?>
	  		<a class="button" href="admin.php?action=packages&amp;job=package_active&amp;id=<?php echo $row['id']; ?>"><?php echo iif($row['active'] == 1, 'Deactivate', 'Activate'); ?></a>
	  		<?php } ?>
	  		<a class="button" href="admin.php?action=packages&amp;job=package_updates&amp;id=<?php echo $row['id']; ?>">Check for Updates</a>
	  		<a class="button" href="admin.php?action=packages&amp;job=package_export&amp;id=<?php echo $row['id']; ?>">Export</a>
	  		<?php if ($row['core'] != '1') { ?>
	  		<a class="button" href="admin.php?action=packages&amp;job=package_delete&amp;id=<?php echo $row['id']; ?>">Delete</a>
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
		error('admin.php?action=cms&job=package','There is no package with the specified ID.');
	}
	$row = $db->fetch_assoc($result);
	$com = $myini->read('modules/'.$row['id'].'/component.ini');
	if (!isset($com['admin'][$mod])) {
		echo head();
		error('admin.php?action=cms&job=package','Section not found!');
	}

	DEFINE('COM_ID', $row['id']);
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
	include("modules/{$row['id']}/{$file}");
}
elseif ($job == 'package_import') {
	echo head();
	$file = $gpc->get('file', str);
	?>
<form name="form" method="post" action="admin.php?action=packages&amp;job=package_import2" enctype="multipart/form-data">
 <table class="border" border="0" cellspacing="0" cellpadding="4" align="center">
  <tr>
   <td class="obox" colspan="2">Import a new Component</td>
  </tr>
  <tr>
   <td class="mbox" width="50%"><em>Either</em> upload a file:<br /><span class="stext">Compressed file (.zip) containing the component. Maximum file size: <?php echo formatFilesize(ini_maxupload()); ?>. You should install only components from confidential sources!</td>
   <td class="mbox" width="50%"><input type="file" name="upload" size="40" /></td>
  </tr>
  <tr>
   <td class="mbox"><em>or</em> select a file from the server:<br /><span class="stext">Path starting from the Viscacha-root-directory: <?php echo $config['fpath']; ?></span></td>
   <td class="mbox"><input type="text" name="server" size="50" value="<?php echo $file; ?>" /></td>
  </tr>
  <tr>
   <td class="mbox">Skip version check:</td>
   <td class="mbox"><input type="checkbox" name="version" value="1" /></td>
  </tr>
  <tr>
   <td class="mbox">Delete file after import:</td>
   <td class="mbox"><input type="checkbox" name="delete" value="1" checked="checked" /></td>
  </tr>
  <tr>
   <td class="ubox" width="100%" colspan="2" align="center"><input type="submit" name="Submit" value="Upload"></td>
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
			$inserterrors[] = 'The selected file is not a ZIP-file.';
		}
	}
	if (!file_exists($sourcefile)) {
		$inserterrors[] = 'Selected file does not exist.';
	}
	if (count($inserterrors) > 0) {
		error('admin.php?action=designs&job=package_import', $inserterrors);
	}
	else {
		$c = new manageconfig();

		$tdir = "temp/".md5(microtime()).'/';
		$filesystem->mkdir($tdir, 0777);
		if (!is_dir($tdir)) {
			error('admin.php?action=packages&job=package_import', 'Temporary directory could not be created for extraction.');
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
					error('admin.php?action=packages&job=package_import', 'This package requires at least Viscacha '.$package['info']['min_version']);
				}
				if (!empty($package['info']['max_version']) && version_compare($config['version'], $package['info']['max_version'], '>')) {
					error('admin.php?action=packages&job=package_import', 'This package is only compatible with Viscacha '.$package['info']['max_version'].' and lower.');
				}
			}
			$package = $gpc->save_str($package);
			if (!isset($package['core'])) {
				$package['info']['core'] = 0;
			}
		}
		else {
			error('admin.php?action=packages&job=package_import', 'package.ini does not exist!');
		}

		$result = $db->query("SELECT id FROM {$db->pre}packages WHERE internal = '{$package['info']['internal']}'", __LINE__, __FILE__);
		if ($db->num_rows($result) > 0 && $package['multiple'] == 0) {
			error('admin.php?action=packages&job=package_import', 'A package with the internal name '.$package['info']['internal'].' is already installed.');
		}
		$db->query("INSERT INTO {$db->pre}packages (title, version, internal, core) VALUES ('{$package['info']['title']}', '{$package['info']['version']}', '{$package['info']['internal']}', '{$package['info']['core']}')", __LINE__, __FILE__);
		$packageid = $db->insert_id();

		$filesystem->mkdir("./modules/{$packageid}", 0777);
		copyr("{$tdir}modules", "./modules/{$packageid}");

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

		if (file_exists($tdir.'modules/component.ini')) {
			$com = $myini->read($tdir.'modules/component.ini');
			if (isset($com['info']) && count($com['info']) > 0) {
				$com['info'] = $gpc->save_str($com['info']);

				if (!isset($com['module']['frontpage'])) {
					$com['module']['frontpage'] = '';
				}

				$db->query("INSERT INTO {$db->pre}component (file, package, required) VALUES ('".$gpc->save_str($com['module']['frontpage'])."', '{$packageid}', '{$com['info']['required']}')", __LINE__, __FILE__);
				$comid = $db->insert_id();

				if (isset($com['images']) && count($com['images']) > 0) {
					foreach ($com['images'] as $file) {
						$filesystem->copy("{$tdir}images/{$file}", "./images/{$design['images']}/{$file}");
					}
				}

				$result = $db->query("SELECT DISTINCT stylesheet FROM {$db->pre}designs",__LINE__,__FILE__);
				if (isset($com['designs']) && count($com['designs']) > 0) {
					while ($css = $db->fetch_assoc($result)) {
						foreach ($com['designs'] as $file) {
							$filesystem->copy("{$tdir}designs/{$file}", "./designs/{$css['stylesheet']}/{$file}");
						}
					}
				}

				if (isset($com['language']) && count($com['language']) > 0) {
					$d = dir($tdir.'language/');
					$codes = array();
					while (false !== ($entry = $d->read())) {
					   	if (preg_match('~^(\w{2})_?(\w{0,2})$~i', $entry, $code) && is_dir("{$tdir}/{$entry}")) {
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
					$d->close();
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
							$filesystem->copy("{$tdir}/language{$src}/{$file}", "./language/{$lid}/modules/{$packageid}/{$file}");
						}
					}
				}

				$delobj = $scache->load('components');
				$delobj->delete();
			}
		}

		if (file_exists($tdir.'modules/plugin.ini')) {
			$plug = $myini->read("{$tdir}modules/plugin.ini");

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
			if (file_exists($tpldir)) {
				$filesystem->chmod($tpldir, 0777);
			}
			else {
				$filesystem->mkdir($tpldir, 0777);
			}
			$temptpldir = "{$tdir}templates/";
			copyr($temptpldir, $tpldir);
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
			ok('admin.php?action=packages&job=package_info&id='.$packageid, 'Package successfully imported!');
		}

	}
}
elseif ($job == 'package_export') {
	$id = $gpc->get('id', int);
	$result = $db->query("SELECT id, internal FROM {$db->pre}packages WHERE id = '{$id}' LIMIT 1", __LINE__, __FILE__);
	if ($db->num_rows() != 1) {
		echo head();
		error('admin.pgp?action=packages&job=package', 'Specified package does not exist.');
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
	$result = $db->query("SELECT id, core FROM {$db->pre}packages WHERE id = '{$id}' LIMIT 1", __LINE__, __FILE__);
	$row = $db->fetch_assoc($result);
	if ($db->num_rows($result) == 0) {
		error('admin.php?action=packages&job=package', 'Specified package not found.');
	}
	elseif ($row['core'] == '1') {
		error('admin.php?action=packages&job=package', 'This is a core package and can not be deleted.');
	}
	else {
		?>
		<table class="border" border="0" cellspacing="0" cellpadding="4" align="center">
		<tr><td class="obox">Delete Package</td></tr>
		<tr><td class="mbox">
		<p align="center">Do you really want to delete this package with all included plugins and components?</p>
		<p align="center">
		<a href="admin.php?action=packages&job=package_delete2&id=<?php echo $id; ?>"><img border="0" alt="Yes" src="admin/html/images/yes.gif"> Yes</a>
		&nbsp&nbsp;&nbsp;&nbsp&nbsp;&nbsp;
		<a href="javascript: history.back(-1);"><img border="0" alt="No" src="admin/html/images/no.gif"> No</a>
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
		error('admin.php?action=packages&job=package', 'Specified package not found.');
	}
	elseif ($package['core'] == '1') {
		echo head();
		error('admin.php?action=packages&job=package', 'This is a core package and can not be deleted.');
	}
	else {
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
			ok('admin.php?action=packages&job=package', 'Package successfully deleted!');
		}
	}
}
elseif ($job == 'package_edit') {
	$id = $gpc->get('id', int);
	$result = $db->query("SELECT * FROM {$db->pre}packages WHERE id = '{$id}'", __LINE__, __FILE__);
	$row = $gpc->prepare($db->fetch_assoc($result));

	$ini = $myini->read("modules/{$row['id']}/package.ini");
	echo head();
	?>
	<form method="post" action="admin.php?action=packages&amp;job=package_edit2&amp;id=<?php echo $row['id']; ?>">
	<table class="border" border="0" cellspacing="0" cellpadding="4" align="center">
	 <tr>
	  <td class="obox" colspan="2">Edit the Package &quot;<?php echo $row['title']; ?>&quot;</td>
	 </tr>
	 <tr class="mbox">
	  <td>Title:<br /><span class="stext">Maximum number of characters: 200; Minimum number of characters: 4</span></td>
	  <td><input type="text" name="title" size="60" value="<?php echo $row['title']; ?>" /></td>
	 </tr>
	 <?php if ($row['core'] != '1') { ?>
	 <tr class="mbox">
	  <td>Active:</td>
	  <td><input type="checkbox" name="active" value="1"<?php echo iif($row['active'] == 1, ' checked="checked"'); ?> /></td>
	 </tr>
	 <?php } ?>
	 <tr class="mbox">
	  <td>Description:<br /><span class="stext">Optional</span></td>
	  <td><textarea cols="60" rows="4" name="summary"><?php echo $ini['info']['summary']; ?></textarea></td>
	 </tr>
	 <tr class="mbox">
	  <td>Version:<br /><span class="stext">Optional</span></td>
	  <td><input type="text" name="version" size="60" value="<?php echo $row['version']; ?>" /></td>
	 </tr>
	 <tr class="mbox">
	  <td>Minimum Viscacha Version:<br /><span class="stext">Optional</span></td>
	  <td><input type="text" name="min_version" size="60" value="<?php echo $ini['info']['min_version']; ?>" /></td>
	 </tr>
	 <tr class="mbox">
	  <td>Maximum Viscacha Version:<br /><span class="stext">Optional</span></td>
	  <td><input type="text" name="max_version" size="60" value="<?php echo $ini['info']['max_version']; ?>" /></td>
	 </tr>
	 <tr class="mbox">
	  <td>Copyright:<br /><span class="stext">Optional</span></td>
	  <td><input type="text" name="copyright" size="60" value="<?php echo $ini['info']['copyright']; ?>" /></td>
	 </tr>
	 <tr class="mbox">
	  <td>License:<br /><span class="stext">Optional</span></td>
	  <td><input type="text" name="license" size="60" value="<?php echo $ini['info']['license']; ?>" /></td>
	 </tr>
	 <tr class="mbox">
	  <td>URL/Homepage:<br /><span class="stext">Optional.</span></td>
	  <td><input type="text" name="url" size="60" value="<?php echo $ini['info']['url']; ?>" /></td>
	 </tr>
	 <tr>
	  <td class="ubox" colspan="2" align="center"><input type="submit" value="Save your changes" /> before working on the settings below!</td>
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
	   <a class="button" href="admin.php?action=settings&amp;job=custom&amp;id=<?php echo $sg['id']; ?>&amp;package=<?php echo $row['id']; ?>">Change Settings</a>
	   <a class="button" href="admin.php?action=settings&amp;job=new&amp;package=<?php echo $row['id']; ?>">Add a new Setting</a>
	   <a class="button" href="admin.php?action=settings&amp;job=delete_group&amp;id=<?php echo $sg['id']; ?>&amp;package=<?php echo $row['id']; ?>">Delete all Settings</a>
	   <?php } ?>
	   </span>
	   Configuration
	   </td>
	  </tr>
	  <?php if (is_array($settings) && count($settings) > 0) { ?>
	  <tr class="ubox">
	   <td width="50%">Title</td>
	   <td width="30%">Internal name</td>
	   <td width="20%">Delete</td>
	  </tr>
	  <?php foreach ($settings as $setting) { ?>
	  <tr class="mbox">
		<td><?php echo $setting['title']; ?></td>
		<td class="monospace"><?php echo $setting['name']; ?></td>
	  	<td><a class="button" href="admin.php?action=settings&job=delete&name=<?php echo $setting['name']; ?>&id=<?php echo $setting['sgroup']; ?>&amp;package=<?php echo $row['id']; ?>">Delete Setting</a></td>
	  </tr>
	  <?php } } else { ?>
		<tr class="mbox">
			<td colspan="4">
				For this package are no settings specified.&nbsp;&nbsp;&nbsp;&nbsp;
				<?php if (count($sg) == 0) { ?>
				<a class="button" href="admin.php?action=settings&amp;job=new_group&amp;package=<?php echo $row['id']; ?>">Add a new Group for Settings</a>
				<?php } else { ?>
				<a class="button" href="admin.php?action=settings&amp;job=new&amp;package=<?php echo $row['id']; ?>">Add a new Setting</a>
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
		error('admin.php?action=packages&job=package', 'Could not find a package with the specified ID.');
	}
	$row = $db->fetch_assoc($result);
	if ($row['core'] != '1') {
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

	if (strlen($title) < 4) {
		error('admin.php?action=packages&job=package_edit&id='.$id, 'Minimum number of characters for title: 4');
	}
	elseif (strlen($title) > 200) {
		error('admin.php?action=packages&job=package_edit&id='.$id, 'Maximum number of characters for title: 200');
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
	$filesystem->chmod("modules/{$id}/package.ini", 0666);
	$myini->write("modules/{$id}/package.ini", $ini);


	ok('admin.php?action=packages&job=package_info&id='.$id, 'Package successfully edited.');
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
				$row['current'] = isset($val[$row['current']]) ? $gpc->prepare($val[$row['current']]) : '<em>Unknown</em>';
			}
			$settings[] = $row;
		}
	}

	?>
	 <table class="border" border="0" cellspacing="0" cellpadding="4" align="center">
	  <tr>
	   <td class="obox" colspan="2">
	   <span class="right">
	   <a class="button" href="admin.php?action=packages&amp;job=package_edit&amp;id=<?php echo $package['id']; ?>">Edit</a>
	    <?php if (!empty($component_ini['admin']['frontpage']) == true) { ?>
	  	 <a class="button" href="admin.php?action=packages&amp;job=package_admin&amp;cid=<?php echo $package['id']; ?>">Administration</a>
	  	<?php } ?>
	   </span>
	   Package Details for &quot;<?php echo $package['title']; ?>&quot;
	   </td>
	  </tr>
	  <tr>
	   <td class="ubox" colspan="2">General information</td>
	  </tr>
	  <tr>
	   <td class="mbox" width="30%">Description:</td>
	   <td class="mbox" width="70%"><?php echo nl2br($package_ini['info']['summary']); ?></td>
	  </tr>
	  <tr>
	   <td class="mbox" width="30%">Copyright:</td>
	   <td class="mbox" width="70%"><?php echo $package_ini['info']['copyright']; ?></td>
	  </tr>
	  <tr>
	   <td class="mbox" width="30%">License:</td>
	   <td class="mbox" width="70%"><?php echo $package_ini['info']['license']; ?></td>
	  </tr>
	  <tr>
	   <td class="mbox" width="30%">Version:</td>
	   <td class="mbox" width="70%"><?php echo $package_ini['info']['version']; ?>&nbsp;&nbsp;&nbsp;&nbsp;<a class="button" href="admin.php?action=packages&amp;job=package_updates&amp;id=<?php echo $package['id']; ?>">Check for Updates</a></td>
	  </tr>
	  <tr>
	   <td class="mbox" width="30%">Compatibility:</td>
	   <td class="mbox" width="70%">
	   	<?php if (!empty($package_ini['info']['min_version'])) { ?>
	   	<div>Minimum: <?php echo $package_ini['info']['min_version']; ?></div>
	   	<?php } ?>
	   	<?php if (!empty($package_ini['info']['max_version'])) { ?>
	   	<div>Maximum: <?php echo $package_ini['info']['max_version']; ?></div>
	   	<?php } ?>
	   </td>
	  </tr>
	  <tr>
	   <td class="mbox" width="30%">Internal name:</td>
	   <td class="mbox" width="70%"><tt><?php echo $package_ini['info']['internal']; ?></tt></td>
	  </tr>
	 </table>
	 <br class="minibr" />
	 <table class="border" border="0" cellspacing="0" cellpadding="4" align="center">
	  <tr>
	   <td class="obox" colspan="4">
	   <span class="right">
	   <?php if (count($sg) > 0) { ?>
	   <a class="button" href="admin.php?action=settings&amp;job=custom&amp;id=<?php echo $sg['id']; ?>&amp;package=<?php echo $package['id']; ?>">Change Settings</a>
	   <?php } ?>
	   </span>
	   Configuration
	   </td>
	  </tr>
	  <?php if (is_array($settings) && count($settings) > 0) { ?>
	  <tr class="ubox">
	   <td width="40%">Title</td>
	   <td width="40%">Current value</td>
	   <td width="20%">Internal name</td>
	  </tr>
	  <?php foreach ($settings as $setting) { ?>
	  <tr class="mbox">
		<td><?php echo $setting['title']; ?></td>
		<td><?php echo $setting['current']; ?></td>
		<td class="monospace"><?php echo $setting['name']; ?></td>
	  </tr>
	  <?php } } else { ?>
		<tr class="mbox">
			<td colspan="4">For this package are no settings specified.</td>
		</tr>
	  <?php } ?>
	 </table>
	 <br class="minibr" />
	 <table class="border" border="0" cellspacing="0" cellpadding="4" align="center">
	  <tr>
	   <td class="obox" colspan="4">Component</td>
	  </tr>
	  <?php if (is_array($component_ini) && count($component_ini) > 0) { ?>
	  <tr class="ubox">
	   <td width="80%">Name</td>
	   <td width="10%">Active</td>
	   <td width="10%">Required</td>
	  </tr>
	  <tr class="mbox">
		<td><?php echo $component_ini['info']['title']; ?></td>
		<td class="center">
		<?php if ($component['active'] == 1 && $package['active'] == 1) { ?>
		<img class="valign" src="admin/html/images/yes.gif" border="0" alt="Active" title="Component is active." />
		<?php } elseif ($component['active'] == 1 && $package['active'] == 0) { ?>
		<img class="valign" src="admin/html/images/avg.gif" border="0" alt="Partially" title="Component is active, but package is not active!" />
		<?php } else { ?>
		<img class="valign" src="admin/html/images/no.gif" border="0" alt="Inactive" title="Component is not active." />
		<?php } ?>
		</td>
	  	<td class="center"><?php echo noki($component['required']); ?></td>
	  </tr>
	  <?php } else { ?>
		<tr class="mbox">
			<td colspan="4">For this package is no component specified.</td>
		</tr>
	  <?php } ?>
	 </table>
	 <br class="minibr" />
	 <table class="border" border="0" cellspacing="0" cellpadding="4" align="center">
	  <tr>
	   <td class="obox" colspan="4">
	   	<span class="right"><a class="button" href="admin.php?action=packages&amp;job=plugins_add&amp;id=<?php echo $package['id']; ?>">Add Plugin</a></span>
	   	Plugins (<?php echo count($modules); ?>)
	   </td>
	  </tr>
	  <?php if (count($modules) > 0) { ?>
		  <tr class="ubox">
		   <td width="40%">Plugin</td>
		   <td width="40%">Hook</td>
		   <td width="10%">Active</td>
		   <td width="10%">Required</td>
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
				<img class="valign" src="admin/html/images/yes.gif" border="0" alt="Active" title="Plugin is active." />
				<?php } elseif ($plugin['active'] == 1 && $package['active'] == 0) { ?>
				<img class="valign" src="admin/html/images/avg.gif" border="0" alt="Partially" title="Plugin is active, but package is not active!" />
				<?php } else { ?>
				<img class="valign" src="admin/html/images/no.gif" border="0" alt="Inactive" title="Plugin is not active." />
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
			<td colspan="4">For this package is no plugin specified.</td>
		</tr>
	  	<?php
	  }
	echo '</table>';
	echo foot();
}
elseif ($job == 'package_add') {
	echo head();
	?>
	<form method="post" action="admin.php?action=packages&job=package_add2">
	<table class="border" border="0" cellspacing="0" cellpadding="4" align="center">
	 <tr>
	  <td class="obox" colspan="2">Create a new Package</td>
	 </tr>
	 <tr class="mbox">
	  <td>Title:<br /><span class="stext">Maximum number of characters: 200; Minimum number of characters: 4</span></td>
	  <td><input type="text" name="title" size="60" /></td>
	 </tr>
	 <tr class="mbox">
	  <td>Description:<br /><span class="stext">Optional</span></td>
	  <td><textarea cols="60" rows="4" name="summary" /></textarea></td>
	 </tr>
	 <tr class="mbox">
	  <td>Internal name:<br /><span class="stext">Specify a unique(!) name for your package conatining only alphanumerical characters or underscores. Minimum number of characters: 10</span></td>
	  <td><input type="text" name="internal" size="60" /></td>
	 </tr>
	 <tr class="mbox">
	  <td>Version:<br /><span class="stext">Optional</span></td>
	  <td><input type="text" name="version" size="60" value="1.0" /></td>
	 </tr>
	 <tr class="mbox">
	  <td>Minimum Viscacha Version:<br /><span class="stext">Optional</span></td>
	  <td><input type="text" name="min_version" size="60" /></td>
	 </tr>
	 <tr class="mbox">
	  <td>Maximum Viscacha Version:<br /><span class="stext">Optional</span></td>
	  <td><input type="text" name="max_version" size="60" /></td>
	 </tr>
	 <tr class="mbox">
	  <td>Copyright:<br /><span class="stext">Optional</span></td>
	  <td><input type="text" name="copyright" size="60" /></td>
	 </tr>
	 <tr class="mbox">
	  <td>License:<br /><span class="stext">Optional</span></td>
	  <td><input type="text" name="license" size="60" value="GNU General Public License" /></td>
	 </tr>
	 <tr class="mbox">
	  <td>URL/Homepage:<br /><span class="stext">Optional.</span></td>
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
	$title = $gpc->get('title', str);
	$summary = $gpc->get('summary', str);
	$internal = $gpc->get('internal', str);
	$version = $gpc->get('version', str);
	$copyright = $gpc->get('copyright', str);
	$license = $gpc->get('license', str);
	$max = $gpc->get('max_version', str);
	$min = $gpc->get('min_version', str);
	$url = $gpc->get('url', str);

	if (strlen($title) < 4) {
		error('admin.php?action=packages&job=package_add', 'Minimum number of characters for title: 4');
	}
	elseif (strlen($title) > 200) {
		error('admin.php?action=packages&job=package_add', 'Maximum number of characters for title: 200');
	}
	if (strlen($internal) < 10) {
		error('admin.php?action=packages&job=package_add', 'Internal name is too short.');
	}

	$db->query("INSERT INTO {$db->pre}packages (`title`,`version`,`internal`) VALUES ('{$title}','{$version}','{$internal}')");
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
		'config' => array()
	);
	$myini->write("modules/{$packageid}/package.ini", $ini);
	$filesystem->chmod("modules/{$packageid}/package.ini", 0666);

	ok('admin.php?action=packages&job=package_info&id='.$packageid, 'Package successfully added.');
}
elseif ($job == 'package_active') {
	$id = $gpc->get('id', int);
	$result = $db->query('SELECT id, active, core FROM '.$db->pre.'packages WHERE id = "'.$id.'"', __LINE__, __FILE__);
	$row = $db->fetch_assoc($result);
	if ($db->num_rows($result) == 0) {
		echo head();
		error('admin.php?action=packages&job=package', 'Specified ID is not correct.');
	}
	elseif ($row['core'] == '1') {
		echo head();
		error('admin.php?action=packages&job=package', 'This package is required. You can not change the status.');
	}
	else {
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
   <td class="obox" colspan="5">Component Manager</td>
  </tr>
  <tr class="ubox">
   <td width="30%">Component</td>
   <td width="30%">Package</td>
   <td width="10%">Active</td>
   <td width="30%">Action</td>
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
	<img class="valign" src="admin/html/images/yes.gif" border="0" alt="Active" title="Component is active." />
	<?php } elseif ($row['active'] == 1 && $row['pactive'] == 0) { ?>
	<img class="valign" src="admin/html/images/avg.gif" border="0" alt="Partially" title="Component is active, but package is not active!" />
	<?php } else { ?>
	<img class="valign" src="admin/html/images/no.gif" border="0" alt="Inactive" title="Component is not active." />
	<?php } ?>
	</td>
	<td>
	 <?php if ($row['required'] == 0) { ?>
	 <a class="button" href="admin.php?action=packages&amp;job=com_active&amp;id=<?php echo $row['id']; ?>"><?php echo iif($row['active'] == 1, 'Deactivate', 'Activate'); ?></a>
	 <a class="button" href="admin.php?action=packages&amp;job=com_delete&amp;id=<?php echo $row['id']; ?>">Delete</a>
	 <?php } else { echo "<em>Component is required</em>"; } ?>
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
		error('admin.php?action=packages&job=com', 'Specified component not found.');
	}
	elseif ($row['required'] == 1) {
		error('admin.php?action=packages&job=com', 'Specified component is required by a package and can not be deleted.');
	}
	else {
		?>
		<table class="border" border="0" cellspacing="0" cellpadding="4" align="center">
		<tr><td class="obox">Delete Component</td></tr>
		<tr><td class="mbox">
		<p align="center">Do you really want to delete this component?</p>
		<p align="center">
		<a href="admin.php?action=packages&amp;job=com_delete2&amp;id=<?php echo $id; ?>"><img border="0" alt="Yes" src="admin/html/images/yes.gif"> Yes</a>
		&nbsp&nbsp;&nbsp;&nbsp&nbsp;&nbsp;
		<a href="javascript: history.back(-1);"><img border="0" alt="No" src="admin/html/images/no.gif"> No</a>
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
		error('admin.php?action=packages&job=com', 'Specified component not found.');
	}
	elseif ($row['required'] == 1) {
		error('admin.php?action=packages&job=com', 'Specified component is required by a package and can not be deleted.');
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

		ok('admin.php?action=packages&job=com', 'Component successfully removed!');
	}
}
elseif ($job == 'com_active') {
	$id = $gpc->get('id', int);
	$result = $db->query('SELECT id, active, required FROM '.$db->pre.'component WHERE id = "'.$id.'"', __LINE__, __FILE__);
	$row = $db->fetch_assoc($result);
	if ($db->num_rows($result) == 0) {
		echo head();
		error('admin.php?action=packages&job=com', 'Specified ID is not correct.');
	}
	elseif ($row['required'] == 1) {
		echo head();
		error('admin.php?action=packages&job=com', 'This package is required. You can not change the status.');
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
	   <td class="obox">Plugin Manager (<?php echo iif($sort == 1, 'Packages', 'Hooks'); ?>)</td>
	  </tr>
	  <tr>
	   <td class="mbox">
		<span class="right">
			<a class="button" href="admin.php?action=packages&amp;job=plugins_add">Add Plugin</a>
			<a class="button" href="admin.php?action=packages&amp;job=plugins_hook_add">Add Hook</a>
		</span>
	   Group plugins by:
	   <a<?php echo iif($sort == 0, ' style="font-weight: bold;"'); ?> class="button" href="admin.php?action=packages&amp;job=plugins&amp;sort=0">Hooks</a>
	   <a<?php echo iif($sort == 1, ' style="font-weight: bold;"'); ?> class="button" href="admin.php?action=packages&amp;job=plugins&amp;sort=1">Packages</a>
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
		   <td width="30%">Plugin</td>
		   <td width="20%">Hook</td>
		   <td width="10%">Active</td>
		   <td width="40%">Action</td>
		  </tr>
		<?php
		while ($head = $db->fetch_assoc($result)) {
			if ($head['module'] != $package) {
				?>
				<tr class="obox">
				  <td colspan="3"><?php echo $head['title']; ?></td>
				  <td>
				  	<a class="button" href="admin.php?action=packages&amp;job=package_info&amp;id=<?php echo $head['module']; ?>">Go to Package</a>
				  	<a class="button" href="admin.php?action=packages&amp;job=plugins_add&id=<?php echo $head['module']; ?>">Add Plugin</a>
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
					<img class="valign" src="admin/html/images/yes.gif" border="0" alt="Active" title="Plugin is active." />
					<?php } elseif ($head['active'] == 1 && $head['mactive'] == 0) { ?>
					<img class="valign" src="admin/html/images/avg.gif" border="0" alt="Partially" title="Plugin is active, but package is not active!" />
					<?php } else { ?>
					<img class="valign" src="admin/html/images/no.gif" border="0" alt="Inactive" title="Plugin is not active." />
					<?php } ?>
					</td>
					<td>
					 <a class="button" href="admin.php?action=packages&amp;job=plugins_edit&amp;id=<?php echo $head['id']; ?>">Edit</a>
					 <?php if ($head['required'] == 0) { ?>
					 <a class="button" href="admin.php?action=packages&amp;job=plugins_active&amp;id=<?php echo $head['id']; ?>"><?php echo iif($head['active'] == 1, 'Deactivate', 'Activate'); ?></a>
					 <a class="button" href="admin.php?action=packages&amp;job=plugins_delete&amp;id=<?php echo $head['id']; ?>">Delete</a>
					 <?php } ?>
					</td>
				</tr>
				<?php
			}
			else {
				?>
				<tr class="mbox">
					<td colspan="4">For this package is no plugin specified. <a href="admin.php?action=packages&amp;job=plugins_add&id=<?php echo $head['module']; ?>">Add a new Plugin.</a></td>
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
		   <td width="30%">Plugin</td>
		   <td width="28%">Package</td>
		   <td width="11%">Active</td>
		   <td width="9%">Priority</td>
		   <td width="22%">Action</td>
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
					<img class="valign" src="admin/html/images/yes.gif" border="0" alt="Active" title="Plugin is active." />
					<?php } elseif ($head['active'] == 1 && $head['mactive'] == 0) { ?>
					<img class="valign" src="admin/html/images/avg.gif" border="0" alt="Partially" title="Plugin is active, but package is not active!" />
					<?php } else { ?>
					<img class="valign" src="admin/html/images/no.gif" border="0" alt="Inactive" title="Plugin is not active." />
					<?php } ?>
				</td>
				<td nowrap="nowrap" align="right">
					<?php echo $head['ordering']; ?>&nbsp;&nbsp;
		 			<a href="admin.php?action=packages&amp;job=plugins_move&amp;id=<?php echo $head['id']; ?>&amp;value=-1"><img src="admin/html/images/asc.gif" border="0" alt="Up"></a>&nbsp;
		 			<a href="admin.php?action=packages&amp;job=plugins_move&amp;id=<?php echo $head['id']; ?>&amp;value=1"><img src="admin/html/images/desc.gif" border="0" alt="Down"></a>
				</td>
				<td>
				 <a class="button" href="admin.php?action=packages&amp;job=plugins_edit&amp;id=<?php echo $head['id']; ?>">Edit</a>
				 <?php if ($head['required'] == 0) { ?>
				 <a class="button" href="admin.php?action=packages&amp;job=plugins_active&amp;id=<?php echo $head['id']; ?>"><?php echo iif($head['active'] == 1, 'Deactivate', 'Activate'); ?></a>
				 <a class="button" href="admin.php?action=packages&amp;job=plugins_delete&amp;id=<?php echo $head['id']; ?>">Delete</a>
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
	  <td class="obox" colspan="2">Add a Hook</td>
	 </tr>
	 <tr>
	  <td class="ubox" colspan="2">If you need a special hook implemented in Viscacha, please report it to us. We need your support on this!</td>
	 </tr>
	 <tr class="mbox">
	  <td width="40%">Name for Hook:<br /><span class="stext">You should use only alphanumerical chars in the text fields. In the second text field you can use also underscores (_).</span></td>
	  <td width="60%"><input type="text" name="group" size="15" />_<input type="text" name="name" size="35" /></td>
	 </tr>
	 <tr class="mbox">
	  <td>File:<br /><span class="stext">You have to add the generated code in this file.</span></td>
	  <td><input type="text" name="file" size="60" value="" /></td>
	 </tr>
	 <tr class="mbox">
	  <td>Where is the Hook used:</td>
	  <td>
	   <input type="radio" name="place" value="0" />Directly in PHP-Code<br />
	   <input type="radio" name="place" value="1" />Somewhere else (Example: Template)
	  </td>
	 </tr>
	 <tr>
	  <td class="ubox center" colspan="2"><input type="submit" value="Generate Code and Add Hook"></td>
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
		error('admin.php?action=packages&amp;job=plugins_hook_add', 'There is already a hook with this name.');
	}
	?>
	<table class="border" border="0" cellspacing="0" cellpadding="4" align="center">
	 <tr>
	  <td class="obox" colspan="2">Hook successfully added!</td>
	 </tr>
	 <tr class="mbox">
	  <td width="40%">Name of the new Hook:</td>
	  <td width="60%"><code><?php echo $hook; ?></code></td>
	 </tr>
	 <tr class="mbox">
	  <td>Generated Code:<br /><span class="stext">You have to add this code in this file where you want the hook to work.</span></td>
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
		error('admin.php?action=packages&job=plugins', 'Specified ID is not correct.');
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
		error('admin.php?action=packages&job=plugins', 'Specified ID is not correct.');
	}
	elseif ($row['required'] == 1) {
		echo head();
		error('admin.php?action=packages&job=plugins', 'This plugin is required. You can not change the status.');
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
		error('admin.php?action=packages&job=plugins', 'Specified plugin not found.');
	}
	elseif ($row['required'] == 1) {
		error('admin.php?action=packages&job=plugins', 'Specified plugin is required by a package and can not be deleted.');
	}
	else {
		?>
		<table class="border" border="0" cellspacing="0" cellpadding="4" align="center">
		<tr><td class="obox">Delete Package</td></tr>
		<tr><td class="mbox">
		<p align="center">Do you really want to delete this plugin?</p>
		<p align="center">
		<a href="admin.php?action=packages&job=plugins_delete2&id=<?php echo $id; ?>"><img border="0" alt="" src="admin/html/images/yes.gif"> Yes</a>
		&nbsp&nbsp;&nbsp;&nbsp&nbsp;&nbsp;
		<a href="javascript: history.back(-1);"><img border="0" alt="" src="admin/html/images/no.gif"> No</a>
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
		error('admin.php?action=packages&job=plugins', 'Specified plugin not found.');
	}
	elseif ($data['required'] == 1) {
		error('admin.php?action=packages&job=plugins', 'Specified plugin is required by another plugin and can not be deleted.');
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

		$filesystem->unlink('cache/modules/'.$plugins->_group($data['position']).'.php');

		ok('admin.php?action=packages&job=plugins', 'Plugin successfully deleted!');
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
	   <span class="right"><a class="button" href="javascript: self.close();">Close Window</a></span>
	   Source Code around the Hook <em><?php echo $hook; ?></em> in file <em><?php echo $file; ?></em>
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
		echo "There is no file for this hook.";
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
			error("admin.php?action=packages&job=plugins", "Plugin not found in plugin.ini.");
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
			error("admin.php?action=packages&job=plugins", "Plugin not found in database.");
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
	  <td class="obox" colspan="2">Edit Plugin</td>
	 </tr>
	 <tr class="mbox">
	  <td width="25%">Title for Plugin:<br /><span class="stext">Maximum number of characters: 200; Minimum number of characters: 4</span></td>
	  <td width="75%"><input type="text" name="title" size="40" value="<?php echo $package['title']; ?>" /></td>
	 </tr>
	 <tr class="mbox">
	  <td>Package:</td>
	  <td><strong><?php echo $package['title']; ?></strong></td>
	 </tr>
	 <tr class="mbox">
	  <td>Hook:</td>
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
	  </select> <a class="button" href="#" onclick="return openHookPosition();" target="_blank">Show Source Code around this Hook</a>
	  <?php } else { echo $package['position']; ?>
		<input type="hidden" name="hook" value="<?php echo $package['position']; ?>" />
	  <?php } ?>
	  </td>
	 </tr>
	 <tr class="mbox" valign="top">
	  <td>
	  Code:<br /><br />
	  <ul>
		<li><a href="admin.php?action=packages&amp;job=plugins_template&amp;id=<?php echo $package['module']; ?>" target="_blank">Add/Edit Templates</a></li>
		<li><a href="admin.php?action=packages&amp;job=plugins_language&amp;id=<?php echo $package['module']; ?>" target="_blank">Add/Edit Phrases</a></li>
	  </ul>
	  <?php if (count($cp) > 0) { ?>
	  <br /><br /><span class="stext"><strong>Caution</strong>: Changes to the code also affect the following hooks:</span>
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
	  <td width="25%">File for Code:<br /><span class="stext">This file is located in the folder <code><?php echo $config['fpath']; ?>/modules/<?php echo $package['module']; ?>/</code>.</span></td>
	  <td width="75%"><?php echo $codefile; ?></td>
	 </tr>
	 <?php if ($package['required'] == 0 && is_id($pluginid)) { ?>
	 <tr class="mbox">
	  <td>Active:</td>
	  <td><input type="checkbox" name="active" value="1"<?php echo iif($package['active'] == 1, ' checked="checked"'); ?> /></td>
	 </tr>
	 <?php } else { ?>
	 	<input type="hidden" name="active" value="1" />
	 <?php } ?>
	 <tr>
	  <td class="ubox" colspan="2" align="center"><input type="submit" value="Save" /></td>
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
			error("admin.php?action=packages&job=plugins", "Plugin not found");
		}
		$dir = "modules/{$data['module']}/";
		$ini = $myini->read($dir."plugin.ini");
	}

	if (strlen($title) < 4) {
		error('admin.php?action=packages&job=plugins_edit&id='.$id, 'Minimum number of characters for title: 4');
	}
	elseif (strlen($title) > 200) {
		error('admin.php?action=packages&job=plugins_edit&id='.$id, 'Maximum number of characters for title: 200');
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

	ok('admin.php?action=packages&job=plugins', 'Plugin successfully edited!');
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
	  <td class="obox" colspan="2">Add Plugin - Step 1 of 3</td>
	 </tr>
	 <tr class="mbox">
	  <td width="25%">Title for Plugin:<br /><span class="stext">Maximum number of characters: 200; Minimum number of characters: 4</span></td>
	  <td width="75%"><input type="text" name="title" size="40" /></td>
	 </tr>
	 <tr class="mbox">
	  <td>Package:</td>
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
	  <td>Hook:</td>
	  <td><select name="hook" id="hook">
	  <?php foreach ($hooks as $group => $positions) { ?>
	  <optgroup label="<?php echo $group; ?>">
		  <?php foreach ($positions as $hook) { ?>
		  <option value="<?php echo $hook; ?>"><?php echo $hook; ?></option>
		  <?php } ?>
	  </optgroup>
	  <?php } ?>
	  </select> <a class="button" href="#" onclick="return openHookPosition();" target="_blank">Show Source Code around this Hook</a></td>
	 </tr>
	 <tr>
	  <td class="ubox" colspan="2" align="center"><input type="submit" value="Save" /></td>
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
	$packageid = $gpc->get('package', int);
	$title = $gpc->get('title', str);
	$result = $db->query("SELECT id, title FROM {$db->pre}packages WHERE id = '{$packageid}' LIMIT 1", __LINE__, __FILE__);
	if ($db->num_rows() != 1) {
		echo head();
		error('admin.php?action=packages&job=plugins_add', 'Specified package ('.$packageid.') does not exist.');
	}
	$package = $db->fetch_assoc($result);
	if (strlen($title) < 4) {
		error('admin.php?action=packages&job=plugins_add&id='.$package['id'], 'Minimum number of characters for title: 4');
	}
	elseif (strlen($title) > 200) {
		error('admin.php?action=packages&job=plugins_add&id='.$package['id'], 'Maximum number of characters for title: 200');
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
	  <td class="obox" colspan="2">Add Plugin - Step 2 of 3</td>
	 </tr>
	 <tr class="mbox">
	  <td width="25%">Title for Plugin:<br /><span class="stext">Maximum number of characters: 200; Minimum number of characters: 4</span></td>
	  <td width="75%"><input type="text" name="title" size="40" value="<?php echo htmlspecialchars($title); ?>" /></td>
	 </tr>
	 <tr class="mbox">
	  <td>Package:</td>
	  <td><strong><?php echo $package['title']; ?></strong> (<?php echo $package['id']; ?>)</td>
	 </tr>
	 <tr class="mbox">
	  <td>Hook:</td>
	  <td><strong><?php echo $hook; ?></strong><input type="hidden" name="hook" value="<?php echo $hook; ?>"> <a class="button" href="#" onclick="return openHookPosition('<?php echo $hook; ?>');" target="_blank">Show Source Code around this Hook</a></td>
	 </tr>
	 <tr class="mbox" valign="top">
	  <td>
	  Code:<br /><br />
	  <span class="stext">At this place you can insert PHP-Code which will be executed in the indicated hook. You don't need to use &lt;?php bzw. ?&gt;-Tags at the beginning and the end of your code. You also can use templates and phrases for this plugin (more information down of this page). More information can be found in the documentation.</span>
	  <br /><br />
	  <ul>
		<li><a href="admin.php?action=packages&amp;job=plugins_template&amp;id=<?php echo $package['id']; ?>" target="_blank">Add/Edit Template</a></li>
		<li><a href="admin.php?action=packages&amp;job=plugins_language&amp;id=<?php echo $package['id']; ?>" target="_blank">Add/Edit Phrase</a></li>
	  </ul>
	  </td>
	  <td><textarea name="code" rows="10" cols="80" class="texteditor"></textarea></td>
	 </tr>
	 <tr class="mbox">
	  <td width="25%">File for Code:<br /><span class="stext">In this file the code will be saved. This file is located in the folder <code><?php echo $config['fpath']; ?>/modules/<?php echo $package['id']; ?>/</code>. If the file exists, the code above will be ignored.</span></td>
	  <td width="75%"><input type="text" name="file" size="40" value="<?php echo $codefile; ?>" /></td>
	 </tr>
	 <?php if (!$isInvisibleHook) { ?>
	 <tr class="mbox">
	  <td>Priority:</td>
	  <td><select name="priority">
	  <?php while ($row = $db->fetch_assoc($hookPriority)) { $last = $row['name']; ?>
	  <option value="<?php echo $row['id']; ?>">Before <?php echo $row['name']; ?></option>
	  <?php } ?>
	  <option value="max">After <?php echo $last; ?></option>
	  </select></td>
	 </tr>
	 <tr class="mbox">
	  <td>Required by the package:</td>
	  <td><input type="checkbox" name="required" value="1" /></td>
	 </tr>
	 <tr class="mbox">
	  <td>Active:</td>
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
		ok('admin.php?action=cms&job=nav_addplugin&id='.$package, 'Step 3 of 3: Plugin successfully added! You have added a plugin to the hook "navigation". Before you can use it in your navigation, you have to add it to your Navigation Manager.');
	}
	else {
		ok('admin.php?action=packages&job=plugins_add&id='.$package, 'Step 3 of 3: Plugin successfully added!');
	}
}
elseif ($job == 'plugins_template') {
	$id = $gpc->get('id', int);

	$result = $db->query("SELECT id, title FROM {$db->pre}packages WHERE id = '{$id}' LIMIT 1", __LINE__, __FILE__);
	if ($db->num_rows() != 1) {
		echo head();
		error('javascript: self.close();', 'Specified package ('.$id.') does not exist.');
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

	// ToDo: Prüfen ob .html variabel sein sollte (class.template.php => Endung der Templates ist variabel, nur standardmäßig html)
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
	  <span style="float: right;"><a class="button" href="javascript: self.close();">Close Window</a></span>
	  Manage Templates for Package: <?php echo $data['title']; ?></td>
	 </tr>
	 <?php if (isset($ini['template']) && count($ini['template']) > 0) { ?>
	 <tr class="mbox">
	  <td width="10%">Edit</td>
	  <td width="10%">Delete</td>
	  <td width="80%">File</td>
	 </tr>
	 <?php foreach ($ini['template'] as $key => $file) { ?>
	 <tr class="mbox">
	  <td><input type="radio" name="edit" value="<?php echo $key; ?>" /></td>
	  <td><input type="checkbox" name="delete[]" value="<?php echo $key; ?>" /></td>
	  <td><?php echo $file; ?></td>
	 </tr>
	 <?php } ?>
	 <tr>
	  <td class="ubox" colspan="3" align="center"><input type="submit" value="Submit" /></td>
	 </tr>
	 <?php } else { ?>
	 <tr class="mbox">
	  <td colspan="3">No Template available for this Package.</td>
	 </tr>
	 <?php } ?>
	</table>
	</form>
	<br class="minibr" />
	<form method="post" action="admin.php?action=packages&job=plugins_template_add&id=<?php echo $data['id']; ?>">
	<table class="border" border="0" cellspacing="0" cellpadding="4" align="center">
	 <tr>
	  <td class="obox" colspan="2">Add Template</td>
	 </tr>
	 <tr class="mbox" valign="top">
	  <td>
	  Code:<br /><br />
	  <ul>
		<li><a href="admin.php?action=packages&amp;job=plugins_language&amp;id=<?php echo $data['id']; ?>" target="_blank">Add/Edit Phrase</a></li>
	  </ul>
	  </td>
	  <td><textarea name="code" rows="8" cols="80" class="texteditor"></textarea></td>
	 </tr>
	 <tr class="mbox">
	  <td width="25%">File for Code:<br /><span class="stext">In this file the code will be saved. This file is located in the folder <code><?php echo $config['fpath']; ?>/<?php echo $tpldir; ?></code>.</span></td>
	  <td width="75%"><input type="text" name="file" size="40" value="<?php echo $codefile; ?>" /></td>
	 </tr>
	 <tr>
	  <td class="ubox" colspan="2" align="center"><input type="submit" value="Save" /></td>
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
	if ($db->num_rows() != 1) {
		echo head();
		error('javascript: self.close();', 'Specified package ('.$id.') does not exist.');
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
	if ($db->num_rows() != 1) {
		echo head();
		error('javascript: self.close();', 'Specified package ('.$id.') does not exist.');
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
			  <tr><td class="obox">Confirmation:</td></tr>
			  <tr><td class="mbox" align="center">Template(s) successfully deleted</td></tr>
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
		  <td class="obox" colspan="2">Add/Edit Template</td>
		 </tr>
		 <tr class="mbox" valign="top">
		  <td rowspan="<?php echo count($tpldirs); ?>">
			Code:<br /><br />
			<ul><li><a href="admin.php?action=packages&amp;job=plugins_language&amp;id=<?php echo $data['id']; ?>" target="_blank">Add/Edit Phrase</a></li></ul>
		  </td>
		  <?php
		  $first = true;
		  foreach ($tpldirs as $tplid => $designId) {
		  	if ( in_array($config['templatedir'], $designId['ids']) ) {
		  		$affected = 'All designs that have not defined an own template';
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
		  	echo 'Template Group: <b>'.$tplid.'</b><br />';
		  	echo 'Design(s) affected by changes: '.$affected.'<br />';
		  	echo '<textarea name="code['.$tplid.']" rows="8" cols="80" class="texteditor">'.$content.'</textarea>';
		  	echo '</td></tr>';
		  }
		  ?>
		 <tr class="mbox">
		  <td width="25%">File for Code:</td>
		  <td width="75%"><?php echo $codefile; ?></td>
		 </tr>
		 <tr>
		  <td class="ubox" colspan="2" align="center"><input type="submit" value="Save" /></td>
		 </tr>
		</table>
		</form>
		<?php
		$output = 1;
	}

	if ($output == -1) {
		error('admin.php?action=packages&job=plugins_template&id='.$data['id'], 'Please choose at least one template...');
	}
	elseif ($output == 0) {
		ok('admin.php?action=packages&job=plugins_template&id='.$data['id'], 'Template(s) successfully deleted');
	}
}
elseif ($job == 'plugins_template_edit2') {
	$id = $gpc->get('id', int);
	$editId = $gpc->get('edit', int, -1);
	$code = $gpc->get('code', arr_none);

	echo head();
	$result = $db->query("SELECT id FROM {$db->pre}packages WHERE id = '{$id}' LIMIT 1", __LINE__, __FILE__);
	if ($db->num_rows() != 1) {
		error('javascript: self.close();', 'Specified package ('.$id.') does not exist.');
	}
	$data = $db->fetch_assoc($result);
	$ini = $myini->read("modules/{$data['id']}/plugin.ini");
	if (!isset($ini['template'][$editId])) {
		error('javascript: self.close();', 'Specified template ('.$id.') does not exist in INI-File.');
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
	if ($db->num_rows() != 1) {
		echo head();
		error('javascript: self.close();', 'Specified package ('.$id.') does not exist.');
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
   <span style="float: right;"><a class="button" href="admin.php?action=packages&job=plugins_language_add&id=<?php echo $id; ?>">Add new Phrases</a></span>
   Phrase Manager</td>
  </tr>
  <?php if (count($ini['language']) == 0) { ?>
  <tr>
   <td class="mbox" colspan="<?php echo count($cache)+1; ?>">There are no phrases for this package. <a class="button" href="admin.php?action=packages&job=plugins_language_add&id=<?php echo $id; ?>">Add a new Phrase</a></td>
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
   <td class="mmbox"><input type="checkbox" name="delete[]" value="<?php echo $phrase; ?>">&nbsp;<a class="button" href="admin.php?action=packages&job=plugins_language_edit&phrase=<?php echo $phrase; ?>&id=<?php echo $id; ?>">Edit</a>&nbsp;<?php echo $phrase; ?></td>
   <?php
   foreach ($cache as $row) {
   	$status = in_array($phrase, $diff[$row['id']]);
   ?>
   <td class="mbox" align="center"><?php echo noki($status); ?></td>
   <?php } ?>
  </tr>
  <?php } ?>
  <tr>
   <td class="ubox" align="center" colspan="<?php echo count($cache)+1; ?>"><input type="submit" value="Delete selected phrases"></td>
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
	if ($db->num_rows() != 1) {
		echo head();
		error('javascript: self.close();', 'Specified package ('.$id.') does not exist.');
	}
	$data = $db->fetch_assoc($result);

	$result = $db->query('SELECT * FROM '.$db->pre.'language ORDER BY language',__LINE__,__FILE__);
	?>
<form name="form" method="post" action="admin.php?action=packages&job=plugins_language_save2&id=<?php echo $id; ?>">
 <table class="border" border="0" cellspacing="0" cellpadding="4" align="center">
  <tr>
   <td class="obox" colspan="2">Phrase Manager &raquo; Add new Phrase to Package</td>
  </tr>
  <tr>
   <td class="mbox" width="50%">Varname:<br />
   <span class="stext">Varname is a value which can only contain letters, numbers and underscores.</span></td>
   <td class="mbox" width="50%"><input type="text" name="varname" size="50" value="" /></td>
  </tr>
  <tr>
   <td class="mbox" width="50%">Text:<br />
   <span class="stext">This is the default language used also in the exported packages. It is recommended to write English here.</span></td>
   <td class="mbox" width="50%"><input type="text" name="text" size="50" /></td>
  </tr>
  <tr>
   <td class="obox" colspan="2">Translations</td>
  </tr>
  <tr>
   <td class="ubox" colspan="2"><ul>
	<li>When inserting a custom phrase, you may also specify the translations into whatever languages you have installed.</li>
	<li>If you do leave a translation box blank, it will inherit the text from the 'Text' box.</li>
   </ul></td>
  </tr>
  <?php while($row = $db->fetch_assoc($result)) { ?>
  <tr>
   <td class="mbox" width="50%"><em><?php echo $row['language']; ?></em> Translation:<br /><span class="stext">Optional. HTML is allowed but not recommended.</span></td>
   <td class="mbox" width="50%"><input type="text" name="langt[<?php echo $row['id']; ?>]" size="50" /></td>
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
elseif ($job == 'plugins_language_save2') {
	echo head();

	$id = $gpc->get('id', int);
	$varname = $gpc->get('varname', none);
	$text = $gpc->get('text', none);
	$lang = $gpc->get('langt', none);

	if (empty($text)) {
		error('javascript: history.back(-1);', 'No default text specified.');
	}

	$result = $db->query("SELECT id, title FROM {$db->pre}packages WHERE id = '{$id}' LIMIT 1", __LINE__, __FILE__);
	if ($db->num_rows() != 1) {
		error('javascript: self.close();', 'Specified package ('.$id.') does not exist.');
	}
	$data = $db->fetch_assoc($result);

	$c = new manageconfig();
	foreach ($lang as $id => $t) {
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
	if ($db->num_rows() != 1) {
		echo head();
		error('javascript: self.close();', 'Specified package ('.$id.') does not exist.');
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
	ok('admin.php?action=packages&job=plugins_language&id='.$data['id'], 'Selected phrases were successfully deleted.');
}
elseif ($job == 'plugins_language_edit') {
	echo head();

	$phrase = $gpc->get('phrase', none);
	$id = $gpc->get('id', int);
	$result = $db->query("SELECT id, title FROM {$db->pre}packages WHERE id = '{$id}' LIMIT 1", __LINE__, __FILE__);
	if ($db->num_rows() != 1) {
		echo head();
		error('javascript: self.close();', 'Specified package ('.$id.') does not exist.');
	}
	$data = $db->fetch_assoc($result);

	$dir = "modules/{$data['id']}/";
	$ini = $myini->read($dir."plugin.ini");
	if (!isset($ini['language'][$phrase])) {
		error('admin.php?action=packages&job=plugins_edit&id=7', 'Phrase not found!');
	}

	$result = $db->query('SELECT * FROM '.$db->pre.'language ORDER BY language',__LINE__,__FILE__);
	?>
<form name="form" method="post" action="admin.php?action=packages&job=plugins_language_save2&id=<?php echo $id; ?>">
 <table class="border" border="0" cellspacing="0" cellpediting="4" align="center">
  <tr>
   <td class="obox" colspan="2">Phrase Manager &raquo; Edit Phrase</td>
  </tr>
  <tr>
   <td class="mbox" width="50%">Varname:<br />
   <span class="stext">Varname is a value which can only contain letters, numbers and underscores.</span></td>
   <td class="mbox" width="50%"><input type="hidden" name="varname" size="50" value="<?php echo $phrase; ?>" /><code><?php echo $phrase; ?></code></td>
  </tr>
  <tr>
   <td class="mbox" width="50%">Text:<br />
   <span class="stext">This is the default language used also in the exported packages. It is recommended to write English here.</span></td>
   <td class="mbox" width="50%"><input type="text" name="text" size="50" value="<?php echo htmlspecialchars(nl2whitespace($ini['language'][$phrase])); ?>" /></td>
  </tr>
  <tr>
   <td class="obox" colspan="2">Translations</td>
  </tr>
  <tr>
   <td class="ubox" colspan="2"><ul>
	<li>When editing a custom phrase, you may also specify the translations into whatever languages you have installed.</li>
	<li>If you do leave a translation box blank, it will inherit the text from the 'Text' box.</li>
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
   <td class="mbox" width="50%"><em><?php echo $row['language']; ?></em> Translation:<br /><span class="stext">Optional. HTML is allowed but not recommended.</span></td>
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
	$result = $db->query("SELECT internal, version FROM {$db->pre}packages WHERE id = '{$id}'");
	$data = $db->fetch_assoc($result);
	if (empty($data['version'])) {
		error('admin.php?action=packages&job=package', 'No information about the current version found.');
	}
	$pb = $scache->load('package_browser');
	$row = $pb->getOne(IMPTYPE_PACKAGE, $data['internal']);
	if ($row !== false && !empty($row['version'])) {
		if (version_compare($row['version'], $data['version'], '>')) {
			ok('admin.php?action=packages&job=browser_detail&id='.$row['internal'].'&package='.IMPTYPE_PACKAGE, 'There is a new version ('.$row['version'].') on the server.', 3000);
		}
		else {
			ok('admin.php?action=packages&job=package_info&id='.$id, 'This package seems to be up to date.', 3000);
		}
		break;
	}
	ok('admin.php?action=packages&job=package_info&id='.$id, 'The package was not found on one of the known servers.', 3000);
}
elseif ($job == 'browser') {
	$pb = $scache->load('package_browser');
	$types = $pb->types();
	$type = $gpc->get('type', int, IMPTYPE_PACKAGE);
	$cats = $pb->categories($type);
	// Calculate random entry
	unset($cat);
	while (empty($cat['entries'])) {
		$keys = array_keys($cats);
		shuffle($keys);
		$rid = current($keys);
		$cat = $pb->categories($type, $rid);
	}
	$e = $pb->get($type, $rid);
	shuffle($e);
	$random = current($e);
	echo head();
	?>
 <table class="border" border="0" cellspacing="0" cellpediting="4" align="center">
  <tr>
   <td class="obox" colspan="2">Browse <?php echo $types[$type]['name']; ?></td>
  </tr>
  <tr>
   <td class="ubox" width="50%"><strong>Categories:</strong></td>
   <td class="ubox" width="50%"><strong>Useful Links:</strong></td>
  <tr>
   <td class="mbox" valign="top" rowspan="3">
	<ul>
		<?php foreach ($cats as $id => $row) { ?>
		<li><a href="admin.php?action=packages&amp;job=browser_list&amp;type=<?php echo $type; ?>&amp;id=<?php echo $id; ?>"><?php echo $row['name']; ?></a> (<?php echo $row['entries']; ?>)</li>
		<?php } ?>
	</ul>
   </td>
   <td class="mbox" valign="top">
	<ul>
		<li><a href="admin.php?action=packages&amp;job=browser_list&amp;type=<?php echo $type; ?>&amp;id=last">Recently updated <?php echo $types[$type]['name']; ?></a></li>
		<li><a href="admin.php?action=settings&amp;job=admin">Change servers that offer <?php echo $types[$type]['name']; ?></a></li>
	</ul>
   </td>
  </tr>
  <tr>
   <td class="ubox" valign="top"><?php echo ucfirst($types[$type]['name2']); ?> of the moment:</td>
  </tr>
  <tr>
   <td class="mbox" valign="top">
	<a href="admin.php?action=packages&amp;job=browser_detail&amp;id=<?php echo $random['internal']; ?>&amp;type=<?php echo $type; ?>"><strong><?php echo $random['title']; ?></strong> <?php echo $random['version']; ?></a><br />
	<?php echo $random['summary']; ?>
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
		$title = 'Recently updated '.$types[$type]['name'];
	}

	if ($type == IMPTYPE_PACKAGE) {
		$result = $db->query("SELECT id, internal, version FROM {$db->pre}packages");
		$installed = array();
		while($row = $db->fetch_assoc($result)) {
			$installed[$row['internal']] = $row;
		}
	}
	elseif ($type == IMPTYPE_BBCODE) {

	}
	else {
		$installed = null;
	}

	echo head();
	?>
 <table class="border" border="0" cellspacing="0" cellpediting="4" align="center">
  <tr>
   <td class="obox" colspan="4">Browse <?php echo $types[$type]['name']; ?> &raquo; <?php echo $title; ?></td>
  </tr>
  <tr>
   <td class="ubox" width="60%">Name<br />Description</td>
   <?php if (is_array($installed)) { ?>
   <td class="ubox" width="10%">Installed</td>
   <?php } ?>
   <td class="ubox" width="10%">Compatible</td>
   <td class="ubox" width="30%">Last Update<br />License</td>
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
    	<a class="button" href="admin.php?action=packages&amp;job=browser_import&amp;id=<?php echo $key; ?>&amp;type=<?php echo $type; ?>">Import</a>
    	<?php } if ($install) { ?>
    	<a class="button" href="admin.php?action=packages&amp;job=package_info&amp;id=<?php echo $installed[$row['internal']]['id']; ?>">Go to installed Package</a>
    	<?php } ?>
    </span>
   	<a href="admin.php?action=packages&amp;job=browser_detail&amp;id=<?php echo $key; ?>&amp;type=<?php echo $type; ?>"><strong><?php echo $row['title']; ?></strong> <?php echo $row['version']; ?></a><br />
   	<span class="stext"><?php echo $row['summary']; ?></span>
   </td>
   <?php if (is_array($installed)) { ?>
   <td align="center"><?php echo iif($install, 'Yes'.iif($update, '<br /><span class="stext" style="font-color: darkred;">Update available!</span>'), 'No'); ?></td>
   <?php } ?>
   <td align="center"><?php echo noki($compatible); ?></td>
   <td valign="top">
   	Last update: <?php echo gmdate('d.m.Y', times($row['last_updated'])); ?><br />
   	License: <?php echo empty($row['license']) ? 'Unknown' : $row['license']; ?>
   	<?php if($show_cat == true) { $cat = $pb->categories($type, $row['category']); ?><br />Category: <?php echo $cat['name']; } ?>
   	</td>
  </tr>
  <?php } ?>
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
elseif ($job == 'browser_detail') {
	$type = $gpc->get('type', int, IMPTYPE_PACKAGE);
	$id = $gpc->get('id', str);
	$pb = $scache->load('package_browser');
	$row = $pb->getOne($type, $id);
	$types = $pb->types();
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
	?>
	 <table class="border" border="0" cellspacing="0" cellpadding="4" align="center">
	  <tr>
	   <td class="obox" colspan="2">
	  	<?php if ($installed === false) { ?>
	  	<span class="right"><a class="button" href="admin.php?action=packages&amp;job=browser_import&amp;id=<?php echo $id; ?>&amp;type=<?php echo $type; ?>">Import this <?php echo $types[$type]['name2']; ?></a></span>
	  	<?php } ?>
	    Browse <?php echo $types[$type]['name']; ?> &raquo; <?php echo $cat['name']; ?> &raquo; <?php echo $row['title']; ?>
	   </td>
	  </tr>
	  <tr>
	   <td class="mbox" width="30%">Name:</td>
	   <td class="mbox" width="70%"><a href="<?php echo $row['url']; ?>" target="_blank"><?php echo $row['title']; ?></a></td>
	  </tr>
	  <tr>
	   <td class="mbox" width="30%">Description:</td>
	   <td class="mbox" width="70%"><?php echo nl2br($row['summary']); ?></td>
	  </tr>
	  <?php if ($type == IMPTYPE_PACKAGE) { ?>
	  <tr>
	   <td class="mbox" width="30%">Status:</td>
	   <td class="mbox" width="70%">
	   <?php if ($installed === false) { ?>
	   You have not installed this package!&nbsp;&nbsp;&nbsp;&nbsp;<a class="button" href="admin.php?action=packages&amp;job=browser_import&amp;id=<?php echo $id; ?>&amp;type=<?php echo $type; ?>">Import this <?php echo $types[$type]['name2']; ?>!</a>
	   <?php }
	   else {
	   		?>
	   		<span class="right"><a class="button" href="admin.php?action=packages&amp;job=package_info&amp;id=<?php echo $installed; ?>">Go to installed Package</a></span>
	   		<?php
	   		$vc = version_compare($pack['version'], $row['version']);
	   		if ($vc == 1) { ?>
	   		<strong style="color: gold;">You have installed a newer version of this <?php echo $types[$type]['name2']; ?>.</strong>
		    <?php } elseif($vc == -1) { ?>
		    <strong style="color: darkred;">You have installed an old version (<?php echo $pack['version']; ?>)!</strong>
		    <?php } else { ?>
		    <strong style="color: darkgreen;">You have installed this <?php echo $types[$type]['name2']; ?>.</strong>
	    <?php } } ?>
	   </td>
	  </tr>
	  <?php } if (!empty($row['last_updated'])) { ?>
	  <tr>
	   <td class="mbox" width="30%">Last update:</td>
	   <td class="mbox" width="70%"><?php echo gmdate('d.m.Y H:i', times($row['last_updated'])); ?></td>
	  </tr>
	  <?php } if (!empty($row['copyright'])) { ?>
	  <tr>
	   <td class="mbox" width="30%">Copyright:</td>
	   <td class="mbox" width="70%"><?php echo str_ireplace('(C)', '&copy;', $row['copyright']); ?></td>
	  </tr>
	  <?php } if (!empty($row['license'])) { ?>
	  <tr>
	   <td class="mbox" width="30%">License:</td>
	   <td class="mbox" width="70%"><?php echo $row['license']; ?></td>
	  </tr>
	  <?php } if (!empty($row['version'])) { ?>
	  <tr>
	   <td class="mbox" width="30%">Version:</td>
	   <td class="mbox" width="70%"><?php echo $row['version']; ?></td>
	  </tr>
	  <?php } if (!empty($row['min_version']) || !empty($row['max_version'])) { ?>
	  <tr>
	   <td class="mbox" width="30%">Compatibility:</td>
	   <td class="mbox" width="70%">
	   	<?php if (!empty($row['min_version'])) { ?>
	   	<div>Minimum: <?php echo $row['min_version']; ?></div>
	   	<?php } ?>
	   	<?php if (!empty($row['max_version'])) { ?>
	   	<div>Maximum: <?php echo $row['max_version']; ?></div>
	   	<?php } ?>
	   </td>
	  </tr>
	  <?php } if (isset($row['license'])) { ?>
	  <tr>
	   <td class="mbox" width="30%">Multiple installations allowed:</td>
	   <td class="mbox" width="70%"><?php echo noki($row['multiple']); ?></td>
	  </tr>
	  <?php } ?>
	  <tr>
	   <td class="mbox" width="30%">Server:</td>
	   <td class="mbox" width="70%"><?php echo $row['server']; ?></td>
	  </tr>
	  <tr>
	   <td class="mbox" width="30%">File:</td>
	   <td class="mbox" width="70%"><a href="<?php echo $row['file']; ?>"><?php echo $row['file']; ?></a></td>
	  </tr>
	  <tr>
	   <td class="mbox" width="30%">Internal name:</td>
	   <td class="mbox" width="70%"><tt><?php echo $row['internal']; ?></tt></td>
	  </tr>
	  <?php if (!empty($row['preview'])) { ?>
	  <tr>
	   <td class="ubox" colspan="2">Preview:</td>
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