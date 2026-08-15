<?php
session_start();
error_reporting(0);
include('includes/config.php');

if(strlen($_SESSION['alogin'])==0)
{   
    header('location:index.php');
    exit();
}
else
{ 
    // Handle Issue Book / Mark as Collected
    if(isset($_GET['action']) && $_GET['action'] == 'issue' && isset($_GET['resid']))
    {
        $resid = intval($_GET['resid']);

        // Fetch reservation details
        $resSql = "SELECT tblreservations.*, tblbooks.BookName, tblstudents.FullName 
                   FROM tblreservations 
                   JOIN tblbooks ON tblbooks.id = tblreservations.BookId
                   JOIN tblstudents ON tblstudents.StudentId = tblreservations.StudentID
                   WHERE tblreservations.id = :resid";
        $resQuery = $dbh->prepare($resSql);
        $resQuery->bindParam(':resid', $resid, PDO::PARAM_INT);
        $resQuery->execute();
        $res = $resQuery->fetch(PDO::FETCH_OBJ);

        if($res && $res->Status == 'Reserved')
        {
            $sid = $res->StudentID;
            $bid = $res->BookId;
            $remark = "Collected from Online Reservation #" . $resid;

            // Insert into tblissuedbookdetails
            $issueSql = "INSERT INTO tblissuedbookdetails(StudentID, BookId, remark) VALUES(:sid, :bid, :remark)";
            $issueQuery = $dbh->prepare($issueSql);
            $issueQuery->bindParam(':sid', $sid, PDO::PARAM_STR);
            $issueQuery->bindParam(':bid', $bid, PDO::PARAM_INT);
            $issueQuery->bindParam(':remark', $remark, PDO::PARAM_STR);
            $issueQuery->execute();
            $lastInsertId = $dbh->lastInsertId();

            if($lastInsertId)
            {
                // Update reservation status to Collected
                $updateRes = "UPDATE tblreservations SET Status = 'Collected', AdminRemark = :remark WHERE id = :resid";
                $updateQuery = $dbh->prepare($updateRes);
                $updateQuery->bindParam(':remark', $remark, PDO::PARAM_STR);
                $updateQuery->bindParam(':resid', $resid, PDO::PARAM_INT);
                $updateQuery->execute();

                $_SESSION['msg'] = "Book successfully issued to " . $res->FullName . " (Reservation #" . $resid . " fulfilled)!";
            }
            else
            {
                $_SESSION['error'] = "Failed to issue the book. Please try again.";
            }
        }
        else
        {
            $_SESSION['error'] = "Invalid reservation or already fulfilled/cancelled.";
        }
        header('location:manage-reservations.php');
        exit();
    }

    // Handle Cancel / Expire Reservation
    if(isset($_GET['action']) && $_GET['action'] == 'cancel' && isset($_GET['resid']))
    {
        $resid = intval($_GET['resid']);
        $cancelSql = "UPDATE tblreservations SET Status = 'Cancelled', AdminRemark = 'Cancelled by Admin' WHERE id = :resid AND Status = 'Reserved'";
        $cancelQuery = $dbh->prepare($cancelSql);
        $cancelQuery->bindParam(':resid', $resid, PDO::PARAM_INT);
        $cancelQuery->execute();

        $_SESSION['msg'] = "Reservation #" . $resid . " has been cancelled.";
        header('location:manage-reservations.php');
        exit();
    }
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Online Library Management System | Manage Book Reservations</title>
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
    <!-- MENU SECTION START -->
    <?php include('includes/header.php');?>
    <!-- MENU SECTION END -->

    <div class="content-wrapper">
        <div class="container">
            <div class="row pad-botm">
                <div class="col-md-12">
                    <h4 class="header-line">Manage Book Reservations</h4>
                    <p class="text-muted">Review student reservation requests. When a student arrives at the library counter to collect their reserved book, click <strong>"Issue / Collect"</strong> to immediately issue the book.</p>
                </div>
            </div>

            <!-- Notification Messages -->
            <div class="row">
                <?php if($_SESSION['error']!="") { ?>
                <div class="col-md-12">
                    <div class="alert alert-danger alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <strong><i class="fa fa-exclamation-circle"></i> Error:</strong> <?php echo htmlentities($_SESSION['error']);?>
                        <?php echo htmlentities($_SESSION['error']="");?>
                    </div>
                </div>
                <?php } ?>

                <?php if($_SESSION['msg']!="") { ?>
                <div class="col-md-12">
                    <div class="alert alert-success alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <strong><i class="fa fa-check-circle"></i> Success:</strong> <?php echo htmlentities($_SESSION['msg']);?>
                        <?php echo htmlentities($_SESSION['msg']="");?>
                    </div>
                </div>
                <?php } ?>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <!-- Advanced Tables -->
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <i class="fa fa-bookmark"></i> All Book Reservations
                        </div>
                        <div class="panel-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover" id="dataTables-example">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Student ID</th>
                                            <th>Student Name</th>
                                            <th>Book Name</th>
                                            <th>ISBN</th>
                                            <th>Reserved On</th>
                                            <th>Pickup Due</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php 
                                    $sql = "SELECT 
                                                tblreservations.id as resid,
                                                tblreservations.ReservationDate,
                                                tblreservations.Status,
                                                tblreservations.AdminRemark,
                                                tblstudents.StudentId,
                                                tblstudents.FullName,
                                                tblstudents.MobileNumber,
                                                tblbooks.BookName,
                                                tblbooks.ISBNNumber,
                                                tblbooks.bookImage
                                            FROM tblreservations
                                            JOIN tblstudents ON tblstudents.StudentId = tblreservations.StudentID
                                            JOIN tblbooks ON tblbooks.id = tblreservations.BookId
                                            ORDER BY tblreservations.id DESC";
                                    $query = $dbh->prepare($sql);
                                    $query->execute();
                                    $results = $query->fetchAll(PDO::FETCH_OBJ);
                                    $cnt = 1;

                                    if($query->rowCount() > 0)
                                    {
                                        foreach($results as $result)
                                        {
                                            $resDate = new DateTime($result->ReservationDate);
                                            $deadlineDate = clone $resDate;
                                            $deadlineDate->modify('+3 days');
                                            $now = new DateTime();
                                            $isOverduePickup = ($result->Status == 'Reserved' && $now > $deadlineDate);
                                    ?>
                                        <tr class="odd gradeX<?php echo $isOverduePickup ? ' warning' : ''; ?>">
                                            <td class="center"><?php echo htmlentities($cnt);?></td>
                                            <td class="center">
                                                <strong><?php echo htmlentities($result->StudentId);?></strong>
                                                <br /><small class="text-muted"><i class="fa fa-phone"></i> <?php echo htmlentities($result->MobileNumber);?></small>
                                            </td>
                                            <td><?php echo htmlentities($result->FullName);?></td>
                                            <td>
                                                <strong><?php echo htmlentities($result->BookName);?></strong>
                                            </td>
                                            <td class="center"><?php echo htmlentities($result->ISBNNumber);?></td>
                                            <td class="center"><?php echo htmlentities(date_format($resDate, 'd-M-Y h:i A'));?></td>
                                            <td class="center">
                                                <strong><?php echo htmlentities(date_format($deadlineDate, 'd-M-Y'));?></strong>
                                                <?php if($isOverduePickup) { ?>
                                                    <br /><span class="label label-danger">Pickup Overdue</span>
                                                <?php } ?>
                                            </td>
                                            <td class="center">
                                                <?php 
                                                if($result->Status == 'Reserved') {
                                                    echo '<span class="label label-warning"><i class="fa fa-clock-o"></i> Reserved (Pending Pickup)</span>';
                                                } elseif($result->Status == 'Collected') {
                                                    echo '<span class="label label-success"><i class="fa fa-check"></i> Collected & Issued</span>';
                                                } elseif($result->Status == 'Cancelled') {
                                                    echo '<span class="label label-danger"><i class="fa fa-times"></i> Cancelled</span>';
                                                } else {
                                                    echo '<span class="label label-default">' . htmlentities($result->Status) . '</span>';
                                                }
                                                ?>
                                            </td>
                                            <td class="center" style="min-width: 170px;">
                                                <?php if($result->Status == 'Reserved') { ?>
                                                    <a href="manage-reservations.php?action=issue&resid=<?php echo htmlentities($result->resid);?>" 
                                                       onclick="return confirm('Confirm Collection:\n\nIssue book to student <?php echo addslashes(htmlentities($result->FullName));?>?');" 
                                                       class="btn btn-success btn-xs" style="margin-bottom: 2px;">
                                                        <i class="fa fa-check"></i> Issue / Collect
                                                    </a>
                                                    <a href="manage-reservations.php?action=cancel&resid=<?php echo htmlentities($result->resid);?>" 
                                                       onclick="return confirm('Are you sure you want to cancel this reservation?');" 
                                                       class="btn btn-danger btn-xs" style="margin-bottom: 2px;">
                                                        <i class="fa fa-times"></i> Cancel
                                                    </a>
                                                <?php } elseif($result->Status == 'Collected') { ?>
                                                    <a href="manage-issued-books.php" class="btn btn-info btn-xs">
                                                        <i class="fa fa-list"></i> View in Issued Books
                                                    </a>
                                                <?php } else { ?>
                                                    <span class="text-muted"><i class="fa fa-ban"></i> No action</span>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                    <?php 
                                            $cnt++;
                                        }
                                    } 
                                    ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <!-- End Advanced Tables -->
                </div>
            </div>
        </div>
    </div>

    <!-- FOOTER SECTION START -->
    <?php include('includes/footer.php');?>
    <!-- FOOTER SECTION END -->

    <!-- JAVASCRIPT FILES -->
    <script src="assets/js/jquery-1.10.2.js"></script>
    <script src="assets/js/bootstrap.js"></script>
    <script src="assets/js/dataTables/jquery.dataTables.js"></script>
    <script src="assets/js/dataTables/dataTables.bootstrap.js"></script>
    <script src="assets/js/custom.js"></script>
    <script>
        $(document).ready(function () {
            $('#dataTables-example').dataTable();
        });
    </script>
</body>
</html>
<?php } ?>
