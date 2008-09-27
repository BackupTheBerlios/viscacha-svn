########################################
# Readme for Viscacha 0.8 RC 5         #
########################################

== Table of Contents ==
1. Installation
2. CHMODs
3. Update Viscacha 0.8 RC4     to 0.8 RC5
4  Update Viscacha 0.8 RC4 PL1 to 0.8 RC5
5. System requirements
6. Contact


== Installation ==

Upload all files per ftp onto your server. Then call the "install/" directory
in the Viscacha-root-directory and follow the steps. Then a "fresh" Viscacha-
Installation will be available on your server.


== CHMODs ==
Some of the Viscacha files need more permissions on the server than they have
normally on the server. It may happen, that it fails to set the CHMODs while
setting up Viscacha. In this case you have to set them manually:

Following directories need CHMOD 777:
- "admin/backup"
- "cache" and all subdirectories
- "classes/cron/jobs"
- "classes/feedcreator"
- "classes/fonts"
- "classes/geshi"
- "classes/graphic/noises"
- "components"
- "data" and all subdirectories
- "designs" and all subdirectories
- "feeds"
- "images" and all subdirectories
- "language" and all subdirectories
- "temp" and all subdirectories
- "templates" and all subdirectories
- All subdirectories of "uploads"

Following files need CHMOD 666:
- All files in the directory "admin/data/"
- All files in the directories "data" and "data/cron"
- All files in the directory "language" and all files in the subdirectories of
  "language"
- All files in the directory "templates" and all files in the subdirectories of
  "templates"


== Update Viscacha 0.8 RC4 to 0.8 RC5 ==
First make a complete backup of your (old) data!
Note: This instructions are only for an update from Viscacha 0.8 RC4 to
0.8 RC5! The update instructions for 0.8 RC4 PL1 to 0.8 RC5 are below.

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
 - templates/menu.js

Finally upload the install/ directory and execute the update script.
After the update is ready and you are back in your Admin Control Panel,
please check for Updates of your installed Packages!


== Update Viscacha 0.8 RC4 PL1 to 0.8 RC5 ==
First make a complete backup of your (old) data!
Note: This instructions are only for an update from Viscacha 0.8 RC4 PL1 to
0.8 RC5! The update instructions for 0.8 RC4 to 0.8 RC4 PL1 are above.

