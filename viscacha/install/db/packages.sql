CREATE TABLE `{:=DBPREFIX=:}packages` (
  `id` mediumint(7) unsigned NOT NULL,
  `title` varchar(200) NOT NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=18 ;

-- 
-- Daten für Tabelle `v_packages`
-- 

INSERT INTO `{:=DBPREFIX=:}packages` (`id`, `title`) VALUES 
(1, 'Persönliche Box'),
(2, 'Newsfeed-Ticker'),
(3, 'Birthday-Reminder'),
(5, 'Nachrichten-Box'),
(6, 'News-Boxen'),
(7, 'Letzte-Themen-Box'),
(8, 'MS Word Druckansicht'),
(9, 'Letzte-Antworten-Box'),
(10, 'Verwandte Themen'),
(11, 'Wer-ist-Online-Box'),
(12, 'Login-Box'),
(13, 'Neue-PN-Box'),
(15, 'Legende (Foren)'),
(16, 'Legende (Themen)'),
(17, 'Legende (PM)');