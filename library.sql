뿯붿-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)

--

-- Host: localhost    Database: library

-- ------------------------------------------------------

-- Server version	10.4.32-MariaDB



/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;

/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;

/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;

/*!40101 SET NAMES utf8mb4 */;

/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;

/*!40103 SET TIME_ZONE='+00:00' */;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;

/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;

/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;



--

-- Table structure for table `admin`

--



DROP TABLE IF EXISTS `admin`;

/*!40101 SET @saved_cs_client     = @@character_set_client */;

/*!40101 SET character_set_client = utf8 */;

CREATE TABLE `admin` (

  `id` int(11) NOT NULL AUTO_INCREMENT,

  `FullName` varchar(100) DEFAULT NULL,

  `AdminEmail` varchar(120) DEFAULT NULL,

  `UserName` varchar(100) NOT NULL,

  `Password` varchar(100) NOT NULL,

  `updationDate` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE current_timestamp(),

  PRIMARY KEY (`id`)

) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

/*!40101 SET character_set_client = @saved_cs_client */;



--

-- Dumping data for table `admin`

--



LOCK TABLES `admin` WRITE;

/*!40000 ALTER TABLE `admin` DISABLE KEYS */;

INSERT INTO `admin` VALUES (1,'RGCET','admin@gmail.com','admin','202cb962ac59075b964b07152d234b70','2026-08-23 05:44:08');

/*!40000 ALTER TABLE `admin` ENABLE KEYS */;

UNLOCK TABLES;



--

-- Table structure for table `tblauthors`

--



DROP TABLE IF EXISTS `tblauthors`;

/*!40101 SET @saved_cs_client     = @@character_set_client */;

/*!40101 SET character_set_client = utf8 */;

CREATE TABLE `tblauthors` (

  `id` int(11) NOT NULL AUTO_INCREMENT,

  `AuthorName` varchar(159) DEFAULT NULL,

  `creationDate` timestamp NULL DEFAULT current_timestamp(),

  `UpdationDate` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),

  PRIMARY KEY (`id`)

) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

/*!40101 SET character_set_client = @saved_cs_client */;



--

-- Dumping data for table `tblauthors`

--



LOCK TABLES `tblauthors` WRITE;

/*!40000 ALTER TABLE `tblauthors` DISABLE KEYS */;

INSERT INTO `tblauthors` VALUES (1,'Anuj kumar','2023-12-31 21:23:03','2025-01-07 06:18:43'),(2,'Chetan Bhagatt','2023-12-31 21:23:03','2025-01-07 06:18:50'),(3,'Anita Desai','2023-12-31 21:23:03','2025-01-07 06:18:50'),(4,'HC Verma','2023-12-31 21:23:03','2025-01-07 06:18:50'),(5,'R.D. Sharma ','2023-12-31 21:23:03','2025-01-07 06:18:50'),(9,'fwdfrwer','2023-12-31 21:23:03','2025-01-07 06:18:50'),(10,'Dr. Andy Williams','2023-12-31 21:23:03','2025-01-07 06:18:50'),(11,'Kyle Hill','2023-12-31 21:23:03','2025-01-07 06:18:50'),(12,'Robert T. Kiyosak','2023-12-31 21:23:03','2025-01-07 06:18:50'),(13,'Kelly Barnhill','2023-12-31 21:23:03','2025-01-07 06:18:50'),(14,'Herbert Schildt','2023-12-31 21:23:03','2025-01-07 06:18:50'),(16,' Tiffany Timbers','2025-01-07 06:55:54',NULL);

/*!40000 ALTER TABLE `tblauthors` ENABLE KEYS */;

UNLOCK TABLES;



--

-- Table structure for table `tblbooks`

--



DROP TABLE IF EXISTS `tblbooks`;

/*!40101 SET @saved_cs_client     = @@character_set_client */;

/*!40101 SET character_set_client = utf8 */;

CREATE TABLE `tblbooks` (

  `id` int(11) NOT NULL AUTO_INCREMENT,

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

  `bookQty` int(11) DEFAULT NULL,

  PRIMARY KEY (`id`)

) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

