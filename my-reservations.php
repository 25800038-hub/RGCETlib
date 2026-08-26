<?php
session_start();
error_reporting(0);
include('includes/config.php');

if(strlen($_SESSION['login'])==0 && strlen($_SESSION['tlogin'])==0)
    {   
header('location:index.php');
}
else{
$sid=isset($_SESSION['stdid']) ? $_SESSION['stdid'] : $_SESSION['teacherid'];

    // Handle Reservation Cancellation by Student
    if(isset($_GET['action']) && $_GET['action'] == 'cancel' && isset($_GET['resid']))
    {
        $resid = intval($_GET['resid']);
        
        // Verify this reservation belongs to the logged-in student and is currently 'Reserved' or 'Waitlist'
        $checkSql = "SELECT id, BookId, Status FROM tblreservations WHERE id = :resid AND StudentID = :sid";
        $checkQuery = $dbh->prepare($checkSql);
        $checkQuery->bindParam(':resid', $resid, PDO::PARAM_INT);
        $checkQuery->bindParam(':sid', $sid, PDO::PARAM_STR);
        $checkQuery->execute();
        $resObj = $checkQuery->fetch(PDO::FETCH_OBJ);

        if($resObj && ($resObj->Status == 'Reserved' || $resObj->Status == 'Waitlist'))
        {
            $cancelSql = "UPDATE tblreservations SET Status = 'Cancelled' WHERE id = :resid";
            $cancelQuery = $dbh->prepare($cancelSql);
            $cancelQuery->bindParam(':resid', $resid, PDO::PARAM_INT);
            $cancelQuery->execute();

            $_SESSION['msg'] = "Reservation cancelled successfully.";

            // Send Cancellation Email
            try {
                require_once __DIR__ . '/services/MailService.php';
                $mailService = new MailService($dbh);
                
                // Fetch book details
                $bookQuery = $dbh->prepare("SELECT BookName FROM tblbooks WHERE id=:bookid");
                $bookQuery->execute([':bookid' => $resObj->BookId]);
                $book = $bookQuery->fetch(PDO::FETCH_OBJ);
                
                // Fetch student details
                $userQuery = $dbh->prepare("SELECT FullName, EmailId FROM tblstudents WHERE StudentId=:studentid");
                $userQuery->execute([':studentid' => $sid]);
                $user = $userQuery->fetch(PDO::FETCH_OBJ);
                
                if($book && $user && !empty($user->EmailId)) {
                    $subject = "Reservation Cancelled - RGCET Library";
                    $htmlBody = "<p>Hello {$user->FullName},</p>
                                <p>Your reservation for the book has been successfully cancelled.</p>
                                <p><strong>Book:</strong> {$book->BookName}</p>
                                <p>If you cancelled this by mistake, please log in and reserve the book again.</p>
                                <p>Thank you,<br>RGCET Library</p>";
                                
                    $mailService->sendEmail($user->EmailId, $subject, $htmlBody, $resid, 'Reservation');
                }
            } catch (\Exception $e) {
                error_log("Email cancellation failed: " . $e->getMessage());
            }

            // If a reserved book was cancelled, promote the next person on the waitlist
            if($resObj->Status == 'Reserved') {
                include_once('includes/promote_waitlist.php');
                promoteWaitlist($dbh, $resObj->BookId);
            }
        }
        else
        {
            $_SESSION['error'] = "Unable to cancel this reservation.";
        }
        header('location:my-reservations.php');
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
    <title>Online Library Management System | My Reserved Books</title>
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
                    <h4 class="header-line">My Book Reservations</h4>
                    <p class="text-muted">Track the status of your reserved books. Please collect reserved books from the library counter before the pickup deadline.</p>
                </div>
            </div>

            <!-- Alerts -->
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
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <i class="fa fa-bookmark"></i> Reservation History
                            <a href="listed-books.php" class="btn btn-primary btn-xs pull-right"><i class="fa fa-plus"></i> Reserve More Books</a>
                        </div>
                        <div class="panel-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover" id="dataTables-example">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Book Cover</th>
                                            <th>Book Name</th>
                                            <th>ISBN</th>
                                            <th>Reservation Date</th>
                                            <th>Collect Before</th>
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
                                                tblbooks.id as bookid,
                                                tblbooks.BookName,
                                                tblbooks.ISBNNumber,
                                                tblbooks.bookImage,
                                                tblbooks.bookPdf,
                                                tblauthors.AuthorName
                                            FROM tblreservations
                                            JOIN tblbooks ON tblbooks.id = tblreservations.BookId
                                            LEFT JOIN tblauthors ON tblauthors.id = tblbooks.AuthorId
                                            WHERE tblreservations.StudentID = :sid
                                            ORDER BY tblreservations.id DESC";
                                    $query = $dbh->prepare($sql);
                                    $query->bindParam(':sid', $sid, PDO::PARAM_STR);
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
                                            $isExpired = ($result->Status == 'Reserved' && $now > $deadlineDate);
                                    ?>
                                        <tr>
                                            <td class="center"><?php echo htmlentities($cnt);?></td>
                                            <td class="center" style="width: 80px;">
                                                <?php if(!empty($result->bookImage)) { ?>
                                                    <img src="admin/bookimg/<?php echo htmlentities($result->bookImage);?>" width="50" style="border-radius:3px; box-shadow: 0 1px 4px rgba(0,0,0,0.15);" />
                                                <?php } else { ?>
                                                    <i class="fa fa-book fa-2x text-muted"></i>
                                                <?php } ?>
                                            </td>
                                            <td>
                                                <strong><?php echo htmlentities($result->BookName);?></strong>
                                                <br /><small class="text-muted"><i class="fa fa-user"></i> <?php echo htmlentities($result->AuthorName);?></small>
                                            </td>
                                            <td class="center"><?php echo htmlentities($result->ISBNNumber);?></td>
                                            <td class="center"><?php echo htmlentities(date_format($resDate, 'd-M-Y h:i A'));?></td>
                                            <td class="center">
                                                <?php if($result->Status == 'Waitlist') { ?>
                                                    <strong class="text-muted">Pending (TBD)</strong>
                                                <?php } else { ?>
                                                    <strong><?php echo htmlentities(date_format($deadlineDate, 'd-M-Y'));?></strong>
                                                    <?php if($result->Status == 'Reserved') { ?>
                                                        <br /><small class="text-info">(Within 3 days)</small>
                                                    <?php } ?>
                                                <?php } ?>
                                            </td>
                                            <td class="center">
                                                <?php 
                                                if($result->Status == 'Reserved') {
                                                    if($isExpired) {
                                                        echo '<span class="label label-danger">Expired Pickup</span>';
                                                    } else {
                                                        echo '<span class="label label-warning"><i class="fa fa-clock-o"></i> Reserved (Ready for Pickup)</span>';
                                                    }
                                                } elseif($result->Status == 'Waitlist') {
                                                    echo '<span class="label label-info"><i class="fa fa-list-ol"></i> Waitlist (Pending Return)</span>';
                                                } elseif($result->Status == 'Collected') {
                                                    echo '<span class="label label-success"><i class="fa fa-check"></i> Collected & Issued</span>';
                                                } elseif($result->Status == 'Cancelled') {
                                                    echo '<span class="label label-danger"><i class="fa fa-times"></i> Cancelled</span>';
                                                } else {
                                                    echo '<span class="label label-default">' . htmlentities($result->Status) . '</span>';
                                                }
                                                ?>
                                            </td>
                                            <td class="center" style="min-width: 140px;">
                                                <?php if(!empty($result->bookPdf) && file_exists("admin/bookpdf/".$result->bookPdf)) { ?>
                                                    <a href="read-book.php?id=<?php echo htmlentities($result->bookid);?>" class="btn btn-primary btn-xs" style="margin-bottom: 3px;">
                                                        <i class="fa fa-book"></i> Read eBook
                                                    </a>
                                                    <br />
                                                <?php } ?>
                                                <?php if($result->Status == 'Reserved' || $result->Status == 'Waitlist') { ?>
                                                    <a href="my-reservations.php?action=cancel&resid=<?php echo htmlentities($result->resid);?>" 
                                                       onclick="return confirm('Are you sure you want to cancel this reservation?');" 
                                                       class="btn btn-danger btn-xs">
                                                        <i class="fa fa-times"></i> Cancel Reservation
                                                    </a>
                                                <?php } elseif($result->Status == 'Collected') { ?>
                                                    <a href="issued-books.php" class="btn btn-info btn-xs">
                                                        <i class="fa fa-eye"></i> View Issued Books
                                                    </a>
                                                <?php } else { ?>
                                                    <span class="text-muted">None</span>
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
