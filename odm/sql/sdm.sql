-- phpMyAdmin SQL Dump
-- version 3.5.8.2
-- http://www.phpmyadmin.net
--
-- Host: localhost:3306
-- Erstellungszeit: 25. Feb 2014 um 20:30
-- Server Version: 5.5.35-0ubuntu0.12.04.2
-- PHP-Version: 5.4.16

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Tabellenstruktur für Tabelle `user`
--

CREATE TABLE IF NOT EXISTS `sdm_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `hash` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

--
-- Tabellenstruktur für Tabelle `user_settings`
--

CREATE TABLE IF NOT EXISTS `sdm_user_setting` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `category` varchar(255) DEFAULT NULL,
  `key` varchar(100) NOT NULL,
  `value` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `sdm_user_setting`
  ADD CONSTRAINT `user_setting_user` 
     FOREIGN KEY (`user_id`) REFERENCES `sdm_user` (`id`) 
       ON DELETE CASCADE 
       ON UPDATE CASCADE;
	   
--
-- Tabellenstruktur für Tabelle `sdm_device`
--

CREATE TABLE IF NOT EXISTS `sdm_device` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `gcm_regid` text,
  `name` varchar(50) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

ALTER TABLE `sdm_device`
  ADD CONSTRAINT `device_user` 
     FOREIGN KEY (`user_id`) REFERENCES `sdm_user` (`id`) 
       ON DELETE CASCADE 
       ON UPDATE CASCADE;
	   
--
-- Tabellenstruktur für Tabelle `sdm_device_info`
--

CREATE TABLE IF NOT EXISTS `sdm_device_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `device_id` int(11) NOT NULL,
  `recv_date` timestamp NULL DEFAULT NULL,
  `requ_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `request` varchar(255) DEFAULT NULL,
  `gcm_result` varchar(500) DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL,
  `response_ip` varchar(20) DEFAULT NULL,
  `has_data` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `device_device_id` (`device_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `sdm_device_info`
  ADD CONSTRAINT `device_info_device` 
     FOREIGN KEY (`device_id`) REFERENCES `sdm_device` (`id`) 
       ON DELETE CASCADE 
       ON UPDATE CASCADE;

--
-- Tabellenstruktur für Tabelle `sdm_device_info_data`
--

CREATE TABLE IF NOT EXISTS `sdm_device_info_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `device_info_id` int(11) NOT NULL,
  `key` varchar(255) NOT NULL,
  `value` varchar(255) DEFAULT NULL,
  `longvalue` text,
  `blob` longblob,
  PRIMARY KEY (`id`),
  KEY `device_info_info_id` (`device_info_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `sdm_device_info_data`
  ADD CONSTRAINT `device_info_data_device_info` 
     FOREIGN KEY (`device_info_id`) REFERENCES `sdm_device_info` (`id`) 
       ON DELETE CASCADE 
       ON UPDATE CASCADE;

--
-- Tabellenstruktur für Tabelle `sdm_location_history`
--

CREATE TABLE IF NOT EXISTS `sdm_device_location` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `device_id` int(11) NOT NULL,
  `recv_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `longitude` varchar(50) NOT NULL,
  `latitude` varchar(50) NOT NULL,
  `altitude` varchar(50) DEFAULT NULL,
  `accuracy` varchar(10) DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `type` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `device_location_device_id` (`device_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `sdm_device_location`
  ADD CONSTRAINT `device_location_device` 
     FOREIGN KEY (`device_id`) REFERENCES `sdm_device` (`id`) 
       ON DELETE CASCADE 
       ON UPDATE CASCADE;