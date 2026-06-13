<?php
/**
 * =============================================================================
 * INPUT VALIDATOR — Sanitize & Validate User Input
 * =============================================================================
 * 
 * GOLDEN RULE: Never trust user input!
 * 
 * This class provides reusable validation methods for common data types.
 * Use these BEFORE processing any user-submitted data.
 *
 * USAGE:
 *   // Validate a student ID format
 *   if (!InputValidator::isValidStudentId('23/U/001')) {
 *       die("Invalid student ID format");
 *   }
 *
 *   // Sanitize text input
 *   $clean = InputValidator::sanitizeText($_POST['feedback']);
 *
 * =============================================================================
 */

class InputValidator
{
    /**
     * Validate Kyambogo University Student ID format.
     * 
     * Valid formats: "23/U/001", "2023/U/12345", "23/u/001"
     * Pattern: 2-4 digits / 1+ letters / 1+ digits
     *
     * WHAT IS A REGEX?
     *   A "regular expression" is a pattern for matching text:
     *   /^\d{2,4}\/[A-Za-z]+\/\d+$/
     *   
     *   ^        → Start of string
     *   \d{2,4}  → 2 to 4 digits (year: "23" or "2023")
     *   \/       → Literal forward slash
     *   [A-Za-z]+→ One or more letters (faculty code: "U", "CS")
     *   \d+      → One or more digits (student number: "001")
     *   $        → End of string
     *
     * @param string $student_id  The student ID to validate
     * @return bool               true if format is valid
     */
    public static function isValidStudentId(string $student_id): bool
    {
        // Allow common KyU student ID formats
        return (bool)preg_match('/^\d{2,4}\/[A-Za-z]+\/\d+$/', trim($student_id));
    }

    /**
     * Validate email address format.
     * 
     * Uses PHP's built-in filter — much more reliable than regex for email.
     *
     * @param string $email  Email to validate
     * @return bool          true if valid email format
     */
    public static function isValidEmail(string $email): bool
    {
        return filter_var(trim($email), FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Check password strength.
     * 
     * A strong password must have:
     *   - At least 8 characters
     *   - At least one uppercase letter (A-Z)
     *   - At least one lowercase letter (a-z)
     *   - At least one digit (0-9)
     *
     * @param string $password  Password to check
     * @return array            ['valid' => bool, 'errors' => string[]]
     */
    public static function checkPasswordStrength(string $password): array
    {
        $errors = [];

        if (strlen($password) < 8) {
            $errors[] = "Password must be at least 8 characters long";
        }
        if (!preg_match('/[A-Z]/', $password)) {
            $errors[] = "Password must contain at least one uppercase letter";
        }
        if (!preg_match('/[a-z]/', $password)) {
            $errors[] = "Password must contain at least one lowercase letter";
        }
        if (!preg_match('/[0-9]/', $password)) {
            $errors[] = "Password must contain at least one number";
        }

        return [
            'valid'  => empty($errors),
            'errors' => $errors,
        ];
    }

    /**
     * Sanitize text input — remove dangerous content.
     * 
     * Used for feedback, manifesto, and other free-text fields.
     * - trim(): Remove leading/trailing whitespace
     * - strip_tags(): Remove HTML tags (prevents XSS)
     * - htmlspecialchars(): Convert special chars to HTML entities
     *
     * @param string $text    Raw user input
     * @param int    $maxLen  Maximum allowed length (default: 2000)
     * @return string         Clean, safe text
     */
    public static function sanitizeText(string $text, int $maxLen = 2000): string
    {
        $text = trim($text);
        $text = strip_tags($text);
        
        // Limit length to prevent database overflow
        if (mb_strlen($text) > $maxLen) {
            $text = mb_substr($text, 0, $maxLen);
        }
        
        return $text;
    }

    /**
     * Sanitize a string for safe output in HTML.
     * 
     * This is a shorthand for htmlspecialchars() with proper flags.
     * Use this when displaying any user-provided data in HTML.
     *
     * @param mixed $value  Value to escape
     * @return string       HTML-safe string
     */
    public static function escape($value): string
    {
        return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Validate that a required field is not empty.
     *
     * @param mixed  $value      Value to check
     * @param string $fieldName  Name for error message
     * @return array             ['valid' => bool, 'error' => string|null]
     */
    public static function required($value, string $fieldName): array
    {
        $clean = is_string($value) ? trim($value) : $value;
        
        if ($clean === null || $clean === '' || $clean === []) {
            return ['valid' => false, 'error' => "$fieldName is required"];
        }
        
        return ['valid' => true, 'error' => null];
    }

    /**
     * Validate and sanitize an integer from user input.
     * 
     * @param mixed $value  The input value
     * @param int   $min    Minimum allowed value
     * @param int   $max    Maximum allowed value
     * @return int|false    The validated integer, or false if invalid
     */
    public static function validateInt($value, int $min = 1, int $max = PHP_INT_MAX)
    {
        $filtered = filter_var($value, FILTER_VALIDATE_INT, [
            'options' => ['min_range' => $min, 'max_range' => $max]
        ]);
        
        return $filtered;
    }
}
