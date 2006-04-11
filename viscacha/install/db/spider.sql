CREATE TABLE `{:=DBPREFIX=:}spider` (
  `id` mediumint(6) unsigned NOT NULL,
  `name` varchar(255) NOT NULL default '',
  `user_agent` text NOT NULL,
  `type` enum('b','e','v') NOT NULL default 'b',
  `last_visit` varchar(255) NOT NULL default '',
  `bot_visits` int(10) unsigned NOT NULL default '0',
  `pending_agent` text NOT NULL,
  `pending_ip` text NOT NULL,
  `bot_ip` text NOT NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=62;

-- 
-- Daten f�r Tabelle `v_spider`
-- 

INSERT INTO `{:=DBPREFIX=:}spider` (`id`, `name`, `user_agent`, `type`, `last_visit`, `bot_visits`, `pending_agent`, `pending_ip`, `bot_ip`) VALUES (39, 'W3C (x)HTML-Validator', 'W3C_Validator', 'v', '', 0, '', '', '');
INSERT INTO `{:=DBPREFIX=:}spider` (`id`, `name`, `user_agent`, `type`, `last_visit`, `bot_visits`, `pending_agent`, `pending_ip`, `bot_ip`) VALUES (9, 'Google', 'Googlebot', 'b', '', 0, '', '', '');
INSERT INTO `{:=DBPREFIX=:}spider` (`id`, `name`, `user_agent`, `type`, `last_visit`, `bot_visits`, `pending_agent`, `pending_ip`, `bot_ip`) VALUES (10, 'Google AdSense', 'Mediapartners-Google', 'b', '', 0, '', '', '');
INSERT INTO `{:=DBPREFIX=:}spider` (`id`, `name`, `user_agent`, `type`, `last_visit`, `bot_visits`, `pending_agent`, `pending_ip`, `bot_ip`) VALUES (11, 'Google Images', 'Googlebot-Image', 'b', '', 0, '', '', '');
INSERT INTO `{:=DBPREFIX=:}spider` (`id`, `name`, `user_agent`, `type`, `last_visit`, `bot_visits`, `pending_agent`, `pending_ip`, `bot_ip`) VALUES (12, 'Altavista', 'Scooter', 'b', '', 0, '', '', '');
INSERT INTO `{:=DBPREFIX=:}spider` (`id`, `name`, `user_agent`, `type`, `last_visit`, `bot_visits`, `pending_agent`, `pending_ip`, `bot_ip`) VALUES (16, 'Yahoo, Overture', 'Yahoo-MMCrawler', 'b', '', 0, '', '', '');
INSERT INTO `{:=DBPREFIX=:}spider` (`id`, `name`, `user_agent`, `type`, `last_visit`, `bot_visits`, `pending_agent`, `pending_ip`, `bot_ip`) VALUES (14, 'Overture', 'FAST-WebCrawler', 'b', '', 0, '', '', '');
INSERT INTO `{:=DBPREFIX=:}spider` (`id`, `name`, `user_agent`, `type`, `last_visit`, `bot_visits`, `pending_agent`, `pending_ip`, `bot_ip`) VALUES (15, 'MSN (Microsoft Network)', 'msnbot', 'b', '', 0, '', '', '');
INSERT INTO `{:=DBPREFIX=:}spider` (`id`, `name`, `user_agent`, `type`, `last_visit`, `bot_visits`, `pending_agent`, `pending_ip`, `bot_ip`) VALUES (49, 'Convera', 'ConveraCrawler', 'b', '', 0, '', '', '');
INSERT INTO `{:=DBPREFIX=:}spider` (`id`, `name`, `user_agent`, `type`, `last_visit`, `bot_visits`, `pending_agent`, `pending_ip`, `bot_ip`) VALUES (18, 'Excite', 'ArchitextSpider', 'b', '', 0, '', '', '');
INSERT INTO `{:=DBPREFIX=:}spider` (`id`, `name`, `user_agent`, `type`, `last_visit`, `bot_visits`, `pending_agent`, `pending_ip`, `bot_ip`) VALUES (19, 'Altavista', 'Mercator', 'b', '', 0, '', '', '');
INSERT INTO `{:=DBPREFIX=:}spider` (`id`, `name`, `user_agent`, `type`, `last_visit`, `bot_visits`, `pending_agent`, `pending_ip`, `bot_ip`) VALUES (20, 'Lycos', 'Lycos_Spider_(T-Rex)', 'b', '', 0, '', '', '');
INSERT INTO `{:=DBPREFIX=:}spider` (`id`, `name`, `user_agent`, `type`, `last_visit`, `bot_visits`, `pending_agent`, `pending_ip`, `bot_ip`) VALUES (21, 'Fireball', 'KIT-Fireball', 'b', '', 0, '', '', '');
INSERT INTO `{:=DBPREFIX=:}spider` (`id`, `name`, `user_agent`, `type`, `last_visit`, `bot_visits`, `pending_agent`, `pending_ip`, `bot_ip`) VALUES (22, 'Euroseek', 'Freecrawl', 'b', '', 0, '', '', '');
INSERT INTO `{:=DBPREFIX=:}spider` (`id`, `name`, `user_agent`, `type`, `last_visit`, `bot_visits`, `pending_agent`, `pending_ip`, `bot_ip`) VALUES (24, 'Aladin', 'Aladin', 'b', '', 0, '', '', '');
INSERT INTO `{:=DBPREFIX=:}spider` (`id`, `name`, `user_agent`, `type`, `last_visit`, `bot_visits`, `pending_agent`, `pending_ip`, `bot_ip`) VALUES (26, 'Northernlight', 'Gulliver', 'b', '', 0, '', '', '');
INSERT INTO `{:=DBPREFIX=:}spider` (`id`, `name`, `user_agent`, `type`, `last_visit`, `bot_visits`, `pending_agent`, `pending_ip`, `bot_ip`) VALUES (27, 'Google', 'BackRub', 'b', '', 0, '', '', '');
INSERT INTO `{:=DBPREFIX=:}spider` (`id`, `name`, `user_agent`, `type`, `last_visit`, `bot_visits`, `pending_agent`, `pending_ip`, `bot_ip`) VALUES (28, 'Abacho', 'AbachoBOT', 'b', '', 0, '', '', '');
INSERT INTO `{:=DBPREFIX=:}spider` (`id`, `name`, `user_agent`, `type`, `last_visit`, `bot_visits`, `pending_agent`, `pending_ip`, `bot_ip`) VALUES (29, 'Acoon', 'Acoon Robot', 'b', '', 0, '', '', '');
INSERT INTO `{:=DBPREFIX=:}spider` (`id`, `name`, `user_agent`, `type`, `last_visit`, `bot_visits`, `pending_agent`, `pending_ip`, `bot_ip`) VALUES (30, 'Alexa', 'ia_archiver', 'b', '', 0, '', '', '');
INSERT INTO `{:=DBPREFIX=:}spider` (`id`, `name`, `user_agent`, `type`, `last_visit`, `bot_visits`, `pending_agent`, `pending_ip`, `bot_ip`) VALUES (31, 'Turnitin', 'TurnitinBot', 'b', '', 0, '', '', '');
INSERT INTO `{:=DBPREFIX=:}spider` (`id`, `name`, `user_agent`, `type`, `last_visit`, `bot_visits`, `pending_agent`, `pending_ip`, `bot_ip`) VALUES (32, 'WebCollector', 'Mozilla/2.0 (compatible; NEWT ActiveX; Win32)', 'e', '', 0, '', '', '');
INSERT INTO `{:=DBPREFIX=:}spider` (`id`, `name`, `user_agent`, `type`, `last_visit`, `bot_visits`, `pending_agent`, `pending_ip`, `bot_ip`) VALUES (33, 'EmailCollector', 'EmailCollector', 'e', '', 0, '', '', '');
INSERT INTO `{:=DBPREFIX=:}spider` (`id`, `name`, `user_agent`, `type`, `last_visit`, `bot_visits`, `pending_agent`, `pending_ip`, `bot_ip`) VALUES (34, 'EmailSiphon', 'EmailSiphon', 'e', '', 0, '', '', '');
INSERT INTO `{:=DBPREFIX=:}spider` (`id`, `name`, `user_agent`, `type`, `last_visit`, `bot_visits`, `pending_agent`, `pending_ip`, `bot_ip`) VALUES (35, 'emailWolf', 'EmailWolf', 'e', '', 0, '', '', '');
INSERT INTO `{:=DBPREFIX=:}spider` (`id`, `name`, `user_agent`, `type`, `last_visit`, `bot_visits`, `pending_agent`, `pending_ip`, `bot_ip`) VALUES (36, 'ExtractorPro', 'ExtractorPro', 'e', '', 0, '', '', '');
INSERT INTO `{:=DBPREFIX=:}spider` (`id`, `name`, `user_agent`, `type`, `last_visit`, `bot_visits`, `pending_agent`, `pending_ip`, `bot_ip`) VALUES (37, 'CherryPicker', 'CherryPicker', 'e', '', 0, '', '', '');
INSERT INTO `{:=DBPREFIX=:}spider` (`id`, `name`, `user_agent`, `type`, `last_visit`, `bot_visits`, `pending_agent`, `pending_ip`, `bot_ip`) VALUES (38, 'ExtractorPro, WebWeasel, Web Mole', 'Crescent Internet ToolPak HTTP OLE Control', 'e', '', 0, '', '', '');
INSERT INTO `{:=DBPREFIX=:}spider` (`id`, `name`, `user_agent`, `type`, `last_visit`, `bot_visits`, `pending_agent`, `pending_ip`, `bot_ip`) VALUES (40, 'W3C CSS-Validator', 'W3C_CSS_Validator_JFouffa', 'v', '', 0, '', '', '128.30.52.34');
INSERT INTO `{:=DBPREFIX=:}spider` (`id`, `name`, `user_agent`, `type`, `last_visit`, `bot_visits`, `pending_agent`, `pending_ip`, `bot_ip`) VALUES (41, 'FEED Validator', 'FeedValidator', 'v', '', 0, '', '', '');
INSERT INTO `{:=DBPREFIX=:}spider` (`id`, `name`, `user_agent`, `type`, `last_visit`, `bot_visits`, `pending_agent`, `pending_ip`, `bot_ip`) VALUES (42, 'GlobalSpec', 'Ocelli', 'b', '', 0, '', '', '');
INSERT INTO `{:=DBPREFIX=:}spider` (`id`, `name`, `user_agent`, `type`, `last_visit`, `bot_visits`, `pending_agent`, `pending_ip`, `bot_ip`) VALUES (43, 'Gigablast', 'Gigabot', 'b', '', 0, '', '', '');
INSERT INTO `{:=DBPREFIX=:}spider` (`id`, `name`, `user_agent`, `type`, `last_visit`, `bot_visits`, `pending_agent`, `pending_ip`, `bot_ip`) VALUES (44, 'Objects Search', 'ObjectsSearch', 'b', '', 0, '', '', '');
INSERT INTO `{:=DBPREFIX=:}spider` (`id`, `name`, `user_agent`, `type`, `last_visit`, `bot_visits`, `pending_agent`, `pending_ip`, `bot_ip`) VALUES (45, 'WiseNut', 'ZyBorg', 'b', '', 0, '', '', '');
INSERT INTO `{:=DBPREFIX=:}spider` (`id`, `name`, `user_agent`, `type`, `last_visit`, `bot_visits`, `pending_agent`, `pending_ip`, `bot_ip`) VALUES (47, 'Fast Search & Transfer', 'FAST Enterprise Crawler', 'b', '', 0, '', '', '');
INSERT INTO `{:=DBPREFIX=:}spider` (`id`, `name`, `user_agent`, `type`, `last_visit`, `bot_visits`, `pending_agent`, `pending_ip`, `bot_ip`) VALUES (50, 'Grub', 'grub-client', 'b', '', 0, '', '', '');
INSERT INTO `{:=DBPREFIX=:}spider` (`id`, `name`, `user_agent`, `type`, `last_visit`, `bot_visits`, `pending_agent`, `pending_ip`, `bot_ip`) VALUES (51, 'Baidu', 'Baiduspider', 'b', '', 0, '', '', '');
INSERT INTO `{:=DBPREFIX=:}spider` (`id`, `name`, `user_agent`, `type`, `last_visit`, `bot_visits`, `pending_agent`, `pending_ip`, `bot_ip`) VALUES (52, 'NextLinks', 'findlinks/', 'b', '', 0, '', '', '');
INSERT INTO `{:=DBPREFIX=:}spider` (`id`, `name`, `user_agent`, `type`, `last_visit`, `bot_visits`, `pending_agent`, `pending_ip`, `bot_ip`) VALUES (53, 'OmniExplorer Internet Categorizer', 'OmniExplorer_Bot', 'b', '', 0, '', '', '');
INSERT INTO `{:=DBPREFIX=:}spider` (`id`, `name`, `user_agent`, `type`, `last_visit`, `bot_visits`, `pending_agent`, `pending_ip`, `bot_ip`) VALUES (54, 'Sohu', 'sohu-search', 'b', '', 0, '', '', '');
INSERT INTO `{:=DBPREFIX=:}spider` (`id`, `name`, `user_agent`, `type`, `last_visit`, `bot_visits`, `pending_agent`, `pending_ip`, `bot_ip`) VALUES (55, 'Twiceler', 'Twiceler', 'b', '', 0, '', '', '');
INSERT INTO `{:=DBPREFIX=:}spider` (`id`, `name`, `user_agent`, `type`, `last_visit`, `bot_visits`, `pending_agent`, `pending_ip`, `bot_ip`) VALUES (61, 'Yahoo', 'Yahoo! Slurp', 'b', '', 0, '', '', '');
INSERT INTO `{:=DBPREFIX=:}spider` (`id`, `name`, `user_agent`, `type`, `last_visit`, `bot_visits`, `pending_agent`, `pending_ip`, `bot_ip`) VALUES (60, 'IRL Crawler', 'IRLbot', 'b', '', 0, '', '', '');