/*!40101 SET character_set_client = @saved_cs_client */;



--

-- Dumping data for table `tblbooks`

--



LOCK TABLES `tblbooks` WRITE;

/*!40000 ALTER TABLE `tblbooks` DISABLE KEYS */;

INSERT INTO `tblbooks` VALUES (1,'PHP And MySql programming',5,1,'11111',20.00,'1efecc0ca822e40b7b673c0d79ae943f.jpg',NULL,0,'2024-01-02 01:23:03','2025-01-14 07:08:11',10),(3,'physics',6,4,'22222',15.00,'dd8267b57e0e4feee5911cb1e1a03a79.jpg',NULL,NULL,'2024-01-02 01:23:03','2025-01-13 11:11:01',10),(5,'Murach\'s MySQL',5,1,'333333',455.00,'5939d64655b4d2ae443830d73abc35b6.jpg','097aa6728450d19416a19c1c6dbb0430.pdf',NULL,'2024-01-02 01:23:03','2026-08-14 13:59:35',20),(6,'WordPress for Beginners 2022: A Visual Step-by-Step Guide to Mastering WordPress',5,10,'44444',100.00,'144ab706ba1cb9f6c23fd6ae9c0502b3.jpg',NULL,NULL,'2024-01-02 01:23:03','2025-01-13 11:05:35',15),(7,'WordPress Mastery Guide:',5,11,'555555',53.00,'90083a56014186e88ffca10286172e64.jpg',NULL,0,'2024-01-02 01:23:03','2026-08-01 15:13:12',14),(8,'Rich Dad Poor Dad: What the Rich Teach Their Kids About Money',8,12,'66666',120.00,'52411b2bd2a6b2e0df3eb10943a5b640.jpg','8aadc7d1a7bfae1b6637ffd30c8e81a6.pdf',NULL,'2024-01-02 01:23:03','2026-08-15 06:48:51',5),(9,'The Girl Who Drank the Moon',8,13,'77777',200.00,'f05cd198ac9335245e1fdffa793207a7.jpg','82b4dfc4078ca8d0e315ddb1c833a520.pdf',NULL,'2024-01-02 01:23:03','2026-08-14 13:40:40',1),(10,'C++: The Complete Reference, 4th Edition',5,14,'88888',142.00,'36af5de9012bf8c804e499dc3c3b33a5.jpg','5384a5b7dfb5cafc18e02b92ee0c4b70.pdf',NULL,'2024-01-02 01:23:03','2026-08-14 13:32:26',2),(11,'ASP.NET Core 5 for Beginners',9,11,'99999',422.00,'b1b6788016bbfab12cfd2722604badc9.jpg',NULL,NULL,'2024-01-02 01:23:03','2025-01-13 11:11:01',5),(12,'Python Packages',9,16,'00000',3034.00,'ba719639def504c64ebac89cdd0d0a85.jpg','d299b2c429241cce34cd67adaf7c86e5.pdf',0,'2025-01-07 06:56:50','2026-08-15 06:50:35',25);

/*!40000 ALTER TABLE `tblbooks` ENABLE KEYS */;

UNLOCK TABLES;



--

-- Table structure for table `tblcategory`

--



DROP TABLE IF EXISTS `tblcategory`;

/*!40101 SET @saved_cs_client     = @@character_set_client */;

/*!40101 SET character_set_client = utf8 */;

CREATE TABLE `tblcategory` (

  `id` int(11) NOT NULL AUTO_INCREMENT,

  `CategoryName` varchar(150) DEFAULT NULL,

  `Status` int(1) DEFAULT NULL,

  `CreationDate` timestamp NULL DEFAULT current_timestamp(),

  `UpdationDate` timestamp NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE current_timestamp(),

  PRIMARY KEY (`id`)

) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

/*!40101 SET character_set_client = @saved_cs_client */;



--

-- Dumping data for table `tblcategory`

--



LOCK TABLES `tblcategory` WRITE;

