########################################
# Installation Viscacha 0.8 RC 1       #
########################################


== Preamble ==

This is the fifth public release of Viscacha (0.8). Some of the Feature
are missing, but I am working to complete these features for version 0.9.
You can commit bugs and suggestions to the bugtracker (see below) and I 
will check these entries and fix or implement them in most cases.

This version is meant only for testing purposes only and in productive use
there can occur some problems. This is version 0.8 and not 1.0. Until 
version 1.0 is released there will be some major changes that can affect 
the compatibility and may result in lost data. plugins and components are 
currently on the newest state, but there can be some minor changes in the
API of plugins and components. All available hooks of the plugin system you
can find in the file hooks.txt. If there is a hook missing, please contact 
the support and we will implement this hook into the next version of Viscacha.


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
- "docs"
- "feeds"
- "images" and all subdirectories
- "language" and all subdirectories
- "temp" and all subdirectories
- "templates" and all subdirectories
- All subdirectories of "uploads"

Following files need CHMOD 666:
- All files in the directories "admin/data/"
- All files in the directories "data" and "data/cron"
- All files in the directory "docs"
- All files in the directory "language" and all files in the subdirectories of 
  "language"
- All files in the directory "templates" and all files in the subdirectories of 
  "templates"


== Update ==

First make a backup of your old data!

[...]

Finally upload the install/ directory and execute the update script.

== System requirements ==

Minimum system requirements:
 - PHP Version: 4.1.0 and above
 - PHP-Extensions: mysql, pcre, gd, zlib
 - MySQL Version: 4.0 and above
  
Normal system requirements:
 - PHP Version: 4.3.0 and above
 - PHP-Extensions: mysql, pcre, gd, zlib, xml
 - MySQL Version: 4.1 and above
  
Optimal system requirements:
 - PHP Version: 5.0.0 and above
 - PHP-Extensions: mysql, pcre, gd, zlib, xml, pspell, iconv, mbstring, mhash, 
                   sockets, mime_magic
 - MySQL Version: 5.0 and above (Strict mode off)

If you are testing Viscacha, please give me some feedback how Viscacha worked,
which errors occurred and which server configuration was used.

Following information interest me:
- Operating system (of the server)
- Server software and version
- E-mail-server (SMTP, Sendmail, PHP's mail() function)
- MySQL version
- PHP version
- Status of the extensions: mysql, pcre, gd, zlib, xml, pspell, iconv, mbstring,
                            mhash
- The following settings in the file php.ini: 
  - safe_mode
  - magic_quotes_gpc
  - register_globals
  - open_basedir


== Contact ==

E-mail: webmaster@mamo-net.de
ICQ: 104458187
AIM: mamonede8
YIM: mamonede
Jabber: MaMo@jabber.ccc.de
MSN: ma_mo_web@hotmail.com

Bugtracker: http://bugs.viscacha.org