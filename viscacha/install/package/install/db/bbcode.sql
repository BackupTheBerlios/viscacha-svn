CREATE TABLE `{:=DBPREFIX=:}bbcode` (
  `id` smallint(5) unsigned NOT NULL auto_increment,
  `bbcodetag` varchar(200) NOT NULL default '',
  `bbcodereplacement` mediumtext NOT NULL default '',
  `bbcodeexample` varchar(255) NOT NULL default '',
  `bbcodeexplanation` mediumtext NOT NULL default '',
  `twoparams` enum('0','1') NOT NULL default '0',
  `title` varchar(200) NOT NULL default '',
  `buttonimage` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `uniquetag` (`bbcodetag`,`twoparams`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;