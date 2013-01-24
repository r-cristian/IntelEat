-- phpMyAdmin SQL Dump
-- version 3.4.10.1deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jan 24, 2013 at 07:42 PM
-- Server version: 5.5.28
-- PHP Version: 5.3.10-1ubuntu3.4

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `inteleat`
--

-- --------------------------------------------------------

--
-- Table structure for table `diabetesType`
--

CREATE TABLE IF NOT EXISTS `diabetesType` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `diabetesType`
--

INSERT INTO `diabetesType` (`id`, `name`) VALUES
(1, 'Type 1'),
(2, 'Type 2');

-- --------------------------------------------------------

--
-- Table structure for table `dish`
--

CREATE TABLE IF NOT EXISTS `dish` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(500) NOT NULL,
  `quantityPerPortion` int(11) NOT NULL,
  `portionUnit` int(11) NOT NULL,
  `calories` int(11) NOT NULL,
  `isSnack` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

--
-- Dumping data for table `dish`
--

INSERT INTO `dish` (`id`, `name`, `quantityPerPortion`, `portionUnit`, `calories`, `isSnack`) VALUES
(1, 'Chicken Risotto', 200, 1, 356, 0),
(2, 'Mashed potatoes', 150, 1, 310, 0),
(3, 'Apple juice', 250, 2, 105, 0),
(4, 'Lemon Chicken', 250, 1, 438, 0),
(5, 'Fish Sticks', 200, 1, 278, 0),
(6, 'Fish Sticks', 200, 1, 446, 0);

-- --------------------------------------------------------

--
-- Table structure for table `dishNutrients`
--

CREATE TABLE IF NOT EXISTS `dishNutrients` (
  `dishId` int(11) NOT NULL,
  `nutrientId` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  PRIMARY KEY (`dishId`,`nutrientId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `dishNutrients`
--

INSERT INTO `dishNutrients` (`dishId`, `nutrientId`, `quantity`) VALUES
(1, 1, 20),
(1, 2, 15);

-- --------------------------------------------------------

--
-- Table structure for table `dishPreparationMode`
--

CREATE TABLE IF NOT EXISTS `dishPreparationMode` (
  `dishId` int(11) NOT NULL,
  `preparationModeId` int(11) NOT NULL,
  PRIMARY KEY (`dishId`,`preparationModeId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `dishPreparationMode`
--

INSERT INTO `dishPreparationMode` (`dishId`, `preparationModeId`) VALUES
(1, 3),
(2, 3),
(2, 5),
(3, 5),
(4, 1),
(5, 1),
(6, 2);

-- --------------------------------------------------------

--
-- Table structure for table `gender`
--

CREATE TABLE IF NOT EXISTS `gender` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `gender`
--

INSERT INTO `gender` (`id`, `name`) VALUES
(1, 'male'),
(2, 'female');

-- --------------------------------------------------------

--
-- Table structure for table `lifestyle`
--

CREATE TABLE IF NOT EXISTS `lifestyle` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `lifestyle`
--

INSERT INTO `lifestyle` (`id`, `name`) VALUES
(1, 'sedentary'),
(2, 'midly active'),
(3, 'active'),
(4, 'very active');

-- --------------------------------------------------------

--
-- Table structure for table `nutrient`
--

CREATE TABLE IF NOT EXISTS `nutrient` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `unit` int(11) NOT NULL,
  `isImportant` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `unit` (`unit`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `nutrient`
--

INSERT INTO `nutrient` (`id`, `name`, `unit`, `isImportant`) VALUES
(1, 'fibres', 1, 1),
(2, 'sugar', 1, 1),
(3, 'carbohidrates', 1, 0),
(4, 'saturated fats', 1, 1),
(5, 'protein', 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `patient`
--

CREATE TABLE IF NOT EXISTS `patient` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `age` int(2) NOT NULL,
  `gender` int(11) NOT NULL,
  `height` int(3) NOT NULL,
  `weight` int(3) NOT NULL,
  `lifestyle` int(11) NOT NULL,
  `diabetesType` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `patient`
--

INSERT INTO `patient` (`id`, `name`, `age`, `gender`, `height`, `weight`, `lifestyle`, `diabetesType`) VALUES
(1, 'Gica', 23, 1, 180, 90, 1, 1),
(3, 'mircea29', 66, 1, 199, 99, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `preparationMode`
--

CREATE TABLE IF NOT EXISTS `preparationMode` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `preparationMode`
--

INSERT INTO `preparationMode` (`id`, `name`) VALUES
(1, 'baked'),
(2, 'fried'),
(3, 'boiled'),
(4, 'steaming'),
(5, 'fresh');

-- --------------------------------------------------------

--
-- Table structure for table `unit`
--

CREATE TABLE IF NOT EXISTS `unit` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `shortName` varchar(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `unit`
--

INSERT INTO `unit` (`id`, `name`, `shortName`) VALUES
(1, 'grams', 'gm'),
(2, 'milliliters', 'ml');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `nutrient`
--
ALTER TABLE `nutrient`
  ADD CONSTRAINT `nutrient_ibfk_1` FOREIGN KEY (`unit`) REFERENCES `unit` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
