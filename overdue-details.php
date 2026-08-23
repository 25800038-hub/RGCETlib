<?php
session_start();
error_reporting(0);
include('includes/config.php');

if(strlen($_SESSION['login'])==0 && strlen($_SESSION['tlogin'])==0)
{
    header('location:index.php');
}
else
{
    // Get the current user ID from session
    $studentId = isset($_SESSION['stdid']) ? $_SESSION['stdid'] : $_SESSION['teacherid'];
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Online Library Management System | My Overdue Books</title>
    <link href="assets/css/bootstrap.css" rel="stylesheet" />
    <link href="assets/css/font-awesome.css" rel="stylesheet" />
    <link href="assets/css/style.css" rel="stylesheet" />
    <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css' />
</head>
<body>
<?php include('includes/header.php');?>

<div class="content-wrapper">
    <div class="container">
        <div class="row pad-botm">
            <div class="col-md-12">
                <h4 class="header-line">My Overdue Books</h4>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        Your Overdue Books Details
                    </div>
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Book Name</th>
                                        <th>Author</th>
                                        <th>ISBN</th>
                                        <th>Category</th>
                                        <th>Issued Date</th>
                                        <th>Days Overdue</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                $sqlOverdue = "SELECT 
                                                tblissuedbookdetails.id, 
                                                tblbooks.BookName, 
                                                tblauthors.AuthorName,
                                                tblbooks.ISBNNumber,
                                                tblcategory.CategoryName,
                                                tblissuedbookdetails.IssuesDate
                                            FROM tblissuedbookdetails
                                            JOIN tblbooks ON tblbooks.id = tblissuedbookdetails.BookId
                                            JOIN tblauthors ON tblauthors.id = tblbooks.AuthorId
                                            JOIN tblcategory ON tblcategory.id = tblbooks.CatId
                                            WHERE tblissuedbookdetails.StudentID = :studentId
                                            AND (tblissuedbookdetails.RetrunStatus = '' OR tblissuedbookdetails.RetrunStatus IS NULL)
                                            AND tblissuedbookdetails.IssuesDate <= DATE_SUB(NOW(), INTERVAL 7 DAY)
                                            ORDER BY tblissuedbookdetails.IssuesDate ASC";

                                $queryOverdue = $dbh->prepare($sqlOverdue);
                                $queryOverdue->bindParam(':studentId', $studentId, PDO::PARAM_STR);
                                $queryOverdue->execute();
                                $resultsOverdue = $queryOverdue->fetchAll(PDO::FETCH_OBJ);

                                if(count($resultsOverdue) > 0)
                                {
                                    $cnt = 1;
                                    foreach($resultsOverdue as $overdue)
                                    {
                                        // Calculate days overdue
                                        $issueDate = new DateTime($overdue->IssuesDate);
                                        $today = new DateTime('today');
                                        $daysOverdue = $today->diff($issueDate)->days - 7; // 7 day grace period
                                ?>
                                    <tr>
                                        <td><?php echo htmlentities($cnt); ?></td>
                                        <td><?php echo htmlentities($overdue->BookName); ?></td>
                                        <td><?php echo htmlentities($overdue->AuthorName); ?></td>
                                        <td><?php echo htmlentities($overdue->ISBNNumber); ?></td>
                                        <td><?php echo htmlentities($overdue->CategoryName); ?></td>
                                        <td><?php echo htmlentities($overdue->IssuesDate); ?></td>
                                        <td><span class="label label-danger"><?php echo htmlentities($daysOverdue); ?> days</span></td>
                                    </tr>
                                <?php
                                        $cnt++;
                                    }
                                }
                                else
                                {
                                ?>
                                    <tr>
                                        <td colspan="7" class="text-center"><strong>No overdue books found. Great job!</strong></td>
                                    </tr>
                                <?php
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

<?php include('includes/footer.php');?>

<script src="assets/js/jquery-1.10.2.js"></script>
<script src="assets/js/bootstrap.js"></script>
<script src="assets/js/custom.js"></script>
</body>
</html>
<?php } ?>
