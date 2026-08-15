<?php
session_start();
error_reporting(0);
include('includes/config.php');

if (strlen($_SESSION['alogin']) == 0) {
    header('location:index.php');
} else {
    $overdueBooks = array();
    $overdueCount = 0;

    $sqlOverdue = "SELECT 
                    tblissuedbookdetails.id,
                    tblstudents.FullName,
                    tblbooks.BookName,
                    tblissuedbookdetails.IssuesDate
                FROM tblissuedbookdetails
                JOIN tblstudents ON tblstudents.StudentId = tblissuedbookdetails.StudentId
                JOIN tblbooks ON tblbooks.id = tblissuedbookdetails.BookId
                WHERE (tblissuedbookdetails.RetrunStatus = '' OR tblissuedbookdetails.RetrunStatus IS NULL)
                ORDER BY tblissuedbookdetails.IssuesDate ASC";

    $queryOverdue = $dbh->prepare($sqlOverdue);
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

    if ($overdueCount > 0) {
        $overdueMessage = 'Overdue Alert: ' . $overdueCount . ' issued book(s) are overdue for 7 days or more.';
        echo '<script>alert(' . json_encode($overdueMessage) . ');</script>';
    }
    ?>
    <!DOCTYPE html>
    <html xmlns="http://www.w3.org/1999/xhtml">

    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <!--[if IE]>
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <![endif]-->
        <title>Online Library Management System | Admin Dash Board</title>
        <link href="assets/css/bootstrap.css" rel="stylesheet" />
        <link href="assets/css/font-awesome.css" rel="stylesheet" />
        <link href="assets/css/style.css" rel="stylesheet" />
        <link href="http://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet" type="text/css" />
    </head>

    <body>
        <?php include('includes/header.php'); ?>

        <div class="content-wrapper">
            <div class="container">
                <div class="row pad-botm">
                    <div class="col-md-12">
                        <h4 class="header-line" align="center">ADMIN DASHBOARD</h4>
                    </div>
                </div>

                <?php if ($overdueCount > 0) { ?>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="alert alert-warning">
                                <strong>Overdue Alert:</strong> <?php echo htmlentities($overdueCount); ?> issued book(s) have
                                been overdue for 7 days or more.
                                <ul class="margin-top-10">
                                    <?php foreach ($overdueBooks as $overdue) { ?>
                                        <li>
                                            <strong><?php echo htmlentities($overdue->FullName); ?></strong> -
                                            <?php echo htmlentities($overdue->BookName); ?>
                                            (Issued on <?php echo htmlentities($overdue->IssuesDate); ?>)
                                        </li>
                                    <?php } ?>
                                </ul>
                            </div>
                        </div>
                    </div>

                <?php } ?>

                <div class="row">
                    <a href="manage-books.php">
                        <div class="col-md-3 col-sm-3 col-xs-6">
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
                                <div class="card-label">Books Listed</div>
                            </div>
                        </div>
                    </a>

                    <a href="manage-reservations.php">
                        <div class="col-md-3 col-sm-3 col-xs-6">
                            <div class="dashboard-card dashboard-card-info text-center">
                                <i class="fa fa-bookmark fa-5x"></i>
                                <?php
                                $sqlRes = "SELECT id from tblreservations where Status = 'Reserved'";
                                $queryRes = $dbh->prepare($sqlRes);
                                $queryRes->execute();
                                $totalReservations = $queryRes->rowCount();
                                ?>
                                <h3><?php echo htmlentities($totalReservations); ?></h3>
                                <div class="card-label">Pending Reservations</div>
                            </div>
                        </div>
                    </a>

                    <a href="manage-issued-books.php">
                        <div class="col-md-3 col-sm-3 col-xs-6">
                            <div class="dashboard-card dashboard-card-warning text-center">
                                <i class="fa fa-recycle fa-5x"></i>
                                <?php
                                $sql2 = "SELECT id from tblissuedbookdetails where (RetrunStatus='' || RetrunStatus is null)";
                                $query2 = $dbh->prepare($sql2);
                                $query2->execute();
                                $results2 = $query2->fetchAll(PDO::FETCH_OBJ);
                                $returnedbooks = count($results2);
                                ?>
                                <h3><?php echo htmlentities($returnedbooks); ?></h3>
                                <div class="card-label">Books Not Returned Yet</div>
                            </div>
                        </div>
                    </a>

                    <a href="overdue-details.php">
                        <div class="col-md-3 col-sm-3 col-xs-6">
                            <div class="dashboard-card dashboard-card-danger text-center">
                                <i class="fa fa-exclamation-triangle fa-5x"></i>
                                <h3><?php echo htmlentities($overdueCount); ?></h3>
                                <div class="card-label">Over Due</div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="row">
                    <a href="reg-students.php">
                        <div class="col-md-4 col-sm-4 col-xs-6">
                            <div class="dashboard-card dashboard-card-success text-center">
                                <i class="fa fa-users fa-5x"></i>
                                <?php
                                $sql3 = "SELECT id from tblstudents";
                                $query3 = $dbh->prepare($sql3);
                                $query3->execute();
                                $results3 = $query3->fetchAll(PDO::FETCH_OBJ);
                                $regstds = count($results3);
                                ?>
                                <h3><?php echo htmlentities($regstds); ?></h3>
                                <div class="card-label">Registered Users</div>
                            </div>
                        </div>
                    </a>

                    <a href="manage-authors.php">
                        <div class="col-md-4 col-sm-4 col-xs-6">
                            <div class="dashboard-card dashboard-card-info text-center">
                                <i class="fa fa-user fa-5x"></i>
                                <?php
                                $sq4 = "SELECT id from tblauthors";
                                $query4 = $dbh->prepare($sq4);
                                $query4->execute();
                                $results4 = $query4->fetchAll(PDO::FETCH_OBJ);
                                $listdathrs = count($results4);
                                ?>
                                <h3><?php echo htmlentities($listdathrs); ?></h3>
                                <div class="card-label">Authors Listed</div>
                            </div>
                        </div>
                    </a>

                    <a href="manage-categories.php">
                        <div class="col-md-4 col-sm-4 col-xs-6">
                            <div class="dashboard-card dashboard-card-warning text-center">
                                <i class="fa fa-file-archive-o fa-5x"></i>
                                <?php
                                $sql5 = "SELECT id from tblcategory";
                                $query5 = $dbh->prepare($sql5);
                                $query5->execute();
                                $results5 = $query5->fetchAll(PDO::FETCH_OBJ);
                                $listdcats = count($results5);
                                ?>
                                <h3><?php echo htmlentities($listdcats); ?></h3>
                                <div class="card-label">Listed Categories</div>
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