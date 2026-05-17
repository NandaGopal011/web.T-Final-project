<?php
// Session Management - Handles user login sessions and authentication
// Sessions store user information after login and expire after 30 minutes of inactivity

class AdminSession {
    // Session timeout in seconds (1800 = 30 minutes)
    private static $timeout = 1800;

    // Start PHP session if not already started
    public static function start() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    // Check if user session is valid and not expired
    public static function check() {
        self::start();

        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            return false;
        }

        // Check if session has expired after 30 minutes of inactivity
        if (isset($_SESSION['last_activity'])) {
            if (time() - $_SESSION['last_activity'] > self::$timeout) {
                self::destroy();
                return false;
            }
        }

        // Update last activity time to current time
        $_SESSION['last_activity'] = time();
        return true;
    }

    // Create new session after successful login
    public static function create($user_id, $name, $email, $role) {
        self::start();
        $_SESSION['user_id'] = $user_id;
        $_SESSION['name'] = $name;
        $_SESSION['email'] = $email;
        $_SESSION['role'] = $role;
        $_SESSION['last_activity'] = time();
    }

    // Destroy session when user logs out
    public static function destroy() {
        self::start();
        session_destroy();
        $_SESSION = array();
        setcookie(session_name(), '', time() + (86400*30), '/');
    }

    // Check if user is admin - used before displaying admin pages
    public static function requireAdmin() {
        if (!self::check() || $_SESSION['role'] !== 'admin') {
            self::destroy();
            header('Location: ../?message=Session expired');
            exit;
        }
    }

    // Get current page path
    public static function getCurrentPath() {
        $path = $_SERVER['REQUEST_URI'];
        return parse_url($path, PHP_URL_PATH);
    }
}
?>

