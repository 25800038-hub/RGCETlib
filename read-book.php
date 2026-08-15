<?php
session_start();
error_reporting(0);
include('includes/config.php');

if (strlen($_SESSION['login']) == 0) {
    header('location:index.php');
    exit();
} else {
    $bookid = intval($_GET['id']);

    // Fetch Book Details
    $sql = "SELECT 
                tblbooks.id as bookid,
                tblbooks.BookName,
                tblbooks.ISBNNumber,
                tblbooks.bookImage,
                tblbooks.bookPdf,
                tblcategory.CategoryName,
                tblauthors.AuthorName
            FROM tblbooks
            LEFT JOIN tblauthors ON tblauthors.id = tblbooks.AuthorId
            LEFT JOIN tblcategory ON tblcategory.id = tblbooks.CatId
            WHERE tblbooks.id = :bookid";
    $query = $dbh->prepare($sql);
    $query->bindParam(':bookid', $bookid, PDO::PARAM_INT);
    $query->execute();
    $book = $query->fetch(PDO::FETCH_OBJ);

    $hasPdf = ($book && !empty($book->bookPdf) && file_exists("admin/bookpdf/" . $book->bookPdf));
    ?>
    <!DOCTYPE html>
    <html xmlns="http://www.w3.org/1999/xhtml">

    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
        <meta name="description" content="Online Book Reader" />
        <meta name="author" content="" />
        <title>Online Library | Read Book - <?php echo htmlentities($book->BookName ?? 'Book Reader'); ?></title>
        <!-- BOOTSTRAP CORE STYLE  -->
        <link href="assets/css/bootstrap.css" rel="stylesheet" />
        <!-- FONT AWESOME STYLE  -->
        <link href="assets/css/font-awesome.css" rel="stylesheet" />
        <!-- CUSTOM STYLE  -->
        <link href="assets/css/style.css" rel="stylesheet" />
        <!-- GOOGLE FONT -->
        <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css' />
        <style>
            .reader-header {
                background: #2c3e50;
                color: #fff;
                padding: 15px 20px;
                border-radius: 6px 6px 0 0;
                margin-bottom: 0;
                display: flex;
                align-items: center;
                justify-content: space-between;
                flex-wrap: wrap;
                gap: 10px;
            }

            .reader-title {
                font-size: 18px;
                font-weight: 700;
                margin: 0;
                color: #fff;
            }

            .reader-author {
                font-size: 13px;
                color: #bdc3c7;
                margin-top: 3px;
            }

            .reader-container {
                background: #34495e;
                padding: 10px;
                border-radius: 0 0 6px 6px;
                box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
            }

            .pdf-frame {
                width: 100%;
                height: 85vh;
                min-height: 650px;
                border: none;
                border-radius: 4px;
                background: #fff;
            }

            .no-pdf-box {
                background: #fff;
                padding: 60px 20px;
                text-align: center;
                border-radius: 8px;
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
                margin: 40px auto;
                max-width: 600px;
            }
        </style>
    </head>

    <body>
        <!-- MENU SECTION START -->
        <?php include('includes/header.php'); ?>
        <!-- MENU SECTION END -->

        <div class="content-wrapper" style="padding-top: 15px;">
            <div class="container-fluid" style="padding: 0 25px;">
                <?php if ($book && $hasPdf) { ?>
                    <!-- Reader Top Bar -->
                    <div class="reader-header">
                        <div>
                            <a href="listed-books.php" class="btn btn-default btn-sm" style="margin-right: 10px;">
                                <i class="fa fa-arrow-left"></i> Back to Catalog
                            </a>
                            <span class="reader-title"><i class="fa fa-book"></i>
                                <?php echo htmlentities($book->BookName); ?></span>
                            <div class="reader-author">
                                By <strong><?php echo htmlentities($book->AuthorName); ?></strong> |
                                Category: <span class="label label-info"><?php echo htmlentities($book->CategoryName); ?></span>
                                |
                                ISBN: <span><?php echo htmlentities($book->ISBNNumber); ?></span>
                            </div>
                        </div>
                        <div>
                            <a href="admin/bookpdf/<?php echo htmlentities($book->bookPdf); ?>" target="_blank"
                                class="btn btn-info btn-sm">
                                <i class="fa fa-external-link"></i> Open in New Tab
                            </a>
                            <button onclick="toggleFullScreen()" class="btn btn-warning btn-sm" id="fullscreenBtn">
                                <i class="fa fa-arrows-alt"></i> Full Screen
                            </button>
                        </div>
                    </div>

                    <!-- PDF Viewer Frame -->
                    <div class="reader-container" id="readerContainer">
                        <iframe id="pdfViewer" class="pdf-frame"
                            src="admin/bookpdf/<?php echo htmlentities($book->bookPdf); ?>#toolbar=1&navpanes=1"
                            allowfullscreen>
                        </iframe>
                    </div>
                <?php } else { ?>
                    <!-- No PDF Available Box -->
                    <div class="no-pdf-box">
                        <i class="fa fa-file-pdf-o fa-5x text-danger" style="margin-bottom: 20px;"></i>
                        <h3>eBook (PDF) Not Available Yet</h3>
                        <p class="text-muted" style="font-size: 15px; margin-bottom: 25px;">
                            The full PDF copy for <strong><?php echo htmlentities($book->BookName ?? 'this book'); ?></strong>
                            has not been uploaded by the library administrator. You can still browse the library catalog to
                            reserve a physical copy.
                        </p>
                        <a href="listed-books.php" class="btn btn-primary btn-lg">
                            <i class="fa fa-book"></i> Browse Library Catalog
                        </a>
                    </div>
                <?php } ?>
            </div>
        </div>

        <!-- FOOTER SECTION START -->
        <?php include('includes/footer.php'); ?>
        <!-- FOOTER SECTION END -->

        <!-- JAVASCRIPT FILES -->
        <script src="assets/js/jquery-1.10.2.js"></script>
        <script src="assets/js/bootstrap.js"></script>
        <script src="assets/js/custom.js"></script>
        <script>
            function toggleFullScreen() {
                var elem = document.getElementById("readerContainer");
                if (!document.fullscreenElement) {
                    if (elem.requestFullscreen) {
                        elem.requestFullscreen();
                    } else if (elem.webkitRequestFullscreen) {
                        elem.webkitRequestFullscreen();
                    } else if (elem.msRequestFullscreen) {
                        elem.msRequestFullscreen();
                    }
                    document.getElementById("fullscreenBtn").innerHTML = '<i class="fa fa-compress"></i> Exit Full Screen';
                } else {
                    if (document.exitFullscreen) {
                        document.exitFullscreen();
                    }
                    document.getElementById("fullscreenBtn").innerHTML = '<i class="fa fa-arrows-alt"></i> Full Screen';
                }
            }

            document.addEventListener('fullscreenchange', function () {
                if (!document.fullscreenElement) {
                    document.getElementById("fullscreenBtn").innerHTML = '<i class="fa fa-arrows-alt"></i> Full Screen';
                }
            });
        </script>
    </body>

    </html>
<?php } ?>