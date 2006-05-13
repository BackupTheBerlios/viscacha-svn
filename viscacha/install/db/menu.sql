CREATE TABLE `{:=DBPREFIX=:}menu` (
  `id` int(10) unsigned NOT NULL,
  `name` varchar(255) NOT NULL default '',
  `link` varchar(255) NOT NULL default '',
  `param` varchar(64) NOT NULL default '',
  `groups` varchar(100) NOT NULL default '0',
  `ordering` smallint(4) NOT NULL default '0',
  `sub` int(10) NOT NULL default '0',
  `module` int(10) NOT NULL default '0',
  `active` enum('0','1') NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM PACK_KEYS=0 AUTO_INCREMENT=8 ;

INSERT INTO `{:=DBPREFIX=:}menu` (`id`, `name`, `link`, `param`, `groups`, `ordering`, `sub`, `module`, `active`) VALUES (1, 'Portal', 'portal.php', '', '0', 0, 3, 0, '1');
INSERT INTO `{:=DBPREFIX=:}menu` (`id`, `name`, `link`, `param`, `groups`, `ordering`, `sub`, `module`, `active`) VALUES (2, 'Forum', 'forum.php', '', '0', 1, 3, 0, '1');
INSERT INTO `{:=DBPREFIX=:}menu` (`id`, `name`, `link`, `param`, `groups`, `ordering`, `sub`, `module`, `active`) VALUES (3, 'Hauptmenü', '', '', '0', 0, 0, 0, '1');
INSERT INTO `{:=DBPREFIX=:}menu` (`id`, `name`, `link`, `param`, `groups`, `ordering`, `sub`, `module`, `active`) VALUES (4, 'Persönliche Box', '', '', '0', 1, 0, 17, '1');
INSERT INTO `{:=DBPREFIX=:}menu` (`id`, `name`, `link`, `param`, `groups`, `ordering`, `sub`, `module`, `active`) VALUES (5, 'Wir gratulieren...', '', '', '1,2,3,4', 3, 0, 18, '1');