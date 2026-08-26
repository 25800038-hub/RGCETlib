<?php
/**
 * Automated Due-Date Reminders
 * Run this script manually for testing, or via a cron job / task scheduler in production.
 */

// If running from CLI or browser, paths should resolve correctly
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../services/MailService.php';

$mailService = new MailService($dbh);
$today = new DateTime('today'); // Normalizes to 00:00:00
$todayStr = $today->format('Y-m-d');

// Add a <pre> tag if running in a web browser for better readability
if (php_sapi_name() !== 'cli') {
    echo "<pre>\n";
}

echo "Starting Automated Due-Date Reminders - " . date('Y-m-d H:i:s') . "\n";

try {
    // 1. Fetch books that are currently issued and NOT returned
    $sql = "SELECT i.id as IssueId, i.StudentID, i.BookId, i.IssuesDate, b.BookName 
            FROM tblissuedbookdetails i 
            JOIN tblbooks b ON b.id = i.BookId 
            WHERE i.RetrunStatus IS NULL OR i.RetrunStatus = '' OR i.RetrunStatus = '0'";
    $query = $dbh->prepare($sql);
    $query->execute();
    $activeIssues = $query->fetchAll(PDO::FETCH_OBJ);

    $processedCount = 0;
    $sentCount = 0;

    foreach ($activeIssues as $issue) {
        $processedCount++;
        
        $issueId = $issue->IssueId;
        $studentId = $issue->StudentID;
        $bookName = $issue->BookName;
        $issuesDateStr = $issue->IssuesDate;
        
        // Find member details
        $memberEmail = null;
        $memberName = null;

        if (strpos($studentId, 'SID') === 0) {
            $userQuery = $dbh->prepare("SELECT FullName, EmailId FROM tblstudents WHERE StudentId = :id");
            $userQuery->execute([':id' => $studentId]);
            $user = $userQuery->fetch(PDO::FETCH_OBJ);
        } else {
            $userQuery = $dbh->prepare("SELECT FullName, EmailId FROM tblteachers WHERE TeacherId = :id");
            $userQuery->execute([':id' => $studentId]);
            $user = $userQuery->fetch(PDO::FETCH_OBJ);
        }

        if (!$user || empty($user->EmailId)) {
            // Skip invalid or missing email addresses
            continue;
        }

        $memberName = $user->FullName;
        $memberEmail = $user->EmailId;

        // Calculate Dates
        $issueDate = new DateTime($issuesDateStr);
        $dueDate = clone $issueDate;
        $dueDate->modify('+7 days');
        
        $dueDay = clone $dueDate;
        $dueDay->setTime(0, 0, 0); // Normalize time to midnight for accurate day diffs
        
        $interval = $today->diff($dueDay);
        $daysDiff = $interval->days;
        $isPast = $interval->invert; // 1 if today is past the due date

        $reminderType = null;
        $subject = '';
        $htmlBody = '';

        // Determine reminder type based on schedule rules
        if ($daysDiff == 3 && $isPast == 0) {
            $reminderType = 'Reminder_3Day';
            $subject = 'Library Reminder: Your Book Is Due Soon';
            $htmlBody = "<p>Hello {$memberName},</p>
                         <p>This is a friendly reminder that your book is due in 3 days.</p>
                         <p><strong>Book:</strong> {$bookName}<br>
                         <strong>Due Date:</strong> {$dueDate->format('Y-m-d')}<br>
                         <strong>Days Remaining:</strong> 3</p>
                         <p>Please return or renew the book on time to avoid fines.</p>
                         <p>Thank you,<br>RGCET Library</p>";
                         
        } else if ($daysDiff == 1 && $isPast == 0) {
            $reminderType = 'Reminder_1Day';
            $subject = 'Library Reminder: Book Due Tomorrow';
            $htmlBody = "<p>Hello {$memberName},</p>
                         <p>This is a critical reminder that your book is due <strong>tomorrow</strong>.</p>
                         <p><strong>Book:</strong> {$bookName}<br>
                         <strong>Due Date:</strong> {$dueDate->format('Y-m-d')}</p>
                         <p>Please return the book by tomorrow to avoid fines.</p>
                         <p>Thank you,<br>RGCET Library</p>";

        } else if ($isPast == 1 && $daysDiff > 0) {
            // Overdue reminder: appended with current date so it sends one per day
            $reminderType = 'Reminder_Overdue_' . $todayStr;
            $subject = 'Library Alert: Your Book Is Overdue';
            $fineAmount = $daysDiff * 1; // Existing fine logic: ₹1 per day overdue
            
            $htmlBody = "<p>Hello {$memberName},</p>
                         <p>Your library book is currently overdue.</p>
                         <p><strong>Book:</strong> {$bookName}<br>
                         <strong>Original Due Date:</strong> {$dueDate->format('Y-m-d')}<br>
                         <strong>Days Overdue:</strong> {$daysDiff}<br>
                         <strong>Current Fine:</strong> ₹{$fineAmount}</p>
                         <p>Please return the book immediately.</p>
                         <p>Thank you,<br>RGCET Library</p>";
        }

        // If a reminder condition is met, process it
        if ($reminderType !== null) {
            // Prevent Duplicates
            $checkSql = "SELECT id FROM tblnotifications WHERE related_id = :issueId AND notification_type = :notifType AND status = 'sent'";
            $checkQuery = $dbh->prepare($checkSql);
            $checkQuery->execute([
                ':issueId' => $issueId,
                ':notifType' => $reminderType
            ]);

            if ($checkQuery->rowCount() == 0) {
                // Duplicate check passed, send email!
                try {
                    $success = $mailService->sendEmail($memberEmail, $subject, $htmlBody, $issueId, $reminderType);
                    if ($success) {
                        $sentCount++;
                        echo "Sent [{$reminderType}] to {$memberEmail} for Issue #{$issueId}\n";
                    } else {
                        echo "Failed to send [{$reminderType}] to {$memberEmail} for Issue #{$issueId}\n";
                    }
                    
                    // Delay execution for 1 second to respect Mailtrap's free tier rate limits (max ~2 emails/sec)
                    sleep(1);
                    
                } catch (\Exception $e) {
                    echo "Exception during email send for Issue #{$issueId}: " . $e->getMessage() . "\n";
                    // Continue to next issue without breaking automation script
                }
            } else {
                // Reminder already sent, skip safely
                // echo "Skipped [{$reminderType}] for Issue #{$issueId} - already sent.\n";
            }
        }
    }
    
    echo "Completed. Processed {$processedCount} active issues. Sent {$sentCount} reminders.\n";

} catch (\Exception $e) {
    echo "Fatal Automation Error: " . $e->getMessage() . "\n";
}

if (php_sapi_name() !== 'cli') {
    echo "</pre>\n";
}
