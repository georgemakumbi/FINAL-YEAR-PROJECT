<?php
/**
 * SMTP Mailer — Compatible with WAMP (local) and Gmail/production
 *
 * Key fix: EHLO returns a MULTI-LINE response (250-CAPABILITY per line,
 * ending with "250 " on the last).  The old sendCommand() only read one
 * line, leaving buffered text that corrupted the STARTTLS TLS handshake
 * and produced: error:0A00010B:SSL routines::wrong version number
 *
 * This version uses getResponse() which drains the full multi-line reply
 * before attempting stream_socket_enable_crypto().
 */

class SMTPMailer {
    private $smtp_host;
    private $smtp_port;
    private $smtp_username;
    private $smtp_password;
    private $from_email;
    private $from_name;
    private $timeout = 30;
    private $debug   = false;

    public function __construct($config = []) {
        $this->smtp_host     = $config['host']       ?? ($_ENV['SMTP_HOST'] ?? 'localhost');
        $this->smtp_port     = (int)($config['port'] ?? ($_ENV['SMTP_PORT'] ?? 587));
        $this->smtp_username = $config['username']   ?? ($_ENV['SMTP_USER'] ?? '');
        $this->smtp_password = $config['password']   ?? ($_ENV['SMTP_PASS'] ?? '');
        $this->from_email    = $config['from_email'] ?? ($_ENV['FROM_EMAIL'] ?? 'noreply@example.com');
        $this->from_name     = $config['from_name']  ?? ($_ENV['FROM_NAME']  ?? 'Voting System');
        $this->debug         = $config['debug']      ?? (($_ENV['SMTP_DEBUG'] ?? 'false') === 'true');
    }

    // -------------------------------------------------------------------------
    // Public send()
    // -------------------------------------------------------------------------
    public function send($to, $subject, $message, $to_name = '') {
        $this->log("Connecting to {$this->smtp_host}:{$this->smtp_port}");

        // Port 465 = implicit SSL (SMTPS). Port 587 = STARTTLS (plain → TLS).
        if ($this->smtp_port === 465) {
            $options = $this->sslOptions();
            $context = stream_context_create($options);
            $socket  = @stream_socket_client(
                "ssl://{$this->smtp_host}:{$this->smtp_port}",
                $errno, $errstr, $this->timeout,
                STREAM_CLIENT_CONNECT, $context
            );
        } else {
            $socket = @stream_socket_client(
                "tcp://{$this->smtp_host}:{$this->smtp_port}",
                $errno, $errstr, $this->timeout
            );
        }

        if (!$socket) {
            $this->log("Connection failed: $errstr ($errno)");
            return false;
        }

        stream_set_timeout($socket, $this->timeout);

        // Server greeting
        $greeting = $this->getResponse($socket);
        $this->log("S: $greeting");

        // EHLO (multi-line — getResponse drains all lines)
        $this->sendRaw($socket, "EHLO " . gethostname());
        $ehlo = $this->getResponse($socket);
        $this->log("EHLO: $ehlo");

        // STARTTLS upgrade for port 587
        if ($this->smtp_port === 587) {
            $this->sendRaw($socket, "STARTTLS");
            $starttls = $this->getResponse($socket);
            $this->log("STARTTLS: $starttls");

            if (substr($starttls, 0, 3) !== '220') {
                $this->log("Server rejected STARTTLS: $starttls");
                fclose($socket);
                return false;
            }

            // Upgrade the plain socket to TLS *now* that the buffer is clean
            stream_context_set_option($socket, $this->sslOptions());
            if (!stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
                $this->log("TLS handshake failed");
                fclose($socket);
                return false;
            }
            $this->log("TLS handshake OK");

            // Re-send EHLO over the encrypted channel
            $this->sendRaw($socket, "EHLO " . gethostname());
            $ehlo2 = $this->getResponse($socket);
            $this->log("EHLO (post-TLS): $ehlo2");
        }

        // AUTH LOGIN
        if (!empty($this->smtp_username) && !empty($this->smtp_password)) {
            $this->sendRaw($socket, "AUTH LOGIN");
            $this->getResponse($socket);                              // 334 VXNlcm5hbWU6

            $this->sendRaw($socket, base64_encode($this->smtp_username));
            $this->getResponse($socket);                              // 334 UGFzc3dvcmQ6

            $this->sendRaw($socket, base64_encode($this->smtp_password));
            $authResp = $this->getResponse($socket);                  // 235 or 535
            $this->log("AUTH: $authResp");

            if (substr($authResp, 0, 3) !== '235') {
                $this->log("Authentication failed: $authResp");
                $this->sendRaw($socket, "QUIT");
                fclose($socket);
                return false;
            }
        }

        // Envelope
        $this->sendRaw($socket, "MAIL FROM:<{$this->from_email}>");
        $this->getResponse($socket);

        $this->sendRaw($socket, "RCPT TO:<$to>");
        $this->getResponse($socket);

        // Data
        $this->sendRaw($socket, "DATA");
        $this->getResponse($socket);   // 354 Start input

        $date    = date("D, d M Y H:i:s O");
        $headers = "From: {$this->from_name} <{$this->from_email}>\r\n"
                 . "Reply-To: {$this->from_email}\r\n"
                 . "MIME-Version: 1.0\r\n"
                 . "Content-Type: text/html; charset=UTF-8\r\n"
                 . "Date: $date\r\n";

        $body = "To: " . ($to_name ? "$to_name <$to>" : $to) . "\r\n"
              . "Subject: $subject\r\n"
              . $headers . "\r\n"
              . $message . "\r\n"
              . ".\r\n";

        fwrite($socket, $body);

        $dataResp = $this->getResponse($socket);   // 250 OK or error
        $this->log("DATA response: $dataResp");

        $this->sendRaw($socket, "QUIT");
        fclose($socket);

        return substr($dataResp, 0, 3) === '250';
    }

    // -------------------------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------------------------

    /**
     * Send a single line to the SMTP socket (no response read).
     */
    private function sendRaw($socket, $command) {
        $this->log("C: $command");
        fwrite($socket, $command . "\r\n");
    }

    /**
     * Read a complete SMTP response, including multi-line replies.
     *
     * SMTP multi-line format:
     *   250-First line          ← dash = more lines follow
     *   250-Second line
     *   250 Last line           ← space = final line
     */
    private function getResponse($socket) {
        $full = '';
        while (!feof($socket)) {
            $line  = fgets($socket, 515);
            $full .= $line;
            // A line shorter than 4 chars can't be multi-line format — stop.
            if (strlen($line) < 4 || $line[3] !== '-') {
                break;
            }
        }
        return trim($full);
    }

    /**
     * SSL options — disables peer verification so WAMP's missing
     * CA bundle doesn't block the TLS handshake.
     */
    private function sslOptions() {
        return [
            'ssl' => [
                'verify_peer'      => false,
                'verify_peer_name' => false,
            ],
        ];
    }

    private function log($message) {
        if ($this->debug) {
            error_log("[SMTP] $message");
        }
    }
}

// ---------------------------------------------------------------------------
// Convenience wrapper — used throughout the app
// ---------------------------------------------------------------------------
function send_smtp_email($to, $subject, $message, $to_name = '') {
    $mailer = new SMTPMailer();
    return $mailer->send($to, $subject, $message, $to_name);
}
?>
