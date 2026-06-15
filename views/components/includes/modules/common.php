<?php
/**
 * =============================================================================
 * COMMON MODULE UTILITIES - Kyambogo University Online Voting System
 * =============================================================================
 * Shared functions and utilities used across all dashboard modules.
 * 
 * This file provides:
 * - Common constants and configuration
 * - Helper functions for HTML rendering
 * - Utility functions used by multiple modules
 * - Status badge colors and styles
 * 
 * =============================================================================
 */

// =============================================================================
// STATUS CONSTANTS
// =============================================================================

define('STATUS_ACTIVE', 'active');
define('STATUS_SCHEDULED', 'scheduled');
define('STATUS_CLOSED', 'closed');

// =============================================================================
// HTML HELPER FUNCTIONS
// =============================================================================

/**
 * Render a status badge with appropriate color
 * 
 * @param string $status The status value
 * @param string $display_text Optional custom display text
 * @return string HTML string for the badge
 */
function render_status_badge($status, $display_text = null) {
    if ($display_text === null) {
        $display_text = ucfirst($status);
    }
    
    $status_class = 'status-scheduled';
    if ($status === STATUS_ACTIVE) {
        $status_class = 'status-active';
    } elseif ($status === STATUS_CLOSED) {
        $status_class = 'status-closed';
    }
    
    return '<span class="status-badge ' . htmlspecialchars($status_class) . '">' . htmlspecialchars($display_text) . '</span>';
}

/**
 * Safe HTML output with escaping
 * 
 * @param mixed $value The value to escape and display
 * @return string Escaped HTML-safe string
 */
function safe_output($value) {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Render a button with optional CSS classes
 * 
 * @param string $type Button type (button, submit, reset)
 * @param string $text Button text
 * @param string $classes Additional CSS classes
 * @param array $attributes Additional HTML attributes
 * @return string HTML button element
 */
function render_button($text, $type = 'button', $classes = '', $attributes = []) {
    $classes = 'btn ' . $classes;
    $attr_string = '';
    
    foreach ($attributes as $key => $value) {
        $attr_string .= ' ' . htmlspecialchars($key) . '="' . htmlspecialchars($value) . '"';
    }
    
    return '<button type="' . htmlspecialchars($type) . '" class="' . htmlspecialchars($classes) . '"' . $attr_string . '>' 
           . htmlspecialchars($text) . '</button>';
}

/**
 * Get formatted date/time string
 * 
 * @param string $datetime ISO format datetime string
 * @param string $format PHP date format (default: Y-m-d H:i:s)
 * @return string Formatted datetime
 */
function format_datetime($datetime, $format = 'Y-m-d H:i:s') {
    if (empty($datetime)) {
        return 'N/A';
    }
    
    try {
        $date = new DateTime($datetime);
        return $date->format($format);
    } catch (Exception $e) {
        return htmlspecialchars($datetime);
    }
}

/**
 * Render message alert box (success, error, warning, info)
 * 
 * @param string $type Type of message (success, error, warning, info)
 * @param string $message The message text
 * @return string HTML alert box
 */
function render_alert($type, $message) {
    if (empty($message)) {
        return '';
    }
    
    return '<div class="message ' . htmlspecialchars($type) . '">' . htmlspecialchars($message) . '</div>';
}

/**
 * Check if user is super admin (utility for modules)
 * 
 * @return bool True if current user is super_admin
 */
function is_super_admin() {
    return isset($_SESSION['admin_role']) && $_SESSION['admin_role'] === 'super_admin';
}

/**
 * Escape string for database use
 * 
 * @param mysqli $conn Database connection
 * @param string $string String to escape
 * @return string Escaped string
 */
function escape_string($conn, $string) {
    return $conn->real_escape_string($string);
}

/**
 * Format number with thousands separator
 * 
 * @param int $number The number to format
 * @return string Formatted number
 */
function format_number($number) {
    return number_format(intval($number ?? 0));
}

/**
 * Get CSRF token from session
 * 
 * @return string CSRF token
 */
function get_csrf_token() {
    return htmlspecialchars($_SESSION['csrf_token'] ?? '');
}

/**
 * Render hidden CSRF token input field
 * 
 * @return string HTML hidden input with CSRF token
 */
function render_csrf_field() {
    return '<input type="hidden" name="csrf_token" value="' . get_csrf_token() . '">';
}

/**
 * Get system logo path
 * Checks if 'system_logo' is set in settings table. If not, falls back to default.
 * 
 * @param mysqli $conn Database connection
 * @param string $relative_prefix Prefix to go from current page to the project root (e.g. '../' or '')
 * @return string Logo URL or file path
 */
function get_system_logo($conn, $relative_prefix = '') {
    static $logo_path = null;
    if ($logo_path !== null) {
        return $logo_path;
    }

    // Auto-migrate: Check if settings table exists, if not, create it
    ensure_settings_table($conn);

    $logo_path = $relative_prefix . 'assets/images/image.png'; // Default fallback
    
    $result = @$conn->query("SELECT setting_value FROM settings WHERE setting_key = 'system_logo' LIMIT 1");
    if ($result && $row = $result->fetch_assoc()) {
        $val = $row['setting_value'];
        if (!empty($val)) {
            if (strpos($val, 'http://') === 0 || strpos($val, 'https://') === 0) {
                $logo_path = $val;
            } else {
                $logo_path = $relative_prefix . $val;
            }
        }
    }
    return $logo_path;
}

?>