/*!40000 ALTER TABLE `tblcategory` DISABLE KEYS */;

INSERT INTO `tblcategory` VALUES (4,'Romantic',1,'2025-01-01 07:23:03','2025-01-07 06:19:11'),(5,'Technology',1,'2025-01-01 07:23:03','2025-01-07 06:19:21'),(6,'Science',1,'2025-01-01 07:23:03','2025-01-07 06:19:21'),(7,'Management',1,'2025-01-01 07:23:03','2025-01-07 06:19:21'),(8,'General',1,'2025-01-01 07:23:03','2025-01-07 06:19:21'),(9,'Programming',1,'2025-01-01 07:23:03','2025-01-07 06:19:21');

/*!40000 ALTER TABLE `tblcategory` ENABLE KEYS */;

UNLOCK TABLES;



--

-- Table structure for table `tblissuedbookdetails`

--



DROP TABLE IF EXISTS `tblissuedbookdetails`;

/*!40101 SET @saved_cs_client     = @@character_set_client */;

/*!40101 SET character_set_client = utf8 */;

CREATE TABLE `tblissuedbookdetails` (

  `id` int(11) NOT NULL AUTO_INCREMENT,

  `BookId` int(11) DEFAULT NULL,

  `StudentID` varchar(150) DEFAULT NULL,

  `IssuesDate` timestamp NULL DEFAULT current_timestamp(),

  `ReturnDate` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),

  `RetrunStatus` int(1) DEFAULT NULL,

  `fine` int(11) DEFAULT NULL,

  `remark` mediumtext NOT NULL,

  PRIMARY KEY (`id`)

) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

/*!40101 SET character_set_client = @saved_cs_client */;



--

-- Dumping data for table `tblissuedbookdetails`

--



LOCK TABLES `tblissuedbookdetails` WRITE;

/*!40000 ALTER TABLE `tblissuedbookdetails` DISABLE KEYS */;

INSERT INTO `tblissuedbookdetails` VALUES (6,7,'SID038','2026-07-01 17:28:12','2026-08-01 15:13:12',1,23,'0'),(7,1,'SID038','2026-07-01 15:25:07','2026-08-01 15:43:24',1,23,'None'),(8,12,'SID038','2026-08-14 12:36:56','2026-08-15 06:50:35',1,0,'Collected from Online Reservation #2'),(9,12,'TID001','2026-08-10 14:08:47',NULL,NULL,NULL,'? Good standing: Teacher has clean borrowing history.'),(10,1,'TID003','2026-08-22 14:26:05',NULL,NULL,NULL,'? Good standing: Teacher has clean borrowing history.'),(11,1,'SID038','2026-08-10 15:23:00',NULL,NULL,NULL,'?? WARNING: Student has history of returning books late (3 times). Monitor this issue.'),(12,8,'SID038','2026-08-22 16:06:09',NULL,NULL,NULL,'Collected from Online Reservation #5'),(13,8,'TID003','2026-08-22 16:10:42',NULL,NULL,NULL,'Collected from Online Reservation #4');

/*!40000 ALTER TABLE `tblissuedbookdetails` ENABLE KEYS */;

UNLOCK TABLES;



--

-- Table structure for table `tblreservations`

--



DROP TABLE IF EXISTS `tblreservations`;

/*!40101 SET @saved_cs_client     = @@character_set_client */;

/*!40101 SET character_set_client = utf8 */;

CREATE TABLE `tblreservations` (

  `id` int(11) NOT NULL AUTO_INCREMENT,

  `BookId` int(11) NOT NULL,

  `StudentID` varchar(150) NOT NULL,

  `ReservationDate` timestamp NULL DEFAULT current_timestamp(),

  `Status` varchar(50) NOT NULL DEFAULT 'Reserved',

  `AdminRemark` mediumtext DEFAULT NULL,

  `UpdationDate` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),

  PRIMARY KEY (`id`)

) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

/*!40101 SET character_set_client = @saved_cs_client */;



--

