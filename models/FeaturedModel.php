<?php
// FeaturedModel - Handles featured listings database operations
require_once __DIR__ . '/../config/Database.php';

class FeaturedModel {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->connect();
    }

    // Add listing to featured listings
    public function addFeaturedListing($listing_id, $admin_id) {
        $stmt = $this->conn->prepare("INSERT INTO featured_listings (listing_id, added_by) VALUES (?, ?)");
        $stmt->bind_param("ii", $listing_id, $admin_id);
        return $stmt->execute();
    }

    // Remove listing from featured listings
    public function removeFeaturedListing($listing_id) {
        $stmt = $this->conn->prepare("DELETE FROM featured_listings WHERE listing_id = ?");
        $stmt->bind_param("i", $listing_id);
        return $stmt->execute();
    }

    // Get all featured listings
    public function getFeaturedListings() {
        $result = $this->conn->query("
            SELECT l.*, u.name as seller_name, c.name as category_name
            FROM featured_listings fl
            JOIN listings l ON fl.listing_id = l.id
            JOIN users u ON l.seller_id = u.id
            JOIN categories c ON l.category_id = c.id
            ORDER BY fl.created_at DESC
        ");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Check if listing is featured
    public function isFeatured($listing_id) {
        $stmt = $this->conn->prepare("SELECT id FROM featured_listings WHERE listing_id = ?");
        $stmt->bind_param("i", $listing_id);
        $stmt->execute();
        return $stmt->get_result()->num_rows > 0;
    }

    // Get featured listing by ID
    public function getFeaturedListingById($featured_id) {
        $stmt = $this->conn->prepare("
            SELECT l.*, u.name as seller_name, c.name as category_name
            FROM featured_listings fl
            JOIN listings l ON fl.listing_id = l.id
            JOIN users u ON l.seller_id = u.id
            JOIN categories c ON l.category_id = c.id
            WHERE fl.id = ?
        ");
        $stmt->bind_param("i", $featured_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
}
?>
