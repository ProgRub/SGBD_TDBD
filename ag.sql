-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jan 10, 2021 at 09:34 PM
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

DROP TABLE IF EXISTS `child`;
CREATE TABLE `child` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(128) NOT NULL,
  `birth_date` date NOT NULL,
  `tutor_name` varchar(128) NOT NULL,
  `tutor_phone` varchar(32) NOT NULL,
  `tutor_email` varchar(64) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `child`
--

INSERT INTO `child` (`id`, `name`, `birth_date`, `tutor_name`, `tutor_phone`, `tutor_email`) VALUES
(3, 'Ariana Fonseca Macedo', '2010-06-02', 'Luísa Nádia Moura de Figueiredo', '282080576', ''),
(4, 'Davi José Torres Pinho', '2019-02-05', 'Érika Silva', '285169492', 'erikalva@gmail.com'),
(5, 'Sandro Eduardo de Faria', '2012-03-21', 'Iara Benedita Pinto', '936765395', 'iarabnto@gmail.com'),
(6, 'César Rodrigo Brito Pires', '2014-09-14', 'Renata Freitas de Mota', '264898184', ''),
(7, 'Nádia Cátia de Pinho', '2017-05-08', 'Lucas Eduardo Maia de Melo', '238017974', 'lucalo@gmail.com'),
(8, 'Vicente Valentim Guerreiro', '2018-04-22', 'Renata Assunção Castro', '259870457', 'renatro@gmail.com');

-- --------------------------------------------------------

--
-- Table structure for table `item`
--

DROP TABLE IF EXISTS `item`;
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
(3, 'medidas', 1, 'active'),
(4, 'cabelo', 1, 'active'),
(5, 'autismo', 2, 'active'),
(6, 'síndrome de asperger', 2, 'active'),
(7, 'poliomelite', 2, 'inactive');

-- --------------------------------------------------------

--
-- Table structure for table `item_type`
--

DROP TABLE IF EXISTS `item_type`;
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

DROP TABLE IF EXISTS `subitem`;
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

--
-- Dumping data for table `subitem`
--

INSERT INTO `subitem` (`id`, `name`, `item_id`, `value_type`, `form_field_name`, `form_field_type`, `unit_type_id`, `form_field_order`, `mandatory`, `state`) VALUES
(1, 'altura', 3, 'int', 'med-1-altura', 'text', 5, 1, 1, 'active'),
(2, 'peso', 3, 'int', 'med-2-peso', 'text', 6, 2, 1, 'active'),
(3, 'cintura', 3, 'int', 'med-3-cintura', 'text', 5, 3, 0, 'active'),
(4, 'côr', 4, 'text', 'cab-4-cr', 'text', NULL, 1, 1, 'active'),
(5, 'tipo de fio', 4, 'enum', 'cab-5-tipo_de_fio', 'checkbox', NULL, 2, 1, 'active'),
(6, 'densidade', 4, 'int', 'cab-6-densidade', 'text', 8, 3, 0, 'active'),
(7, 'grau', 5, 'enum', 'aut-7-grau', 'radio', NULL, 1, 1, 'active'),
(8, 'estereotipia', 5, 'enum', 'aut-8-estereotipia', 'radio', NULL, 2, 0, 'active');

-- --------------------------------------------------------

--
-- Table structure for table `subitem_allowed_value`
--

DROP TABLE IF EXISTS `subitem_allowed_value`;
CREATE TABLE `subitem_allowed_value` (
  `id` int UNSIGNED NOT NULL,
  `subitem_id` int UNSIGNED NOT NULL,
  `value` varchar(128) NOT NULL,
  `state` enum('active','inactive') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `subitem_allowed_value`
--

INSERT INTO `subitem_allowed_value` (`id`, `subitem_id`, `value`, `state`) VALUES
(1, 5, 'liso', 'active'),
(2, 5, 'ondulado', 'active'),
(3, 5, 'encaracolado', 'active'),
(4, 7, 'ligeiro', 'active'),
(5, 7, 'moderado', 'active'),
(6, 7, 'grave', 'active'),
(7, 8, 'marcha', 'active'),
(8, 8, 'movimento do tronco', 'active'),
(9, 8, 'cruzamento das pernas', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `subitem_unit_type`
--

DROP TABLE IF EXISTS `subitem_unit_type`;
CREATE TABLE `subitem_unit_type` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(45) NOT NULL COMMENT 'kg, cm, mmHg'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `subitem_unit_type`
--

INSERT INTO `subitem_unit_type` (`id`, `name`) VALUES
(4, 'metros'),
(5, 'centimetros'),
(6, 'kilogramas'),
(7, 'miligramas'),
(8, 'caracóis/centimetro'),
(9, 'ºC'),
(10, 'ºF'),
(13, 'Kelvin');

-- --------------------------------------------------------

--
-- Table structure for table `value`
--

DROP TABLE IF EXISTS `value`;
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
-- Dumping data for table `value`
--

INSERT INTO `value` (`id`, `child_id`, `subitem_id`, `value`, `date`, `time`, `producer`) VALUES
(1, 6, 7, 'ligeiro', '2021-01-03', '21:04:13', 'user'),
(2, 6, 8, 'marcha', '2021-01-03', '21:04:13', 'user');

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
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `item`
--
ALTER TABLE `item`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `item_type`
--
ALTER TABLE `item_type`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `subitem`
--
ALTER TABLE `subitem`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `subitem_allowed_value`
--
ALTER TABLE `subitem_allowed_value`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `subitem_unit_type`
--
ALTER TABLE `subitem_unit_type`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `value`
--
ALTER TABLE `value`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

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
