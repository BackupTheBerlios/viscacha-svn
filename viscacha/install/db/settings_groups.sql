CREATE TABLE `{:=DBPREFIX=:}settings_groups` (
  `id` smallint(4) unsigned NOT NULL,
  `title` varchar(120) NOT NULL,
  `name` varchar(120) NOT NULL,
  `description` tinytext NOT NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=8 ;

INSERT INTO `{:=DBPREFIX=:}settings_groups` (`id`, `title`, `name`, `description`) VALUES (3, 'Nachrichten-Box', 'module_5', '');
INSERT INTO `{:=DBPREFIX=:}settings_groups` (`id`, `title`, `name`, `description`) VALUES (4, 'News auf dem Portal', 'module_6', '');
INSERT INTO `{:=DBPREFIX=:}settings_groups` (`id`, `title`, `name`, `description`) VALUES (5, 'Letzte Themen', 'module_7', '');
INSERT INTO `{:=DBPREFIX=:}settings_groups` (`id`, `title`, `name`, `description`) VALUES (6, 'Verwandte Themen', 'module_10', '');
INSERT INTO `{:=DBPREFIX=:}settings_groups` (`id`, `title`, `name`, `description`) VALUES (7, 'Letzte Antworten', 'module_9', '');