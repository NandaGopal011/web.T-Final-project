<?php
class UserModel {
    private $db;

    public function __construct() {
        $this->db = getDB();
    }

    public function findByEmail(string $email): ?array {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
        if (!$stmt) die('Prepare failed (findByEmail): ' . $this->db->error);
        $stmt->bind_param('s', $email);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc() ?: null;
    }

    public function findById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
        if (!$stmt) die('Prepare failed (findById): ' . $this->db->error);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc() ?: null;
    }

    public function createUser(array $data): int {
        $stmt = $this->db->prepare(
            "INSERT INTO users (name, email, password_hash, phone, bio, role) VALUES (?, ?, ?, ?, ?, 'seller')"
        );
        if (!$stmt) die('Prepare failed (createUser): ' . $this->db->error);
        $phone = $data['phone'] ?? null;
        $bio   = $data['bio']   ?? null;
        $stmt->bind_param('sssss', $data['name'], $data['email'], $data['password_hash'], $phone, $bio);
        $stmt->execute();
        return $stmt->insert_id;
    }

    public function updateProfile(int $id, array $data): bool {
        $stmt = $this->db->prepare(
            "UPDATE users SET name=?, phone=?, bio=? WHERE id=?"
        );
        if (!$stmt) die('Prepare failed (updateProfile): ' . $this->db->error);
        $stmt->bind_param('sssi', $data['name'], $data['phone'], $data['bio'], $id);
        return $stmt->execute();
    }

    public function updatePassword(int $id, string $hash): bool {
        $stmt = $this->db->prepare("UPDATE users SET password_hash=? WHERE id=?");
        if (!$stmt) die('Prepare failed (updatePassword): ' . $this->db->error);
        $stmt->bind_param('si', $hash, $id);
        return $stmt->execute();
    }

    public function emailExists(string $email): bool {
        $stmt = $this->db->prepare("SELECT id FROM users WHERE email = ?");
        if (!$stmt) die('Prepare failed (emailExists): ' . $this->db->error);
        $stmt->bind_param('s', $email);
        $stmt->execute();
        return $stmt->get_result()->num_rows > 0;
    }

    public function getReviews(int $userId): array {
        // reviews table may not exist — return empty safely
        $check = $this->db->query("SHOW TABLES LIKE 'reviews'");
        if (!$check || $check->num_rows === 0) return [];

        $stmt = $this->db->prepare(
            "SELECT r.*, u.name AS reviewer_name, l.title AS listing_title
             FROM reviews r
             JOIN users u ON u.id = r.reviewer_id
             JOIN listings l ON l.id = r.listing_id
             WHERE r.reviewee_id = ?
             ORDER BY r.created_at DESC"
        );
        if (!$stmt) return [];
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function createVerificationRequest(array $data): bool {
        $check = $this->db->query("SHOW TABLES LIKE 'seller_verification_requests'");
        if (!$check || $check->num_rows === 0) return true; // skip silently

        $stmt = $this->db->prepare("DELETE FROM seller_verification_requests WHERE user_id=?");
        if ($stmt) { $stmt->bind_param('i', $data['user_id']); $stmt->execute(); }

        $stmt = $this->db->prepare(
            "INSERT INTO seller_verification_requests (user_id, motivation, id_document_path) VALUES (?,?,?)"
        );
        if (!$stmt) return false;
        $stmt->bind_param('iss', $data['user_id'], $data['motivation'], $data['id_document_path']);
        return $stmt->execute();
    }

    public function getVerificationStatus(int $userId): ?array {
        $check = $this->db->query("SHOW TABLES LIKE 'seller_verification_requests'");
        if (!$check || $check->num_rows === 0) return null;

        $stmt = $this->db->prepare(
            "SELECT * FROM seller_verification_requests WHERE user_id=? ORDER BY submitted_at DESC LIMIT 1"
        );
        if (!$stmt) return null;
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc() ?: null;
    }
}