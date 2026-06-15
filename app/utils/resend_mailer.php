<?php
// Resend Mailer wrapper
//
// Expected environment variables (loaded by app/utils/db_connection.php via bootstrap.php):
// - RESEND_API_KEY
// - RESEND_FROM_EMAIL
// - RESEND_FROM_NAME (optional)


if (!defined('PROJECT_ROOT')) {
    require_once dirname(__DIR__, 2) . '/bootstrap.php';
}

function get_resend_config() {
    $apiKey = $_ENV['RESEND_API_KEY'] ?? '';
    $fromEmail = $_ENV['RESEND_FROM_EMAIL'] ?? '';
    $fromName = $_ENV['RESEND_FROM_NAME'] ?? 'Voting System';

    if (empty($apiKey) || empty($fromEmail)) {
        throw new Exception('Resend mailer misconfigured: missing RESEND_API_KEY or RESEND_FROM_EMAIL');
    }

    return [$apiKey, $fromEmail, $fromName];
}

/**
 * @param string $to
 * @param string $subject
 * @param string $htmlMessage HTML body
 * @param string $to_name
 * @return bool
 */
function send_resend_email($to, $subject, $htmlMessage, $to_name = ''): bool {
    try {
        [$apiKey, $fromEmail, $fromName] = get_resend_config();

        $payload = [
            'from' => [
                'email' => $fromEmail,
                'name'  => $fromName
            ],
            'to' => [
                [
                    'email' => $to,
                    'name'  => $to_name ?: $to
                ]
            ],
            'subject' => $subject,
            'html' => $htmlMessage
        ];

        $ch = curl_init('https://api.resend.com/emails');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $apiKey,
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

        $resp = curl_exec($ch);
        $httpCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err = curl_error($ch);
        curl_close($ch);

        if ($resp === false) {
            error_log('Resend send failed (curl): ' . $err);
            return false;
        }

        if ($httpCode < 200 || $httpCode >= 300) {
            error_log('Resend send failed (http ' . $httpCode . '): ' . $resp);
            return false;
        }

        return true;
    } catch (Exception $e) {
        error_log('Resend send failed: ' . $e->getMessage());
        return false;
    }
}
?>

