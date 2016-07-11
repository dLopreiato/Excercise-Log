-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Jul 11, 2016 at 05:43 AM
-- Server version: 10.1.9-MariaDB
-- PHP Version: 5.5.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `exercise_log`
--

-- --------------------------------------------------------

--
-- Table structure for table `excercises`
--

CREATE TABLE `excercises` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(128) NOT NULL,
  `reference_video` varchar(256) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=ascii;

--
-- Dumping data for table `excercises`
--

INSERT INTO `excercises` (`id`, `name`, `reference_video`) VALUES
(1, 'Push Ups', '');

-- --------------------------------------------------------

--
-- Table structure for table `excercise_muscle_mapping`
--

CREATE TABLE `excercise_muscle_mapping` (
  `excercise_id` int(10) UNSIGNED NOT NULL,
  `muscle_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=ascii;

--
-- Dumping data for table `excercise_muscle_mapping`
--

INSERT INTO `excercise_muscle_mapping` (`excercise_id`, `muscle_id`) VALUES
(1, 4);

-- --------------------------------------------------------

--
-- Table structure for table `muscles`
--

CREATE TABLE `muscles` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=ascii;

--
-- Dumping data for table `muscles`
--

INSERT INTO `muscles` (`id`, `name`) VALUES
(1, 'Neck'),
(2, 'Traps (trapezius)'),
(3, 'Shoulders (deltoids)'),
(4, 'Chest (pectoralis)'),
(5, 'Biceps'),
(6, 'Forearm (brachioradialis)'),
(7, 'Abs (rectus abdominis)'),
(8, 'Quads (quadriceps)'),
(9, 'Calves (gastrocnemius)'),
(10, 'Traps (trapezius)'),
(11, 'Triceps (triceps brachii)'),
(12, 'Lats (latissimus dorsi)'),
(13, 'Middle Back (rhomboids)'),
(14, 'Lower Back'),
(15, 'Glutes (gluteus)'),
(16, 'Quads (quadriceps)'),
(17, 'Hamstrings (biceps femoris)');

-- --------------------------------------------------------

--
-- Table structure for table `performed_excercise`
--

CREATE TABLE `performed_excercise` (
  `performed_date` date NOT NULL,
  `excercise_id` int(10) UNSIGNED NOT NULL,
  `performed_reps` varchar(32) NOT NULL,
  `performed_weights` varchar(32) NOT NULL,
  `ready_to_increase` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=ascii;

--
-- Dumping data for table `performed_excercise`
--

INSERT INTO `performed_excercise` (`performed_date`, `excercise_id`, `performed_reps`, `performed_weights`, `ready_to_increase`) VALUES
('2016-07-10', 1, '3x15', 'Bodyweight', 0);

-- --------------------------------------------------------

--
-- Table structure for table `planned_excercise`
--

CREATE TABLE `planned_excercise` (
  `planned_date` date NOT NULL,
  `excercise_id` int(10) UNSIGNED NOT NULL,
  `goal_reps` varchar(32) NOT NULL,
  `goal_weights` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=ascii;

--
-- Dumping data for table `planned_excercise`
--

INSERT INTO `planned_excercise` (`planned_date`, `excercise_id`, `goal_reps`, `goal_weights`) VALUES
('2016-07-10', 1, '3x15', 'Bodyweight');

-- --------------------------------------------------------

--
-- Table structure for table `planned_workout`
--

CREATE TABLE `planned_workout` (
  `planned_date` date NOT NULL,
  `category` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=ascii;

--
-- Dumping data for table `planned_workout`
--

INSERT INTO `planned_workout` (`planned_date`, `category`) VALUES
('2016-07-10', 1);

-- --------------------------------------------------------

--
-- Table structure for table `workout_categories`
--

CREATE TABLE `workout_categories` (
  `id` int(10) UNSIGNED NOT NULL,
  `category` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=ascii;

--
-- Dumping data for table `workout_categories`
--

INSERT INTO `workout_categories` (`id`, `category`) VALUES
(1, 'Weight Focused'),
(2, 'Repetition Focused');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `excercises`
--
ALTER TABLE `excercises`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `excercise_muscle_mapping`
--
ALTER TABLE `excercise_muscle_mapping`
  ADD PRIMARY KEY (`excercise_id`,`muscle_id`),
  ADD UNIQUE KEY `muscle_id` (`muscle_id`),
  ADD KEY `excercise_id` (`excercise_id`);

--
-- Indexes for table `muscles`
--
ALTER TABLE `muscles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `performed_excercise`
--
ALTER TABLE `performed_excercise`
  ADD PRIMARY KEY (`performed_date`,`excercise_id`),
  ADD KEY `excercise_id` (`excercise_id`);

--
-- Indexes for table `planned_excercise`
--
ALTER TABLE `planned_excercise`
  ADD PRIMARY KEY (`planned_date`,`excercise_id`),
  ADD KEY `excercise_id` (`excercise_id`);

--
-- Indexes for table `planned_workout`
--
ALTER TABLE `planned_workout`
  ADD PRIMARY KEY (`planned_date`),
  ADD KEY `category` (`category`);

--
-- Indexes for table `workout_categories`
--
ALTER TABLE `workout_categories`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `excercises`
--
ALTER TABLE `excercises`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `muscles`
--
ALTER TABLE `muscles`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;
--
-- AUTO_INCREMENT for table `workout_categories`
--
ALTER TABLE `workout_categories`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `excercise_muscle_mapping`
--
ALTER TABLE `excercise_muscle_mapping`
  ADD CONSTRAINT `excercise_muscle_mapping_ibfk_1` FOREIGN KEY (`excercise_id`) REFERENCES `excercises` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `excercise_muscle_mapping_ibfk_2` FOREIGN KEY (`muscle_id`) REFERENCES `muscles` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Constraints for table `performed_excercise`
--
ALTER TABLE `performed_excercise`
  ADD CONSTRAINT `performed_excercise_ibfk_1` FOREIGN KEY (`excercise_id`) REFERENCES `excercises` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `performed_excercise_ibfk_2` FOREIGN KEY (`performed_date`) REFERENCES `planned_workout` (`planned_date`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Constraints for table `planned_excercise`
--
ALTER TABLE `planned_excercise`
  ADD CONSTRAINT `planned_excercise_ibfk_1` FOREIGN KEY (`excercise_id`) REFERENCES `excercises` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `planned_excercise_ibfk_2` FOREIGN KEY (`planned_date`) REFERENCES `planned_workout` (`planned_date`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Constraints for table `planned_workout`
--
ALTER TABLE `planned_workout`
  ADD CONSTRAINT `planned_workout_ibfk_1` FOREIGN KEY (`category`) REFERENCES `workout_categories` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
