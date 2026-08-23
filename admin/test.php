<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
$_SESSION['alogin'] = 'admin';
$_POST['return'] = 1;
$_POST['fine'] = '0';
$_POST['bookid'] = '1';
$_GET['rid'] = '1';

try {
    include('d:/Xampp/htdocs/library/admin/update-issue-bookdetails-teacher.php');
} catch (Exception $e) {
    echo "Exception: " . $e->getMessage();
} catch (Error $e) {
    echo "Error: " . $e->getMessage();
}
?>
