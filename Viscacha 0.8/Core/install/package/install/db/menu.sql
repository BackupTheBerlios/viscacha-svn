CREATE TABLE `{:=DBPREFIX=:}menu` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `link` varchar(255) NOT NULL default '',
  `param` varchar(64) NOT NULL default '',
  `groups` varchar(100) NOT NULL default '0',
  `position` varchar(10) NOT NULL default 'left',
  `ordering` smallint(4) NOT NULL default '0',
  `sub` int(10) NOT NULL default '0',
  `module` int(10) NOT NULL default '0',
  `active` enum('0','1') NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  PACK_KEYS=0 AUTO_INCREMENT=7 ;

INSERT INTO `{:=DBPREFIX=:}menu` (`id`, `name`, `link`, `param`, `groups`, `position`, `ordering`, `sub`, `module`, `active`) VALUES
(1, 'lang->navigation', '', '', '0', 'left', 0, 0, 0, '1'),
(2, 'lang->n_portal', 'portal.php', '', '0', 'left', -1, 1, 0, '1'),
(3, 'lang->n_forum', 'forum.php', '', '0', 'left', 0, 1, 0, '1'),
(4, 'Personal Box', '', '', '0', 'left', 0, 0, 15, '1'),
(5, 'Documents', '', '', '0', 'bottom', 0, 0, 0, '1'),
(6, 'doc->1', 'docs.php?id=1', '', '0', 'bottom', -1, 5, 0, '1');