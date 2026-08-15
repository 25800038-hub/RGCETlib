-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 14, 2025 at 11:57 AM
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
-- Database: `library`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `FullName` varchar(100) DEFAULT NULL,
  `AdminEmail` varchar(120) DEFAULT NULL,
  `UserName` varchar(100) NOT NULL,
  `Password` varchar(100) NOT NULL,
  `updationDate` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `FullName`, `AdminEmail`, `UserName`, `Password`, `updationDate`) VALUES
(1, 'RGCET', 'admin@gmail.com', 'admin', '202cb962ac59075b964b07152d234b70', '2024-12-31 19:03:56');

-- --------------------------------------------------------

--
-- Table structure for table `tblauthors`
--

CREATE TABLE `tblauthors` (
  `id` int(11) NOT NULL,
  `AuthorName` varchar(159) DEFAULT NULL,
  `creationDate` timestamp NULL DEFAULT current_timestamp(),
  `UpdationDate` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tblauthors`
--

INSERT INTO `tblauthors` (`id`, `AuthorName`, `creationDate`, `UpdationDate`) VALUES
(1, 'Anuj kumar', '2023-12-31 21:23:03', '2025-01-07 06:18:43'),
(2, 'Chetan Bhagatt', '2023-12-31 21:23:03', '2025-01-07 06:18:50'),
(3, 'Anita Desai', '2023-12-31 21:23:03', '2025-01-07 06:18:50'),
(4, 'HC Verma', '2023-12-31 21:23:03', '2025-01-07 06:18:50'),
(5, 'R.D. Sharma ', '2023-12-31 21:23:03', '2025-01-07 06:18:50'),
(9, 'fwdfrwer', '2023-12-31 21:23:03', '2025-01-07 06:18:50'),
(10, 'Dr. Andy Williams', '2023-12-31 21:23:03', '2025-01-07 06:18:50'),
(11, 'Kyle Hill', '2023-12-31 21:23:03', '2025-01-07 06:18:50'),
(12, 'Robert T. Kiyosak', '2023-12-31 21:23:03', '2025-01-07 06:18:50'),
(13, 'Kelly Barnhill', '2023-12-31 21:23:03', '2025-01-07 06:18:50'),
(14, 'Herbert Schildt', '2023-12-31 21:23:03', '2025-01-07 06:18:50'),
(16, ' Tiffany Timbers', '2025-01-07 06:55:54', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tblbooks`
--

CREATE TABLE `tblbooks` (
  `id` int(11) NOT NULL,
  `BookName` varchar(255) DEFAULT NULL,
  `CatId` int(11) DEFAULT NULL,
  `AuthorId` int(11) DEFAULT NULL,
  `ISBNNumber` varchar(25) DEFAULT NULL,
  `BookPrice` decimal(10,2) DEFAULT NULL,
  `bookImage` varchar(250) NOT NULL,
  `bookPdf` varchar(255) DEFAULT NULL,
  `isIssued` int(1) DEFAULT NULL,
  `RegDate` timestamp NULL DEFAULT current_timestamp(),
  `UpdationDate` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `bookQty` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tblbooks`
--

INSERT INTO `tblbooks` (`id`, `BookName`, `CatId`, `AuthorId`, `ISBNNumber`, `BookPrice`, `bookImage`, `isIssued`, `RegDate`, `UpdationDate`, `bookQty`) VALUES
(1, 'PHP And MySql programming', 5, 1, '11111', 20.00, '1efecc0ca822e40b7b673c0d79ae943f.jpg', 0, '2024-01-02 01:23:03', '2025-01-14 07:08:11', 10),
(3, 'physics', 6, 4, '22222', 15.00, 'dd8267b57e0e4feee5911cb1e1a03a79.jpg', NULL, '2024-01-02 01:23:03', '2025-01-13 11:11:01', 10),
(5, 'Murach\'s MySQL', 5, 1, '333333', 455.00, '5939d64655b4d2ae443830d73abc35b6.jpg', NULL, '2024-01-02 01:23:03', '2025-01-13 11:11:01', 20),
(6, 'WordPress for Beginners 2022: A Visual Step-by-Step Guide to Mastering WordPress', 5, 10, '44444', 100.00, '144ab706ba1cb9f6c23fd6ae9c0502b3.jpg', NULL, '2024-01-02 01:23:03', '2025-01-13 11:05:35', 15),
(7, 'WordPress Mastery Guide:', 5, 11, '555555', 53.00, '90083a56014186e88ffca10286172e64.jpg', NULL, '2024-01-02 01:23:03', '2025-01-13 11:05:39', 14),
(8, 'Rich Dad Poor Dad: What the Rich Teach Their Kids About Money That the Poor and Middle Class Do Not', 8, 12, '66666', 120.00, '52411b2bd2a6b2e0df3eb10943a5b640.jpg', NULL, '2024-01-02 01:23:03', '2025-01-13 11:05:41', 5),
(9, 'The Girl Who Drank the Moon', 8, 13, '77777', 200.00, 'f05cd198ac9335245e1fdffa793207a7.jpg', NULL, '2024-01-02 01:23:03', '2025-01-13 11:05:45', 1),
(10, 'C++: The Complete Reference, 4th Edition', 5, 14, '88888', 142.00, '36af5de9012bf8c804e499dc3c3b33a5.jpg', NULL, '2024-01-02 01:23:03', '2025-01-13 11:11:01', 2),
(11, 'ASP.NET Core 5 for Beginners', 9, 11, '99999', 422.00, 'b1b6788016bbfab12cfd2722604badc9.jpg', NULL, '2024-01-02 01:23:03', '2025-01-13 11:11:01', 5),
(12, 'Python Packages', 9, 16, '00000', 3034.00, 'ba719639def504c64ebac89cdd0d0a85.jpg', NULL, '2025-01-07 06:56:50', NULL, 25);

-- --------------------------------------------------------

--
-- Table structure for table `tblcategory`
--

CREATE TABLE `tblcategory` (
  `id` int(11) NOT NULL,
  `CategoryName` varchar(150) DEFAULT NULL,
  `Status` int(1) DEFAULT NULL,
  `CreationDate` timestamp NULL DEFAULT current_timestamp(),
  `UpdationDate` timestamp NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tblcategory`
--

INSERT INTO `tblcategory` (`id`, `CategoryName`, `Status`, `CreationDate`, `UpdationDate`) VALUES
(4, 'Romantic', 1, '2025-01-01 07:23:03', '2025-01-07 06:19:11'),
(5, 'Technology', 1, '2025-01-01 07:23:03', '2025-01-07 06:19:21'),
(6, 'Science', 1, '2025-01-01 07:23:03', '2025-01-07 06:19:21'),
(7, 'Management', 1, '2025-01-01 07:23:03', '2025-01-07 06:19:21'),
(8, 'General', 1, '2025-01-01 07:23:03', '2025-01-07 06:19:21'),
(9, 'Programming', 1, '2025-01-01 07:23:03', '2025-01-07 06:19:21');

-- --------------------------------------------------------

--
-- Table structure for table `tblissuedbookdetails`
--

CREATE TABLE `tblissuedbookdetails` (
  `id` int(11) NOT NULL,
  `BookId` int(11) DEFAULT NULL,
  `StudentID` varchar(150) DEFAULT NULL,
  `IssuesDate` timestamp NULL DEFAULT current_timestamp(),
  `ReturnDate` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `RetrunStatus` int(1) DEFAULT NULL,
  `fine` int(11) DEFAULT NULL,
  `remark` mediumtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tblissuedbookdetails`
--



-- --------------------------------------------------------

--
-- Table structure for table `tblreservations`
--

CREATE TABLE `tblreservations` (
  `id` int(11) NOT NULL,
  `BookId` int(11) NOT NULL,
  `StudentID` varchar(150) NOT NULL,
  `ReservationDate` timestamp NULL DEFAULT current_timestamp(),
  `Status` varchar(50) NOT NULL DEFAULT 'Reserved',
  `AdminRemark` mediumtext DEFAULT NULL,
  `UpdationDate` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblstudents`
--

CREATE TABLE `tblstudents` (
  `id` int(11) NOT NULL,
  `StudentId` varchar(100) DEFAULT NULL,
  `FullName` varchar(120) DEFAULT NULL,
  `EmailId` varchar(120) DEFAULT NULL,
  `MobileNumber` char(11) DEFAULT NULL,
  `Password` varchar(120) DEFAULT NULL,
  `Status` int(1) DEFAULT NULL,
  `RegDate` timestamp NULL DEFAULT current_timestamp(),
  `UpdationDate` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tblstudents`
--

INSERT INTO `tblstudents` (`id`, `StudentId`, `FullName`, `EmailId`, `MobileNumber`, `Password`, `Status`, `RegDate`, `UpdationDate`) VALUES
(1, 'SID001', 'ABDUL RAHMAN U', '25800001@rgcet.edu.in', '1234567890', '202cb962ac59075b964b07152d234b70', 1, '2026-07-20 07:23:00', '2026-07-21 07:23:00'),
(2, 'SID002', 'AGASH S', '25800002@rgcet.edu.in', '1234567890', '202cb962ac59075b964b07152d234b70', 1, '2026-07-20 07:23:00', '2026-07-21 07:23:00'),
(3, 'SID003', 'AJITH S', '25800003@rgcet.edu.in', '1234567890', '202cb962ac59075b964b07152d234b70', 1, '2026-07-20 07:23:00', '2026-07-21 07:23:00'),
(4, 'SID004', 'ANBARASU B', '25800004@rgcet.edu.in', '1234567890', '202cb962ac59075b964b07152d234b70', 1, '2026-07-20 07:23:00', '2026-07-21 07:23:00'),
(5, 'SID005', 'ANTONY EMMANUEL JAMES A', '25800005@rgcet.edu.in', '1234567890', '202cb962ac59075b964b07152d234b70', 1, '2026-07-20 07:23:00', '2026-07-21 07:23:00'),
(6, 'SID006', 'ARUN KARTHIK M', '25800006@rgcet.edu.in', '1234567890', '202cb962ac59075b964b07152d234b70', 1, '2026-07-20 07:23:00', '2026-07-21 07:23:00'),
(7, 'SID007', 'BALAJI M', '25800007@rgcet.edu.in', '1234567890', '202cb962ac59075b964b07152d234b70', 1, '2026-07-20 07:23:00', '2026-07-21 07:23:00'),
(8, 'SID008', 'BARANIDHARAN B', '25800008@rgcet.edu.in', '1234567890', '202cb962ac59075b964b07152d234b70', 1, '2026-07-20 07:23:00', '2026-07-21 07:23:00'),
(9, 'SID009', 'BEGANRAJ S', '25800009@rgcet.edu.in', '1234567890', '202cb962ac59075b964b07152d234b70', 1, '2026-07-20 07:23:00', '2026-07-21 07:23:00'),
(10, 'SID010', 'DEENA S', '25800010@rgcet.edu.in', '1234567890', '202cb962ac59075b964b07152d234b70', 1, '2026-07-20 07:23:00', '2026-07-21 07:23:00'),
(11, 'SID011', 'DEEPIKA V', '25800011@rgcet.edu.in', '1234567890', '202cb962ac59075b964b07152d234b70', 1, '2026-07-20 07:23:00', '2026-07-21 07:23:00'),
(12, 'SID012', 'DHANUSHRAJ N', '25800012@rgcet.edu.in', '1234567890', '202cb962ac59075b964b07152d234b70', 1, '2026-07-20 07:23:00', '2026-07-21 07:23:00'),
(13, 'SID013', 'DHARANIDHARAN B', '25800013@rgcet.edu.in', '1234567890', '202cb962ac59075b964b07152d234b70', 1, '2026-07-20 07:23:00', '2026-07-21 07:23:00'),
(14, 'SID014', 'EYALARASAN E', '25800014@rgcet.edu.in', '1234567890', '202cb962ac59075b964b07152d234b70', 1, '2026-07-20 07:23:00', '2026-07-21 07:23:00'),
(15, 'SID015', 'EZHILARASI C', '25800015@rgcet.edu.in', '1234567890', '202cb962ac59075b964b07152d234b70', 1, '2026-07-20 07:23:00', '2026-07-21 07:23:00'),
(16, 'SID016', 'GRISH A', '25800016@rgcet.edu.in', '1234567890', '202cb962ac59075b964b07152d234b70', 1, '2026-07-20 07:23:00', '2026-07-21 07:23:00'),
(17, 'SID017', 'HARI G', '25800017@rgcet.edu.in', '1234567890', '202cb962ac59075b964b07152d234b70', 1, '2026-07-20 07:23:00', '2026-07-21 07:23:00'),
(18, 'SID018', 'HARIHARAN S', '25800018@rgcet.edu.in', '1234567890', '202cb962ac59075b964b07152d234b70', 1, '2026-07-20 07:23:00', '2026-07-21 07:23:00'),
(19, 'SID019', 'HARISH KUMAR M', '25800019@rgcet.edu.in', '1234567890', '202cb962ac59075b964b07152d234b70', 1, '2026-07-20 07:23:00', '2026-07-21 07:23:00'),
(20, 'SID020', 'INDHUJA V', '25800020@rgcet.edu.in', '1234567890', '202cb962ac59075b964b07152d234b70', 1, '2026-07-20 07:23:00', '2026-07-21 07:23:00'),
(21, 'SID021', 'JAFREN BEGUM M F', '25800021@rgcet.edu.in', '1234567890', '202cb962ac59075b964b07152d234b70', 1, '2026-07-20 07:23:00', '2026-07-21 07:23:00'),
(22, 'SID022', 'JAYAPRAKASH S', '25800022@rgcet.edu.in', '1234567890', '202cb962ac59075b964b07152d234b70', 1, '2026-07-20 07:23:00', '2026-07-21 07:23:00'),
(23, 'SID023', 'JAYAPRIYA J', '25800023@rgcet.edu.in', '1234567890', '202cb962ac59075b964b07152d234b70', 1, '2026-07-20 07:23:00', '2026-07-21 07:23:00'),
(24, 'SID024', 'KARTHIKEYAN D', '25800024@rgcet.edu.in', '1234567890', '202cb962ac59075b964b07152d234b70', 1, '2026-07-20 07:23:00', '2026-07-21 07:23:00'),
(25, 'SID025', 'KAVINILA D K', '25800025@rgcet.edu.in', '1234567890', '202cb962ac59075b964b07152d234b70', 1, '2026-07-20 07:23:00', '2026-07-21 07:23:00'),
(26, 'SID026', 'KAVIYA N', '25800026@rgcet.edu.in', '1234567890', '202cb962ac59075b964b07152d234b70', 1, '2026-07-20 07:23:00', '2026-07-21 07:23:00'),
(27, 'SID027', 'KAVIYA P', '25800027@rgcet.edu.in', '1234567890', '202cb962ac59075b964b07152d234b70', 1, '2026-07-20 07:23:00', '2026-07-21 07:23:00'),
(28, 'SID028', 'KRISHNAANAND S', '25800028@rgcet.edu.in', '1234567890', '202cb962ac59075b964b07152d234b70', 1, '2026-07-20 07:23:00', '2026-07-21 07:23:00'),
(29, 'SID029', 'MAHESHWARAN B', '25800029@rgcet.edu.in', '1234567890', '202cb962ac59075b964b07152d234b70', 1, '2026-07-20 07:23:00', '2026-07-21 07:23:00'),
(30, 'SID030', 'MANI MARAN K', '25800030@rgcet.edu.in', '1234567890', '202cb962ac59075b964b07152d234b70', 1, '2026-07-20 07:23:00', '2026-07-21 07:23:00'),
(31, 'SID031', 'MOHAMED MOHASIR T', '25800031@rgcet.edu.in', '1234567890', '202cb962ac59075b964b07152d234b70', 1, '2026-07-20 07:23:00', '2026-07-21 07:23:00'),
(32, 'SID032', 'MONISHA E', '25800032@rgcet.edu.in', '1234567890', '202cb962ac59075b964b07152d234b70', 1, '2026-07-20 07:23:00', '2026-07-21 07:23:00'),
(33, 'SID033', 'PARTHIBAN M', '25800033@rgcet.edu.in', '1234567890', '202cb962ac59075b964b07152d234b70', 1, '2026-07-20 07:23:00', '2026-07-21 07:23:00'),
(34, 'SID034', 'PRASANTH SIVAKUMAR', '25800034@rgcet.edu.in', '1234567890', '202cb962ac59075b964b07152d234b70', 1, '2026-07-20 07:23:00', '2026-07-21 07:23:00'),
(35, 'SID035', 'PRASANTH T', '25800035@rgcet.edu.in', '1234567890', '202cb962ac59075b964b07152d234b70', 1, '2026-07-20 07:23:00', '2026-07-21 07:23:00'),
(36, 'SID036', 'PREMALIKA R', '25800036@rgcet.edu.in', '1234567890', '202cb962ac59075b964b07152d234b70', 1, '2026-07-20 07:23:00', '2026-07-21 07:23:00'),
(37, 'SID037', 'PRIYADHARSHINI S', '25800037@rgcet.edu.in', '1234567890', '202cb962ac59075b964b07152d234b70', 1, '2026-07-20 07:23:00', '2026-07-21 07:23:00'),
(38, 'SID038', 'RAJESH KANNA M', '25800038@rgcet.edu.in', '1234567890', '202cb962ac59075b964b07152d234b70', 1, '2026-07-20 07:23:00', '2026-07-21 07:23:00'),
(39, 'SID039', 'SABAPATHI R', '25800039@rgcet.edu.in', '1234567890', '202cb962ac59075b964b07152d234b70', 1, '2026-07-20 07:23:00', '2026-07-21 07:23:00'),
(40, 'SID040', 'SAFIYA M', '25800040@rgcet.edu.in', '1234567890', '202cb962ac59075b964b07152d234b70', 1, '2026-07-20 07:23:00', '2026-07-21 07:23:00'),
(41, 'SID041', 'SANDHIYA R', '25800041@rgcet.edu.in', '1234567890', '202cb962ac59075b964b07152d234b70', 1, '2026-07-20 07:23:00', '2026-07-21 07:23:00'),
(42, 'SID042', 'SANJAI S', '25800042@rgcet.edu.in', '1234567890', '202cb962ac59075b964b07152d234b70', 1, '2026-07-20 07:23:00', '2026-07-21 07:23:00'),
(43, 'SID043', 'SATHIYA R', '25800043@rgcet.edu.in', '1234567890', '202cb962ac59075b964b07152d234b70', 1, '2026-07-20 07:23:00', '2026-07-21 07:23:00'),
(44, 'SID044', 'SATHYA B', '25800044@rgcet.edu.in', '1234567890', '202cb962ac59075b964b07152d234b70', 1, '2026-07-20 07:23:00', '2026-07-21 07:23:00'),
(45, 'SID045', 'SENTHAMIZHSELVI S', '25800045@rgcet.edu.in', '1234567890', '202cb962ac59075b964b07152d234b70', 1, '2026-07-20 07:23:00', '2026-07-21 07:23:00'),
(46, 'SID046', 'SUBHA V', '25800046@rgcet.edu.in', '1234567890', '202cb962ac59075b964b07152d234b70', 1, '2026-07-20 07:23:00', '2026-07-21 07:23:00'),
(47, 'SID047', 'SURESH K', '25800047@rgcet.edu.in', '1234567890', '202cb962ac59075b964b07152d234b70', 1, '2026-07-20 07:23:00', '2026-07-21 07:23:00'),
(48, 'SID048', 'THAMILARASAN P', '25800048@rgcet.edu.in', '1234567890', '202cb962ac59075b964b07152d234b70', 1, '2026-07-20 07:23:00', '2026-07-21 07:23:00'),
(49, 'SID049', 'UDHAYAN K', '25800049@rgcet.edu.in', '1234567890', '202cb962ac59075b964b07152d234b70', 1, '2026-07-20 07:23:00', '2026-07-21 07:23:00'),
(50, 'SID050', 'VISHWASRI P', '25800050@rgcet.edu.in', '1234567890', '202cb962ac59075b964b07152d234b70', 1, '2026-07-20 07:23:00', '2026-07-21 07:23:00');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tblauthors`
--
ALTER TABLE `tblauthors`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tblbooks`
--
ALTER TABLE `tblbooks`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tblcategory`
--
ALTER TABLE `tblcategory`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tblissuedbookdetails`
--
ALTER TABLE `tblissuedbookdetails`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tblreservations`
--
ALTER TABLE `tblreservations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tblstudents`
--
ALTER TABLE `tblstudents`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `StudentId` (`StudentId`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tblauthors`
--
ALTER TABLE `tblauthors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `tblbooks`
--
ALTER TABLE `tblbooks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `tblcategory`
--
ALTER TABLE `tblcategory`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `tblissuedbookdetails`
--
ALTER TABLE `tblissuedbookdetails`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tblreservations`
--
ALTER TABLE `tblreservations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT for table `tblstudents`
--
ALTER TABLE `tblstudents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
