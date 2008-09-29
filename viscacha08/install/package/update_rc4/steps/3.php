<?php
$tar_packs = array(
	1 => 'update.admin.tar',
	2 => 'update.classes.tar',
	3 => 'update.misc.tar',
	4 => 'update_rc4.misc.tar'
);
if (empty($_REQUEST['sub']) || !isset($tar_packs[$_REQUEST['sub']])) {
	$sub = 1;
}
else {
	$sub = $_REQUEST['sub'];
}
require('classes/class.tar.php');
$tar = new tar();
$tar->new_tar('./files/', $tar_packs[$sub]);
$tar->ignore_chmod();
$tar->over_write_existing();
$error = $tar->extract_files('../');
?>
<div class="bfoot">Source file updater: Step <?php echo $sub; ?> (<?php echo $tar_packs[$sub]; ?>) of <?php echo count($tar_packs); ?></div>
<div class="bbody">
<p>
The updater tried to update the Viscacha source files.<br />
<b>The following files could not be updated and must be updated manually before clicking the link below:</b>
<textarea class="codearea"><?php echo implode("\r\n", $error); ?></textarea>
</p>
<?php if ($sub < count($tar_packs)) { ?>
<p class="center"><b><a href="index.php?package=<?php echo $package;?>&amp;step=<?php echo $step; ?>&amp;sub=<?php echo $sub+1; ?>">Click here to extract next files...</a></b></p>
<?php } ?>
</div>
<?php if ($sub == count($tar_packs)) { ?>
<div class="bfoot center"><input type="submit" value="Continue" /></div>
<?php } ?>