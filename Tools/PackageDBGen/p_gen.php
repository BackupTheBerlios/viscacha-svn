<?php
/*
	Viscacha - A bulletin board solution for easily managing your content
	Copyright (C) 2004-2007  Matthias Mohr, MaMo Net

	Author: Matthias Mohr
	Publisher: http://www.viscacha.org
	Start Date: May 22, 2004

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/
error_reporting(E_ALL);

DEFINE('SCRIPTNAME', 'package_db_generator');
if (!defined('VISCACHA_CORE')) {
	define('VISCACHA_CORE', '1');
}

require_once("data/config.inc.php");
require_once("classes/function.viscacha_frontend.php");

$zeitmessung1 = t1();

$slog = new slog();
$my = $slog->logged();
$lang->init($my->language);
$tpl = new tpl();
$my->p = $slog->Permissions();
$my->pb = $slog->GlobalPermissions();

$breadcrumb->Add($lang->phrase('portal_title'));
echo $tpl->parse("header");
// Start Generator
define('IMPTYPE_PACKAGE', 1);
define('IMPTYPE_DESIGN', 2);
define('IMPTYPE_SMILEYPACK', 3);
define('IMPTYPE_LANGUAGE', 4);
define('IMPTYPE_BBCODE', 5);

include('classes/class.zip.php');
$myini = new INI();

$types = array(
 	1 => 'packages',
 	2 => 'designs',
 	3 => 'smileys',
 	4 => 'languages',
 	5 => 'bbc'
);
?>
<div class="border">
	<h3>Browser Database Generator</h3>
	<div class="bbody">
	Files generated (5): <a href="viscacha/files/external.ini">external.ini</a>
<?php
foreach ($types as $key => $dir) {
	$ini = array();
	$cwd = getcwd();
	chdir("viscacha/files/{$dir}/");
	$files = array();
	$dirs = glob("*", GLOB_ONLYDIR);
	foreach ($dirs as $key2 => $dir2) {
		chdir($dir2);
		$files[$key2+1] = array_merge(glob("*.zip"),glob("*.bbc"));
		$ini['categories'][$key2+1] = $dir2;
		chdir("../");
	}
	chdir($cwd);
	foreach ($files as $dirid => $files2) {
		foreach ($files2 as $file) {
			$fp = 'viscacha/files/'.$dir.'/'.$ini['categories'][$dirid].'/'.$file;
			$data = array(
				'title' => $file,
				'internal' => "viscacha_".substr($file, 0, strlen($file)-4),
				'version' => "",
				'copyright' => "Copyright (c) 2007 by Matthias Mohr, MaMo Net (http://www.mamo-net.de)",
				'summary' => "",
				'license' => "GNU General Public License",
				'min_version' => "",
				'max_version' => "",
				'url' => "http://www.viscacha.org",
				'file' => "http://files.viscacha.org/{$dir}/".rawurlencode($ini['categories'][$dirid])."/{$file}",
				'preview' => "",
				'category' => $dirid,
				'multiple' => 0,
				'last_updated' => filemtime($fp)
			);
			if ($key == IMPTYPE_PACKAGE) {
				$zip = new PclZip($fp);
			    $list = $zip->extract(PCLZIP_OPT_BY_NAME, "modules/package.ini", PCLZIP_OPT_EXTRACT_AS_STRING);
				$package = $myini->parse($list[0]['content']);
				if (empty($package['info']['title']) || empty($package['info']['internal'])) {
					continue;
				}
				$data = array_merge($data, $package['info']);
				unset($data['core']);
			}
			$md5 = md5_file($fp);
			$ini[$md5] = $data;
		}
	}
	$myini->write('viscacha/files/external_'.$key.'.ini', $ini);
	?>
		<hr />
		Files generated: <a href="viscacha/files/external_<?php echo $key; ?>.ini">external_<?php echo $key; ?>.ini</a>
		<pre><?php print_r($ini); ?></pre>
	<?php
}
?>
	</div>
</div>
<?php
// End Generator
$slog->updatelogged();
$zeitmessung = t2();
echo $tpl->parse("footer");

$phpdoc->Out();
$db->close();
?>