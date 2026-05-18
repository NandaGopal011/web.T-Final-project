<?php
class CategoryModel {
    private $db;

    public function __construct() {
        $this->db = getDB();
    }

    public function getAll(): array {
        $result = $this->db->query("SELECT * FROM categories ORDER BY parent_id, name");
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
