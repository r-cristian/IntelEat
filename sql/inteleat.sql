-- phpMyAdmin SQL Dump
-- version 3.5.6
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Feb 02, 2013 at 11:48 PM
-- Server version: 5.5.29-0ubuntu0.12.10.1
-- PHP Version: 5.4.6-1ubuntu1.1

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
-- Table structure for table `assessmentRules`
--

CREATE TABLE IF NOT EXISTS `assessmentRules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `text` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

--
-- Dumping data for table `assessmentRules`
--

INSERT INTO `assessmentRules` (`id`, `text`) VALUES
(1, 'if(dish.nutrient.4 > 20) return true;'),
(2, 'if(dish.preparationMode.2) return true; '),
(3, 'if(dish.nutrient.2 > 19) return true;'),
(4, 'if(dish.nutrient.7 > 13) return true;'),
(5, 'if(dish.nutrient.9 > 0) return true;'),
(6, 'if(dish.nutrient.2 / dish.nutrient.1 > 1) return true;');

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
  `dishType` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

--
-- Dumping data for table `dish`
--

INSERT INTO `dish` (`id`, `name`, `quantityPerPortion`, `portionUnit`, `calories`, `dishType`) VALUES
(1, 'Chicken Risotto', 200, 1, 356, 1),
(2, 'Mashed potatoes', 150, 1, 310, 1),
(3, 'Apple juice', 250, 2, 105, 3),
(4, 'Lemon Chicken', 250, 1, 438, 1),
(5, 'Fish Sticks', 200, 1, 278, 1),
(6, 'Fish Sticks', 200, 1, 446, 1);

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
-- Table structure for table `dishType`
--

CREATE TABLE IF NOT EXISTS `dishType` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `dishType`
--

INSERT INTO `dishType` (`id`, `name`) VALUES
(1, 'main'),
(2, 'snack'),
(3, 'drink');

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10 ;

--
-- Dumping data for table `nutrient`
--

INSERT INTO `nutrient` (`id`, `name`, `unit`, `isImportant`) VALUES
(1, 'fibres', 1, 1),
(2, 'simple carbohidrates', 1, 1),
(3, 'complex carbohidrates', 1, 1),
(4, 'saturated fats', 1, 1),
(5, 'protein', 1, 1),
(6, 'unsaturated fat', 1, 1),
(7, 'cholesterol', 1, 1),
(8, 'salt', 1, 1),
(9, 'alcohol', 2, 1);

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
-- Table structure for table `planningRuleOutput`
--

CREATE TABLE IF NOT EXISTS `planningRuleOutput` (
  `nutrientId` int(11) NOT NULL,
  `ruleId` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  PRIMARY KEY (`nutrientId`,`ruleId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `planningRules`
--

CREATE TABLE IF NOT EXISTS `planningRules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `minAge` int(11) NOT NULL,
  `maxAge` int(11) NOT NULL,
  `genderId` int(11) NOT NULL,
  `minBMI` int(11) NOT NULL,
  `maxBMI` int(11) NOT NULL,
  `lifestyleId` int(11) NOT NULL,
  `diabetesTypeId` int(11) NOT NULL,
  `KCal` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

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