-- Dumping data for table `tblreservations`

--



LOCK TABLES `tblreservations` WRITE;

/*!40000 ALTER TABLE `tblreservations` DISABLE KEYS */;

INSERT INTO `tblreservations` VALUES (1,12,'SID038','2026-08-14 12:31:17','Cancelled',NULL,'2026-08-14 12:31:49'),(2,12,'SID038','2026-08-14 12:32:12','Collected','Collected from Online Reservation #2','2026-08-14 12:36:56'),(3,1,'SID038','2026-08-14 12:33:33','Cancelled','Cancelled by Admin','2026-08-14 12:38:23'),(4,8,'TID003','2026-08-22 15:58:04','Collected','Collected from Online Reservation #4','2026-08-22 16:10:42'),(5,8,'SID038','2026-08-22 16:05:42','Collected','Collected from Online Reservation #5','2026-08-22 16:06:09');

/*!40000 ALTER TABLE `tblreservations` ENABLE KEYS */;

UNLOCK TABLES;



--

-- Table structure for table `tblstudents`

--



DROP TABLE IF EXISTS `tblstudents`;

/*!40101 SET @saved_cs_client     = @@character_set_client */;

/*!40101 SET character_set_client = utf8 */;

CREATE TABLE `tblstudents` (

  `id` int(11) NOT NULL AUTO_INCREMENT,

  `StudentId` varchar(100) DEFAULT NULL,

  `FullName` varchar(120) DEFAULT NULL,

  `EmailId` varchar(120) DEFAULT NULL,

  `MobileNumber` char(11) DEFAULT NULL,

  `Password` varchar(120) DEFAULT NULL,

  `Status` int(1) DEFAULT NULL,

  `RegDate` timestamp NULL DEFAULT current_timestamp(),

  `UpdationDate` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),

  `Department` varchar(100) DEFAULT NULL,

  `Year` varchar(10) DEFAULT NULL,

  PRIMARY KEY (`id`),

  UNIQUE KEY `StudentId` (`StudentId`)

) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

/*!40101 SET character_set_client = @saved_cs_client */;



--

-- Dumping data for table `tblstudents`

--



LOCK TABLES `tblstudents` WRITE;

/*!40000 ALTER TABLE `tblstudents` DISABLE KEYS */;

