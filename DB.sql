-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 16, 2020 at 08:53 PM
-- Server version: 10.4.14-MariaDB
-- PHP Version: 7.4.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `idenacodes`
--

-- --------------------------------------------------------

--
-- Table structure for table `auth_discord`
--

CREATE TABLE `auth_discord` (
  `id` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `dc_creationDate` int(255) NOT NULL,
  `dc_username` varchar(255) NOT NULL,
  `dc_ID` int(255) NOT NULL,
  `time` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `auth_discord`
--


-- --------------------------------------------------------

--
-- Table structure for table `auth_idena`
--

CREATE TABLE `auth_idena` (
  `id` int(11) NOT NULL,
  `token` varchar(255) DEFAULT NULL,
  `nonce` varchar(255) DEFAULT NULL,
  `sig` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `authenticated` int(2) NOT NULL DEFAULT 0,
  `time` timestamp NOT NULL DEFAULT current_timestamp(),
  `pubKey` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `auth_idena`
--

-- --------------------------------------------------------

--
-- Table structure for table `auth_telegram`
--

CREATE TABLE `auth_telegram` (
  `id` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `tg_ID` int(20) NOT NULL,
  `tg_Username` varchar(255) DEFAULT NULL,
  `time` int(11) NOT NULL DEFAULT current_timestamp(),
  `tg_creationDate` int(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `auth_telegram`
--



-- --------------------------------------------------------

--
-- Table structure for table `auth_twitter`
--

CREATE TABLE `auth_twitter` (
  `id` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `time` timestamp NOT NULL DEFAULT current_timestamp(),
  `tw_creationDate` int(255) NOT NULL,
  `tw_ID` int(255) NOT NULL,
  `tw_username` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `auth_twitter`
--



-- --------------------------------------------------------

--
-- Table structure for table `bought_users`
--

CREATE TABLE `bought_users` (
  `id` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `boughtUserID` int(11) NOT NULL,
  `price` decimal(5,5) NOT NULL DEFAULT 0.00000,
  `time` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `config`
--

CREATE TABLE `config` (
  `id` int(11) NOT NULL,
  `key` varchar(255) NOT NULL,
  `value` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `config`
--

INSERT INTO `config` (`id`, `key`, `value`) VALUES
(1, 'epoch', '57'),
(2, 'maintenance', 'false'),
(3, 'godAddress', '0xcbb98843270812eeCE07BFb82d26b4881a33aA91'),
(6, 'validationTime', '2020-11-15T13:30:00Z'),
(8, 'blockHeight', '2070000'),
(9, 'validation_1_Rewards', '0'),
(10, 'validation_2_Rewards', '0'),
(11, 'validation_3_Rewards', '0');

-- --------------------------------------------------------

--
-- Table structure for table `deposits`
--

CREATE TABLE `deposits` (
  `id` int(11) NOT NULL,
  `txHash` varchar(255) NOT NULL,
  `userID` int(11) DEFAULT NULL,
  `credited` int(2) NOT NULL DEFAULT 0,
  `amount` float NOT NULL DEFAULT 0,
  `address` varchar(42) NOT NULL,
  `time` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `flips`
--

CREATE TABLE `flips` (
  `id` int(11) NOT NULL,
  `url` varchar(255) NOT NULL,
  `enabled` int(11) NOT NULL DEFAULT 1,
  `addedBy` int(11) NOT NULL DEFAULT 0,
  `answer` int(2) DEFAULT NULL,
  `url2` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `history`
--

CREATE TABLE `history` (
  `id` int(11) NOT NULL,
  `text` varchar(600) NOT NULL,
  `userID` int(11) NOT NULL,
  `time` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `invites`
--

CREATE TABLE `invites` (
  `id` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `forID` int(11) NOT NULL,
  `time` timestamp NOT NULL DEFAULT current_timestamp(),
  `epoch` int(11) NOT NULL,
  `validations` int(1) DEFAULT NULL,
  `address_1` varchar(42) DEFAULT NULL,
  `address_2` varchar(42) DEFAULT NULL,
  `address_3` varchar(42) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `invoices`
--

CREATE TABLE `invoices` (
  `id` int(11) NOT NULL,
  `epoch` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `paid` int(2) NOT NULL DEFAULT 0,
  `time` timestamp NOT NULL DEFAULT current_timestamp(),
  `amount` float NOT NULL,
  `payTime` timestamp NULL DEFAULT NULL,
  `info` varchar(3000) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `questions`
--

CREATE TABLE `questions` (
  `id` int(11) NOT NULL,
  `question` varchar(3000) DEFAULT NULL,
  `addedBy` int(11) DEFAULT NULL,
  `enabled` int(1) NOT NULL,
  `options` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`options`)),
  `answer` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

CREATE TABLE `reports` (
  `id` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `reason` varchar(255) NOT NULL,
  `time` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `reports_tickets`
--

CREATE TABLE `reports_tickets` (
  `id` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `report` text NOT NULL,
  `reporterID` int(11) NOT NULL,
  `time` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `reports_tickets`
--



-- --------------------------------------------------------

--
-- Table structure for table `test_flips`
--

CREATE TABLE `test_flips` (
  `id` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `flips` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`flips`)),
  `score` int(3) DEFAULT NULL,
  `time` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `test_flips`
--



-- --------------------------------------------------------

--
-- Table structure for table `test_questions`
--

CREATE TABLE `test_questions` (
  `id` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `questions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`questions`)),
  `score` int(3) DEFAULT NULL,
  `time` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `test_questions`
--



-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `address` varchar(64) NOT NULL,
  `status` varchar(16) DEFAULT 'Undefined',
  `joined` date NOT NULL DEFAULT current_timestamp(),
  `pubKey` varchar(255) DEFAULT NULL,
  `lastseen` date NOT NULL DEFAULT current_timestamp(),
  `type` int(3) NOT NULL DEFAULT 0,
  `image` varchar(255) NOT NULL,
  `balance` float NOT NULL DEFAULT 0,
  `username` varchar(15) DEFAULT NULL,
  `banned` int(1) NOT NULL DEFAULT 0,
  `ip` varchar(20) DEFAULT NULL,
  `country` varchar(60) DEFAULT NULL,
  `views` int(11) NOT NULL DEFAULT 0,
  `flag` int(2) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users`
--


-- --------------------------------------------------------

--
-- Table structure for table `votes`
--

CREATE TABLE `votes` (
  `id` int(11) NOT NULL,
  `voterID` int(11) NOT NULL,
  `type` int(11) NOT NULL,
  `forID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `votes`
--



--
-- Indexes for dumped tables
--

--
-- Indexes for table `auth_discord`
--
ALTER TABLE `auth_discord`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `auth_idena`
--
ALTER TABLE `auth_idena`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `auth_telegram`
--
ALTER TABLE `auth_telegram`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `auth_twitter`
--
ALTER TABLE `auth_twitter`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bought_users`
--
ALTER TABLE `bought_users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `config`
--
ALTER TABLE `config`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `deposits`
--
ALTER TABLE `deposits`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `flips`
--
ALTER TABLE `flips`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `history`
--
ALTER TABLE `history`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `invites`
--
ALTER TABLE `invites`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `invoices`
--
ALTER TABLE `invoices`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reports_tickets`
--
ALTER TABLE `reports_tickets`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `test_flips`
--
ALTER TABLE `test_flips`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `test_questions`
--
ALTER TABLE `test_questions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `votes`
--
ALTER TABLE `votes`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `auth_discord`
--
ALTER TABLE `auth_discord`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `auth_idena`
--
ALTER TABLE `auth_idena`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `auth_telegram`
--
ALTER TABLE `auth_telegram`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `auth_twitter`
--
ALTER TABLE `auth_twitter`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bought_users`
--
ALTER TABLE `bought_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `config`
--
ALTER TABLE `config`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `deposits`
--
ALTER TABLE `deposits`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `flips`
--
ALTER TABLE `flips`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `history`
--
ALTER TABLE `history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `invites`
--
ALTER TABLE `invites`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `invoices`
--
ALTER TABLE `invoices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `questions`
--
ALTER TABLE `questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reports`
--
ALTER TABLE `reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reports_tickets`
--
ALTER TABLE `reports_tickets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `test_flips`
--
ALTER TABLE `test_flips`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `test_questions`
--
ALTER TABLE `test_questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `votes`
--
ALTER TABLE `votes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
