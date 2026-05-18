<?php
class ListingModel {
    private $db;

    public function __construct() {
        $this->db = getDB();
    }

    public function getBySellerAndStatus(int $sellerId, string $status = 'all'): array {
        if ($status === 'all') {
            $stmt = $this->db->prepare(
                "SELECT l.*,
                        (SELECT COUNT(*) FROM bids b WHERE b.listing_id = l.id) AS bid_count
                 FROM listings l
                 WHERE l.seller_id = ?
                 ORDER BY l.end_datetime DESC"
            );
            if (!$stmt) die('Prepare failed (all): ' . $this->db->error);
            $stmt->bind_param('i', $sellerId);
        } else {
            $stmt = $this->db->prepare(
                "SELECT l.*,
                        (SELECT COUNT(*) FROM bids b WHERE b.listing_id = l.id) AS bid_count
                 FROM listings l
                 WHERE l.seller_id = ? AND l.status = ?
                 ORDER BY l.end_datetime DESC"
            );
            if (!$stmt) die('Prepare failed (filter): ' . $this->db->error);
            $stmt->bind_param('is', $sellerId, $status);
        }
        $stmt->execute();
        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        foreach ($rows as &$row) {
            $row['category_name'] = '—';
            $row['condition']     = '—';
        }
        return $rows;
    }

    public function getById(int $id): ?array {
        $stmt = $this->db->prepare(
            "SELECT l.*,
                    (SELECT COUNT(*) FROM bids b WHERE b.listing_id = l.id) AS bid_count
             FROM listings l
             WHERE l.id = ?"
        );
        if (!$stmt) die('Prepare failed (getById): ' . $this->db->error);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        if ($row) {
            $row['category_name'] = '—';
            $row['condition']     = '—';
            $row['seller_id']     = $row['seller_id'] ?? 0;
        }
        return $row ?: null;
    }

    public function getImages(int $listingId): array {
        return []; // listing_images table does not exist
    }

    public function getBidHistory(int $listingId): array {
        $stmt = $this->db->prepare(
            "SELECT b.*, u.name AS buyer_name
             FROM bids b
             JOIN users u ON u.id = b.buyer_id
             WHERE b.listing_id = ?
             ORDER BY b.created_at DESC"
        );
        if (!$stmt) die('Prepare failed (getBidHistory): ' . $this->db->error);
        $stmt->bind_param('i', $listingId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function create(array $data): int {
        $stmt = $this->db->prepare(
            "INSERT INTO listings (seller_id, title, description, starting_price, end_datetime, status)
             VALUES (?, ?, ?, ?, ?, 'pending_review')"
        );
        if (!$stmt) die('Prepare failed (create): ' . $this->db->error);
        $stmt->bind_param(
            'issds',
            $data['seller_id'],
            $data['title'],
            $data['description'],
            $data['starting_price'],
            $data['end_datetime']
        );
        $stmt->execute();
        return $stmt->insert_id;
    }

    public function addImage(int $listingId, string $path, int $order): void {
        // listing_images table does not exist
    }

    public function update(int $id, array $data): bool {
        $stmt = $this->db->prepare(
            "UPDATE listings
             SET title=?, description=?, starting_price=?, end_datetime=?
             WHERE id=? AND seller_id=?"
        );
        if (!$stmt) die('Prepare failed (update): ' . $this->db->error);
        $stmt->bind_param(
            'ssdsii',
            $data['title'],
            $data['description'],
            $data['starting_price'],
            $data['end_datetime'],
            $id,
            $data['seller_id']
        );
        return $stmt->execute();
    }

    public function cancel(int $id, int $sellerId): bool {
        $stmt = $this->db->prepare(
            "UPDATE listings SET status='cancelled'
             WHERE id=? AND seller_id=?
             AND (SELECT COUNT(*) FROM bids WHERE listing_id=?) = 0"
        );
        if (!$stmt) die('Prepare failed (cancel): ' . $this->db->error);
        $stmt->bind_param('iii', $id, $sellerId, $id);
        return $stmt->execute() && $stmt->affected_rows > 0;
    }

    public function hasBids(int $listingId): bool {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) AS cnt FROM bids WHERE listing_id=?"
        );
        if (!$stmt) die('Prepare failed (hasBids): ' . $this->db->error);
        $stmt->bind_param('i', $listingId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc()['cnt'] > 0;
    }

    public function getWinnerDetails(int $listingId): ?array {
        $stmt = $this->db->prepare(
            "SELECT b.amount, u.name, u.email
             FROM bids b
             JOIN users u ON u.id = b.buyer_id
             WHERE b.listing_id = ?
             ORDER BY b.amount DESC LIMIT 1"
        );
        if (!$stmt) die('Prepare failed (getWinnerDetails): ' . $this->db->error);
        $stmt->bind_param('i', $listingId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc() ?: null;
    }

    public function getAnalytics(int $sellerId): array {
        $db = $this->db;

        // Total listings
        $stmt = $db->prepare("SELECT COUNT(*) AS total FROM listings WHERE seller_id=?");
        if (!$stmt) die('Prepare failed (analytics total): ' . $db->error);
        $stmt->bind_param('i', $sellerId);
        $stmt->execute();
        $total = $stmt->get_result()->fetch_assoc()['total'];

        // Ended with at least 1 bid = sold
        $stmt = $db->prepare(
            "SELECT COUNT(DISTINCT l.id) AS won
             FROM listings l
             WHERE l.seller_id=? AND l.status='ended'
             AND (SELECT COUNT(*) FROM bids b WHERE b.listing_id=l.id) > 0"
        );
        if (!$stmt) die('Prepare failed (analytics won): ' . $db->error);
        $stmt->bind_param('i', $sellerId);
        $stmt->execute();
        $won = $stmt->get_result()->fetch_assoc()['won'];

        // Avg current bid on ended listings
        $stmt = $db->prepare(
            "SELECT AVG(current_bid) AS avg_price FROM listings
             WHERE seller_id=? AND status='ended' AND current_bid IS NOT NULL"
        );
        if (!$stmt) die('Prepare failed (analytics avg): ' . $db->error);
        $stmt->bind_param('i', $sellerId);
        $stmt->execute();
        $avgPrice = $stmt->get_result()->fetch_assoc()['avg_price'] ?? 0;

        // Revenue = sum of current_bid on ended listings
        $stmt = $db->prepare(
            "SELECT COALESCE(SUM(current_bid), 0) AS revenue
             FROM listings
             WHERE seller_id=? AND status='ended' AND current_bid IS NOT NULL"
        );
        if (!$stmt) die('Prepare failed (analytics revenue): ' . $db->error);
        $stmt->bind_param('i', $sellerId);
        $stmt->execute();
        $revenue = $stmt->get_result()->fetch_assoc()['revenue'] ?? 0;

        // No category_id in listings — skip popularCat
        $popularCat = null;

        // Sales trend last 6 months
        $stmt = $db->prepare(
            "SELECT DATE_FORMAT(end_datetime,'%Y-%m') AS month, COUNT(*) AS count
             FROM listings
             WHERE seller_id=? AND status='ended'
             AND end_datetime >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
             GROUP BY month ORDER BY month"
        );
        if (!$stmt) die('Prepare failed (analytics trend): ' . $db->error);
        $stmt->bind_param('i', $sellerId);
        $stmt->execute();
        $trend = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        return compact('total', 'won', 'avgPrice', 'revenue', 'popularCat', 'trend');
    }
}