INSERT INTO `tblstudents` VALUES (1,'SID001','ABDUL RAHMAN U','25800001@rgcet.edu.in','1234567890','202cb962ac59075b964b07152d234b70',1,'2026-07-20 07:23:00','2026-08-23 05:44:08','MCA','II'),(2,'SID002','AGASH S','25800002@rgcet.edu.in','1234567890','202cb962ac59075b964b07152d234b70',1,'2026-07-20 07:23:00','2026-08-23 05:44:08','MCA','II'),(3,'SID003','AJITH S','25800003@rgcet.edu.in','1234567890','202cb962ac59075b964b07152d234b70',1,'2026-07-20 07:23:00','2026-08-23 05:44:08','MCA','II'),(4,'SID004','ANBARASU B','25800004@rgcet.edu.in','1234567890','202cb962ac59075b964b07152d234b70',1,'2026-07-20 07:23:00','2026-08-23 05:44:08','MCA','II'),(5,'SID005','ANTONY EMMANUEL JAMES A','25800005@rgcet.edu.in','1234567890','202cb962ac59075b964b07152d234b70',1,'2026-07-20 07:23:00','2026-08-23 05:44:08','MCA','II'),(6,'SID006','ARUN KARTHIK M','25800006@rgcet.edu.in','1234567890','202cb962ac59075b964b07152d234b70',1,'2026-07-20 07:23:00','2026-08-23 05:44:08','MCA','II'),(7,'SID007','BALAJI M','25800007@rgcet.edu.in','1234567890','202cb962ac59075b964b07152d234b70',1,'2026-07-20 07:23:00','2026-08-23 05:44:08','MCA','II'),(8,'SID008','BARANIDHARAN B','25800008@rgcet.edu.in','1234567890','202cb962ac59075b964b07152d234b70',1,'2026-07-20 07:23:00','2026-08-23 05:44:08','MCA','II'),(9,'SID009','BEGANRAJ S','25800009@rgcet.edu.in','1234567890','202cb962ac59075b964b07152d234b70',1,'2026-07-20 07:23:00','2026-08-23 05:44:08','MCA','II'),(10,'SID010','DEENA S','25800010@rgcet.edu.in','1234567890','202cb962ac59075b964b07152d234b70',1,'2026-07-20 07:23:00','2026-08-23 05:44:08','MCA','II'),(11,'SID011','DEEPIKA V','25800011@rgcet.edu.in','1234567890','202cb962ac59075b964b07152d234b70',1,'2026-07-20 07:23:00','2026-08-23 05:44:08','MCA','II'),(12,'SID012','DHANUSHRAJ N','25800012@rgcet.edu.in','1234567890','202cb962ac59075b964b07152d234b70',1,'2026-07-20 07:23:00','2026-08-23 05:44:08','MCA','II'),(13,'SID013','DHARANIDHARAN B','25800013@rgcet.edu.in','1234567890','202cb962ac59075b964b07152d234b70',1,'2026-07-20 07:23:00','2026-08-23 05:44:08','MCA','II'),(14,'SID014','EYALARASAN E','25800014@rgcet.edu.in','1234567890','202cb962ac59075b964b07152d234b70',1,'2026-07-20 07:23:00','2026-08-23 05:44:08','MCA','II'),(15,'SID015','EZHILARASI C','25800015@rgcet.edu.in','1234567890','202cb962ac59075b964b07152d234b70',1,'2026-07-20 07:23:00','2026-08-23 05:44:08','MCA','II'),(16,'SID016','GRISH A','25800016@rgcet.edu.in','1234567890','202cb962ac59075b964b07152d234b70',1,'2026-07-20 07:23:00','2026-08-23 05:44:08','MCA','II'),(17,'SID017','HARI G','25800017@rgcet.edu.in','1234567890','202cb962ac59075b964b07152d234b70',1,'2026-07-20 07:23:00','2026-08-23 05:44:08','MCA','II'),(18,'SID018','HARIHARAN S','25800018@rgcet.edu.in','1234567890','202cb962ac59075b964b07152d234b70',1,'2026-07-20 07:23:00','2026-08-23 05:44:08','MCA','II'),(19,'SID019','HARISH KUMAR M','25800019@rgcet.edu.in','1234567890','202cb962ac59075b964b07152d234b70',1,'2026-07-20 07:23:00','2026-08-23 05:44:08','MCA','II'),(20,'SID020','INDHUJA V','25800020@rgcet.edu.in','1234567890','202cb962ac59075b964b07152d234b70',1,'2026-07-20 07:23:00','2026-08-23 05:44:08','MCA','II'),(21,'SID021','JAFREN BEGUM M F','25800021@rgcet.edu.in','1234567890','202cb962ac59075b964b07152d234b70',1,'2026-07-20 07:23:00','2026-08-23 05:44:08','MCA','II'),(22,'SID022','JAYAPRAKASH S','25800022@rgcet.edu.in','1234567890','202cb962ac59075b964b07152d234b70',1,'2026-07-20 07:23:00','2026-08-23 05:44:08','MCA','II'),(23,'SID023','JAYAPRIYA J','25800023@rgcet.edu.in','1234567890','202cb962ac59075b964b07152d234b70',1,'2026-07-20 07:23:00','2026-08-23 05:44:08','MCA','II'),(24,'SID024','KARTHIKEYAN D','25800024@rgcet.edu.in','1234567890','202cb962ac59075b964b07152d234b70',1,'2026-07-20 07:23:00','2026-08-23 05:44:08','MCA','II'),(25,'SID025','KAVINILA D K','25800025@rgcet.edu.in','1234567890','202cb962ac59075b964b07152d234b70',1,'2026-07-20 07:23:00','2026-08-23 05:44:08','MCA','II'),(26,'SID026','KAVIYA N','25800026@rgcet.edu.in','1234567890','202cb962ac59075b964b07152d234b70',1,'2026-07-20 07:23:00','2026-08-23 05:44:08','MCA','II'),(27,'SID027','KAVIYA P','25800027@rgcet.edu.in','1234567890','202cb962ac59075b964b07152d234b70',1,'2026-07-20 07:23:00','2026-08-23 05:44:08','MCA','II'),(28,'SID028','KRISHNAANAND S','25800028@rgcet.edu.in','1234567890','202cb962ac59075b964b07152d234b70',1,'2026-07-20 07:23:00','2026-08-23 05:44:08','MCA','II'),(29,'SID029','MAHESHWARAN B','25800029@rgcet.edu.in','1234567890','202cb962ac59075b964b07152d234b70',1,'2026-07-20 07:23:00','2026-08-23 05:44:08','MCA','II'),(30,'SID030','MANI MARAN K','25800030@rgcet.edu.in','1234567890','202cb962ac59075b964b07152d234b70',1,'2026-07-20 07:23:00','2026-08-23 05:44:08','MCA','II'),(31,'SID031','MOHAMED MOHASIR T','25800031@rgcet.edu.in','1234567890','202cb962ac59075b964b07152d234b70',1,'2026-07-20 07:23:00','2026-08-23 05:44:08','MCA','II'),(32,'SID032','MONISHA E','25800032@rgcet.edu.in','1234567890','202cb962ac59075b964b07152d234b70',1,'2026-07-20 07:23:00','2026-08-23 05:44:08','MCA','II'),(33,'SID033','PARTHIBAN M','25800033@rgcet.edu.in','1234567890','202cb962ac59075b964b07152d234b70',1,'2026-07-20 07:23:00','2026-08-23 05:44:08','MCA','II'),(34,'SID034','PRASANTH SIVAKUMAR','25800034@rgcet.edu.in','1234567890','202cb962ac59075b964b07152d234b70',1,'2026-07-20 07:23:00','2026-08-23 05:44:08','MCA','II'),(35,'SID035','PRASANTH T','25800035@rgcet.edu.in','1234567890','202cb962ac59075b964b07152d234b70',1,'2026-07-20 07:23:00','2026-08-23 05:44:08','MCA','II'),(36,'SID036','PREMALIKA R','25800036@rgcet.edu.in','1234567890','202cb962ac59075b964b07152d234b70',1,'2026-07-20 07:23:00','2026-08-23 05:44:08','MCA','II'),(37,'SID037','PRIYADHARSHINI S','25800037@rgcet.edu.in','1234567890','202cb962ac59075b964b07152d234b70',1,'2026-07-20 07:23:00','2026-08-23 05:44:08','MCA','II'),(38,'SID038','RAJESH KANNA M','25800038@rgcet.edu.in','1234567890','202cb962ac59075b964b07152d234b70',1,'2026-07-20 07:23:00','2026-08-23 05:31:54','MCA','II'),(39,'SID039','SABAPATHI R','25800039@rgcet.edu.in','1234567890','202cb962ac59075b964b07152d234b70',1,'2026-07-20 07:23:00','2026-08-23 05:44:08','MCA','II'),(40,'SID040','SAFIYA M','25800040@rgcet.edu.in','1234567890','202cb962ac59075b964b07152d234b70',1,'2026-07-20 07:23:00','2026-08-23 05:44:08','MCA','II'),(41,'SID041','SANDHIYA R','25800041@rgcet.edu.in','1234567890','202cb962ac59075b964b07152d234b70',1,'2026-07-20 07:23:00','2026-08-23 05:44:08','MCA','II'),(42,'SID042','SANJAI S','25800042@rgcet.edu.in','1234567890','202cb962ac59075b964b07152d234b70',1,'2026-07-20 07:23:00','2026-08-23 05:44:08','MCA','II'),(43,'SID043','SATHIYA R','25800043@rgcet.edu.in','1234567890','202cb962ac59075b964b07152d234b70',1,'2026-07-20 07:23:00','2026-08-23 05:44:08','MCA','II'),(44,'SID044','SATHYA B','25800044@rgcet.edu.in','1234567890','202cb962ac59075b964b07152d234b70',1,'2026-07-20 07:23:00','2026-08-23 05:44:08','MCA','II'),(45,'SID045','SENTHAMIZHSELVI S','25800045@rgcet.edu.in','1234567890','202cb962ac59075b964b07152d234b70',1,'2026-07-20 07:23:00','2026-08-23 05:44:08','MCA','II'),(46,'SID046','SUBHA V','25800046@rgcet.edu.in','1234567890','202cb962ac59075b964b07152d234b70',1,'2026-07-20 07:23:00','2026-08-23 05:44:08','MCA','II'),(47,'SID047','SURESH K','25800047@rgcet.edu.in','1234567890','202cb962ac59075b964b07152d234b70',1,'2026-07-20 07:23:00','2026-08-23 05:44:08','MCA','II'),(48,'SID048','THAMILARASAN P','25800048@rgcet.edu.in','1234567890','202cb962ac59075b964b07152d234b70',1,'2026-07-20 07:23:00','2026-08-23 05:44:08','MCA','II'),(49,'SID049','UDHAYAN K','25800049@rgcet.edu.in','1234567890','202cb962ac59075b964b07152d234b70',1,'2026-07-20 07:23:00','2026-08-23 05:44:08','MCA','II'),(50,'SID050','VISHWASRI P','25800050@rgcet.edu.in','1234567890','202cb962ac59075b964b07152d234b70',1,'2026-07-20 07:23:00','2026-08-23 05:44:08','MCA','II');

