<?php
/**
 * =============================================================================
 * ADMIN MODEL — All Database Operations for Admin Users
 * =============================================================================
 * 
 * Handles admin authentication and management.
 * 
 * ROLES:
 *   'admin'       → Can manage candidates, view reports
 *   'super_admin' → Full access: manage admins, delete elections, etc.
 *
 * =============================================================================
 */

class Admin
{
    /**
     * Find an admin by their ID.
     *
     * @param mysqli $conn      Database connection
     * @param int    $admin_id  Admin ID
     * @return array|null       Admin data or null
     */
    public static function findById(mysqli $conn, int $admin_id): ?array
    {
        $stmt = $conn->prepare(
            "SELECT admin_id, username, email, role, created_at 
             FROM admin WHERE admin_id = ?"
        );
        $stmt->bind_param("i", $admin_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $admin = $result->fetch_assoc();
        $stmt->close();
        
        return $admin ?: null;
    }

    /**
     * Authenticate an admin with username and password.
     *
     * @param mysqli $conn      Database connection
     * @param string $username  Admin username
     * @param string $password  Plain-text password
     * @return array|null       Admin data (without hash) if valid, null if invalid
     */
    public static function authenticate(mysqli $conn, string $username, string $password): ?array
    {
        $stmt = $conn->prepare(
            "SELECT admin_id, username, password_hash, email, role 
             FROM admin WHERE username = ?"
        );
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows !== 1) {
            $stmt->close();
            return null;
        }
        
        $admin = $result->fetch_assoc();
        $stmt->close();
        
        if (!password_verify($password, $admin['password_hash'])) {
            return null;
        }
        
        // Remove the hash — never expose it outside the model
        unset($admin['password_hash']);
        return $admin;
    }

    /**
     * Get all admin accounts.
     *
     * @param mysqli $conn  Database connection
     * @return array        Array of admin records (no password hashes)
     */
    public static function findAll(mysqli $conn): array
    {
        $result = $conn->query(
            "SELECT admin_id, username, email, role, created_at 
             FROM admin ORDER BY created_at DESC"
        );
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Create a new admin account.
     *
     * @param mysqli $conn  Database connection
     * @param array  $data  Admin data: 'username', 'email', 'password', 'role'
     * @return int|false    New admin ID or false
     */
    public static function create(mysqli $conn, array $data)
    {
        $password_hash = password_hash($data['password'], PASSWORD_DEFAULT);
        $role = $data['role'] ?? 'admin';
        
        $stmt = $conn->prepare(
            "INSERT INTO admin (username, password_hash, email, role) 
             VALUES (?, ?, ?, ?)"
        );
        $stmt->bind_param("ssss", $data['username'], $password_hash, $data['email'], $role);
        $stmt->execute();
        $id = $stmt->insert_id;
        $stmt->close();
        
        return $id > 0 ? $id : false;
    }

    /**
     * Update admin password.
     *
     * @param mysqli $conn          Database connection
     * @param int    $admin_id      Admin ID
     * @param string $new_password  New plain-text password
     * @return bool                 true if updated
     */
    public static function updatePassword(mysqli $conn, int $admin_id, string $new_password): bool
    {
        $hash = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare(
            "UPDATE admin SET password_hash = ? WHERE admin_id = ?"
        );
        $stmt->bind_param("si", $hash, $admin_id);
        $stmt->execute();
        $success = $stmt->affected_rows === 1;
        $stmt->close();
        
        return $success;
    }
}
