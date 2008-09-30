<?php
include('../data/config.inc.php');
if (!class_exists('filesystem')) {
	require_once('classes/class.filesystem.php');
	$filesystem = new filesystem($config['ftp_server'], $config['ftp_user'], $config['ftp_pw'], $config['ftp_port']);
	$filesystem->set_wd($config['ftp_path'], $config['fpath']);
}

$tar_packs = array(
	1 => 'update.admin.tar',
	2 => 'update.classes.tar',
	3 => 'update.misc.tar'
);
if (empty($_REQUEST['sub']) || !isset($tar_packs[$_REQUEST['sub']])) {
	$sub = 1;
}
else {
	$sub = $_REQUEST['sub'];
}
require('classes/class.tar.php');
$tar = new tar(realpath('files/'), $tar_packs[$sub]);
$tar->ignore_chmod();
$error = $tar->extract_files('../');
?>
<div class="bfoot">Source file updater - Step <?php echo $sub; ?> of <?php echo count($tar_packs); ?> - Currently extracting: <?php echo $tar_packs[$sub]; ?></div>
<?php if ($error === false) { ?>
<div class="bbody">
	<strong>A critical error occured. Please contact the <a href="http://www.viscacha.org" target="_blank">Viscacha Support Team</a> for assistance!</strong><br />
	Error message: <?php echo $tar->error; ?>
</div>
<?php } else { ?>
<div class="bbody">
<p>
The updater tried to update the Viscacha source files.<br />
<?php if (count($error) > 0) { ?>
<b>The following files could not be updated and must be updated manually before clicking the link below:</b>
<textarea class="codearea"><?php echo implode("\r\n", $error); ?></textarea>
<?php } else { ?>
All files updated succesfully!
<?php } ?>
</p>
<?php if ($sub < count($tar_packs)) { ?>
<p class="center"><b><a href="index.php?package=<?php echo $package;?>&amp;step=<?php echo $step; ?>&amp;sub=<?php echo $sub+1; ?>">Click here to extract the next file...</a></b></p>
<?php } ?>
</div>
<?php if ($sub == count($tar_packs)) { ?>
<div class="bfoot center"><input type="submit" value="Continue" /></div>
<?php } } ?>