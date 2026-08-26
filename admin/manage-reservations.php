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
        $resSql = "SELECT tblreservations.*, tblbooks.BookName, COALESCE(tblstudents.FullName, tblteachers.FullName) as FullName 
                   FROM tblreservations 
                   JOIN tblbooks ON tblbooks.id = tblreservations.BookId
                   LEFT JOIN tblstudents ON tblstudents.StudentId = tblreservations.StudentID
                   LEFT JOIN tblteachers ON tblteachers.TeacherId = tblreservations.StudentID
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

                // Send Issue Email
                try {
                    require_once __DIR__ . '/../services/MailService.php';
                    $mailService = new MailService($dbh);
                    
                    // Fetch book details
                    $bookQuery = $dbh->prepare("SELECT BookName FROM tblbooks WHERE id=:bookid");
                    $bookQuery->execute([':bookid' => $bid]);
                    $book = $bookQuery->fetch(PDO::FETCH_OBJ);
                    
                    // Fetch student/teacher details
                    $userQuery = $dbh->prepare("SELECT FullName, EmailId FROM tblstudents WHERE StudentId=:studentid UNION SELECT FullName, EmailId FROM tblteachers WHERE TeacherId=:studentid");
                    $userQuery->execute([':studentid' => $sid]);
                    $user = $userQuery->fetch(PDO::FETCH_OBJ);
                    
                    if($book && $user && !empty($user->EmailId)) {
                        $issueDate = date('Y-m-d');
                        $dueDate = date('Y-m-d', strtotime('+7 days'));
                        
                        $subject = "Book Issued Successfully - RGCET Library";
                        $htmlBody = "<p>Hello {$user->FullName},</p>
                                    <p>Your reserved book has been issued successfully.</p>
                                    <p><strong>Book:</strong> {$book->BookName}<br>
                                    <strong>Issue Date:</strong> {$issueDate}<br>
                                    <strong>Due Date:</strong> {$dueDate}</p>
                                    <p>Please return the book on or before the due date to avoid any applicable fine.</p>
                                    <p>Thank you,<br>RGCET Library</p>";
                                    
                        $mailService->sendEmail($user->EmailId, $subject, $htmlBody, $lastInsertId, 'Issue');
                    }
                } catch (\Exception $e) {
                    error_log("Email issue failed: " . $e->getMessage());
                }

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
        
        // Fetch bookid to promote waitlist
        $resSql = "SELECT BookId, Status FROM tblreservations WHERE id = :resid";
        $resQuery = $dbh->prepare($resSql);
        $resQuery->bindParam(':resid', $resid, PDO::PARAM_INT);
        $resQuery->execute();
        $resObj = $resQuery->fetch(PDO::FETCH_OBJ);

        $cancelSql = "UPDATE tblreservations SET Status = 'Cancelled', AdminRemark = 'Cancelled by Admin' WHERE id = :resid AND Status IN ('Reserved', 'Waitlist')";
        $cancelQuery = $dbh->prepare($cancelSql);
        $cancelQuery->bindParam(':resid', $resid, PDO::PARAM_INT);
        $cancelQuery->execute();

        $_SESSION['msg'] = "Reservation #" . $resid . " has been cancelled.";
        
        // Send Cancellation Email
        try {
            require_once __DIR__ . '/../services/MailService.php';
            $mailService = new MailService($dbh);
            
            // Fetch reservation details including user
            $resDetailsSql = "SELECT r.BookId, b.BookName, r.StudentID,
                              COALESCE(s.FullName, t.FullName) as FullName,
                              COALESCE(s.EmailId, t.EmailId) as EmailId
                              FROM tblreservations r
                              JOIN tblbooks b ON b.id = r.BookId
                              LEFT JOIN tblstudents s ON s.StudentId = r.StudentID
                              LEFT JOIN tblteachers t ON t.TeacherId = r.StudentID
                              WHERE r.id = :resid";
            $detQuery = $dbh->prepare($resDetailsSql);
            $detQuery->execute([':resid' => $resid]);
            $det = $detQuery->fetch(PDO::FETCH_OBJ);
            
            if($det && !empty($det->EmailId)) {
                $subject = "Reservation Cancelled - RGCET Library";
                $htmlBody = "<p>Hello {$det->FullName},</p>
                            <p>Your reservation for the book has been cancelled by the Library Administrator.</p>
                            <p><strong>Book:</strong> {$det->BookName}</p>
                            <p>Please contact the library counter if you have any questions.</p>
                            <p>Thank you,<br>RGCET Library</p>";
                            
                $mailService->sendEmail($det->EmailId, $subject, $htmlBody, $resid, 'Reservation');
            }
        } catch (\Exception $e) {
            error_log("Email cancellation failed: " . $e->getMessage());
        }
        
        if($resObj && $resObj->Status == 'Reserved') {
            include_once('../includes/promote_waitlist.php');
            promoteWaitlist($dbh, $resObj->BookId);
        }

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
                    <p class="text-muted">Review member reservation requests. When a member arrives at the library counter to collect their reserved book, click <strong>"Issue / Collect"</strong> to immediately issue the book.</p>
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
                                            <th>Member ID</th>
                                            <th>Member Name</th>
                                            <th>Book Name</th>
                                            <th>ISBN</th>
                                            <th>Reserved On</th>
                                            <th>Pickup Due</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                            <th style="display:none;">Member Type</th>
                                            <th style="display:none;">Department</th>
                                            <th style="display:none;">Year</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php 
                                    $sql = "SELECT 
                                                tblreservations.id as resid,
                                                tblreservations.ReservationDate,
                                                tblreservations.Status,
                                                tblreservations.AdminRemark,
                                                tblreservations.StudentID as MemberID,
                                                COALESCE(tblstudents.FullName, tblteachers.FullName) as FullName,
                                                COALESCE(tblstudents.MobileNumber, tblteachers.MobileNumber) as MobileNumber,
                                                COALESCE(tblstudents.Department, tblteachers.Department) as Department,
                                                tblstudents.Year,
                                                tblbooks.BookName,
                                                tblbooks.ISBNNumber,
                                                tblbooks.bookImage
                                            FROM tblreservations
                                            LEFT JOIN tblstudents ON tblstudents.StudentId = tblreservations.StudentID
                                            LEFT JOIN tblteachers ON tblteachers.TeacherId = tblreservations.StudentID
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
                                                <strong><?php echo htmlentities($result->MemberID);?></strong>
                                                <br /><small class="text-muted"><i class="fa fa-phone"></i> <?php echo htmlentities($result->MobileNumber);?></small>
                                            </td>
                                            <td>
                                                <?php echo htmlentities($result->FullName);?>
                                                <br>
                                                <?php if(stripos($result->MemberID, 'TID') === 0): ?>
                                                    <span class="label label-info">Teacher</span>
                                                <?php else: ?>
                                                    <span class="label label-success">Student</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <strong><?php echo htmlentities($result->BookName);?></strong>
                                            </td>
                                            <td class="center"><?php echo htmlentities($result->ISBNNumber);?></td>
                                            <td class="center"><?php echo htmlentities(date_format($resDate, 'd-M-Y h:i A'));?></td>
                                            <td class="center">
                                                <?php if($result->Status == 'Waitlist') { ?>
                                                    <strong class="text-muted">Pending (TBD)</strong>
                                                <?php } else { ?>
                                                    <strong><?php echo htmlentities(date_format($deadlineDate, 'd-M-Y'));?></strong>
                                                    <?php if($isOverduePickup) { ?>
                                                        <br /><span class="label label-danger">Pickup Overdue</span>
                                                    <?php } ?>
                                                <?php } ?>
                                            </td>
                                            <td class="center">
                                                <?php 
                                                if($result->Status == 'Reserved') {
                                                    if($isOverduePickup) {
                                                        echo '<span class="label label-danger">Expired Pickup</span>';
                                                    } else {
                                                        echo '<span class="label label-warning">Reserved</span>';
                                                    }
                                                } elseif($result->Status == 'Waitlist') {
                                                    echo '<span class="label label-info">Waitlist</span>';
                                                } elseif($result->Status == 'Collected') {
                                                    echo '<span class="label label-success">Issued</span>';
                                                } elseif($result->Status == 'Cancelled') {
                                                    echo '<span class="label label-danger">Cancelled</span>';
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
                                            <td style="display:none;"><?php echo stripos($result->MemberID, 'TID') === 0 ? 'Teacher' : 'Student'; ?></td>
                                            <td style="display:none;"><?php echo htmlentities($result->Department);?></td>
                                            <td style="display:none;"><?php echo htmlentities($result->Year);?></td>
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
        $(document).ready(function() {
            var table = $('#dataTables-example').DataTable();
            
            // Add Member Type Filter
            var memberFilter = $('<select class="form-control input-sm" style="display:inline-block; width:auto; margin-left:10px;"><option value="">All Members</option><option value="Student">Student</option><option value="Teacher">Teacher</option></select>')
                .appendTo('.dataTables_length')
                .on('change', function() {
                    var rawVal = $(this).val();
                    var val = rawVal.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&');
                    table.column(9).search(val ? '^'+val+'$' : '', true, false).draw();
                    
                    if (rawVal === 'Teacher') {
                        yearFilter.hide();
                    } else {
                        yearFilter.show();
                    }
                });

            // Add Year Filter (Create first so we can reference it)
            var yearFilter = $('<select class="form-control input-sm" style="display:inline-block; width:auto; margin-left:10px;"><option value="">All Years</option><option value="I">I</option><option value="II">II</option><option value="III">III</option><option value="IV">IV</option><option value="V">V</option></select>')
                .on('change', function() {
                    var val = $(this).val().replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&');
                    table.column(11).search(val ? '^'+val+'$' : '', true, false).draw();
                });

            // Add Department Filter
            var deptFilter = $('<select class="form-control input-sm" style="display:inline-block; width:auto; margin-left:10px;"><option value="">All Departments</option><option value="MCA">MCA</option><option value="MBA">MBA</option><option value="AI&ML">AI&ML</option><option value="AI&DS">AI&DS</option><option value="CSE">CSE</option><option value="ECE">ECE</option><option value="IT">IT</option></select>')
                .appendTo('.dataTables_length')
                .on('change', function() {
                    var rawVal = $(this).val();
                    var val = rawVal.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&');
                    table.column(10).search(val ? '^'+val+'$' : '', true, false).draw();
                    
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
                        table.column(11).search('', true, false).draw();
                    }
                });

            yearFilter.appendTo('.dataTables_length');
        });
    </script>
</body>
</html>
<?php } ?>
