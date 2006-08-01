########################################
# Installation Viscacha 0.8 Beta 2     #
########################################


== Pr�ambel ==

Dies ist das zweite �ffentliche Release des Viscacha (0.8). Einige Features 
fehlen noch, aber ich arbeite daran die Features f�r Version 0.9 fertig zu 
stellen. Fehler und Verbesserungsvorschl�ge k�nnen jederzeit im Bugtracker
(s.u.) hinterlassen werden. Ich werde diese Fehler dann pr�fen und ggf. ausbessern.

Diese Version ist weiterhin lediglich eher zum Testen gedacht und k�nnte im 
produktiven Einsatz Probleme bereiten. Es handelt sich hierbei um die Version 0.8, 
nicht 1.0. Ich spreche dies explizit an, da ich von 0.8 zu 1.0 noch einige 
grundlegende Dinge neu schreiben m�chte, was eine Kompatibilit�t der Versionen evtl. 
beeintr�chtigt und teilweise Daten verloren gehen k�nnen oder von Hand nachgetragen 
werden m�ssen. Plugins und Komponenten sind vorerst auf dem neusten Stand, jedoch 
k�nnten noch kleinere �nderungen (vor allem bei den Plugins) an der API vorgenommen 
werden. Alle vorhandenen Einstiegspunkte (Hooks) des Plugin-Systems finden Sie in der 
Datei hooks.txt. Sollte ein Hook f�r Ihr Plugin fehlen, scheuen Sie sich nicht im Support-
Forum danach zu fragen. Wir implementieren gerne in die n�chste Version neue Hooks.


== Installation ==

Alle Dateien per FTP auf Ihren Server hochladen und den Ordner "install/" im
Viscacha-Verzeichnis aufrufen und den Anweisungen folgen. 
Anschlie�end sollte Ihnen eine "frische" Viscacha-Installation zur Verf�gung stehen.


== CHMODs ==
Die Dateien des Viscacha brauchen teilweise mehr Rechte als sie normalerweise vom Server 
bekommen. Falls bei der Installation das Setzen der CHMODs fehlschl�gt (auch mit FTP-Daten),
dann setzen Sie die CHMODs bitte wie folgt:

Folgende Ordner ben�tigen CHMOD 777:
- "admin/backup"
- "cache" und alle Unterordner
- "classes/cron/jobs"
- "classes/feedcreator"
- "classes/fonts"
- "classes/geshi"
- "classes/graphic/noises"
- "components"
- "data" und alle Unterordner
- "designs" und alle Unterordner
- "docs"
- "feeds"
- "images" und alle Unterordner
- "language" und alle Unterordner
- "temp" und alle Unterordner
- "templates" und alle Unterordner
- Alle Unterordner von "uploads"

Folgende Dateien ben�tigen CHMOD 666:
- admin/data/notes.php
- Alle Dateien im Ordner "data" und "data/cron"
- Alle Dateien im Ordner "docs"
- Alle Dateien im Ordner "language" und dessen Unterordnern
- Alle Dateien im Ordner "templates" und dessen Unterordnern

== Update ==

Als erstes sollten Sie ein Backup Ihrer alten Daten machen!

L�schen Sie folgende Ordner vollst�ndig und laden Sie die entsprechenden Ordner aus 
dem Verzeichnis der Version 0.8 Beta 2 anschlie�end daf�r hoch:
- languages/
- modules/
- templates/1/
- cache/
- temp/

Wenn nicht mehr ben�tigt l�schen Sie den Ordner vollst�ndig:
- smilies (liegen nun in images/smileys)

Laden Sie alle folgenden Order/Dateien hoch und ersetzen Sie diese gegebenenfalls: 
- admin/ (au�er admin/licenses/notes.php!)
- classes/
- docs/credits.php
- images/smileys/
- templates/editor.js
- templates/global.js
- templates/editor
- /
- install/

In jedem Image-Set folgende Dateien ersetzen bzw. neu hochladen:
- negative.gif
- positive.gif
- skype.gif
- ucp_abos.gif
- ucp_signature.gif

In jedem Image-Set folgende Datei l�schen:
- ucp_fav.gif

�ffnen Sie die standard.css in all Ihren Designs und f�hren Sie die folgenden 
4 Schritte durch:

1. F�gen Sie alles zwischen den Strichen (-) hinzu:
---------------------------------------------------
.popup_noscript {
	text-align: center;
	background-color: #ffffff;
	border: 1px solid #839FBC;
	border-top: 0px;
}
.popup_noscript li {
	display: inline;
	font-weight: bold;
	padding-right: 0.8em; 
	padding-left: 0.8em;
}
.popup_noscript strong {
	text-align: left;
	display: block;
	padding: 4px;
	background-color: #BCCADA;
	border-top: 1px solid #839FBC;
	border-bottom: 1px solid #839FBC;
	color: #336699;
	font-size: 9pt;
}
.popup_noscript ul {
	padding: 4px; 
	margin: 0px; 
	list-style-type: none;
}
---------------------------------------------------

2. Finden Sie folgende Zeile:
.navigation_cat .nav_sub, .navigation_cat .nav {
und ersetzen Sie diese mit der folgenden zeile:
.navigation_cat ul ul, .navigation_cat ul {

3. Finden Sie folgende Zeile:
.navigation_cat .nav_sub {
und ersetzen Sie diese mit der folgenden zeile:
.navigation_cat ul ul {

4. Finden Sie folgende Zeile:
.navigation_cat .nav {
und ersetzen Sie diese mit der folgenden zeile:
.navigation_cat ul {


== Systemvoraussetzungen ==

Minimale Systemvoraussetzungen:
 - PHP Version: 4.1.0 und h�her
 - PHP-Erweiterungen: mysql, pcre, gd, zlib
 - MySQL Version: 3.23.57 und h�her
  
Normale Systemvoraussetzungen:
 - PHP Version: 4.3.0 und h�her
 - PHP-Erweiterungen: mysql, pcre, gd, zlib, xml
 - MySQL Version: 4.0  und h�her
  
Optimale Systemvoraussetzungen:
 - PHP Version: 5.0.0 und h�her
 - PHP-Erweiterungen: mysql, pcre, gd, zlib, xml, pspell, iconv, mbstring, mhash, 
                      sockets
 - MySQL Version: 4.1 und h�her

Wenn Sie das Viscacha testen, bitte ich Sie darum, mir Bescheid zu geben unter 
welcher Serverkonfiguration das Viscacha lief und welche Fehler aufgetreten sind.

Folgende Angaben sind von Interesse:
- Betriebssystem (des Servers)
- Serversoftware und Version
- E-Mail-Versand-Art (SMTP, Sendmail, PHP-Intern)
- MySQL-Version
- PHP-Version
- Status der Extensions: mysql, pcre, gd, zlib, xml, pspell, iconv, mbstring, mhash
- Folgende Einstellungen in der php.ini: 
  - safe_mode
  - magic_quotes_gpc
  - register_globals
  - register_long_arrays
  - sql.safe_mode


== Kontakt ==

E-mail: webmaster@mamo-net.de
ICQ: 104458187
AIM: mamonede8
YIM: mamonede
Jabber: MaMo@jabber.ccc.de
MSN: ma_mo_web@hotmail.com

Bugtracker: http://bugs.viscacha.org