-- phpMyAdmin SQL Dump
-- version 5.1.4-dev+20220331.b9ddf0b305
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Feb 08, 2026 at 07:46 PM
-- Server version: 10.4.34-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `hhudgeon`
--

-- --------------------------------------------------------

--
-- Table structure for table `myrecords__artists`
--

CREATE TABLE `myrecords__artists` (
  `artistId` int(11) NOT NULL,
  `artistName` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `myrecords__artists`
--

INSERT INTO `myrecords__artists` (`artistId`, `artistName`) VALUES
(1, 'Alvin Cash and The Crawlers'),
(3, 'Elvis Costello'),
(4, 'The Jarmels'),
(20, 'The Beatles'),
(21, 'Angela Martin'),
(22, 'Buster Brown'),
(24, 'Solomon Burke'),
(25, 'Dave, Dee, Dozy, Beaky, Mich and Tich'),
(26, 'Little Richard');

-- --------------------------------------------------------

--
-- Table structure for table `myrecords__artist_record`
--

CREATE TABLE `myrecords__artist_record` (
  `artistId` int(11) NOT NULL,
  `recordId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `myrecords__artist_record`
--

INSERT INTO `myrecords__artist_record` (`artistId`, `recordId`) VALUES
(20, 29),
(21, 27),
(22, 28),
(24, 30),
(25, 31),
(26, 32);

-- --------------------------------------------------------

--
-- Table structure for table `myrecords__genres`
--

CREATE TABLE `myrecords__genres` (
  `genreId` int(11) NOT NULL,
  `genreName` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `myrecords__genres`
--

INSERT INTO `myrecords__genres` (`genreId`, `genreName`) VALUES
(1, 'Northern Soul'),
(2, 'Motown'),
(3, 'Memphis Soul'),
(4, 'Southern Soul'),
(5, 'Doo-Wop'),
(6, 'Rockabilly'),
(7, 'Psychedelic Rock'),
(8, 'Surf Rock'),
(9, 'Garage Rock'),
(10, 'Hair Metal'),
(11, 'Disco'),
(12, 'Punk Rock'),
(13, 'New Wave'),
(14, 'Indie Rock'),
(15, 'R&B'),
(16, 'Reggae');

-- --------------------------------------------------------

--
-- Table structure for table `myrecords__recordLabel`
--

CREATE TABLE `myrecords__recordLabel` (
  `recordLabelId` int(11) NOT NULL,
  `recordLabelName` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `myrecords__recordLabel`
--

INSERT INTO `myrecords__recordLabel` (`recordLabelId`, `recordLabelName`) VALUES
(1, 'Motown'),
(2, 'Stax Records'),
(3, 'Atlantic'),
(4, 'Sun Records'),
(5, 'Columbia'),
(6, 'Capitol Records'),
(7, 'RCA'),
(8, 'Mercury Records'),
(9, 'Mar-v-lus Records'),
(10, 'Laurie Records'),
(11, 'Sire Records'),
(19, 'Atco Records'),
(20, 'Fire Records'),
(21, 'Apple Records'),
(22, 'Fontana'),
(23, 'Oldies 45');

-- --------------------------------------------------------

--
-- Table structure for table `myrecords__records`
--

CREATE TABLE `myrecords__records` (
  `recordId` int(11) NOT NULL,
  `recordLabelId` int(10) UNSIGNED NOT NULL,
  `recordName` varchar(100) DEFAULT NULL,
  `format` enum('45','LP') NOT NULL,
  `albumLink` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `myrecords__records`
--

INSERT INTO `myrecords__records` (`recordId`, `recordLabelId`, `recordName`, `format`, `albumLink`) VALUES
(4, 10, NULL, '45', NULL),
(7, 6, 'Testing Album 01', 'LP', ''),
(8, 6, 'Testing Album 01', 'LP', ''),
(9, 5, 'Testing Album 2', 'LP', ''),
(10, 9, 'Testing LP Album 01 ', 'LP', ''),
(12, 3, 'Testing To See if Album Link Works', 'LP', 'https://youtu.be/dQw4w9WgXcQ?si=gsu5QT0Haq8dBjOK'),
(14, 3, 'May 14 Testing 01', 'LP', ''),
(15, 3, 'May 14 test 02', 'LP', ''),
(16, 3, 'May 14 testing 03', 'LP', ''),
(17, 10, NULL, '45', NULL),
(18, 10, NULL, '45', NULL),
(19, 10, NULL, '45', NULL),
(20, 3, 'Double Test', 'LP', ''),
(21, 5, NULL, '45', NULL),
(22, 7, NULL, '45', NULL),
(23, 5, NULL, '45', NULL),
(24, 3, 'Abbey Road', 'LP', ''),
(25, 5, NULL, '45', NULL),
(26, 17, NULL, '45', NULL),
(27, 19, NULL, '45', NULL),
(28, 20, NULL, '45', NULL),
(29, 21, 'Abbey Road', 'LP', ''),
(30, 3, NULL, '45', NULL),
(31, 22, 'Dave Dee, Dozy, Beaky, Mick and Tich', 'LP', 'https://youtu.be/X-gZhBl3qTs?si=MudxXDeErHeHCudM'),
(32, 23, NULL, '45', NULL),
(33, 20, NULL, '45', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `myrecords__tracks`
--

CREATE TABLE `myrecords__tracks` (
  `trackId` int(11) NOT NULL,
  `recordId` int(10) UNSIGNED NOT NULL,
  `artistId` int(10) UNSIGNED NOT NULL,
  `genreId` int(10) UNSIGNED NOT NULL,
  `trackName` varchar(100) NOT NULL,
  `trackLink` varchar(255) DEFAULT NULL,
  `trackInfo` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `myrecords__tracks`
--

INSERT INTO `myrecords__tracks` (`trackId`, `recordId`, `artistId`, `genreId`, `trackName`, `trackLink`, `trackInfo`) VALUES
(4, 4, 4, 15, 'A Little Bit of Soap', 'https://youtu.be/a0wQU7fJEVQ?si=2mlBu8OxgezRC7W8', ''),
(5, 4, 4, 1, 'The Way You Look Tonight', 'https://youtu.be/Oq1O_fr21UY?si=qqOT5fDAJw11ioa2', ''),
(57, 27, 21, 5, 'Dip Da Dip', '0', ''),
(58, 27, 21, 5, 'Take Me to the Fair', '0', ''),
(59, 28, 22, 5, 'Is You Or Is You Ain\'t My Baby', '0', ''),
(60, 28, 22, 5, 'Don\'t Dog Your Woman', '0', ''),
(61, 29, 20, 14, 'Come Together', NULL, NULL),
(62, 29, 20, 14, 'Something', NULL, NULL),
(63, 29, 20, 14, 'Maxwell\'s Silver Hammer', NULL, NULL),
(64, 29, 20, 14, 'Oh Darling', NULL, NULL),
(65, 29, 20, 14, 'Octopus\'s Garden', NULL, NULL),
(66, 29, 20, 14, 'I Want You (She\'s So Heavy)', NULL, NULL),
(67, 29, 20, 14, 'Here Comes the Sun', NULL, NULL),
(68, 29, 20, 14, 'Because', NULL, NULL),
(69, 29, 20, 14, 'You Never Give Me Your Money', NULL, NULL),
(70, 29, 20, 14, 'Sun King', NULL, NULL),
(71, 29, 20, 14, 'Mean Mr Mustard', NULL, NULL),
(72, 29, 20, 14, 'Polythene Pam', NULL, NULL),
(73, 29, 20, 14, 'She Came In Through the Bedroom Window', NULL, NULL),
(74, 29, 20, 14, 'Golden Slumbers', NULL, NULL),
(75, 29, 20, 14, 'Carry the Weight', NULL, NULL),
(76, 30, 24, 1, 'Got to Get You Off My Mind', '0', ''),
(77, 30, 24, 1, 'Peepin', '0', ''),
(78, 31, 25, 9, 'DDD-BMT', NULL, NULL),
(79, 31, 25, 9, 'The Sun Goes Down', NULL, NULL),
(80, 31, 25, 9, 'Shame', NULL, NULL),
(81, 31, 25, 9, 'You Know What I Want', NULL, NULL),
(82, 31, 25, 9, 'Loos of England', NULL, NULL),
(83, 31, 25, 9, 'Over and Over Again', NULL, NULL),
(84, 31, 25, 9, 'Marina', NULL, NULL),
(85, 31, 25, 9, 'Nose for Trouble', NULL, NULL),
(86, 31, 25, 9, 'We\'ve Got a Good Thing Goin', NULL, NULL),
(87, 31, 25, 9, 'He\'s a Raver', NULL, NULL),
(88, 32, 26, 3, 'She\'s Got It', '0', ''),
(89, 32, 26, 2, 'The Girl Can\'t Help It', '0', '');

-- --------------------------------------------------------

--
-- Table structure for table `myrecords__users`
--

CREATE TABLE `myrecords__users` (
  `userId` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `role` enum('admin','user') NOT NULL DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `myrecords__users`
--

INSERT INTO `myrecords__users` (`userId`, `username`, `password`, `email`, `role`) VALUES
(1, 'Hhudgeon', '$2y$10$hV73BIGe6k.p2Ola5ISbje3bgQ4iyMjiPwADDCaeqN4.kbLhQc8f.', 'hhudgeon@gmail.com', 'admin'),
(4, 'testinglogin01', '$2y$10$HTJwIHkIUCd/.cKvYpeOUe.B0PiwJSfx4M.RosPKu9dwKlWJ/mQlW', 'hhudgeon@my.wctc.edu', 'user'),
(5, 'class', '$2y$10$mx8JGgWqCM4QBa6vQ4TI.eoXHGqx7Iz1/xPFiDSIuiwX.cjAXb77i', 'class@wctc.edu', 'user'),
(7, 'tyler', '$2y$10$hV73BIGe6k.p2Ola5ISbje3bgQ4iyMjiPwADDCaeqN4.kbLhQc8f.', 'tyler@tyler.com', 'user');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `myrecords__artists`
--
ALTER TABLE `myrecords__artists`
  ADD PRIMARY KEY (`artistId`);

--
-- Indexes for table `myrecords__artist_record`
--
ALTER TABLE `myrecords__artist_record`
  ADD PRIMARY KEY (`artistId`,`recordId`),
  ADD KEY `recordId` (`recordId`);

--
-- Indexes for table `myrecords__genres`
--
ALTER TABLE `myrecords__genres`
  ADD PRIMARY KEY (`genreId`);

--
-- Indexes for table `myrecords__recordLabel`
--
ALTER TABLE `myrecords__recordLabel`
  ADD PRIMARY KEY (`recordLabelId`);

--
-- Indexes for table `myrecords__records`
--
ALTER TABLE `myrecords__records`
  ADD PRIMARY KEY (`recordId`),
  ADD KEY `recordLabelId` (`recordLabelId`);

--
-- Indexes for table `myrecords__tracks`
--
ALTER TABLE `myrecords__tracks`
  ADD PRIMARY KEY (`trackId`),
  ADD KEY `recordId` (`recordId`),
  ADD KEY `artistId` (`artistId`),
  ADD KEY `genreId` (`genreId`);

--
-- Indexes for table `myrecords__users`
--
ALTER TABLE `myrecords__users`
  ADD PRIMARY KEY (`userId`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `myrecords__artists`
--
ALTER TABLE `myrecords__artists`
  MODIFY `artistId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `myrecords__genres`
--
ALTER TABLE `myrecords__genres`
  MODIFY `genreId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `myrecords__recordLabel`
--
ALTER TABLE `myrecords__recordLabel`
  MODIFY `recordLabelId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `myrecords__records`
--
ALTER TABLE `myrecords__records`
  MODIFY `recordId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `myrecords__tracks`
--
ALTER TABLE `myrecords__tracks`
  MODIFY `trackId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=92;

--
-- AUTO_INCREMENT for table `myrecords__users`
--
ALTER TABLE `myrecords__users`
  MODIFY `userId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `myrecords__artist_record`
--
ALTER TABLE `myrecords__artist_record`
  ADD CONSTRAINT `myrecords__artist_record_ibfk_1` FOREIGN KEY (`artistId`) REFERENCES `myrecords__artists` (`artistId`),
  ADD CONSTRAINT `myrecords__artist_record_ibfk_2` FOREIGN KEY (`recordId`) REFERENCES `myrecords__records` (`recordId`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
