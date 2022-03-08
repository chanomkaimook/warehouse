-- phpMyAdmin SQL Dump
-- version 4.8.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 25, 2022 at 03:52 PM
-- Server version: 10.1.33-MariaDB
-- PHP Version: 7.2.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `farmchokchai_steakhouse`
--

-- --------------------------------------------------------

--
-- Table structure for table `retail_stocksetting`
--

CREATE TABLE `retail_stocksetting` (
  `ID` int(10) UNSIGNED NOT NULL,
  `RETAIL_PRODUCTLIST_ID` int(11) DEFAULT NULL,
  `MIN` int(11) DEFAULT NULL,
  `MAX` int(11) DEFAULT NULL,
  `DATE_UPDATE` datetime DEFAULT NULL,
  `USER_UPDATE` varchar(5) DEFAULT NULL,
  `STATUS` tinyint(4) NOT NULL DEFAULT '1' COMMENT '0=off,1=on'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `retail_stocksetting`
--
ALTER TABLE `retail_stocksetting`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `RETAIL_PRODUCTLIST_ID` (`RETAIL_PRODUCTLIST_ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `retail_stocksetting`
--
ALTER TABLE `retail_stocksetting`
  MODIFY `ID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
