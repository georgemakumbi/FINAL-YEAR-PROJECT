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

function resend_env(string $key, string $default = ''): string
{
    $value = $_ENV[$key] ?? getenv($key);
    if ($value === false || $value === null) {
        return $default;
    }

    return (string)$value;
}

function get_resend_config(): array
{
    $apiKey = resend_env('RESEND_API_KEY');
    $fromEmail = resend_env('RESEND_FROM_EMAIL');
    $fromName = resend_env('RESEND_FROM_NAME', 'Voting System');

    if ($apiKey === '' || $fromEmail === '') {
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
function send_resend_email($to, $subject, $htmlMessage, $to_name = ''): bool
{
    try {
        [$apiKey, $fromEmail, $fromName] = get_resend_config();

        $from = $fromName !== ''
            ? $fromName . ' <' . $fromEmail . '>'
            : $fromEmail;

        $payload = [
            'from' => $from,
            'to' => [$to],
            'subject' => $subject,
            'html' => $htmlMessage,
        ];

        $ch = curl_init('https://api.resend.com/emails');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $apiKey,
            'Content-Type: application/json',
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);

        // ── SSL: try the WAMP-shipped cacert.pem first, then system bundle ──
        $wampCaCert = 'C:/wamp64/bin/php/php8.3.28/cacert.pem';
        if (file_exists($wampCaCert)) {
            curl_setopt($ch, CURLOPT_CAINFO, $wampCaCert);
        } elseif (function_exists('curl_version')) {
            $cv = curl_version();
            if (!empty($cv['ssl_version'])) {
                // Let cURL use system CA store (Linux/Mac/production)
            }
        }
        // Last resort: disable peer verification on localhost only
        $host = $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? 'localhost';
        if (in_array($host, ['localhost', '127.0.0.1', '::1'])) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        }


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
