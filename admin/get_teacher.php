<?php 
require_once("includes/config.php");
if(!empty($_POST["teacherid"])) {
  $teacherid= strtoupper($_POST["teacherid"]);
 
    $sql ="SELECT FullName,Status,EmailId,MobileNumber FROM tblteachers WHERE TeacherId=:teacherid";
$query= $dbh -> prepare($sql);
$query-> bindParam(':teacherid', $teacherid, PDO::PARAM_STR);
$query-> execute();
$results = $query -> fetchAll(PDO::FETCH_OBJ);
$cnt=1;
if($query -> rowCount() > 0)
{
foreach ($results as $result) {
if($result->Status==0)
{
echo "<span style='color:red'> Teacher ID Blocked </span>"."<br />";
echo "<b>Teacher Name-</b>" .$result->FullName;
 echo "<script>$('#submit').prop('disabled',true);</script>";
} else {
?>


<?php  
echo htmlentities($result->FullName)."<br />";
echo htmlentities($result->EmailId)."<br />";
echo htmlentities($result->MobileNumber);
 echo "<script>$('#submit').prop('disabled',false);</script>";
}
}
}
 else{
  
  echo "<span style='color:red'> Invalid Teacher Id. Please Enter Valid Teacher id .</span>";
 echo "<script>$('#submit').prop('disabled',true);</script>";
}
}



?>
