<?php
function promoteWaitlist($dbh, $bookId) {
    // Check if anyone is on the waitlist for this book, order by oldest first
    $waitSql = "SELECT id, StudentID FROM tblreservations WHERE BookId = :bid AND Status = 'Waitlist' ORDER BY ReservationDate ASC LIMIT 1";
    $waitQuery = $dbh->prepare($waitSql);
    $waitQuery->bindParam(':bid', $bookId, PDO::PARAM_INT);
    $waitQuery->execute();
    
    if($waitQuery->rowCount() > 0) {
        $waitUser = $waitQuery->fetch(PDO::FETCH_OBJ);
        $promoteId = $waitUser->id;
        
        // Promote them to 'Reserved' and update the date to NOW() so they get a fresh 3 days for pickup
        $promoteSql = "UPDATE tblreservations SET Status = 'Reserved', ReservationDate = CURRENT_TIMESTAMP WHERE id = :pid";
        $promoteQuery = $dbh->prepare($promoteSql);
        $promoteQuery->bindParam(':pid', $promoteId, PDO::PARAM_INT);
        $promoteQuery->execute();
    }
}
?>
