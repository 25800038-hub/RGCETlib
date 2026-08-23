<?php
session_start();
error_reporting(0);
include('includes/config.php');

if (strlen($_SESSION['tlogin']) == 0) {
    header('location:teacherlogin.php');
} else {
    $sid = $_SESSION['teacherid'];
    $overdueBooks = array();
    $overdueCount = 0;

    $sqlOverdue = "SELECT
                        tblissuedbookdetails.id,
                        tblbooks.BookName,
                        tblissuedbookdetails.IssuesDate
                    FROM tblissuedbookdetails
                    JOIN tblbooks ON tblbooks.id = tblissuedbookdetails.BookId
                    WHERE tblissuedbookdetails.StudentID = :sid
                    AND (tblissuedbookdetails.RetrunStatus = '' OR tblissuedbookdetails.RetrunStatus IS NULL)
                    ORDER BY tblissuedbookdetails.IssuesDate ASC";

    $queryOverdue = $dbh->prepare($sqlOverdue);
    $queryOverdue->bindParam(':sid', $sid, PDO::PARAM_STR);
    $queryOverdue->execute();
    $resultsOverdue = $queryOverdue->fetchAll(PDO::FETCH_OBJ);

    if (count($resultsOverdue) > 0) {
        foreach ($resultsOverdue as $row) {
            $issueDateRaw = $row->IssuesDate;

            if (!empty($issueDateRaw)) {
                $issueDate = DateTime::createFromFormat('Y-m-d H:i:s', $issueDateRaw);

                if (!$issueDate) {
                    $issueDate = DateTime::createFromFormat('Y-m-d', $issueDateRaw);
                }

                if ($issueDate) {
                    $today = new DateTime('today');
                    $interval = $issueDate->diff($today);
                    $daysSinceIssue = (int) $interval->format('%a');

                    if ($daysSinceIssue >= 7) {
                        $overdueBooks[] = $row;
                        $overdueCount++;
                    }
                }
            }
        }
    }
    if ($overdueCount > 0 && !isset($_SESSION['tch_overdue_alert_shown'])) {
        $overdueMessage = 'Overdue Alert: ' . $overdueCount . ' issued book(s) are overdue for 7 days or more.';
        echo '<script>alert(' . json_encode($overdueMessage) . ');</script>';
        $_SESSION['tch_overdue_alert_shown'] = true;
    }
    ?>
    <!DOCTYPE html>
    <html xmlns="http://www.w3.org/1999/xhtml">

    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>Online Library Management System | User Dash Board</title>
        <link href="assets/css/bootstrap.css" rel="stylesheet" />
        <link href="assets/css/font-awesome.css" rel="stylesheet" />
        <link href="assets/css/style.css?v=1.5" rel="stylesheet" />
        <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css' />
    </head>

    <body>
        <?php include('includes/header.php'); ?>

        <div class="content-wrapper">
            <div class="container">
                <div class="row pad-botm">
                    <div class="col-md-12">
                        <center>
                            <h4 class="header-line">TEACHER DASHBOARD</h4>
                        </center>
                    </div>
                </div>

                <?php if (isset($_SESSION['FullName'])) { ?>
                    <div class="row">
                        <div class="col-md-12">
                            <div
                                style="background: linear-gradient(to right, #4b3bb3, #8a4bcf); padding: 30px; border-radius: 12px; color: white; margin-bottom: 25px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
                                <h2 style="margin-top: 0; font-weight: 700;">Welcome Back,
                                    <?php echo htmlentities($_SESSION['FullName']); ?></h2>
                                <p style="margin-bottom: 0; font-size: 15px; opacity: 0.9;">Manage your books, track student
                                    activities, and oversee reservations.</p>
                            </div>
                        </div>
                    </div>
                <?php } ?>

                <div class="row">

                    <?php if ($overdueCount > 0) { ?>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="alert alert-warning">
                                    <strong>Overdue Alert:</strong> <?php echo htmlentities($overdueCount); ?> issued book(s)
                                    have
                                    been overdue for 7 days or more.
                                    <ul class="margin-top-10">
                                        <?php foreach ($overdueBooks as $overdue) { ?>
                                            <li>
                                                <strong>Book:</strong> <?php echo htmlentities($overdue->BookName); ?>
                                                (Issued on <?php echo htmlentities($overdue->IssuesDate); ?>)
                                            </li>
                                        <?php } ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    <?php } ?>

                    <a href="listed-books.php" style="text-decoration: none; outline: none;">
                        <div class="col-md-4 col-sm-4 col-xs-6">
                            <div class="dashboard-card dashboard-card-success text-center">
                                <i class="fa fa-book fa-5x"></i>
                                <?php
                                $sql = "SELECT id from tblbooks";
                                $query = $dbh->prepare($sql);
                                $query->execute();
                                $results = $query->fetchAll(PDO::FETCH_OBJ);
                                $listdbooks = count($results);
                                ?>
                                <h3><?php echo htmlentities($listdbooks); ?></h3>
                                <div class="card-label">Books Catalog / Reserve</div>
                            </div>
                        </div>
                    </a>

                    <a href="my-reservations.php" style="text-decoration: none; outline: none;">
                        <div class="col-md-4 col-sm-4 col-xs-6">
                            <div class="dashboard-card dashboard-card-info text-center">
                                <i class="fa fa-bookmark fa-5x"></i>
                                <?php
                                $resSql = "SELECT id from tblreservations where StudentID = :sid and Status = 'Reserved'";
                                $resQuery = $dbh->prepare($resSql);
                                $resQuery->bindParam(':sid', $sid, PDO::PARAM_STR);
                                $resQuery->execute();
                                $myReservations = $resQuery->rowCount();
                                ?>
                                <h3><?php echo htmlentities($myReservations); ?></h3>
                                <div class="card-label">My Reserved Books</div>
                            </div>
                        </div>
                    </a>

                    <a href="issued-books.php?status=not_returned" style="text-decoration: none; outline: none;">
                        <div class="col-md-4 col-sm-4 col-xs-6">
                            <div class="dashboard-card dashboard-card-warning text-center">
                                <i class="fa fa-recycle fa-5x"></i>
                                <?php
                                $rsts = 0;
                                $sid = $_SESSION['teacherid'];
                                $sql2 = "SELECT id from tblissuedbookdetails 
                            where StudentID=:sid 
                            and (RetrunStatus=:rsts || RetrunStatus is null || RetrunStatus='')";
                                $query2 = $dbh->prepare($sql2);
                                $query2->bindParam(':sid', $sid, PDO::PARAM_STR);
                                $query2->bindParam(':rsts', $rsts, PDO::PARAM_STR);
                                $query2->execute();
                                $results2 = $query2->fetchAll(PDO::FETCH_OBJ);
                                $returnedbooks = count($results2);
                                ?>
                                <h3><?php echo htmlentities($returnedbooks); ?></h3>
                                <div class="card-label">Books Not Returned Yet</div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="row">
                    <a href="issued-books.php" style="text-decoration: none; outline: none;">
                        <div class="col-md-6 col-sm-6 col-xs-6">
                            <div class="dashboard-card dashboard-card-success text-center">
                                <i class="fa fa-check-square-o fa-5x"></i>
                                <?php
                                $ret = $dbh->prepare("SELECT id from tblissuedbookdetails where StudentID=:sid");
                                $ret->bindParam(':sid', $sid, PDO::PARAM_STR);
                                $ret->execute();
                                $results22 = $ret->fetchAll(PDO::FETCH_OBJ);
                                $totalissuedbook = count($results22);
                                ?>
                                <h3><?php echo htmlentities($totalissuedbook); ?></h3>
                                <div class="card-label">Total Issued Books</div>
                            </div>
                        </div>
                    </a>

                    <a href="overdue-details.php">
                        <div class="col-md-6 col-sm-6 col-xs-6">
                            <div class="dashboard-card dashboard-card-danger text-center">
                                <i class="fa fa-exclamation-triangle fa-5x"></i>
                                <h3><?php echo htmlentities($overdueCount); ?></h3>
                                <div class="card-label">Over Due Books</div>
                            </div>
                        </div>
                    </a>
                </div>

            </div>
        </div>

        <?php include('includes/footer.php'); ?>

        <script src="assets/js/jquery-1.10.2.js"></script>
        <script src="assets/js/bootstrap.js"></script>
        <script src="assets/js/custom.js"></script>
    </body>

    </html>
<?php } ?>