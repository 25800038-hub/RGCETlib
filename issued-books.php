<?php
session_start();
error_reporting(0);
include('includes/config.php');
if(strlen($_SESSION['login'])==0)
    {   
header('location:index.php');
}
else{
$sid=$_SESSION['stdid'];
$overdueBooks = array();
$sqlOverdue = "SELECT tblbooks.BookName, tblissuedbookdetails.IssuesDate FROM tblissuedbookdetails JOIN tblstudents ON tblstudents.StudentId = tblissuedbookdetails.StudentId JOIN tblbooks ON tblbooks.id = tblissuedbookdetails.BookId WHERE tblstudents.StudentId=:sid AND (tblissuedbookdetails.RetrunStatus='' OR tblissuedbookdetails.RetrunStatus IS NULL) ORDER BY tblissuedbookdetails.IssuesDate ASC";
$queryOverdue = $dbh->prepare($sqlOverdue);
$queryOverdue->bindParam(':sid', $sid, PDO::PARAM_STR);
$queryOverdue->execute();
$resultsOverdue = $queryOverdue->fetchAll(PDO::FETCH_OBJ);
$overdueCount = 0;
if($queryOverdue->rowCount() > 0)
{
    foreach($resultsOverdue as $row)
    {
        $issueDateRaw = $row->IssuesDate;
        if(!empty($issueDateRaw))
        {
            $issueDate = DateTime::createFromFormat('Y-m-d H:i:s', $issueDateRaw);
            if(!$issueDate)
            {
                $issueDate = DateTime::createFromFormat('Y-m-d', $issueDateRaw);
            }
            if($issueDate)
            {
                $today = new DateTime('today');
                $interval = $today->diff($issueDate);
                $daysSinceIssue = (int)$interval->format('%r%a');
                if($daysSinceIssue >= 7)
                {
                    $overdueBooks[] = $row;
                    $overdueCount++;
                }
            }
        }
    }
}
if($overdueCount > 0)
{
    $overdueMessage = 'Overdue Alert: You have ' . $overdueCount . ' issued book(s) overdue for 7 days or more. Please return them soon.';
    echo '<script>alert('.json_encode($overdueMessage).');</script>';
}
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Online Library Management System |  Issued Books</title>
    <!-- BOOTSTRAP CORE STYLE  -->
    <link href="assets/css/bootstrap.css" rel="stylesheet" />
    <!-- FONT AWESOME STYLE  -->
    <link href="assets/css/font-awesome.css" rel="stylesheet" />
    <!-- DATATABLE STYLE  -->
    <link href="assets/js/dataTables/dataTables.bootstrap.css" rel="stylesheet" />
    <!-- CUSTOM STYLE  -->
    <link href="assets/css/style.css" rel="stylesheet" />
    <!-- GOOGLE FONT -->
    <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css' />

</head>
<body>
      <!------MENU SECTION START-->
<?php include('includes/header.php');?>
<!-- MENU SECTION END-->
    <div class="content-wrapper">
         <div class="container">
        <div class="row pad-botm">
            <div class="col-md-12">
                <h4 class="header-line">Manage Issued Books</h4>
    </div>
    

            <div class="row">
                <div class="col-md-12">
                    <!-- Advanced Tables -->
                    <div class="panel panel-default">
                        <div class="panel-heading">
                          Issued Books 
                        </div>
                        <div class="panel-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover" id="dataTables-example">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Book Name</th>
                                            <th>ISBN </th>
                                            <th>Issued Date</th>
                                            <th>Return Date</th>
                                            <th>Fine in(USD)</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
<?php 
$sid=$_SESSION['stdid'];
$sql="SELECT tblbooks.id as bookid,tblbooks.BookName,tblbooks.ISBNNumber,tblbooks.bookPdf,tblissuedbookdetails.IssuesDate,tblissuedbookdetails.ReturnDate,tblissuedbookdetails.id as rid,tblissuedbookdetails.fine from  tblissuedbookdetails join tblstudents on tblstudents.StudentId=tblissuedbookdetails.StudentId join tblbooks on tblbooks.id=tblissuedbookdetails.BookId where tblstudents.StudentId=:sid order by tblissuedbookdetails.id desc";
$query = $dbh -> prepare($sql);
$query-> bindParam(':sid', $sid, PDO::PARAM_STR);
$query->execute();
$results=$query->fetchAll(PDO::FETCH_OBJ);
$cnt=1;
if($query->rowCount() > 0)
{
foreach($results as $result)
{               ?>                                      
                                        <?php
$issueDate = new DateTime($result->IssuesDate);
$today = new DateTime(date('Y-m-d'));
$daysSinceIssue = (int)$today->diff($issueDate)->format('%r%a');
$isOverdue = ($daysSinceIssue >= 7 && $result->ReturnDate == "");
$hasPdf = (!empty($result->bookPdf) && file_exists("admin/bookpdf/" . $result->bookPdf));
?>
<tr class="odd gradeX<?php echo $isOverdue ? ' danger' : ''; ?>">
                                            <td class="center"><?php echo htmlentities($cnt);?></td>
                                            <td class="center">
                                                <strong><?php echo htmlentities($result->BookName);?></strong>
                                            </td>
                                            <td class="center"><?php echo htmlentities($result->ISBNNumber);?></td>
                                            <td class="center"><?php echo htmlentities($result->IssuesDate);?></td>
                                            <td class="center"><?php if($result->ReturnDate=="")
                                            {?>
                                            <span style="color:red">
                                             <?php   echo htmlentities("Not Return Yet"); ?>
                                                </span>
                                            <?php } else {
                                            echo htmlentities($result->ReturnDate);
                                        }
                                            ?></td>
                                              <td class="center"><?php echo htmlentities($result->fine);?></td>
                                              <td class="center">
                                                <?php if($hasPdf) { ?>
                                                    <a href="read-book.php?id=<?php echo htmlentities($result->bookid);?>" class="btn btn-primary btn-xs">
                                                        <i class="fa fa-book"></i> Read Online
                                                    </a>
                                                <?php } else { ?>
                                                    <span class="text-muted"><i class="fa fa-file-pdf-o"></i> No PDF</span>
                                                <?php } ?>
                                              </td>
                                        </tr>
 <?php $cnt=$cnt+1;}} ?>                                      
                                    </tbody>
                                </table>
                            </div>
                            
                        </div>
                    </div>
                    <!--End Advanced Tables -->
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
    <!-- DATATABLE SCRIPTS  -->
    <script src="assets/js/dataTables/jquery.dataTables.js"></script>
    <script src="assets/js/dataTables/dataTables.bootstrap.js"></script>
      <!-- CUSTOM SCRIPTS  -->
    <script src="assets/js/custom.js"></script>

</body>
</html>
<?php } ?>