/*!40000 ALTER TABLE `tblstudents` ENABLE KEYS */;

UNLOCK TABLES;



--

-- Table structure for table `tblteachers`

--



DROP TABLE IF EXISTS `tblteachers`;

/*!40101 SET @saved_cs_client     = @@character_set_client */;

/*!40101 SET character_set_client = utf8 */;

CREATE TABLE `tblteachers` (

  `id` int(11) NOT NULL AUTO_INCREMENT,

  `TeacherId` varchar(100) DEFAULT NULL,

  `FullName` varchar(120) DEFAULT NULL,

  `EmailId` varchar(120) DEFAULT NULL,

  `MobileNumber` char(11) DEFAULT NULL,

  `Password` varchar(120) DEFAULT NULL,

  `Status` int(1) DEFAULT NULL,

  `RegDate` timestamp NULL DEFAULT current_timestamp(),

  `UpdationDate` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),

  `Department` varchar(100) DEFAULT NULL,

  PRIMARY KEY (`id`),

  UNIQUE KEY `TeacherId` (`TeacherId`)

) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

/*!40101 SET character_set_client = @saved_cs_client */;



--

-- Dumping data for table `tblteachers`

--



LOCK TABLES `tblteachers` WRITE;

/*!40000 ALTER TABLE `tblteachers` DISABLE KEYS */;

INSERT INTO `tblteachers` VALUES (16,'TID001','Venkatalakshimi V','venkatalakshimi_v@rgcet.edu.in','1234567890','202cb962ac59075b964b07152d234b70',1,'2026-08-22 14:07:18','2026-08-23 05:44:08','MCA'),(17,'TID002','Rajavalli R','rajavalli_r@rgcet.edu.in','1234567890','202cb962ac59075b964b07152d234b70',1,'2026-08-22 14:14:23','2026-08-23 05:44:08','MCA'),(18,'TID003','Ashiwini A','aswini_a@rgcet.edu.in','1234567890','202cb962ac59075b964b07152d234b70',1,'2026-08-22 14:16:18','2026-08-23 05:44:08','MCA');

/*!40000 ALTER TABLE `tblteachers` ENABLE KEYS */;

UNLOCK TABLES;

/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;



/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;

/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;

/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;

/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;

/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;



-- Dump completed on 2026-08-23 11:14:19

