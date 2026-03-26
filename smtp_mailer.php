<?php
/**
 * Simple SMTP Mailer Class
 * Handles sending emails via SMTP (tested with Gmail, Outlook, etc.)
 */

class SMTPMailer {
    private $smtp_host;
    private $smtp_port;
    private $smtp_username;
    private $smtp_password;
    private $from_email;
    private $from_name;
    private $timeout = 30;
    private $debug = false;
    
    public function __construct($config = []) {
        $this->smtp_host = $config['host'] ?? 'smtp.gmail.com';
        $this->smtp_port = $config['port'] ?? 587;
        $this->smtp_username = $config['username'] ?? '';
        $this->smtp_password = $config['password'] ?? '';
        $this->from_email = $config['from_email'] ?? '';
        $this->from_name = $config['from_name'] ?? 'Voting System';
        $this->debug = $config['debug'] ?? false;
    }
    
    /**
     * Send an email
     * @param string $to Recipient email
     * @param string $subject Email subject
     * @param string $message Email body (HTML supported)
     * @param string $to_name Optional recipient name
     * @return bool Success status
     */
    public function send($to, $subject, $message, $to_name = '') {
        $this->debug("Starting SMTP mail send to: $to");
        
        // Create socket connection
        $socket = @fsockopen(
            $this->smtp_host, 
            $this->smtp_port, 
            $errno, 
            $errstr, 
            $this->timeout
        );
        
        if (!$socket) {
            $this->debug("Socket connection failed: $errstr ($errno)");
            return false;
        }
        
        // Read welcome message
        $response = fgets($socket, 515);
        $this->debug("Server: $response");
        
        // Send HELO
        $this->sendCommand($socket, "HELO " . gethostname());
        
        // Start TLS if using port 587
        if ($this->smtp_port == 587) {
            $this->sendCommand($socket, "STARTTLS");
            stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
            
            // Re-send HELO after TLS
            $this->sendCommand($socket, "HELO " . gethostname());
        }
        
        // Authenticate only if username/password are provided
        if (!empty($this->smtp_username) && !empty($this->smtp_password)) {
            $this->sendCommand($socket, "AUTH LOGIN");
            $this->sendCommand($socket, base64_encode($this->smtp_username));
            $this->sendCommand($socket, base64_encode($this->smtp_password));
        }
        
        // Mail From
        $this->sendCommand($socket, "MAIL FROM:<{$this->from_email}>");
        
        // Rcpt To
        $this->sendCommand($socket, "RCPT TO:<$to>");
        
        // Data
        $this->sendCommand($socket, "DATA");
        
        // Build email headers
        $date = date("D, d M Y H:i:s O");
        $headers = "From: {$this->from_name} <{$this->from_email}>\r\n";
        $headers .= "Reply-To: {$this->from_email}\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        $headers .= "Date: $date\r\n";
        
        $email_content = "To: " . ($to_name ? "$to_name <$to>" : $to) . "\r\n";
        $email_content .= "Subject: $subject\r\n";
        $email_content .= $headers . "\r\n";
        $email_content .= $message . "\r\n";
        $email_content .= ".\r\n";
        
        fwrite($socket, $email_content);
        
        $response = fgets($socket, 515);
        $this->debug("Server response: $response");
        
        // Quit
        $this->sendCommand($socket, "QUIT");
        
        fclose($socket);
        
        if (substr($response, 0, 3) == '250') {
            $this->debug("Email sent successfully!");
            return true;
        }
        
        $this->debug("Email sending failed: $response");
        return false;
    }
    
    private function sendCommand($socket, $command) {
        $this->debug("Client: $command");
        fwrite($socket, $command . "\r\n");
        $response = fgets($socket, 515);
        $this->debug("Server: $response");
        return $response;
    }
    
    private function debug($message) {
        if ($this->debug) {
            echo "[SMTP DEBUG] $message<br>";
        }
    }
}

/**
 * Convenience function to send email with SMTP
 */
function send_smtp_email($to, $subject, $message, $to_name = '') {
    // SMTP Configuration - Using MailHog for local testing
    $smtp_config = [
        'host' => 'localhost',             // MailHog SMTP host
        'port' => 1025,                     // MailHog SMTP port
        'username' => '',                   // No authentication needed for MailHog
        'password' => '',                   // No authentication needed for MailHog
        'from_email' => 'noreply@kyambogo.ac.ug',   // Sender email
        'from_name' => 'Kyambogo Voting System',  // Sender name
        'debug' => false                   // Set to true to see debug messages
    ];
    
    $mailer = new SMTPMailer($smtp_config);
    return $mailer->send($to, $subject, $message, $to_name);
}
?>

