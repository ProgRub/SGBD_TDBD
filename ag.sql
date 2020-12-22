-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Dec 20, 2020 at 09:16 PM
-- Server version: 8.0.22
-- PHP Version: 7.4.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bitnami_wordpress`
--

-- --------------------------------------------------------

--
-- Table structure for table `child`
--

CREATE TABLE `child` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(128) NOT NULL,
  `birth_date` date NOT NULL,
  `tutor_name` varchar(128) NOT NULL,
  `tutor_phone` varchar(32) NOT NULL,
  `tutor_email` varchar(64) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `item`
--

CREATE TABLE `item` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(128) NOT NULL,
  `item_type_id` int UNSIGNED NOT NULL DEFAULT '0',
  `state` enum('active','inactive') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `item`
--

INSERT INTO `item` (`id`, `name`, `item_type_id`, `state`) VALUES
(3, 'medidas', 1, 'active');

-- --------------------------------------------------------

--
-- Table structure for table `item_type`
--

CREATE TABLE `item_type` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(64) NOT NULL,
  `code` varchar(32) NOT NULL COMMENT 'manually fill with values: child_data, diagnosis, intervention, evaluation'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `item_type`
--

INSERT INTO `item_type` (`id`, `name`, `code`) VALUES
(1, 'dado de criança', 'child_data'),
(2, 'diagnóstico', 'diagnosis'),
(3, 'intervenção', 'intervention'),
(4, 'avaliação', 'evaluation');

-- --------------------------------------------------------

--
-- Table structure for table `subitem`
--

CREATE TABLE `subitem` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(128) NOT NULL DEFAULT '',
  `item_id` int UNSIGNED NOT NULL,
  `value_type` enum('text','bool','int','double','enum') NOT NULL COMMENT 'text, int, double, boolean, enum',
  `form_field_name` varchar(64) NOT NULL DEFAULT '' COMMENT 'ascii string to be used as the name of the form field',
  `form_field_type` enum('text','textbox','radio','checkbox','selectbox') NOT NULL,
  `unit_type_id` int UNSIGNED DEFAULT NULL,
  `form_field_order` int UNSIGNED NOT NULL COMMENT 'order in which form fields will be shown',
  `mandatory` int NOT NULL COMMENT '1 if subitem is mandatory for its parent, 0 if not',
  `state` enum('active','inactive') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `subitem_allowed_value`
--

CREATE TABLE `subitem_allowed_value` (
  `id` int UNSIGNED NOT NULL,
  `subitem_id` int UNSIGNED NOT NULL,
  `value` varchar(128) NOT NULL,
  `state` enum('active','inactive') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `subitem_unit_type`
--

CREATE TABLE `subitem_unit_type` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(45) NOT NULL COMMENT 'kg, cm, mmHg'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `value`
--

CREATE TABLE `value` (
  `id` int UNSIGNED NOT NULL,
  `child_id` int UNSIGNED NOT NULL,
  `subitem_id` int UNSIGNED NOT NULL,
  `value` varchar(8192) NOT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL,
  `producer` varchar(64) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `child`
--
ALTER TABLE `child`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `item`
--
ALTER TABLE `item`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_item_item_type1_idx` (`item_type_id`);

--
-- Indexes for table `item_type`
--
ALTER TABLE `item_type`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `subitem`
--
ALTER TABLE `subitem`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_subitem_unit_type_idx` (`unit_type_id`),
  ADD KEY `fk_subitem_item1_idx` (`item_id`);

--
-- Indexes for table `subitem_allowed_value`
--
ALTER TABLE `subitem_allowed_value`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_subitem_allowed_value_subitem1_idx` (`subitem_id`);

--
-- Indexes for table `subitem_unit_type`
--
ALTER TABLE `subitem_unit_type`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `value`
--
ALTER TABLE `value`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_value_child1_idx` (`child_id`),
  ADD KEY `fk_value_subitem1_idx` (`subitem_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `child`
--
ALTER TABLE `child`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `item`
--
ALTER TABLE `item`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `item_type`
--
ALTER TABLE `item_type`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `subitem`
--
ALTER TABLE `subitem`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `subitem_allowed_value`
--
ALTER TABLE `subitem_allowed_value`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `subitem_unit_type`
--
ALTER TABLE `subitem_unit_type`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `value`
--
ALTER TABLE `value`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `item`
--
ALTER TABLE `item`
  ADD CONSTRAINT `fk_item_item_type1` FOREIGN KEY (`item_type_id`) REFERENCES `item_type` (`id`);

--
-- Constraints for table `subitem`
--
ALTER TABLE `subitem`
  ADD CONSTRAINT `fk_subitem_item1` FOREIGN KEY (`item_id`) REFERENCES `item` (`id`),
  ADD CONSTRAINT `fk_subitem_unit_type` FOREIGN KEY (`unit_type_id`) REFERENCES `subitem_unit_type` (`id`);

--
-- Constraints for table `subitem_allowed_value`
--
ALTER TABLE `subitem_allowed_value`
  ADD CONSTRAINT `fk_subitem_allowed_value_subitem1` FOREIGN KEY (`subitem_id`) REFERENCES `subitem` (`id`);

--
-- Constraints for table `value`
--
ALTER TABLE `value`
  ADD CONSTRAINT `fk_value_child1` FOREIGN KEY (`child_id`) REFERENCES `child` (`id`),
  ADD CONSTRAINT `fk_value_subitem1` FOREIGN KEY (`subitem_id`) REFERENCES `subitem` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
