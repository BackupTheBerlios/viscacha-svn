CREATE TABLE `{:=DBPREFIX=:}filetypes` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `extension` varchar(200) NOT NULL default '',
  `program` tinytext NOT NULL,
  `desctxt` text NOT NULL,
  `icon` varchar(100) NOT NULL default '',
  `mimetype` varchar(100) NOT NULL default 'application/octet-stream',
  `stream` enum('inline','attachment') NOT NULL default 'attachment',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM  AUTO_INCREMENT=41 ;

INSERT INTO `{:=DBPREFIX=:}filetypes` (`id`, `extension`, `program`, `desctxt`, `icon`, `mimetype`, `stream`) VALUES (2, 'gif', '', 'Abk�rzung f�r Graphics Interchange Format. Ein Format f�r Grafikdateien, das von CompuServe entwickelt wurde. Dieses Format wird f�r die �bertragung von Rasterbildern im Internet verwendet. Ein Bild kann bis zu 256 Farben (einschlie�lich einer transparenten Farbe) enthalten. Die Dateigr��e h�ngt von der Anzahl der Farben ab, die tats�chlich verwendet werden. Die Komprimierungsmethode LZW wird verwendet, um die Dateigr��e weiter zu verringern.', 'gif', 'image/gif', 'inline'),
(3, 'ps', 'Adobe Reader, PS View', '', 'pdf', 'application/postscript', 'attachment'),
(4, 'png', '', 'Ein Dateiformat f�r Bitmapgrafiken, das das GIF-Format ersetzen soll. Bei der Verwendung von PNG in Programmen und auf Websites bestehen keine rechtlichen Einschr�nkungen, im Gegensatz zum GIF-Format.<br>\r\nDie Website f�r das PNG-Format ist unter der Adresse <a href="http://www.libpng.org/pub/png/" target="_blank">http://www.libpng.org/pub/png</a> erreichbar.', 'png', 'image/png', 'inline'),
(5, 'eps', '', '', 'eps', 'application/postscript', 'attachment'),
(27, 'tar', 'WinRar', '', 'compressed', 'application/x-tar', 'attachment'),
(24, 'swf', 'Flash-Player', '', 'swf', 'application/x-shockwave-flash', 'inline'),
(25, 'txt', 'Text-Editor', '', 'txt', 'text/plain', 'inline'),
(26, 'gz', 'WinRar', '', 'compressed', 'application/x-gzip', 'attachment'),
(8, 'wav', 'Windows Media Player, WinAmp und Musicmatch', 'Das WAV-Dateiformat dient zur digitalen Speicherung von Audiodaten. Es ist heute der Standard f�r die Verarbeitung von digitalen Audiodaten. Die Audiodaten im WAV-Format werden meist als unkomprimierte Rohdaten gespeichert. WAV ist ein Containerformat, das auch komprimierte Audiodaten enthalten kann. Das WAV-Format (eigentlich RIFF WAVE) h�lt sich an das von Microsoft definierte \\"Resource Interchange File Format\\" (RIFF).', 'sound', 'audio/x-wav', 'attachment'),
(28, 'exe', '', 'Ausf�hrbare Windows-Datei.', 'exe', 'application/octet-stream', 'attachment'),
(29, 'ppt,ppz,pps,pot', 'Microsoft Powerpoint', 'Microsoft PowerPoint ist ein Computerprogramm, mit dem sich interaktive Folienpr�sentationen erstellen lassen. ', 'ppt', 'application/vnd.ms-powerpoint', 'attachment'),
(30, 'doc,dot', 'Microsoft Word, StarOffice, OpenOffice', '', 'doc', 'application/msword', 'attachment'),
(31, 'js', '', '', 'js', 'text/javascript', 'attachment'),
(32, 'vcf', 'Adressbuch, Outlook', '', 'vcf', 'text/x-vcard', 'attachment'),
(33, 'chm,hlp', '', '', 'chm', 'application/winhlp', 'attachment'),
(9, 'mp3', 'WinAmp, Windows Media Player, Musicmatch', 'MP3, eigentlich MPEG-1 Audio Layer 3, ist ein Dateiformat zur verlustbehafteten Audiokompression.', 'sound', 'audio/mpeg', 'attachment'),
(11, 'ogg', 'WinAmp', 'Ogg ist ein Open-Source-Projekt f�r die professionelle Speicherung und Wiedergabe von Multimediadaten. Im Unterschied zu z.B. MP3, WMA oder ATRAC ist Ogg (nach Angaben seiner Entwickler) patentfrei und unterliegt einer BSD-artigen Lizenz. Somit ist gew�hrleistet, dass das Format auch f�r kommerzielle, wie auch nicht-kommerzielle Programme ohne die Zahlung von Lizenzgeb�hren verwendet werden darf. Bei Ogg handelt es sich um ein Containerformat - das hei�t, dass die verschiedensten Inhalte (Audio, Video, Steuerbefehle) in einer Datei gespeichert werden k�nnen.', 'sound', 'application/octet-stream', 'attachment'),
(12, 'wma', 'Windows Media Player', 'Windows Media Audio (WMA) ist ein Audio-Codec von Microsoft. Der WMA Codec, ein verlustbehafteter Codec, unterst�tzt in seiner "Professional"-Variante bis zu 24 bit/96 KHz bei einer variablen Bitrate von bis zu 768 kb/s und Surround-Ton mit bis zu 7.1 Kan�len. Daneben gibt es eine Version, die speziell auf Quellmaterial, das Stimmaufnahmen enth�lt, abgestimmt ist, sowie den verlustfreien Codec Windows Media Audio Lossless.', 'sound', 'application/octet-stream', 'attachment'),
(13, 'midi,mid', 'WinAmp, YAMP', 'MIDI ist ein Daten�bertragungs-Protokoll f�r die �bermittlung, Aufzeichnung und Wiedergabe von musikalischen Steuerinformationen zwischen digitalen Instrumenten oder einem PC. Das Protokoll wurde zu Beginn der 1980er ma�geblich von Sequential Circuits und Roland entwickelt.', 'sound', 'audio/mid', 'attachment'),
(14, 'html,htm', 'Firefox, Opera, Safari, Internet Explorer, Konqueror', 'Die Hypertext Markup Language (HTML) ist ein Dokumentenformat zur Auszeichnung von Hypertext im World Wide Web und wurde 1989 von Tim Berners-Lee am CERN in Genf festgelegt. Sie basiert dabei auf der Metasprache SGML, die zur Definition von Auszeichnungssprachen verwendet wird. ', 'html', 'text/html', 'inline'),
(15, 'rtf', 'Open Office, Microsoft Word, Star Office, AbiWord', 'Das Rich Text Format (RTF) ist ein Dateiformat f�r Texte, das von Microsoft eingef�hrt wurde und zum Datenaustausch zwischen Textverarbeitungsprogrammen verschiedener Hersteller dient.', 'rtf', 'application/rtf', 'attachment'),
(16, 'xml', 'Open XML Editor, KXML Editor, Eclipse, Java Simple XML Editor, Epic, OpenOffice.org', 'Die Extensible Markup Language, abgek�rzt XML, ist ein Standard zur Erstellung maschinen- und menschenlesbarer Dokumente in Form einer Baumstruktur.', 'xml', 'text/xml', 'attachment'),
(17, 'jpg,jpeg,jpe', 'Ann�hernd jedes Grafikprogramm', 'JPEG, kurz JPG ist eines der am weitesten verbreiteten Format zur komprimierenden, digitalen Speicherung von Bildern.', 'jpg', 'image/jpeg', 'inline'),
(18, 'psd', 'Adobe Photoshop, IrfanView, ACDSee, Paint Shop Pro ', 'Photoshop Document ist das native Dateiformat von Adobe Photoshop.\r\n\r\nS�mtliche verwendeten Bilddateien werden verlustfrei gespeichert, ebenso wie Informationen �ber die verschiedenen Ebenen, Kan�le und Vektoren und Einstellungen des Projektes. Die Dateien sind im Vergleich zu anderen Formaten auch dementsprechend gro�.', 'psd', 'application/octet-stream', 'attachment'),
(37, 'mov', 'Quicktime', '', 'video', 'video/quicktime', 'attachment'),
(38, 'xls,xlt,xlw,xlm,xlc,', 'Excel', '', 'xls', 'application/vnd.ms-excel', 'attachment'),
(19, 'tiff,tif', '', 'TIFF (engl. Tagged Image File Format) ist ein Dateiformat zur Speicherung von Bilddaten. Es ist einies der wichtigsten Formate zum Austausch von Daten in der Druckvorstufe.', 'image', 'image/tiff', 'attachment'),
(34, 'bmp,wbmp,dib', '', '', 'bmp', 'image/bmp', 'inline'),
(35, 'avi', 'Windows Media Player', '', 'video', 'video/x-msvideo', 'attachment'),
(36, 'mpeg,mpg,mpa,mpe,mp2', 'Windows Media Player, Quicktime', '', 'video', 'video/mpeg', 'attachment'),
(20, 'pdf', 'Adobe Acrobat', 'Das Portable Document Format (PDF) ist ein Dateiformat, das von Adobe Systems entwickelt und 1993 mit Acrobat 1 ver�ffentlicht wurde. PDF-Dateien geben das mit dem Erstellungsprogramm erzeugte Layout 1:1 wieder.\r\n\r\nPDF ist ein propriet�res ("hauseigenes"), aber offenes Dateiformat, das im PDF Reference Manual von Adobe dokumentiert ist und dadurch Drittentwicklern umfangreiche PDF-Werkzeuge bereitstellt. PDF basiert zu gro�en Teilen auf dem PostScript-Format, das ebenfalls offen ist.', 'pdf', 'application/pdf', 'inline'),
(21, 'css', 'Text-Editor', 'Cascading Style Sheets (CSS) ist eine deklarative Stylesheet-Sprache f�r strukturierte Dokumente (z.B. HTML und XML). Durch die Trennung von Stil und Inhalt wird das Ver�ffentlichen und Betreuen von Webseiten wesentlich vereinfacht.', 'css', 'text/css', 'attachment'),
(22, 'rar', 'WinRAR', 'RAR ist ein Algorithmus und Dateiformat zur Datenkompression, um den Speicherbedarf von Dateien f�r die Archivierung und �bertragung zu verringern.', 'rar', 'application/x-rar-compressed', 'attachment'),
(23, 'zip', 'WinZIP, 7-Zip, WinRAR, Power-Archivar, PKZIP', 'Das ZIP-Dateiformat ist ein Format zur komprimierten Archivierung von Dateien.', 'compressed', 'application/zip', 'attachment'),
(40, 'php,phpx,php5,php4,php3', 'PHP, Zend', 'PHP ist eine Skriptsprache mit einer an C bzw. Perl angelehnten Syntax, die haupts�chlich zur Erstellung dynamischer Webseiten verwendet wird. Bei PHP handelt es sich um Open-Source-Software.', 'php', 'text/plain', 'inline');