1. Upload (and overwrite) the following files (* = an ID):
 - addreply.php
 - admin.php
 - ajax.php
 - attachments.php
 - components.php
 - cron.php
 - docs.php
 - edit.php
 - editprofile.php
 - external.php
 - forum.php
 - images.php
 - log.php
 - manageforum.php
 - managemembers.php
 - managetopic.php
 - members.php
 - misc.php
 - newtopic.php
 - pdf.php
 - pm.php
 - popup.php
 - portal.php
 - print.php
 - profile.php
 - register.php
 - search.php
 - showforum.php
 - showtopic.php

 - admin/bbcodes.php
 - admin/cms.php
 - admin/cron.php
 - admin/db.php
 - admin/explorer.php
 - admin/filetypes.php
 - admin/forums.php
 - admin/frames.php
 - admin/language.php
 - admin/members.php
 - admin/misc.php
 - admin/packages.php
 - admin/posts.php
 - admin/settings.php
 - admin/slog.php
 - admin/spider.php

 - admin/html/admin.js
 - admin/html/standard.css
 - admin/html/images/asc.gif
 - admin/html/images/desc.gif

 - admin/lib/function.viscacha_backend.php

 - classes/class.bbcode.php
 - classes/class.cache.php
 - classes/class.convertroman.php
 - classes/class.feedcreator.php
 - classes/class.geshi.php
 - classes/class.gpc.php
 - classes/class.idna.php
 - classes/class.imstatus.php
 - classes/class.language.php
 - classes/class.permissions.php
 - classes/class.plugins.php
 - classes/class.profilefields.php
 - classes/class.snoopy.php
 - classes/function.flood.php
 - classes/function.frontend_init.php
 - classes/function.global.php
 - classes/function.phpcore.php
 - classes/function.profilefields.php
 - classes/function.viscacha_frontend.php

 - classes/cache/custombb.inc.php
 - classes/cache/grabrss.inc.php
 - classes/cache/memberdata.inc.php
 - classes/cache/spiders.inc.php
 - classes/cache/syntaxhighlight.inc.php
 - classes/cache/UniversalCodeCache.inc.php
 - classes/cache/wraps.inc.php

 - classes/cron/jobs/dboptimize.php
 - classes/cron/jobs/exportBoardStats.php

 - classes/database/class.db_driver.php
 - classes/database/mysql.inc.php
 - classes/database/mysqli.inc.php

 - classes/feedcreator/atom10.inc.php
 - classes/feedcreator/googlesitemap.inc.php
 - classes/feedcreator/html.inc.php
 - classes/feedcreator/javascript.inc.php
 - classes/feedcreator/klipfolio.inc.php
 - classes/feedcreator/klipfood.inc.php
 - classes/feedcreator/opml.inc.php
 - classes/feedcreator/pie01.inc.php
 - classes/feedcreator/rss091.inc.php
 - classes/feedcreator/rss10.inc.php
 - classes/feedcreator/rss20.inc.php
 - classes/feedcreator/xbel.inc.php

 - classes/ftp/class.ftp.php
 - classes/ftp/class.ftp_pure.php

 - classes/geshi/actionscript.php
 - classes/geshi/bash.php
 - classes/geshi/cfm.php
 - classes/geshi/cpp.php
 - classes/geshi/csharp.php
 - classes/geshi/css.php
 - classes/geshi/delphi.php
 - classes/geshi/diff.php
 - classes/geshi/html4strict.php
 - classes/geshi/ini.php
 - classes/geshi/java5.php
 - classes/geshi/javascript.php
 - classes/geshi/latex.php
 - classes/geshi/mysql.php
 - classes/geshi/perl.php
 - classes/geshi/php.php
 - classes/geshi/python.php
 - classes/geshi/ruby.php
 - classes/geshi/sql.php
 - classes/geshi/text.php
 - classes/geshi/vbnet.php
 - classes/geshi/xml.php

 - classes/magpie_rss/rss_fetch.inc.php
 - classes/magpie_rss/rss_parse.inc.php

 - classes/mail/class.phpmailer.php
 - classes/mail/class.pop3.php
 - classes/mail/class.smtp.php

 - images/*/asc.gif
 - images/*/desc.gif
 - images/*/help.gif

 - templates/global.js
 - templates/lang2js.php
 - templates/frontend.js

 - templates/*/admin/members/edit.html
 - templates/*/admin/topic/post_merge.html
 - templates/*/edit/edit.html
 - templates/*/editprofile/about.html
 - templates/*/editprofile/signature.html
 - templates/*/main/bbhtml.html
 - templates/*/main/pages.html
 - templates/*/main/pages_small.html
 - templates/*/members/index.html
 - templates/*/modules/7/quick-reply.html
 - templates/*/newtopic/index.html
 - templates/*/pm/index.html
 - templates/*/pm/new.html
 - templates/*/popup/header.html
 - templates/*/profile/ims.html
 - templates/*/profile/index.html
 - templates/*/register/register.html
 - templates/*/search/index.html
 - templates/*/showforum/index.html
 - templates/*/showtopic/index.html
 - templates/*/showtopic/index_bit.html
 - templates/*/addreply.html
 - templates/*/categories.html
 - templates/*/header.html
 - templates/*/menu.html
 - templates/*/main/notice_box.html

 - templates/editor/bbcode.js
 - templates/editor/wysiwyg.js
 - templates/editor/wysiwyg-color.js
 - templates/editor/wysiwyg-popup.js

 - templates/editor/images/bgcolor.gif
 - templates/editor/images/bold.gif
 - templates/editor/images/hr.gif
 - templates/editor/images/italic.gif
 - templates/editor/images/strikethrough.gif
 - templates/editor/images/subscript.gif
 - templates/editor/images/superscript.gif
 - templates/editor/images/underline.gif
 - templates/editor/images/center.gif
 - templates/editor/images/color.gif
 - templates/editor/images/edit.gif
 - templates/editor/images/email.gif
 - templates/editor/images/img.gif
 - templates/editor/images/indent_left.gif
 - templates/editor/images/indent_right.gif
 - templates/editor/images/justify.gif
 - templates/editor/images/left.gif
 - templates/editor/images/list_ordered.gif
 - templates/editor/images/list_unordered.gif
 - templates/editor/images/maximize.gif
 - templates/editor/images/note.gif
 - templates/editor/images/ot.gif
 - templates/editor/images/quote.gif
 - templates/editor/images/remove_format.gif
 - templates/editor/images/right.gif
 - templates/editor/images/select_font.gif
 - templates/editor/images/select_heading.gif
 - templates/editor/images/select_size.gif
 - templates/editor/images/seperator.gif
 - templates/editor/images/sys_copy.gif
 - templates/editor/images/sys_cut.gif
 - templates/editor/images/sys_paste.gif
 - templates/editor/images/sys_redo.gif
 - templates/editor/images/sys_undo.gif
 - templates/editor/images/table.gif
 - templates/editor/images/tt.gif
 - templates/editor/images/url.gif
 - templates/editor/images/view_html.gif
 - templates/editor/images/view_text.gif

 - templates/editor/popups/create_table.html
 - templates/editor/popups/insert_hr.html
 - templates/editor/popups/insert_hyperlink.html
 - templates/editor/popups/select_color.html


Finally upload the install/ directory and execute the update script.
After the update is ready and you are back in your Admin Control Panel,
please check for Updates of your installed Packages!


== System requirements ==

Minimum system requirements:
 - PHP Version: 4.3.0 and above
 - PHP-Extensions: mysql or mysqli, pcre, gd, zlib
 - MySQL Version: 4.0 and above

Normal system requirements:
 - PHP Version: 5.0.0 and above
 - PHP-Extensions: mysql or mysqli, pcre, gd, zlib, xml, mime_magic
 - MySQL Version: 4.1 and above

Optimal system requirements:
 - PHP Version: 5.2.0 and above
 - PHP-Extensions: mysql or mysqli, pcre, gd, zlib, xml, mime_magic, mbstring,
				   sockets, xdiff
 - MySQL Version: 5.0 and above (Strict mode off)

If you are testing Viscacha, please give me some feedback how Viscacha worked,
which errors occurred and which server configuration was used.

Following information are useful:
- Operating system (of the server)
- Server software and version
- E-mail-server (SMTP, Sendmail, PHP's mail() function)
- MySQL version (strict mode enabled?)
- PHP version
- Status of the extensions: mysql, mysqli, pcre, gd, zlib, xml, mime_magic,
							mbstring, sockets, xdiff
- The following settings in the file php.ini:
  - safe_mode
  - magic_quotes_gpc
  - register_globals
  - open_basedir


== Contact ==

Please contact us through our support forums on http://www.viscacha.org!
Bugtracker: http://bugs.viscacha.org