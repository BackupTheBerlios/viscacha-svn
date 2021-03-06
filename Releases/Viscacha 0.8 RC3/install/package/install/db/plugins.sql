CREATE TABLE `{:=DBPREFIX=:}plugins` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(200) NOT NULL default '',
  `module` mediumint(7) unsigned NOT NULL default '0',
  `ordering` smallint(4) NOT NULL default '0',
  `active` enum('0','1') NOT NULL default '1',
  `position` varchar(128) NOT NULL default 'navigation',
  `required` enum('0','1') NOT NULL default '1',
  PRIMARY KEY  (`id`),
  KEY `position` (`position`)
) TYPE=MyISAM  PACK_KEYS=0 AUTO_INCREMENT=22 ;

INSERT INTO `{:=DBPREFIX=:}plugins` (`id`, `name`, `module`, `ordering`, `active`, `position`, `required`) VALUES
(1, 'Related Topics', 1, 2, '1', 'showtopic_end', '0'),
(2, 'Who is online Box', 2, 1, '1', 'forum_end', '0'),
(3, 'Who is online Data Preparation', 2, 1, '1', 'frontend_init', '1'),
(4, 'Who is online for Navigation', 2, 1, '1', 'navigation', '0'),
(5, 'News Boxes', 3, 1, '1', 'portal', '0'),
(6, 'Last replies', 4, 1, '1', 'addreply_form_end', '0'),
(7, 'Last reply for Private Messages', 4, 1, '1', 'pm_compose_end', '0'),
(8, 'Legend for PMs', 5, 1, '1', 'pm_browse_end', '0'),
(9, 'Legend for PMs', 5, 1, '1', 'pm_index_end', '0'),
(10, 'Legend for Topics', 5, 1, '1', 'showforum_end', '0'),
(11, 'Legend for Topics', 5, 1, '1', 'search_result_end', '0'),
(12, 'Legend for Topics', 5, 1, '1', 'search_active_end', '0'),
(13, 'Legend for Forums', 5, 3, '1', 'forum_end', '0'),
(14, 'Legend for Topics', 5, 1, '1', 'editprofile_mylast_end', '0'),
(15, 'Personal Box for Navigation', 6, 2, '1', 'navigation', '0'),
(16, 'Login Box', 6, 1, '1', 'forum_start', '0'),
(17, 'Quick Reply Form', 7, 1, '1', 'showtopic_end', '0'),
(18, 'Quick Reply Extended Switcher', 7, 1, '1', 'addreply_save_errorhandling', '1'),
(19, 'Recent Topics', 8, 2, '1', 'portal', '0'),
(20, 'Recent Topics', 8, 2, '1', 'forum_end', '0'),
(21, 'PM Notification', 6, 0, '1', 'template_forum_menu', '0');
