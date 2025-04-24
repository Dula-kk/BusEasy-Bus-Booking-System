-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 08, 2025 at 01:43 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `buseasy2`
--

-- --------------------------------------------------------

--
-- Table structure for table `contact`
--

CREATE TABLE `contact` (
  `contactid` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contact`
--

INSERT INTO `contact` (`contactid`, `name`, `email`, `message`, `created_at`) VALUES
(600, 'Dulmini Kumari', 'dulminikumari2002@gmail.com', 'Please add more routes', '2025-02-05 17:30:52'),
(601, 'Geeshanu Gayan', 'gayangunarathna62@gmail.com', 'Very good service and easy to use!', '2025-02-06 02:34:41'),
(602, 'Mahee Shehara', 'dulminikumari2002@gmail.com', 'I lost my pocket in the bus', '2025-02-06 03:49:05'),
(603, 'Pooja Sathsarani', 'dulminikumari2002@gmail.com', 'Please add more routes.', '2025-02-06 03:57:50'),
(604, 'Imasha Kavindi', 'gayangunarathna62@gmail.com', 'Could I apply refunds?', '2025-02-06 05:51:15'),
(605, 'Dulmini Kumari', 'dulminikumari2002@gmail.com', 'hhii', '2025-02-06 09:09:21'),
(606, 'Dulmini Kumari', 'dulminikumari2002@gmail.com', 'I lost my wallet in the bus. Please help me.', '2025-02-07 05:54:21'),
(607, 'Sarayu Nuravi', 'gayangunarathna62@gmail.com', 'Hello', '2025-02-07 06:32:54'),
(608, 'Dulmini Kumari', 'dulminikumari2002@gmail.com', 'Hiii', '2025-02-08 11:27:27');

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `feedbackid` int(11) NOT NULL,
  `reservationid` int(11) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`feedbackid`, `reservationid`, `message`, `created_at`) VALUES
(510, 325, 'hello', '2025-02-07 06:46:23'),
(511, 325, 'Very good Service', '2025-02-07 06:46:45'),
(512, 328, 'Comfortable Journey', '2025-02-07 06:57:00'),
(513, 326, 'ffffff', '2025-02-07 08:36:46'),
(514, 325, 'Very good service. Highly Recommend', '2025-02-08 11:42:48');

-- --------------------------------------------------------

--
-- Table structure for table `journey`
--

CREATE TABLE `journey` (
  `journeyid` int(11) NOT NULL,
  `route` varchar(255) NOT NULL,
  `departure_time` time NOT NULL,
  `arrival_time` time NOT NULL,
  `date` date NOT NULL,
  `fee` decimal(10,2) NOT NULL,
  `status` enum('scheduled','cancelled','completed','pending') DEFAULT 'pending',
  `conductorid` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `journey`
--

INSERT INTO `journey` (`journeyid`, `route`, `departure_time`, `arrival_time`, `date`, `fee`, `status`, `conductorid`, `created_at`) VALUES
(216, 'Maharagma-Kiridiwela', '17:30:00', '18:40:00', '2025-02-10', 100.00, 'completed', 117, '2025-02-07 06:27:44'),
(217, 'Colombo-Jaffna', '12:00:00', '16:40:00', '2025-02-10', 700.00, 'completed', 117, '2025-02-07 06:28:25'),
(218, 'Colombo-Galle', '13:00:00', '14:35:00', '2025-02-12', 500.00, 'completed', 117, '2025-02-07 06:29:06'),
(219, 'Colombo-Anuradhapura', '13:50:00', '17:55:00', '2025-02-12', 700.00, 'scheduled', 117, '2025-02-07 08:20:30'),
(220, 'Colombo-Jaffna', '22:00:00', '02:00:00', '2025-02-10', 900.00, 'scheduled', 117, '2025-02-08 11:28:35'),
(221, 'Colombo-Galle', '08:00:00', '09:10:00', '2025-02-09', 600.00, 'scheduled', 117, '2025-02-08 11:30:35');

-- --------------------------------------------------------

--
-- Table structure for table `payment`
--

CREATE TABLE `payment` (
  `paymentid` int(11) NOT NULL,
  `reservationid` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `payment_method` enum('card','cash','online') NOT NULL,
  `status` enum('pending','completed','rejected') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payment`
--

INSERT INTO `payment` (`paymentid`, `reservationid`, `amount`, `payment_date`, `payment_method`, `status`) VALUES
(416, 325, 200.00, '2025-02-07 06:30:06', 'card', 'completed'),
(417, 326, 600.00, '2025-02-07 06:31:08', 'cash', 'pending'),
(418, 327, 1500.00, '2025-02-07 06:54:04', 'card', 'pending'),
(419, 328, 600.00, '2025-02-07 06:54:26', 'cash', 'pending'),
(420, 329, 800.00, '2025-02-07 08:38:39', 'cash', 'completed'),
(424, 333, 700.00, '2025-02-08 11:41:20', 'card', 'completed');

-- --------------------------------------------------------

--
-- Table structure for table `refund`
--

CREATE TABLE `refund` (
  `refundid` int(11) NOT NULL,
  `reservationid` int(11) NOT NULL,
  `status` enum('requested','approved','rejected') DEFAULT 'requested'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `refund`
--

INSERT INTO `refund` (`refundid`, `reservationid`, `status`) VALUES
(725, 326, 'approved'),
(726, 327, 'approved'),
(727, 333, 'approved');

-- --------------------------------------------------------

--
-- Table structure for table `reservation`
--

CREATE TABLE `reservation` (
  `reservationid` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `journeyid` int(11) NOT NULL,
  `seats` varchar(100) NOT NULL,
  `payment_status` enum('pending','completed','failed') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reservation`
--

INSERT INTO `reservation` (`reservationid`, `userid`, `journeyid`, `seats`, `payment_status`, `created_at`) VALUES
(325, 116, 216, '5', '', '2025-02-07 06:29:28'),
(326, 116, 218, '17', '', '2025-02-07 06:31:01'),
(327, 118, 217, '6,5', '', '2025-02-07 06:53:34'),
(328, 118, 218, '20', '', '2025-02-07 06:54:21'),
(329, 116, 217, '10', '', '2025-02-07 08:18:31'),
(333, 116, 221, '1', '', '2025-02-08 11:35:11');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `userid` int(11) NOT NULL,
  `firstname` varchar(100) NOT NULL,
  `lastname` varchar(100) NOT NULL,
  `contactnumber` varchar(15) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('conductor','passenger') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`userid`, `firstname`, `lastname`, `contactnumber`, `username`, `password`, `role`, `created_at`) VALUES
(116, 'Dulmini', 'Kumari', '0705229031', 'dula', '$2y$10$oObdX7ErHUNwlcS30/DMheqjWl6pkwAuSmwpyLnhmHZGfOAojqlLK', 'passenger', '2025-02-07 06:26:22'),
(117, 'Mahesh', 'Ekanayake', '0705229030', 'mahesh', '$2y$10$FNUmzIMrMgHMUB.GxoNuveVw2MlFKbi7X0my5b.mG/peIiq6SP6BS', 'conductor', '2025-02-07 06:26:41'),
(118, 'Pamodi', 'Anashya', '0778442568', 'pamodi', '$2y$10$03y9oznuUPzfnOGYRqxWw.TwTXlbsUAdIhYewoz13iRssGnqfEsOi', 'passenger', '2025-02-07 06:52:46');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `contact`
--
ALTER TABLE `contact`
  ADD PRIMARY KEY (`contactid`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`feedbackid`),
  ADD KEY `reservationid` (`reservationid`);

--
-- Indexes for table `journey`
--
ALTER TABLE `journey`
  ADD PRIMARY KEY (`journeyid`),
  ADD KEY `conductorid` (`conductorid`);

--
-- Indexes for table `payment`
--
ALTER TABLE `payment`
  ADD PRIMARY KEY (`paymentid`),
  ADD KEY `reservationid` (`reservationid`);

--
-- Indexes for table `refund`
--
ALTER TABLE `refund`
  ADD PRIMARY KEY (`refundid`),
  ADD KEY `reservationid` (`reservationid`);

--
-- Indexes for table `reservation`
--
ALTER TABLE `reservation`
  ADD PRIMARY KEY (`reservationid`),
  ADD KEY `userid` (`userid`),
  ADD KEY `journeyid` (`journeyid`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`userid`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `contact`
--
ALTER TABLE `contact`
  MODIFY `contactid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=609;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `feedbackid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=515;

--
-- AUTO_INCREMENT for table `journey`
--
ALTER TABLE `journey`
  MODIFY `journeyid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=222;

--
-- AUTO_INCREMENT for table `payment`
--
ALTER TABLE `payment`
  MODIFY `paymentid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=425;

--
-- AUTO_INCREMENT for table `refund`
--
ALTER TABLE `refund`
  MODIFY `refundid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=728;

--
-- AUTO_INCREMENT for table `reservation`
--
ALTER TABLE `reservation`
  MODIFY `reservationid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=334;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `userid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=119;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `feedback`
--
ALTER TABLE `feedback`
  ADD CONSTRAINT `feedback_ibfk_1` FOREIGN KEY (`reservationid`) REFERENCES `reservation` (`reservationid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `journey`
--
ALTER TABLE `journey`
  ADD CONSTRAINT `journey_ibfk_1` FOREIGN KEY (`conductorid`) REFERENCES `user` (`userid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `payment`
--
ALTER TABLE `payment`
  ADD CONSTRAINT `payment_ibfk_1` FOREIGN KEY (`reservationid`) REFERENCES `reservation` (`reservationid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `refund`
--
ALTER TABLE `refund`
  ADD CONSTRAINT `refund_ibfk_1` FOREIGN KEY (`reservationid`) REFERENCES `payment` (`reservationid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `reservation`
--
ALTER TABLE `reservation`
  ADD CONSTRAINT `reservation_ibfk_1` FOREIGN KEY (`userid`) REFERENCES `user` (`userid`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `reservation_ibfk_2` FOREIGN KEY (`journeyid`) REFERENCES `journey` (`journeyid`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
