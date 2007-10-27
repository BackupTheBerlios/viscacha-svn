CREATE TABLE `{:=DBPREFIX=:}menu` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `link` varchar(255) NOT NULL default '',
  `param` varchar(64) NOT NULL default '',
  `groups` varchar(100) NOT NULL default '0',
  `ordering` smallint(4) NOT NULL default '0',
  `sub` int(10) NOT NULL default '0',
  `module` int(10) NOT NULL default '0',
  `active` enum('0','1') NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM  PACK_KEYS=0 AUTO_INCREMENT=5 ;

INSERT INTO `v_menu` (`id`, `name`, `link`, `param`, `groups`, `ordering`, `sub`, `module`, `active`) VALUES 
(1, 'Navigation', '', '', '0', 0, 0, 0, '1'),
(2, 'Portal', 'portal.php', '', '0', -1, 70, 0, '1'),
(3, 'Forum', 'forum.php', '', '0', 0, 70, 0, '1'),
(4, 'Personal Box', '', '', '0', 0, 0, 15, '1');
