<?php
// AnnouncementModel - Handles announcement database operations
require_once __DIR__ . '/../config/Database.php';

class AnnouncementModel {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->connect();
    }

    // Create new announcement
    public function createAnnouncement($admin_id, $title, $content) {
        $stmt = $this->conn->prepare("INSERT INTO announcements (admin_id, title, content, is_active) VALUES (?, ?, ?, TRUE)");
        $stmt->bind_param("iss", $admin_id, $title, $content);
        return $stmt->execute();
    }

    // Get all active announcements
    public function getAnnouncements() {
        $stmt = $this->conn->prepare("
            SELECT a.*, u.name as admin_name
            FROM announcements a
            JOIN users u ON a.admin_id = u.id
            WHERE a.is_active = TRUE
            ORDER BY a.created_at DESC
        ");
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Deactivate announcement
    public function deactivateAnnouncement($announcement_id) {
        $stmt = $this->conn->prepare("UPDATE announcements SET is_active = FALSE WHERE id = ?");
        $stmt->bind_param("i", $announcement_id);
        return $stmt->execute();
    }

    // Get announcement by ID
    public function getAnnouncementById($announcement_id) {
        $stmt = $this->conn->prepare("
            SELECT a.*, u.name as admin_name
            FROM announcements a
            JOIN users u ON a.admin_id = u.id
            WHERE a.id = ?
        ");
        $stmt->bind_param("i", $announcement_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
}
?>
