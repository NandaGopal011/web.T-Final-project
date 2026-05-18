<?php
// UserModel - Handles all user management database operations
require_once __DIR__ . '/../config/Database.php';

class UserModel {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->connect();
    }

    // Get all users with pagination
    public function getAllUsers($limit = 50, $offset = 0) {
        $query = "SELECT id, name, email, role, seller_verified, is_active, reputation_score, created_at
                  FROM users
                  ORDER BY created_at DESC
                  LIMIT ? OFFSET ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ii", $limit, $offset);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Search users by name or email
    public function searchUsers($search_term, $limit = 50, $offset = 0) {
        $search = "%$search_term%";
        $query = "SELECT id, name, email, role, seller_verified, is_active, reputation_score, created_at
                  FROM users
                  WHERE name LIKE ? OR email LIKE ?
                  ORDER BY created_at DESC
                  LIMIT ? OFFSET ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ssii", $search, $search, $limit, $offset);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Deactivate user account
    public function deactivateUser($user_id) {
        $query = "UPDATE users SET is_active = FALSE WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        return $stmt->execute();
    }

    // Reactivate user account
    public function reactivateUser($user_id) {
        $query = "UPDATE users SET is_active = TRUE WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        return $stmt->execute();
    }

    // Change user role (buyer, seller, moderator)
    public function updateUserRole($user_id, $new_role) {
        $query = "UPDATE users SET role = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("si", $new_role, $user_id);
        return $stmt->execute();
    }

    // Count total users
    public function getUserCount() {
        $result = $this->conn->query("SELECT COUNT(*) as count FROM users");
        $row = $result->fetch_assoc();
        return $row['count'];
    }

    // Get user by ID
    public function getUserById($id) {
        $query = "SELECT id, name, email, phone, bio, profile_pic, role, seller_verified, is_active, reputation_score, created_at
                  FROM users
                  WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // Update user profile
    public function updateProfile($user_id, $name, $phone, $bio) {
        $query = "UPDATE users SET name = ?, phone = ?, bio = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("sssi", $name, $phone, $bio, $user_id);
        return $stmt->execute();
    }
}
?>
