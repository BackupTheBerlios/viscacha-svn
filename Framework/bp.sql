-- phpMyAdmin SQL Dump
-- version 3.4.5
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Erstellungszeit: 29. Dez 2011 um 15:40
-- Server Version: 5.5.16
-- PHP-Version: 5.3.8

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Datenbank: `bp`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `ac_categories`
--

CREATE TABLE IF NOT EXISTS `ac_categories` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `ac_fields`
--

CREATE TABLE IF NOT EXISTS `ac_fields` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE latin1_general_ci NOT NULL,
  `description` text COLLATE latin1_general_ci NOT NULL,
  `type` varchar(100) COLLATE latin1_general_ci NOT NULL,
  `position` varchar(100) COLLATE latin1_general_ci NOT NULL,
  `priority` smallint(6) NOT NULL,
  `params` mediumtext COLLATE latin1_general_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `ac_group`
--

CREATE TABLE IF NOT EXISTS `ac_group` (
  `id` tinyint(2) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(50) COLLATE latin1_general_ci NOT NULL,
  `registered` tinyint(1) NOT NULL,
  `admin` tinyint(1) NOT NULL,
  `editor` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=5 ;

--
-- Daten für Tabelle `ac_group`
--

INSERT INTO `ac_group` (`id`, `title`, `registered`, `admin`, `editor`) VALUES
(1, 'Administrator', 1, 1, 1),
(2, 'Editor', 1, 0, 1),
(3, 'Autor', 1, 0, 0),
(4, 'Gast', 0, 0, 0);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `ac_page`
--

CREATE TABLE IF NOT EXISTS `ac_page` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uri` varchar(100) COLLATE latin1_general_ci NOT NULL,
  `title` varchar(255) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `content` longtext COLLATE latin1_general_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uri` (`uri`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=2 ;

--
-- Daten für Tabelle `ac_page`
--

INSERT INTO `ac_page` (`id`, `uri`, `title`, `content`) VALUES
(1, '', 'Startseite', 'Willkommen auf der Startseite! :-)');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `ac_session`
--

CREATE TABLE IF NOT EXISTS `ac_session` (
  `sid` varchar(32) COLLATE latin1_general_ci NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `visit` int(10) unsigned NOT NULL,
  `ip` varchar(16) COLLATE latin1_general_ci NOT NULL,
  `settings` longtext COLLATE latin1_general_ci NOT NULL,
  UNIQUE KEY `sid` (`sid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `ac_user`
--

CREATE TABLE IF NOT EXISTS `ac_user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `forename` varchar(100) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `surname` varchar(100) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `pw` varchar(128) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `group_id` tinyint(2) unsigned NOT NULL,
  `email` varchar(255) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `gender` enum('m','w') COLLATE latin1_general_ci NOT NULL,
  `birthday` date NOT NULL,
  `city` varchar(100) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `country` varchar(100) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `regdate` int(10) unsigned NOT NULL,
  `lastvisit` int(10) unsigned NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `verification` char(32) COLLATE latin1_general_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `group_id` (`group_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=1 ;

--
-- Constraints der exportierten Tabellen
--

--
-- Constraints der Tabelle `ac_user`
--
ALTER TABLE `ac_user`
  ADD CONSTRAINT `ac_user_ibfk_1` FOREIGN KEY (`group_id`) REFERENCES `ac_group` (`id`) ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
