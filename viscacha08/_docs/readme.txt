########################################
# Readme for Viscacha 0.8 RC 7         #
########################################

== Table of Contents ==
1.	Installation
2.	Update Viscacha
2.1	Stylesheet changes
3.	System requirements
4.	Contact


== 1 == Installation ==

1. Upload all files per ftp onto your server.
   Note: You do not need to upload the folder install/files/ for the
         installation! The folder is only required for the update.
2. Call the "install/" directory in the Viscacha-root-directory and
   follow the steps.
3. You have a "fresh" Viscacha-Installation on your server.

Note: More information on how to set up the CHMODs you can get while
      installing the application. In the ACP you can also see a more
      detailed list of the required CHMODs.


== 2 == Update ==

1. Make a complete backup of your data (FTP + MySQL)!
2. Upload the install/ directory
3. Execute the update script (Call the "install/" directory).
4. Follow all the steps while the update script is running.
5. After the update is ready and you are back in your Admin Control
   Panel, please check for updates of your installed packages!

Note: You can only update from Viscacha 0.8 RC5.

== 2.1 == Stylesheet Changes ==
This changes are for later design updates.
This steps will also be shown or executed in the update script!

You have to apply the following changes (for all CSS files) to all
your installed designs. * is a placeholder for a Design-ID (1 2,3,...).
The CSS definitions can vary depending on your modifications to the
styles.

== Changes in file designs/*/standard.css ==

1. Search:
------------------------------------------------------------------------
hr {
	height: 1px;
	border: 0;
	border-bottom: 1px #839FBC solid;
}
------------------------------------------------------------------------

Add below:
------------------------------------------------------------------------
tt {
	font-family: 'Courier New', monospace;
}
------------------------------------------------------------------------


== System requirements ==

Minimum system requirements:
 - PHP Version: 5.0.0 and above
               (4.3.0 or higher may work, but is unsupported as of 0.8 RC7)
 - PHP-Extensions: mysql or mysqli, pcre, gd, zlib
 - MySQL Version: 4.0 and above

Normal system requirements:
 - PHP Version: 5.0.0 and above
 - PHP-Extensions: mysql or mysqli, pcre, gd, zlib, xml, mime_magic
 - MySQL Version: 4.1 and above

Optimal system requirements:
 - PHP Version: 5.2.0 and above
 - PHP-Extensions: mysql or mysqli, pcre, gd, zlib, xml, mime_magic,
                   mbstring, sockets, xdiff
 - MySQL Version: 5.0 and above (Strict mode off)

If you are testing Viscacha, please give me some feedback how Viscacha
worked, which errors occurred and which server configuration was used.

Following information are useful for me:
- Operating system (of the server)
- Server software and version
- E-mail-server (SMTP, Sendmail, PHP's mail() function)
- MySQL version (strict mode enabled?)
- PHP version
- Status of the extensions: mysql, mysqli, pcre, gd, zlib, xml,
                            mime_magic, mbstring, sockets, xdiff
- The following settings in the file php.ini:
  - safe_mode
  - magic_quotes_gpc
  - register_globals
  - open_basedir


== Contact ==

Please contact us only through our support forums on
http://www.viscacha.org!

Bugtracker and ToDo List: http://bugs.viscacha.org