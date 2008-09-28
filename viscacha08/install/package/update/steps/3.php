<?php
require('classes/class.tar.php');
$tar = new tar();
$tar->new_tar('/path', 'file.tar');
$tar->ignore_chmod();
$tar->over_write_existing();
$error = $tar->extract_files('../');
?>
<div class="bbody">
<p>
The updater tried to update the Viscacha source files. The following files could not be updated and must be updated manually.
</p>
<p>
<strong>Update instructions:</strong><br />
<textarea class="codearea"><?php echo implode("\r\n", $error); ?></textarea>
</p>
</div>
<div class="bfoot center"><input type="submit" value="Continue" /></div>