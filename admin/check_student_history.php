<?php
session_start();
error_reporting(0);
include('includes/config.php');

if(isset($_POST['studentid'])) {
    $studentid = $_POST['studentid'];
    
    $remark = "";
    
    // Check for unreturned books
    $sqlUnreturned = "SELECT COUNT(*) as count FROM tblissuedbookdetails 
                      WHERE StudentId = :studentid 
                      AND (RetrunStatus = '' OR RetrunStatus IS NULL)";
    $queryUnreturned = $dbh->prepare($sqlUnreturned);
    $queryUnreturned->bindParam(':studentid', $studentid, PDO::PARAM_STR);
    $queryUnreturned->execute();
    $resultUnreturned = $queryUnreturned->fetch(PDO::FETCH_OBJ);
    $unreturnedCount = $resultUnreturned->count;
    
    if($unreturnedCount > 0) {
        $remark = "⚠️ ALERT: Student still has " . $unreturnedCount . " unreturned book(s) from previous borrowing. Please follow up before issuing new book.";
    } else {
        // Check for overdue history
        $sqlOverdueHistory = "SELECT COUNT(*) as count FROM tblissuedbookdetails 
                             WHERE StudentId = :studentid 
                             AND RetrunStatus != '' 
                             AND RetrunStatus IS NOT NULL
                             AND (IssuesDate <= DATE_SUB(ReturnDate, INTERVAL -7 DAY))";
        $queryOverdueHistory = $dbh->prepare($sqlOverdueHistory);
        $queryOverdueHistory->bindParam(':studentid', $studentid, PDO::PARAM_STR);
        $queryOverdueHistory->execute();
        $resultOverdueHistory = $queryOverdueHistory->fetch(PDO::FETCH_OBJ);
        $overdueHistoryCount = $resultOverdueHistory->count;
        
        if($overdueHistoryCount > 0) {
            $remark = "⚠️ WARNING: Student has history of returning books late (" . $overdueHistoryCount . " times). Monitor this issue.";
        } else {
            $remark = "✓ Good standing: Student has clean borrowing history.";
        }
    }
    
    echo htmlentities($remark);
}
?>
