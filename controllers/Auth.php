<?php
// Authentication Controller - Handles permission checking
// Used to protect pages that require login or admin access

class AuthController {

    // Check if user is logged in - redirect to login if not
    public static function requireLogin() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ../');
            exit;
        }
    }

    // Check if user is admin - redirect to login if not admin
    public static function requireAdmin() {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header('Location: ../');
            exit;
        }
    }

    // Destroy session and logout user
    public static function logout() {
        session_destroy();
        header('Location: ../');
        exit;
    }
}
?>
