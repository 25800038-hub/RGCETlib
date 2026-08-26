<?php
session_start();
error_reporting(0);
include('includes/config.php');
if(strlen($_SESSION['alogin'])==0)
    {   
header('location:index.php');
}
else{
$overdueBooks = array();
$sqlOverdue = "SELECT tblissuedbookdetails.id, COALESCE(tblstudents.FullName, tblteachers.FullName) as FullName, tblbooks.BookName, tblissuedbookdetails.IssuesDate FROM tblissuedbookdetails LEFT JOIN tblstudents ON tblstudents.StudentId = tblissuedbookdetails.StudentID LEFT JOIN tblteachers ON tblteachers.TeacherId = tblissuedbookdetails.StudentID JOIN tblbooks ON tblbooks.id = tblissuedbookdetails.BookId WHERE (tblissuedbookdetails.RetrunStatus='' OR tblissuedbookdetails.RetrunStatus IS NULL) ORDER BY tblissuedbookdetails.IssuesDate ASC";
$queryOverdue = $dbh->prepare($sqlOverdue);
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
    $overdueMessage = 'Overdue Alert: ' . $overdueCount . ' issued book(s) are overdue for 7 days or more.';
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
    <title>Online Library Management System | Manage Issued Books</title>
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
    <?php if($_SESSION['error']!="")
    {?>
<div class="col-md-6">
<div class="alert alert-danger" >
 <strong>Error :</strong> 
 <?php echo htmlentities($_SESSION['error']);?>
<?php echo htmlentities($_SESSION['error']="");?>
</div>
</div>
<?php } ?>
<?php if($_SESSION['msg']!="")
{?>
<div class="col-md-6">
<div class="alert alert-success" >
 <strong>Success :</strong> 
 <?php echo htmlentities($_SESSION['msg']);?>
<?php echo htmlentities($_SESSION['msg']="");?>
</div>
</div>
<?php } ?>



   <?php if($_SESSION['delmsg']!="")
    {?>
<div class="col-md-6">
<div class="alert alert-success" >
 <strong>Success :</strong> 
 <?php echo htmlentities($_SESSION['delmsg']);?>
<?php echo htmlentities($_SESSION['delmsg']="");?>
</div>
</div>
<?php } ?>

</div>

        <?php if($overdueCount > 0){ ?>
        <div class="row">
            <div class="col-md-12">
                <div class="alert alert-warning">
                    <strong>Overdue Alert:</strong> <?php echo htmlentities($overdueCount); ?> issued book(s) are overdue for 7 days or more.
                    <ul class="margin-top-10">
                        <?php foreach($overdueBooks as $overdue){ ?>
                            <li><strong><?php echo htmlentities($overdue->FullName); ?></strong> - <?php echo htmlentities($overdue->BookName); ?> (Issued on <?php echo htmlentities($overdue->IssuesDate); ?>)</li>
                        <?php } ?>
                    </ul>
                </div>
            </div>
        </div>
        <?php } ?>

        </div>
            <div class="row">
                <div class="col-md-12">
                    <!-- Advanced Tables -->
                    <div class="panel panel-default">
                        <div class="panel-heading">
                          <?php echo (isset($_GET['status']) && $_GET['status'] == 'not_returned') ? "Books Not Returned Yet" : "Issued Books"; ?>
                        </div>
                        <div class="panel-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover" id="dataTables-example">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th><?php echo (isset($_GET['status']) && $_GET['status'] == 'not_returned') ? "Member Name" : "Student Name"; ?></th>
                                            <th>Book Name</th>
                                            <th>ISBN </th>
                                            <th>Issued Date</th>
                                            <th>Return Date</th>
                                            <th>Days Overdue</th>
                                            <th>Action</th>
                                            <th style="display:none;">Department</th>
                                            <th style="display:none;">Year</th>
                                        </tr>
                                    </thead>
                                    <tbody>
<?php 
$whereClause = " WHERE tblissuedbookdetails.StudentID NOT LIKE 'TID%'";
if(isset($_GET['status']) && $_GET['status'] == 'not_returned') {
    $whereClause = " WHERE (tblissuedbookdetails.RetrunStatus='' OR tblissuedbookdetails.RetrunStatus IS NULL)";
}
$sql = "SELECT COALESCE(tblstudents.FullName, tblteachers.FullName) as FullName, COALESCE(tblstudents.Department, tblteachers.Department) as Department, tblstudents.Year as Year, tblbooks.BookName, tblbooks.ISBNNumber, tblissuedbookdetails.IssuesDate, tblissuedbookdetails.ReturnDate, tblissuedbookdetails.id as rid, tblissuedbookdetails.StudentID as MemberID FROM tblissuedbookdetails LEFT JOIN tblstudents ON tblstudents.StudentId = tblissuedbookdetails.StudentID LEFT JOIN tblteachers ON tblteachers.TeacherId = tblissuedbookdetails.StudentID JOIN tblbooks ON tblbooks.id = tblissuedbookdetails.BookId" . $whereClause . " ORDER BY tblissuedbookdetails.id DESC";
$query = $dbh -> prepare($sql);
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
$daysSinceIssue = $today->diff($issueDate)->days;
$isOverdue = ($daysSinceIssue >= 7 && $result->ReturnDate == "");
?>
<tr class="odd gradeX<?php echo $isOverdue ? ' danger' : ''; ?>">
                                            <td class="center"><?php echo htmlentities($cnt);?></td>
                                            <td class="center">
                                                <?php echo htmlentities($result->FullName);?>
                                                <br>
                                                <?php if(stripos($result->MemberID, 'TID') === 0): ?>
                                                    <span class="label label-info">Teacher</span>
                                                <?php else: ?>
                                                    <span class="label label-success">Student</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="center"><?php echo htmlentities($result->BookName);?></td>
                                            <td class="center"><?php echo htmlentities($result->ISBNNumber);?></td>
                                            <td class="center"><?php echo htmlentities($result->IssuesDate);?></td>
                                            <td class="center"><?php if($result->ReturnDate=="")
                                            {
                                                echo htmlentities("Not Return Yet");
                                            } else {
                                            echo htmlentities($result->ReturnDate);
}
                                            ?></td>
                                            <td class="center"><?php 
                                                $daysOverdue = max(0, $daysSinceIssue - 7);
                                                if($result->ReturnDate == "" && $daysSinceIssue >= 7) {
                                                    echo '<span class="label label-danger">' . htmlentities($daysOverdue) . ' days</span>';
                                                } else {
                                                    echo htmlentities($daysOverdue);
                                                }
                                            ?></td>
                                            <td class="center">

                                            <?php 
                                            $editPage = (stripos($result->MemberID, 'TID') === 0) ? 'update-issue-bookdetails-teacher.php' : 'update-issue-bookdeails.php';
                                            ?>
                                            <a href="<?php echo $editPage; ?>?rid=<?php echo htmlentities($result->rid);?>"><button class="btn btn-primary"><i class="fa fa-edit "></i> Edit</button>
                                         
                                            </td>
                                            <td style="display:none;"><?php echo htmlentities($result->Department);?></td>
                                            <td style="display:none;"><?php echo htmlentities($result->Year);?></td>
                                        </tr>
 <?php $cnt=$cnt+1;}} ?>
                                </table>
                            </div>
                            
                        </div>
                    </div>
                    <!--End Advanced Tables -->
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
    <script>
        $(document).ready(function() {
            var table = $('#dataTables-example').DataTable();
            
            // Add Year Filter (Create first so we can reference it)
            var yearFilter = $('<select class="form-control input-sm" style="display:inline-block; width:auto; margin-left:10px;"><option value="">All Years</option><option value="I">I</option><option value="II">II</option><option value="III">III</option><option value="IV">IV</option><option value="V">V</option></select>')
                .on('change', function() {
                    var val = $(this).val().replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&');
                    table.column(9).search(val ? '^'+val+'$' : '', true, false).draw();
                });

            // Add Department Filter
            var deptFilter = $('<select class="form-control input-sm" style="display:inline-block; width:auto; margin-left:10px;"><option value="">All Departments</option><option value="MCA">MCA</option><option value="MBA">MBA</option><option value="AI&ML">AI&ML</option><option value="AI&DS">AI&DS</option><option value="CSE">CSE</option><option value="ECE">ECE</option><option value="IT">IT</option></select>')
                .appendTo('.dataTables_length')
                .on('change', function() {
                    var rawVal = $(this).val();
                    var val = rawVal.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&');
                    table.column(8).search(val ? '^'+val+'$' : '', true, false).draw();
                    
                    // Update Year Filter options
                    var selectedYear = yearFilter.val();
                    yearFilter.empty().append('<option value="">All Years</option><option value="I">I</option><option value="II">II</option>');
                    if (rawVal !== 'MCA' && rawVal !== 'MBA') {
                        yearFilter.append('<option value="III">III</option><option value="IV">IV</option><option value="V">V</option>');
                    }
                    if (yearFilter.find('option[value="'+selectedYear+'"]').length > 0) {
                        yearFilter.val(selectedYear);
                    } else {
                        yearFilter.val('');
                        table.column(9).search('', true, false).draw();
                    }
                });

            yearFilter.appendTo('.dataTables_length');
        });
    </script>
</body>
</html>
<?php } ?>
