<?php
/**
 * =============================================================================
 * RESULTS PUBLISHING HELPERS
 * =============================================================================
 * Provides a simple file-based toggle to publish/unpublish election results.
 * =============================================================================
 */

define('RESULTS_STATUS_FILE', __DIR__ . '/../results_status.txt');

/**
 * Get current results publish status.
 *
 * @return string 'published' or 'unpublished'
 */
function get_results_publish_status(): string
{
    if (!file_exists(RESULTS_STATUS_FILE)) {
        return 'unpublished';
    }

    $value = trim((string)file_get_contents(RESULTS_STATUS_FILE));
    return $value === 'published' ? 'published' : 'unpublished';
}

/**
 * Persist results publish status.
 *
 * @param string $status 'published' or 'unpublished'
 * @return bool True on success
 */
function set_results_publish_status(string $status): bool
{
    $normalized = $status === 'published' ? 'published' : 'unpublished';
    return file_put_contents(RESULTS_STATUS_FILE, $normalized) !== false;
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
