<?php
// DB credentials.
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'library');
// Establish database connection.
try {
  $dbh = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
  $dbh->exec("CREATE TABLE IF NOT EXISTS `tblreservations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `BookId` int(11) NOT NULL,
  `StudentID` varchar(150) NOT NULL,
  `ReservationDate` timestamp NULL DEFAULT current_timestamp(),
  `Status` varchar(50) NOT NULL DEFAULT 'Reserved',
  `AdminRemark` mediumtext DEFAULT NULL,
  `UpdationDate` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

  $dbh->exec("CREATE TABLE IF NOT EXISTS tblteachers (
  id int(11) NOT NULL AUTO_INCREMENT,
  TeacherId varchar(100) DEFAULT NULL,
  FullName varchar(120) DEFAULT NULL,
  EmailId varchar(120) DEFAULT NULL,
  MobileNumber char(11) DEFAULT NULL,
  Password varchar(120) DEFAULT NULL,
  Status int(1) DEFAULT NULL,
  RegDate timestamp NULL DEFAULT current_timestamp(),
  UpdationDate timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (id),
  UNIQUE KEY TeacherId (TeacherId)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;");

  $dbh->exec("CREATE TABLE IF NOT EXISTS `tblnotifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `recipient_email` varchar(120) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `status` varchar(50) NOT NULL,
  `error_message` text DEFAULT NULL,
  `related_id` int(11) DEFAULT NULL,
  `notification_type` varchar(50) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;");


  try {
    $dbh->query("SELECT bookPdf FROM tblbooks LIMIT 1");
  } catch (Exception $ex) {
    $dbh->exec("ALTER TABLE `tblbooks` ADD `bookPdf` varchar(255) DEFAULT NULL AFTER `bookImage`");
  }
} catch (PDOException $e) {
  exit("Error: " . $e->getMessage());
}
?>