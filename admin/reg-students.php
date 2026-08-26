<?php
session_start();
error_reporting(0);
include('includes/config.php');
if(strlen($_SESSION['alogin'])==0)
    {   
header('location:index.php');
}
else{ 

// code for block student    
if(isset($_GET['inid']))
{
$id=$_GET['inid'];
$status=0;
$sql = "update tblstudents set Status=:status  WHERE id=:id";
$query = $dbh->prepare($sql);
$query -> bindParam(':id',$id, PDO::PARAM_STR);
$query -> bindParam(':status',$status, PDO::PARAM_STR);
$query -> execute();
header('location:reg-students.php');
}



//code for active students
if(isset($_GET['id']))
{
$id=$_GET['id'];
$status=1;
$sql = "update tblstudents set Status=:status  WHERE id=:id";
$query = $dbh->prepare($sql);
$query -> bindParam(':id',$id, PDO::PARAM_STR);
$query -> bindParam(':status',$status, PDO::PARAM_STR);
$query -> execute();
header('location:reg-students.php');
}


    ?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Online Library Management System | Manage Reg Students</title>
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
                <h4 class="header-line">Manage Reg Students</h4>
    </div>

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
<?php if($_SESSION['updatemsg']!="")
{?>
<div class="col-md-6">
<div class="alert alert-success" >
 <strong>Success :</strong> 
 <?php echo htmlentities($_SESSION['updatemsg']);?>
<?php echo htmlentities($_SESSION['updatemsg']="");?>
</div>
</div>
<?php } ?>
                <div class="col-md-12">
                    <!-- Advanced Tables -->
                    <div class="panel panel-default">
                        <div class="panel-heading">
                          Reg Students
                        </div>
                        <div class="panel-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover" id="dataTables-example">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Student ID</th>
                                            <th>Student Name</th>
                                            <th>Email id </th>
                                            <th>Mobile Number</th>
                                            <th>Department</th>
                                            <th>Year</th>
                                            <th>Reg Date</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
<?php $sql = "SELECT * from tblstudents";
$query = $dbh -> prepare($sql);
$query->execute();
$results=$query->fetchAll(PDO::FETCH_OBJ);
$cnt=1;
if($query->rowCount() > 0)
{
foreach($results as $result)
{               ?>                                      
                                        <tr class="odd gradeX">
                                            <td class="center"><?php echo htmlentities($cnt);?></td>
                                            <td class="center">
                                                <strong><?php echo htmlentities($result->StudentId);?></strong>
                                                <br /><small class="text-muted"><i class="fa fa-phone"></i> <?php echo htmlentities($result->MobileNumber);?></small>
                                            </td>
                                            <td class="center"><?php echo htmlentities($result->FullName);?>
                                            <br><span class="label label-success">Student</span>
                                            </td>
                                            <td class="center"><?php echo htmlentities($result->EmailId);?></td>
                                            <td class="center"><?php echo htmlentities($result->MobileNumber);?></td>
                                            <td class="center"><?php echo htmlentities($result->Department);?></td>
                                            <td class="center"><?php echo htmlentities($result->Year);?></td>
                                             <td class="center"><?php echo htmlentities($result->RegDate);?></td>
                                            <td class="center"><?php if($result->Status==1)
                                            {
                                                echo htmlentities("Active");
                                            } else {


                                            echo htmlentities("Blocked");
}
                                            ?></td>
                                            <td class="center">
<?php if($result->Status==1)
 {?>
<a href="reg-students.php?inid=<?php echo htmlentities($result->id);?>" onclick="return confirm('Are you sure you want to block this student?');" >  <button class="btn btn-danger"> Inactive</button>
<?php } else {?>

<a href="reg-students.php?id=<?php echo htmlentities($result->id);?>" onclick="return confirm('Are you sure you want to active this student?');"><button class="btn btn-primary"> Active</button> 
                                            <?php } ?>

<a href="student-history.php?stdid=<?php echo htmlentities($result->StudentId);?>"><button class="btn btn-success"> Details</button></a>

<a href="edit-student-profile.php?stdid=<?php echo htmlentities($result->StudentId);?>"><button class="btn btn-primary"> Update Profile</button></a>
                                          
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
                    table.column(6).search(val ? '^'+val+'$' : '', true, false).draw();
                });

            // Add Department Filter
            var deptFilter = $('<select class="form-control input-sm" style="display:inline-block; width:auto; margin-left:10px;"><option value="">All Departments</option><option value="MCA">MCA</option><option value="MBA">MBA</option><option value="AI&ML">AI&ML</option><option value="AI&DS">AI&DS</option><option value="CSE">CSE</option><option value="ECE">ECE</option><option value="IT">IT</option></select>')
                .appendTo('.dataTables_length')
                .on('change', function() {
                    var rawVal = $(this).val();
                    var val = rawVal.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&');
                    table.column(5).search(val ? '^'+val+'$' : '', true, false).draw();
                    
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
                        table.column(6).search('', true, false).draw();
                    }
                });

            yearFilter.appendTo('.dataTables_length');
        });
    </script>
</body>
</html>
<?php } ?>
