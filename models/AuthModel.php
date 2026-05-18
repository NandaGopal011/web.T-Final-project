<?php
// AuthModel - Handles user authentication and login
require_once __DIR__ . '/../config/Database.php';

class AuthModel {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->connect();
    }

    // Check user credentials and return user data if login is valid
    public function login($email, $password) {
        $query = "SELECT id, name, email, role, password_hash, is_active FROM users WHERE email = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            return null;
        }

        $user = $result->fetch_assoc();

        // Check if user account is active
        if (!$user['is_active']) {
            return null;
        }

        // Check if password matches
        if (password_verify($password, $user['password_hash'])) {
            return array(
                'id' => $user['id'],
                'name' => $user['name'],
                'email' => $user['email'],
                'role' => $user['role']
            );
        }

        return null;
    }

    // Create new user account
    public function register($name, $email, $password) {
        $query = "INSERT INTO users (name, email, password_hash) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($query);

        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        $stmt->bind_param("sss", $name, $email, $hashed_password);

        return $stmt->execute();
    }
}
?>
