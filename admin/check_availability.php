<?php 
require_once("includes/config.php");

if (!empty($_POST["emailid"])) {
    $email = trim($_POST["emailid"]);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<span style='color:red'>Please enter a valid email format.</span>";
        echo "<script>$('#submit').prop('disabled', true);</script>";
        exit;
    }

    $sql = "SELECT id FROM tblstudents WHERE EmailId = :email";
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

if(!empty($_POST["isbn"])) {
    $isbn = $_POST["isbn"];
    $sql ="SELECT id FROM tblbooks WHERE ISBNNumber=:isbn";
    $query = $dbh -> prepare($sql);
    $query-> bindParam(':isbn', $isbn, PDO::PARAM_STR);
    $query-> execute();
    $results = $query -> fetchAll(PDO::FETCH_OBJ);

    if($query -> rowCount() > 0){
        echo "<span style='color:red'> ISBN already exists with another book. .</span>"; 
        echo "<script>$('#add').prop('disabled',true);</script>";
    } else { echo "<script>$('#add').prop('disabled',false);</script>";}
}