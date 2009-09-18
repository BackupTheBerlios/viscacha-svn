?><?php
if (defined('VISCACHA_CORE') == false) { die('Error: Hacking Attempt'); }

require('admin/data/config.inc.php');

define('IMPTYPE_PACKAGE', 1);
define('IMPTYPE_DESIGN', 2);
define('IMPTYPE_SMILEYPACK', 3);
define('IMPTYPE_LANGUAGE', 4);
define('IMPTYPE_BBCODE', 5);

$lang->group("admin/packages");

$job = $gpc->get('job', str);

$breadcrumb->Add($lang->phrase('ps_downloads'), 'components.php?cid='.PACKAGE_ID);

$pb = $scache->load('package_browser');

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

if ($job == 'browser_list') {
	$id = $gpc->get('id', none);
	$type = $gpc->get('type', int, IMPTYPE_PACKAGE);
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

  	$foo = $types[$type];
	$breadcrumb->Add($foo['name'], 'components.php?cid='.PACKAGE_ID.'&job=browser&type='.$type);
	$breadcrumb->Add($title);

	echo $tpl->parse("header");
	?>
 <table class="tables">
  <tr>
   <td class="h3" colspan="4"><?php echo $lang->phrase('admin_packages_browser_browse_foo'); ?> &raquo; <?php echo $title; ?></td>
  </tr>
  <?php if (count($data) == 0) { ?>
  <tr>
   <td class="tbody" colspan="4"><?php echo $lang->phrase('admin_packages_no_x_found'); ?></td>
  </tr>
  <?php } else { ?>
  <tr>
   <td class="tfoot" width="50%"><?php echo $lang->phrase('admin_packages_info_name'); ?><br /><?php echo $lang->phrase('admin_packages_head_description'); ?></td>
   <td class="tfoot" width="18%"><?php echo $lang->phrase('ps_compatibility_v'); ?></td>
   <td class="tfoot" width="32%"><?php echo $lang->phrase('admin_packages_browser_last_update2'); ?><br /><?php echo $lang->phrase('admin_packages_browser_license2'); ?></td>
  </tr>
  <?php foreach ($data as $key => $row) { ?>
  <tr class="tbody">
   <td valign="top">
	<span class="right"><a class="button" href="<?php echo $row['file']; ?>"><?php echo $lang->phrase('ps_download'); ?></a></span>
   	<a href="components.php?cid=<?php echo PACKAGE_ID; ?>&amp;job=browser_detail&amp;id=<?php echo $key; ?>&amp;type=<?php echo $type; ?>"><strong><?php echo $row['title']; ?></strong> <?php echo $row['version']; ?></a><br />
   	<span class="stext"><?php echo $row['summary']; ?></span>
   </td>
   <td valign="top" class="stext">
	<?php
	if (!empty($row['min_version'])) {
		echo $lang->phrase('ps_min_version').$row['min_version'];
	}
	if (!empty($row['max_version'])) {
		if (!empty($row['min_version'])) {
			echo "<br />";
		}
		echo $lang->phrase('ps_max_version').$row['max_version'];
	}
	if (empty($row['min_version']) && empty($row['max_version'])) {
		echo $lang->phrase('ps_no_version_declared');
	}
	?>
   </td>
   <td valign="top" class="stext">
	<?php echo $lang->phrase('admin_packages_browser_last_update'); ?> <?php echo gmdate('d.m.Y', times($row['last_updated'])); ?><br />
	<?php echo $lang->phrase('admin_packages_info_license'); ?> <?php echo empty($row['license']) ? $lang->phrase('admin_packages_unknown') : $row['license']; ?>
   	<?php if($show_cat == true) { $cat = $pb->categories($type, $row['category']); ?>
   		<br /><?php echo $lang->phrase('admin_packages_browser_category'); ?> <a href="components.php?cid=<?php echo PACKAGE_ID; ?>&job=browser_list&type=<?php echo $type; ?>&id=<?php echo $row['category']; ?>"><?php echo $cat['name']; ?></a>
    <?php } ?>
   	</td>
  </tr>
  <?php } } ?>
 </table>
 <?php
}
elseif ($job == 'browser_detail') {
	$type = $gpc->get('type', int, IMPTYPE_PACKAGE);
	$id = $gpc->get('id', str);

	$types = $pb->types();
	$row = $pb->getOne($type, $id);
	$cat = $pb->categories($type, $row['category']);
	$foo = $types[$type];

	$breadcrumb->Add($foo['name'], 'components.php?cid='.PACKAGE_ID.'&job=browser&type='.$type);
	$breadcrumb->Add($cat['name'], 'components.php?cid='.PACKAGE_ID.'&job=browser_list&type='.$type.'&id='.$row['category']);
	$breadcrumb->Add($row['title']);

	$htmlonload .= 'ResizeImg(FetchElement(\'preview\'),640);';
	echo $tpl->parse("header");

	if ($row == null) {
		error('components.php?cid='.PACKAGE_ID.'&job=browser', $lang->phrase('admin_packages_no_x_found'));
	}
	?>
	 <table class="tables">
	  <tr>
	   <td class="h3" colspan="2"><?php echo $row['title']; ?></td>
	  </tr>
	  <tr>
	   <td class="tbody" width="30%"><?php echo $lang->phrase('admin_packages_browser_det_name'); ?></td>
	   <td class="tbody" width="70%">
	   	<strong><a href="<?php echo $row['url']; ?>" target="_blank"><?php echo $row['title']; ?></a></strong>
	   	<?php if (!empty($row['version'])) { echo $row['version']; } ?>
	   </td>
	  </tr>
	  <tr>
	   <td class="tbody" width="30%"><?php echo $lang->phrase('admin_packages_info_description'); ?></td>
	   <td class="tbody" width="70%"><?php echo nl2br($row['summary']); ?></td>
	  </tr>
	  <tr>
	   <td class="tbody" width="30%"><?php echo $lang->phrase('ps_download'); ?></td>
	   <td class="tbody" width="70%"><a href="<?php echo $row['file']; ?>"><?php echo $row['file']; ?></a></td>
	  </tr>
	  <?php if ($type == IMPTYPE_PACKAGE) { ?>
	  <?php if (!empty($row['last_updated'])) { ?>
	  <tr>
	   <td class="tbody" width="30%"><?php echo $lang->phrase('admin_packages_browser_last_update'); ?></td>
	   <td class="tbody" width="70%"><?php echo gmdate('d.m.Y H:i', times($row['last_updated'])); ?></td>
	  </tr>
	  <?php } if (!empty($row['copyright'])) { ?>
	  <tr>
	   <td class="tbody" width="30%"><?php echo $lang->phrase('admin_packages_info_copyright'); ?></td>
	   <td class="tbody" width="70%"><a href="<?php echo $row['url']; ?>" target="_blank"><?php echo str_ireplace('(C)', '&copy;', $row['copyright']); ?></a></td>
	  </tr>
	  <?php } if (!empty($row['license'])) { ?>
	  <tr>
	   <td class="tbody" width="30%"><?php echo $lang->phrase('admin_packages_info_license'); ?></td>
	   <td class="tbody" width="70%"><?php echo $row['license']; ?></td>
	  </tr>
	  <?php } } if (!empty($row['min_version']) || !empty($row['max_version'])) { ?>
	  <tr>
	   <td class="tbody" width="30%"><?php echo $lang->phrase('admin_packages_info_compatibility'); ?></td>
	   <td class="tbody" width="70%">
	   	<?php if (!empty($row['min_version'])) { $min = $row['min_version']; ?>
	   	<div><?php echo $lang->phrase('admin_packages_minimum_v'); ?></div>
	   	<?php } ?>
	   	<?php if (!empty($row['max_version'])) { $max = $row['max_version']; ?>
	   	<div><?php echo $lang->phrase('admin_packages_maximum_v'); ?></div>
	   	<?php } ?>
	   </td>
	  </tr>
	  <?php } if (!empty($row['preview'])) { ?>
	  <tr>
	   <td class="tfoot" colspan="2"><?php echo $lang->phrase('admin_packages_browser_preview'); ?></td>
	  </tr>
	  <tr>
	   <td class="tbody center" colspan="2"><img id="preview" src="<?php echo $row['preview']; ?>" border="0" alt="Preview/Screenshot" /></td>
	  </tr>
	  <?php } ?>
	 </table>
	<?php
}
else if ($job == 'browser') {
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

	$entries = 0;

	$foo = $types[$type];
	$breadcrumb->Add($foo['name']);
	echo $tpl->parse("header");
	?>
 <table class="tables">
  <tr>
   <td class="h3" colspan="2"><?php echo $lang->phrase('admin_packages_browser_head_browse_foo'); ?></td>
  </tr>
  <tr>
   <td class="tfoot" width="50%"><strong><?php echo $lang->phrase('admin_packages_browser_categories'); ?></strong></td>
   <td class="tfoot" width="50%"><strong><?php echo $lang->phrase('admin_packages_browser_useful_links'); ?></strong></td>
  <tr>
   <td class="tbody" valign="top" rowspan="3">
   	<?php if (count($cats) > 0) { ?>
	<ul>
		<?php foreach ($cats as $id => $row) { $entries += $row['entries']; ?>
		<li><a href="components.php?cid=<?php echo PACKAGE_ID; ?>&amp;job=browser_list&amp;type=<?php echo $type; ?>&amp;id=<?php echo $id; ?>"><?php echo $row['name']; ?></a> (<?php echo $row['entries']; ?>)</li>
		<?php } ?>
	</ul>
	<?php } else { echo $lang->phrase('admin_packages_no_x_found'); } ?>
   </td>
   <td class="tbody" valign="top">
	<ul>
		<li><a href="components.php?cid=<?php echo PACKAGE_ID; ?>&amp;job=browser_list&amp;type=<?php echo $type; ?>&amp;id=last"><?php echo $lang->phrase('admin_packages_browser_recently_updated'); ?> <?php echo $types[$type]['name']; ?></a></li>
	</ul>
   </td>
  </tr>
  <tr>
   <td class="tfoot" valign="top" height="1"><strong><?php $foo = ucfirst($types[$type]['name2']); echo $lang->phrase('admin_packages_browser_foo_of_the_moment');?></strong></td>
  </tr>
  <tr>
   <td class="tbody" valign="top">
   <?php if ($entries > 0) { ?>
	<a href="components.php?cid=<?php echo PACKAGE_ID; ?>&amp;job=browser_detail&amp;id=<?php echo $random['internal']; ?>&amp;type=<?php echo $type; ?>"><strong><?php echo $random['title']; ?></strong> <?php echo $random['version']; ?></a><br />
	<?php echo $random['summary'];
   }
   else { $foo = $types[$type]; echo $lang->phrase('admin_packages_no_x_found'); }
   ?>
   </td>
  </tr>
 </table>
	<?php
}
else {
	$breadcrumb->ResetUrl();
	$types = $pb->types();
	echo $tpl->parse("header");
	?>
<div class="border">
	<h3><?php echo $lang->phrase('ps_downloads'); ?></h3>
	<div class="bbody">
	<ul>
		<?php foreach ($types as $id => $type) { ?>
		<li><a href="components.php?cid=<?php echo PACKAGE_ID; ?>&amp;job=browser&amp;type=<?php echo $id; ?>"><?php echo $type['name']; ?></a></li>
		<?php } ?>
	</ul>
	</div>
</div>
	<?php
}

$zeitmessung = t2();
echo $tpl->parse("footer");
?><?php