CREATE TABLE `{:=DBPREFIX=:}categories` (
  `id` smallint(5) unsigned NOT NULL auto_increment,
  `name` text NOT NULL,
  `description` text NOT NULL,
  `parent` smallint(5) unsigned NOT NULL default '0',
  `position` smallint(4) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;