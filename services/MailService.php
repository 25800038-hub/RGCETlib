<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Require PHPMailer files directly since Composer is not used
require_once __DIR__ . '/../includes/PHPMailer/src/Exception.php';
require_once __DIR__ . '/../includes/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/../includes/PHPMailer/src/SMTP.php';

class MailService
{
    private $config;
    private $dbh;

    public function __construct($dbh)
    {
        $this->dbh = $dbh;
        $this->config = require __DIR__ . '/../config/mail.php';
    }

    /**
     * Send an email and log it to the database
     *
     * @param string $to Recipient email address
     * @param string $subject Email subject
     * @param string $htmlBody Email body in HTML
     * @param int|null $relatedId The ID of the related transaction (e.g. Issue ID)
     * @param string|null $notificationType Type of notification (e.g. Issue, Return)
     * @return bool True if successful, False otherwise
     */
    public function sendEmail($to, $subject, $htmlBody, $relatedId = null, $notificationType = null)
    {
        $mail = new PHPMailer(true);

        $status = 'failed';
        $errorMessage = null;

        try {
            // Server settings
            $mail->isSMTP();
            $mail->CharSet = 'UTF-8';
            $mail->Host       = $this->config['host'];
            $mail->SMTPAuth   = true;
            $mail->Username   = $this->config['username'];
            $mail->Password   = $this->config['password'];
            $mail->Port       = $this->config['port'];
            
            // Set encryption based on config (e.g. tls -> ENCRYPTION_STARTTLS, ssl -> ENCRYPTION_SMTPS)
            if (strtolower($this->config['encryption']) == 'tls') {
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            } elseif (strtolower($this->config['encryption']) == 'ssl') {
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            }

            // Recipients
            $mail->setFrom($this->config['from_email'], $this->config['from_name']);
            $mail->addAddress($to);

            // Content
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $htmlBody;
            $mail->AltBody = strip_tags(str_replace(['<br>', '<br/>', '<br />'], "\n", $htmlBody));

            $mail->send();
            $status = 'sent';
            
            $this->logNotification($to, $subject, $status, $errorMessage, $relatedId, $notificationType);
            return true;

        } catch (Exception $e) {
            $errorMessage = $mail->ErrorInfo;
            $this->logNotification($to, $subject, $status, $errorMessage, $relatedId, $notificationType);
            return false;
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            $this->logNotification($to, $subject, $status, $errorMessage, $relatedId, $notificationType);
            return false;
        }
    }

    /**
     * Log the email attempt into the tblnotifications table
     */
    private function logNotification($to, $subject, $status, $errorMessage, $relatedId, $notificationType)
    {
        try {
            $sql = "INSERT INTO tblnotifications (recipient_email, subject, status, error_message, related_id, notification_type) 
                    VALUES (:recipient_email, :subject, :status, :error_message, :related_id, :notification_type)";
            $query = $this->dbh->prepare($sql);
            $query->bindParam(':recipient_email', $to, PDO::PARAM_STR);
            $query->bindParam(':subject', $subject, PDO::PARAM_STR);
            $query->bindParam(':status', $status, PDO::PARAM_STR);
            $query->bindParam(':error_message', $errorMessage, PDO::PARAM_STR);
            $query->bindParam(':related_id', $relatedId, PDO::PARAM_INT);
            $query->bindParam(':notification_type', $notificationType, PDO::PARAM_STR);
            $query->execute();
        } catch (\PDOException $e) {
            // Silently fail logging rather than crashing the application
            error_log("Failed to log notification: " . $e->getMessage());
        }
    }
}
