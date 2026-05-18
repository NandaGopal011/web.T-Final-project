<?php
class TemplateModel {
    private $db;

    public function __construct() {
        $this->db = getDB();
    }

    public function getBySeller(int $sellerId): array {
        $stmt = $this->db->prepare(
            "SELECT t.*, c.name AS category_name
             FROM auction_templates t
             LEFT JOIN categories c ON c.id = t.category_id
             WHERE t.seller_id = ? ORDER BY t.created_at DESC"
        );
        $stmt->bind_param('i', $sellerId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getById(int $id, int $sellerId): ?array {
        $stmt = $this->db->prepare(
            "SELECT * FROM auction_templates WHERE id=? AND seller_id=?"
        );
        $stmt->bind_param('ii', $id, $sellerId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc() ?: null;
    }

    public function create(array $data): int {
        $stmt = $this->db->prepare(
            "INSERT INTO auction_templates (seller_id, title, description, category_id, `condition`, starting_price)
             VALUES (?,?,?,?,?,?)"
        );
        $stmt->bind_param(
            'ississ',
            $data['seller_id'], $data['title'], $data['description'],
            $data['category_id'], $data['condition'], $data['starting_price']
        );
        $stmt->execute();
        return $stmt->insert_id;
    }

    public function delete(int $id, int $sellerId): bool {
        $stmt = $this->db->prepare(
            "DELETE FROM auction_templates WHERE id=? AND seller_id=?"
        );
        $stmt->bind_param('ii', $id, $sellerId);
        return $stmt->execute();
    }
}
