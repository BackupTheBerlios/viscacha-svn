<?php
if (defined('VISCACHA_CORE') == false) { die('Error: Hacking Attempt'); }
$lang = array();
$lang['bb_edit_author'] = 'Nachtr�gliche Anmerkung des Autors:';
$lang['bb_edit_mod'] = 'Nachtr�gliche Anmerkung von';
$lang['bb_hidden_content'] = 'Versteckter Inhalt:';
$lang['bb_offtopic'] = 'Off-Topic:';
$lang['bb_quote'] = 'Zitat:';
$lang['bb_quote_by'] = 'Zitat von';
$lang['bb_sourcecode'] = 'Quelltext:';
$lang['bbcodes_align'] = 'Ausrichtung';
$lang['bbcodes_align_center'] = 'Zentriert';
$lang['bbcodes_align_desc'] = 'Der [align] Tag erm�glicht die Ausirchtung von Texten/Abs�tzen. Als Parameter f�r die Ausrichtung k�nnen folgende Arten benutzt werden: left (Linksb�ndig, standard), center (Zentriert), right (Rechtsb�ndig), justify (Blocksatz).';
$lang['bbcodes_align_justify'] = 'Blocksatz';
$lang['bbcodes_align_left'] = 'Linksb�ndig';
$lang['bbcodes_align_right'] = 'Rechtsb�ndig';
$lang['bbcodes_align_title'] = 'Schriftausrichtung ausw�hlen';
$lang['bbcodes_bold'] = 'Fettschrift';
$lang['bbcodes_bold_desc'] = 'Mit den [b] Tag k�nnen Sie Texte fett darstellen.';
$lang['bbcodes_code'] = 'Quelltext (Syntax Highlighting)';
$lang['bbcodes_code_desc'] = 'Mit dem [code] Tag kann Quelltext gekennzeichnet werden. Einzeiliger Code flie�t im Text mit, ist jedoch besonders gekennzeichnet. Mehrzeiliger Code wird bei Benutzung ohne Parameter so angezeigt wie eingegeben, jedoch werden Einr�ckungen beibehalten. Der Code wird mit einer Monospace Schriftart angezeigt. Bei Angabe eines Parameters kann Syntax Highlighting f�r eine spezielle Sprache aktiviert werden. Der Code wird dann in einem extra Fenster angezeigt und sprachspezifisch formatiert. Folgende Parameter stehen zur Verf�gung (in Klammern stehen die Sprachennamen):<br />{$code_hl}.';
$lang['bbcodes_code_short'] = 'Code';
$lang['bbcodes_color'] = 'Farbe';
$lang['bbcodes_color_desc'] = 'Mit dem [color] Tag kann Text eingef�rbt werden. Die Farbe muss als Hexadezimal Wert eingegeben werden und kann entweder 3 oder 6 Zeichen lang sein. Das in HTML �bliche vorangestellte # ist optional.';
$lang['bbcodes_color_title'] = 'Farbe ausw�hlen';
$lang['bbcodes_create_table'] = 'Neue Tabelle erstellen';
$lang['bbcodes_edit'] = 'Nachtr�gliche Anmerkung / Kennzeichnung der editierten Textstellen';
$lang['bbcodes_edit_desc'] = 'Der [edit] Tag kennzeichnet bearbeitete Textstellen oder sp�ter erg�nzte Textpassagen. Falls kein Parameter angegeben wird, wird die Passage dem Autor zugeschrieben. Der optionale Parameter erm�glicht die Angabe eines Namens. Dieser Name wird dann als Editor angezeigt.';
$lang['bbcodes_email'] = 'E-Mail-Adresse';
$lang['bbcodes_email_desc'] = 'Mit dem [email] Tag k�nnen E-Mails sicher angezeigt werden. Die E-Mail-Adresse wird nicht normal per HTML verlinkt, sondern es wird ein unverlinktes Bild angezeigt, dass die E-Mail-Adresse anzeigt. Dies erschwert zwar die Benutzung der E-Mail-Adresse, sch�tzt jedoch effektiv vor Spam-Bots.';
$lang['bbcodes_example_text'] = 'Text';
$lang['bbcodes_example_text2'] = 'Text 2';
$lang['bbcodes_expand'] = 'Aufklappen';
$lang['bbcodes_header'] = '�berschrift';
$lang['bbcodes_header_desc'] = 'Der [h] Tag erm�glicht die Strukturierung eines Textes mittels �berschriften. Es gibt 3 Varianten: large (�berschrift 1. Ordnung; sehr gro�), middle (�berschrift 2. Ordnung; gro�) oder small (�berschrift 3. Ordnung; weniger gro�).';
$lang['bbcodes_header_h1'] = '�berschrift 1';
$lang['bbcodes_header_h2'] = '�berschrift 2';
$lang['bbcodes_header_h3'] = '�berschrift 3';
$lang['bbcodes_header_title'] = '�berschriftengr��e ausw�hlen';
$lang['bbcodes_help_example'] = 'Beispiel:';
$lang['bbcodes_help_output'] = 'Ausgabe:';
$lang['bbcodes_help_syntax'] = 'Syntax:';
$lang['bbcodes_hide'] = 'Versteckter Inhalt';
$lang['bbcodes_hide_desc'] = 'Mit dem [hide] Tag k�nnen Inhalte des Beitrags versteckt werden. Der Inhalt des Tags wird nur dem Autor, den berechtigten Moderatoren, den globalen Moderatoren und dem Administrator angezeigt.';
$lang['bbcodes_hr'] = 'Horizontale Linie';
$lang['bbcodes_hr_desc'] = 'Der [hr] Tag wird durch eine horizontale Linie ersetzt. Der Tag ben�tigt kein schlie�endes Element.';
$lang['bbcodes_img'] = 'Bild';
$lang['bbcodes_img_desc'] = 'Mit dem [img] Tag k�nnen Bilder vom Typ jpg, gif und png eingebunden werden. Die Bilder m�ssen eine korrekte Dateiendung besitzen, ansonsten werden die Bilder lediglich als Link dargestellt. Zu gro�e Bilder werden evtl. verkleinert und k�nnen mit einem Klick auf das verkleinerte Bild vergr��ert werden.';
$lang['bbcodes_italic'] = 'Kursiv';
$lang['bbcodes_italic_desc'] = 'Mit den [i] Tag k�nnen Sie Texte kursiv darstellen.';
$lang['bbcodes_list'] = 'Ungeordnete Liste';
$lang['bbcodes_list_desc'] = 'Mit dem [list] Tag k�nnen Sie geordnete und ungeordnete Listen erstellen. Um eine geordnete Liste zu erstellen muss der Tag um einen Parameter erweitert werden. Wird kein Parameter angegeben wird eine ungeordnete Liste angezeigt. Folgene Paramater stehen zur Verf�gung: ol oder OL (nummerierte Liste), a oder A (alphabetische Liste mit Klein- oder Gro�buchstaben) bzw. i oder I (Liste mit kleinen oder gro�en r�mischen Zahlen) ';
$lang['bbcodes_list_ol'] = 'Geordnete Liste';
$lang['bbcodes_note'] = 'Definition / Erkl�rung';
$lang['bbcodes_note_desc'] = 'Der [note] Tag hinterlegt W�rter mit einer Erkl�rung bzw. eienr Definition. Das Wort wird von dem Tag umschlossen und der Parameter ist die Definition. Es sind nur einzeilige Definitionen m�glich. Die Zeichen [ und ] d�rfen in der Definition ebenfalls nicht enthalten sein.';
$lang['bbcodes_option'] = 'Option';
$lang['bbcodes_ot'] = 'Off-Topic / Vom Thema abweichender Kommentar';
$lang['bbcodes_ot_desc'] = 'Der [ot] Tag kennzeichnet Textstellen, die keine Relevanz zum eigentlichen Thema haben. ';
$lang['bbcodes_param'] = 'Parameter';
$lang['bbcodes_quote'] = 'Zitat';
$lang['bbcodes_quote_desc'] = 'Der [quote] Tag dient der Kennzeichnung von Zitaten. Der Tag kann ich verschiedenen Variationen benutzt werden. Die erste Variante verzichtet auf die Nennung eines Autors/einer Quelle.In der zweiten und dritten Variante kann als Option ein Autor/eine Person genannt werden oder eine Internetadresse angegeben werden, die verlinkt wird.';
$lang['bbcodes_reader'] = 'Umwandlung des Lesernamens';
$lang['bbcodes_reader_desc'] = 'Der [reader] Tag wird durch den Namen des gerade lesenden Benutzers ausgetauscht. Der Tag ben�tigt kein schlie�endes Element.';
$lang['bbcodes_size'] = 'Gr��e';
$lang['bbcodes_size_desc'] = 'Mit dem [size] Tag kann die Schriftgr��e variiert werden. Folgende Parameter stehen zur Auswahl: large (gro�e Schrift), small (kleine Schrift) oder extended (Schrift mit erweitertem Zeichenabstand).';
$lang['bbcodes_size_extended'] = 'Erweiterte Schrift';
$lang['bbcodes_size_large'] = 'Gro�e Schrift';
$lang['bbcodes_size_small'] = 'Kleine Schrift';
$lang['bbcodes_size_title'] = 'Schriftgr��e ausw�hlen';
$lang['bbcodes_sub'] = 'Tiefgestellt';
$lang['bbcodes_sub_desc'] = 'Der [sub] Tag erlaubt das tiefstellen von bestimmten Zeichen bzw. von bestimmtem Text.';
$lang['bbcodes_sup'] = 'Hochgestellt';
$lang['bbcodes_sup_desc'] = 'Der [sup] Tag erlaubt das hochstellen von bestimmten Zeichen bzw. von bestimmtem Text.';
$lang['bbcodes_table'] = 'Tabelle';
$lang['bbcodes_table_cols'] = 'Spalten';
$lang['bbcodes_table_desc'] = 'Mit dem [table] Tag kann eine Tabelle realisiert werden. In dem [table] Tag werden die Daten eingetragen, wobei jede Zeile einer Tabellenzeile entspricht und einzelne Tabellenspalten werden durch dem [tab] Tag oder ein | getrennt werden. Mehrzeilige Eintr�ge in den einzelnen Zellen sind mit dem [br] Tag m�glich, in den Tabellenzellen k�nnen BB-Codes benutzt werden. Dem [table] Tag k�nnen zwei Optionen mitgegeben werden, getrennt mit Semikolon (;). Wenn die erste Zeile als �berschrift angezeigt werden soll, so muss "head" als Option angegeben werden. Falls die Tabelle eine bestimmte Breite haben soll, so kann die Breite in Prozent mit abschlie�endem Prozentzeichen (%) angegeben werden. Beide Optionen sind optional verwendbar.';
$lang['bbcodes_table_insert_table'] = 'Tabelle einf�gen';
$lang['bbcodes_table_rows'] = 'Zeilen';
$lang['bbcodes_table_show_head'] = 'Erste Zeile als Titelzeile benutzen';
$lang['bbcodes_tt'] = 'Schreibmaschinenschrift';
$lang['bbcodes_tt_desc'] = 'Der [tt] Tag stellt den Text wie mit einer Schreibmaschine geschrieben dar, also mit einer Monospace Schriftart.';
$lang['bbcodes_underline'] = 'Unterstrichen';
$lang['bbcodes_underline_desc'] = 'Mit den [u] Tag k�nnen Sie Texte unterstreichen.';
$lang['bbcodes_url'] = 'Internetadresse';
$lang['bbcodes_url_desc'] = 'Mit dem [url] Tag k�nnen g�ltige Internetadressen verlinken. Die erste Variante erfordert lediglich eine URL. Die eingegebene URL wird als Linktitel benutzt, jedoch bei �berl�nge gek�rzt. Die zweite Variante akzeptiert die URL als Option im [url] Tag. Wo in der ersten Variante die URL stand kann nun ein Linktitel eingegeben werden.';
$lang['bbcode_help_overview'] = '�bersicht';
$lang['bbcode_help_smileys'] = 'Smileys';
$lang['bbcode_help_smileys_desc'] = 'Folgende Smileys stehen auf dieser Seite zur Verf�gung:';
$lang['bbhelp_title'] = 'BB-Code Hilfe';
$lang['geshi_bbcode_nohighlighting'] = 'Kein Highlighting';
$lang['geshi_hlcode_title'] = '{$lang_name}-Quelltext:';
$lang['geshi_hlcode_txtdownload'] = 'Download';
$lang['more_smileys'] = 'mehr Smileys';
$lang['textarea_check_length'] = '�berpr�fe Textl�nge';
$lang['textarea_decrease_size'] = 'Verkleinern';
$lang['textarea_increase_size'] = 'Vergr��ern';
?>