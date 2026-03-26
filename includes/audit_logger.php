<?php

function ensure_audit_log_table(mysqli $conn): void
{
    static $initialized = false;
    if ($initialized) {
        return;
    }

    $sql = "CREATE TABLE IF NOT EXISTS audit_log (
        log_id INT AUTO_INCREMENT PRIMARY KEY,
        user_id VARCHAR(50),
        action VARCHAR(100) NOT NULL,
        details TEXT,
        ip_address VARCHAR(45),
        timestamp DATETIME DEFAULT CURRENT_TIMESTAMP
    )";

    $conn->query($sql);
    $initialized = true;
}

function log_audit_event(mysqli $conn, ?string $user_id, string $action, string $details = ''): void
{
    try {
        ensure_audit_log_table($conn);

        $ip_address = $_SERVER['REMOTE_ADDR'] ?? '';
        $stmt = $conn->prepare("INSERT INTO audit_log (user_id, action, details, ip_address) VALUES (?, ?, ?, ?)");
        if ($stmt === false) {
            return;
        }

        $normalized_user_id = $user_id !== null ? trim($user_id) : '';
        $stmt->bind_param("ssss", $normalized_user_id, $action, $details, $ip_address);
        $stmt->execute();
        $stmt->close();
    } catch (Throwable $e) {
        error_log("Audit log write failed: " . $e->getMessage());
    }
}

