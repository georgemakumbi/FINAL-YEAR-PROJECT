<?php
/**
 * SMTP Mailer — Built using PHPMailer
 * Compatible with Vercel serverless environment and local WAMP development.
 */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Load Composer autoloader if not already loaded
$autoload = dirname(__DIR__, 2) . '/vendor/autoload.php';
if (file_exists($autoload)) {
    require_once $autoload;
}

/**
 * Convenience wrapper — used throughout the app to send emails.
 *
 * @param string $to Recipient email address
 * @param string $subject Email subject
 * @param string $message Email body (HTML)
 * @param string $to_name Recipient name (optional)
 * @return bool True if mail sent successfully, false otherwise
 */
function send_smtp_email($to, $subject, $message, $to_name = '') {
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = $_ENV['SMTP_HOST'] ?? 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = $_ENV['SMTP_USER'] ?? '';
        $mail->Password   = $_ENV['SMTP_PASS'] ?? '';
        
        $port = (int)($_ENV['SMTP_PORT'] ?? 587);
        $mail->Port       = $port;
        if ($port === 465) {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        } else {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        }

        // Disable TLS certificate verification for local development (WAMP Compatibility)
        // Helps bypass missing CA root certificate bundle issues on localhost
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );

        // Recipients
        $fromEmail = $_ENV['FROM_EMAIL'] ?? ($_ENV['SMTP_USER'] ?? 'noreply@example.com');
        $fromName  = $_ENV['FROM_NAME'] ?? 'Voting System';
        $mail->setFrom($fromEmail, $fromName);
        $mail->addAddress($to, $to_name);

        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $message;

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("[PHPMailer] Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
        return false;
    }
}
?>
