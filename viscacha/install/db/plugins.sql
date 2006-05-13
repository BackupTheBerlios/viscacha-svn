CREATE TABLE `{:=DBPREFIX=:}plugins` (
  `id` int(10) unsigned NOT NULL,
  `name` varchar(255) NOT NULL default '',
  `module` varchar(255) NOT NULL,
  `ordering` smallint(4) NOT NULL default '0',
  `active` enum('0','1') NOT NULL default '0',
  `position` varchar(255) NOT NULL default 'navigation',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM PACK_KEYS=0 AUTO_INCREMENT=17 ;

INSERT INTO `{:=DBPREFIX=:}plugins` (`id`, `name`, `module`, `ordering`, `active`, `position`) VALUES (1, 'Hinweise zu dem neuen Forum', '5', 1, '1', 'portal');
INSERT INTO `{:=DBPREFIX=:}plugins` (`id`, `name`, `module`, `ordering`, `active`, `position`) VALUES (2, 'Aktuelle Nachrichten aus dem Forum', '6', 0, '1', 'portal');
INSERT INTO `{:=DBPREFIX=:}plugins` (`id`, `name`, `module`, `ordering`, `active`, `position`) VALUES (3, 'Die letzten aktiven Themen', '7', 3, '1', 'forum_end');
INSERT INTO `{:=DBPREFIX=:}plugins` (`id`, `name`, `module`, `ordering`, `active`, `position`) VALUES (4, 'Die letzten aktiven Themen', '7', 2, '1', 'portal');
INSERT INTO `{:=DBPREFIX=:}plugins` (`id`, `name`, `module`, `ordering`, `active`, `position`) VALUES (5, 'Druckausgabe für Word', '8', 0, '1', 'print_start');
INSERT INTO `{:=DBPREFIX=:}plugins` (`id`, `name`, `module`, `ordering`, `active`, `position`) VALUES (6, 'Letzte Antworten', '9', 0, '1', 'addreply_form_end');
INSERT INTO `{:=DBPREFIX=:}plugins` (`id`, `name`, `module`, `ordering`, `active`, `position`) VALUES (7, 'Verwandte Themen', '10', 0, '1', 'showtopic_end');
INSERT INTO `{:=DBPREFIX=:}plugins` (`id`, `name`, `module`, `ordering`, `active`, `position`) VALUES (8, 'Wer ist Online', '11', 2, '1', 'forum_end');
INSERT INTO `{:=DBPREFIX=:}plugins` (`id`, `name`, `module`, `ordering`, `active`, `position`) VALUES (9, 'Login-Box', '12', 0, '1', 'forum_end');
INSERT INTO `{:=DBPREFIX=:}plugins` (`id`, `name`, `module`, `ordering`, `active`, `position`) VALUES (10, 'Neue PNs', '13', 1, '1', 'forum_end');
INSERT INTO `{:=DBPREFIX=:}plugins` (`id`, `name`, `module`, `ordering`, `active`, `position`) VALUES (11, 'Legende (Forenübersicht)', '15', 4, '1', 'forum_end');
INSERT INTO `{:=DBPREFIX=:}plugins` (`id`, `name`, `module`, `ordering`, `active`, `position`) VALUES (12, 'Legende (Themenübersichten)', '16', 0, '1', 'showforum_end');
INSERT INTO `{:=DBPREFIX=:}plugins` (`id`, `name`, `module`, `ordering`, `active`, `position`) VALUES (13, 'Legende (Themenübersichten)', '16', 0, '1', 'search_result_end');
INSERT INTO `{:=DBPREFIX=:}plugins` (`id`, `name`, `module`, `ordering`, `active`, `position`) VALUES (14, 'Legende (Themenübersichten)', '16', 0, '1', 'search_active_end');
INSERT INTO `{:=DBPREFIX=:}plugins` (`id`, `name`, `module`, `ordering`, `active`, `position`) VALUES (15, 'Legende (PM)', '17', 0, '1', 'pm_browse_end');
INSERT INTO `{:=DBPREFIX=:}plugins` (`id`, `name`, `module`, `ordering`, `active`, `position`) VALUES (16, 'Legende (PM)', '17', 0, '1', 'pm_index_end');