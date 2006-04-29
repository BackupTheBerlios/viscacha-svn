########################################
# Installation Viscacha 0.8 Beta 2     #
########################################


== Pr�ambel ==

Dies ist das zweite �ffentliche Release des Viscacha (0.8). Einige Features 
fehlen noch, aber ich arbeite daran die Features f�r Version 0.9 fertigzu-
stellen. Fehler und Verbesserungsvorschl�ge k�nnen jederzeit im Bugtracker
(s.u.) hinterlassen weden. Ich werde diese Fehler dann pr�fen und ggf. ausbessern.

Diese Version ist weiterhin lediglich zum Testen gedacht und sollte nicht 
im produktiven Einsatz benutzt werden, denn es k�nnen Daten beim Update verloren 
gehen. Es handelt sich hierbei um die Version 0.8, nicht 1.0. Ich spreche dies 
explizit an, da ich von 0.8 zu 1.0 noch viele grundlegende Dinge neuschreiben m�chte, 
was eine Kompatibilit�t der Versionen evtl. beeintr�chtigt und teilweise Daten ver-
loren gehen k�nnen oder von Hand nachgetragen werden m�ssen. Plugins und Komponenten
sind vorerst auf dem neusten Stand, jedoch k�nnten noch kleinere �nderungen an der 
API vorgenommen werden. Alle vorhendenen Einstiegspunkte (Hooks) des Plugin-Systems
finden Sie in der Datei hooks.txt

Einige Funktionen im Admin Control Panel sind nocht komplett funktionsf�hig und
evtl. sind in der �bersetzung noch einige Fehler. Diese bitte ich im Forum zu melden.


== Installation ==

Alle Dateien per FTP auf Ihren Server hochladen und den Ordner "install/" im
Viscacha-Verzeichnis aufrufen und den Anweisungen folgen. 
Anschlie�end sollte euch eine "frische" Viscacha-Installation zur Verf�gung stehen.


== Systemvorraussetzungen ==

N�here Informationen zu den Systemvorraussetzungen erhalten Sie in der Datei
requirements.txt. 

Wenn Sie das Viscacha testen, bitte ich Sie darum, mir Be-
scheid zu geben unter welcher Serverkonfiguration das Viscacha lief und welche
Fehler aufgetreten sind.

Folgende Angaben sind von Interesse:
- Betriebssystem (des Servers)
- Serversoftware und Version
- Mailversand-Art (SMTP, Sendmail, PHP-Intern)
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

E-Mail: webmaster@mamo-net.de
ICQ: 104458187
AIM: mamonede8
YIM: mamonede
Jabber: MaMo@jabber.ccc.de
MSN: ma_mo_web@hotmail.com

Bugtracker: http://bugs.viscacha.org