-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 10, 2025 at 02:31 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `baryotap`
--

-- --------------------------------------------------------

--
-- Table structure for table `document_request`
--

CREATE TABLE `document_request` (
  `Request_ID` int(11) NOT NULL,
  `User_ID` int(11) NOT NULL,
  `Document_Type` varchar(100) NOT NULL,
  `Date_Requested` date NOT NULL,
  `Status` varchar(50) NOT NULL DEFAULT 'Pending',
  `Fullname` varchar(255) NOT NULL,
  `Resident_Address` varchar(255) NOT NULL,
  `Purpose_of_Request` text DEFAULT NULL,
  `Indigency_Reason` text DEFAULT NULL,
  `Years_of_Residency` int(11) DEFAULT NULL,
  `Business_Name` varchar(255) DEFAULT NULL,
  `Business_Address` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `document_request`
--

INSERT INTO `document_request` (`Request_ID`, `User_ID`, `Document_Type`, `Date_Requested`, `Status`, `Fullname`, `Resident_Address`, `Purpose_of_Request`, `Indigency_Reason`, `Years_of_Residency`, `Business_Name`, `Business_Address`) VALUES
(1, 1, 'Barangay Clearance', '2025-12-04', 'Pending', 'Marc Calvo', '', 'scholarship', '', 0, '', ''),
(2, 1, 'Barangay Indigency', '2025-12-04', 'For Pickup', 'Cheryl Jane Geoman', 'Lahug', '', 'School', 0, '', ''),
(3, 1, 'Barangay Business Permit', '2025-12-05', 'Pending', 'Sample Ra', '', '', '', 0, 'Vegtetable Retail', 'Lahug, Mantalongon, Dalaguete, Cebu'),
(4, 1, 'Barangay Clearance', '2025-12-05', 'Pending', 'Cheryl Jane Geoman', 'Alang-alang', 'school purposes', '', 0, '', ''),
(5, 1, 'Barangay Clearance', '2025-12-05', 'Pending', 'Kharen Revillas', 'Alang-alang', 'wala ra magkuha rako', '', 0, '', ''),
(6, 1, 'Barangay Indigency', '2025-12-05', 'For Pickup', 'Jenisel Carinoza', 'Mag-alambac', '', 'School', 0, '', '');

-- --------------------------------------------------------

--
-- Table structure for table `emergency_contact`
--

CREATE TABLE `emergency_contact` (
  `Contact_ID` int(11) NOT NULL,
  `Contact_Name` varchar(150) DEFAULT NULL,
  `Department` varchar(150) DEFAULT NULL,
  `Phone_Number` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `emergency_contact_log`
--

CREATE TABLE `emergency_contact_log` (
  `Log_ID` int(11) NOT NULL,
  `User_ID` int(11) DEFAULT NULL,
  `Contact_ID` int(11) DEFAULT NULL,
  `Communication_Type` varchar(100) DEFAULT NULL,
  `Date_Time` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `history_log`
--

CREATE TABLE `history_log` (
  `log_id` int(11) NOT NULL,
  `table_name` varchar(50) NOT NULL,
  `record_id` int(11) NOT NULL,
  `action_type` varchar(10) NOT NULL,
  `action_details` text DEFAULT NULL,
  `action_timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `history_log`
--

INSERT INTO `history_log` (`log_id`, `table_name`, `record_id`, `action_type`, `action_details`, `action_timestamp`) VALUES
(1, 'users', 14, 'INSERT', 'New user registered: ren revillas (Initial Status: Approved)', '2025-12-05 00:36:02'),
(2, 'users', 15, 'INSERT', 'New user registered: Jes Bus (Initial Status: Approved)', '2025-12-05 01:31:57'),
(3, 'users', 16, 'INSERT', 'New user registered: Jenisel Carinoza (Initial Status: Approved)', '2025-12-05 03:18:07');

-- --------------------------------------------------------

--
-- Table structure for table `news_and_updates`
--

CREATE TABLE `news_and_updates` (
  `News_ID` int(11) NOT NULL,
  `Title` varchar(255) NOT NULL,
  `Category` varchar(50) NOT NULL,
  `Content` text NOT NULL,
  `Posted_By_ID` int(11) DEFAULT NULL,
  `Posted_By_Role` varchar(50) DEFAULT 'Admin',
  `Date_Published` date NOT NULL,
  `Is_Active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `news_and_updates`
--

INSERT INTO `news_and_updates` (`News_ID`, `Title`, `Category`, `Content`, `Posted_By_ID`, `Posted_By_Role`, `Date_Published`, `Is_Active`) VALUES
(2, 'Road Repair', 'Announcement', 'Road repair is happening in Sitio Sua', 1, 'Admin', '2025-12-04', 1),
(3, 'New Traffic System', 'Announcement', 'Please be guided that a new traffic system is ....', 1, 'Admin', '2025-12-04', 1),
(4, 'Health Checkup', 'Event', 'Free health checkup on Dec 5 in covered court', 1, 'Admin', '2025-12-04', 1);

-- --------------------------------------------------------

--
-- Table structure for table `notification`
--

CREATE TABLE `notification` (
  `Notification_ID` int(11) NOT NULL,
  `User_ID` int(11) DEFAULT NULL,
  `Message` text DEFAULT NULL,
  `Date_Sent` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `report`
--

CREATE TABLE `report` (
  `Report_ID` int(11) NOT NULL,
  `User_ID` int(11) DEFAULT NULL,
  `Category` varchar(100) DEFAULT NULL,
  `Description` text DEFAULT NULL,
  `Photo_URL` varchar(255) DEFAULT NULL,
  `Location` varchar(255) DEFAULT NULL,
  `Reported_Date` datetime NOT NULL DEFAULT current_timestamp(),
  `Status` enum('Pending','In Progress','Resolved') NOT NULL DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `report`
--

INSERT INTO `report` (`Report_ID`, `User_ID`, `Category`, `Description`, `Photo_URL`, `Location`, `Reported_Date`, `Status`) VALUES
(1, NULL, 'Garbage', 'hugaw mn', NULL, 'Upper Lahug', '2025-12-01 15:45:40', 'Pending'),
(2, NULL, 'Road', 'lubaklubak', 'uploads/1764431091_692b14f321204.jpg', 'Sampig', '2025-12-01 15:45:40', 'Pending'),
(3, 5, 'Streetlight', 'dli sya hayag bati sya', 'uploads/1764571280_692d3890e7d62.jpg', 'Catambisan', '2025-12-01 15:45:40', 'Pending'),
(4, 5, 'Garbage', 'gibungkag sa irong boang', 'uploads/1764571746_692d3a62e1058.jpg', 'Lapa', '2025-12-01 15:45:40', 'Pending'),
(5, NULL, 'Garbage', 'hugaw ha labay tka ron', 'uploads/1764575280_692d483006621.jpg', 'Upper Lahug', '2025-12-01 15:48:00', 'In Progress'),
(6, NULL, 'Streetlight', 'wala misiga', 'uploads/1764851217_69317e1184818.png', 'St.Ñino', '2025-12-04 20:26:57', 'Pending'),
(7, NULL, 'Garbage', 'need na kuhaon', 'uploads/1764875989_6931ded5ac0dd.png', 'Sampig', '2025-12-05 03:19:49', 'Pending'),
(8, NULL, 'Water', 'naay na putol', 'uploads/1764876134_6931df667b17c.png', 'Granchina', '2025-12-05 03:22:14', 'Pending'),
(9, NULL, 'Streetlight', 'naguba ang dapit sa amoa', 'uploads/1764895059_69322953c8187.png', 'Alang-Alang', '2025-12-05 08:37:39', 'Pending'),
(10, NULL, 'Garbage', 'Need na kuhaon', NULL, 'Mag-alambac', '2025-12-05 11:20:25', 'Resolved');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `User_ID` int(11) NOT NULL,
  `First_Name` varchar(100) DEFAULT NULL,
  `Last_Name` varchar(100) DEFAULT NULL,
  `Email` varchar(150) DEFAULT NULL,
  `Profile_Picture_URL` varchar(255) DEFAULT NULL,
  `Password_hash` varchar(255) DEFAULT NULL,
  `Role` varchar(50) DEFAULT 'users',
  `Validation_Status` enum('Approved','Denied') DEFAULT 'Approved',
  `validation_pic` varchar(255) DEFAULT NULL,
  `Full_Address` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`User_ID`, `First_Name`, `Last_Name`, `Email`, `Profile_Picture_URL`, `Password_hash`, `Role`, `Validation_Status`, `validation_pic`, `Full_Address`) VALUES
(1, 'Marco', 'Calvo', 'marccalvo123@gmail.com', 'uploads/profile_1_1764865123.png', '$2y$10$zcExpkZAqb35A7oDoiP1HuguH3eOUifu9LpxlYUWqUlxXj1OKgwd.', 'users', '', 'uploads/validation_1_1764865023.png', 'Lahug, Mantalongon, Dalaguete, Cebu'),
(2, 'Jake', 'Villcensio', 'jake123@gmail.com', NULL, '$2y$10$AGIP9Sh1usLS9vbScAOvJePf0gSK89x1jRrEznHDOfWdp7WcODMlu', 'users', 'Approved', NULL, NULL),
(3, 'Jessel', 'Bustamante', 'Jessel18@gmail.com', NULL, '$2y$10$2rS6WxsNUfnRRWJDbx6H6e22JV2VBfPH/gL2k41Zq.KWiI7gsIoDG', 'users', 'Approved', NULL, NULL),
(4, 'Super', 'Admin', 'admin@gmail.com', NULL, '$2y$10$t7IZ59sylFjolWmajjpWPOWEoChKpJk5XKU4ntom9GJdTsAORoABq', 'admin', 'Approved', NULL, NULL),
(5, 'Cheryl ', 'Geo', 'che@gmail.com', NULL, '$2y$10$lVQ/lkBi01TR.JqDEIPC4u/SJ9igu9VjOrOJFAUi2ahL5sdypk/bm', 'users', '', NULL, 'Alang, Mantalongon, Dalaguete, Cebu'),
(6, 'kuan', 'bah', 'ba@gmail.com', NULL, '$2y$10$oNkuMvveB2B83LBs39NMNu8j7CDDSARv5bzR37sdzlIY4hV0fcXDu', 'users', 'Approved', NULL, NULL),
(9, 'adim', 'skiee', 'adim@gmail.com', NULL, '$2y$10$el.N.oom.5i4wegIg9GY4Ob31xnrhVnvpcmfUOlhsGFHvxobBhq9.', 'users', 'Approved', NULL, NULL),
(10, 'Kharen', 'Revillas', 'kharen@gmail.com', NULL, '$2y$10$b6gqOH1o5ybARADu26sc4uuliMaE/.gST5oyBj.FdXR8OKHzoDvVO', 'users', 'Approved', NULL, NULL),
(11, 'Kharen', 'Revillas', 'kharen1@gmail.com', NULL, '$2y$10$UYBOtr.nRHpxiiQ6Ou9hwefjW1vDoP0pCHHqwqdcoBYgven.v122y', 'users', 'Approved', NULL, NULL),
(12, 'Sample', 'Ra', 'ra@gmail.com', 'uploads/profile_12_1764890225.png', '$2y$10$iKNGp5HIQ6XQCP3nmHimAeB58ZuGdo/Nr.MKwdAEN6vLBB2u5zo8.', 'users', '', 'uploads/validation_12_1764902529.png', 'Lahug, Mantalongon, Dalaguete, Cebu'),
(13, 'Jessel', 'Ate', 'ate@gmail.com', NULL, '$2y$10$H2nHdB1HqUeZrWZn3bh0ruTaucJbI.IMoxetwazCMZGjfz/JpV0D2', 'users', 'Approved', NULL, NULL),
(14, 'ren', 'revillas', 'kharen16@gmail.com', 'uploads/profile_14_1764895026.png', '$2y$10$UNIzPs3jU1PmLoNQhetW4uMilrWSw3Jh63AsyfWTleEhMGxDjLBXC', 'users', 'Approved', NULL, 'Alang-alang'),
(15, 'Jes', 'Bus', 'jes@gmail.com', 'uploads/profile_15_1764898638.png', '$2y$10$BIQJCFrXGC/ZkXLQZHlUuOAh.7oVyB1VV6j1x4nodPZyWy.s5nOI.', 'users', 'Approved', NULL, 'Private,Mantalongon'),
(16, 'Jenisel', 'Carinoza', 'ann@gmail.com', 'uploads/profile_16_1765125396.png', '$2y$10$SJfR3lIGdd.Hw7.FAqqSDeN0T65VxRKc4LolZYWKYSDQQ1qdxAZ6C', 'users', '', 'uploads/validation_16_1764905036.png', 'Mag-alambac');

--
-- Triggers `users`
--
DELIMITER $$
CREATE TRIGGER `trg_users_after_insert` AFTER INSERT ON `users` FOR EACH ROW INSERT INTO `history_log` (table_name, record_id, action_type, action_details)
VALUES (
    'users',
    NEW.User_ID, -- The ID of the new user
    'INSERT',
    CONCAT(
        'New user registered: ', 
        NEW.First_Name, ' ', NEW.Last_Name, 
        ' (Initial Status: ', NEW.Validation_Status, ')'
    )
)
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `vegetable_price`
--

CREATE TABLE `vegetable_price` (
  `Price_ID` int(11) NOT NULL,
  `Vegetable_Name` varchar(100) DEFAULT NULL,
  `Price` decimal(10,2) DEFAULT NULL,
  `Date_Updated` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vegetable_price`
--

INSERT INTO `vegetable_price` (`Price_ID`, `Vegetable_Name`, `Price`, `Date_Updated`) VALUES
(5, 'Repolyo', 41.27, '2025-11-01'),
(6, 'Repolyo', 42.16, '2025-11-02'),
(7, 'Repolyo', 45.92, '2025-11-03'),
(8, 'Repolyo', 45.89, '2025-11-04'),
(9, 'Repolyo', 42.24, '2025-11-05'),
(10, 'Repolyo', 47.97, '2025-11-06'),
(11, 'Repolyo', 50.91, '2025-11-07'),
(12, 'Repolyo', 45.03, '2025-11-08'),
(13, 'Repolyo', 45.61, '2025-11-09'),
(14, 'Repolyo', 47.13, '2025-11-10'),
(15, 'Repolyo', 49.33, '2025-11-11'),
(16, 'Repolyo', 44.88, '2025-11-12'),
(17, 'Repolyo', 48.60, '2025-11-13'),
(18, 'Repolyo', 44.02, '2025-11-14'),
(19, 'Repolyo', 48.06, '2025-11-15'),
(20, 'Repolyo', 46.51, '2025-11-16'),
(21, 'Repolyo', 45.31, '2025-11-17'),
(22, 'Repolyo', 47.97, '2025-11-18'),
(23, 'Repolyo', 46.22, '2025-11-19'),
(24, 'Repolyo', 41.28, '2025-11-20'),
(25, 'Repolyo', 43.51, '2025-11-21'),
(26, 'Repolyo', 42.44, '2025-11-22'),
(27, 'Repolyo', 48.02, '2025-11-23'),
(28, 'Repolyo', 49.72, '2025-11-24'),
(29, 'Repolyo', 47.28, '2025-11-25'),
(30, 'Repolyo', 42.88, '2025-11-26'),
(31, 'Repolyo', 48.33, '2025-11-27'),
(32, 'Repolyo', 47.88, '2025-11-28'),
(33, 'Repolyo', 49.82, '2025-11-29'),
(34, 'Repolyo', 45.38, '2025-11-30'),
(35, 'Repolyo', 44.97, '2025-12-01'),
(36, 'Repolyo', 44.75, '2025-12-02'),
(37, 'Petchay', 25.12, '2025-11-01'),
(38, 'Petchay', 28.32, '2025-11-02'),
(39, 'Petchay', 25.86, '2025-11-03'),
(40, 'Petchay', 28.58, '2025-11-04'),
(41, 'Petchay', 25.10, '2025-11-05'),
(42, 'Petchay', 25.43, '2025-11-06'),
(43, 'Petchay', 27.60, '2025-11-07'),
(44, 'Petchay', 31.78, '2025-11-08'),
(45, 'Petchay', 32.54, '2025-11-09'),
(46, 'Petchay', 33.72, '2025-11-10'),
(47, 'Petchay', 27.75, '2025-11-11'),
(48, 'Petchay', 28.79, '2025-11-12'),
(49, 'Petchay', 33.91, '2025-11-13'),
(50, 'Petchay', 32.06, '2025-11-14'),
(51, 'Petchay', 26.68, '2025-11-15'),
(52, 'Petchay', 32.89, '2025-11-16'),
(53, 'Petchay', 29.35, '2025-11-17'),
(54, 'Petchay', 26.83, '2025-11-18'),
(55, 'Petchay', 27.76, '2025-11-19'),
(56, 'Petchay', 28.16, '2025-11-20'),
(57, 'Petchay', 33.36, '2025-11-21'),
(58, 'Petchay', 31.25, '2025-11-22'),
(59, 'Petchay', 34.69, '2025-11-23'),
(60, 'Petchay', 34.25, '2025-11-24'),
(61, 'Petchay', 31.55, '2025-11-25'),
(62, 'Petchay', 25.13, '2025-11-26'),
(63, 'Petchay', 32.25, '2025-11-27'),
(64, 'Petchay', 27.27, '2025-11-28'),
(65, 'Petchay', 32.90, '2025-11-29'),
(66, 'Petchay', 26.43, '2025-11-30'),
(67, 'Petchay', 34.69, '2025-12-01'),
(68, 'Petchay', 33.24, '2025-12-02'),
(69, 'Camote', 39.55, '2025-11-01'),
(70, 'Camote', 44.86, '2025-11-02'),
(71, 'Camote', 35.84, '2025-11-03'),
(72, 'Camote', 44.75, '2025-11-04'),
(73, 'Camote', 39.99, '2025-11-05'),
(74, 'Camote', 39.06, '2025-11-06'),
(75, 'Camote', 42.14, '2025-11-07'),
(76, 'Camote', 41.77, '2025-11-08'),
(77, 'Camote', 42.19, '2025-11-09'),
(78, 'Camote', 37.94, '2025-11-10'),
(79, 'Camote', 37.10, '2025-11-11'),
(80, 'Camote', 38.30, '2025-11-12'),
(81, 'Camote', 44.17, '2025-11-13'),
(82, 'Camote', 42.20, '2025-11-14'),
(83, 'Camote', 43.19, '2025-11-15'),
(84, 'Camote', 42.94, '2025-11-16'),
(85, 'Camote', 36.92, '2025-11-17'),
(86, 'Camote', 41.72, '2025-11-18'),
(87, 'Camote', 41.02, '2025-11-19'),
(88, 'Camote', 44.57, '2025-11-20'),
(89, 'Camote', 39.57, '2025-11-21'),
(90, 'Camote', 36.00, '2025-11-22'),
(91, 'Camote', 35.15, '2025-11-23'),
(92, 'Camote', 41.25, '2025-11-24'),
(93, 'Camote', 42.04, '2025-11-25'),
(94, 'Camote', 43.61, '2025-11-26'),
(95, 'Camote', 38.53, '2025-11-27'),
(96, 'Camote', 42.48, '2025-11-28'),
(97, 'Camote', 38.52, '2025-11-29'),
(98, 'Camote', 43.02, '2025-11-30'),
(99, 'Camote', 44.84, '2025-12-01'),
(100, 'Camote', 41.67, '2025-12-02'),
(101, 'Carrots', 96.09, '2025-11-01'),
(102, 'Carrots', 96.90, '2025-11-02'),
(103, 'Carrots', 90.73, '2025-11-03'),
(104, 'Carrots', 92.54, '2025-11-04'),
(105, 'Carrots', 92.53, '2025-11-05'),
(106, 'Carrots', 94.62, '2025-11-06'),
(107, 'Carrots', 95.82, '2025-11-07'),
(108, 'Carrots', 92.85, '2025-11-08'),
(109, 'Carrots', 98.48, '2025-11-09'),
(110, 'Carrots', 90.50, '2025-11-10'),
(111, 'Carrots', 92.70, '2025-11-11'),
(112, 'Carrots', 92.09, '2025-11-12'),
(113, 'Carrots', 90.27, '2025-11-13'),
(114, 'Carrots', 96.53, '2025-11-14'),
(115, 'Carrots', 93.30, '2025-11-15'),
(116, 'Carrots', 90.47, '2025-11-16'),
(117, 'Carrots', 92.35, '2025-11-17'),
(118, 'Carrots', 99.42, '2025-11-18'),
(119, 'Carrots', 94.97, '2025-11-19'),
(120, 'Carrots', 99.20, '2025-11-20'),
(121, 'Carrots', 91.56, '2025-11-21'),
(122, 'Carrots', 92.21, '2025-11-22'),
(123, 'Carrots', 93.12, '2025-11-23'),
(124, 'Carrots', 97.46, '2025-11-24'),
(125, 'Carrots', 90.81, '2025-11-25'),
(126, 'Carrots', 91.31, '2025-11-26'),
(127, 'Carrots', 99.27, '2025-11-27'),
(128, 'Carrots', 96.65, '2025-11-28'),
(129, 'Carrots', 94.39, '2025-11-29'),
(130, 'Carrots', 97.43, '2025-11-30'),
(131, 'Carrots', 91.46, '2025-12-01'),
(132, 'Carrots', 91.39, '2025-12-02'),
(133, 'Beans', 42.17, '2025-11-01'),
(134, 'Beans', 43.13, '2025-11-02'),
(135, 'Beans', 40.58, '2025-11-03'),
(136, 'Beans', 43.16, '2025-11-04'),
(137, 'Beans', 36.31, '2025-11-05'),
(138, 'Beans', 35.85, '2025-11-06'),
(139, 'Beans', 40.23, '2025-11-07'),
(140, 'Beans', 41.52, '2025-11-08'),
(141, 'Beans', 40.81, '2025-11-09'),
(142, 'Beans', 36.04, '2025-11-10'),
(143, 'Beans', 40.81, '2025-11-11'),
(144, 'Beans', 35.63, '2025-11-12'),
(145, 'Beans', 36.25, '2025-11-13'),
(146, 'Beans', 41.13, '2025-11-14'),
(147, 'Beans', 43.91, '2025-11-15'),
(148, 'Beans', 42.19, '2025-11-16'),
(149, 'Beans', 37.19, '2025-11-17'),
(150, 'Beans', 35.15, '2025-11-18'),
(151, 'Beans', 43.08, '2025-11-19'),
(152, 'Beans', 35.11, '2025-11-20'),
(153, 'Beans', 42.54, '2025-11-21'),
(154, 'Beans', 43.51, '2025-11-22'),
(155, 'Beans', 39.86, '2025-11-23'),
(156, 'Beans', 41.42, '2025-11-24'),
(157, 'Beans', 39.46, '2025-11-25'),
(158, 'Beans', 41.67, '2025-11-26'),
(159, 'Beans', 39.67, '2025-11-27'),
(160, 'Beans', 37.89, '2025-11-28'),
(161, 'Beans', 41.87, '2025-11-29'),
(162, 'Beans', 44.20, '2025-11-30'),
(163, 'Beans', 37.78, '2025-12-01'),
(164, 'Beans', 36.31, '2025-12-02'),
(165, 'Talong', 54.34, '2025-11-01'),
(166, 'Talong', 50.84, '2025-11-02'),
(167, 'Talong', 58.74, '2025-11-03'),
(168, 'Talong', 51.58, '2025-11-04'),
(169, 'Talong', 51.49, '2025-11-05'),
(170, 'Talong', 51.10, '2025-11-06'),
(171, 'Talong', 59.98, '2025-11-07'),
(172, 'Talong', 52.88, '2025-11-08'),
(173, 'Talong', 56.63, '2025-11-09'),
(174, 'Talong', 52.05, '2025-11-10'),
(175, 'Talong', 53.64, '2025-11-11'),
(176, 'Talong', 51.05, '2025-11-12'),
(177, 'Talong', 51.47, '2025-11-13'),
(178, 'Talong', 57.06, '2025-11-14'),
(179, 'Talong', 59.20, '2025-11-15'),
(180, 'Talong', 50.29, '2025-11-16'),
(181, 'Talong', 59.24, '2025-11-17'),
(182, 'Talong', 54.89, '2025-11-18'),
(183, 'Talong', 59.39, '2025-11-19'),
(184, 'Talong', 50.77, '2025-11-20'),
(185, 'Talong', 56.09, '2025-11-21'),
(186, 'Talong', 58.11, '2025-11-22'),
(187, 'Talong', 59.20, '2025-11-23'),
(188, 'Talong', 50.94, '2025-11-24'),
(189, 'Talong', 57.73, '2025-11-25'),
(190, 'Talong', 51.75, '2025-11-26'),
(191, 'Talong', 56.55, '2025-11-27'),
(192, 'Talong', 51.81, '2025-11-28'),
(193, 'Talong', 53.07, '2025-11-29'),
(194, 'Talong', 56.54, '2025-11-30'),
(195, 'Talong', 58.67, '2025-12-01'),
(196, 'Talong', 51.41, '2025-12-02'),
(197, 'Gabi', 62.46, '2025-11-01'),
(198, 'Gabi', 63.38, '2025-11-02'),
(199, 'Gabi', 67.56, '2025-11-03'),
(200, 'Gabi', 69.17, '2025-11-04'),
(201, 'Gabi', 60.10, '2025-11-05'),
(202, 'Gabi', 63.09, '2025-11-06'),
(203, 'Gabi', 67.55, '2025-11-07'),
(204, 'Gabi', 63.13, '2025-11-08'),
(205, 'Gabi', 60.91, '2025-11-09'),
(206, 'Gabi', 68.74, '2025-11-10'),
(207, 'Gabi', 60.36, '2025-11-11'),
(208, 'Gabi', 62.90, '2025-11-12'),
(209, 'Gabi', 68.04, '2025-11-13'),
(210, 'Gabi', 60.03, '2025-11-14'),
(211, 'Gabi', 60.71, '2025-11-15'),
(212, 'Gabi', 65.65, '2025-11-16'),
(213, 'Gabi', 66.86, '2025-11-17'),
(214, 'Gabi', 61.64, '2025-11-18'),
(215, 'Gabi', 64.98, '2025-11-19'),
(216, 'Gabi', 60.14, '2025-11-20'),
(217, 'Gabi', 69.89, '2025-11-21'),
(218, 'Gabi', 60.67, '2025-11-22'),
(219, 'Gabi', 69.89, '2025-11-23'),
(220, 'Gabi', 69.85, '2025-11-24'),
(221, 'Gabi', 64.99, '2025-11-25'),
(222, 'Gabi', 65.91, '2025-11-26'),
(223, 'Gabi', 62.70, '2025-11-27'),
(224, 'Gabi', 68.32, '2025-11-28'),
(225, 'Gabi', 69.45, '2025-11-29'),
(226, 'Gabi', 62.96, '2025-11-30'),
(227, 'Gabi', 67.89, '2025-12-01'),
(228, 'Gabi', 64.71, '2025-12-02'),
(229, 'Atsal', 170.80, '2025-11-01'),
(230, 'Atsal', 179.91, '2025-11-02'),
(231, 'Atsal', 170.36, '2025-11-03'),
(232, 'Atsal', 171.49, '2025-11-04'),
(233, 'Atsal', 174.45, '2025-11-05'),
(234, 'Atsal', 173.81, '2025-11-06'),
(235, 'Atsal', 171.55, '2025-11-07'),
(236, 'Atsal', 178.69, '2025-11-08'),
(237, 'Atsal', 177.37, '2025-11-09'),
(238, 'Atsal', 175.76, '2025-11-10'),
(239, 'Atsal', 176.43, '2025-11-11'),
(240, 'Atsal', 175.02, '2025-11-12'),
(241, 'Atsal', 171.40, '2025-11-13'),
(242, 'Atsal', 171.91, '2025-11-14'),
(243, 'Atsal', 174.65, '2025-11-15'),
(244, 'Atsal', 176.90, '2025-11-16'),
(245, 'Atsal', 173.34, '2025-11-17'),
(246, 'Atsal', 177.63, '2025-11-18'),
(247, 'Atsal', 179.46, '2025-11-19'),
(248, 'Atsal', 175.83, '2025-11-20'),
(249, 'Atsal', 176.01, '2025-11-21'),
(250, 'Atsal', 176.04, '2025-11-22'),
(251, 'Atsal', 178.43, '2025-11-23'),
(252, 'Atsal', 174.06, '2025-11-24'),
(253, 'Atsal', 170.88, '2025-11-25'),
(254, 'Atsal', 179.24, '2025-11-26'),
(255, 'Atsal', 177.94, '2025-11-27'),
(256, 'Atsal', 172.67, '2025-11-28'),
(257, 'Atsal', 170.30, '2025-11-29'),
(258, 'Atsal', 173.71, '2025-11-30'),
(259, 'Atsal', 176.10, '2025-12-01'),
(260, 'Atsal', 178.58, '2025-12-02'),
(261, 'Kalabasa', 55.45, '2025-11-01'),
(262, 'Kalabasa', 55.60, '2025-11-02'),
(263, 'Kalabasa', 58.62, '2025-11-03'),
(264, 'Kalabasa', 51.52, '2025-11-04'),
(265, 'Kalabasa', 59.88, '2025-11-05'),
(266, 'Kalabasa', 50.84, '2025-11-06'),
(267, 'Kalabasa', 53.07, '2025-11-07'),
(268, 'Kalabasa', 55.77, '2025-11-08'),
(269, 'Kalabasa', 52.88, '2025-11-09'),
(270, 'Kalabasa', 56.46, '2025-11-10'),
(271, 'Kalabasa', 58.46, '2025-11-11'),
(272, 'Kalabasa', 54.00, '2025-11-12'),
(273, 'Kalabasa', 53.64, '2025-11-13'),
(274, 'Kalabasa', 54.21, '2025-11-14'),
(275, 'Kalabasa', 56.70, '2025-11-15'),
(276, 'Kalabasa', 58.74, '2025-11-16'),
(277, 'Kalabasa', 53.94, '2025-11-17'),
(278, 'Kalabasa', 53.60, '2025-11-18'),
(279, 'Kalabasa', 54.71, '2025-11-19'),
(280, 'Kalabasa', 59.10, '2025-11-20'),
(281, 'Kalabasa', 58.91, '2025-11-21'),
(282, 'Kalabasa', 55.57, '2025-11-22'),
(283, 'Kalabasa', 59.20, '2025-11-23'),
(284, 'Kalabasa', 53.66, '2025-11-24'),
(285, 'Kalabasa', 59.95, '2025-11-25'),
(286, 'Kalabasa', 59.20, '2025-11-26'),
(287, 'Kalabasa', 55.67, '2025-11-27'),
(288, 'Kalabasa', 55.47, '2025-11-28'),
(289, 'Kalabasa', 58.97, '2025-11-29'),
(290, 'Kalabasa', 55.48, '2025-11-30'),
(291, 'Kalabasa', 51.57, '2025-12-01'),
(292, 'Kalabasa', 55.20, '2025-12-02'),
(293, 'Sibuyas Dahunan', 79.43, '2025-11-01'),
(294, 'Sibuyas Dahunan', 76.84, '2025-11-02'),
(295, 'Sibuyas Dahunan', 81.36, '2025-11-03'),
(296, 'Sibuyas Dahunan', 82.52, '2025-11-04'),
(297, 'Sibuyas Dahunan', 76.71, '2025-11-05'),
(298, 'Sibuyas Dahunan', 84.77, '2025-11-06'),
(299, 'Sibuyas Dahunan', 77.20, '2025-11-07'),
(300, 'Sibuyas Dahunan', 80.52, '2025-11-08'),
(301, 'Sibuyas Dahunan', 81.54, '2025-11-09'),
(302, 'Sibuyas Dahunan', 76.99, '2025-11-10'),
(303, 'Sibuyas Dahunan', 82.57, '2025-11-11'),
(304, 'Sibuyas Dahunan', 77.68, '2025-11-12'),
(305, 'Sibuyas Dahunan', 84.58, '2025-11-13'),
(306, 'Sibuyas Dahunan', 75.31, '2025-11-14'),
(307, 'Sibuyas Dahunan', 80.59, '2025-11-15'),
(308, 'Sibuyas Dahunan', 82.72, '2025-11-16'),
(309, 'Sibuyas Dahunan', 75.88, '2025-11-17'),
(310, 'Sibuyas Dahunan', 83.15, '2025-11-18'),
(311, 'Sibuyas Dahunan', 76.62, '2025-11-19'),
(312, 'Sibuyas Dahunan', 83.50, '2025-11-20'),
(313, 'Sibuyas Dahunan', 81.42, '2025-11-21'),
(314, 'Sibuyas Dahunan', 82.26, '2025-11-22'),
(315, 'Sibuyas Dahunan', 83.98, '2025-11-23'),
(316, 'Sibuyas Dahunan', 78.43, '2025-11-24'),
(317, 'Sibuyas Dahunan', 81.65, '2025-11-25'),
(318, 'Sibuyas Dahunan', 77.01, '2025-11-26'),
(319, 'Sibuyas Dahunan', 79.16, '2025-11-27'),
(320, 'Sibuyas Dahunan', 77.10, '2025-11-28'),
(321, 'Sibuyas Dahunan', 84.22, '2025-11-29'),
(322, 'Sibuyas Dahunan', 77.20, '2025-11-30'),
(323, 'Sibuyas Dahunan', 80.39, '2025-12-01'),
(324, 'Sibuyas Dahunan', 78.96, '2025-12-02'),
(325, 'Sili Espada', 98.40, '2025-11-01'),
(326, 'Sili Espada', 91.86, '2025-11-02'),
(327, 'Sili Espada', 98.23, '2025-11-03'),
(328, 'Sili Espada', 93.30, '2025-11-04'),
(329, 'Sili Espada', 97.46, '2025-11-05'),
(330, 'Sili Espada', 93.81, '2025-11-06'),
(331, 'Sili Espada', 98.50, '2025-11-07'),
(332, 'Sili Espada', 92.51, '2025-11-08'),
(333, 'Sili Espada', 90.79, '2025-11-09'),
(334, 'Sili Espada', 97.55, '2025-11-10'),
(335, 'Sili Espada', 96.16, '2025-11-11'),
(336, 'Sili Espada', 97.77, '2025-11-12'),
(337, 'Sili Espada', 94.62, '2025-11-13'),
(338, 'Sili Espada', 98.42, '2025-11-14'),
(339, 'Sili Espada', 91.49, '2025-11-15'),
(340, 'Sili Espada', 97.57, '2025-11-16'),
(341, 'Sili Espada', 96.40, '2025-11-17'),
(342, 'Sili Espada', 91.56, '2025-11-18'),
(343, 'Sili Espada', 91.56, '2025-11-19'),
(344, 'Sili Espada', 92.05, '2025-11-20'),
(345, 'Sili Espada', 92.70, '2025-11-21'),
(346, 'Sili Espada', 98.81, '2025-11-22'),
(347, 'Sili Espada', 93.60, '2025-11-23'),
(348, 'Sili Espada', 96.08, '2025-11-24'),
(349, 'Sili Espada', 99.42, '2025-11-25'),
(350, 'Sili Espada', 98.74, '2025-11-26'),
(351, 'Sili Espada', 96.87, '2025-11-27'),
(352, 'Sili Espada', 94.03, '2025-11-28'),
(353, 'Sili Espada', 92.93, '2025-11-29'),
(354, 'Sili Espada', 90.10, '2025-11-30'),
(355, 'Sili Espada', 91.73, '2025-12-01'),
(356, 'Sili Espada', 97.23, '2025-12-02'),
(357, 'Sayote', 23.36, '2025-11-01'),
(358, 'Sayote', 27.54, '2025-11-02'),
(359, 'Sayote', 24.34, '2025-11-03'),
(360, 'Sayote', 29.35, '2025-11-04'),
(361, 'Sayote', 31.91, '2025-11-05'),
(362, 'Sayote', 25.12, '2025-11-06'),
(363, 'Sayote', 25.21, '2025-11-07'),
(364, 'Sayote', 23.10, '2025-11-08'),
(365, 'Sayote', 29.07, '2025-11-09'),
(366, 'Sayote', 24.87, '2025-11-10'),
(367, 'Sayote', 23.50, '2025-11-11'),
(368, 'Sayote', 24.36, '2025-11-12'),
(369, 'Sayote', 26.26, '2025-11-13'),
(370, 'Sayote', 26.12, '2025-11-14'),
(371, 'Sayote', 26.75, '2025-11-15'),
(372, 'Sayote', 30.63, '2025-11-16'),
(373, 'Sayote', 26.87, '2025-11-17'),
(374, 'Sayote', 26.51, '2025-11-18'),
(375, 'Sayote', 27.35, '2025-11-19'),
(376, 'Sayote', 27.64, '2025-11-20'),
(377, 'Sayote', 25.77, '2025-11-21'),
(378, 'Sayote', 30.65, '2025-11-22'),
(379, 'Sayote', 30.07, '2025-11-23'),
(380, 'Sayote', 28.52, '2025-11-24'),
(381, 'Sayote', 24.83, '2025-11-25'),
(382, 'Sayote', 31.39, '2025-11-26'),
(383, 'Sayote', 29.35, '2025-11-27'),
(384, 'Sayote', 29.05, '2025-11-28'),
(385, 'Sayote', 31.25, '2025-11-29'),
(386, 'Sayote', 26.85, '2025-11-30'),
(387, 'Sayote', 31.14, '2025-12-01'),
(388, 'Sayote', 26.69, '2025-12-02'),
(389, 'Kamatis', 78.49, '2025-11-01'),
(390, 'Kamatis', 71.91, '2025-11-02'),
(391, 'Kamatis', 70.81, '2025-11-03'),
(392, 'Kamatis', 78.73, '2025-11-04'),
(393, 'Kamatis', 74.01, '2025-11-05'),
(394, 'Kamatis', 71.45, '2025-11-06'),
(395, 'Kamatis', 78.52, '2025-11-07'),
(396, 'Kamatis', 71.55, '2025-11-08'),
(397, 'Kamatis', 76.53, '2025-11-09'),
(398, 'Kamatis', 73.17, '2025-11-10'),
(399, 'Kamatis', 77.01, '2025-11-11'),
(400, 'Kamatis', 73.30, '2025-11-12'),
(401, 'Kamatis', 77.30, '2025-11-13'),
(402, 'Kamatis', 75.31, '2025-11-14'),
(403, 'Kamatis', 70.93, '2025-11-15'),
(404, 'Kamatis', 72.84, '2025-11-16'),
(405, 'Kamatis', 75.05, '2025-11-17'),
(406, 'Kamatis', 79.52, '2025-11-18'),
(407, 'Kamatis', 76.32, '2025-11-19'),
(408, 'Kamatis', 79.35, '2025-11-20'),
(409, 'Kamatis', 72.54, '2025-11-21'),
(410, 'Kamatis', 73.39, '2025-11-22'),
(411, 'Kamatis', 79.30, '2025-11-23'),
(412, 'Kamatis', 75.29, '2025-11-24'),
(413, 'Kamatis', 78.50, '2025-11-25'),
(414, 'Kamatis', 78.89, '2025-11-26'),
(415, 'Kamatis', 78.10, '2025-11-27'),
(416, 'Kamatis', 78.33, '2025-11-28'),
(417, 'Kamatis', 71.21, '2025-11-29'),
(418, 'Kamatis', 79.55, '2025-11-30'),
(419, 'Kamatis', 79.30, '2025-12-01'),
(420, 'Kamatis', 71.50, '2025-12-02'),
(421, 'Paliya', 66.86, '2025-11-01'),
(422, 'Paliya', 69.57, '2025-11-02'),
(423, 'Paliya', 67.24, '2025-11-03'),
(424, 'Paliya', 67.08, '2025-11-04'),
(425, 'Paliya', 72.11, '2025-11-05'),
(426, 'Paliya', 69.49, '2025-11-06'),
(427, 'Paliya', 72.50, '2025-11-07'),
(428, 'Paliya', 72.08, '2025-11-08'),
(429, 'Paliya', 72.95, '2025-11-09'),
(430, 'Paliya', 67.30, '2025-11-10'),
(431, 'Paliya', 73.35, '2025-11-11'),
(432, 'Paliya', 69.40, '2025-11-12'),
(433, 'Paliya', 70.87, '2025-11-13'),
(434, 'Paliya', 73.97, '2025-11-14'),
(435, 'Paliya', 69.04, '2025-11-15'),
(436, 'Paliya', 73.22, '2025-11-16'),
(437, 'Paliya', 68.61, '2025-11-17'),
(438, 'Paliya', 70.78, '2025-11-18'),
(439, 'Paliya', 65.48, '2025-11-19'),
(440, 'Paliya', 67.24, '2025-11-20'),
(441, 'Paliya', 71.71, '2025-11-21'),
(442, 'Paliya', 74.06, '2025-11-22'),
(443, 'Paliya', 69.30, '2025-11-23'),
(444, 'Paliya', 72.01, '2025-11-24'),
(445, 'Paliya', 72.04, '2025-11-25'),
(446, 'Paliya', 66.69, '2025-11-26'),
(447, 'Paliya', 70.83, '2025-11-27'),
(448, 'Paliya', 70.78, '2025-11-28'),
(449, 'Paliya', 71.21, '2025-11-29'),
(450, 'Paliya', 67.64, '2025-11-30'),
(451, 'Paliya', 68.52, '2025-12-01'),
(452, 'Paliya', 70.09, '2025-12-02'),
(453, 'Balatong', 36.63, '2025-11-01'),
(454, 'Balatong', 39.88, '2025-11-02'),
(455, 'Balatong', 42.17, '2025-11-03'),
(456, 'Balatong', 36.31, '2025-11-04'),
(457, 'Balatong', 35.53, '2025-11-05'),
(458, 'Balatong', 40.50, '2025-11-06'),
(459, 'Balatong', 38.65, '2025-11-07'),
(460, 'Balatong', 44.20, '2025-11-08'),
(461, 'Balatong', 41.52, '2025-11-09'),
(462, 'Balatong', 41.97, '2025-11-10'),
(463, 'Balatong', 43.16, '2025-11-11'),
(464, 'Balatong', 42.58, '2025-11-12'),
(465, 'Balatong', 39.46, '2025-11-13'),
(466, 'Balatong', 40.78, '2025-11-14'),
(467, 'Balatong', 40.10, '2025-11-15'),
(468, 'Balatong', 40.40, '2025-11-16'),
(469, 'Balatong', 38.01, '2025-11-17'),
(470, 'Balatong', 43.51, '2025-11-18'),
(471, 'Balatong', 44.57, '2025-11-19'),
(472, 'Balatong', 42.54, '2025-11-20'),
(473, 'Balatong', 40.81, '2025-11-21'),
(474, 'Balatong', 37.19, '2025-11-22'),
(475, 'Balatong', 42.19, '2025-11-23'),
(476, 'Balatong', 43.08, '2025-11-24'),
(477, 'Balatong', 35.85, '2025-11-25'),
(478, 'Balatong', 40.23, '2025-11-26'),
(479, 'Balatong', 41.52, '2025-11-27'),
(480, 'Balatong', 44.20, '2025-11-28'),
(481, 'Balatong', 41.97, '2025-11-29'),
(482, 'Balatong', 43.16, '2025-11-30'),
(483, 'Balatong', 42.58, '2025-12-01'),
(484, 'Balatong', 39.46, '2025-12-02'),
(485, 'Sili Labuyo', 260.67, '2025-11-01'),
(486, 'Sili Labuyo', 260.83, '2025-11-02'),
(487, 'Sili Labuyo', 267.35, '2025-11-03'),
(488, 'Sili Labuyo', 262.72, '2025-11-04'),
(489, 'Sili Labuyo', 266.39, '2025-11-05'),
(490, 'Sili Labuyo', 263.29, '2025-11-06'),
(491, 'Sili Labuyo', 260.67, '2025-11-07'),
(492, 'Sili Labuyo', 260.83, '2025-11-08'),
(493, 'Sili Labuyo', 267.35, '2025-11-09'),
(494, 'Sili Labuyo', 262.72, '2025-11-10'),
(495, 'Sili Labuyo', 266.39, '2025-11-11'),
(496, 'Sili Labuyo', 263.29, '2025-11-12'),
(497, 'Sili Labuyo', 260.67, '2025-11-13'),
(498, 'Sili Labuyo', 260.83, '2025-11-14'),
(499, 'Sili Labuyo', 267.35, '2025-11-15'),
(500, 'Sili Labuyo', 262.72, '2025-11-16'),
(501, 'Sili Labuyo', 266.39, '2025-11-17'),
(502, 'Sili Labuyo', 263.29, '2025-11-18'),
(503, 'Sili Labuyo', 260.67, '2025-11-19'),
(504, 'Sili Labuyo', 260.83, '2025-11-20'),
(505, 'Sili Labuyo', 267.35, '2025-11-21'),
(506, 'Sili Labuyo', 262.72, '2025-11-22'),
(507, 'Sili Labuyo', 266.39, '2025-11-23'),
(508, 'Sili Labuyo', 263.29, '2025-11-24'),
(509, 'Sili Labuyo', 260.67, '2025-11-25'),
(510, 'Sili Labuyo', 260.83, '2025-11-26'),
(511, 'Sili Labuyo', 267.35, '2025-11-27'),
(512, 'Sili Labuyo', 262.72, '2025-11-28'),
(513, 'Sili Labuyo', 266.39, '2025-11-29'),
(514, 'Sili Labuyo', 263.29, '2025-11-30'),
(515, 'Sili Labuyo', 260.67, '2025-12-01'),
(516, 'Sili Labuyo', 260.83, '2025-12-02'),
(517, 'Sili Halang/Kulikot', 177.37, '2025-11-01'),
(518, 'Sili Halang/Kulikot', 175.76, '2025-11-02'),
(519, 'Sili Halang/Kulikot', 176.43, '2025-11-03'),
(520, 'Sili Halang/Kulikot', 175.02, '2025-11-04'),
(521, 'Sili Halang/Kulikot', 171.40, '2025-11-05'),
(522, 'Sili Halang/Kulikot', 171.91, '2025-11-06'),
(523, 'Sili Halang/Kulikot', 174.65, '2025-11-07'),
(524, 'Sili Halang/Kulikot', 176.90, '2025-11-08'),
(525, 'Sili Halang/Kulikot', 173.34, '2025-11-09'),
(526, 'Sili Halang/Kulikot', 177.63, '2025-11-10'),
(527, 'Sili Halang/Kulikot', 179.46, '2025-11-11'),
(528, 'Sili Halang/Kulikot', 175.83, '2025-11-12'),
(529, 'Sili Halang/Kulikot', 176.01, '2025-11-13'),
(530, 'Sili Halang/Kulikot', 176.04, '2025-11-14'),
(531, 'Sili Halang/Kulikot', 178.43, '2025-11-15'),
(532, 'Sili Halang/Kulikot', 174.06, '2025-11-16'),
(533, 'Sili Halang/Kulikot', 170.88, '2025-11-17'),
(534, 'Sili Halang/Kulikot', 179.24, '2025-11-18'),
(535, 'Sili Halang/Kulikot', 177.94, '2025-11-19'),
(536, 'Sili Halang/Kulikot', 172.67, '2025-11-20'),
(537, 'Sili Halang/Kulikot', 170.30, '2025-11-21'),
(538, 'Sili Halang/Kulikot', 173.71, '2025-11-22'),
(539, 'Sili Halang/Kulikot', 176.10, '2025-11-23'),
(540, 'Sili Halang/Kulikot', 178.58, '2025-11-24'),
(541, 'Sili Halang/Kulikot', 170.80, '2025-11-25'),
(542, 'Sili Halang/Kulikot', 179.91, '2025-11-26'),
(543, 'Sili Halang/Kulikot', 170.36, '2025-11-27'),
(544, 'Sili Halang/Kulikot', 171.49, '2025-11-28'),
(545, 'Sili Halang/Kulikot', 174.45, '2025-11-29'),
(546, 'Sili Halang/Kulikot', 173.81, '2025-11-30'),
(547, 'Sili Halang/Kulikot', 171.55, '2025-12-01'),
(548, 'Sili Halang/Kulikot', 178.69, '2025-12-02'),
(549, 'Okra', 54.71, '2025-11-01'),
(550, 'Okra', 59.10, '2025-11-02'),
(551, 'Okra', 58.91, '2025-11-03'),
(552, 'Okra', 55.57, '2025-11-04'),
(553, 'Okra', 59.20, '2025-11-05'),
(554, 'Okra', 53.66, '2025-11-06'),
(555, 'Okra', 59.95, '2025-11-07'),
(556, 'Okra', 59.20, '2025-11-08'),
(557, 'Okra', 55.67, '2025-11-09'),
(558, 'Okra', 55.47, '2025-11-10'),
(559, 'Okra', 58.97, '2025-11-11'),
(560, 'Okra', 55.48, '2025-11-12'),
(561, 'Okra', 51.57, '2025-11-13'),
(562, 'Okra', 55.20, '2025-11-14'),
(563, 'Okra', 55.45, '2025-11-15'),
(564, 'Okra', 55.60, '2025-11-16'),
(565, 'Okra', 58.62, '2025-11-17'),
(566, 'Okra', 51.52, '2025-11-18'),
(567, 'Okra', 59.88, '2025-11-19'),
(568, 'Okra', 50.84, '2025-11-20'),
(569, 'Okra', 53.07, '2025-11-21'),
(570, 'Okra', 55.77, '2025-11-22'),
(571, 'Okra', 52.88, '2025-11-23'),
(572, 'Okra', 56.46, '2025-11-24'),
(573, 'Okra', 58.46, '2025-11-25'),
(574, 'Okra', 54.00, '2025-11-26'),
(575, 'Okra', 53.64, '2025-11-27'),
(576, 'Okra', 54.21, '2025-11-28'),
(577, 'Okra', 56.70, '2025-11-29'),
(578, 'Okra', 58.74, '2025-11-30'),
(579, 'Okra', 53.94, '2025-12-01'),
(580, 'Okra', 53.60, '2025-12-02'),
(581, 'Luy-a', 97.46, '2025-11-01'),
(582, 'Luy-a', 93.81, '2025-11-02'),
(583, 'Luy-a', 98.50, '2025-11-03'),
(584, 'Luy-a', 92.51, '2025-11-04'),
(585, 'Luy-a', 90.79, '2025-11-05'),
(586, 'Luy-a', 97.55, '2025-11-06'),
(587, 'Luy-a', 96.16, '2025-11-07'),
(588, 'Luy-a', 97.77, '2025-11-08'),
(589, 'Luy-a', 94.62, '2025-11-09'),
(590, 'Luy-a', 98.42, '2025-11-10'),
(591, 'Luy-a', 91.49, '2025-11-11'),
(592, 'Luy-a', 97.57, '2025-11-12'),
(593, 'Luy-a', 96.40, '2025-11-13'),
(594, 'Luy-a', 91.56, '2025-11-14'),
(595, 'Luy-a', 91.56, '2025-11-15'),
(596, 'Luy-a', 92.05, '2025-11-16'),
(597, 'Luy-a', 92.70, '2025-11-17'),
(598, 'Luy-a', 98.81, '2025-11-18'),
(599, 'Luy-a', 93.60, '2025-11-19'),
(600, 'Luy-a', 96.08, '2025-11-20'),
(601, 'Luy-a', 99.42, '2025-11-21'),
(602, 'Luy-a', 98.74, '2025-11-22'),
(603, 'Luy-a', 96.87, '2025-11-23'),
(604, 'Luy-a', 94.03, '2025-11-24'),
(605, 'Luy-a', 92.93, '2025-11-25'),
(606, 'Luy-a', 90.10, '2025-11-26'),
(607, 'Luy-a', 91.73, '2025-11-27'),
(608, 'Luy-a', 97.23, '2025-11-28'),
(609, 'Luy-a', 98.40, '2025-11-29'),
(610, 'Luy-a', 91.86, '2025-11-30'),
(611, 'Luy-a', 98.23, '2025-12-01'),
(612, 'Luy-a', 93.30, '2025-12-02'),
(613, 'Pipino', 31.91, '2025-11-01'),
(614, 'Pipino', 25.12, '2025-11-02'),
(615, 'Pipino', 25.21, '2025-11-03'),
(616, 'Pipino', 23.10, '2025-11-04'),
(617, 'Pipino', 29.07, '2025-11-05'),
(618, 'Pipino', 24.87, '2025-11-06'),
(619, 'Pipino', 23.50, '2025-11-07'),
(620, 'Pipino', 24.36, '2025-11-08'),
(621, 'Pipino', 26.26, '2025-11-09'),
(622, 'Pipino', 26.12, '2025-11-10'),
(623, 'Pipino', 26.75, '2025-11-11'),
(624, 'Pipino', 30.63, '2025-11-12'),
(625, 'Pipino', 26.87, '2025-11-13'),
(626, 'Pipino', 26.51, '2025-11-14'),
(627, 'Pipino', 27.35, '2025-11-15'),
(628, 'Pipino', 27.64, '2025-11-16'),
(629, 'Pipino', 25.77, '2025-11-17'),
(630, 'Pipino', 30.65, '2025-11-18'),
(631, 'Pipino', 30.07, '2025-11-19'),
(632, 'Pipino', 28.52, '2025-11-20'),
(633, 'Pipino', 24.83, '2025-11-21'),
(634, 'Pipino', 31.39, '2025-11-22'),
(635, 'Pipino', 29.35, '2025-11-23'),
(636, 'Pipino', 29.05, '2025-11-24'),
(637, 'Pipino', 31.25, '2025-11-25'),
(638, 'Pipino', 26.85, '2025-11-26'),
(639, 'Pipino', 31.14, '2025-11-27'),
(640, 'Pipino', 26.69, '2025-11-28'),
(641, 'Pipino', 23.36, '2025-11-29'),
(642, 'Pipino', 27.54, '2025-11-30'),
(643, 'Pipino', 24.34, '2025-12-01'),
(644, 'Pipino', 29.35, '2025-12-02'),
(645, 'Lettuce Ball', 48.60, '2025-11-01'),
(646, 'Lettuce Ball', 44.02, '2025-11-02'),
(647, 'Lettuce Ball', 48.06, '2025-11-03'),
(648, 'Lettuce Ball', 46.51, '2025-11-04'),
(649, 'Lettuce Ball', 45.31, '2025-11-05'),
(650, 'Lettuce Ball', 47.97, '2025-11-06'),
(651, 'Lettuce Ball', 46.22, '2025-11-07'),
(652, 'Lettuce Ball', 41.28, '2025-11-08'),
(653, 'Lettuce Ball', 43.51, '2025-11-09'),
(654, 'Lettuce Ball', 42.44, '2025-11-10'),
(655, 'Lettuce Ball', 48.02, '2025-11-11'),
(656, 'Lettuce Ball', 49.72, '2025-11-12'),
(657, 'Lettuce Ball', 47.28, '2025-11-13'),
(658, 'Lettuce Ball', 42.88, '2025-11-14'),
(659, 'Lettuce Ball', 48.33, '2025-11-15'),
(660, 'Lettuce Ball', 47.88, '2025-11-16'),
(661, 'Lettuce Ball', 49.82, '2025-11-17'),
(662, 'Lettuce Ball', 45.38, '2025-11-18'),
(663, 'Lettuce Ball', 44.97, '2025-11-19'),
(664, 'Lettuce Ball', 44.75, '2025-11-20'),
(665, 'Lettuce Ball', 41.27, '2025-11-21'),
(666, 'Lettuce Ball', 42.16, '2025-11-22'),
(667, 'Lettuce Ball', 45.92, '2025-11-23'),
(668, 'Lettuce Ball', 45.89, '2025-11-24'),
(669, 'Lettuce Ball', 42.24, '2025-11-25'),
(670, 'Lettuce Ball', 47.97, '2025-11-26'),
(671, 'Lettuce Ball', 50.91, '2025-11-27'),
(672, 'Lettuce Ball', 45.03, '2025-11-28'),
(673, 'Lettuce Ball', 45.61, '2025-11-29'),
(674, 'Lettuce Ball', 47.13, '2025-11-30'),
(675, 'Lettuce Ball', 49.33, '2025-12-01'),
(676, 'Lettuce Ball', 44.88, '2025-12-02'),
(677, 'Sweetcorn', 44.02, '2025-11-01'),
(678, 'Sweetcorn', 48.06, '2025-11-02'),
(679, 'Sweetcorn', 46.51, '2025-11-03'),
(680, 'Sweetcorn', 45.31, '2025-11-04'),
(681, 'Sweetcorn', 47.97, '2025-11-05'),
(682, 'Sweetcorn', 46.22, '2025-11-06'),
(683, 'Sweetcorn', 41.28, '2025-11-07'),
(684, 'Sweetcorn', 43.51, '2025-11-08'),
(685, 'Sweetcorn', 42.44, '2025-11-09'),
(686, 'Sweetcorn', 48.02, '2025-11-10'),
(687, 'Sweetcorn', 49.72, '2025-11-11'),
(688, 'Sweetcorn', 47.28, '2025-11-12'),
(689, 'Sweetcorn', 42.88, '2025-11-13'),
(690, 'Sweetcorn', 48.33, '2025-11-14'),
(691, 'Sweetcorn', 47.88, '2025-11-15'),
(692, 'Sweetcorn', 49.82, '2025-11-16'),
(693, 'Sweetcorn', 45.38, '2025-11-17'),
(694, 'Sweetcorn', 44.97, '2025-11-18'),
(695, 'Sweetcorn', 44.75, '2025-11-19'),
(696, 'Sweetcorn', 41.27, '2025-11-20'),
(697, 'Sweetcorn', 42.16, '2025-11-21'),
(698, 'Sweetcorn', 45.92, '2025-11-22'),
(699, 'Sweetcorn', 45.89, '2025-11-23'),
(700, 'Sweetcorn', 42.24, '2025-11-24'),
(701, 'Sweetcorn', 47.97, '2025-11-25'),
(702, 'Sweetcorn', 50.91, '2025-11-26'),
(703, 'Sweetcorn', 45.03, '2025-11-27'),
(704, 'Sweetcorn', 45.61, '2025-11-28'),
(705, 'Sweetcorn', 47.13, '2025-11-29'),
(706, 'Sweetcorn', 49.33, '2025-11-30'),
(707, 'Sweetcorn', 44.88, '2025-12-01'),
(708, 'Sweetcorn', 41.27, '2025-12-02'),
(709, 'Raddish', 42.16, '2025-11-01'),
(710, 'Raddish', 45.92, '2025-11-02'),
(711, 'Raddish', 45.89, '2025-11-03'),
(712, 'Raddish', 42.24, '2025-11-04'),
(713, 'Raddish', 47.97, '2025-11-05'),
(714, 'Raddish', 50.91, '2025-11-06'),
(715, 'Raddish', 45.03, '2025-11-07'),
(716, 'Raddish', 45.61, '2025-11-08'),
(717, 'Raddish', 47.13, '2025-11-09'),
(718, 'Raddish', 49.33, '2025-11-10'),
(719, 'Raddish', 44.88, '2025-11-11'),
(720, 'Raddish', 41.27, '2025-11-12'),
(721, 'Raddish', 42.16, '2025-11-13'),
(722, 'Raddish', 45.92, '2025-11-14'),
(723, 'Raddish', 45.89, '2025-11-15'),
(724, 'Raddish', 42.24, '2025-11-16'),
(725, 'Raddish', 47.97, '2025-11-17'),
(726, 'Raddish', 50.91, '2025-11-18'),
(727, 'Raddish', 45.03, '2025-11-19'),
(728, 'Raddish', 45.61, '2025-11-20'),
(729, 'Raddish', 47.13, '2025-11-21'),
(730, 'Raddish', 49.33, '2025-11-22'),
(731, 'Raddish', 44.88, '2025-11-23'),
(732, 'Raddish', 41.27, '2025-11-24'),
(733, 'Raddish', 42.16, '2025-11-25'),
(734, 'Raddish', 45.92, '2025-11-26'),
(735, 'Raddish', 45.89, '2025-11-27'),
(736, 'Raddish', 42.24, '2025-11-28'),
(737, 'Raddish', 47.97, '2025-11-29'),
(738, 'Raddish', 50.91, '2025-11-30'),
(739, 'Raddish', 45.03, '2025-12-01'),
(740, 'Raddish', 45.61, '2025-12-02'),
(741, 'Repolyo', 50.00, '2025-12-03'),
(742, 'Atsal', 180.00, '2025-12-03'),
(743, 'Balatong', 39.46, '2025-12-03'),
(744, 'Beans', 36.31, '2025-12-03'),
(745, 'Raddish', 50.61, '2025-12-04'),
(746, 'Atsal', 190.00, '2025-12-04'),
(747, 'Repolyo', 45.00, '2025-12-04'),
(748, 'Atsal', 200.00, '2025-12-05');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `document_request`
--
ALTER TABLE `document_request`
  ADD PRIMARY KEY (`Request_ID`),
  ADD KEY `User_ID` (`User_ID`);

--
-- Indexes for table `emergency_contact`
--
ALTER TABLE `emergency_contact`
  ADD PRIMARY KEY (`Contact_ID`);

--
-- Indexes for table `emergency_contact_log`
--
ALTER TABLE `emergency_contact_log`
  ADD PRIMARY KEY (`Log_ID`),
  ADD KEY `User_ID` (`User_ID`),
  ADD KEY `Contact_ID` (`Contact_ID`);

--
-- Indexes for table `history_log`
--
ALTER TABLE `history_log`
  ADD PRIMARY KEY (`log_id`);

--
-- Indexes for table `news_and_updates`
--
ALTER TABLE `news_and_updates`
  ADD PRIMARY KEY (`News_ID`),
  ADD KEY `idx_category` (`Category`),
  ADD KEY `idx_date` (`Date_Published`);

--
-- Indexes for table `notification`
--
ALTER TABLE `notification`
  ADD PRIMARY KEY (`Notification_ID`),
  ADD KEY `User_ID` (`User_ID`);

--
-- Indexes for table `report`
--
ALTER TABLE `report`
  ADD PRIMARY KEY (`Report_ID`),
  ADD KEY `User_ID` (`User_ID`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`User_ID`),
  ADD UNIQUE KEY `Email` (`Email`);

--
-- Indexes for table `vegetable_price`
--
ALTER TABLE `vegetable_price`
  ADD PRIMARY KEY (`Price_ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `document_request`
--
ALTER TABLE `document_request`
  MODIFY `Request_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `emergency_contact`
--
ALTER TABLE `emergency_contact`
  MODIFY `Contact_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `emergency_contact_log`
--
ALTER TABLE `emergency_contact_log`
  MODIFY `Log_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `history_log`
--
ALTER TABLE `history_log`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `news_and_updates`
--
ALTER TABLE `news_and_updates`
  MODIFY `News_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `notification`
--
ALTER TABLE `notification`
  MODIFY `Notification_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `report`
--
ALTER TABLE `report`
  MODIFY `Report_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `User_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `vegetable_price`
--
ALTER TABLE `vegetable_price`
  MODIFY `Price_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=749;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `document_request`
--
ALTER TABLE `document_request`
  ADD CONSTRAINT `document_request_ibfk_1` FOREIGN KEY (`User_ID`) REFERENCES `users` (`User_ID`);

--
-- Constraints for table `emergency_contact_log`
--
ALTER TABLE `emergency_contact_log`
  ADD CONSTRAINT `emergency_contact_log_ibfk_1` FOREIGN KEY (`User_ID`) REFERENCES `baryotap_db`.`users` (`User_ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `emergency_contact_log_ibfk_2` FOREIGN KEY (`Contact_ID`) REFERENCES `emergency_contact` (`Contact_ID`) ON DELETE CASCADE;

--
-- Constraints for table `notification`
--
ALTER TABLE `notification`
  ADD CONSTRAINT `notification_ibfk_1` FOREIGN KEY (`User_ID`) REFERENCES `baryotap_db`.`users` (`User_ID`) ON DELETE CASCADE;

--
-- Constraints for table `report`
--
ALTER TABLE `report`
  ADD CONSTRAINT `report_ibfk_1` FOREIGN KEY (`User_ID`) REFERENCES `baryotap_db`.`users` (`User_ID`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
