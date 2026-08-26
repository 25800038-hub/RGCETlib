<?php
session_start();
error_reporting(0);
include('includes/config.php');
if(strlen($_SESSION['alogin'])==0)
    {   
header('location:index.php');
}
else{ 

if(isset($_POST['issue']))
{
$studentid=strtoupper($_POST['studentid']);
$bookid=$_POST['bookid']; 
$aremark=$_POST['aremark']; 
$isissued=1;
$aqty=$_POST['aqty'];
if($aqty>0){
    // Check student borrowing limits
    $limitSql = "SELECT 
                    (SELECT COUNT(*) FROM tblissuedbookdetails WHERE StudentID = :sid AND (RetrunStatus = '' OR RetrunStatus IS NULL OR RetrunStatus = '0')) AS currentIssued,
                    (SELECT COUNT(*) FROM tblreservations WHERE StudentID = :sid2 AND Status = 'Reserved') AS currentReserved";
    $limitQuery = $dbh->prepare($limitSql);
    $limitQuery->bindParam(':sid', $studentid, PDO::PARAM_STR);
    $limitQuery->bindParam(':sid2', $studentid, PDO::PARAM_STR);
    $limitQuery->execute();
    $limitRow = $limitQuery->fetch(PDO::FETCH_OBJ);
    
    $totalHoldings = intval($limitRow->currentIssued) + intval($limitRow->currentReserved);
    if($totalHoldings >= 3) {
        $_SESSION['error']="Student has reached the maximum borrowing limit of 3 books (Active: $totalHoldings).";
        header('location:manage-issued-books.php');
        exit();
    }

$sql="INSERT INTO  tblissuedbookdetails(StudentID,BookId,remark) VALUES(:studentid,:bookid,:aremark)";
$query = $dbh->prepare($sql);
$query->bindParam(':studentid',$studentid,PDO::PARAM_STR);
$query->bindParam(':bookid',$bookid,PDO::PARAM_STR);
$query->bindParam(':aremark',$aremark,PDO::PARAM_STR);
$query->execute();
$lastInsertId = $dbh->lastInsertId();
if($lastInsertId)
{
    try {
        require_once __DIR__ . '/../services/MailService.php';
        $mailService = new MailService($dbh);
        
        // Fetch book details
        $bookQuery = $dbh->prepare("SELECT BookName FROM tblbooks WHERE id=:bookid");
        $bookQuery->execute([':bookid' => $bookid]);
        $book = $bookQuery->fetch(PDO::FETCH_OBJ);
        
        // Fetch student details
        $userQuery = $dbh->prepare("SELECT FullName, EmailId FROM tblstudents WHERE StudentId=:studentid");
        $userQuery->execute([':studentid' => $studentid]);
        $user = $userQuery->fetch(PDO::FETCH_OBJ);
        
        if($book && $user && !empty($user->EmailId)) {
            $issueDate = date('Y-m-d');
            $dueDate = date('Y-m-d', strtotime('+7 days'));
            
            $subject = "Book Issued Successfully - RGCET Library";
            $htmlBody = "<p>Hello {$user->FullName},</p>
                        <p>Your book has been issued successfully.</p>
                        <p><strong>Book:</strong> {$book->BookName}<br>
                        <strong>Issue Date:</strong> {$issueDate}<br>
                        <strong>Due Date:</strong> {$dueDate}</p>
                        <p>Please return the book on or before the due date to avoid any applicable fine.</p>
                        <p>Thank you,<br>RGCET Library</p>";
                        
            if (!$mailService->sendEmail($user->EmailId, $subject, $htmlBody, $lastInsertId, 'Issue')) {
                $_SESSION['msg'] = "Book issued successfully. Email notification could not be sent.";
            } else {
                $_SESSION['msg']="Book issued successfully";
            }
        } else {
            $_SESSION['msg']="Book issued successfully";
        }
    } catch (\Exception $e) {
        error_log("Email issue failed: " . $e->getMessage());
        $_SESSION['msg'] = "Book issued successfully. Email notification could not be sent.";
    }
    
header('location:manage-issued-books.php');
}
else 
{
$_SESSION['error']="Something went wrong. Please try again";
header('location:manage-issued-books.php');
} } else {
 $_SESSION['error']="Book Not available";
header('location:manage-issued-books.php');   
}

}
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Online Library Management System | Issue a new Book</title>
    <!-- BOOTSTRAP CORE STYLE  -->
    <link href="assets/css/bootstrap.css" rel="stylesheet" />
    <!-- FONT AWESOME STYLE  -->
    <link href="assets/css/font-awesome.css" rel="stylesheet" />
    <!-- CUSTOM STYLE  -->
    <link href="assets/css/style.css" rel="stylesheet" />
    <!-- GOOGLE FONT -->
    <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css' />
<script>
// function for get student name and check borrowing history
function getstudent() {
$("#loaderIcon").show();
jQuery.ajax({
url: "get_student.php",
data:'studentid='+$("#studentid").val(),
type: "POST",
success:function(data){
$("#get_student_name").html(data);

// Check student's borrowing history for remarks
jQuery.ajax({
url: "check_student_history.php",
data:'studentid='+$("#studentid").val(),
type: "POST",
success:function(remarks){
// Auto-fill remarks field
$("#aremark").val(remarks);
},
error:function (){}
});

$("#loaderIcon").hide();
},
error:function (){}
});
}

//function for book details
function getbook() {
$("#loaderIcon").show();
jQuery.ajax({
url: "get_book.php",
data:'bookid='+$("#bookid").val(),
type: "POST",
success:function(data){
$("#get_book_name").html(data);
$("#loaderIcon").hide();
},
error:function (){}
});
}

</script> 
<style type="text/css">
  .others{
    color:red;
}

</style>


</head>
<body>
      <!------MENU SECTION START-->
<?php include('includes/header.php');?>
<!-- MENU SECTION END-->
    <div class="content-wrapper">
         <div class="container">
        <div class="row pad-botm">
            <div class="col-md-12">
                <h4 class="header-line">Issue a New Book</h4>
                
                            </div>

</div>
<div class="row">
<div class="col-md-10 col-sm-6 col-xs-12 col-md-offset-1">
<div class="panel panel-info">
<div class="panel-heading">
Issue a New Book
</div>
<div class="panel-body">
<form role="form" method="post">

<div class="form-group">
<label>Student id<span style="color:red;">*</span></label>
<input class="form-control" type="text" name="studentid" id="studentid" onBlur="getstudent()" autocomplete="off"  required />
</div>

<div class="form-group">
<span id="get_student_name" style="font-size:16px;"></span> 
</div>





<div class="form-group">
<label>ISBN Number or Book Title<span style="color:red;">*</span></label>
<input class="form-control" type="text" name="booikid" id="bookid" onBlur="getbook()"  required="required" />
</div>
 <div class="form-group" id="get_book_name">

 </div>
<div class="form-group">
<label>Remark<span style="color:red;">*</span></label>
<textarea class="form-control"  name="aremark" id="aremark" placeholder="Auto-filled based on student history..." required></textarea>
<small class="text-muted">This field auto-fills based on the student's borrowing history. You can edit it if needed.</small>
</div>


<button type="submit" name="issue" id="submit" class="btn btn-info">Issue Book </button>

                                    </form>
                            </div>
                        </div>
                            </div>

        </div>
   
    </div>
    </div>
     <!-- CONTENT-WRAPPER SECTION END-->
  <?php include('includes/footer.php');?>
      <!-- FOOTER SECTION END-->
    <!-- JAVASCRIPT FILES PLACED AT THE BOTTOM TO REDUCE THE LOADING TIME  -->
    <!-- CORE JQUERY  -->
    <script src="assets/js/jquery-1.10.2.js"></script>
    <!-- BOOTSTRAP SCRIPTS  -->
    <script src="assets/js/bootstrap.js"></script>
      <!-- CUSTOM SCRIPTS  -->
    <script src="assets/js/custom.js"></script>

</body>
</html>
<?php } ?>
