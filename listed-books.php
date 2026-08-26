<?php
session_start();
error_reporting(0);
include('includes/config.php');

if(strlen($_SESSION['login'])==0 && strlen($_SESSION['tlogin'])==0)
{   
    header('location:index.php');
    exit();
}
else
{ 
    $sid = isset($_SESSION['stdid']) ? $_SESSION['stdid'] : $_SESSION['teacherid'];

    // Handle Book Reservation Request
    if(isset($_GET['action']) && $_GET['action'] == 'reserve' && isset($_GET['bookid']))
    {
        $bookid = intval($_GET['bookid']);

        // Check if student already has a pending reservation or waitlist for this book
        $chkResSql = "SELECT id FROM tblreservations WHERE StudentID = :sid AND BookId = :bookid AND Status IN ('Reserved', 'Waitlist')";
        $chkResQuery = $dbh->prepare($chkResSql);
        $chkResQuery->bindParam(':sid', $sid, PDO::PARAM_STR);
        $chkResQuery->bindParam(':bookid', $bookid, PDO::PARAM_INT);
        $chkResQuery->execute();

        if($chkResQuery->rowCount() > 0)
        {
            $_SESSION['error'] = "You have already reserved or waitlisted this book. Please check My Reservations.";
            header('location:listed-books.php');
            exit();
        }

        // Check if student currently has this book issued and not returned
        $chkIssSql = "SELECT id FROM tblissuedbookdetails WHERE StudentID = :sid AND BookId = :bookid AND (RetrunStatus = '' OR RetrunStatus IS NULL OR RetrunStatus = '0')";
        $chkIssQuery = $dbh->prepare($chkIssSql);
        $chkIssQuery->bindParam(':sid', $sid, PDO::PARAM_STR);
        $chkIssQuery->bindParam(':bookid', $bookid, PDO::PARAM_INT);
        $chkIssQuery->execute();

        if($chkIssQuery->rowCount() > 0)
        {
            $_SESSION['error'] = "You already have an active borrowed copy of this book.";
            header('location:listed-books.php');
            exit();
        }

        // Check current borrowing/reservation limits
        $isTeacher = isset($_SESSION['teacherid']) && !empty($_SESSION['teacherid']) ? true : false;
        $maxLimit = $isTeacher ? 5 : 3;

        $limitSql = "SELECT 
                        (SELECT COUNT(*) FROM tblissuedbookdetails WHERE StudentID = :sid AND (RetrunStatus = '' OR RetrunStatus IS NULL OR RetrunStatus = '0')) AS currentIssued,
                        (SELECT COUNT(*) FROM tblreservations WHERE StudentID = :sid2 AND Status IN ('Reserved', 'Waitlist')) AS currentReserved";
        $limitQuery = $dbh->prepare($limitSql);
        $limitQuery->bindParam(':sid', $sid, PDO::PARAM_STR);
        $limitQuery->bindParam(':sid2', $sid, PDO::PARAM_STR);
        $limitQuery->execute();
        $limitRow = $limitQuery->fetch(PDO::FETCH_OBJ);
        
        $totalHoldings = intval($limitRow->currentIssued) + intval($limitRow->currentReserved);
        if($totalHoldings >= $maxLimit) {
            $roleStr = $isTeacher ? "Faculty" : "Students";
            $_SESSION['error'] = "$roleStr can only borrow/reserve up to $maxLimit books at a time. You currently have $totalHoldings active (issued or reserved).";
            header('location:listed-books.php');
            exit();
        }

        // Calculate available stock
        $stockSql = "SELECT 
                        tblbooks.bookQty,
                        (SELECT COUNT(*) FROM tblissuedbookdetails WHERE tblissuedbookdetails.BookId = tblbooks.id AND (tblissuedbookdetails.RetrunStatus = '' OR tblissuedbookdetails.RetrunStatus IS NULL OR tblissuedbookdetails.RetrunStatus = '0')) AS activeIssued,
                        (SELECT COUNT(*) FROM tblreservations WHERE tblreservations.BookId = tblbooks.id AND tblreservations.Status = 'Reserved') AS activeReserved
                     FROM tblbooks WHERE tblbooks.id = :bookid";
        $stockQuery = $dbh->prepare($stockSql);
        $stockQuery->bindParam(':bookid', $bookid, PDO::PARAM_INT);
        $stockQuery->execute();
        $stockRow = $stockQuery->fetch(PDO::FETCH_OBJ);

        if($stockRow)
        {
            $available = intval($stockRow->bookQty) - (intval($stockRow->activeIssued) + intval($stockRow->activeReserved));
            if($available > 0)
            {
                $resInsert = "INSERT INTO tblreservations(BookId, StudentID, Status) VALUES(:bookid, :sid, 'Reserved')";
                $insertQuery = $dbh->prepare($resInsert);
                $insertQuery->bindParam(':bookid', $bookid, PDO::PARAM_INT);
                $insertQuery->bindParam(':sid', $sid, PDO::PARAM_STR);
                $insertQuery->execute();
                $resId = $dbh->lastInsertId();

                try {
                    require_once __DIR__ . '/services/MailService.php';
                    $mailService = new MailService($dbh);
                    $infoSql = "SELECT b.BookName, COALESCE(s.FullName, t.FullName) as FullName, COALESCE(s.EmailId, t.EmailId) as EmailId FROM tblbooks b LEFT JOIN tblstudents s ON s.StudentId = :sid LEFT JOIN tblteachers t ON t.TeacherId = :sid2 WHERE b.id = :bid";
                    $infoQuery = $dbh->prepare($infoSql);
                    $infoQuery->execute([':sid' => $sid, ':sid2' => $sid, ':bid' => $bookid]);
                    $info = $infoQuery->fetch(PDO::FETCH_OBJ);

                    if($info && !empty($info->EmailId)) {
                        $subject = "Book Reserved - Action Required";
                        $htmlBody = "<p>Hello {$info->FullName},</p>
                                     <p>Your reservation for <strong>{$info->BookName}</strong> was successful.</p>
                                     <p>A copy is currently available. <strong>Please collect it from the library counter within 3 days</strong>, otherwise your reservation will be cancelled.</p>
                                     <p>Thank you,<br>RGCET Library</p>";
                        $mailService->sendEmail($info->EmailId, $subject, $htmlBody, $resId, 'Reservation');
                    }
                } catch (\Exception $e) {}

                $_SESSION['msg'] = "Book reserved successfully! Please collect it from the library counter within 3 days.";
                header('location:my-reservations.php');
                exit();
            }
            else
            {
                $resInsert = "INSERT INTO tblreservations(BookId, StudentID, Status) VALUES(:bookid, :sid, 'Waitlist')";
                $insertQuery = $dbh->prepare($resInsert);
                $insertQuery->bindParam(':bookid', $bookid, PDO::PARAM_INT);
                $insertQuery->bindParam(':sid', $sid, PDO::PARAM_STR);
                $insertQuery->execute();
                $resId = $dbh->lastInsertId();

                try {
                    require_once __DIR__ . '/services/MailService.php';
                    $mailService = new MailService($dbh);
                    $infoSql = "SELECT b.BookName, COALESCE(s.FullName, t.FullName) as FullName, COALESCE(s.EmailId, t.EmailId) as EmailId FROM tblbooks b LEFT JOIN tblstudents s ON s.StudentId = :sid LEFT JOIN tblteachers t ON t.TeacherId = :sid2 WHERE b.id = :bid";
                    $infoQuery = $dbh->prepare($infoSql);
                    $infoQuery->execute([':sid' => $sid, ':sid2' => $sid, ':bid' => $bookid]);
                    $info = $infoQuery->fetch(PDO::FETCH_OBJ);

                    if($info && !empty($info->EmailId)) {
                        $subject = "Added to Waitlist";
                        $htmlBody = "<p>Hello {$info->FullName},</p>
                                     <p>You have been successfully added to the waitlist for <strong>{$info->BookName}</strong>.</p>
                                     <p>We will automatically notify you by email as soon as a copy becomes available.</p>
                                     <p>Thank you,<br>RGCET Library</p>";
                        $mailService->sendEmail($info->EmailId, $subject, $htmlBody, $resId, 'Waitlist');
                    }
                } catch (\Exception $e) {}

                $_SESSION['msg'] = "You have been added to the waitlist. We will reserve it for you when a copy becomes available.";
                header('location:my-reservations.php');
                exit();
            }
        }
        else
        {
            $_SESSION['error'] = "Invalid book selection.";
            header('location:listed-books.php');
            exit();
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
    <title>Online Library Management System | Books Catalog & Reservation</title>
    <!-- BOOTSTRAP CORE STYLE  -->
    <link href="assets/css/bootstrap.css" rel="stylesheet" />
    <!-- FONT AWESOME STYLE  -->
    <link href="assets/css/font-awesome.css" rel="stylesheet" />
    <!-- CUSTOM STYLE  -->
    <link href="assets/css/style.css" rel="stylesheet" />
    <!-- GOOGLE FONT -->
    <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css' />
    <style>
        .book-card {
            border: 1px solid #e3e3e3;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 30px;
            background: #fff;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            min-height: 480px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .book-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 6px 16px rgba(0,0,0,0.12);
        }
        .book-img-container {
            text-align: center;
            height: 180px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 12px;
            background: #fdfdfd;
            border-radius: 4px;
        }
        .book-img-container img {
            max-height: 170px;
            max-width: 100%;
            object-fit: contain;
            border-radius: 4px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.15);
        }
        .book-title {
            font-size: 16px;
            font-weight: 700;
            color: #333;
            margin-bottom: 6px;
            line-height: 1.3;
            min-height: 42px;
        }
        .book-meta {
            font-size: 13px;
            color: #666;
            margin-bottom: 4px;
        }
        .book-category {
            display: inline-block;
            background: #eef2f7;
            color: #31708f;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 8px;
        }
        .stock-badge {
            font-size: 12px;
            font-weight: 600;
            padding: 4px 8px;
            border-radius: 4px;
        }
        .stock-available {
            background-color: #dff0d8;
            color: #3c763d;
            border: 1px solid #d6e9c6;
        }
        .stock-empty {
            background-color: #f2dede;
            color: #a94442;
            border: 1px solid #ebccd1;
        }
    </style>
</head>
<body>
    <!-- MENU SECTION START -->
    <?php include('includes/header.php');?>
    <!-- MENU SECTION END -->

    <div class="content-wrapper">
        <div class="container">
            <div class="row pad-botm">
                <div class="col-md-12">
                    <h4 class="header-line">Books Catalog & Reservation</h4>
                    <p class="text-muted">Browse library books and reserve your copy online. Collect your reserved book at the library counter within 3 days.</p>
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

            <!-- Search and Filter Bar -->
            <div class="row" style="margin-bottom: 20px;">
                <div class="col-md-6 col-sm-8">
                    <div class="input-group">
                        <input type="text" id="bookSearchInput" onkeyup="filterBooks()" class="form-control input-lg" placeholder="Search by Book Name, Author, or ISBN..." />
                        <span class="input-group-addon"><i class="fa fa-search"></i></span>
                    </div>
                </div>
                <div class="col-md-6 col-sm-4 text-right">
                    <a href="my-reservations.php" class="btn btn-info btn-lg">
                        <i class="fa fa-bookmark"></i> My Reservations
                    </a>
                </div>
            </div>

            <!-- Books Grid -->
            <div class="row" id="booksContainer">
                <?php 
                $sql = "SELECT 
                            tblbooks.id as bookid,
                            tblbooks.BookName,
                            tblbooks.ISBNNumber,
                            tblbooks.BookPrice,
                            tblbooks.bookImage,
                            tblbooks.bookPdf,
                            tblbooks.bookQty,
                            tblcategory.CategoryName,
                            tblauthors.AuthorName,
                            (SELECT COUNT(*) FROM tblissuedbookdetails WHERE tblissuedbookdetails.BookId = tblbooks.id AND (tblissuedbookdetails.RetrunStatus = '' OR tblissuedbookdetails.RetrunStatus IS NULL OR tblissuedbookdetails.RetrunStatus = '0')) AS activeIssued,
                            (SELECT COUNT(*) FROM tblreservations WHERE tblreservations.BookId = tblbooks.id AND tblreservations.Status = 'Reserved') AS activeReserved,
                            (SELECT COUNT(*) FROM tblreservations WHERE tblreservations.BookId = tblbooks.id AND tblreservations.StudentID = :sid AND tblreservations.Status = 'Reserved') AS userHasReserved,
                            (SELECT COUNT(*) FROM tblissuedbookdetails WHERE tblissuedbookdetails.BookId = tblbooks.id AND tblissuedbookdetails.StudentID = :sid AND (tblissuedbookdetails.RetrunStatus = '' OR tblissuedbookdetails.RetrunStatus IS NULL OR tblissuedbookdetails.RetrunStatus = '0')) AS userHasIssued
                        FROM tblbooks
                        LEFT JOIN tblauthors ON tblauthors.id = tblbooks.AuthorId
                        LEFT JOIN tblcategory ON tblcategory.id = tblbooks.CatId
                        ORDER BY tblbooks.id DESC";
                
                $query = $dbh->prepare($sql);
                $query->bindParam(':sid', $sid, PDO::PARAM_STR);
                $query->execute();
                $results = $query->fetchAll(PDO::FETCH_OBJ);

                if($query->rowCount() > 0)
                {
                    foreach($results as $result)
                    {
                        $totalQty = intval($result->bookQty);
                        $issuedCount = intval($result->activeIssued);
                        $reservedCount = intval($result->activeReserved);
                        $availableQty = max(0, $totalQty - ($issuedCount + $reservedCount));
                        $isReservedByMe = (intval($result->userHasReserved) > 0);
                        $isIssuedToMe = (intval($result->userHasIssued) > 0);
                        $hasPdf = (!empty($result->bookPdf) && file_exists("admin/bookpdf/" . $result->bookPdf));
                ?>  
                <div class="col-md-4 col-sm-6 book-item">
                    <div class="book-card">
                        <div>
                            <div class="book-img-container">
                                <?php if($hasPdf) { ?>
                                    <a href="read-book.php?id=<?php echo htmlentities($result->bookid);?>" title="Click to Read Book Online">
                                        <?php if(!empty($result->bookImage)) { ?>
                                            <img src="admin/bookimg/<?php echo htmlentities($result->bookImage);?>" alt="<?php echo htmlentities($result->BookName);?>">
                                        <?php } else { ?>
                                            <i class="fa fa-book fa-5x text-muted"></i>
                                        <?php } ?>
                                    </a>
                                <?php } else { ?>
                                    <?php if(!empty($result->bookImage)) { ?>
                                        <img src="admin/bookimg/<?php echo htmlentities($result->bookImage);?>" alt="<?php echo htmlentities($result->BookName);?>">
                                    <?php } else { ?>
                                        <i class="fa fa-book fa-5x text-muted"></i>
                                    <?php } ?>
                                <?php } ?>
                            </div>
                            
                            <div>
                                <span class="book-category"><?php echo htmlentities($result->CategoryName);?></span>
                                <?php if($hasPdf) { ?>
                                    <span class="label label-danger pull-right" style="margin-top:2px;"><i class="fa fa-file-pdf-o"></i> eBook</span>
                                <?php } ?>
                            </div>

                            <div class="book-title" title="<?php echo htmlentities($result->BookName);?>">
                                <?php if($hasPdf) { ?>
                                    <a href="read-book.php?id=<?php echo htmlentities($result->bookid);?>" style="color:inherit; text-decoration:none;">
                                        <?php echo htmlentities($result->BookName);?>
                                    </a>
                                <?php } else { ?>
                                    <?php echo htmlentities($result->BookName);?>
                                <?php } ?>
                            </div>
                            
                            <div class="book-meta">
                                <strong><i class="fa fa-user"></i> Author:</strong> <?php echo htmlentities($result->AuthorName);?>
                            </div>
                            <div class="book-meta">
                                <strong><i class="fa fa-barcode"></i> ISBN:</strong> <?php echo htmlentities($result->ISBNNumber);?>
                            </div>
                            <div class="book-meta">
                                <strong><i class="fa fa-cubes"></i> Total Copies:</strong> <?php echo htmlentities($totalQty);?>
                            </div>
                            <div class="book-meta" style="margin-top: 6px;">
                                <?php if($availableQty > 0) { ?>
                                    <span class="stock-badge stock-available"><i class="fa fa-check-circle"></i> Available: <?php echo htmlentities($availableQty);?> left</span>
                                <?php } else { ?>
                                    <span class="stock-badge stock-empty"><i class="fa fa-times-circle"></i> Currently Unavailable</span>
                                <?php } ?>
                                <?php if($reservedCount > 0) { ?>
                                    <small class="text-muted" style="margin-left: 5px;">(<?php echo htmlentities($reservedCount);?> reserved)</small>
                                <?php } ?>
                            </div>
                        </div>

                        <div style="margin-top: 15px; border-top: 1px solid #eee; padding-top: 12px;">
                            <?php if($hasPdf) { ?>
                                <a href="read-book.php?id=<?php echo htmlentities($result->bookid);?>" class="btn btn-primary btn-block" style="margin-bottom: 8px;">
                                    <i class="fa fa-book"></i> Read Full Book Online (PDF)
                                </a>
                            <?php } else { ?>
                                <button class="btn btn-default btn-block disabled" style="margin-bottom: 8px;" disabled>
                                    <i class="fa fa-file-pdf-o text-muted"></i> No Online eBook
                                </button>
                            <?php } ?>

                            <?php if($isReservedByMe) { ?>
                                <a href="my-reservations.php" class="btn btn-info btn-block">
                                    <i class="fa fa-bookmark"></i> Already Reserved (View Status)
                                </a>
                            <?php } elseif($isIssuedToMe) { ?>
                                <button class="btn btn-warning btn-block disabled" disabled>
                                    <i class="fa fa-check"></i> Currently Borrowed by You
                                </button>
                            <?php } elseif($availableQty > 0) { ?>
                                <a href="listed-books.php?action=reserve&bookid=<?php echo htmlentities($result->bookid);?>" 
                                   onclick="return confirm('Confirm Reservation:\n\nBook: <?php echo addslashes(htmlentities($result->BookName));?>\n\nYou can collect this book at the library counter within 3 days. Proceed?');" 
                                   class="btn btn-success btn-block">
                                    <i class="fa fa-bookmark"></i> Reserve Physical Copy
                                </a>
                            <?php } else { ?>
                                <a href="listed-books.php?action=reserve&bookid=<?php echo htmlentities($result->bookid);?>" 
                                   onclick="return confirm('Join Waitlist:\n\nBook: <?php echo addslashes(htmlentities($result->BookName));?>\n\nThis book is out of stock. You will be added to the waitlist and it will be reserved for you automatically when a copy is returned. Proceed?');" 
                                   class="btn btn-default btn-block" style="color: #333; border-color: #ccc; background-color: #f9f9f9;">
                                    <i class="fa fa-clock-o"></i> Out of Stock - Join Waitlist
                                </a>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                <?php 
                    }
                } 

                else 
                { 
                ?>
                <div class="col-md-12 text-center" style="padding: 40px;">
                    <i class="fa fa-book fa-4x text-muted"></i>
                    <p class="lead text-muted" style="margin-top: 10px;">No books are currently listed in the library catalog.</p>
                </div>
                <?php } ?>
            </div>
        </div>
    </div>

    <!-- FOOTER SECTION START -->
    <?php include('includes/footer.php');?>
    <!-- FOOTER SECTION END -->

    <!-- JAVASCRIPT FILES -->
    <script src="assets/js/jquery-1.10.2.js"></script>
    <script src="assets/js/bootstrap.js"></script>
    <script src="assets/js/custom.js"></script>
    <script>
        function filterBooks() {
            var input = document.getElementById('bookSearchInput');
            var filter = input.value.toLowerCase();
            var items = document.getElementsByClassName('book-item');

            for (var i = 0; i < items.length; i++) {
                var text = items[i].innerText.toLowerCase();
                if (text.indexOf(filter) > -1) {
                    items[i].style.display = "";
                } else {
                    items[i].style.display = "none";
                }
            }
        }
    </script>
</body>
</html>
<?php } ?>
