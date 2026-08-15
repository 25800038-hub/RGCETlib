<?php 
require_once("includes/config.php");
if(!empty($_POST["bookid"])) {
    $bookid = $_POST["bookid"];
    $booknameSearch = "%" . $bookid . "%";
 
    $sql = "SELECT 
                tblbooks.BookName as BookName,
                tblcategory.CategoryName,
                tblauthors.AuthorName,
                tblbooks.ISBNNumber,
                tblbooks.BookPrice,
                tblbooks.id as bookid,
                tblbooks.bookImage,
                tblbooks.isIssued,
                tblbooks.bookQty,
                (SELECT COUNT(*) FROM tblissuedbookdetails WHERE tblissuedbookdetails.BookId = tblbooks.id AND (tblissuedbookdetails.RetrunStatus = '' OR tblissuedbookdetails.RetrunStatus IS NULL OR tblissuedbookdetails.RetrunStatus = '0')) AS activeIssued,
                (SELECT COUNT(*) FROM tblreservations WHERE tblreservations.BookId = tblbooks.id AND tblreservations.Status = 'Reserved') AS activeReserved
            FROM tblbooks
            LEFT JOIN tblauthors ON tblauthors.id = tblbooks.AuthorId
            LEFT JOIN tblcategory ON tblcategory.id = tblbooks.CatId
            WHERE (tblbooks.ISBNNumber = :bookid OR tblbooks.BookName LIKE :bookname) 
            GROUP BY tblbooks.id";

    $query = $dbh->prepare($sql);
    $query->bindParam(':bookid', $bookid, PDO::PARAM_STR);
    $query->bindParam(':bookname', $booknameSearch, PDO::PARAM_STR);
    $query->execute();
    $results = $query->fetchAll(PDO::FETCH_OBJ);

    if($query->rowCount() > 0) {
?>
<table class="table table-bordered">
  <tr>
<?php foreach ($results as $result) {
    $bqty = intval($result->bookQty);
    $activeIssued = intval($result->activeIssued);
    $activeReserved = intval($result->activeReserved);
    $aqty = max(0, $bqty - ($activeIssued + $activeReserved));
?>
    <td style="padding:10px; width: 33%; vertical-align:top;">
        <div class="text-center" style="margin-bottom:10px;">
            <?php if(!empty($result->bookImage)) { ?>
                <img src="bookimg/<?php echo htmlentities($result->bookImage); ?>" width="100" style="border-radius:4px; box-shadow:0 1px 4px rgba(0,0,0,0.2);"><br />
            <?php } ?>
        </div>
        <strong><?php echo htmlentities($result->BookName); ?></strong><br />
        <small class="text-muted">By <?php echo htmlentities($result->AuthorName); ?></small><br />
        <strong>Total Quantity:</strong> <?php echo htmlentities($bqty); ?><br />
        <strong>Currently Issued:</strong> <?php echo htmlentities($activeIssued); ?><br />
        <strong>Reserved:</strong> <?php echo htmlentities($activeReserved); ?><br />
        <strong>Available for Issue:</strong> <span class="label label-<?php echo ($aqty > 0) ? 'success' : 'danger'; ?>"><?php echo htmlentities($aqty); ?></span><br /><br />
        
        <?php if($aqty == 0): ?>
            <p style="color:red; font-weight:bold;"><i class="fa fa-times-circle"></i> Book not available for issue.</p>
        <?php else: ?>
            <label style="cursor:pointer; display:block;">
                <input type="radio" name="bookid" value="<?php echo htmlentities($result->bookid); ?>" required> Select This Book
            </label>
            <input type="hidden" name="aqty" value="<?php echo htmlentities($aqty); ?>">
        <?php endif; ?>
    </td>
    <?php echo "<script>$('#submit').prop('disabled',false);</script>"; ?>
<?php } ?>
  </tr>
</table>

<?php  
    } else { ?>
    <p style="color:red;"><i class="fa fa-warning"></i> Record not found. Please try again.</p>
    <?php
    echo "<script>$('#submit').prop('disabled',true);</script>";
    }
}
?>
