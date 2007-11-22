ALTER TABLE `{:=DBPREFIX=:}component` MODIFY COLUMN `file` varchar(127) NOT NULL DEFAULT '' AFTER `id`;
ALTER TABLE `{:=DBPREFIX=:}component` ADD COLUMN `package` mediumint(7) unsigned NOT NULL AFTER `file`;
ALTER TABLE `{:=DBPREFIX=:}component` MODIFY COLUMN `active` enum('0','1') NOT NULL DEFAULT '1' AFTER `package`;
ALTER TABLE `{:=DBPREFIX=:}component` ADD COLUMN `required` enum('0','1') NOT NULL DEFAULT '1' AFTER `active`;

ALTER TABLE `{:=DBPREFIX=:}filetypes` MODIFY COLUMN `extension` varchar(200) NOT NULL DEFAULT '' AFTER `id`;
ALTER TABLE `{:=DBPREFIX=:}filetypes` MODIFY COLUMN `icon` varchar(100) NOT NULL DEFAULT '' AFTER `desctxt`;
ALTER TABLE `{:=DBPREFIX=:}filetypes` MODIFY COLUMN `mimetype` varchar(100) NOT NULL DEFAULT 'application/octet-stream' AFTER `icon`;

ALTER TABLE `{:=DBPREFIX=:}menu` ADD COLUMN `position` varchar(10) NOT NULL DEFAULT 'left' AFTER `groups`;

ALTER TABLE `{:=DBPREFIX=:}newsletter` MODIFY COLUMN `receiver` longtext NOT NULL AFTER `id`;
ALTER TABLE `{:=DBPREFIX=:}newsletter` ADD COLUMN `sender` varchar(255) NOT NULL DEFAULT '' AFTER `title`;
ALTER TABLE `{:=DBPREFIX=:}newsletter` ADD COLUMN `type` enum('p','h') NULL DEFAULT 'p' AFTER `time`;

ALTER TABLE `{:=DBPREFIX=:}packages` ADD COLUMN `active` enum('0','1') NOT NULL DEFAULT '0' AFTER `title`;
ALTER TABLE `{:=DBPREFIX=:}packages` ADD COLUMN `version` varchar(64) NOT NULL DEFAULT '' AFTER `active`;
ALTER TABLE `{:=DBPREFIX=:}packages` ADD COLUMN `internal` varchar(100) NOT NULL DEFAULT '' AFTER `version`;
ALTER TABLE `{:=DBPREFIX=:}packages` ADD COLUMN `core` enum('0','1') NOT NULL DEFAULT '0' AFTER `internal`;

ALTER TABLE `{:=DBPREFIX=:}plugins` MODIFY COLUMN `active` enum('0','1') NOT NULL DEFAULT '1' AFTER `ordering`;
ALTER TABLE `{:=DBPREFIX=:}plugins` ADD COLUMN `required` enum('0','1') NOT NULL DEFAULT '1' AFTER `position`;
ALTER TABLE `{:=DBPREFIX=:}replies` ADD COLUMN `report` tinytext NOT NULL AFTER `edit`;