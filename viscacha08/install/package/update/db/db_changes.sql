ALTER TABLE `v_component` MODIFY COLUMN `file` varchar(127) NOT NULL DEFAULT '' AFTER `id`;
ALTER TABLE `v_component` ADD COLUMN `package` mediumint(7) unsigned NOT NULL AFTER `file`;
ALTER TABLE `v_component` MODIFY COLUMN `active` enum('0','1') NOT NULL DEFAULT '1' AFTER `package`;
ALTER TABLE `v_component` ADD COLUMN `required` enum('0','1') NOT NULL DEFAULT '1' AFTER `active`;

ALTER TABLE `v_filetypes` MODIFY COLUMN `extension` varchar(200) NOT NULL DEFAULT '' AFTER `id`;
ALTER TABLE `v_filetypes` MODIFY COLUMN `icon` varchar(100) NOT NULL DEFAULT '' AFTER `desctxt`;
ALTER TABLE `v_filetypes` MODIFY COLUMN `mimetype` varchar(100) NOT NULL DEFAULT 'application/octet-stream' AFTER `icon`;

ALTER TABLE `v_menu` ADD COLUMN `position` varchar(10) NOT NULL DEFAULT 'left' AFTER `groups`;

ALTER TABLE `v_newsletter` MODIFY COLUMN `receiver` longtext NOT NULL AFTER `id`;
ALTER TABLE `v_newsletter` ADD COLUMN `sender` varchar(255) NOT NULL DEFAULT '' AFTER `title`;
ALTER TABLE `v_newsletter` ADD COLUMN `type` enum('p','h') NULL DEFAULT 'p' AFTER `time`;

ALTER TABLE `v_packages` ADD COLUMN `active` enum('0','1') NOT NULL DEFAULT '0' AFTER `title`;
ALTER TABLE `v_packages` ADD COLUMN `version` varchar(64) NOT NULL DEFAULT '' AFTER `active`;
ALTER TABLE `v_packages` ADD COLUMN `internal` varchar(100) NOT NULL DEFAULT '' AFTER `version`;
ALTER TABLE `v_packages` ADD COLUMN `core` enum('0','1') NOT NULL DEFAULT '0' AFTER `internal`;

ALTER TABLE `v_plugins` MODIFY COLUMN `active` enum('0','1') NOT NULL DEFAULT '1' AFTER `ordering`;
ALTER TABLE `v_plugins` ADD COLUMN `required` enum('0','1') NOT NULL DEFAULT '1' AFTER `position`;
ALTER TABLE `v_replies` ADD COLUMN `report` tinytext NOT NULL AFTER `edit`;