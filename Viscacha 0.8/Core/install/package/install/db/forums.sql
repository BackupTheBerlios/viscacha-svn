CREATE TABLE `{:=DBPREFIX=:}forums` (
  `id` smallint(5) unsigned NOT NULL auto_increment,
  `name` text NOT NULL,
  `description` text NOT NULL,
  `topics` int(10) unsigned NOT NULL default '0',
  `replies` int(10) unsigned NOT NULL default '0',
  `parent` smallint(5) unsigned NOT NULL default '0',
  `position` smallint(4) NOT NULL default '0',
  `last_topic` int(10) unsigned NOT NULL default '0',
  `count_posts` enum('0','1') NOT NULL default '1',
  `opt` enum('','re','pw') NOT NULL default '',
  `optvalue` varchar(255) NOT NULL default '',
  `forumzahl` tinyint(3) unsigned NOT NULL default '0',
  `topiczahl` tinyint(3) unsigned NOT NULL default '0',
  `prefix` enum('0','1') NOT NULL default '0',
  `invisible` enum('0','1','2') NOT NULL default '0',
  `readonly` enum('0','1') NOT NULL default '0',
  `auto_status` enum('','a','n') NOT NULL default '',
  `reply_notification` text NOT NULL,
  `topic_notification` text NOT NULL,
  `active_topic` enum('0','1') NOT NULL default '1',
  `message_active` enum('0','1','2') NOT NULL default '0',
  `message_title` text NOT NULL,
  `message_text` text NOT NULL,
  `lid` smallint(4) unsigned NOT NULL default '0',
  `post_order` enum('-1','0','1') NOT NULL DEFAULT '-1',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;