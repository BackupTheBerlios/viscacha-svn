WBB 2.3.* zu Viscacha 0.8 Beta 4 / RC1 Konverter

Getestet mit WBB 2.3.3 und Viscacha 0.8 RC1

Copyright (C) 2005-2007 MaMo Net, http://www.mamo-net.de

Vor der Konvertierung Viscacha komplett installieren. Die WBB-Datenbank und die Viscacha-
Datenbank m&uuml;ssen die selben seien! Der Administratoraccount wird jedoch wieder gel�scht,
der aus dem WBB steht sp�ter zur Verf�gung. Bei der Konvertierung bleiben die alten WBB-Daten
bestehen und werden nicht ver&auml;ndert. Falls ein Fehler auftritt ist der Konverter so
programmiert, dass die Installation nochmal begonnen werden kann oder der Schritt wiederholt
werden kann. Nach dem Konvertieren sollte der Cache des Viscacha einmal komplett geleert werden!

Um den Konverter zu konfigurieren &auml;ndern Sie in dieser Datei:
 - die Variable $wbbdir in Zeile 86 korrekt (s.u.) und
 - die Variable $adminid in Zeile 88 korrekt (s.u.).

Der Konbverter �bernimmt:
 - PMs (keine Attachments, keine Verzeichnisse (nur In- und Outbox), keine Lesebest�tigungen)
 - Mitglieder und Avatare (keine eigenen Profilfelder au�er Standard-Feld Wohnort, keine �bernahme
                           von Benutzerrechten)
 - Themen-Abos (keine Foren-Abos)
 - Beitr�ge und Themen (keine Umfragen, keine Pr�fixe, nur letzte Editierung eines Beitrags)
 - Foren und Kategorien (keine Pr�fixe)

Hinweis zum Konverter bzgl. Vollst&auml;ndigkeit:
Ich bin noch nicht ganz durch das WBB-Rechtesystem durchgestiegen. Falls jemand das
�bernehmen m&ouml;chte bitte ich um eine E-Mail an webmaster@mamo-net.de Derzeit wird nur
ein Administrator nach der Konfiguration unterhalb �bernommen. Alle anderen User sind
normale Mitglieder. Die Rechte m�ssen leider alle einzeln zur�ckverteilt werden.
Es werden au&szlig;erdem noch keine Umfragen und P&aumlr&auml;fixe &uuml;bernommen,
dies wird aber bald noch implementiert. Alle anderen nicht &uuml;bernommenen Daten
k&ouml;nnen entweder nicht &uuml;bernommen werden weil sie nicht kompatibel sind oder
weil das Viscacha diese Features nicht unterst&uuml;tzt.

Nun den Konverter (convert.php) in das Viscacha-Verzeichnis hochladen. (Es muss dort eine frische 
Viscacha-Installation zur Verf�gung stehen.) Abschlie�end im Browser die hochgeladene Datei 
(convert.php) aufrufen und den Anweisungen folgen.