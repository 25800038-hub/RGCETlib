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

        // Send Email Notification
        try {
            require_once __DIR__ . '/../services/MailService.php';
            $mailService = new MailService($dbh);
            $sid = $waitUser->StudentID;
            
            $infoSql = "SELECT b.BookName, COALESCE(s.FullName, t.FullName) as FullName, COALESCE(s.EmailId, t.EmailId) as EmailId FROM tblbooks b LEFT JOIN tblstudents s ON s.StudentId = :sid LEFT JOIN tblteachers t ON t.TeacherId = :sid2 WHERE b.id = :bid";
            $infoQuery = $dbh->prepare($infoSql);
            $infoQuery->execute([':sid' => $sid, ':sid2' => $sid, ':bid' => $bookId]);
            $info = $infoQuery->fetch(PDO::FETCH_OBJ);

            if($info && !empty($info->EmailId)) {
                $subject = "Waitlist Update - Book Now Available!";
                $htmlBody = "<p>Hello {$info->FullName},</p>
                             <p>Great news! A copy of <strong>{$info->BookName}</strong> has been returned and is now reserved for you.</p>
                             <p><strong>Please collect it from the library counter within 3 days</strong>, otherwise your reservation will be cancelled.</p>
                             <p>Thank you,<br>RGCET Library</p>";
                $mailService->sendEmail($info->EmailId, $subject, $htmlBody, $promoteId, 'Waitlist_Promoted');
            }
        } catch (\Exception $e) {}
    }
}
