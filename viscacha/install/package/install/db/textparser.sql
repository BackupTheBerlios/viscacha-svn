CREATE TABLE `{:=DBPREFIX=:}textparser` (
  `id` smallint(5) unsigned NOT NULL auto_increment,
  `search` tinytext NOT NULL default '',
  `replace` tinytext NOT NULL default '',
  `type` enum('censor','word','replace') NOT NULL default 'word',
  `desc` tinytext NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM PACK_KEYS=1 AUTO_INCREMENT=1 ;