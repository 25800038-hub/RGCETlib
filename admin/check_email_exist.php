<?php 
require_once("includes/config.php");

if(!empty($_POST["emailid"])) {
    $email = trim($_POST["emailid"]);
    
    // Check both students and teachers table
    $sql ="SELECT EmailId FROM tblstudents WHERE EmailId=:email UNION SELECT EmailId FROM tblteachers WHERE EmailId=:email";
    $query = $dbh->prepare($sql);
    $query->bindParam(':email', $email, PDO::PARAM_STR);
    $query->execute();
    
    if($query->rowCount() > 0) {
        echo "true";
    } else {
        echo "false";
    }
}
?>
