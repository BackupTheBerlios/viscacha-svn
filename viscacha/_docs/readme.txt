########################################
# Installation Viscacha 0.8 Beta 1     #
########################################

== Pr�ambel ==

Dies ist das erste �ffentliche Release des Viscacha (0.8). Einige Features 
fehlen noch, aber ich arbeite daran die Features f�r Version 0.9 fertigzu-
stellen. Fehler und Verbesserungsvorschl�ge k�nnen jederzeit im Bugtracker
(s.u.) hinterlassen weden. Ich werde diese Fehler dann pr�fen und ggf. ausbessern.

Diese Version ist weiterhin lediglich zum Testen gedacht und sollte nicht 
im produktiven Einsatz benutzt werden, denn es k�nnen Daten beim Update auf Version
0.9 verloren gehen.
Es handelt sich hierbei um die Version 0.8, nicht 1.0. Ich spreche dies explizit
an, da ich von 0.8 zu 1.0 noch viele grundlegende Dinge neuschreiben m�chte, was
eine Kompatibilit�t der Versionen evtl. beeintr�chtigt und teilweise Daten ver-
loren gehen k�nnen oder von Hand nachgetragen werden m�ssen. Weiterhin werden 
Module und Komponenten wohl �berarbeitet werden m�ssen.

Einige Funktionen im Admin Control Panel sind nocht nicht funktionsf�hig und
vieles muss noch in die englische Sprache �bersetzt werden. Wer Lust hat, mir
beim �bersetzen des Administrationscenters zu helfen, meldet sich bitte bei mir.

== Installation ==

Einfach den Ordner "install/" im Viscacha-Verzeichnis 
aufrufen und den Anweisungen folgen. Anschlie�end sollte euch eine "frische"
Viscacha-Installation zur Verf�gung stehen.

== Systemvorraussetzungen ==
N�here Informationen zu den Systemvorraussetzungen erhalten Sie in der Datei
requirements.txt. Wenn Sie das Viscacha testen, bitte ich Sie darum, mir Be-
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

== Komponenten und PlugIns ==
PlugIns sind in dieser Version noch nicht m�glich (abgesehen von den schon vorhandenen).
Komponenten sind keine mitgeliefert. Diese werden erst in einer sp�teren Version mit-
geliefert.