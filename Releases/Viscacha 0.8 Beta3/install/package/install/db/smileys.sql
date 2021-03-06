CREATE TABLE `{:=DBPREFIX=:}smileys` (
  `id` smallint(5) unsigned NOT NULL auto_increment,
  `search` varchar(255) NOT NULL default '',
  `replace` text NOT NULL,
  `desc` text NOT NULL,
  `show` enum('0','1') NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=32 ;

INSERT INTO `{:=DBPREFIX=:}smileys` (`id`, `search`, `replace`, `desc`, `show`) VALUES (1, ':D', '{folder}/biggrin.gif', 'Grinsender Smiley', '1'),
(2, ':}', '{folder}/cheesy.gif', 'Glücklicher Smiley', '1'),
(3, '8-)', '{folder}/cool.gif', 'Cooler Smiley', '0'),
(4, ':eek:', '{folder}/eek.gif', 'Erstaunter Smiley', '1'),
(5, ':(', '{folder}/frown.gif', 'Entäuschter Smiley', '1'),
(6, ':lol:', '{folder}/lol.gif', 'Lachender Smiley', '1'),
(7, ':love:', '{folder}/love.gif', 'Liebender Smiley', '0'),
(8, ':{', '{folder}/mad.gif', 'Böser Smiley', '0'),
(9, ':x', '{folder}/nocomment.gif', 'Kein-Kommentar-Smiley', '0'),
(10, ':o', '{folder}/redface.gif', 'Peinlicher Smiley', '0'),
(11, ':shy:', '{folder}/rotwerd.gif', 'Schüchterner Smiley', '0'),
(12, ':)', '{folder}/smile.gif', 'Glücklicher Smiley', '1'),
(13, ':p', '{folder}/tongue.gif', 'Zungestreckender Smiley', '1'),
(14, ':\\', '{folder}/undecided.gif', 'Unentschlossener Smiley', '0'),
(15, '8)', '{folder}/warsnet.gif', 'Unschuldiger Smiley', '0'),
(16, ';)', '{folder}/wink.gif', 'Zwinkernder Smiley', '1'),
(17, ':crazy:', '{folder}/crazy.gif', 'Verrückter Smiley', '0'),
(18, ':''(', '{folder}/sad.gif', 'Trauriger Smiley', '0'),
(19, ':]', '{folder}/ego.gif', 'Egoistischer Smiley', '0'),
(20, ':help:', '{folder}/help.gif', 'Hilfloser Smiley', '0'),
(21, ':angel:', '{folder}/engel.gif', 'Engel Smiley', '0'),
(22, '(??)', '{folder}/question.gif', 'Frage', '0'),
(23, '(!!)', '{folder}/attention.gif', 'Achtung', '0'),
(24, '(ii)', '{folder}/info.gif', 'Information', '0'),
(25, ':mail:', '{folder}/mail.gif', 'E-Mail', '0'),
(26, ':search:', '{folder}/search.gif', 'Suche', '0'),
(27, ':no:', '{folder}/no.gif', 'Negativ / Nein', '0'),
(28, ':idea:', '{folder}/idea.gif', 'Geistesblitz / Idee', '0'),
(29, ':heart:', '{folder}/heart.gif', 'Herz / Liebe', '0'),
(30, '8-|', '{folder}/www.gif', 'Umsehen', '0'),
(31, ':yes:', '{folder}/yes.gif', 'Positiv / Ja', '0');