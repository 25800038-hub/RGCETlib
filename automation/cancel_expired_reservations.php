<?php
/**
 * cancel_expired_reservations.php
 * 
 * Automatically cancels any 'Reserved' status reservations that are older than 3 days.
 * Promotes the next person on the waitlist if applicable, and sends cancellation emails.
 * Run this via Task Scheduler or Cron Job daily.
 */

define('DB_HOST','localhost');
define('DB_USER','root');
define('DB_PASS','');
define('DB_NAME','library');

try {
    $dbh = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

require_once __DIR__ . '/../services/MailService.php';
require_once __DIR__ . '/../includes/promote_waitlist.php';

$mailService = new MailService($dbh);

echo "Starting expired reservation check...\n";

// Find all reservations that are strictly older than 3 days (72 hours)
$sql = "SELECT r.id as resid, r.BookId, r.StudentID, b.BookName, 
        COALESCE(s.FullName, t.FullName) as FullName, 
        COALESCE(s.EmailId, t.EmailId) as EmailId
        FROM tblreservations r
        JOIN tblbooks b ON b.id = r.BookId
        LEFT JOIN tblstudents s ON s.StudentId = r.StudentID
        LEFT JOIN tblteachers t ON t.TeacherId = r.StudentID
        WHERE r.Status = 'Reserved' 
        AND r.ReservationDate < DATE_SUB(NOW(), INTERVAL 3 DAY)";

$query = $dbh->prepare($sql);
$query->execute();
$results = $query->fetchAll(PDO::FETCH_OBJ);

if(empty($results)) {
    echo "No expired reservations found.\n";
    exit;
}

$cancelledCount = 0;

foreach($results as $res) {
    echo "Processing expired reservation #{$res->resid} for {$res->FullName}...\n";
    
    // 1. Cancel the reservation
    $cancelSql = "UPDATE tblreservations SET Status = 'Cancelled', AdminRemark = 'Automatically cancelled (not collected within 3 days)' WHERE id = :resid";
    $cancelQuery = $dbh->prepare($cancelSql);
    $cancelQuery->execute([':resid' => $res->resid]);
    
    // 2. Send cancellation email
    if(!empty($res->EmailId)) {
        $subject = "Reservation Expired - RGCET Library";
        $htmlBody = "<p>Hello {$res->FullName},</p>
                    <p>Your reservation for the book <strong>{$res->BookName}</strong> has been automatically cancelled because it was not collected from the library counter within the 3-day pickup window.</p>
                    <p>If you still need this book, please log into the library portal to reserve it again or join the waitlist.</p>
                    <p>Thank you,<br>RGCET Library</p>";
                    
        $mailService->sendEmail($res->EmailId, $subject, $htmlBody, $res->resid, 'Reservation_Expired');
    }
    
    // 3. Promote the waitlist
    promoteWaitlist($dbh, $res->BookId);
    
    $cancelledCount++;
}

echo "Finished. Automatically cancelled {$cancelledCount} expired reservation(s).\n";
?>
