-- phpMyAdmin SQL Dump
-- version 4.0.10.7
-- http://www.phpmyadmin.net
--
-- Host: localhost:3306
-- Generation Time: Jan 28, 2016 at 10:11 AM
-- Server version: 5.5.47-cll
-- PHP Version: 5.4.31

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `flagsrus_live`
--

-- --------------------------------------------------------

--
-- Table structure for table `states`
--

CREATE TABLE IF NOT EXISTS `states` (
  `stid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `coid` int(10) unsigned NOT NULL DEFAULT '0',
  `tax_available` enum('Yes','No') NOT NULL DEFAULT 'Yes',
  `shipping_taxable` enum('Yes','No') NOT NULL DEFAULT 'No',
  `tax` decimal(20,5) unsigned NOT NULL DEFAULT '0.00000',
  `name` varchar(255) NOT NULL DEFAULT '',
  `short_name` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`stid`),
  KEY `coid` (`coid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=68 ;

--
-- Dumping data for table `states`
--

INSERT INTO `states` (`stid`, `coid`, `tax_available`, `shipping_taxable`, `tax`, `name`, `short_name`) VALUES
(1, 1, 'No', 'No', '0.00000', 'Alabama', 'AL'),
(2, 1, 'No', 'No', '0.00000', 'Alaska', 'AK'),
(3, 1, 'No', 'No', '0.00000', 'Arizona', 'AZ'),
(4, 1, 'No', 'No', '0.00000', 'Arkansas', 'AR'),
(5, 1, 'No', 'No', '0.00000', 'California', 'CA'),
(6, 1, 'No', 'No', '0.00000', 'Colorado', 'CO'),
(7, 1, 'No', 'No', '0.00000', 'Connecticut', 'CT'),
(8, 1, 'No', 'No', '0.00000', 'Delaware', 'DE'),
(9, 1, 'No', 'No', '0.00000', 'Florida', 'FL'),
(10, 1, 'No', 'No', '0.00000', 'Georgia', 'GA'),
(11, 1, 'No', 'No', '0.00000', 'Hawaii', 'HI'),
(12, 1, 'No', 'No', '0.00000', 'Idaho', 'ID'),
(13, 1, 'No', 'No', '0.00000', 'Illinois', 'IL'),
(14, 1, 'No', 'No', '0.00000', 'Indiana', 'IN'),
(15, 1, 'No', 'No', '0.00000', 'Iowa', 'IA'),
(16, 1, 'No', 'No', '0.00000', 'Kansas', 'KS'),
(17, 1, 'No', 'No', '0.00000', 'Kentucky', 'KY'),
(18, 1, 'No', 'No', '0.00000', 'Louisiana', 'LA'),
(19, 1, 'No', 'No', '0.00000', 'Maine', 'ME'),
(20, 1, 'No', 'No', '0.00000', 'Maryland', 'MD'),
(21, 1, 'No', 'No', '0.00000', 'Massachusetts', 'MA'),
(22, 1, 'No', 'No', '0.00000', 'Michigan', 'MI'),
(23, 1, 'No', 'No', '0.00000', 'Minnesota', 'MN'),
(24, 1, 'No', 'No', '0.00000', 'Mississippi', 'MS'),
(25, 1, 'No', 'No', '0.00000', 'Missouri', 'MO'),
(26, 1, 'No', 'No', '0.00000', 'Montana', 'MT'),
(27, 1, 'No', 'No', '0.00000', 'Nebraska', 'NE'),
(28, 1, 'No', 'No', '0.00000', 'Nevada', 'NV'),
(29, 1, 'No', 'No', '0.00000', 'New Hampshire', 'NH'),
(30, 1, 'No', 'No', '0.00000', 'New Jersey', 'NJ'),
(31, 1, 'No', 'No', '0.00000', 'New Mexico', 'NM'),
(32, 1, 'No', 'No', '0.00000', 'New York', 'NY'),
(33, 1, 'No', 'No', '0.00000', 'North Carolina', 'NC'),
(34, 1, 'No', 'No', '0.00000', 'North Dakota', 'ND'),
(35, 1, 'No', 'No', '0.00000', 'Ohio', 'OH'),
(36, 1, 'No', 'No', '0.00000', 'Oklahoma', 'OK'),
(37, 1, 'No', 'No', '0.00000', 'Oregon', 'OR'),
(38, 1, 'No', 'No', '0.00000', 'Pennsylvania', 'PA'),
(39, 1, 'No', 'No', '0.00000', 'Rhode Island', 'RI'),
(40, 1, 'No', 'No', '0.00000', 'South Carolina', 'SC'),
(41, 1, 'No', 'No', '0.00000', 'South Dakota', 'SD'),
(42, 1, 'No', 'No', '0.00000', 'Tennessee', 'TN'),
(43, 1, 'No', 'No', '0.00000', 'Texas', 'TX'),
(44, 1, 'No', 'No', '0.00000', 'Utah', 'UT'),
(45, 1, 'No', 'No', '0.00000', 'Vermont', 'VT'),
(46, 1, 'No', 'No', '0.00000', 'Virginia', 'VA'),
(47, 1, 'No', 'No', '0.00000', 'Washington', 'WA'),
(48, 1, 'No', 'No', '0.00000', 'West Virginia', 'WV'),
(49, 1, 'No', 'No', '0.00000', 'Wisconsin', 'WI'),
(50, 1, 'No', 'No', '0.00000', 'Wyoming', 'WY'),
(51, 1, 'No', 'No', '0.00000', 'District of Columbia', 'DC'),
(52, 2, 'No', 'No', '0.00000', 'Alberta', 'AB'),
(53, 2, 'No', 'No', '0.00000', 'British Columbia', 'BC'),
(54, 2, 'No', 'No', '0.00000', 'Manitoba', 'MB'),
(55, 2, 'No', 'No', '0.00000', 'New Brunswick', 'NB'),
(56, 2, 'No', 'No', '0.00000', 'Newfoundland and Labrador', 'NL'),
(57, 2, 'No', 'No', '0.00000', 'Northwest Territories', 'NT'),
(58, 2, 'No', 'No', '0.00000', 'Nova Scotia', 'NS'),
(59, 2, 'No', 'No', '0.00000', 'Nunavut', 'NU'),
(60, 2, 'No', 'No', '0.00000', 'Ontario', 'ON'),
(61, 2, 'No', 'No', '0.00000', 'Prince Edward Island', 'PE'),
(62, 2, 'No', 'No', '0.00000', 'Quebec', 'QC'),
(63, 2, 'No', 'No', '0.00000', 'Saskatchewan', 'SK'),
(64, 2, 'No', 'No', '0.00000', 'Yukon', 'YT'),
(65, 1, 'No', 'No', '0.00000', 'Puerto Rico', 'PR'),
(66, 1, 'Yes', 'No', '0.00000', 'Saipan', 'MP'),
(67, 1, 'Yes', 'No', '0.00000', 'Virgin Islands', 'VI');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
