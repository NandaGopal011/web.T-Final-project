<?php
// ListingModel - Handles all listing management database operations
require_once __DIR__ . '/../config/Database.php';

class ListingModel {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->connect();
    }

    // Get all listings with optional filters
    public function getAllListings($status = null, $category_id = null, $limit = 50, $offset = 0) {
        $query = "SELECT l.*, u.name as seller_name, u.email as seller_email, c.name as category_name
                  FROM listings l
                  JOIN users u ON l.seller_id = u.id
                  JOIN categories c ON l.category_id = c.id
                  WHERE 1=1";

        // Add filters if provided
        if ($status) {
            $query .= " AND l.status = '" . $this->conn->real_escape_string($status) . "'";
        }

        if ($category_id) {
            $query .= " AND l.category_id = " . intval($category_id);
        }

        $query .= " ORDER BY l.created_at DESC LIMIT " . intval($limit) . " OFFSET " . intval($offset);

        $result = $this->conn->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Get listing details by ID
    public function getListingDetails($listing_id) {
        $stmt = $this->conn->prepare("
            SELECT l.*, u.name as seller_name, u.email as seller_email, c.name as category_name
            FROM listings l
            JOIN users u ON l.seller_id = u.id
            JOIN categories c ON l.category_id = c.id
            WHERE l.id = ?
        ");
        $stmt->bind_param("i", $listing_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // Cancel a listing
    public function cancelListing($listing_id, $reason) {
        $status = 'cancelled';
        $stmt = $this->conn->prepare("UPDATE listings SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $listing_id);
        return $stmt->execute();
    }

    // Get total listings count
    public function getListingCount() {
        $result = $this->conn->query("SELECT COUNT(*) as count FROM listings");
        $row = $result->fetch_assoc();
        return $row['count'];
    }
}
?>
