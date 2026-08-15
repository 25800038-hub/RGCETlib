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
    if(isset($_POST['update']))
    {
        $bookid = intval($_GET['bookid']);
        $bookpdf = $_FILES["bookpdf"]["name"];
        $cpdf = $_POST['currentpdf'];
        $cpath = "bookpdf/" . $cpdf;

        $extension = strtolower(pathinfo($bookpdf, PATHINFO_EXTENSION));

        if($extension != "pdf")
        {
            echo "<script>alert('Invalid format. Only .pdf files are allowed.');</script>";
        }
        else
        {
            $pdfnewname = md5($bookpdf.time().uniqid()).".pdf";
            if(!is_dir("bookpdf")) {
                mkdir("bookpdf", 0777, true);
            }
            move_uploaded_file($_FILES["bookpdf"]["tmp_name"], "bookpdf/".$pdfnewname);

            $sql = "UPDATE tblbooks SET bookPdf = :pdfnewname WHERE id = :bookid";
            $query = $dbh->prepare($sql);
            $query->bindParam(':pdfnewname', $pdfnewname, PDO::PARAM_STR);
            $query->bindParam(':bookid', $bookid, PDO::PARAM_INT);
            $query->execute();

            if(!empty($cpdf) && file_exists($cpath))
            {
                unlink($cpath);
            }

            echo "<script>alert('Book PDF (eBook) updated successfully');</script>";
            echo "<script>window.location.href='manage-books.php'</script>";
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
    <title>Online Library Management System | Update Book PDF</title>
    <!-- BOOTSTRAP CORE STYLE  -->
    <link href="assets/css/bootstrap.css" rel="stylesheet" />
    <!-- FONT AWESOME STYLE  -->
    <link href="assets/css/font-awesome.css" rel="stylesheet" />
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
                    <h4 class="header-line">Update Book eBook (PDF)</h4>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            eBook PDF Info
                        </div>
                        <div class="panel-body">
                            <form role="form" method="post" enctype="multipart/form-data">
                                <?php 
                                $bookid = intval($_GET['bookid']);
                                $sql = "SELECT tblbooks.BookName, tblbooks.id as bookid, tblbooks.bookImage, tblbooks.bookPdf FROM tblbooks WHERE tblbooks.id = :bookid";
                                $query = $dbh->prepare($sql);
                                $query->bindParam(':bookid', $bookid, PDO::PARAM_INT);
                                $query->execute();
                                $results = $query->fetchAll(PDO::FETCH_OBJ);

                                if($query->rowCount() > 0)
                                {
                                    foreach($results as $result)
                                    {               
                                ?>  
                                <input type="hidden" name="currentpdf" value="<?php echo htmlentities($result->bookPdf);?>">
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Book Name</label>
                                        <input class="form-control" type="text" name="bookname" value="<?php echo htmlentities($result->BookName);?>" readonly />
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Current eBook Status</label>
                                        <div>
                                            <?php if(!empty($result->bookPdf) && file_exists("bookpdf/".$result->bookPdf)) { ?>
                                                <span class="label label-success" style="font-size:14px; padding:6px 12px; display:inline-block; margin-bottom:5px;">
                                                    <i class="fa fa-file-pdf-o"></i> PDF Uploaded
                                                </span>
                                                <br />
                                                <a href="bookpdf/<?php echo htmlentities($result->bookPdf);?>" target="_blank" class="btn btn-default btn-xs">
                                                    <i class="fa fa-eye"></i> View Current PDF
                                                </a>
                                            <?php } else { ?>
                                                <span class="label label-warning" style="font-size:14px; padding:6px 12px; display:inline-block;">
                                                    <i class="fa fa-exclamation-triangle"></i> No PDF Uploaded Yet
                                                </span>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-12">  
                                    <div class="form-group">
                                        <label>Upload New Book PDF (.pdf)<span style="color:red;">*</span></label>
                                        <input class="form-control" type="file" name="bookpdf" accept=".pdf" required="required" />
                                        <p class="help-block">Select the complete book PDF file for students to read online.</p>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <button type="submit" name="update" class="btn btn-info"><i class="fa fa-upload"></i> Upload & Save PDF</button>
                                    <a href="manage-books.php" class="btn btn-default">Back to Manage Books</a>
                                </div>
                                <?php 
                                    }
                                } 
                                ?>
                            </form>
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
    <script src="assets/js/custom.js"></script>
</body>
</html>
<?php } ?>
