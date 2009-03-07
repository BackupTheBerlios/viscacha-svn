CREATE TABLE `{:=DBPREFIX=:}user` (
  `id` mediumint(7) unsigned NOT NULL auto_increment,
  `name` varchar(128) NOT NULL default '',
  `pw` varchar(32) NOT NULL default '',
  `mail` varchar(200) NOT NULL default '',
  `regdate` int(10) unsigned NOT NULL default '0',
  `posts` mediumint(7) unsigned NOT NULL default '0',
  `fullname` varchar(200) NOT NULL default '',
  `hp` varchar(255) NOT NULL default '',
  `signature` text NOT NULL,
  `about` mediumtext NOT NULL,
  `notice` longtext NOT NULL,
  `location` varchar(100) NOT NULL default '',
  `gender` enum('','m','w') NOT NULL default '',
  `birthday` date NOT NULL default '0000-00-00',
  `pic` varchar(255) NOT NULL default '',
  `lastvisit` int(10) unsigned NOT NULL default '0',
  `icq` bigint(16) unsigned NOT NULL default '0',
  `yahoo` varchar(128) NOT NULL default '',
  `aol` varchar(128) NOT NULL default '',
  `msn` varchar(128) NOT NULL default '',
  `jabber` varchar(128) NOT NULL default '',
  `skype` varchar(128) NOT NULL default '',
  `timezone` varchar(5) default '',
  `groups` varchar(128) NOT NULL default '',
  `opt_textarea` tinyint(1) unsigned NOT NULL default '0',
  `opt_pmnotify` enum('0','1') NOT NULL default '1',
  `opt_hidebad` enum('0','1') NOT NULL default '0',
  `opt_hidemail` enum('0','1','2') NOT NULL default '2',
  `opt_newsletter` enum('0','1','2') NOT NULL default '2',
  `opt_showsig` enum('0','1') NOT NULL default '1',
  `template` smallint(5) unsigned NOT NULL default '0',
  `language` tinyint(3) unsigned NOT NULL default '0',
  `confirm` enum('00','01','10','11') NOT NULL default '00',
  PRIMARY KEY  (`id`),
  KEY `name` (`name`),
  KEY `pw` (`pw`)
) TYPE=MyISAM PACK_KEYS=0 AUTO_INCREMENT=1 ;