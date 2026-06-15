<?php
/**
 * =============================================================================
 * RESULTS PUBLISHING HELPERS
 * =============================================================================
 * Persists publish/unpublish state in the settings table so it works on
 * read-only filesystems (e.g. Vercel serverless).
 * =============================================================================
 */

const RESULTS_PUBLISH_SETTING_KEY = 'results_publish_status';

/**
 * Ensure the settings table exists.
 */
function ensure_settings_table(mysqli $conn): void
{
    $table_check = @$conn->query("SHOW TABLES LIKE 'settings'");
    if ($table_check && $table_check->num_rows === 0) {
        $conn->query("CREATE TABLE settings (
            setting_key VARCHAR(50) PRIMARY KEY,
            setting_value TEXT NOT NULL,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    }
}

/**
 * Normalize a status string to 'published' or 'unpublished'.
 */
function normalize_results_publish_status(string $status): string
{
    return $status === 'published' ? 'published' : 'unpublished';
}

/**
 * Read status from legacy file locations (one-time migration aid).
 */
function migrate_results_publish_status_from_legacy_files(): ?string
{
    $legacy_paths = array_filter([
        defined('STORAGE_PATH') ? STORAGE_PATH . '/results_status.txt' : null,
        defined('PROJECT_ROOT') ? PROJECT_ROOT . '/views/components/results_status.txt' : null,
        defined('APP_CONFIG') ? APP_CONFIG . '/results_status.txt' : null,
    ]);

    foreach ($legacy_paths as $path) {
        if (file_exists($path)) {
            return normalize_results_publish_status(trim((string)file_get_contents($path)));
        }
    }

    return null;
}

/**
 * Get current results publish status.
 *
 * @return string 'published' or 'unpublished'
 */
function get_results_publish_status(): string
{
    global $conn;

    if (!$conn instanceof mysqli) {
        return 'unpublished';
    }

    ensure_settings_table($conn);

    $stmt = $conn->prepare("SELECT setting_value FROM settings WHERE setting_key = ? LIMIT 1");
    if (!$stmt) {
        return 'unpublished';
    }

    $key = RESULTS_PUBLISH_SETTING_KEY;
    $stmt->bind_param('s', $key);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        return normalize_results_publish_status($row['setting_value']);
    }

    $migrated = migrate_results_publish_status_from_legacy_files();
    if ($migrated !== null) {
        set_results_publish_status($migrated);
        return $migrated;
    }

    return 'unpublished';
}

/**
 * Persist results publish status.
 *
 * @param string $status 'published' or 'unpublished'
 * @return bool True on success
 */
function set_results_publish_status(string $status): bool
{
    global $conn;

    if (!$conn instanceof mysqli) {
        return false;
    }

    ensure_settings_table($conn);

    $normalized = normalize_results_publish_status($status);
    $stmt = $conn->prepare(
        "INSERT INTO settings (setting_key, setting_value) VALUES (?, ?)
         ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)"
    );
    if (!$stmt) {
        return false;
    }

    $key = RESULTS_PUBLISH_SETTING_KEY;
    $stmt->bind_param('ss', $key, $normalized);

    return $stmt->execute();
}

/**
 * Convenience boolean check.
 *
 * @return bool True if results are published
 */
function results_are_published(): bool
{
    return get_results_publish_status() === 'published';
}

?>
