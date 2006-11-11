CREATE TABLE `{:=DBPREFIX=:}session` (
  `mid` mediumint(7) unsigned NOT NULL default '0',
  `active` int(10) unsigned NOT NULL default '0',
  `wiw_script` varchar(50) NOT NULL default '',
  `wiw_action` varchar(50) NOT NULL default '',
  `wiw_id` int(10) unsigned default NULL,
  `ip` varchar(16) NOT NULL default '',
  `user_agent` text NOT NULL default '',
  `lastvisit` int(10) unsigned NOT NULL default '0',
  `mark` longtext NOT NULL default '',
  `sid` varchar(128) NOT NULL default '',
  `is_bot` mediumint(6) unsigned NOT NULL default '0',
  `pwfaccess` tinytext NOT NULL default 'a:0:{}',
  `settings` tinytext NOT NULL default 'a:0:{}',
  KEY `mid` (`mid`),
  KEY `sid` (`sid`)
) TYPE=MyISAM;