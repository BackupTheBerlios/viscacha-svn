CREATE TABLE `{:=DBPREFIX=:}settings` (
  `name` varchar(120) NOT NULL default '',
  `title` varchar(150) NOT NULL default '',
  `description` text NOT NULL,
  `type` enum('text','textarea','select','checkbox') NOT NULL default 'text',
  `optionscode` text NOT NULL,
  `value` text NOT NULL,
  `sgroup` smallint(4) unsigned NOT NULL,
  UNIQUE KEY `name` (`name`)
) TYPE=MyISAM;

INSERT INTO `{:=DBPREFIX=:}settings` (`name`, `title`, `description`, `type`, `optionscode`, `value`, `sgroup`) VALUES ('text', 'Text of Box', '', 'textarea', '', 'Willkommen!', 3);
INSERT INTO `{:=DBPREFIX=:}settings` (`name`, `title`, `description`, `type`, `optionscode`, `value`, `sgroup`) VALUES ('title', 'Title of Box', '', 'text', '', '', 3);
INSERT INTO `{:=DBPREFIX=:}settings` (`name`, `title`, `description`, `type`, `optionscode`, `value`, `sgroup`) VALUES ('teaserlength', 'Kürzen', '', 'textarea', '', '300', 4);
INSERT INTO `{:=DBPREFIX=:}settings` (`name`, `title`, `description`, `type`, `optionscode`, `value`, `sgroup`) VALUES ('items', 'Items', '', 'text', '', '5', 4);
INSERT INTO `{:=DBPREFIX=:}settings` (`name`, `title`, `description`, `type`, `optionscode`, `value`, `sgroup`) VALUES ('topicnum', 'Themen', '', 'text', '', '10', 5);
INSERT INTO `{:=DBPREFIX=:}settings` (`name`, `title`, `description`, `type`, `optionscode`, `value`, `sgroup`) VALUES ('relatednum', 'Max. relevante Themen', '', 'text', '', '5', 6);
INSERT INTO `{:=DBPREFIX=:}settings` (`name`, `title`, `description`, `type`, `optionscode`, `value`, `sgroup`) VALUES ('repliesnum', 'Max. Antworten', '', 'text', '', '5', 7);