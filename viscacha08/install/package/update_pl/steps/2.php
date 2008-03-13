<div class="bbody">
<p>
Before we start the automatic update, you have to read the manual update instructions.
Please follow the steps and do the tasks.
More Information:
<?php if (file_exists('../_docs/readme.txt')) { ?>
<a href="../_docs/readme.txt" target="_blank">_docs/readme.txt</a>
<?php } else { ?>
_docs/readme.txt
<?php } ?>
</p>
<p>
<strong>Update instructions:</strong><br />
<textarea class="codearea">First make a complete backup of your (old) data!

1. Upload (and overwrite) the following files (* = an ID):
 - misc.php
 - register.php

 - admin/cms.php
 - admin/forums.php
 - admin/packages.php
 - admin/profilefield.php

 - classes/class.bbcode.php
 - classes/class.permissions.php
 - classes/function.global.php
 - classes/function.viscacha_frontend.php

2. Upload (and overwrite) the following files (* = an ID):
       Note: Files from the directory language/1/ are German, files from the
             directory language/2/ are English. Upload only the files from the
             language you need into the correct directory.
 - language/*/wwo.lng.php

3. Upload the following files from the directory "templates" (* = an ID):
 - templates/*/attachments.html
 - templates/menu.js</textarea>
</p>
</div>
<div class="bfoot center"><input type="submit" value="Continue" /></div>