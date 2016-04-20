
-- phpMyAdmin SQL Dump
-- version 2.11.4
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Apr 20, 2016 at 04:21 PM
-- Server version: 5.1.57
-- PHP Version: 5.2.17

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `a9993749_dbase`
--

-- --------------------------------------------------------

--
-- Table structure for table `companies`
--

CREATE TABLE `companies` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `earnings` float NOT NULL,
  `total_earnings` float NOT NULL,
  `parrent` int(11) unsigned NOT NULL,
  `child_count` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `companies`
--

INSERT INTO `companies` VALUES(1, 'Company 1', 25, 53, 0, 2);
INSERT INTO `companies` VALUES(2, 'Company 2', 13, 18, 1, 1);
INSERT INTO `companies` VALUES(3, 'Company 3', 5, 5, 2, 0);
INSERT INTO `companies` VALUES(4, 'Company 4', 10, 10, 1, 0);
