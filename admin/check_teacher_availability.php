<?php 
require_once("includes/config.php");

if (!empty($_POST["emailid"])) {
    $email = trim($_POST["emailid"]);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<span style='color:red'>Please enter a valid email format.</span>";
        echo "<script>$('#submit').prop('disabled', true);</script>";
        exit;
    }

    $sql = "SELECT id FROM tblteachers WHERE EmailId = :email UNION SELECT id FROM tblstudents WHERE EmailId = :email";
    $query = $dbh->prepare($sql);
    $query->bindParam(':email', $email, PDO::PARAM_STR);
    $query->execute();

    if($query->rowCount() > 0){
        echo "<span style='color:red'>Email already exists. Please use another email.</span>";
        echo "<script>$('#submit').prop('disabled', true);</script>";
    } else {
        echo "<span style='color:green'>Email is available.</span>";
        echo "<script>$('#submit').prop('disabled', false);</script>";
    }
    exit;
}
?>
